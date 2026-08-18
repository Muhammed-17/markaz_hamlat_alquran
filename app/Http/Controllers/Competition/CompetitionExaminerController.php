<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionExaminer;
use App\Models\CompetitionQuestion;
use App\Models\Examiner;
use App\Http\Requests\Competition\Storecompetitionexaminerrequest;
use App\Http\Requests\Competition\Updatecompetitionexaminerrequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompetitionExaminerController extends Controller
{
    public function index(Competition $competition)
    {
        $this->authorize('update', $competition);

        $examiners = $competition->competitionExaminers()
            ->with(['examiner.user', 'competitionExaminerLevels.competitionLevel.level'])
            ->get();

        return view('competitions.examiners.examiners', compact('competition', 'examiners'));
    }

    public function create(Competition $competition)
    {
        $this->authorize('update', $competition);

        $levels = $competition->competitionLevels()->with('level')->get();

        $existingExaminerIds = $competition->competitionExaminers()->pluck('examiner_id');

        $examiners = Examiner::query()
            ->with('user')
            ->whereNotIn('id', $existingExaminerIds)
            ->get();

        // تحويل المختبرين إلى مصفوفة خيارات متوافقة مع المكون
        $examinerOptions = $examiners->map(fn($examiner) => [
            'value' => (string) $examiner->id,
            'label' => ($examiner->user?->name ?? 'بدون اسم') . ($examiner->phone ? ' - ' . $examiner->phone : ''),
        ])->values()->toArray();

        return view('competitions.examiners.examiner_levels', [
            'competition'         => $competition,
            'competitionExaminer' => null,
            'levels'              => $levels,
            'examiners'           => $examiners,
            'examinerOptions'     => $examinerOptions,
            'selectedExaminer'    => null,
            'selectedLevelIds'    => [],
        ]);
    }

    public function edit(Competition $competition, CompetitionExaminer $competitionExaminer)
    {
        $this->authorize('update', $competition);


        $levels = $competition->competitionLevels()->with('level')->get();
        $selectedLevelIds = $competitionExaminer->competitionExaminerLevels()->pluck('competition_level_id')->toArray();

        return view('competitions.examiners.examiner_levels', [
            'competition'         => $competition,
            'competitionExaminer' => $competitionExaminer,
            'levels'              => $levels,
            'selectedExaminer'    => $competitionExaminer->examiner,
            'selectedLevelIds'    => $selectedLevelIds,
        ]);
    }

    public function store(Competition $competition, Storecompetitionexaminerrequest  $request)
    {
        $this->authorize('update', $competition);
        $data = $request->validated();

        DB::transaction(function () use ($competition, $data) {
            $competitionExaminer = $competition->competitionExaminers()
                ->firstOrCreate(['examiner_id' => $data['examiner_id']]);

            $this->syncExaminerLevels($competitionExaminer, $data['competition_level_ids'] ?? []);
        });

        return redirect()
            ->route('competitions.examiners', $competition)
            ->with('success', 'تم إضافة المختبر بنجاح.');
    }

    public function update(Competition $competition, CompetitionExaminer $competitionExaminer, Updatecompetitionexaminerrequest  $request)
    {
        $this->authorize('update', $competition);
        $data = $request->validated();
        $this->syncExaminerLevels($competitionExaminer, $data['competition_level_ids'] ?? []);

        return redirect()
            ->route('competitions.examiners', $competition)
            ->with('success', 'تم تحديث مستويات المختبر بنجاح.');
    }

    public function destroy(Competition $competition, CompetitionExaminer $competitionExaminer)
    {
        $this->authorize('update', $competition);

        $competitionExaminer->competitionExaminerLevels()->delete();
        $competitionExaminer->delete();

        return redirect()
            ->route('competitions.examiners', $competition)
            ->with('success', 'تم حذف المختبر من المسابقة بنجاح.');
    }

    public function searchExaminers(Request $request, Competition $competition)
    {
        $search = trim($request->get('q', ''));

        $existingExaminerIds = $competition->competitionExaminers()->pluck('examiner_id');

        $examiners = Examiner::query()
            ->with('user')
            ->whereNotIn('id', $existingExaminerIds)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->limit(20)
            ->get()
            ->map(fn($examiner) => [
                'id'   => $examiner->id,
                'text' => ($examiner->user?->name ?? 'بدون اسم') . ($examiner->phone ? ' - ' . $examiner->phone : ''),
            ]);

        return response()->json($examiners);
    }

    /**
     * Manually sync competition_level_ids for a CompetitionExaminer,
     * since competitionExaminerLevels() is a HasMany relation (no sync()).
     */
    private function syncExaminerLevels(CompetitionExaminer $competitionExaminer, array $levelIds): void
    {
        $levelIds = array_map('intval', $levelIds);

        $existingIds = $competitionExaminer->competitionExaminerLevels()
            ->pluck('competition_level_id')
            ->toArray();

        $toRemove = array_diff($existingIds, $levelIds);
        $toAdd    = array_diff($levelIds, $existingIds);

        if (!empty($toRemove)) {
            // إلغاء تبني الأسئلة الخاصة بهذا المختبر ضمن المستويات التي تمت إزالتها
            CompetitionQuestion::where('competition_examiner_id', $competitionExaminer->id)
                ->whereIn('competition_level_id', $toRemove)
                ->update(['competition_examiner_id' => null]);

            $competitionExaminer->competitionExaminerLevels()
                ->whereIn('competition_level_id', $toRemove)
                ->delete();
        }

        foreach ($toAdd as $levelId) {
            $competitionExaminer->competitionExaminerLevels()->create([
                'competition_level_id' => $levelId,
            ]);
        }
    }
}
