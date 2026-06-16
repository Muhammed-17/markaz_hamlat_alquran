<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class CenterScope implements Scope
{
    // ✅ جداول لا تحتاج فلترة بالـ center
    private const IGNORED_TABLES = [
        'centers',
        'users',
        'roles',
        'permissions',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
    ];

    // ✅ static cache يستمر طول الـ request
    private static array $teacherCache   = [];
    private static array $circleIdsCache = [];

    // ─────────────────────────────────────────────────────────────
    public function apply(Builder $builder, Model $model): void
    {
        if (in_array($model->getTable(), self::IGNORED_TABLES)) return;

        $user = auth()->user();
        if (!$user) return;

        // ✅ admin و general_manager يريان الكل بدون قيود
        if ($user->hasRole(['admin', 'general_manager'])) return;

        // ✅ guardian يُعامَل في الـ Controller/Policy — لا فلترة هنا
        if ($user->hasRole('guardian')) return;

        $teacher = $this->getTeacher($user->id);

        // ✅ مستخدم ليس له teacher record — لا يرى شيئاً
        if (!$teacher) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $table = $model->getTable();

        match (true) {
            $user->hasRole('manager')    => $this->applyManagerScope($builder, $table, $teacher),
            $user->hasRole('supervisor') => $this->applySupervisorScope($builder, $table, $teacher),
            $user->hasRole('teacher')    => $this->applyTeacherScope($builder, $table, $teacher),
            // ✅ أي دور آخر غير معروف — لا يرى شيئاً
            default                      => $builder->whereRaw('1 = 0'),
        };
    }

    // ─── manager ─────────────────────────────────────────────────
    private function applyManagerScope(Builder $builder, string $table, object $teacher): void
    {
        // ✅ تحقق أن center_id موجود
        if (is_null($teacher->center_id)) {
            $builder->whereRaw('1 = 0');
            return;
        }

        match ($table) {
            'circles', 'students', 'teachers'
            => $builder->where("{$table}.center_id", $teacher->center_id),

            'subscriptions', 'attendances'
            => $builder->whereIn(
                'student_id',
                // ✅ subquery بدل pluck لتجنب تحميل IDs في الذاكرة
                DB::table('students')
                    ->select('id')
                    ->where('center_id', $teacher->center_id)
            ),

            default => null,
        };
    }

    // ─── supervisor ───────────────────────────────────────────────
    private function applySupervisorScope(Builder $builder, string $table, object $teacher): void
    {
        $circleIds = $this->getSupervisorCircleIds($teacher);

        if ($circleIds->isEmpty()) {
            $builder->whereRaw('1 = 0');
            return;
        }

        // ✅ supervisor يرى كل معلمي فرعه وليس فقط معلمي حلقاته
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

    // ─── teacher ─────────────────────────────────────────────────
    private function applyTeacherScope(Builder $builder, string $table, object $teacher): void
    {
        $circleIds = $this->getTeacherCircleIds($teacher);

        if ($circleIds->isEmpty()) {
            $builder->whereRaw('1 = 0');
            return;
        }

        // ✅ teacher يرى معلمي فرعه فقط
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

    // ─── مشترك: فلترة بالحلقات ───────────────────────────────────
    private function applyScopeByCircleIds(Builder $builder, string $table, $circleIds): void
    {
        match ($table) {
            'circles'
            => $builder->whereIn('id', $circleIds),

            'students'
            => $builder->whereIn('circle_id', $circleIds),

            'subscriptions', 'attendances'
            // ✅ subquery بدل pluck
            => $builder->whereIn(
                'student_id',
                DB::table('students')
                    ->select('id')
                    ->whereIn('circle_id', $circleIds)
            ),

            default => null,
        };
    }

    // ─── static cache: teacher record ────────────────────────────
    private function getTeacher(int $userId): ?object
    {
        // ✅ static cache يستمر طول الـ request بدل instance cache
        if (!isset(self::$teacherCache[$userId])) {
            self::$teacherCache[$userId] = DB::table('teachers')
                ->where('user_id', $userId)
                ->select(['id', 'center_id', 'user_id'])
                ->first();
        }

        return self::$teacherCache[$userId];
    }

    // ─── static cache: circle IDs للـ supervisor ─────────────────
    private function getSupervisorCircleIds(object $teacher)
    {
        $cacheKey = "supervisor_{$teacher->id}";

        if (!isset(self::$circleIdsCache[$cacheKey])) {
            self::$circleIdsCache[$cacheKey] = DB::table('circles')
                ->where('supervisor_id', $teacher->id)
                ->where('center_id', $teacher->center_id)
                ->where('is_active', true) // ✅ تغيير من status='مقيد' إلى is_active=true
                ->pluck('id');
        }

        return self::$circleIdsCache[$cacheKey];
    }

    // ─── static cache: circle IDs للـ teacher ────────────────────
    private function getTeacherCircleIds(object $teacher)
    {
        $cacheKey = "teacher_{$teacher->id}";

        if (!isset(self::$circleIdsCache[$cacheKey])) {
            self::$circleIdsCache[$cacheKey] = DB::table('circles')
                ->whereIn('id', function ($sub) use ($teacher) {
                    $sub->select('circle_id')
                        ->from('circle_teacher')
                        ->where('teacher_id', $teacher->id);
                })
                ->where('center_id', $teacher->center_id)
                ->where('is_active', true) // ✅ تغيير من status='مقيد' إلى is_active=true
                ->pluck('id');
        }

        return self::$circleIdsCache[$cacheKey];
    }

    // ✅ مسح الـ cache — يُستدعى في الـ Tests أو عند تغيير الدور
    public static function clearCache(): void
    {
        self::$teacherCache   = [];
        self::$circleIdsCache = [];
    }
}
