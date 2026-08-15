<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examiner\UpdateCompetitionAnswerRequest;
use App\Models\CompetitionAnswer;
use App\Models\CompetitionResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class CompetitionAnswerController
 *
 * يتيح للإدارة (مشرف/مدير) تعديل درجة سؤال معيّن،
 * مع إعادة حساب مجموع النتيجة تلقائياً.
 */
class CompetitionAnswerController extends Controller
{
    /**
     * عرض شاشة تعديل سؤال.
     */
    public function edit(CompetitionAnswer $answer): View
    {
        $this->authorize('update', $answer);

        $answer->load(['competitionQuestion', 'competitionExaminer.examiner', 'user']);

        return view('admin.competition-answers.edit', compact('answer'));
    }

    /**
     * حفظ تعديل السؤال، ثم إعادة حساب مجموع النتيجة.
     */
    public function update(UpdateCompetitionAnswerRequest $request, CompetitionAnswer $answer): RedirectResponse
    {
        $answered = (bool) $request->answered;

        DB::transaction(function () use ($request, $answer, $answered) {
            $answer->update([
                'user_id'                 => auth()->id(),
                'competition_examiner_id' => $answer->competition_examiner_id,
                'answered'                => $answered,
                'score'                   => $answered ? $request->score : 0,
                'memorization_mistakes'   => $answered ? $request->memorization_mistakes : null,
                'tashkeel_mistakes'       => $answered ? $request->tashkeel_mistakes : null,
                'notes'                   => $request->notes,
            ]);

            $this->recalculateResultTotal($answer->competition_participant_id);
        });

        return redirect()
            ->route('admin.competition-results.show', $answer->competition_participant_id)
            ->with('success', 'تم تحديث درجة السؤال وإعادة حساب المجموع بنجاح.');
    }

    /**
     * إعادة حساب مجموع النتيجة لمشارك معيّن (إن وُجدت نتيجة معتمدة له).
     */
    protected function recalculateResultTotal(int $participantId): void
    {
        $result = CompetitionResult::where('competition_participant_id', $participantId)->first();

        if (!$result) {
            return;
        }

        $totalScore = CompetitionAnswer::where('competition_participant_id', $participantId)->sum('score');

        $result->update(['total_score' => $totalScore]);
    }
}
