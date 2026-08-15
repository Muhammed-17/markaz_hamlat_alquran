<?php

namespace App\Policies;

use App\Models\Examiner;
use App\Models\User;

/**
 * Class ExaminerPolicy
 *
 * Authorization rules for the Examiner model.
 */
class ExaminerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view examiners');
    }

    public function view(User $user, Examiner $examiner): bool
    {
        return $user->can('view examiners');
    }

    public function create(User $user): bool
    {
        return $user->can('create examiners');
    }

    public function update(User $user, Examiner $examiner): bool
    {
        return $user->can('edit examiners');
    }

    public function delete(User $user, Examiner $examiner): bool
    {
        return $user->can('delete examiners');
    }
}
