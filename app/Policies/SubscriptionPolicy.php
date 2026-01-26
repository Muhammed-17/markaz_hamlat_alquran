<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view subscriptions') || 
               $user->hasPermissionTo('view own subscriptions');
    }

    public function view(User $user, Subscription $subscription): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('guardian')) return $subscription->student->guardian_id === $user->id;
        if ($user->hasRole('teacher')) {
            return $subscription->student->circle && 
                   $subscription->student->circle->teachers->contains('user_id', $user->id);
        }
        if ($user->hasRole('supervisor')) return true;
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create subscriptions');
    }

    public function update(User $user, Subscription $subscription): bool
    {
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('teacher') && $user->hasPermissionTo('edit subscriptions')) {
            return $subscription->student->circle && 
                   $subscription->student->circle->teachers->contains('user_id', $user->id);
        }
        return false;
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->hasRole('admin');
    }
}