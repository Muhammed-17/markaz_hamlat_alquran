<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use App\Notifications\SequentialAbsenceNotification;
use App\Traits\ResolvesUserScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use App\Exports\AttendanceMonthlyReportExport;

class AttendanceController extends Controller
{
    use ResolvesUserScope;

    // ─────────────────────────────────────────
    // Dashboard / Report
    // ─────────────────────────────────────────
    public function report()
    {
        $this->authorize('viewAny', Attendance::class);

        $user = Auth::user();
        $query = Attendance::query();

        if ($user->hasRole('guardian')) {
            $query->whereHas('student', fn($q) => $q->where('guardian_id', $user->id));
        } elseif (!$user->hasRole(['admin', 'general_manager'])) {
            $circleIds = $this->getAccessibleCircleIds($user);
            $circleIds->isEmpty()
                ? $query->whereRaw('1=0')
                : $query->whereHas('student', fn($q) => $q->whereIn('circle_id', $circleIds));
        }

        $stats = (clone $query)
            ->selectRaw('status, count(*) as count')
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('status')
            ->get();

        $dailyStats = (clone $query)
            ->selectRaw('date, count(*) as count')
            ->where('date', '>=', now()->subDays(7))
            ->where('status', 'present')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $monthlyStats = (clone $query)
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, status, count(*) as count")
            ->where('date', '>=', now()->subMonths(12))
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        $circleStats = (clone $query)
            ->where('date', '>=', now()->subDays(30))
            ->whereHas('student.circle')
            ->with('student.circle')
            ->get()
            ->groupBy('student.circle.name')
            ->map(fn($group) => [
                'total'   => $group->count(),
                'present' => $group->where('status', 'present')->count(),
                'absent'  => $group->where('status', 'absent')->count(),
                'late'    => $group->where('status', 'late')->count(),
                'excused' => $group->where('status', 'excused')->count(),
                'rate'    => $group->count() > 0
                    ? round($group->where('status', 'present')->count() / $group->count() * 100, 1)
                    : 0,
            ]);

        return view('attendance.report', compact('stats', 'dailyStats', 'monthlyStats', 'circleStats'));
    }

    // ─────────────────────────────────────────
    // Create attendance form
    // ─────────────────────────────────────────
    public function create(Request $request)
    {
        $this->authorize('create', Attendance::class);

        $user   = Auth::user();
        $date   = $request->get('date', Carbon::today()->format('Y-m-d'));
        $circles = $this->getAccessibleCircles($user);

        $selectedCircleId = $request->get('circle_id', $circles->first()?->id);
        $students         = collect();
        $attendanceData   = collect();

        if ($selectedCircleId) {
            $students = Student::where('circle_id', $selectedCircleId)
                ->where('status', 'مقيد')
                ->get();

            $attendanceData = Attendance::where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.create', compact(
            'circles',
            'students',
            'attendanceData',
            'date',
            'selectedCircleId'
        ));
    }

    // ─────────────────────────────────────────
    // Store attendance
    // ─────────────────────────────────────────
    public function store(StoreAttendanceRequest $request)
    {
        $this->authorize('create', Attendance::class);

        $validated = $request->validated();
        $date      = $validated['date'];
        $circleId  = $validated['circle_id'];
        $user      = Auth::user();

        if (!$user->hasRole(['admin', 'general_manager'])) {
            $accessibleIds = $this->getAccessibleCircleIds($user);
            if (!$accessibleIds->contains($circleId)) {
                abort(403, 'ليس لديك صلاحية على هذه الحلقة.');
            }
        }

        $validStudentIds = Student::where('circle_id', $circleId)
            ->where('status', 'مقيد')
            ->pluck('id');

        foreach ($validated['attendance'] as $data) {
            if (!$validStudentIds->contains($data['student_id'])) continue;

            Attendance::updateOrCreate(
                ['student_id' => $data['student_id'], 'date' => $date],
                [
                    'status' => $data['status'],
                    'notes'  => !empty($data['notes']) ? $data['notes'] : null,
                    'user_id' => Auth::id()
                ]
            );
        }

        return redirect()->route('attendance.index')
            ->with('success', 'تم حفظ سجل الحضور بنجاح');
    }

    // ─────────────────────────────────────────
    // Show single attendance record
    // ─────────────────────────────────────────
    public function show(Attendance $attendance)
    {
        $this->authorize('view', $attendance);

        $attendance->load(['student.circle.mainTeachers', 'user']);

        $previousRecord = Attendance::where('student_id', $attendance->student_id)
            ->where('date', '<', $attendance->date)
            ->orderBy('date', 'desc')->first();

        $nextRecord = Attendance::where('student_id', $attendance->student_id)
            ->where('date', '>', $attendance->date)
            ->orderBy('date', 'asc')->first();

        $monthlySummary = Attendance::where('student_id', $attendance->student_id)
            ->whereMonth('date', $attendance->date->month)
            ->whereYear('date', $attendance->date->year)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('attendance.show', compact(
            'attendance',
            'previousRecord',
            'nextRecord',
            'monthlySummary'
        ));
    }

    // ─────────────────────────────────────────
    // Edit attendance form
    // ─────────────────────────────────────────
    public function edit(Attendance $attendance)
    {
        $this->authorize('update', $attendance);

        $attendance->load(['student.circle', 'user']);  // ✅ تأكد من تحميل العلاقات

        return view('attendance.edit', compact('attendance'));
    }

    // ─────────────────────────────────────────
    // Update attendance record
    // ─────────────────────────────────────────
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        $this->authorize('update', $attendance);

        $validated = $request->validated();

        $attendance->update([
            'status' => $validated['status'],
            'notes'  => $validated['notes'] ?? null,
            'date'   => $validated['date'],
        ]);

        return redirect()->route('attendance.index')
            ->with('success', 'تم تحديث سجل الحضور بنجاح');
    }


    // ─────────────────────────────────────────
    // Delete attendance record
    // ─────────────────────────────────────────
    public function destroy(Attendance $attendance)
    {
        $this->authorize('delete', $attendance);

        $attendance->delete();

        return redirect()->route('attendance.index')
            ->with('success', 'تم حذف سجل الحضور بنجاح');
    }




    // ─────────────────────────────────────────
    // List attendance records
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);

        $selectedDate        = $request->filled('date')
            ? Carbon::parse($request->get('date'))->format('Y-m-d')
            : null;
        $sortOrder           = in_array($request->get('sort_order', 'desc'), ['asc', 'desc'])
            ? $request->get('sort_order', 'desc') : 'desc';
        $selectedCircleId    = $request->get('circle_id');
        $selectedCenterId    = $request->get('center_id');
        $selectedRegistrarId = $request->get('user_id');
        $selectedStatus      = $request->get('status');
        $search              = $request->get('search');

        $circles = $this->getAccessibleCircles($user);

        $centers = $user->hasRole(['admin', 'general_manager'])
            ? \App\Models\Center::orderBy('name')->get()
            : collect();

        $registrars = $user->can('view all teachers')
            ? User::whereHas('attendances', function ($q) use ($circleIds, $user) {
                if (!$user->hasRole(['admin', 'general_manager'])) {
                    $q->whereHas('student', fn($s) => $s->whereIn('circle_id', $circleIds));
                }
            })->get()
            : collect();

        // ── بناء الاستعلام ──────
        $attendanceQuery = Attendance::with([
            'student.circle.mainTeachers',
            'student.center',
            'user',
        ]);

        // ── فلتر التاريخ ──────
        if ($selectedDate) {
            $attendanceQuery->whereDate('date', $selectedDate);
        }

        // ── البحث باسم الطالب ──────
        if ($search) {
            $attendanceQuery->whereHas('student', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        // ── تصفية حسب الدور ──────
        if ($user->hasRole('guardian')) {
            $attendanceQuery->whereHas('student', fn($q) => $q->where('guardian_id', $user->id));
        } elseif (!$user->hasRole(['admin', 'general_manager'])) {
            if ($circleIds->isEmpty()) {
                $emptyRecords = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
                return view('attendance.index', compact(
                    'circles',
                    'centers',
                    'registrars',
                    'selectedCircleId',
                    'selectedCenterId',
                    'selectedRegistrarId',
                    'selectedStatus',
                    'selectedDate',
                    'sortOrder',
                    'search',
                ) + ['records' => $emptyRecords]);
            }
            $attendanceQuery->whereHas('student', fn($q) => $q->whereIn('circle_id', $circleIds));
        }

        // ── فلتر الفرع ──────
        if ($selectedCenterId && $user->hasRole(['admin', 'general_manager'])) {
            $attendanceQuery->whereHas('student', fn($q) => $q->where('center_id', $selectedCenterId));
        }

        // ── فلتر الحلقة ──────
        if ($selectedCircleId) {
            if (!$user->hasRole(['admin', 'general_manager']) && !$circleIds->contains($selectedCircleId)) {
                abort(403, 'ليس لديك صلاحية على هذه الحلقة.');
            }
            $attendanceQuery->whereHas('student', fn($q) => $q->where('circle_id', $selectedCircleId));
        }

        // ── فلتر المسجل ──────
        if ($selectedRegistrarId && $user->can('view all teachers')) {
            $attendanceQuery->where('user_id', $selectedRegistrarId);
        }

        // ── فلتر الحالة ──────
        if ($selectedStatus && in_array($selectedStatus, ['present', 'absent', 'late', 'excused'])) {
            $attendanceQuery->where('status', $selectedStatus);
        }

        $records = $attendanceQuery
            ->orderBy('date', $sortOrder)
            ->paginate(20)
            ->withQueryString();

        return view('attendance.index', compact(
            'records',
            'circles',
            'centers',
            'registrars',
            'selectedCircleId',
            'selectedCenterId',
            'selectedRegistrarId',
            'selectedStatus',
            'selectedDate',
            'sortOrder',
            'search'
        ));
    }


    // ─────────────────────────────────────────
    // My Attendance (for guardians)
    // ─────────────────────────────────────────
    public function myAttendance(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('view own attendance')) abort(403);

        $month        = $request->get('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $students = Student::where('guardian_id', $user->id)
            ->with([
                'attendances' => fn($q) => $q
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->orderBy('date', 'desc'),
            ])->get();

        $summary = $students->map(function ($student) {
            $total = $student->attendances->count();
            return [
                'student'         => $student,
                'total_days'      => $total,
                'present'         => $student->attendances->where('status', 'present')->count(),
                'absent'          => $student->attendances->where('status', 'absent')->count(),
                'late'            => $student->attendances->where('status', 'late')->count(),
                'excused'         => $student->attendances->where('status', 'excused')->count(),
                'attendance_rate' => $total > 0
                    ? round($student->attendances->where('status', 'present')->count() / $total * 100, 1)
                    : 0,
            ];
        });

        return view('attendance.my-attendance', compact('summary', 'month'));
    }

    // ─────────────────────────────────────────
    // Sequential Absences
    // ─────────────────────────────────────────
    public function sequentialAbsences()
    {
        $this->authorize('viewAny', Attendance::class);

        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);

        $studentQuery = Student::with([
            'attendances' => fn($q) => $q->orderBy('date', 'desc')->take(30),
            'circle.supervisors',
            'circle.mainTeachers',
        ])->where('status', '!=', 'متوقف');

        if ($user->hasRole('guardian')) {
            $studentQuery->where('guardian_id', $user->id);
        } elseif (!$user->hasRole(['admin', 'general_manager'])) {
            $circleIds->isEmpty()
                ? $studentQuery->whereRaw('1=0')
                : $studentQuery->whereIn('circle_id', $circleIds);
        }

        $students = $studentQuery->get()
            ->filter(fn($s) => $this->hasSequentialPattern($s))
            ->map(function ($student) {
                $statuses              = $student->attendances->sortBy('date')->pluck('status')->toArray();
                $student->absence_days    = collect($statuses)->filter(fn($s) => $s === 'absent')->count();
                $student->sequential_count = $this->countSequentialAbsences($student);
                return $student;
            })
            ->sortByDesc('absence_days')
            ->values();

        return view('attendance.sequential-absences', compact('students'));
    }

    // ─────────────────────────────────────────
    // Notify guardian about sequential absences
    // ─────────────────────────────────────────
    public function notifyStudent(Student $student, Request $request)
    {
        $this->authorize('update', $student);

        $key = 'notify-student:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json(['message' => "يرجى الانتظار {$seconds} ثانية قبل إرسال تنبيه آخر."], 429);
        }
        RateLimiter::hit($key, 60);

        $guardian = $student->guardian;
        if (!$guardian) {
            return response()->json(['message' => 'لا يوجد ولي أمر مرتبط بهذا الطالب.'], 422);
        }

        $alreadyNotified = $guardian->notifications()
            ->where('type', SequentialAbsenceNotification::class)
            ->whereDate('created_at', today())
            ->where('data', 'like', '%"student_id":' . $student->id . '%')
            ->exists();

        if ($alreadyNotified) {
            return response()->json(['message' => 'تم إرسال تنبيه بالفعل اليوم لهذا الطالب.'], 409);
        }

        $absenceDays = $student->attendances()
            ->where('status', 'absent')
            ->where('date', '>=', now()->subDays(30))
            ->count();

        $message = $request->input('message') ? strip_tags($request->input('message')) : null;

        $guardian->notify(new SequentialAbsenceNotification($student, $absenceDays, $message));

        return response()->json(['message' => 'تم إرسال التنبيه بنجاح.']);
    }

    // ─────────────────────────────────────────
    // Toggle guardian contact status
    // ─────────────────────────────────────────
    public function toggleContact(Student $student)
    {
        $this->authorize('update', $student);

        $student->update(['is_guardian_contacted' => !$student->is_guardian_contacted]);
        $student->refresh();

        return response()->json([
            'message'               => $student->is_guardian_contacted
                ? 'تم تأكيد التواصل مع ولي الأمر.'
                : 'تم إلغاء تأكيد التواصل.',
            'is_guardian_contacted' => $student->is_guardian_contacted,
        ]);
    }

    // ─────────────────────────────────────────
    // Export to Excel
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $user      = Auth::user();
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));
        $circleId  = $request->get('circle_id');

        $accessibleCircleIds = null; // null = مفيش تقييد (أدمن/مدير عام)

        if (!$user->hasRole(['admin', 'general_manager'])) {
            $allowedIds = $this->getAccessibleCircleIds($user);

            if ($circleId) {
                if (!$allowedIds->contains($circleId)) {
                    abort(403, 'ليس لديك صلاحية على هذه الحلقة.');
                }
            } else {
                // مفيش حلقة محددة، يبقى نقيّد التصدير بحلقاته فقط
                $accessibleCircleIds = $allowedIds;
            }
        }

        $filename = 'attendance_export_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new AttendanceExport($startDate, $endDate, $circleId, $accessibleCircleIds),
            $filename
        );
    }

    // ─────────────────────────────────────────
    // Monthly Report Export
    // ─────────────────────────────────────────
    public function exportMonthly(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $month    = $request->get('month', now()->format('Y-m'));
        $circleId = $request->get('circle_id');
        $filename = 'monthly_report_' . $month . '.xlsx';

        return Excel::download(new AttendanceMonthlyReportExport($month, $circleId), $filename);
    }

    // ─────────────────────────────────────────
    // PDF Report
    // ─────────────────────────────────────────
    public function pdfReport(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));
        $circleId  = $request->get('circle_id');

        $query = Attendance::with(['student.circle', 'user'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($circleId) {
            $query->whereHas('student', fn($q) => $q->where('circle_id', $circleId));
        }

        $records = $query->orderBy('date', 'desc')->get();

        $summary = [
            'total'   => $records->count(),
            'present' => $records->where('status', 'present')->count(),
            'absent'  => $records->where('status', 'absent')->count(),
            'late'    => $records->where('status', 'late')->count(),
            'excused' => $records->where('status', 'excused')->count(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('attendance.pdf-report', compact(
            'records',
            'summary',
            'startDate',
            'endDate'
        ));

        return $pdf->download('attendance_report_' . now()->format('Y-m-d') . '.pdf');
    }

    // ─────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────
    private function hasSequentialPattern(Student $student): bool
    {
        $statuses = $student->attendances->sortBy('date')->pluck('status')->toArray();
        $count    = count($statuses);

        if ($count < 2) return false;

        for ($i = 0; $i < $count - 1; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 1] === 'absent') return true;
        }

        for ($i = 0; $i < $count - 2; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 2] === 'absent') return true;
        }

        for ($i = 0; $i <= $count - 3; $i++) {
            $window   = array_slice($statuses, $i, 5);
            $absences = collect($window)->filter(fn($s) => $s === 'absent')->count();
            if ($absences >= 3) return true;
        }

        return false;
    }

    private function countSequentialAbsences(Student $student): int
    {
        $statuses      = $student->attendances->sortBy('date')->pluck('status')->toArray();
        $maxStreak     = 0;
        $currentStreak = 0;

        foreach ($statuses as $status) {
            if ($status === 'absent') {
                $currentStreak++;
                $maxStreak = max($maxStreak, $currentStreak);
            } else {
                $currentStreak = 0;
            }
        }

        return $maxStreak;
    }
}
