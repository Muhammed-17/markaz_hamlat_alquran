<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        // Simple monthly summary for the chart
        $stats = Attendance::selectRaw('status, count(*) as count')
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('status')
            ->get();

        $dailyStats = Attendance::selectRaw('date, count(*) as count')
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

        // Fetch circles based on user role
        $user = $request->user();
        if ($user->hasRole('admin')) {
            $circles = Circle::all();
        } elseif ($user->hasRole('teacher') && $user->teacher) {
            $circles = $user->teacher->circles;
        } else {
            $circles = collect();
        }

        $selectedCircleId = $request->get('circle_id', $circles->first()?->id);
        $students = collect();
        $attendanceData = collect();

        if ($selectedCircleId) {
            $students = Student::where('circle_id', $selectedCircleId)->get();
            $attendanceData = Attendance::where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.create', compact('circles', 'students', 'attendanceData', 'date', 'selectedCircleId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
        ]);

        $date = $request->date;

        foreach ($request->attendance as $data) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'date' => $date,
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'تم حفظ سجل الحضور بنجاح');
    }
    public function report(Request $request)
    {
        $circles = Circle::all();
        $selectedCircleId = $request->get('circle_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $attendanceQuery = Attendance::with('student.circle')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($selectedCircleId) {
            $attendanceQuery->whereHas('student', function ($q) use ($selectedCircleId) {
                $q->where('circle_id', $selectedCircleId);
            });
        }

        $records = $attendanceQuery->orderBy('date', 'desc')->paginate(20);

        return view('attendance.report', compact('records', 'circles', 'selectedCircleId', 'startDate', 'endDate'));
    }
}
