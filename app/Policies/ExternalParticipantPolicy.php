<?php

namespace App\Policies;

use App\Models\ExternalParticipant;
use App\Models\User;

/**
 * Class ExternalParticipantPolicy
 *
 * Authorization rules for the ExternalParticipant model.
 */
class ExternalParticipantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view external participants');
    }

    public function view(User $user, ExternalParticipant $externalParticipant): bool
    {
        return $user->can('view external participants');
    }

    public function create(User $user): bool
    {
        return $user->can('create external participants');
    }

    public function update(User $user, ExternalParticipant $externalParticipant): bool
    {
        return $user->can('edit external participants');
    }

    public function delete(User $user, ExternalParticipant $externalParticipant): bool
    {
        return $user->can('delete external participants');
    }
}
