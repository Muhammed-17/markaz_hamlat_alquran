<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Student;
use App\Models\Attendance;
use App\Notifications\SequentialAbsenceNotification;
use App\Http\Requests\CreateAttendanceRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Attendance::query();

        if ($user->hasRole('admin')) {
            // No filter for admin
        } elseif ($user->hasRole('supervisor') && $user->teacher) {
            $supervisedCircleIds = Circle::where('supervisor_id', $user->teacher->id)->pluck('id');
            $query->whereHas('student', function ($q) use ($supervisedCircleIds) {
                $q->whereIn('circle_id', $supervisedCircleIds);
            });
        } elseif ($user->hasRole('teacher') && $user->teacher) {
            $teacherCircleIds = $user->teacher->circles->pluck('id');
            $query->whereHas('student', function ($q) use ($teacherCircleIds) {
                $q->whereIn('circle_id', $teacherCircleIds);
            });
        } elseif ($user->hasRole('guardian')) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('guardian_id', $user->id);
            });
        } else {
            $query->whereRaw('1=0');
        }

        // Simple monthly summary for the chart
        $stats = (clone $query)->selectRaw('status, count(*) as count')
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('status')
            ->get();

        $dailyStats = (clone $query)->selectRaw('date, count(*) as count')
            ->where('date', '>=', now()->subDays(7))
            ->where('status', 'present')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('attendance.index', compact('stats', 'dailyStats'));
    }

    public function create(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $user = $request->user();

        // Role-scoped circles
        if ($user->hasRole('admin')) {
            $circles = Circle::all();
        } elseif ($user->hasRole('supervisor') && $user->teacher) {
            $circles = Circle::where('supervisor_id', $user->teacher->id)->get();
        } elseif ($user->hasRole('teacher') && $user->teacher) {
            $circles = $user->teacher->circles;
        } elseif ($user->hasRole('guardian')) {
            $childCircleIds = Student::where('guardian_id', $user->id)
                ->where('status', 'active')
                ->pluck('circle_id')
                ->unique();
            $circles = Circle::whereIn('id', $childCircleIds)->get();
        } else {
            $circles = collect();
        }

        $selectedCircleId = $request->get('circle_id', $circles->first()?->id);
        $students = collect();
        $attendanceData = collect();

        if ($selectedCircleId) {
            $students = Student::where('circle_id', $selectedCircleId)
                ->where('status', 'active')
                ->get();
            $attendanceData = Attendance::where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.create', compact('circles', 'students', 'attendanceData', 'date', 'selectedCircleId'));
    }

    public function store(CreateAttendanceRequest $request)
    {
        $validated = $request->validated();
        $date = $validated['date'];

        foreach ($validated['attendance'] as $data) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'date' => $date,
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                    'user_id' => auth()->id(),
                ]
            );
        }

        return redirect()->back()->with('success', 'تم حفظ سجل الحضور بنجاح');
    }

    public function sequentialAbsences()
    {
        $user = auth()->user();

        $studentQuery = Student::with(['attendances' => function ($q) {
            $q->orderBy('date', 'desc')->take(30);
        }, 'circle.supervisor'])
            ->where('status', '!=', 'inactive');

        // Role-based scoping
        if ($user->hasRole('supervisor') && $user->teacher) {
            $studentQuery->whereHas('circle', fn($q) => $q->where('supervisor_id', $user->teacher->id));
        } elseif ($user->hasRole('teacher') && $user->teacher) {
            $studentQuery->whereIn('circle_id', $user->teacher->circles->pluck('id'));
        } elseif ($user->hasRole('guardian')) {
            $studentQuery->where('guardian_id', $user->id);
        }

        $students = $studentQuery->get()->filter(function ($student) {
            return $this->hasSequentialPattern($student);
        })->map(function ($student) {
            $records = $student->attendances->sortBy('date')->values();
            $statuses = $records->pluck('status')->toArray();
            $student->absence_days = collect($statuses)->filter(fn($s) => $s === 'absent')->count();
            return $student;
        })->sortByDesc('absence_days')->values();

        return view('attendance.sequential-absences', compact('students'));
    }

    public function notifyStudent(Student $student, Request $request)
    {
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

        $absenceDays = $student->attendances()->where('status', 'absent')->count();
        $message = $request->input('message');
        $guardian->notify(new SequentialAbsenceNotification($student, $absenceDays, $message));

        return response()->json(['message' => 'تم إرسال التنبيه بنجاح.']);
    }

    public function toggleContact(Student $student)
    {
        $student->update(['is_guardian_contacted' => !$student->is_guardian_contacted]);
        $student->refresh();

        return response()->json([
            'message' => $student->is_guardian_contacted
                ? 'تم تأكيد التواصل مع ولي الأمر.'
                : 'تم إلغاء تأكيد التواصل.',
            'is_guardian_contacted' => $student->is_guardian_contacted,
        ]);
    }

    private function hasSequentialPattern(Student $student): bool
    {
        $records = $student->attendances->sortBy('date')->values();
        $statuses = $records->pluck('status')->toArray();

        for ($i = 0; $i < count($statuses) - 1; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 1] === 'absent') {
                return true;
            }
        }

        for ($i = 0; $i < count($statuses) - 2; $i++) {
            if ($statuses[$i] === 'absent' && $statuses[$i + 2] === 'absent') {
                return true;
            }
        }

        return false;
    }

    public function report(Request $request)
    {
        $user = auth()->user();

        $circles = Circle::all();
        $registrars = \App\Models\User::whereHas('attendances')->get();

        $selectedCircleId = $request->get('circle_id');
        $selectedRegistrarId = $request->get('user_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $attendanceQuery = Attendance::with(['student.circle.mainTeacher', 'user'])
            ->whereBetween('date', [$startDate, $endDate]);

        // Role-based filtering for the query
        if ($user->hasRole('supervisor') && $user->teacher) {
            $attendanceQuery->whereHas('student', function ($q) use ($user) {
                $q->whereIn('circle_id', Circle::where('supervisor_id', $user->teacher->id)->pluck('id'));
            });
        } elseif ($user->hasRole('teacher') && $user->teacher) {
            $attendanceQuery->whereHas('student', function ($q) use ($user) {
                $q->whereIn('circle_id', $user->teacher->circles->pluck('id'));
            });
        } elseif ($user->hasRole('guardian')) {
            $attendanceQuery->whereHas('student', function ($q) use ($user) {
                $q->where('guardian_id', $user->id);
            });
        }

        if ($selectedCircleId) {
            $attendanceQuery->whereHas('student', function ($q) use ($selectedCircleId) {
                $q->where('circle_id', $selectedCircleId);
            });
        }

        if ($selectedRegistrarId) {
            $attendanceQuery->where('user_id', $selectedRegistrarId);
        }

        $records = $attendanceQuery->orderBy('date', $sortOrder)->paginate(20)->withQueryString();

        return view('attendance.report', compact('records', 'circles', 'registrars', 'selectedCircleId', 'selectedRegistrarId', 'startDate', 'endDate', 'sortOrder'));
    }
}
