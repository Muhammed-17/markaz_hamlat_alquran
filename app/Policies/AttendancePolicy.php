<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view attendance') || 
               $user->hasPermissionTo('view own attendance');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('guardian')) return $attendance->student->guardian_id === $user->id;
        if ($user->hasRole(['teacher', 'supervisor'])) {
            return $attendance->student->circle && 
                   $attendance->student->circle->teachers->contains('user_id', $user->id);
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create attendance');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('teacher')) {
            return $attendance->student->circle && 
                   $attendance->student->circle->teachers->contains('user_id', $user->id);
        }
        return false;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->hasRole('admin');
    }
}