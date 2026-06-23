<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Circle;
use App\Models\Center;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Student;
use App\Models\Scopes\CenterScope;

trait ResolvesUserScope
{
    // ✅ static cache يستمر طول الـ request
    private static array $teacherRecordCache = [];

    // ─── جلب teacher record مع static cache ──────────────────────
    protected function getTeacherRecord(User $user): ?Teacher
    {
        if (isset(self::$teacherRecordCache[$user->id])) {
            return self::$teacherRecordCache[$user->id];
        }

        $teacher = Teacher::where('user_id', $user->id)->first();

        if ($teacher) {
            self::$teacherRecordCache[$user->id] = $teacher;
        }

        return $teacher;
    }

    // ─── الحلقات المتاحة ──────────────────────────────────────────
    protected function getAccessibleCircles(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getAccessibleCirclesQuery($user)->get();
    }


    protected function getAccessibleCirclesQuery(User $user): \Illuminate\Database\Eloquent\Builder
    {
        if ($user->hasRole(['admin', 'general_manager']) || $user->can('view circles')) {
            return Circle::orderBy('name');
        }

        // ✅ guardian فقط — وليس أي دور آخر يملك نفس الصلاحية
        if ($user->hasRole('guardian') && $user->can('view own children')) {
            return Circle::whereIn(
                'id',
                Student::where('guardian_id', $user->id)
                    ->whereNotNull('circle_id')
                    ->where('status', 'مقيد')
                    ->pluck('circle_id')
            )->orderBy('name');
        }

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher || !$teacher->center_id) {
            return Circle::whereRaw('1=0');
        }

        if ($user->hasRole('manager')) {
            $circleIds = $this->getTeacherCircleIds($teacher);
            $query = Circle::where('center_id', $teacher->center_id);

            if ($circleIds->isNotEmpty()) {
                $query->orWhereIn('id', $circleIds);
            }
        } else {
            $circleIds = $this->getTeacherCircleIds($teacher);
            $query = $circleIds->isEmpty()
                ? Circle::whereRaw('1=0')
                : Circle::whereIn('id', $circleIds);
        }

        return $query
            ->orWhereHas('supervisors', fn($q) => $q->where('teachers.id', $teacher->id))
            ->orderBy('name');
    }

    // ─── الفروع المتاحة ───────────────────────────────────────────
    protected function getAccessibleCenters(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Center::orderBy('name')->get();
        }

        $teacher = $this->getTeacherRecord($user);

        if ($teacher && $teacher->center_id) {
            return Center::where('id', $teacher->center_id)->get();
        }

        return Center::whereRaw('1=0')->get();
    }

    // ─── المعلمون المتاحون ────────────────────────────────────────
    protected function getAccessibleTeachers(User $user, ?Teacher $teacher): Collection
    {
        return $this->getAccessibleTeachersQuery($user, $teacher)->get();
    }

    protected function getAccessibleTeachersQuery(User $user, ?Teacher $teacher): \Illuminate\Database\Eloquent\Builder
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Teacher::withoutGlobalScope(CenterScope::class)
                ->with('user.roles')
                ->whereHas('user', fn($u) => $u->where('status', 'active'));
        }

        if ($teacher && $teacher->center_id) {
            return Teacher::withoutGlobalScope(CenterScope::class)
                ->with('user.roles')
                ->whereHas('user', fn($u) => $u->where('status', 'active'))
                ->where(
                    fn($query) =>
                    $query->where('center_id', $teacher->center_id)
                        ->orWhereHas('circles', fn($q) =>
                        $q->where('circles.center_id', $teacher->center_id))
                );
        }

        return Teacher::whereRaw('1 = 0');
    }

    // ─── شرط النطاق الموحّد لمعلم واحد: فرعه + الحلقات الخارجية ────
    protected function applyTeacherCenterScope($query, Teacher $record): void
    {
        $query->where(function ($q) use ($record) {
            $q->where('center_id', $record->center_id)
                ->orWhereHas('circles', fn($cq) =>
                $cq->where('circles.center_id', $record->center_id));
        });
    }

    protected function getAccessibleSupervisors(User $user, ?Teacher $teacher): \Illuminate\Database\Eloquent\Collection
    {
        $rolesFilter = fn($r) => $r->whereIn('name', ['supervisor', 'manager', 'general_manager']);

        if ($user->hasRole(['admin', 'general_manager'])) {
            return Teacher::whereHas(
                'user',
                fn($q) => $q->where('status', 'active')
                    ->whereHas('roles', $rolesFilter)
            )
                ->with('user.roles')
                ->get();
        }

        if ($teacher && $teacher->center_id) {
            return Teacher::where('center_id', $teacher->center_id)
                ->whereHas(
                    'user',
                    fn($q) => $q->where('status', 'active')
                        ->whereHas('roles', $rolesFilter)
                )
                ->with('user.roles')
                ->get();
        }

        return Teacher::whereRaw('1 = 0')->get();
    }

    // ─── helper: circle IDs للـ teacher (رئيسي/مساعد فقط) ────────
    private function getTeacherCircleIds(Teacher $teacher): Collection
    {
        return DB::table('circle_teacher')
            ->where('teacher_id', $teacher->id)
            ->whereIn('role', ['main', 'assistant'])
            ->pluck('circle_id');
    }

    // ─── تطبيق فلتر الحلقات على أي query ────────────────────────
    protected function applyCircleFilter($query, User $user, $circleIds): void
    {
        if ($user->hasRole(['admin', 'general_manager']) || $user->can('view circles')) {
            return;
        }

        $query->whereIn('circle_id', $circleIds);
    }

    // ✅ مسح الـ static cache — للـ Tests
    public static function clearScopeCache(): void
    {
        self::$teacherRecordCache = [];
    }
    // ─── IDs الحلقات المتاحة ──────────────────────────────────────
    protected function getAccessibleCircleIds(User $user): Collection
    {
        return $this->getAccessibleCirclesQuery($user)->pluck('id');
    }
}
