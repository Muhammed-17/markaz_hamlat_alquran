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
        return $user->can('view subscriptions');
    }

    public function view(User $user, Subscription $subscription): bool
    {
        if ($user->hasAnyRole(['admin', 'general_manager'])) return true;

        if ($user->hasRole('guardian')) {
            return $user->can('view own subscriptions')
                && $subscription->student?->guardian_id === $user->id;
        }

        return $user->can('view subscriptions')
            && $this->canAccessSubscription($user, $subscription);
    }

    public function create(User $user): bool
    {
        return $user->can('create subscriptions');
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return $user->can('edit subscriptions')
            && $this->canAccessSubscription($user, $subscription);
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->can('delete subscriptions')
            && $this->canAccessSubscription($user, $subscription);
    }

    private function canAccessSubscription(User $user, Subscription $subscription): bool
    {
        if ($user->hasAnyRole(['admin', 'general_manager'])) return true;

        $teacher = $this->getTeacherRecord($user);
        if (!$teacher) return false;

        if ($user->hasRole('manager')) {
            return $subscription->student->circle?->center_id === $teacher->center_id;
        }

        return $this->getAccessibleCircleIds($user)
            ->contains($subscription->student->circle_id);
    }
}
