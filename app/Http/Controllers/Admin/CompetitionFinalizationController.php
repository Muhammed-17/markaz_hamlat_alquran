<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FinalizeCompetitionRequest;
use App\Models\Competition;
use App\Models\CompetitionLevel;
use App\Models\CompetitionResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Class CompetitionFinalizationController
 *
 * شاشة إغلاق المسابقة على مستوى معين: إعادة حساب الترتيب،
 * اعتماد النتائج، وإغلاق المسابقة نهائياً.
 */
class CompetitionFinalizationController extends Controller
{
    /**
     * عرض شاشة إنهاء مسابقة/مستوى معيّن.
     */
    public function show(Competition $competition, CompetitionLevel $competitionLevel): View
    {
        $this->authorize('finalize', CompetitionResult::class);

        $participants = $competitionLevel->competitionParticipants()
            ->with(['student', 'externalParticipant', 'competitionResult'])
            ->get();

        $completedCount = $participants->filter(fn ($p) => $p->competitionResult !== null)->count();
        $incompleteCount = $participants->count() - $completedCount;

        $results = CompetitionResult::query()
            ->whereHas('competitionParticipant', fn ($q) => $q->where('competition_level_id', $competitionLevel->id))
            ->with(['competitionParticipant.student', 'competitionParticipant.externalParticipant'])
            ->orderByDesc('total_score')
            ->get();

        return view('admin.competition-finalization.show', compact(
            'competition', 'competitionLevel', 'participants', 'completedCount', 'incompleteCount', 'results'
        ));
    }

    /**
     * تنفيذ الإجراء المطلوب: إعادة حساب الترتيب / اعتماد النتائج / إغلاق المسابقة.
     */
    public function update(FinalizeCompetitionRequest $request, Competition $competition, CompetitionLevel $competitionLevel): RedirectResponse
    {
        switch ($request->action) {
            case 'recalculate_rank':
                $this->recalculateRanksForLevel($competitionLevel->id);
                $message = 'تم إعادة حساب الترتيب بنجاح.';
                break;

            case 'finalize':
                $message = 'تم اعتماد نتائج هذا المستوى بنجاح.';
                break;

            case 'close':
                $competition->update(['status' => 'closed']);
                $message = 'تم إغلاق المسابقة بنجاح.';
                break;
        }

        return redirect()
            ->route('admin.competitions.finalization.show', [$competition->id, $competitionLevel->id])
            ->with('success', $message);
    }

    /**
     * إعادة حساب الترتيب لكل مشاركي المستوى.
     */
    protected function recalculateRanksForLevel(int $competitionLevelId): void
    {
        $results = CompetitionResult::query()
            ->whereHas('competitionParticipant', fn ($q) => $q->where('competition_level_id', $competitionLevelId))
            ->orderByDesc('total_score')
            ->get();

        $rank = 0;
        $previousScore = null;

        foreach ($results as $result) {
            if ($previousScore === null || (float) $result->total_score !== (float) $previousScore) {
                $rank++;
            }

            $result->update(['rank' => $rank]);
            $previousScore = $result->total_score;
        }
    }
}
