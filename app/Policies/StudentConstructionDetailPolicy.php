<?php

namespace App\Policies;

use App\Models\StudentConstructionDetail;
use App\Models\User;
use App\Services\UserAccessService;

class StudentConstructionDetailPolicy
{
    public function __construct(
        private UserAccessService $accessService
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->can('view student construction details');
    }

    public function view(User $user, StudentConstructionDetail $studentConstructionDetail): bool
    {
        if (!$user->can('view student construction details')) {
            return false;
        }

        // Admin & General Manager: full access
        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        // Guardian: can only view their own children's records
        if ($user->hasRole('guardian')) {
            return $studentConstructionDetail->student
                && (int) $studentConstructionDetail->student->guardian_id === (int) $user->id;
        }

        // Teachers, Supervisors, Managers: access via circle
        if (!$studentConstructionDetail->student || !$studentConstructionDetail->student->circle_id) {
            return false;
        }

        return $this->accessService->canAccessCircle(
            $user,
            (int) $studentConstructionDetail->student->circle_id
        );
    }

    public function create(User $user): bool
    {
        return $user->can('create student construction details');
    }

    public function update(User $user, StudentConstructionDetail $studentConstructionDetail): bool
    {
        if (!$user->can('edit student construction details')) {
            return false;
        }

        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        if ($user->hasRole('guardian')) {
            return false; // Guardian cannot edit construction details
        }

        if (!$studentConstructionDetail->student || !$studentConstructionDetail->student->circle_id) {
            return false;
        }

        return $this->accessService->canAccessCircle(
            $user,
            (int) $studentConstructionDetail->student->circle_id
        );
    }

    public function delete(User $user, StudentConstructionDetail $studentConstructionDetail): bool
    {
        if (!$user->can('delete student construction details')) {
            return false;
        }

        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        if ($user->hasRole('guardian')) {
            return false; // Guardian cannot delete construction details
        }

        if (!$studentConstructionDetail->student || !$studentConstructionDetail->student->circle_id) {
            return false;
        }

        return $this->accessService->canAccessCircle(
            $user,
            (int) $studentConstructionDetail->student->circle_id
        );
    }

    public function restore(User $user, StudentConstructionDetail $studentConstructionDetail): bool
    {
        return $user->hasRole(['admin', 'general_manager']);
    }

    public function forceDelete(User $user, StudentConstructionDetail $studentConstructionDetail): bool
    {
        return $user->hasRole(['admin', 'general_manager']);
    }
}
