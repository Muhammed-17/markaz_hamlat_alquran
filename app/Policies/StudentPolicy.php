<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view students') || $user->hasRole('guardian');
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasPermissionTo('view students')) return true;

        if ($user->hasRole('guardian')) {
            return $student->guardian_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create students');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('edit students');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('delete students');
    }

    public function manageStatus(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('manage student status');
    }

    public function assignCircle(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('assign student to circle');
    }

    public function recordPayment(User $user, Student $student): bool
    {
        if ($student->status === 'inactive') return false;
        return $user->hasPermissionTo('collect subscription');
    }
}