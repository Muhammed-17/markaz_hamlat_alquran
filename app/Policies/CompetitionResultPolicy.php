<?php

namespace App\Policies;

use App\Models\CompetitionResult;
use App\Models\User;

/**
 * Class CompetitionResultPolicy
 *
 * يتحكم في عرض/تعديل/اعتماد النتائج.
 * الإدارة فقط تستطيع اعتماد النتائج وإغلاق المسابقة.
 */
class CompetitionResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view competition results');
    }

    public function view(User $user, CompetitionResult $result): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, CompetitionResult $result): bool
    {
        return $user->hasPermissionTo('edit competition results');
    }

    /**
     * اعتماد النتائج وإغلاق المسابقة: للإدارة فقط.
     */
    public function finalize(User $user): bool
    {
        return $user->hasPermissionTo('finalize competition results');
    }
}
