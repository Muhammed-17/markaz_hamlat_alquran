<?php

namespace App\Policies;

use App\Models\Level;
use App\Models\User;

/**
 * Class LevelPolicy
 *
 * Authorization rules for the Level model.
 */
class LevelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view levels');
    }

    public function view(User $user, Level $level): bool
    {
        return $user->can('view levels');
    }

    public function create(User $user): bool
    {
        return $user->can('create levels');
    }

    public function update(User $user, Level $level): bool
    {
        return $user->can('edit levels');
    }

    public function delete(User $user, Level $level): bool
    {
        return $user->can('delete levels');
    }
}
