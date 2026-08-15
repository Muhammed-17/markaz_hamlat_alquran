<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionAnswer;
use App\Models\CompetitionLevel;
use App\Models\CompetitionParticipant;
use App\Models\CompetitionQuestion;
use App\Models\Scopes\CenterScope;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class CompetitionAdminController
 *
 * واجهة الأدمن لعرض كل المسابقات والمستويات والمشاركين والنتائج،
 * بدون قيود الإسناد الخاصة بالمختبر (examiner). للعرض فقط —
 * لا يوجد بدء/متابعة اختبار من هذه الواجهة.
 */
class CompetitionAdminController extends Controller
{
    /**
     * قائمة كل المسابقات (بدون فلترة على مختبر معيّن).
     */
    public function competitions(): View
    {
        $competitions = Competition::query()
            ->withCount(['competitionLevels'])
            ->latest()
            ->paginate(12);

        return view('admin.competition_results.index', compact('competitions'));
    }

    /**
     * قائمة كل مستويات مسابقة معيّنة، مع إحصائيات المشاركين والمكتمل.
     */
    public function levels(Competition $competition): View
    {
        $levels = $competition->competitionLevels()
            ->with('level')
            ->withCount(['competitionParticipants as participantsCount'])
            ->get()
            ->map(function (CompetitionLevel $competitionLevel) {
                $competitionLevel->completedCount = $competitionLevel->competitionParticipants()
                    ->whereHas('competitionResult')
                    ->count();

                return $competitionLevel;
            });

        return view('admin.competitions.levels', compact('competition', 'levels'));
    }

    /**
     * قائمة المشاركين ضمن مستوى معيّن — عرض فقط، بدون إجراءات اختبار.
     */
    public function participants(Request $request, CompetitionLevel $competitionLevel): View
    {
        $participants = $competitionLevel->competitionParticipants()
            ->with([
                'student' => fn($query) => $query->withoutGlobalScope(CenterScope::class),
                'externalParticipant',
                'competitionResult',
            ])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->q;
                $query->where(function ($inner) use ($search) {
                    $inner->whereHas('student', fn($q) => $q->withoutGlobalScope(CenterScope::class)->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('externalParticipant', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->paginate(15)
            ->withQueryString();

        $participants->getCollection()->transform(function ($participant) {
            $answeredCount = $participant->competitionAnswers()->count();

            if ($participant->competitionResult) {
                $participant->exam_status = 'completed';
            } elseif ($answeredCount === 0) {
                $participant->exam_status = 'registered';
            } else {
                $participant->exam_status = 'testing';
            }

            return $participant;
        });

        return view('admin.competitions.participants', compact('competitionLevel', 'participants'));
    }

    /**
     * عرض تفاصيل نتيجة مشارك — إجاباته وتقييمات كل الأسئلة، بدون شرط اكتمال النتيجة.
     */
    public function result(CompetitionParticipant $participant): View
    {
        $totalQuestions = CompetitionQuestion::query()
            ->where('competition_level_id', $participant->competition_level_id)
            ->count();

        $answers = CompetitionAnswer::query()
            ->where('competition_participant_id', $participant->id)
            ->with(['competitionQuestion', 'competitionExaminer.examiner'])
            ->get();

        $answeredCount = $answers->where('answered', true)->count();
        $unansweredCount = $answers->where('answered', false)->count();

        return view('admin.competitions.result', compact(
            'participant',
            'totalQuestions',
            'answeredCount',
            'unansweredCount',
            'answers'
        ));
    }
}
