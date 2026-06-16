<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Circle;
use App\Models\Center;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Student;

trait ResolvesUserScope
{
    // ✅ static cache يستمر طول الـ request
    private static array $teacherRecordCache = [];

    // ─── جلب teacher record مع static cache ──────────────────────
    protected function getTeacherRecord(User $user): ?Teacher
    {
        if (array_key_exists($user->id, self::$teacherRecordCache)) {
            return self::$teacherRecordCache[$user->id];
        }

        return self::$teacherRecordCache[$user->id] =
            Teacher::where('user_id', $user->id)->first();
    }

    // ─── الحلقات المتاحة ──────────────────────────────────────────
    protected function getAccessibleCircles(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getAccessibleCirclesQuery($user)->get();
    }

    protected function getAccessibleCirclesQuery(User $user): \Illuminate\Database\Eloquent\Builder
    {
        // ✅ admin و general_manager يريان الكل
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Circle::orderBy('name');
        }

        // ✅ permission-based check بدل role-based
        if ($user->can('view all circles')) {
            return Circle::orderBy('name');
        }

        // ✅ guardian — حلقات أبنائه النشطين فقط
        if ($user->can('view own children')) {
            return Circle::whereIn(
                'id',
                Student::where('guardian_id', $user->id)
                    ->whereNotNull('circle_id')
                    ->where('status', 'مقيد') // ✅ الطلاب النشطون فقط
                    ->pluck('circle_id')
            )->orderBy('name');
        }

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher || !$teacher->center_id) {
            return Circle::whereRaw('1=0');
        }

        // manager — كل حلقات فرعه
        if ($user->hasRole('manager')) {
            return Circle::where('center_id', $teacher->center_id)
                ->orderBy('name');
        }

        // supervisor — حلقاته فقط
        if ($user->hasRole('supervisor')) {
            return Circle::where('supervisor_id', $teacher->id)
                ->where('center_id', $teacher->center_id)
                ->orderBy('name');
        }

        // teacher — حلقاته المسجل فيها
        $circleIds = $this->getTeacherCircleIds($teacher);
        if ($circleIds->isEmpty()) {
            return Circle::whereRaw('1=0');
        }

        return Circle::whereIn('id', $circleIds)
            ->where('center_id', $teacher->center_id)
            ->orderBy('name');
    }

    // ─── IDs الحلقات للفلترة ──────────────────────────────────────
    protected function getAccessibleCircleIds(User $user): Collection
    {
        // ✅ pluck مباشرة بدون query إضافية
        return $this->getAccessibleCirclesQuery($user)->pluck('id');
    }

    // ─── الفروع المتاحة ───────────────────────────────────────────
    protected function getAccessibleCenters(User $user): Collection
    {
        // ✅ admin و general_manager يريان كل الفروع
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Center::withoutGlobalScopes()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        // ✅ استخدام permission بدل role للـ manager
        if ($user->can('view all centers')) {
            return Center::withoutGlobalScopes()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        // guardian — فروع أبنائه فقط
        if ($user->hasRole('guardian')) {
            return Center::withoutGlobalScopes()
                ->select('id', 'name')
                ->whereIn(
                    'id',
                    Student::where('guardian_id', $user->id)
                        ->whereNotNull('center_id')
                        ->where('status', 'مقيد') // ✅ أبناء نشطون فقط
                        ->pluck('center_id')
                )
                ->orderBy('name')
                ->get();
        }

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher || !$teacher->center_id) return collect();

        return Center::withoutGlobalScopes()
            ->select('id', 'name')
            ->where('id', $teacher->center_id)
            ->get();
    }

    // ─── المعلمون المتاحون ────────────────────────────────────────
    protected function getAccessibleTeachers(User $user, ?Teacher $teacher): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Teacher::with('user.roles')
                ->whereHas('user', fn($u) => $u->where('status', 'active'))
                ->get();
        }

        if ($teacher && $teacher->center_id) {
            return Teacher::where('center_id', $teacher->center_id)
                ->whereHas('user', fn($u) => $u->where('status', 'active'))
                ->with('user.roles')
                ->get();
        }

        return Teacher::whereRaw('1 = 0')->get();
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

    // ─── helper: circle IDs للـ teacher ──────────────────────────
    private function getTeacherCircleIds(Teacher $teacher): Collection
    {
        return DB::table('circle_teacher')
            ->where('teacher_id', $teacher->id)
            ->pluck('circle_id');
    }

    // ─── تطبيق فلتر الحلقات على أي query ────────────────────────
    protected function applyCircleFilter($query, User $user, $circleIds): void
    {
        if ($user->hasRole(['admin', 'general_manager']) || $user->can('view all circles')) {
            return;
        }

        $query->whereIn('circle_id', $circleIds);
    }

    // ✅ مسح الـ static cache — للـ Tests
    public static function clearScopeCache(): void
    {
        self::$teacherRecordCache = [];
    }
}
