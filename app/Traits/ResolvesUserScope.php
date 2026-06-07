<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Circle;
use App\Models\Center;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

trait ResolvesUserScope
{
    // ─── جلب teacher record مرة واحدة ────────────────────────────
    protected function getTeacherRecord(User $user): ?object
    {
        return DB::table('teachers')
            ->where('user_id', $user->id)
            ->first();
    }

    // ─── الحلقات المتاحة حسب الـ role ────────────────────────────
    protected function getAccessibleCircles(User $user): Collection
    {
        if ($user->hasRole('admin')) {
            return Circle::orderBy('name')->get();
        }

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return collect();

        if ($user->hasRole('manager')) {
            return Circle::where('center_id', $teacher->center_id)
                ->orderBy('name')->get();
        }

        if ($user->hasRole('supervisor')) {
            return Circle::where('supervisor_id', $teacher->id)
                ->where('center_id', $teacher->center_id)
                ->orderBy('name')->get();
        }

        if ($user->hasRole('teacher')) {
            $circleIds = DB::table('circle_teacher')
                ->where('teacher_id', $teacher->id)
                ->pluck('circle_id');

            return Circle::whereIn('id', $circleIds)
                ->where('center_id', $teacher->center_id)
                ->orderBy('name')->get();
        }

        return collect();
    }

    // ─── الفروع المتاحة حسب الـ role ─────────────────────────────
    protected function getAccessibleCenters(User $user): Collection
    {
        if ($user->hasRole('admin')) {
            return Center::select('id', 'name')->orderBy('name')->get();
        }

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return collect();

        return Center::select('id', 'name')
            ->where('id', $teacher->center_id)
            ->get();
    }

    // ─── IDs الحلقات المتاحة — للفلترة في Queries ────────────────
    protected function getAccessibleCircleIds(User $user): Collection
    {
        if ($user->hasRole('admin')) {
            return Circle::pluck('id');
        }

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return collect();

        if ($user->hasRole('manager')) {
            return Circle::where('center_id', $teacher->center_id)
                ->pluck('id');
        }

        if ($user->hasRole('supervisor')) {
            return Circle::where('supervisor_id', $teacher->id)
                ->where('center_id', $teacher->center_id)
                ->pluck('id');
        }

        if ($user->hasRole('teacher')) {
            return DB::table('circle_teacher')
                ->where('teacher_id', $teacher->id)
                ->pluck('circle_id')
                ->intersect(
                    Circle::where('center_id', $teacher->center_id)->pluck('id')
                );
        }

        return collect();
    }
    // في App\Traits\ResolvesUserScope

    protected function getAccessibleTeachers(?object $teacher): \Illuminate\Database\Eloquent\Collection
    {
        // admin → كل المعلمين
        if (!$teacher) {
            return Teacher::with('user.roles')->get();
        }

        // manager/supervisor/teacher → معلمي فرعه بس
        return Teacher::where('center_id', $teacher->center_id)
            ->with('user.roles')
            ->get();
    }

    protected function getAccessibleSupervisors(?object $teacher): \Illuminate\Database\Eloquent\Collection
    {
        $query = Teacher::whereHas(
            'user',
            fn($q) =>
            $q->whereHas(
                'roles',
                fn($r) =>
                $r->where('name', 'supervisor')
            )
        )->with('user.roles');

        // مش admin → فلتر بالفرع
        if ($teacher) {
            $query->where('center_id', $teacher->center_id);
        }

        return $query->get();
    }
}
