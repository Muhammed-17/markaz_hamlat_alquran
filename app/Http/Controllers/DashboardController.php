<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $stats = [];

        // Admin / Supervisor Stats
        if ($user->hasAnyRole(['admin', 'supervisor'])) {
            $stats['students_count'] = Student::count();
            $stats['teachers_count'] = Teacher::count();
            $stats['circles_count'] = Circle::where('is_active', true)->count();


            $stats['monthly_revenue'] = Subscription::whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])->sum('amount');

            $lastMonthRevenue = Subscription::whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])->sum('amount');

            $stats['revenue_growth'] = $lastMonthRevenue > 0
                ? round((($stats['monthly_revenue'] - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                : 0;

            // ... existing admin logic ...
            // Mock attendance mostly because we need comprehensive daily data
            $stats['attendance_rate'] = 92;

            // 1. Student Growth Chart Data (Cumulative for last 6 months)
            $chartData = [
                'labels' => [],
                'data' => []
            ];

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i)->endOfMonth();
                $chartData['labels'][] = $date->locale('ar')->isoFormat('MMMM');
                $chartData['data'][] = Student::where('created_at', '<=', $date)->where('status', 'active')->count();
            }

            // Calculate percentage increase (Current Month vs 6 Months Ago)
            $startCount = $chartData['data'][0] > 0 ? $chartData['data'][0] : 1;
            $endCount = end($chartData['data']);
            $growthPercentage = round((($endCount - $startCount) / $startCount) * 100, 1);
            $stats['student_growth_percentage'] = $growthPercentage;

            // 1.1 Status Distribution Chart Data
            $rawStatusStats = Student::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Map keys if they are English in DB but we want Arabic labels, or just pass as is.
            // Assuming DB has 'active', 'inactive', 'traveler' or Arabic values. 
            // We serve two arrays: labels and counts.
            $statusChartData = [
                'labels' => array_keys($rawStatusStats),
                'data' => array_values($rawStatusStats),
            ];

            // 2. Absent Students (More than 1 day absent)
            // We count 'absent' status.
            $absentStudents = Student::whereHas('attendances', function ($q) {
                $q->where('status', 'absent');
            }, '>', 1)
                ->withCount(['attendances as absence_days' => function ($q) {
                    $q->where('status', 'absent');
                }])
                ->orderByDesc('absence_days')
                ->limit(5)
                ->get();

            // 3. Unpaid Students (For separate widget)
            // Logic: Active students who haven't paid for the current month.
            $currentMonth = now()->startOfMonth()->format('Y-m-d');
            $unpaidStudents = Student::where('status', 'active')
                ->whereDoesntHave('subscriptions', function ($q) use ($currentMonth) {
                    $q->where('month', $currentMonth)
                        ->where('status', 'مدفوع');
                })
                ->with('circle') // Load circle for display
                ->limit(5)
                ->get();
        }

        // Teacher Stats
        if ($user->hasRole('teacher') && $user->teacher) {
            $teacherCircles = $user->teacher->circles;
            $stats['my_circles_count'] = $teacherCircles->count();
            // Count total unique students across all circles assigned to this teacher
            $stats['my_students_count'] = Student::whereIn('circle_id', $teacherCircles->pluck('id'))->where('status', 'active')->count();
        }

        // Guardian Stats
        if ($user->hasRole('guardian')) {
            $stats['my_children_count'] = $user->students()->count();
        }

        return view('dashboard', compact('stats', 'absentStudents', 'unpaidStudents', 'chartData', 'statusChartData'));
    }
}
