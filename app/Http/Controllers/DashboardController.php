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

            // Mock attendance mostly because we need comprehensive daily data
            $stats['attendance_rate'] = 92; 
        }

        // Teacher Stats
        if ($user->hasRole('teacher') && $user->teacher) {
            $teacherCircles = $user->teacher->circles;
            $stats['my_circles_count'] = $teacherCircles->count();
            // Count total unique students across all circles assigned to this teacher
            $stats['my_students_count'] = $teacherCircles->flatMap(function ($circle) {
                return $circle->students;
            })->unique('id')->count();
        }

        // Guardian Stats
        if ($user->hasRole('guardian')) {
            $stats['my_children_count'] = $user->students()->count();
        }

        return view('dashboard', compact('stats'));
    }
}
