<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\EditStudentRequest;
use App\Http\Requests\Student\StoreStudentRegistrationRequest;
use App\Models\Student;
use App\Models\Circle;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Center;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\ResolvesUserScope;

class StudentController extends Controller
{
    use ResolvesUserScope;

    // ─────────────────────────────────────────
    private array $constructionFields = [
        'current_surah', 'study_system', 'group_name',
        'new_memorization_plan', 'placement_evaluation',
        'old_memorization_plan', 'old_memorization_plan_other',
    ];

    private array $itqanFields = [
        'previous_memorization_side', 'previous_khatamat_count',
        'current_review_amount', 'self_evaluation', 'tajweed_matn',
        'tajweed_matn_other', 'desired_path', 'preferred_time',
        'teacher_name', 'itqan_details',
    ];

    private array $ibdaFields = [
        'previous_licenses_and_chains', 'desired_narration_and_path',
        'preferred_time', 'supervisor_name', 'ibda_details',
    ];

    private array $studentColumns = [
        'name', 'date_of_birth', 'gender', 'second_phone', 'address',
        'guardian_id', 'status', 'suspended_at', 'circle_id', 'student_code',
        'education_type', 'educational_stage', 'school_grade', 'previous_school',
        'center_entry_level', 'join_date', 'whatsapp_number', 'health_status',
        'notes', 'supervisor_id', 'applicant', 'applicant_other', 'center_id',
        'whatsapp_owner', 'whatsapp_owner_other', 'additional_contact_owner',
        'additional_contact_owner_other', 'learning_difficulties', 'personal_traits',
        'hobbies', 'reading', 'exit_details', 'student_exit_status', 'decision',
        'health_status_other', 'learning_difficulties_other', 'personal_traits_other',
        'hobby_other', 'subscription_fees', 'received_tools',
    ];

    // ─────────────────────────────────────────
    public function index()
    {
        $this->authorize('viewAny', Student::class);

        $user  = Auth::user();
        $query = Student::with(['circle', 'center']);

        if ($user->hasRole('guardian')) {
            $query->where('guardian_id', $user->id);
        }

        $students = $query->orderBy('name')->get();
        $circles  = $this->getAccessibleCircles($user);   // ✅ من الـ Trait
        $centers  = $this->getAccessibleCenters($user);   // ✅ من الـ Trait

        return view('students.index', compact('students', 'circles', 'centers'));
    }

    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Student::class);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);        // ✅ من الـ Trait

        // الحلقات والمعلمين والمشرفين مقيدين بالفرع
        $circles   = $this->getAccessibleCircles($user);
        $centers   = $this->getAccessibleCenters($user);
        $teachers  = $this->getAccessibleTeachers($teacher);
        $supervisors = $this->getAccessibleSupervisors($teacher);

        $guardians          = User::role('guardian')->get();
        $subscriptionPrices = DB::table('subscription_prices')->get();
        $generatedCode      = $this->generateStudentCode();

        return view('students.create', compact(
            'circles', 'guardians', 'subscriptionPrices',
            'teachers', 'supervisors', 'generatedCode', 'centers'
        ));
    }

    // ─────────────────────────────────────────
    public function store(StoreStudentRegistrationRequest $request)
    {
        $this->authorize('create', Student::class);

        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($data['guardian_id'] === 'new') {
                $guardian = User::create([
                    'name'     => $data['applicant'] ?? $data['name'],
                    'email'    => $data['parent_email'],
                    'mobile'   => $data['whatsapp_number'] ?? '',
                    'password' => Hash::make($data['password']),
                    'status'   => 'active',
                ]);
                $guardian->assignRole('guardian');
                $data['guardian_id'] = $guardian->id;
            } else {
                $data['guardian_id'] = (int) $data['guardian_id'];
            }

            $studentData = array_intersect_key($data, array_flip($this->studentColumns));

            if (($studentData['status'] ?? '') === 'inactive') {
                $studentData['suspended_at'] = now();
            }

            $student = Student::create($studentData);
            $this->syncDetailRecord($student, $data, 'create');

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'تم تسجيل الطالب بنجاح',
                    'redirect' => route('students.index'),
                    'student'  => $student->load(['constructionDetail', 'itqanDetail', 'ibdaDetail']),
                ]);
            }

            return redirect()->route('students.index')->with('success', 'تم تسجيل الطالب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'حدث خطأ أثناء تسجيل الطالب: ' . $e->getMessage();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    // ─────────────────────────────────────────
    public function show($id)
    {
        $student = Student::findOrFail($id);
        $this->authorize('view', $student);

        $student->load([
            'circle.mainTeacher', 'guardian', 'attendances',
            'subscriptions', 'constructionDetail', 'itqanDetail',
            'ibdaDetail', 'supervisor.user',
        ]);

        $totalAttendance = $student->attendances->count();
        $presentCount    = $student->attendances->where('status', 'present')->count();
        $lateCount       = $student->attendances->where('status', 'late')->count();
        $absentCount     = $student->attendances->where('status', 'absent')->count();
        $excusedCount    = $student->attendances->where('status', 'excused')->count();
        $attendanceRate  = $totalAttendance > 0
            ? round((($presentCount + $lateCount) / $totalAttendance) * 100)
            : 0;

        $startDate = $student->join_date
            ? $student->join_date->copy()->startOfMonth()
            : $student->created_at->copy()->startOfMonth();

        $endDate = $student->status === 'inactive' && $student->suspended_at
            ? $student->suspended_at->copy()->startOfMonth()
            : now()->startOfMonth();

        $subscriptionIndex = $student->subscriptions->keyBy(fn($s) => $s->month->format('Y-m'));
        $feeTimeline       = collect();
        $checkDate         = $startDate->copy();

        while ($checkDate->lte($endDate)) {
            $sub = $subscriptionIndex->get($checkDate->format('Y-m'));
            $feeTimeline->push((object) [
                'month'        => $checkDate->copy(),
                'subscription' => $sub,
                'is_paid'      => $sub && $sub->status === 'مدفوع',
            ]);
            $checkDate->addMonth();
        }

        return view('students.show', [
            'student'           => $student,
            'attendanceRate'    => $attendanceRate,
            'presentCount'      => $presentCount,
            'lateCount'         => $lateCount,
            'absentCount'       => $absentCount,
            'excusedCount'      => $excusedCount,
            'unpaidMonthsCount' => $student->overdue_months_count,
            'paidMonthsCount'   => $student->subscriptions->where('status', 'مدفوع')->count(),
            'totalPaidAmount'   => $student->subscriptions->where('status', 'مدفوع')->sum('amount'),
            'feeTimeline'       => $feeTimeline->sortByDesc('month'),
            'suspendedPastDebt' => $student->suspended_past_debt,
        ]);
    }

    // ─────────────────────────────────────────
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $this->authorize('update', $student);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);        // ✅ من الـ Trait

        $student->load(['guardian', 'constructionDetail', 'itqanDetail', 'ibdaDetail', 'circle']);

        return view('students.edit', [
            'student'            => $student,
            'circles'            => $this->getAccessibleCircles($user),    // ✅
            'centers'            => $this->getAccessibleCenters($user),    // ✅
            'teachers'           => $this->getAccessibleTeachers($teacher), // ✅
            'supervisors'        => $this->getAccessibleSupervisors($teacher), // ✅
            'guardians'          => User::role('guardian')->get(),
            'subscriptionPrices' => DB::table('subscription_prices')->get(),
            'construction'       => $student->constructionDetail,
            'itqan'              => $student->itqanDetail,
            'ibda'               => $student->ibdaDetail,
        ]);
    }

    // ─────────────────────────────────────────
    public function update(EditStudentRequest $request, $id)
    {
        $student = Student::findOrFail($id);
        $this->authorize('update', $student);

        DB::beginTransaction();
        try {
            $studentData = array_intersect_key(
                $request->validated(),
                array_flip($this->studentColumns)
            );

            if (isset($studentData['status'])) {
                if ($studentData['status'] === 'inactive' && $student->status !== 'inactive') {
                    $studentData['suspended_at'] = now();
                } elseif ($studentData['status'] !== 'inactive') {
                    $studentData['suspended_at'] = null;
                }
            }

            $student->update($studentData);
            $this->syncDetailRecord($student, $request->validated(), 'update');

            DB::commit();
            return redirect()->route('students.index')->with('success', 'تم تحديث بيانات الطالب بنجاح ✓');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $this->authorize('delete', $student);
        $student->delete();

        return redirect()->route('students.index')->with('success', 'تم حذف الطالب بنجاح');
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────
    private function generateStudentCode(): string
    {
        $prefix = 'STU-' . now()->format('Y') . '-';
        $last   = Student::where('student_code', 'like', $prefix . '%')
            ->orderBy('student_code', 'desc')
            ->value('student_code');
        $next = $last ? (int) substr($last, -5) + 1 : 1;
        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    private function syncDetailRecord(Student $student, array $data, string $mode): void
    {
        $entryLevel = $data['center_entry_level'] ?? $student->center_entry_level;

        $map = [
            'construction' => ['relation' => 'constructionDetail', 'fields' => $this->constructionFields, 'others' => ['itqanDetail', 'ibdaDetail']],
            'mastery'      => ['relation' => 'itqanDetail',        'fields' => $this->itqanFields,        'others' => ['constructionDetail', 'ibdaDetail']],
            'creativity'   => ['relation' => 'ibdaDetail',         'fields' => $this->ibdaFields,         'others' => ['constructionDetail', 'itqanDetail']],
        ];

        if (!isset($map[$entryLevel])) return;

        $config   = $map[$entryLevel];
        $relation = $config['relation'];
        $fields   = array_intersect_key($data, array_flip($config['fields']));

        if ($mode === 'create') {
            $student->$relation()->create($fields);
        } else {
            $student->$relation()->updateOrCreate(['student_id' => $student->id], $fields);
            foreach ($config['others'] as $other) {
                $student->$other()->delete();
            }
        }
    }
}