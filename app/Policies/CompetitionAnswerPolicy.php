<?php

namespace App\Policies;

use App\Models\CompetitionAnswer;
use App\Models\User;

/**
 * Class CompetitionAnswerPolicy
 *
 * يتحكم في تعديل درجات الأسئلة.
 */
class CompetitionAnswerPolicy
{
    /**
     * المختبر لا يستطيع تعديل نتائج مختبر آخر.
     * المشرف والمدير يستطيعان تعديل كل الإجابات.
     * لا يسمح بالتعديل بعد اعتماد النتيجة النهائية إلا للمشرف/المدير.
     */
    public function update(User $user, CompetitionAnswer $answer): bool
    {
        if ($user->hasAnyRole(['admin', 'general_manager', 'manager', 'supervisor'])) {
            return true;
        }

        if (!$user->hasRole('examiner')) {
            return false;
        }

        $examiner = $user->examiner;
        if (!$examiner || $answer->competition_examiner_id !== $examiner->id) {
            return false;
        }

        if ($answer->competitionParticipant->competitionResult()->exists()) {
            return false;
        }

        return true;
    }
}
