<?php

namespace App\Policies;

use App\Models\Circle;
use App\Models\User;

class CirclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view circles');
    }

    public function view(User $user, Circle $circle): bool
    {
        return $user->hasRole(['admin', 'supervisor']) || 
               ($user->hasRole('teacher') && 
                $circle->teachers->contains('user_id', $user->id));
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create circles');
    }

    public function update(User $user, Circle $circle): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionTo('edit circles');
    }

    public function delete(User $user, Circle $circle): bool
    {
        return $user->hasRole('admin');
    }

    public function manageTeachers(User $user, Circle $circle): bool
    {
        return $user->hasPermissionTo('manage circle teachers');
    }
}