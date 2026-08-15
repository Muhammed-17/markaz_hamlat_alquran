<?php

namespace App\Services;

use App\Models\Center;
use App\Models\Circle;
use App\Models\Scopes\CenterScope;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * ✅ UserAccessService — المصدر الوحيد لكل منطق الصلاحيات والوصول.
 */
class UserAccessService
{
    private array $teacherCache = [];
    private array $circleIdsCache = [];

    public function teacher(User $user): ?Teacher
    {
        if (!array_key_exists($user->id, $this->teacherCache)) {
            $this->teacherCache[$user->id] = Teacher::withoutGlobalScope(CenterScope::class)
                ->where('user_id', $user->id)
                ->first();
        }

        return $this->teacherCache[$user->id];
    }

    private function rememberCircleIds(string $cacheKey, \Closure $resolver): Collection
    {
        if (!isset($this->circleIdsCache[$cacheKey])) {
            $this->circleIdsCache[$cacheKey] = $resolver();
        }

        return $this->circleIdsCache[$cacheKey];
    }

    public function teacherCircleIds(User $user): Collection
    {
        $teacher = $this->teacher($user);
        if (!$teacher) {
            return collect();
        }

        return $this->rememberCircleIds(
            "teacher_{$teacher->id}",
            fn() => $teacher->circles()
                ->wherePivotIn('role', ['main', 'assistant'])
                ->pluck('circles.id')
        );
    }

    public function teacherCircleIdsWithinCenter(User $user): Collection
    {
        $teacher = $this->teacher($user);
        if (!$teacher) {
            return collect();
        }

        return $this->rememberCircleIds(
            "teacher_center_{$teacher->id}",
            fn() => $teacher->circles()
                ->wherePivotIn('role', ['main', 'assistant'])
                ->where('circles.center_id', $teacher->center_id)
                ->pluck('circles.id')
        );
    }

    public function supervisorCircleIds(User $user): Collection
    {
        $teacher = $this->teacher($user);
        if (!$teacher) {
            return collect();
        }

        return $this->rememberCircleIds(
            "supervisor_{$teacher->id}",
            fn() => $teacher->circles()
                ->wherePivotIn('role', ['supervisor', 'main', 'assistant'])
                ->pluck('circles.id')
        );
    }

    public function managerCircleIds(User $user): Collection
    {
        $teacher = $this->teacher($user);
        if (!$teacher || !$teacher->center_id) {
            return collect();
        }

        return $this->rememberCircleIds(
            "manager_{$teacher->center_id}",
            fn() => Circle::where('center_id', $teacher->center_id)->pluck('id')
        );
    }

    public function accessibleCircles(User $user): Builder
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Circle::orderBy('name');
        }

        if ($user->hasRole('guardian')) {
            return Circle::whereIn(
                'id',
                Student::where('guardian_id', $user->id)
                    ->whereNotNull('circle_id')
                    ->where('status', 'مقيد')
                    ->pluck('circle_id')
            )->orderBy('name');
        }

        $teacher = $this->teacher($user);
        if (!$teacher || !$teacher->center_id) {
            return Circle::whereRaw('1=0');
        }

        if ($user->hasRole('manager')) {
            return Circle::where('center_id', $teacher->center_id)->orderBy('name');
        }

        if ($user->hasRole('supervisor')) {
            $circleIds = $this->supervisorCircleIds($user);

            return $circleIds->isEmpty()
                ? Circle::whereRaw('1=0')
                : Circle::whereIn('id', $circleIds)->orderBy('name');
        }

        $circleIds = $this->teacherCircleIds($user);

        return $circleIds->isEmpty()
            ? Circle::whereRaw('1=0')
            : Circle::whereIn('id', $circleIds)->orderBy('name');
    }

    public function accessibleCenters(User $user): Builder
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Center::orderBy('name');
        }

        $teacher = $this->teacher($user);

        if ($teacher && $teacher->center_id) {
            return Center::where('id', $teacher->center_id);
        }

        return Center::whereRaw('1=0');
    }

    /**
     * Teachers/supervisors visible to the given user, scoped by role:
     * - admin / general_manager: everyone, across all branches.
     * - supervisor: only staff assigned to the circles this user supervises.
     * - manager (and any other center-bound role): everyone within the same branch/center.
     */
    public function accessibleTeachers(User $user): Builder
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Teacher::withoutGlobalScope(CenterScope::class)
                ->with('user.roles')
                ->whereHas('user', fn($u) => $u->where('status', 'active'));
        }

        if ($user->hasRole('supervisor')) {
            $circleIds = $this->supervisorCircleIds($user);

            $query = Teacher::withoutGlobalScope(CenterScope::class)
                ->with('user.roles')
                ->whereHas('user', fn($u) => $u->where('status', 'active'));

            return $circleIds->isEmpty()
                ? $query->whereRaw('1=0')
                : $query->whereHas('circles', fn($cq) => $cq->whereIn('circles.id', $circleIds));
        }

        $teacher = $this->teacher($user);

        if ($teacher && $teacher->center_id) {
            $query = Teacher::withoutGlobalScope(CenterScope::class)
                ->with('user.roles')
                ->whereHas('user', fn($u) => $u->where('status', 'active'));

            $this->applyTeacherCenterFilter($query, $teacher);

            return $query;
        }

        return Teacher::whereRaw('1=0');
    }

    public function accessibleSupervisors(User $user): Builder
    {
        return $this->accessibleTeachers($user)
            ->whereHas('user.roles', fn($r) => $r->whereIn('name', ['supervisor', 'manager', 'general_manager']));
    }

    public function applyTeacherCenterFilter(Builder $query, Teacher $record): void
    {
        $query->where(function ($q) use ($record) {
            $q->where('center_id', $record->center_id)
                ->orWhereHas('circles', fn($cq) => $cq->where('circles.center_id', $record->center_id));
        });
    }

    public function canAccessCircle(User $user, int $circleId): bool
    {
        return $this->accessibleCircles($user)->whereKey($circleId)->exists();
    }

    public function canAccessStudent(User $user, Student $student): bool
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        if ($user->hasRole('guardian')) {
            return (int) $student->guardian_id === (int) $user->id;
        }

        if (!$student->circle_id) {
            return false;
        }

        return $this->canAccessCircle($user, (int) $student->circle_id);
    }


    public function applyScopeByCircleIds(Builder $builder, string $table, Collection|array $circleIds): void
    {
        match ($table) {
            'circles'                    => $builder->whereIn('id', $circleIds),
            'students',
            'subscriptions',
            'surah_tests'                 => $builder->whereIn('circle_id', $circleIds),
            'attendances'                => $builder->whereIn('student_id', function ($sub) use ($circleIds) {
                $sub->select('id')->from('students')->whereIn('circle_id', $circleIds);
            }),
            'student_construction_details' => $builder->whereIn('student_id', function ($sub) use ($circleIds) {
                $sub->select('id')->from('students')->whereIn('circle_id', $circleIds);
            }),
            'student_activities',
            'other_assessments',
            'recommendations'            => $builder->whereIn('weekly_plan_id', function ($sub) use ($circleIds) {
                $sub->select('id')->from('quran_weekly_plans')->where(function ($q) use ($circleIds) {
                    $q->whereIn('circle_id', $circleIds)
                        ->orWhereIn('student_id', function ($sub2) use ($circleIds) {
                            $sub2->select('id')->from('students')->whereIn('circle_id', $circleIds);
                        })
                        ->orWhereIn('id', function ($sub2) use ($circleIds) {
                            $sub2->select('quran_weekly_plan_id')
                                ->from('quran_weekly_plan_students')
                                ->whereIn('student_id', function ($sub3) use ($circleIds) {
                                    $sub3->select('id')->from('students')->whereIn('circle_id', $circleIds);
                                });
                        });
                });
            }),

            'behavioral_notes'           => $builder->where(function ($q) use ($circleIds) {
                $q->whereIn('circle_id', $circleIds)
                    ->orWhereIn('student_id', function ($sub) use ($circleIds) {
                        $sub->select('id')->from('students')->whereIn('circle_id', $circleIds);
                    });
            }),
            default                      => null,
        };
    }

    public function clearCache(): void
    {
        $this->teacherCache   = [];
        $this->circleIdsCache = [];
    }
}
