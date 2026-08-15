<?php

namespace App\Policies;

use App\Models\GroupSessionPlan;
use App\Models\User;
use App\Services\UserAccessService;

/**
 * Policy: GroupSessionPlan
 *
 * Controls access based on permissions and circle assignments.
 */
class GroupSessionPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view group session plans');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GroupSessionPlan $groupSessionPlan): bool
    {
        if (!$user->can('view group session plans')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        $circleIds = UserAccessService::circleIdsForUser($user);

        return $circleIds->contains($groupSessionPlan->circle_id)
            || $circleIds->intersect(
                $groupSessionPlan->student?->circles?->pluck('id') ?? collect()
            )->isNotEmpty();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create group session plans');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GroupSessionPlan $groupSessionPlan): bool
    {
        if (!$user->can('edit group session plans')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        $plan = $groupSessionPlan->weeklyPlan;

        if ($user->hasRole('supervisor')) {
            return UserAccessService::circleIdsForUser($user)
                ->contains($plan->circle_id);
        }

        // Teacher can only update their own records
        return $user->teacher?->id === $plan->teacher_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GroupSessionPlan $groupSessionPlan): bool
    {
        return $this->update($user, $groupSessionPlan);
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GroupSessionPlan $groupSessionPlan): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GroupSessionPlan $groupSessionPlan): bool
    {
        return $user->hasRole('admin');
    }

}
