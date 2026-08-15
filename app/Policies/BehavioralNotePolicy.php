<?php

namespace App\Policies;

use App\Models\BehavioralNote;
use App\Models\User;
use App\Services\UserAccessService;

/**
 * Policy: BehavioralNote
 *
 * Controls access based on permissions and circle assignments.
 */
class BehavioralNotePolicy
{
    public function __construct(
        protected UserAccessService $userAccessService
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->can('view behavioral notes');
    }

    public function view(User $user, BehavioralNote $behavioralNote): bool
    {
        if (!$user->can('view behavioral notes')) {
            return false;
        }

        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        return $this->userAccessService->canAccessCircle($user, (int) $behavioralNote->circle_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create behavioral notes');
    }

    public function update(User $user, BehavioralNote $behavioralNote): bool
    {
        if (!$user->can('edit behavioral notes')) {
            return false;
        }

        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        if ($user->hasRole(['manager', 'supervisor'])) {
            return $this->userAccessService->canAccessCircle($user, (int) $behavioralNote->circle_id);
        }

        // المعلم يقدر يعدّل بس السجلات اللي هو بلّغ عنها
        $teacher = $this->userAccessService->teacher($user);
        return $teacher && (int) $teacher->id === (int) $behavioralNote->teacher_id;
    }

    public function delete(User $user, BehavioralNote $behavioralNote): bool
    {
        if (!$user->can('delete behavioral notes')) {
            return false;
        }

        return $this->update($user, $behavioralNote);
    }

    public function restore(User $user, BehavioralNote $behavioralNote): bool
    {
        return $user->can('delete behavioral notes')
            && $user->hasRole(['admin', 'general_manager']);
    }

    public function forceDelete(User $user, BehavioralNote $behavioralNote): bool
    {
        return $user->can('delete behavioral notes')
            && $user->hasRole(['admin', 'general_manager']);
    }

    public function recordAction(User $user, BehavioralNote $behavioralNote): bool
    {
        if (!$user->can('approve behavioral notes')) {
            return false;
        }

        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        if ($user->hasRole(['manager', 'supervisor'])) {
            return $this->userAccessService->canAccessCircle($user, (int) $behavioralNote->circle_id);
        }

        return false;
    }
}
