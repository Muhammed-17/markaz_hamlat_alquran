<?php

namespace App\Policies;

use App\Models\CollectionRound;
use App\Models\User;
use App\Traits\ResolvesUserScope;

class CollectionRoundPolicy
{
    use ResolvesUserScope;

    public function viewAny(User $user): bool
    {
        return $user->can('view collection rounds');
    }

    public function view(User $user, CollectionRound $round): bool
    {
        if ($user->hasAnyRole(['admin', 'general_manager'])) {
            return true;
        }

        if (! $user->can('view collection rounds')) {
            return false;
        }

        return $this->userCanAccessCircle($user, $round->circle_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create collection rounds');
    }

    public function update(User $user, CollectionRound $round): bool
    {
        if (! $user->can('edit collection rounds')) {
            return false;
        }

        if ($round->status === 'confirmed') {
            return $user->hasRole('admin');
        }

        if ($user->hasAnyRole(['admin', 'general_manager', 'manager'])) {
            return true;
        }

        return $this->userCanAccessCircle($user, $round->circle_id) && $round->status === 'pending';
    }

    public function delete(User $user, CollectionRound $round): bool
    {
        if (! $user->can('delete collection rounds')) {
            return false;
        }

        if ($round->status === 'confirmed') {
            return $user->hasRole('admin');
        }

        if ($user->hasAnyRole(['admin', 'general_manager', 'manager'])) {
            return true;
        }

        return $this->userCanAccessCircle($user, $round->circle_id) && $round->status === 'pending';
    }

    public function confirm(User $user, CollectionRound $round): bool
    {
        return $user->hasAnyRole(['admin', 'general_manager', 'manager'])
            && $user->can('confirm collection rounds');
    }

    private function userCanAccessCircle(User $user, int $circleId): bool
    {
        return $this->getAccessibleCircleIds($user)->contains($circleId);
    }
}
