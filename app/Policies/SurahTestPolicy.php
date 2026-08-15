<?php

namespace App\Policies;

use App\Models\SurahTest;
use App\Models\User;
use App\Services\UserAccessService;

/**
 * Authorization rules for SurahTest.
 * Access is gated by permissions (Spatie) first, then scoped by circle
 * access via UserAccessService — no duplicated center/circle logic here.
 */
class SurahTestPolicy
{
    public function __construct(
        private UserAccessService $access
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->can('view surah tests');
    }

    public function view(User $user, SurahTest $surahTest): bool
    {
        if (!$user->can('view surah tests')) {
            return false;
        }

        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        return $this->access->canAccessCircle($user, (int) $surahTest->circle_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create surah tests');
    }

    public function update(User $user, SurahTest $surahTest): bool
    {
        if (!$user->can('update surah tests')) {
            return false;
        }

        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        return $this->access->canAccessCircle($user, (int) $surahTest->circle_id);
    }

    public function delete(User $user, SurahTest $surahTest): bool
    {
        if (!$user->can('delete surah tests')) {
            return false;
        }
        
        if ($user->hasRole(['admin', 'general_manager'])) {
            return true;
        }

        if ($user->hasRole(['manager', 'supervisor'])) {
            return $this->access->canAccessCircle($user, (int) $surahTest->circle_id);
        }

        return false;
    }
}
