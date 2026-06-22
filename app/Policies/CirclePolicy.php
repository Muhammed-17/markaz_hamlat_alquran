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

    // ─── helper ──────────────────────────────────────────────────
    private function canAccessCircle(User $user, Circle $circle): bool
    {
        if ($user->hasRole('admin')) return true;

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        // ✅ بغض النظر عن الدور: لو هو مشرف فعلي على هذه الحلقة (أي فرع) → مسموح
        if ($circle->supervisors->contains('id', $teacher->id)) {
            return true;
        }

        // manager → كل حلقات فرعه
        if ($user->hasRole('manager')) {
            return $circle->center_id === $teacher->center_id;
        }

        // teacher → حلقاته (رئيسي/مساعد) في فرعه
        if ($user->hasRole('teacher')) {
            $circleIds = $this->getAccessibleCircleIds($user);
            return $circleIds->contains($circle->id);
        }

        // supervisor بدون أي حلقة إشراف فعلية على هذه الحلقة بالذات → غير مسموح
        return false;
    }
}
