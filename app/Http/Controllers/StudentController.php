<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\EditStudentRequest;
use App\Http\Requests\Student\StoreStudentRegistrationRequest;
use App\Models\Student;
use App\Models\Circle;
use App\Models\Center;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Scopes\CenterScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Traits\ResolvesUserScope;

class StudentController extends Controller
{
    use ResolvesUserScope;

    private array $constructionFields = [
        'current_surah',
        'study_system',
        'group_name',
        'new_memorization_plan',
        'placement_evaluation',
        'old_memorization_plan',
        'old_memorization_plan_other',
    ];

    private array $itqanFields = [
        'previous_memorization_side',
        'previous_khatamat_count',
        'current_review_amount',
        'self_evaluation',
        'tajweed_matn',
        'tajweed_matn_other',
        'desired_path',
        'preferred_time',
        'teacher_name',
        'itqan_details',
    ];

    private array $ibdaFields = [
        'previous_licenses_and_chains',
        'desired_narration_and_path',
        'preferred_time',
        'supervisor_name',
        'ibda_details',
    ];

    private array $studentColumns = [
        'name',
        'date_of_birth',
        'gender',
        'second_phone',
        'address',
        'guardian_id',
        'status',
        'suspended_at',
        'circle_id',
        'education_type',
        'educational_stage',
        'school_grade',
        'previous_school',
        'center_entry_level',
        'join_date',
        'whatsapp_number',
        'health_status',
        'notes',
        'supervisor_id',
        'applicant',
        'applicant_other',
        'center_id',
        'whatsapp_owner',
        'whatsapp_owner_other',
        'additional_contact_owner',
        'additional_contact_owner_other',
        'learning_difficulties',
        'personal_traits',
        'hobbies',
        'reading',
        'exit_details',
        'student_exit_status',
        'decision',
        'health_status_other',
        'learning_difficulties_other',
        'personal_traits_other',
        'hobby_other',
        'subscription_fees',
        'received_tools',
    ];

    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        $user  = Auth::user();
        $query = Student::query()
            ->select([
                'id',
                'name',
                'status',
                'decision',
                'circle_id',
                'center_id',
                'educational_stage',
                'date_of_birth',
                'student_code',
                'whatsapp_number',
                'school_grade',
            ])
            ->with('circle:id,name');

        if ($user->hasRole('guardian')) {
            $query->where('guardian_id', $user->id);
        }

        if ($user->hasRole('supervisor') || $user->hasRole('teacher')) {
            $query->where('status', 'مقيد');
        }

        $query
            ->when($request->q, fn($q, $v) => $q->where(
                fn($q) => $q
                    ->where('name', 'like', "%$v%")
                    ->orWhere('student_code', 'like', "%$v%")
                    ->orWhere('whatsapp_number', 'like', "%$v%")
            ))
            ->when($request->status,            fn($q, $v) => $q->where('status', $v))
            ->when($request->circle_id,         fn($q, $v) => $q->where('circle_id', $v))
            ->when($request->center_id,         fn($q, $v) => $q->where('center_id', $v))
            ->when($request->educational_stage, fn($q, $v) => $q->where('educational_stage', $v))
            ->when($request->school_grade,      fn($q, $v) => $q->where('school_grade', $v))
            ->when($request->decision,          fn($q, $v) => $q->where('decision', $v))
            ->when($request->age_min, fn($q, $v) => $q->whereRaw(
                'TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= ?',
                [max(1, min(99, (int) $v))]
            ))
            ->when($request->age_max, fn($q, $v) => $q->whereRaw(
                'TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= ?',
                [max(1, min(99, (int) $v))]
            ));

        $allowedSorts = ['name', 'status', 'educational_stage', 'age', 'circle_name'];
        $sortField    = in_array($request->sort, $allowedSorts) ? $request->sort : 'name';
        $sortDir      = $request->dir === 'desc' ? 'desc' : 'asc';

        if ($sortField === 'age') {
            $query->orderByRaw("TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) $sortDir");
        } elseif ($sortField === 'circle_name') {
            $query->leftJoin('circles', 'students.circle_id', '=', 'circles.id')
                ->orderBy('circles.name', $sortDir)
                ->select('students.*');
        } else {
            $query->orderBy($sortField, $sortDir);
        }

        // ✅ الفرق الجوهري: paginate() مباشرة + withQueryString() بدل JSON response
        $students = $query->paginate(30)->withQueryString();

        $circles = $this->getAccessibleCircles($user);
        $centers = $this->getAccessibleCenters($user);

        return view('students.index', compact('students', 'circles', 'centers'));
    }
    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Student::class);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        return view('students.create', [
            'student'            => new Student(), // ← أضف هذا السطر
            'circles'            => $this->getAccessibleCircles($user),
            'centers'            => $this->getAccessibleCenters($user),
            'teachers'           => $this->getAccessibleTeachers($user, $teacher),
            'supervisors'        => $this->getAccessibleSupervisors($user, $teacher),
            'guardians'          => User::role('guardian')->get(),
            'subscriptionPrices' => DB::table('subscription_prices')->get(),
            'generatedCode'      => $this->generateStudentCode(),
        ]);
    }

    // ─────────────────────────────────────────
    public function store(StoreStudentRegistrationRequest $request)
    {
        $this->authorize('create', Student::class);

        DB::beginTransaction();
        try {
            $data         = $request->validated();
            $existingUser = null;

            $data['guardian_id'] = $this->resolveGuardianId(
                $data['guardian_id'] ?? null,
                $data['guardian_name'] ?? null,
                $data['parent_email'] ?? null,
                $data['password'] ?? null,
                $data['whatsapp_number'] ?? null,
                $existingUser,
            );

            $studentData                 = array_intersect_key($data, array_flip($this->studentColumns));
            $studentData['student_code'] = $this->generateStudentCode();

            if (($studentData['status'] ?? '') === 'متوقف') {
                $studentData['suspended_at'] = now();
            }

            $student = Student::create($studentData);
            $this->syncDetailRecord($student, $data, 'create');

            // ✅ مزامنة حالة ولي الأمر
            $this->syncGuardianStatus($student->fresh()->guardian_id);

            DB::commit();

            $message = $existingUser
                ? 'تم تسجيل الطالب وربطه بحساب ولي الأمر الموجود ✓'
                : 'تم تسجيل الطالب بنجاح ✓';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => $message,
                    'redirect' => route('students.index'),
                    'student'  => $student->load(['constructionDetail', 'itqanDetail', 'ibdaDetail']),
                ]);
            }

            return redirect()->route('students.index')->with('success', $message);
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
        $student = Student::withoutGlobalScope(CenterScope::class)->findOrFail($id);
        $this->authorize('view', $student);
        $this->authorizeStudentCenter($student);

        $student->load([
            'circle.mainTeacher',
            'guardian',
            'attendances',
            'subscriptions',
            'constructionDetail',
            'itqanDetail',
            'ibdaDetail',
            'supervisor.user',
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

        $endDate = $student->status === 'متوقف' && $student->suspended_at
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
        $student = Student::withoutGlobalScope(CenterScope::class)->findOrFail($id);
        $this->authorize('update', $student);
        $this->authorizeStudentCenter($student);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        $student->load(['guardian', 'constructionDetail', 'itqanDetail', 'ibdaDetail', 'circle']);

        return view('students.edit', [
            'student'            => $student,
            'circles'            => $this->getAccessibleCircles($user),
            'centers'            => $this->getAccessibleCenters($user),
            'teachers'           => $this->getAccessibleTeachers($user, $teacher),
            'supervisors'        => $this->getAccessibleSupervisors($user, $teacher),
            'guardians'          => User::role('guardian')->get(),
            'subscriptionPrices' => DB::table('subscription_prices')->get(),
            'generatedCode'      => null,
            'construction'       => $student->constructionDetail,
            'itqan'              => $student->itqanDetail,
            'ibda'               => $student->ibdaDetail,
        ]);
    }

    // ─────────────────────────────────────────
    public function update(EditStudentRequest $request, $id)
    {
        $student = Student::withoutGlobalScope(CenterScope::class)->findOrFail($id);
        $this->authorize('update', $student);
        $this->authorizeStudentCenter($student);

        DB::beginTransaction();
        try {
            $data         = $request->validated();
            $existingUser = null;

            $data['guardian_id'] = $this->resolveGuardianId(
                $data['guardian_id'] ?? null,
                $data['guardian_name'] ?? null,
                $data['parent_email'] ?? null,
                $data['password'] ?? null,
                $data['whatsapp_number'] ?? null,
                $existingUser,
            );

            $studentData = array_intersect_key($data, array_flip($this->studentColumns));
            unset($studentData['student_code']);

            // ✅ صلاحية تغيير الحالة والقرار
            if (isset($studentData['status']) || isset($studentData['decision'])) {
                if (!auth()->user()->can('manage student status')) {
                    unset($studentData['status'], $studentData['suspended_at'], $studentData['decision']);
                }
            }

            // ✅ صلاحية تغيير الحلقة
            if (isset($studentData['circle_id'])) {
                if (!auth()->user()->can('assign student to circle')) {
                    unset($studentData['circle_id']);
                }
            }

            if (isset($studentData['status'])) {
                if ($studentData['status'] === 'متوقف' && $student->status !== 'متوقف') {
                    $studentData['suspended_at'] = now();
                } elseif ($studentData['status'] !== 'متوقف') {
                    $studentData['suspended_at'] = null;
                }
            }

            $student->update($studentData);
            $this->syncDetailRecord($student, $data, 'update');

            // ✅ مزامنة حالة ولي الأمر
            $this->syncGuardianStatus($student->fresh()->guardian_id);

            DB::commit();

            $message = $existingUser
                ? 'تم التحديث وربط ولي الأمر الموجود مسبقاً ✓'
                : 'تم تحديث بيانات الطالب بنجاح ✓';

            return redirect()->route('students.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $student = Student::withoutGlobalScope(CenterScope::class)->findOrFail($id);
        $this->authorize('delete', $student);
        $this->authorizeStudentCenter($student);

        if ($student->subscriptions()->exists() || $student->attendances()->exists()) {
            return redirect()->back()->with(
                'error',
                'لا يمكن حذف الطالب لوجود سجلات حضور أو اشتراكات مرتبطة به'
            );
        }

        // ✅ احفظ guardian_id قبل الحذف
        $guardianId = $student->guardian_id;
        $student->delete();

        // ✅ مزامنة حالة ولي الأمر بعد الحذف
        $this->syncGuardianStatus($guardianId);

        return redirect()->route('students.index')->with('success', 'تم حذف الطالب بنجاح');
    }

    // ─────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────

    private function authorizeStudentCenter(Student $student): void
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) return;
        if ($user->hasRole('guardian')) return;

        $accessibleCenterIds = $this->getAccessibleCenters($user)->pluck('id');

        if ($accessibleCenterIds->isNotEmpty() && !$accessibleCenterIds->contains($student->center_id)) {
            abort(403, 'ليس لديك صلاحية الوصول لبيانات هذا الطالب');
        }
    }

    private function resolveGuardianId(
        mixed   $guardianId,
        ?string $guardianName,
        ?string $parentEmail,
        ?string $password,
        ?string $whatsapp,
        ?User   &$existingUser,
    ): int|null {

        if ($guardianId === 'new') {

            if (!auth()->user()->can('create', Student::class)) {
                abort(403, 'ليس لديك صلاحية إنشاء حساب ولي أمر');
            }

            if (!empty($parentEmail)) {
                $existingUser = User::role('guardian')
                    ->where('email', $parentEmail)
                    ->first();
            }

            if (!$existingUser && !empty($whatsapp)) {
                $existingUser = User::role('guardian')
                    ->where('mobile', $whatsapp)
                    ->first();
            }

            if ($existingUser) {
                return $existingUser->id;
            }

            $mobileExists = !empty($whatsapp) && User::where('mobile', $whatsapp)->exists();
            $emailToUse   = !empty($parentEmail)
                ? $parentEmail
                : 'guardian_' . uniqid() . '@temp.local';

            $guardian = User::create([
                'name'     => $guardianName,
                'email'    => $emailToUse,
                'mobile'   => $mobileExists ? null : ($whatsapp ?: null),
                'password' => Hash::make($password ?? Str::random(16)),
                'status'   => 'active',
                // ⚠️ ضروري بالأخص هنا: emailToUse قد يكون عنواناً مؤقتاً
                // وهمياً (@temp.local) لا يمكن إرسال بريد تفعيل حقيقي إليه
                // أصلاً، فبدون هذا السطر سيبقى الحساب محجوباً عن
                // guardian.dashboard بشكل دائم لا حل له.
                'email_verified_at' => now(),
            ]);
            $guardian->assignRole('guardian');

            return $guardian->id;
        }

        if ($guardianId === 'none' || $guardianId === null) {
            return null;
        }

        $intId    = (int) $guardianId;
        $guardian = User::role('guardian')->find($intId);

        if (!$guardian) {
            abort(422, 'ولي الأمر المحدد غير موجود أو غير مصرح به');
        }

        return $guardian->id;
    }

    private function generateStudentCode(): string
    {
        $prefix = 'MHQ-' . now()->format('Ymd') . '-';

        return DB::transaction(function () use ($prefix) {
            $last = Student::where('student_code', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('student_code', 'desc')
                ->value('student_code');

            $next = $last ? (int) substr($last, -5) + 1 : 1;

            return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
        });
    }

    private function syncDetailRecord(Student $student, array $data, string $mode): void
    {
        $entryLevel = $data['center_entry_level'] ?? $student->center_entry_level;

        $map = [
            'construction' => [
                'relation' => 'constructionDetail',
                'fields'   => $this->constructionFields,
                'others'   => ['itqanDetail', 'ibdaDetail'],
            ],
            'mastery' => [
                'relation' => 'itqanDetail',
                'fields'   => $this->itqanFields,
                'others'   => ['constructionDetail', 'ibdaDetail'],
            ],
            'creativity' => [
                'relation' => 'ibdaDetail',
                'fields'   => $this->ibdaFields,
                'others'   => ['constructionDetail', 'itqanDetail'],
            ],
        ];

        if (!isset($map[$entryLevel])) return;

        $config   = $map[$entryLevel];
        $relation = $config['relation'];
        $fields   = array_intersect_key($data, array_flip($config['fields']));

        if ($entryLevel === 'construction' && !empty($data['group_name'])) {
            $circle = Circle::where('name', $data['group_name'])->first();
            if ($circle && auth()->user()->can('assign student to circle')) {
                $student->update(['circle_id' => $circle->id]);
            }
        }

        if ($mode === 'create') {
            $student->$relation()->create($fields);
        } else {
            $student->$relation()->updateOrCreate(['student_id' => $student->id], $fields);
            foreach ($config['others'] as $other) {
                $student->$other()->delete();
            }
        }
    }

    // ✅ مزامنة حالة ولي الأمر بناءً على حالة أبنائه
    private function syncGuardianStatus(int|null $guardianId): void
    {
        if (!$guardianId) return;

        $guardian = User::find($guardianId);
        if (!$guardian) return;

        $hasActiveStudents = Student::where('guardian_id', $guardianId)
            ->whereIn('status', ['مقيد', 'مسافر'])
            ->exists();

        $guardian->update([
            'status' => $hasActiveStudents ? 'active' : 'inactive',
        ]);
    }
}
