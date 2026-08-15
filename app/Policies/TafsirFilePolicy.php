<?php

namespace App\Policies;

use App\Models\TafsirFile;
use App\Models\User;

class TafsirFilePolicy
{
    /**
     * Determine whether the user can view any tafsir files.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'admin',
            'general_manager',
            'manager',
            'supervisor',
            'teacher',
        ]);
    }

    /**
     * Determine whether the user can view the tafsir file.
     */
    public function view(User $user, TafsirFile $tafsirFile): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create tafsir files.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'admin',
            'general_manager',
            'manager',
        ]);
    }

    /**
     * Determine whether the user can update the tafsir file.
     */
    public function update(User $user, TafsirFile $tafsirFile): bool
    {
        return $user->hasAnyRole([
            'admin',
            'general_manager',
            'manager',
        ]);
    }

    /**
     * Determine whether the user can delete the tafsir file.
     */
    public function delete(User $user, TafsirFile $tafsirFile): bool
    {
        return $user->hasAnyRole([
            'admin',
            'general_manager',
            'manager',
        ]);
    }
}
