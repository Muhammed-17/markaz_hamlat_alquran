<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Circle;
use App\Models\Student;
use App\Http\Requests\StoreSubscriptionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedCircleId = $request->get('circle_id');
        $selectedMonth = $request->get('month', now()->format('Y-m'));

        // تحديد الحلقات حسب نوع المستخدم
        $circles = collect();
        if ($user->hasRole('admin')) {
            $circles = Circle::all();
        } elseif ($user->hasRole('supervisor') && $user->teacher) {
            $circles = Circle::where('supervisor_id', $user->teacher->id)->get();
        } elseif ($user->hasRole('teacher') && $user->teacher) {
            $circles = $user->teacher->circles()->get();
        }

        // في حال المعلم او المشرف عنده حلقة واحدة فقط
        if (!$selectedCircleId && $circles->count() === 1) {
            $selectedCircleId = $circles->first()->id;
        }

        // Base query for stats
        $statsBaseQuery = Subscription::query();

        // Role-based filtering for stats
        if ($user->hasRole('supervisor')) {
            $supervisedCircleIds = $circles->pluck('id');
            $statsBaseQuery->whereIn('circle_id', $supervisedCircleIds);
        } elseif ($user->hasRole('teacher')) {
            $teacherCircleIds = $circles->pluck('id');
            $statsBaseQuery->whereIn('circle_id', $teacherCircleIds);
        } elseif ($user->hasRole('guardian')) {
            $childIds = $user->students()->pluck('id');
            $statsBaseQuery->whereIn('student_id', $childIds);
        }

        if ($selectedCircleId) {
            $statsBaseQuery->where('circle_id', $selectedCircleId);
        }

        // 1. Revenue by Month (Last 6 Months)
        $monthlyRevenueQuery = (clone $statsBaseQuery)
            ->selectRaw("DATE_FORMAT(month, '%Y-%m') as month_label, SUM(amount) as total")
            ->where('status', 'مدفوع')
            ->groupBy('month_label')
            ->orderBy('month_label', 'asc')
            ->take(6);

        $monthlyRevenue = $monthlyRevenueQuery->get();


        // 2. Status Distribution
        $monthStart = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth()->format('Y-m-d');

        $statusStats = (clone $statsBaseQuery)
            ->selectRaw('status, count(*) as count, SUM(amount) as total_amount')
            ->where('month', $monthStart)
            ->groupBy('status')
            ->get();

        // 3. Payment Method Distribution
        $paymentStats = (clone $statsBaseQuery)
            ->selectRaw('payment_method, count(*) as count')
            ->where('status', 'مدفوع')
            ->where('month', $monthStart)
            ->groupBy('payment_method')
            ->get();

        // 4. Recently Collected
        $recentQuery = (clone $statsBaseQuery)
            ->with(['student', 'circle', 'collectedBy'])
            ->orderBy('created_at', 'desc')
            ->take(10);

        if ($selectedMonth) {
            $recentQuery->where('month', $monthStart);
        }

        $recentSubscriptions = $recentQuery->get();

        // Calculate Payment Rate (Relative to Total Active Students)
        $studentsQuery = Student::where('status', 'active');
        if ($user->hasRole('supervisor')) {
            $studentsQuery->whereIn('circle_id', $circles->pluck('id'));
        } elseif ($user->hasRole('teacher')) {
            $studentsQuery->whereIn('circle_id', $circles->pluck('id'));
        } elseif ($user->hasRole('guardian')) {
            $studentsQuery->where('guardian_id', $user->id);
        }

        if ($selectedCircleId) {
            $studentsQuery->where('circle_id', $selectedCircleId);
        }

        $students = $studentsQuery->with('circle')->get();
        $totalActiveStudents = $students->count();

        $paidSubsCount = (clone $statsBaseQuery)
            ->where('month', $monthStart)
            ->where('status', 'مدفوع')
            ->count();

        $paymentRate = $totalActiveStudents > 0 ? round(($paidSubsCount / $totalActiveStudents) * 100, 1) : 0;

        // Calculate Total Expected Revenue & Unpaid Amount
        $prices = \App\Models\SubscriptionPrice::all();

        $paidSubscriptionsMap = (clone $statsBaseQuery)
            ->where('month', $monthStart)
            ->where('status', 'مدفوع')
            ->pluck('amount', 'student_id');

        $realUnpaidAmount = 0;
        foreach ($students as $student) {
            $circleLevel = $student->circle?->level;
            $eduLevel = $student->education_level;
            $priceObj = $prices->where('circle_level', $circleLevel)
                ->where('education_level', $eduLevel)
                ->first();
            $expected = $priceObj?->amount ?? 60.00;

            $paid = $paidSubscriptionsMap[$student->id] ?? 0;

            if ($paid < $expected) {
                $realUnpaidAmount += ($expected - $paid);
            }
        }

        $unpaidAmount = $realUnpaidAmount;

        return view('subscription.index', compact(
            'monthlyRevenue',
            'statusStats',
            'paymentStats',
            'recentSubscriptions',
            'circles',
            'selectedCircleId',
            'selectedMonth',
            'paymentRate',
            'unpaidAmount'
        ));
    }


    //TODO: function to show create subscription
    public function create()
    {
        $user = auth()->user();
        $circlesQuery = Circle::with(['students' => function ($q) {
            $q->where('status', 'active');
        }]);

        if ($user->hasRole('supervisor')) {
            $circlesQuery->where('supervisor_id', $user->teacher->id);
        } elseif ($user->hasRole('teacher')) {
            $circlesQuery->whereHas('teachers', function ($q) use ($user) {
                $q->where('teachers.id', $user->teacher->id);
            });
        }

        $circles = $circlesQuery->get();
        $circleIds = $circles->pluck('id');

        $students = Student::with(['circle', 'subscriptions'])
            ->where('status', 'active')
            ->whereIn('circle_id', $circleIds)
            ->get();

        $prices = \App\Models\SubscriptionPrice::all();

        return view('subscription.create', compact('circles', 'students', 'prices'));
    }

    //TODO: function to store subscription
    public function store(StoreSubscriptionRequest $request)
    {
        $validated = $request->validated();

        $month = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()->format('Y-m-d');

        Subscription::create([
            'student_id' => $validated['student_id'],
            'circle_id' => $validated['circle_id'],
            'collected_by' => Auth::id(),
            'amount' => $validated['amount'],
            'month' => $month,
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'],
            'paid_at' => $validated['status'] === 'مدفوع' ? now() : null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'تم تسجيل الاشتراك بنجاح');
    }
    public function lateAndUnpaid(Request $request)
    {
        $user = Auth::user();
        $selectedCircleId = $request->get('circle_id');

        // تحديد الحلقات حسب نوع المستخدم
        $circles = collect();
        if ($user->hasRole('admin')) {
            $circles = Circle::all();
        } elseif ($user->hasRole('supervisor') && $user->teacher) {
            $circles = Circle::where('supervisor_id', $user->teacher->id)->get();
        } elseif ($user->hasRole('teacher') && $user->teacher) {
            $circles = $user->teacher->circles()->get();
        }

        $query = Student::where('status', 'active')
            ->with(['subscriptions' => function ($q) {
                $q->where('status', 'مدفوع');
            }, 'circle']);

        // Filter by role
        if ($user->hasRole('supervisor')) {
            $query->whereIn('circle_id', $circles->pluck('id'));
        } elseif ($user->hasRole('teacher')) {
            $query->whereIn('circle_id', $circles->pluck('id'));
        } elseif ($user->hasRole('guardian')) {
            $query->where('guardian_id', $user->id);
        }

        if ($selectedCircleId && $selectedCircleId !== 'all') {
            $query->where('circle_id', $selectedCircleId);
        }

        $students = $query->get()->map(function ($student) {
            $startDate = $student->enrollment_date
                ? $student->enrollment_date->copy()->startOfMonth()
                : $student->created_at->copy()->startOfMonth();

            $currentDate = now()->startOfMonth();

            $paidMonths = $student->subscriptions
                ->pluck('month')
                ->map(fn($d) => $d->format('Y-m'))
                ->unique()
                ->toArray();

            $unpaidMonthsList = [];

            $checkDate = $startDate->copy();

            while ($checkDate->lte($currentDate)) {
                $monthStr = $checkDate->format('Y-m');
                if (!in_array($monthStr, $paidMonths)) {
                    $unpaidMonthsList[] = $checkDate->locale('ar')->monthName . ' ' . $checkDate->format('Y');
                }
                $checkDate->addMonth();
            }

            $student->unpaid_months_list = $unpaidMonthsList;
            $student->unpaid_months_count = count($unpaidMonthsList);
            return $student;
        })->filter(fn($s) => $s->unpaid_months_count > 0);

        return view('subscription.late_and_unpaid', compact('students', 'circles', 'selectedCircleId'));
    }
}
