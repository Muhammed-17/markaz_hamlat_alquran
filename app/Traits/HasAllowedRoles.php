<?php

namespace App\Traits;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

trait HasAllowedRoles
{
    /**
     * الحصول على الأدوار المسموحة عند إنشاء معلم جديد
     */
    protected function getAllowedRolesForCreate($user): Collection
    {
        // ✅ admin فقط يمكنه إنشاء general_manager
        if ($user->hasRole('admin')) {
            return Role::whereNotIn('name', ['admin', 'guardian'])
                ->orderBy('name')
                ->get();
        }

        // ✅ general_manager يمكنه إنشاء manager, supervisor, teacher
        if ($user->hasRole('general_manager')) {
            return Role::whereNotIn('name', ['admin', 'guardian', 'general_manager'])
                ->orderBy('name')
                ->get();
        }

        // ✅ manager يمكنه إنشاء supervisor, teacher
        if ($user->hasRole('manager')) {
            return Role::whereNotIn('name', ['admin', 'guardian', 'general_manager', 'manager'])
                ->orderBy('name')
                ->get();
        }

        // ✅ supervisor و teacher يمكنهم إنشاء teacher فقط
        return Role::whereNotIn('name', ['admin', 'guardian', 'general_manager', 'manager', 'supervisor'])
            ->orderBy('name')
            ->get();
    }

    /**
     * الحصول على الأدوار المسموحة عند تعديل معلم موجود
     */
    protected function getAllowedRolesForEdit($user, ?Teacher $teacher = null): Collection
    {
        // إذا كان المستخدم يعدل نفسه، يسمح فقط بدوره الحالي
        if ($teacher && $user->id === $teacher->user_id) {
            return $teacher->user->roles;
        }

        // ✅ admin فقط يمكنه تعديل إلى general_manager
        if ($user->hasRole('admin')) {
            $allowed = Role::whereNotIn('name', ['admin', 'guardian'])
                ->orderBy('name')
                ->get();
        }
        // ✅ general_manager يمكنه تعديل إلى manager, supervisor, teacher
        elseif ($user->hasRole('general_manager')) {
            $allowed = Role::whereNotIn('name', ['admin', 'guardian', 'general_manager'])
                ->orderBy('name')
                ->get();
        }
        // ✅ manager يمكنه تعديل إلى supervisor, teacher
        elseif ($user->hasRole('manager')) {
            $allowed = Role::whereNotIn('name', ['admin', 'guardian', 'general_manager', 'manager'])
                ->orderBy('name')
                ->get();
        }
        // ✅ supervisor و teacher
        else {
            $allowed = Role::whereNotIn('name', ['admin', 'guardian', 'general_manager', 'manager', 'supervisor'])
                ->orderBy('name')
                ->get();
        }

        // إضافة الدور الحالي إذا لم يكن في القائمة
        $currentRole = $teacher?->user->roles->first();
        if ($currentRole && !$allowed->contains('name', $currentRole->name)) {
            $allowed->push($currentRole);
        }

        return $allowed;
    }
}