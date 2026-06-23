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

        $isPrimary = $teacher->center_id === $record->center_id;
        $isExternal = $teacher->circles()->where('circles.center_id', $record->center_id)->exists();

        // المعلم الخارجي يمكن تعديل حلقاته فقط
        return $isPrimary || ($isExternal && $user->can('edit external teachers'));
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        if (!$user->can('delete teachers')) return false;
        if ($user->can('view all teachers')) return true;

        $record = $this->getTeacherRecord($user);
        if (!$record) return false;

        return $teacher->center_id === $record->center_id;
    }

    public function toggle(User $user, Teacher $teacher): bool
    {
        if (!$user->can('toggle teacher status')) return false;
        if ($user->can('view all teachers')) return true;

        $record = $this->getTeacherRecord($user);

        if (!$record) return false;

        // ⚠️ تعطيل/تفعيل الحساب يفضل حصره بمدير الفرع الأساسي التابع له المعلم
        return $teacher->center_id === $record->center_id;
    }
}
