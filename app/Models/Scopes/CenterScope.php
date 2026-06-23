<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CenterScope implements Scope
{
    private const IGNORED_TABLES = [
        'centers',
        'roles',
        'permissions',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
    ];

    private static array $teacherCache   = [];
    private static array $circleIdsCache = [];

    public function apply(Builder $builder, Model $model): void
    {
        if (in_array($model->getTable(), self::IGNORED_TABLES)) return;

        $user = auth()->user();
        if (!$user) return;

        if ($user->hasRole(['admin', 'general_manager'])) return;
        if ($user->hasRole('guardian')) return;

        $teacher = $this->getTeacher($user->id);

        if (!$teacher) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $table = $model->getTable();

        // تجميع كل الشروط بالكامل داخل nested where لحماية أسبقية الـ SQL
        $builder->where(function ($nestedQuery) use ($table, $teacher, $user) {

            // مجموعات الشروط الأساسية بناءً على الدور
            $nestedQuery->where(function ($q) use ($table, $teacher, $user) {
                match (true) {
                    $user->hasRole('manager')    => $this->applyManagerScope($q, $table, $teacher),
                    $user->hasRole('teacher')    => $this->applyTeacherScope($q, $table, $teacher),
                    $user->hasRole('supervisor') => $this->applySupervisorTeachersScope($q, $table, $teacher),
                    default                      => $q->whereRaw('1 = 0'),
                };
            });

            // دمج الحلقات المشرف عليها بصيغة آمنة داخل الـ group
            $this->applySupervisedUnion($nestedQuery, $table, $teacher);
        });
    }

    private function applyManagerScope(Builder $builder, string $table, object $teacher): void
    {
        if (is_null($teacher->center_id)) {
            $builder->whereRaw('1 = 0');
            return;
        }

        match ($table) {
            'circles' => $builder->where('circles.center_id', $teacher->center_id)
                ->orWhereIn('circles.id', function ($sub) use ($teacher) {
                    $sub->select('circle_id')
                        ->from('circle_teacher')
                        ->where('teacher_id', $teacher->id)
                        ->whereIn('role', ['main', 'assistant']);
                }),

            'students', 'teachers'
            => $builder->where("{$table}.center_id", $teacher->center_id),

            'subscriptions', 'attendances'
            => $builder->whereIn('student_id', function ($sub) use ($teacher) {
                $sub->select('id')->from('students')->where('center_id', $teacher->center_id);
            }),

            default => null,
        };
    }

    private function applySupervisorTeachersScope(Builder $builder, string $table, object $teacher): void
    {
        if ($table === 'teachers') {
            if (is_null($teacher->center_id)) {
                $builder->whereRaw('1 = 0');
                return;
            }
            $builder->where("{$table}.center_id", $teacher->center_id);
            return;
        }
        $builder->whereRaw('1 = 0');
    }

    private function applySupervisedUnion(Builder $builder, string $table, object $teacher): void
    {
        if (!in_array($table, ['circles', 'students', 'subscriptions', 'attendances'])) return;

        $circleIds = $this->getSupervisorCircleIds($teacher);
        if ($circleIds->isEmpty()) return;

        // ✅ إصلاح أمني حرّج: استخدام orWhere لربط العلاقة الفرعية بأمان دون كسر شروط الكنترولر الخارجية
        $builder->orWhere(function ($q) use ($table, $circleIds) {
            match ($table) {
                'circles' => $q->whereIn('id', $circleIds),
                'students' => $q->whereIn('circle_id', $circleIds),
                'subscriptions', 'attendances' => $q->whereIn('student_id', function ($sub) use ($circleIds) {
                    $sub->select('id')->from('students')->whereIn('circle_id', $circleIds);
                }),
                default => null,
            };
        });
    }

    private function applyTeacherScope(Builder $builder, string $table, object $teacher): void
    {
        $circleIds = $this->getTeacherCircleIds($teacher);

        if ($circleIds->isEmpty()) {
            $builder->whereRaw('1 = 0');
            return;
        }

        if ($table === 'teachers') {
            if (is_null($teacher->center_id)) {
                $builder->whereRaw('1 = 0');
                return;
            }
            $builder->where("{$table}.center_id", $teacher->center_id);
            return;
        }

        $this->applyScopeByCircleIds($builder, $table, $circleIds);
    }

    private function applyScopeByCircleIds(Builder $builder, string $table, Collection|array $circleIds): void
    {
        match ($table) {
            'circles' => $builder->whereIn('id', $circleIds),
            'students' => $builder->whereIn('circle_id', $circleIds),
            'subscriptions', 'attendances' => $builder->whereIn('student_id', function ($sub) use ($circleIds) {
                $sub->select('id')->from('students')->whereIn('circle_id', $circleIds);
            }),
            default => null,
        };
    }

    private function getTeacher(int $userId): ?object
    {
        if (!isset(self::$teacherCache[$userId])) {
            $teacher = DB::table('teachers')
                ->where('user_id', $userId)
                ->select(['id', 'center_id', 'user_id'])
                ->first();

            if ($teacher) {
                self::$teacherCache[$userId] = $teacher;
            } else {
                return null;
            }
        }
        return self::$teacherCache[$userId];
    }

    private function getSupervisorCircleIds(object $teacher)
    {
        $cacheKey = "supervisor_{$teacher->id}";
        if (!isset(self::$circleIdsCache[$cacheKey])) {
            self::$circleIdsCache[$cacheKey] = DB::table('circles')
                ->whereIn('id', function ($sub) use ($teacher) {
                    $sub->select('circle_id')
                        ->from('circle_teacher')
                        ->where('teacher_id', $teacher->id)
                        ->where('role', 'supervisor');
                })
                ->where('is_active', true)
                ->pluck('id');
        }
        return self::$circleIdsCache[$cacheKey];
    }

    private function getTeacherCircleIds(object $teacher)
    {
        $cacheKey = "teacher_{$teacher->id}";
        if (!isset(self::$circleIdsCache[$cacheKey])) {
            self::$circleIdsCache[$cacheKey] = DB::table('circles')
                ->whereIn('id', function ($sub) use ($teacher) {
                    $sub->select('circle_id')
                        ->from('circle_teacher')
                        ->where('teacher_id', $teacher->id)
                        ->whereIn('role', ['main', 'assistant']);
                })
                ->where('center_id', $teacher->center_id)
                ->where('is_active', true)
                ->pluck('id');
        }
        return self::$circleIdsCache[$cacheKey];
    }

    public static function clearCache(): void
    {
        self::$teacherCache   = [];
        self::$circleIdsCache = [];
    }
}
