<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCompetitionResultRequest;
use App\Models\Competition;
use App\Models\CompetitionAnswer;
use App\Models\CompetitionLevel;
use App\Models\CompetitionResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class CompetitionResultController
 *
 * إدارة عرض وتعديل نتائج المشاركين.
 */
class CompetitionResultController extends Controller
{
    /**
     * قائمة جميع النتائج مع فلاتر.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CompetitionResult::class);

        $results = CompetitionResult::query()
            ->with([
                'competitionParticipant.student',
                'competitionParticipant.externalParticipant',
                'competitionParticipant.competition',
                'competitionParticipant.competitionLevel.level',
            ])
            ->when($request->filled('competition_id'), function ($query) use ($request) {
                $query->whereHas('competitionParticipant', fn ($q) => $q->where('competition_id', $request->competition_id));
            })
            ->when($request->filled('competition_level_id'), function ($query) use ($request) {
                $query->whereHas('competitionParticipant', fn ($q) => $q->where('competition_level_id', $request->competition_level_id));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->q;
                $query->whereHas('competitionParticipant', function ($p) use ($search) {
                    $p->whereHas('student', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('externalParticipant', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('total_score')
            ->paginate(15)
            ->withQueryString();

        $competitions = Competition::orderBy('name')->get();
        $levels = CompetitionLevel::with('level')->get();

        return view('admin.competition_results.index', compact('results', 'competitions', 'levels'));
    }

    /**
     * عرض تفاصيل نتيجة مشارك (عبر معرف المشارك).
     */
    public function show(int $participantId): View
    {
        $result = CompetitionResult::where('competition_participant_id', $participantId)->firstOrFail();

        $this->authorize('view', $result);

        $result->load([
            'competitionParticipant.student',
            'competitionParticipant.externalParticipant',
            'competitionParticipant.competition',
            'competitionParticipant.competitionLevel.level',
        ]);

        $answers = CompetitionAnswer::where('competition_participant_id', $participantId)
            ->with('competitionQuestion')
            ->get();

        $totalQuestions = $answers->count();
        $answeredCount = $answers->where('answered', true)->count();
        $unansweredCount = $answers->where('answered', false)->count();

        return view('admin.competition_results.show', compact(
            'result', 'answers', 'totalQuestions', 'answeredCount', 'unansweredCount'
        ));
    }

    /**
     * شاشة تعديل النتيجة (إعادة حساب المجموع / الترتيب).
     */
    public function edit(int $participantId): View
    {
        $result = CompetitionResult::where('competition_participant_id', $participantId)->firstOrFail();

        $this->authorize('update', $result);

        $result->load('competitionParticipant.competitionLevel.level');

        $answers = CompetitionAnswer::where('competition_participant_id', $participantId)->get();

        $totalQuestions = $answers->count();
        $answeredCount = $answers->where('answered', true)->count();
        $unansweredCount = $answers->where('answered', false)->count();

        return view('admin.competition_results.edit', compact(
            'result', 'totalQuestions', 'answeredCount', 'unansweredCount'
        ));
    }

    /**
     * تنفيذ إعادة الحساب أو الحفظ النهائي للنتيجة.
     */
    public function update(UpdateCompetitionResultRequest $request, int $participantId): RedirectResponse
    {
        $result = CompetitionResult::where('competition_participant_id', $participantId)->firstOrFail();

        $totalScore = CompetitionAnswer::where('competition_participant_id', $participantId)->sum('score');

        if ($request->action === 'recalculate') {
            $result->update(['total_score' => $totalScore]);

            return redirect()
                ->route('admin.competition-results.edit', $participantId)
                ->with('success', 'تم إعادة حساب المجموع: ' . $totalScore);
        }

        $result->update(['total_score' => $totalScore]);

        $this->recalculateRanksForLevel($result->competitionParticipant->competition_level_id);

        return redirect()
            ->route('admin.competition-results.index')
            ->with('success', 'تم حفظ النتيجة وإعادة حساب الترتيب بنجاح.');
    }

    /**
     * إعادة حساب الترتيب لكل مشاركي مستوى معيّن (الأعلى درجة = رقم 1).
     * التعادل: نفس الترتيب لكليهما (Dense Rank).
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
