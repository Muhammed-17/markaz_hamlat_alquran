<?php

namespace App\Http\Controllers;

use App\Exports\CompetitionParticipantsExport;
use App\Http\Requests\Competition\StoreCompetitionParticipantRequest;
use App\Http\Requests\Competition\UpdateCompetitionParticipantRequest;
use App\Models\Center;
use App\Models\Circle;
use App\Models\Competition;
use App\Models\CompetitionParticipant;
use App\Models\ExternalParticipant;
use App\Models\Student;
use App\Models\TafsirFile;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CompetitionParticipantController extends Controller
{
    /**
     * Display competition participants.
     */
    public function index(Competition $competition, Request $request)
    {
        $this->authorize('update', $competition);

        $participants = CompetitionParticipant::query()
            ->where('competition_id', $competition->id)
            ->with([
                'competitionLevel.level',
                'student',
                'externalParticipant',
                'supervisor',
                'center',
                'circle',
                'tafsirFile',
            ])
            ->when($request->filled('level_id'), function ($query) use ($request) {
                $query->where('competition_level_id', $request->level_id);
            })
            ->when($request->filled('center_id'), function ($query) use ($request) {
                $query->where('center_id', $request->center_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->whereHas(
                        'student',
                        fn($q2) => $q2->where('name', 'like', "%{$search}%")
                    )
                        ->orWhereHas(
                            'externalParticipant',
                            fn($q2) => $q2->where('name', 'like', "%{$search}%")
                        );
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $levels = $competition->competitionLevels()
            ->with('level')
            ->get();

        $centers = Center::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view(
            'competitions.participants.participants',
            compact(
                'competition',
                'participants',
                'levels',
                'centers'
            )
        );
    }

    /**
     * تصدير قائمة المشاركين إلى ملف Excel، مع تطبيق نفس فلاتر
     * صفحة المشاركين الحالية (البحث، المستوى، المركز).
     */
    public function exportExcel(Competition $competition, Request $request)
    {
        $this->authorize('update', $competition);

        $fileName = 'participants-' . $competition->id . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new CompetitionParticipantsExport($competition, $request),
            $fileName
        );
    }

    /**
     * Show create participant form.
     */
    public function create(Competition $competition)
    {
        $this->authorize('update', $competition);

        $levels = $competition->competitionLevels()
            ->with('level')
            ->get();

        $circles = Circle::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $supervisors = User::role('supervisor')
            ->orderBy('name')
            ->get(['id', 'name']);

        $centers = Center::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        /*
         * ملفات التفسير المتاحة.
         */
        $tafsirFiles = TafsirFile::query()
            ->orderBy('name')
            ->get(['id', 'name', 'description']);

        return view(
            'competitions.participants.create_participant',
            compact(
                'competition',
                'levels',
                'circles',
                'supervisors',
                'centers',
                'tafsirFiles'
            )
        );
    }

    /**
     * Store a new competition participant.
     */
    public function store(
        StoreCompetitionParticipantRequest $request,
        Competition $competition
    ) {
        $this->authorize('update', $competition);

        $data = $request->validated();

        /*
         * تحديد نوع المشارك.
         */
        $isStudent = $data['participant_type'] === 'student';

        /*
         * ربط المشارك بالمسابقة الحالية.
         */
        $data['competition_id'] = $competition->id;

        /*
         * بيانات الطالب / الخارجي.
         */
        $data['student_id'] = $isStudent
            ? $data['student_id']
            : null;

        $data['external_participant_id'] = !$isStudent
            ? $data['external_participant_id']
            : null;

        /*
         * الحلقة تخص الطالب الداخلي فقط.
         */
        $data['circle_id'] = $isStudent
            ? ($data['circle_id'] ?? null)
            : null;

        /*
         * الرسوم.
         */
        $data['registration_fee'] = $data['registration_fee'] ?? 0;

        /*
         * حالة ملف التفسير.
         */
        $data['file_status'] = $data['file_status'];

        /*
         * ملف التفسير.
         *
         * إذا لم يتم اختيار ملف تفسير يتم تخزين null.
         */
        $data['tafsir_file_id'] = $data['tafsir_file_id'] ?? null;

        CompetitionParticipant::create($data);

        return redirect()
            ->route('competitions.participants', $competition)
            ->with('success', 'تم إضافة المشارك بنجاح.');
    }

    /**
     * Show edit participant form.
     */
    public function edit(
        Competition $competition,
        CompetitionParticipant $participant
    ) {
        $this->authorize('update', $competition);

        $levels = $competition->competitionLevels()
            ->with('level')
            ->get();

        $circles = Circle::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $supervisors = User::role('supervisor')
            ->orderBy('name')
            ->get(['id', 'name']);

        $centers = Center::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        /*
         * ملفات التفسير.
         */
        $tafsirFiles = TafsirFile::query()
            ->orderBy('name')
            ->get(['id', 'name', 'description']);

        /*
         * الطلاب التابعون للحلقة الحالية.
         */
        $studentOptions = [];

        if ($participant->isInternal() && $participant->circle_id) {
            $registeredStudentIds = CompetitionParticipant::query()
                ->where('competition_id', $competition->id)
                ->whereNotNull('student_id')
                ->where('id', '!=', $participant->id)
                ->pluck('student_id')
                ->toArray();

            $studentOptions = Student::query()
                ->where('circle_id', $participant->circle_id)
                ->when(!empty($registeredStudentIds), function ($query) use ($registeredStudentIds) {
                    $query->whereNotIn('id', $registeredStudentIds);
                })
                ->orderBy('name')
                ->get()
                ->map(fn($student) => [
                    'value' => $student->id,
                    'label' => $student->name,
                ])
                ->values()
                ->toArray();
        }

        return view(
            'competitions.participants.edit_participant',
            compact(
                'competition',
                'participant',
                'levels',
                'circles',
                'supervisors',
                'centers',
                'tafsirFiles',
                'studentOptions'
            )
        );
    }

    /**
     * Update competition participant.
     */
    public function update(
        UpdateCompetitionParticipantRequest $request,
        Competition $competition,
        CompetitionParticipant $participant
    ) {
        $this->authorize('update', $competition);

        $data = $request->validated();

        $isStudent = $data['participant_type'] === 'student';

        /*
         * إذا تغيّر المستوى، فإن كل الإجابات والنتيجة المسجّلة سابقًا
         * أصبحت غير مرتبطة بأسئلة المستوى الجديد (لأن الأسئلة والمختبرين
         * مرتبطون بمستوى معيّن)، فيجب حذفها لمنع بقاء بيانات تقييم
         * منسوبة لأسئلة مستوى مختلف عن المستوى الحالي للمشارك.
         */
        if ((int) $data['competition_level_id'] !== (int) $participant->competition_level_id) {
            $participant->competitionAnswers()->delete();
            $participant->competitionResult()->delete();
        }

        /*
         * بيانات الطالب / الخارجي.
         */
        $data['student_id'] = $isStudent
            ? $data['student_id']
            : null;

        $data['external_participant_id'] = !$isStudent
            ? $data['external_participant_id']
            : null;

        /*
         * الحلقة للطالب الداخلي فقط.
         */
        $data['circle_id'] = $isStudent
            ? ($data['circle_id'] ?? null)
            : null;

        /*
         * الرسوم.
         */
        $data['registration_fee'] = $data['registration_fee'] ?? 0;

        /*
         * حالة ملف التفسير.
         */
        $data['file_status'] = $data['file_status'] ?? 0;

        /*
         * ملف التفسير.
         */
        $data['tafsir_file_id'] = $data['tafsir_file_id'] ?? null;

        $participant->update($data);

        return redirect()
            ->route('competitions.participants', $competition)
            ->with('success', 'تم تحديث بيانات المشارك بنجاح.');
    }

    /**
     * Delete competition participant.
     */
    public function destroy(
        Competition $competition,
        CompetitionParticipant $participant
    ) {
        $this->authorize('update', $competition);

        $participant->delete();

        return redirect()
            ->route('competitions.participants', $competition)
            ->with('success', 'تم حذف المشارك بنجاح.');
    }

    /**
     * Search students by circle.
     */
    public function searchStudents(Request $request)
    {
        $circleId = $request->get('circle_id');

        if (!$circleId) {
            return response()->json([]);
        }

        $competitionId = $request->get('competition_id');
        $excludeParticipantId = $request->get('exclude_participant_id');

        $registeredStudentIds = [];

        if ($competitionId) {
            $registeredStudentIds = CompetitionParticipant::query()
                ->where('competition_id', $competitionId)
                ->whereNotNull('student_id')
                ->when($excludeParticipantId, function ($query) use ($excludeParticipantId) {
                    $query->where('id', '!=', $excludeParticipantId);
                })
                ->pluck('student_id')
                ->toArray();
        }

        $students = Student::query()
            ->where('circle_id', $circleId)
            ->when(!empty($registeredStudentIds), function ($query) use ($registeredStudentIds) {
                $query->whereNotIn('id', $registeredStudentIds);
            })
            ->orderBy('name')
            ->get()
            ->map(fn($student) => [
                'value' => $student->id,
                'label' => $student->name,
            ]);

        return response()->json($students);
    }
    /**
     * Search external participants.
     *
     * لا يتم إنشاء مشارك خارجي جديد من هنا.
     * البحث فقط في جدول external_participants.
     */
    public function searchExternalParticipants(Request $request)
    {
        $search = $request->get('q', '');
        $competitionId = $request->get('competition_id');
        $excludeParticipantId = $request->get('exclude_participant_id');

        $registeredExternalIds = [];

        if ($competitionId) {
            $registeredExternalIds = CompetitionParticipant::query()
                ->where('competition_id', $competitionId)
                ->whereNotNull('external_participant_id')
                ->when($excludeParticipantId, function ($query) use ($excludeParticipantId) {
                    $query->where('id', '!=', $excludeParticipantId);
                })
                ->pluck('external_participant_id')
                ->toArray();
        }

        $participants = ExternalParticipant::query()
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere(
                        'national_id',
                        'like',
                        "%{$search}%"
                    );
            })
            ->when(!empty($registeredExternalIds), function ($query) use ($registeredExternalIds) {
                $query->whereNotIn('id', $registeredExternalIds);
            })
            ->limit(20)
            ->get()
            ->map(fn($participant) => [
                'id' => $participant->id,
                'name' => $participant->name,
                'national_id' => $participant->national_id,
            ]);

        return response()->json($participants);
    }
}
