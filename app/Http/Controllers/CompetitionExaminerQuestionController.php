<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionExaminer;
use App\Models\CompetitionQuestion;
use Illuminate\Http\Request;

class CompetitionExaminerQuestionController extends Controller
{
    /**
     * عرض صفحة اختيار الأسئلة لمختبر معين ضمن المستويات المسندة له.
     */
    public function index(Competition $competition, CompetitionExaminer $competitionExaminer, Request $request)
    {
        $this->authorize('update', $competition);

        $levels = $competitionExaminer->competitionExaminerLevels()->with('competitionLevel.level')->get();
        $activeLevelId = $request->get('level_id');

        $questions = collect();

        if ($activeLevelId) {
            $questions = CompetitionQuestion::query()
                ->where('competition_level_id', $activeLevelId)
                ->with('competitionExaminers.examiner.user')   // ← هذا السطر
                ->latest()
                ->get();
        }

        return view('competitions.examiners.examiner_questions', [
            'competition'         => $competition,
            'competitionExaminer' => $competitionExaminer,
            'levels'              => $levels,
            'questions'           => $questions,
            'activeLevelId'       => $activeLevelId,
        ]);
    }

    /**
     * تبني/إلغاء تبني سؤال من قِبل هذا المختبر.
     */
    public function toggleClaim(Competition $competition, CompetitionExaminer $competitionExaminer, CompetitionQuestion $question)
    {
        $this->authorize('update', $competition);

        // التأكد إن السؤال تابع لمستوى مسند فعلاً لهذا المختبر
        $isAssignedLevel = $competitionExaminer->competitionExaminerLevels()
            ->where('competition_level_id', $question->competition_level_id)
            ->exists();

        abort_unless($isAssignedLevel, 403, 'هذا المستوى غير مسند لهذا المختبر.');

        if ($question->isClaimedBy($competitionExaminer->id)) {
            // إلغاء التبني لهذا المختبر فقط (لا يمس تبنّي مختبرين آخرين)
            $question->competitionExaminers()->detach($competitionExaminer->id);
        } else {
            // تبني السؤال (بالإضافة لأي مختبرين آخرين متبنّينه)
            $question->competitionExaminers()->syncWithoutDetaching($competitionExaminer->id);
        }
        return redirect()
            ->route('competitions.questions', [$competition, $competitionExaminer, 'level_id' => $question->competition_level_id])
            ->with('success', 'تم تحديث حالة السؤال.');
    }
}
