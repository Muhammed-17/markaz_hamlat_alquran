<?php

namespace App\Policies;

use App\Models\User as UserModel;

class UserPolicy
{
    public function viewAny(UserModel $user): bool
    {
        return $user->hasPermissionTo('view users');
    }

    public function view(UserModel $user, UserModel $model): bool
    {
        return $user->hasRole('admin') || $user->id === $model->id;
    }

    public function create(UserModel $user): bool
    {
        return $user->hasPermissionTo('create users');
    }

    public function update(UserModel $user, UserModel $model): bool
    {
        // يمكن للمستخدم تعديل نفسه، أو المدير تعديل الجميع
        return $user->hasRole('admin') || $user->id === $model->id;
    }

    public function delete(UserModel $user, UserModel $model): bool
    {
        return $user->hasRole('admin') && $user->id !== $model->id;
    }

    public function manageRoles(UserModel $user): bool
    {
        return $user->hasPermissionTo('manage roles');
    }
}