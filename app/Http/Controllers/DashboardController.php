<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        /** @var User $user */
        if (Auth::user()->hasRole('guardian')) {
            return redirect()->route('guardian.dashboard');
        }
        $user = Auth::user();
        $stats = [];
        $absentStudents = collect();
        $unpaidStudents = collect();
        $chartData = ['labels' => [], 'data' => []];
        $statusChartData = ['labels' => [], 'data' => []];

        // 1. Determine Scope for Students, Circles, and Subscriptions
        $studentQuery = Student::where('status', '!=', 'متوقف');
        $circleQuery = Circle::where('is_active', true);
        $subscriptionQuery = Subscription::query();

        if ($user->hasRole('supervisor') && $user->teacher) {
            $supervisorId = $user->teacher->id;
            $studentQuery->whereHas('circle', fn($q) => $q->where('supervisor_id', $supervisorId));
            $circleQuery->where('supervisor_id', $supervisorId);
            $subscriptionQuery->whereHas('circle', fn($q) => $q->where('supervisor_id', $supervisorId));
        } elseif ($user->hasRole('teacher') && $user->teacher) {
            $teacherId = $user->teacher->id;
            $studentQuery->whereHas('circle', fn($q) => $q->whereHas('teachers', fn($t) => $t->where('teachers.id', $teacherId)));
            $circleQuery->whereHas('teachers', fn($q) => $q->where('teachers.id', $teacherId));
            $subscriptionQuery->whereHas('circle', fn($q) => $q->whereHas('teachers', fn($t) => $t->where('teachers.id', $teacherId)));
        }

        // 2. Dashboard Stats & Alerts Calculation
        if ($user->hasAnyRole(['admin', 'supervisor', 'guardian'])) {
            if ($user->hasAnyRole(['admin', 'supervisor'])) {
                $stats['students_count'] = (clone $studentQuery)->count();
                $stats['teachers_count'] = Teacher::count();
                $stats['circles_count'] = (clone $circleQuery)->count();

                $stats['monthly_revenue'] = (clone $subscriptionQuery)
                    ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('amount');

                $lastMonthRevenue = (clone $subscriptionQuery)
                    ->whereBetween('paid_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                    ->sum('amount');

                $stats['revenue_growth'] = $lastMonthRevenue > 0
                    ? round((($stats['monthly_revenue'] - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                    : 0;

                $stats['attendance_rate'] = 92;

                // Student Growth (Cumulative)
                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i)->endOfMonth();
                    $chartData['labels'][] = $date->locale('ar')->isoFormat('MMMM');
                    $chartData['data'][] = (clone $studentQuery)->where('created_at', '<=', $date)->count();
                }

                $startCount = $chartData['data'][0] > 0 ? $chartData['data'][0] : 1;
                $growthPercentage = round(((end($chartData['data']) - $startCount) / $startCount) * 100, 1);
                $stats['student_growth_percentage'] = $growthPercentage;

                // Status Distribution
                if ($user->hasRole('admin')) {
                    $rawStatusStats = Student::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
                } else {
                    $rawStatusStats = Student::whereHas('circle', fn($q) => $q->where('supervisor_id', $user->teacher->id))
                        ->selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
                }

                $statusChartData = [
                    'labels' => array_keys($rawStatusStats),
                    'data' => array_values($rawStatusStats),
                ];
            }

            // Calculation for Absent & Unpaid (Shared between Admin/Supervisor and Guardian)
            // Determine students to check for alerts
            $alertStudentsQuery = Student::where('status', '!=', 'متوقف');
            if ($user->hasRole('supervisor') && $user->teacher) {
                $alertStudentsQuery->whereHas('circle', fn($q) => $q->where('supervisor_id', $user->teacher->id));
            } elseif ($user->hasRole('guardian')) {
                $alertStudentsQuery->where('guardian_id', $user->id);
            } elseif ($user->hasRole('teacher') && $user->teacher) {
                $alertStudentsQuery->whereIn('circle_id', $user->teacher->circles->pluck('id'));
            }

            // Absent Students (Sequential Absence Patterns)
            $absentStudents = (clone $alertStudentsQuery)->with(['attendances' => function ($q) {
                $q->orderBy('date', 'desc')->take(30);
            }, 'circle'])->get()->map(function ($student) {
                $records = $student->attendances->sortBy('date')->values();
                $statuses = $records->pluck('status')->toArray();

                $hasPattern = false;

                // Condition 1: two or more consecutive absences
                for ($i = 0; $i < count($statuses) - 1; $i++) {
                    if ($statuses[$i] === 'absent' && $statuses[$i + 1] === 'absent') {
                        $hasPattern = true;
                        break;
                    }
                }

                // Condition 2: absent → (not absent) → absent pattern
                if (!$hasPattern) {
                    for ($i = 0; $i < count($statuses) - 2; $i++) {
                        if ($statuses[$i] === 'absent' && $statuses[$i + 2] === 'absent') {
                            $hasPattern = true;
                            break;
                        }
                    }
                }

                $student->absence_days = collect($statuses)->filter(fn($s) => $s === 'absent')->count();
                $student->has_sequential_absence = $hasPattern;
                return $student;
            })->filter(fn($s) => $s->has_sequential_absence)
                ->sortByDesc('absence_days')
                ->take(5);

            // Unpaid Students
            $unpaidStudents = (clone $alertStudentsQuery)->with(['subscriptions' => function ($q) {
                $q->where('status', 'مدفوع');
            }, 'circle'])->get()->map(function ($student) {
                $startDate = $student->enrollment_date ? $student->enrollment_date->copy()->startOfMonth() : $student->created_at->copy()->startOfMonth();
                $currentDate = now()->startOfMonth();
                $paidMonths = $student->subscriptions->pluck('month')->map(fn($d) => $d->format('Y-m'))->unique()->toArray();
                $unpaidCount = 0;
                $checkDate = $startDate->copy();
                while ($checkDate->lte($currentDate)) {
                    if (!in_array($checkDate->format('Y-m'), $paidMonths)) {
                        $unpaidCount++;
                    }
                    $checkDate->addMonth();
                }
                $student->unpaid_months_count = $unpaidCount;
                return $student;
            })->filter(fn($s) => $s->unpaid_months_count > 0)
                ->sortByDesc('unpaid_months_count')
                ->take(5);
        }

        // Additional Role-Specific Stats
        if ($user->hasRole('teacher') && $user->teacher) {
            $stats['my_circles_count'] = $user->teacher->circles()->count();
            $stats['my_students_count'] = Student::whereIn('circle_id', $user->teacher->circles->pluck('id'))->where('status', '!=', 'متوقف')->count();
        }

        if ($user->hasRole('guardian')) {
            $stats['my_children_count'] = $user->students()->count();
        }

        return view('dashboard', compact('stats', 'absentStudents', 'unpaidStudents', 'chartData', 'statusChartData'));
    }

    public function guardianDashboard(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $userId = $user->id;

        // 1) Active children count
        $activeChildrenCount = Student::where('guardian_id', $userId)
            ->where('status', 'مقيد')
            ->count();

        // 2) Total children count (all statuses)
        $totalChildrenCount = Student::where('guardian_id', $userId)->count();

        // 3) Latest absences for guardian's children
        $latestAbsences = Attendance::whereHas('student', function ($q) use ($userId) {
            $q->where('guardian_id', $userId);
        })
            ->where('status', 'absent')
            ->with('student')
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        // 4) Unpaid subscriptions for guardian's children
        $unpaidSubscriptions = Subscription::whereHas('student', function ($q) use ($userId) {
            $q->where('guardian_id', $userId);
        })
            ->where('status', '!=', 'مدفوع')
            ->with(['student', 'circle'])
            ->orderBy('month', 'desc')
            ->take(10)
            ->get();

        // 5) Attendance rate for this month per child
        $children = Student::where('guardian_id', $userId)->with(['attendances' => function ($q) {
            $q->where('date', '>=', now()->startOfMonth());
        }])->get();

        $attendanceStats = $children->map(function ($child) {
            $total = $child->attendances->count();
            $present = $child->attendances->where('status', 'present')->count();
            return [
                'name' => $child->name,
                'id' => $child->id,
                'rate' => $total > 0 ? round(($present / $total) * 100) : 0,
                'total' => $total,
                'present' => $present,
            ];
        });

        // 6) Unpaid months count for all children combined
        $unpaidMonthsTotal = 0;
        foreach (
            Student::where('guardian_id', $userId)->with(['subscriptions' => function ($q) {
                $q->where('status', 'مدفوع');
            }])->get() as $student
        ) {
            $startDate = $student->enrollment_date
                ? $student->enrollment_date->copy()->startOfMonth()
                : $student->created_at->copy()->startOfMonth();
            $currentDate = now()->startOfMonth();
            $paidMonths = $student->subscriptions->pluck('month')
                ->map(fn($d) => $d->format('Y-m'))->unique()->toArray();
            $checkDate = $startDate->copy();
            while ($checkDate->lte($currentDate)) {
                if (!in_array($checkDate->format('Y-m'), $paidMonths)) {
                    $unpaidMonthsTotal++;
                }
                $checkDate->addMonth();
            }
        }

        return view('guardian.guardian_dashboard', compact(
            'activeChildrenCount',
            'totalChildrenCount',
            'latestAbsences',
            'unpaidSubscriptions',
            'attendanceStats',
            'unpaidMonthsTotal'
        ));
    }
}
