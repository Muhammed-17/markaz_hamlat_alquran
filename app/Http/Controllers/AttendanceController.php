<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Student;
use App\Models\Attendance;
use App\Notifications\SequentialAbsenceNotification;
use App\Http\Requests\CreateAttendanceRequest;
use App\Traits\ResolvesUserScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    use ResolvesUserScope;

    // ─────────────────────────────────────────
    public function report()
    {
        $this->authorize('viewAny', Attendance::class);

        $user  = Auth::user();
        $query = Attendance::query();

        if ($user->hasRole('guardian')) {
            $query->whereHas(
                'student',
                fn($q) => $q->where('guardian_id', $user->id)
            );
        } elseif (!$user->hasRole('admin')) {
            $circleIds = $this->getAccessibleCircleIds($user);

            $circleIds->isEmpty()
                ? $query->whereRaw('1=0')
                : $query->whereHas(
                    'student',
                    fn($q) => $q->whereIn('circle_id', $circleIds)
                );
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

        return view('attendance.report', compact('stats', 'dailyStats'));
    }

    // ─────────────────────────────────────────
    public function create(Request $request)
    {
        $this->authorize('create', Attendance::class);

        $user    = Auth::user();
        $date    = $request->get('date', Carbon::today()->format('Y-m-d'));
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
    public function store(CreateAttendanceRequest $request)
    {
        $this->authorize('create', Attendance::class);

        $validated = $request->validated();
        $date      = $validated['date'];
        $circleId  = $validated['circle_id'];
        $user      = Auth::user();

        // ── تحقق إن المستخدم يملك صلاحية على هذه الحلقة ──────────
        if (!$user->hasRole('admin')) {
            $accessibleIds = $this->getAccessibleCircleIds($user);
            if (!$accessibleIds->contains($circleId)) {
                abort(403, 'ليس لديك صلاحية على هذه الحلقة.');
            }
        }

        // ── جلب الطلاب المسموح بهم فقط في هذه الحلقة ─────────────
        $validStudentIds = Student::where('circle_id', $circleId)
            ->where('status', 'مقيد')
            ->pluck('id');

        foreach ($validated['attendance'] as $data) {
            if (!$validStudentIds->contains($data['student_id'])) continue;

            Attendance::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'date'       => $date,
                ],
                [
                    'status'  => $data['status'],
                    'notes'   => $data['notes'] ?? null,
                    'user_id' => Auth::id(),
                ]
            );
        }

        return redirect()->route('attendance.index')->with('success', 'تم حفظ سجل الحضور بنجاح');
    }

    // ─────────────────────────────────────────
    public function sequentialAbsences()
    {
        $this->authorize('viewAny', Attendance::class);

        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);

        $studentQuery = Student::with([
            'attendances' => fn($q) => $q->orderBy('date', 'desc')->take(30),
            'circle.supervisor',
        ])->where('status', '!=', 'متوقف');

        if ($user->hasRole('guardian')) {
            $studentQuery->where('guardian_id', $user->id);
        } elseif (!$user->hasRole('admin')) {
            $circleIds->isEmpty()
                ? $studentQuery->whereRaw('1=0')
                : $studentQuery->whereIn('circle_id', $circleIds);
        }

        $students = $studentQuery->get()
            ->filter(fn($s) => $this->hasSequentialPattern($s))
            ->map(function ($student) {
                $statuses              = $student->attendances->sortBy('date')->pluck('status')->toArray();
                $student->absence_days = collect($statuses)->filter(fn($s) => $s === 'absent')->count();
                return $student;
            })
            ->sortByDesc('absence_days')
            ->values();

        return view('attendance.sequential-absences', compact('students'));
    }

    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);

        $startDate           = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate             = $request->get('end_date', now()->format('Y-m-d'));
        $sortOrder           = in_array($request->get('sort_order', 'desc'), ['asc', 'desc'])
            ? $request->get('sort_order', 'desc') : 'desc';
        $selectedCircleId    = $request->get('circle_id');
        $selectedRegistrarId = $request->get('user_id');

        $circles = $this->getAccessibleCircles($user);

        // ── جلب المسجلين مع فلترة بالحلقات المتاحة ────────────────
        $registrars = \App\Models\User::whereHas('attendances', function ($q) use ($circleIds, $user) {
            if (!$user->hasRole('admin')) {
                $q->whereHas(
                    'student',
                    fn($s) => $s->whereIn('circle_id', $circleIds)
                );
            }
        })->get();

        $attendanceQuery = Attendance::with(['student.circle.mainTeacher', 'user'])
            ->whereBetween('date', [$startDate, $endDate]);

        // ── فلترة بالحلقات المتاحة حسب الدور ──────────────────────
        if ($user->hasRole('guardian')) {
            $attendanceQuery->whereHas(
                'student',
                fn($q) => $q->where('guardian_id', $user->id)
            );
        } elseif (!$user->hasRole('admin')) {
            $circleIds->isEmpty()
                ? $attendanceQuery->whereRaw('1=0')
                : $attendanceQuery->whereHas(
                    'student',
                    fn($q) => $q->whereIn('circle_id', $circleIds)
                );
        }

        // ── فلتر الحلقة مع تحقق من الصلاحية ───────────────────────
        if ($selectedCircleId) {
            if (!$user->hasRole('admin') && !$circleIds->contains($selectedCircleId)) {
                abort(403, 'ليس لديك صلاحية على هذه الحلقة.');
            }

            $attendanceQuery->whereHas(
                'student',
                fn($q) => $q->where('circle_id', $selectedCircleId)
            );
        }

        // ── فلتر المسجل مع تحقق من الوجود ─────────────────────────
        if ($selectedRegistrarId) {
            if (!\App\Models\User::find($selectedRegistrarId)) {
                abort(404, 'المسجل غير موجود.');
            }

            $attendanceQuery->where('user_id', $selectedRegistrarId);
        }

        $records = $attendanceQuery
            ->orderBy('date', $sortOrder)
            ->paginate(20)
            ->withQueryString();

        return view('attendance.index', compact(
            'records',
            'circles',
            'registrars',
            'selectedCircleId',
            'selectedRegistrarId',
            'startDate',
            'endDate',
            'sortOrder'
        ));
    }

    // ─────────────────────────────────────────
    public function notifyStudent(Student $student, Request $request)
    {
        $this->authorize('update', $student);

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

        // ── حساب الغيابات في آخر 30 يوم فقط ──────────────────────
        $absenceDays = $student->attendances()
            ->where('status', 'absent')
            ->where('date', '>=', now()->subDays(30))
            ->count();

        // ── تنظيف الرسالة قبل الحفظ ────────────────────────────────
        $message = $request->input('message')
            ? strip_tags($request->input('message'))
            : null;

        $guardian->notify(new SequentialAbsenceNotification(
            $student,
            $absenceDays,
            $message
        ));

        return response()->json(['message' => 'تم إرسال التنبيه بنجاح.']);
    }

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
    private function hasSequentialPattern(Student $student): bool
    {
        $statuses = $student->attendances->sortBy('date')->pluck('status')->toArray();
        $count    = count($statuses);

        for ($i = 0; $i < $count - 1; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 1] === 'absent') return true;
        }
        for ($i = 0; $i < $count - 2; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 2] === 'absent') return true;
        }

        return false;
    }
}
