<?php

namespace App\Policies;

use App\Models\SubscriptionPrice;
use App\Models\User;

class SubscriptionPricePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view prices');
    }

    public function view(User $user, SubscriptionPrice $price): bool
    {
        return $user->hasPermissionTo('view prices');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('edit prices');
    }

    public function update(User $user, SubscriptionPrice $price): bool
    {
        return $user->hasPermissionTo('edit prices');
    }

    public function delete(User $user, SubscriptionPrice $price): bool
    {
        return $user->hasRole('admin');
    }
}