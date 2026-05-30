<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStudentRequest;
use App\Http\Requests\EditStudentRequest;
use App\Models\Student;
use App\Models\Circle;
use App\Models\User;
use App\Http\Requests\StoreStudentRegistrationRequest;
use App\Models\StudentConstructionDetail;
use App\Models\StudentItqanDetail;
use App\Models\StudentIbdaDetail;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class StudentController extends Controller
{
    private function normalizeEducationLevel(?string $educationalStage): string
    {
        return match ($educationalStage) {
            'حضانة', 'تمهيدي' => 'preschool',
            'ابتدائي' => 'primary',
            'اعدادي' => 'secondary',
            'ثانوي' => 'high_school',
            'جامعي' => 'university',
            default => 'other',
        };
    }

    public function index()
    {
        $user = Auth::user();
        $query = Student::with('circle');

        if ($user->hasRole('guardian')) {
            $query->where('guardian_id', $user->id);
        } elseif (!$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                if ($user->hasRole('supervisor') && $user->teacher) {
                    $supervisedCircleIds = Circle::where('supervisor_id', $user->teacher->id)->pluck('id');
                    $q->orWhereIn('circle_id', $supervisedCircleIds);
                }

                if ($user->hasRole('teacher') && $user->teacher) {
                    $teacherCircleIds = $user->teacher->circles->pluck('id');
                    $q->orWhereIn('circle_id', $teacherCircleIds);
                }
            })->where('status', '!=', 'inactive');
        }

        $students = $query->orderBy('name')->get();

        return view('students.index', compact('students'));
    }

    private function generateStudentCode(): string
    {
        $prefix = 'STU-' . now()->format('Y') . '-';
        $last = Student::where('student_code', 'like', $prefix . '%')
            ->orderBy('student_code', 'desc')
            ->value('student_code');
        $nextNumber = $last ? (int) substr($last, -5) + 1 : 1;
        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function create()
    {
        $circles = Circle::all();
        $guardians = User::role('guardian')->get();
        $subscriptionPrices = DB::table('subscription_prices')->get();
        $teachers = Teacher::all();
        $supervisors = Teacher::whereHas('user', function ($q) {
            $q->whereHas('roles', function ($r) {
                $r->where('name', 'supervisor');
            });
        })->get();
        $generatedCode = $this->generateStudentCode();

        return view('students.create', compact(
            'circles',
            'guardians',
            'subscriptionPrices',
            'teachers',
            'supervisors',
            'generatedCode'
        ));
    }

    public function store(StoreStudentRegistrationRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Create or select guardian User account
            if ($data['guardian_id'] === 'new') {
                $guardian = User::create([
                    'name' => $data['applicant'] ?? $data['name'],
                    'email' => $data['parent_email'],
                    'mobile' => $data['whatsapp_number'] ?? '',
                    'password' => Hash::make($data['password']),
                    'status' => 'active',
                ]);
                $guardian->assignRole('guardian');
                $guardianId = $guardian->id;
            } else {
                $guardianId = (int)$data['guardian_id'];
            }

            // Set guardian_id
            $data['guardian_id'] = $guardianId;

            // Determine which detail level fields to extract
            $constructionFields = ['current_surah', 'study_system', 'group_name', 'new_memorization_plan', 'placement_evaluation', 'old_memorization_plan'];
            $itqanFields = ['previous_memorization_side', 'previous_khatamat_count', 'current_review_amount', 'self_evaluation', 'memorized_texts', 'desired_path', 'preferred_time', 'teacher_name', 'itqan_details'];
            $ibdaFields = ['previous_licenses_and_chains', 'desired_narration_and_path', 'preferred_time', 'ibda_details'];
            $allDetailFields = array_merge($constructionFields, $itqanFields, $ibdaFields);

            // Extract only student table fields
            $studentData = array_diff_key($data, array_flip($allDetailFields));

            // DB requires education_level and many flows currently fill educational_stage only.
            if (empty($studentData['education_level'])) {
                $studentData['education_level'] = $this->normalizeEducationLevel($studentData['educational_stage'] ?? null);
            }

            // Handle status/suspended_at logic
            if (isset($studentData['status']) && $studentData['status'] === 'inactive') {
                $studentData['suspended_at'] = now();
            }

            // Create student
            $student = Student::create($studentData);

            // Conditionally create detail record based on center_entry_level
            switch ($data['center_entry_level']) {
                case 'construction':
                    $detailData = array_intersect_key($data, array_flip($constructionFields));
                    $student->constructionDetail()->create($detailData);
                    break;
                case 'itqan':
                    $detailData = array_intersect_key($data, array_flip($itqanFields));
                    $student->itqanDetail()->create($detailData);
                    break;
                case 'ibda':
                    $detailData = array_intersect_key($data, array_flip($ibdaFields));
                    $student->ibdaDetail()->create($detailData);
                    break;
            }

            DB::commit();

            // JSON response for AJAX (Alpine.js / Axios)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل الطالب بنجاح',
                    'redirect' => route('students.index'),
                    'student' => $student->load(['constructionDetail', 'itqanDetail', 'ibdaDetail']),
                ]);
            }

            // Standard HTTP redirect fallback
            return redirect()->route('students.index')
                ->with('success', 'تم تسجيل الطالب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            $errorMessage = 'حدث خطأ أثناء تسجيل الطالب: ' . $e->getMessage();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function show($id)
    {
        $student = Student::with(['circle.mainTeacher', 'guardian', 'attendances', 'subscriptions'])
            ->findOrFail($id);

        Gate::authorize('view', $student);

        // Attendance stats
        $totalAttendance = $student->attendances->count();
        $presentCount = $student->attendances->where('status', 'present')->count();
        $absentCount = $student->attendances->where('status', 'absent')->count();
        $lateCount = $student->attendances->where('status', 'late')->count();
        $excusedCount = $student->attendances->where('status', 'excused')->count();

        $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100) : 0;

        // Financial stats — build month timeline frozen at suspension date if inactive
        $startDate = $student->enrollment_date
            ? $student->enrollment_date->copy()->startOfMonth()
            : $student->created_at->copy()->startOfMonth();

        $endDate = $student->status === 'inactive' && $student->suspended_at
            ? $student->suspended_at->copy()->startOfMonth()
            : now()->startOfMonth();

        $feeTimeline = collect();
        $subscriptionIndex = $student->subscriptions->keyBy(fn($sub) => $sub->month->format('Y-m'));

        $checkDate = $startDate->copy();
        while ($checkDate->lte($endDate)) {
            $monthKey = $checkDate->format('Y-m');
            $sub = $subscriptionIndex->get($monthKey);

            $feeTimeline->push((object) [
                'month' => $checkDate->copy(),
                'subscription' => $sub,
                'is_paid' => $sub && $sub->status === 'مدفوع',
            ]);

            $checkDate->addMonth();
        }

        $feeTimeline = $feeTimeline->sortByDesc('month');
        $totalPaidAmount = $student->subscriptions->where('status', 'مدفوع')->sum('amount');
        $paidMonthsCount = $student->subscriptions->where('status', 'مدفوع')->count();

        return view('students.show', [
            "student" => $student,
            "attendanceRate" => $attendanceRate,
            "presentCount" => $presentCount,
            "absentCount" => $absentCount,
            "lateCount" => $lateCount,
            "excusedCount" => $excusedCount,
            "unpaidMonthsCount" => $student->overdue_months_count,
            "paidMonthsCount" => $paidMonthsCount,
            "totalPaidAmount" => $totalPaidAmount,
            "feeTimeline" => $feeTimeline,
            "suspendedPastDebt" => $student->suspended_past_debt,
        ]);
    }

    public function edit($id)
    {
        $circles = Circle::all();
        $guardians = User::role('guardian')->get();
        $subscriptionPrices = DB::table('subscription_prices')->get();

        $teachers = Teacher::all();
        $supervisors = Teacher::whereHas('user', function ($q) {
            $q->whereHas('roles', function ($r) {
                $r->where('name', 'supervisor');
            });
        })->get();

        $student = Student::with(['guardian', 'constructionDetail', 'itqanDetail', 'ibdaDetail'])->findOrFail($id);
        return view('students.edit', [
            "student" => $student,
            "circles" => $circles,
            "guardians" => $guardians,
            "subscriptionPrices" => $subscriptionPrices,
            "teachers" => $teachers,
            "supervisors" => $supervisors
        ]);
    }

    public function update(EditStudentRequest $request, $id)
    {
        $student = Student::findOrFail($id);
        $data = $request->validated();

        if (($data['guardian_type'] ?? '') === 'new' && !empty($data['guardian_name']) && !empty($data['guardian_mobile'])) {
            $guardian = User::create([
                'name' => $data['guardian_name'],
                'mobile' => $data['guardian_mobile'],
                'email' => $data['guardian_mobile'] . '@markaz.local',
                'password' => Hash::make($data['guardian_mobile']),
                'status' => 'active',
            ]);
            $guardian->assignRole('guardian');
            $data['guardian_id'] = $guardian->id;
        }

        unset($data['guardian_type'], $data['guardian_name'], $data['guardian_mobile']);

        if (isset($data['status'])) {
            if ($data['status'] === 'inactive' && $student->status !== 'inactive') {
                $data['suspended_at'] = now();
            } elseif ($data['status'] !== 'inactive') {
                $data['suspended_at'] = null;
            }
        }

        $student->update($data);
        return redirect()->route('students.index')->with('success', 'تم تحديث الطالب بنجاح');
    }

    public function destroy($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->route('students.index')->with('success', 'تم حذف الطالب بنجاح');
    }
}
