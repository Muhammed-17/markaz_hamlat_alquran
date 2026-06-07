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
        return $user->can('view attendance')
            || $user->can('view own attendance');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('guardian')) {
            return $user->can('view own attendance')
                && $attendance->student->guardian_id === $user->id;
        }

        if (!$user->can('view attendance')) return false;

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

    public function create(User $user): bool
    {
        return $user->can('create attendance');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if (!$user->can('edit attendance')) return false;
        if ($user->hasRole('admin')) return true;

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        if ($user->hasRole('manager')) {
            return $attendance->student->center_id === $teacher->center_id;
        }

        $circleIds = $this->getAccessibleCircleIds($user);
        return $circleIds->contains($attendance->student->circle_id);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->hasRole('admin');
    }
}
