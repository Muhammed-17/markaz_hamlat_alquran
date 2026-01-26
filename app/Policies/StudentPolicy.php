<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view students');
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('guardian')) return $student->guardian_id === $user->id;
        if ($user->hasRole(['teacher', 'supervisor'])) {
            return $student->circle && $student->circle->teachers->contains('user_id', $user->id);
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create students');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasRole('admin');
    }
}