<?php

namespace App\Policies;

use App\Models\CompetitionParticipant;
use App\Models\User;

class CompetitionParticipantPolicy
{
    public function view(User $user, CompetitionParticipant $participant): bool
    {
        if ($user->hasPermissionTo('view competition participants')) {
            return true;
        }

        $examiner = $user->examiner;
        if (!$examiner) {
            return false;
        }

        return $participant->competitionLevel
            ->competitionExaminerLevels()
            ->whereHas('competitionExaminer', fn($q) => $q->where('examiner_id', $examiner->id))
            ->exists();
    }

    /**
     * السماح ببدء/متابعة الاختبار فقط: بشرط عدم اكتمال النتيجة.
     */
    public function examine(User $user, CompetitionParticipant $participant): bool
    {
        if (!$this->view($user, $participant)) {
            return false;
        }

        if ($user->hasPermissionTo('examine competition participants') && $participant->competitionResult()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * السماح بمراجعة/عرض النتيجة، حتى لو كانت معتمدة بالفعل.
     */
    public function review(User $user, CompetitionParticipant $participant): bool
    {
        return $this->view($user, $participant);
    }
}
