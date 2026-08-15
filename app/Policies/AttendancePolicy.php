<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use App\Traits\ResolvesUserScope;

class AttendancePolicy
{
    use ResolvesUserScope;

    public function viewAny(User $user): bool
    {
        return $user->can('view attendance');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('admin')) return true;

        if (!$user->can('view attendance')) return false;

        return $this->userCanManageAttendance($user, $attendance);
    }

    public function create(User $user): bool
    {
        return $user->can('create attendance');
    }

    public function update(User $user, ?Attendance $attendance = null): bool
    {
        if (!$user->can('edit attendance')) return false;

        // استدعاء من index (بدون instance)
        if ($attendance === null) {
            return true;
        }

        if ($user->hasRole('admin')) return true;

        return $this->userCanManageAttendance($user, $attendance);
    }

    public function delete(User $user, ?Attendance $attendance = null): bool
    {
        if (!$user->hasRole('admin')) return false;

        // استدعاء من index (بدون instance)
        if ($attendance === null) {
            return true;
        }

        return true;
    }
    
    private function userCanManageAttendance(User $user, Attendance $attendance): bool
    {
        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        // manager → طلاب فرعه
        if ($user->hasRole('manager')) {
            return $attendance->student->center_id === $teacher->center_id;
        }

        // supervisor/teacher → حلقاتهم
        $circleIds = $this->getAccessibleCircleIds($user);
        return $circleIds->contains($attendance->student->circle_id);
    }
}
