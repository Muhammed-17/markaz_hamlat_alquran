<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use App\Traits\ResolvesUserScope;

class TeacherPolicy
{
    use ResolvesUserScope;

    public function viewAny(User $user): bool
    {
        return $user->can('view teachers') || $user->can('view all teachers');
    }

    public function view(User $user, Teacher $teacher): bool
    {
        if (!$user->can('view teachers')) return false;
        if ($user->can('view all teachers')) return true;

        // manager/supervisor/teacher → فرعه بس
        $record = $this->getTeacherRecord($user);
        return $record && $teacher->center_id === $record->center_id;
    }

    public function create(User $user): bool
    {
        return $user->can('create teachers');
    }

    public function update(User $user, Teacher $teacher): bool
    {
        if (!$user->can('edit teachers')) return false;
        if ($user->can('view all teachers')) return true;

        $record = $this->getTeacherRecord($user);
        return $record && $teacher->center_id === $record->center_id;
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        if (!$user->can('delete teachers')) return false;
        if ($user->can('view all teachers')) return true;

        $record = $this->getTeacherRecord($user);
        return $record && $teacher->center_id === $record->center_id;
    }

    public function toggle(User $user, Teacher $teacher): bool
    {
        if (!$user->can('toggle teacher status')) return false;
        if ($user->can('view all teachers')) return true;

        $record = $this->getTeacherRecord($user);
        return $record && $teacher->center_id === $record->center_id;
    }
}
