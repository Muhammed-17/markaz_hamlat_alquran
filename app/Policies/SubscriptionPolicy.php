<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;
use App\Traits\ResolvesUserScope;

class SubscriptionPolicy
{
    use ResolvesUserScope;

    public function viewAny(User $user): bool
    {
        return $user->can('view subscriptions')
            || $user->can('view own subscriptions');
    }

    public function view(User $user, Subscription $subscription): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('guardian')) {
            return $user->can('view own subscriptions')
                && $subscription->student->guardian_id === $user->id;
        }

        if (!$user->can('view subscriptions')) return false;

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        if ($user->hasRole('manager')) {
            return $subscription->student->center_id === $teacher->center_id;
        }

        $circleIds = $this->getAccessibleCircleIds($user);
        return $circleIds->contains($subscription->student->circle_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create subscriptions');
    }

    public function update(User $user, Subscription $subscription): bool
    {
        if (!$user->can('edit subscriptions')) return false;
        if ($user->hasRole('admin')) return true;

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        if ($user->hasRole('manager')) {
            return $subscription->student->center_id === $teacher->center_id;
        }

        $circleIds = $this->getAccessibleCircleIds($user);
        return $circleIds->contains($subscription->student->circle_id);
    }

    public function collect(User $user, Subscription $subscription): bool
    {
        if (!$user->can('collect subscription')) return false;
        if ($subscription->student->status === 'inactive') return false;
        if ($user->hasRole('admin')) return true;

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        if ($user->hasRole('manager')) {
            return $subscription->student->center_id === $teacher->center_id;
        }

        $circleIds = $this->getAccessibleCircleIds($user);
        return $circleIds->contains($subscription->student->circle_id);
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->hasRole('admin');
    }
}
