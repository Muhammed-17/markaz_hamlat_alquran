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

        $record = $this->getTeacherRecord($user);
        if (!$record) return false;

        // ✅ المعلم يرى نفسه فقط
        if ($user->hasRole('teacher')) {
            return $user->id === $teacher->user_id;
        }

        return $this->getAccessibleTeachersQuery($user, $record)
            ->whereKey($teacher->id)
            ->exists();
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
        if (!$record) return false;

        // ✅ تقييد التعديل على المعلمين في نفس الفرع فقط
        return $teacher->center_id === $record->center_id;
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        if (!$user->can('delete teachers')) return false;

        // ✅ منع حذف النفس
        if ($user->id === $teacher->user_id) return false;

        // ✅ منع حذف الإداريين
        if ($teacher->user->hasRole(['admin', 'general_manager'])) return false;

        if ($user->can('view all teachers')) return true;

        $record = $this->getTeacherRecord($user);
        if (!$record) return false;

        return $teacher->center_id === $record->center_id;
    }

    public function toggle(User $user, Teacher $teacher): bool
    {
        if (!$user->can('toggle teacher status')) return false;

        // ✅ منع تعطيل النفس
        if ($user->id === $teacher->user_id) return false;

        // ✅ منع تعطيل الإداريين
        if ($teacher->user->hasRole(['admin', 'general_manager'])) return false;

        if ($user->can('view all teachers')) return true;

        $record = $this->getTeacherRecord($user);
        if (!$record) return false;

        return $teacher->center_id === $record->center_id;
    }
}
