<?php

namespace App\Policies;

use App\Models\Circle;
use App\Models\User;
use App\Traits\ResolvesUserScope;

class CirclePolicy
{
    use ResolvesUserScope;

    public function viewAny(User $user): bool
    {
        return $user->can('view circles');
    }

    public function view(User $user, Circle $circle): bool
    {
        if (!$user->can('view circles')) return false;
        return $this->canAccessCircle($user, $circle);
    }

    public function create(User $user): bool
    {
        return $user->can('create circles');
    }

    public function update(User $user, Circle $circle): bool
    {
        if (!$user->can('edit circles')) return false;
        return $this->canAccessCircle($user, $circle);
    }

    public function delete(User $user, Circle $circle): bool
    {
        if (!$user->can('delete circles')) return false;
        return $this->canAccessCircle($user, $circle);
    }

    public function manageTeachers(User $user, Circle $circle): bool
    {
        if (!$user->can('manage circle teachers')) return false;
        return $this->canAccessCircle($user, $circle);
    }

    private function canAccessCircle(User $user, Circle $circle): bool
    {
        // ✅ الإداريون
        if ($user->hasRole(['admin', 'general_manager'])) return true;

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        // ✅ المشرف النقي — فقط الحلقات التي يشرف عليها
        if ($user->hasRole('supervisor') && !$user->hasRole(['manager', 'teacher', 'admin', 'general_manager'])) {
            return $circle->supervisors()->where('teachers.id', $teacher->id)->exists();
        }

        // ✅ المدير — فرعه
        if ($user->hasRole('manager')) {
            return $circle->center_id === $teacher->center_id;
        }

        // ✅ المعلم — حلقاته (main/assistant) + مشرف
        if ($user->hasRole('teacher')) {
            $isMainOrAssistant = DB::table('circle_teacher')
                ->where('circle_id', $circle->id)
                ->where('teacher_id', $teacher->id)
                ->whereIn('role', ['main', 'assistant'])
                ->exists();

            $isSupervisor = $circle->supervisors()->where('teachers.id', $teacher->id)->exists();

            return $isMainOrAssistant || $isSupervisor;
        }

        return false;
    }
}
