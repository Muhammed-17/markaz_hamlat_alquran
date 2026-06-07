<?php

namespace App\Policies;

use App\Models\User as UserModel;
use App\Traits\ResolvesUserScope;

class UserPolicy
{
    use ResolvesUserScope;

    public function viewAny(UserModel $user): bool
    {
        return $user->can('view users');
    }

    public function view(UserModel $user, UserModel $model): bool
    {
        // كل مستخدم يشوف نفسه
        if ($user->id === $model->id) return true;
        if (!$user->can('view users')) return false;
        if ($user->hasRole('admin')) return true;

        // manager → مستخدمي فرعه بس
        $record = $this->getTeacherRecord($user);
        return $record && $model->center_id === $record->center_id;
    }

    public function create(UserModel $user): bool
    {
        return $user->can('create users');
    }

    public function update(UserModel $user, UserModel $model): bool
    {
        // كل مستخدم يعدل نفسه — لو عنده permission
        if ($user->id === $model->id && $user->can('edit profile')) return true;
        if (!$user->can('edit users')) return false;
        if ($user->hasRole('admin')) return true;

        // manager → مستخدمي فرعه بس
        $record = $this->getTeacherRecord($user);
        return $record && $model->center_id === $record->center_id;
    }

    public function delete(UserModel $user, UserModel $model): bool
    {
        // مينفعش تحذف نفسك
        if ($user->id === $model->id) return false;
        return $user->hasRole('admin') && $user->can('delete users');
    }

    public function manageRoles(UserModel $user): bool
    {
        return $user->can('manage roles');
    }
}