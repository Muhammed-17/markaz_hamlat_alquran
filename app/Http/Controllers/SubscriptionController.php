<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Circle;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Facades\Role;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $selectedCircleId = $request->get('circle_id');
        $selectedMonth = $request->get('month', now()->format('Y-m'));

        // تحديد الحلقات حسب نوع المستخدم
        if (Auth::user()->hasRole('admin')) {
            $circles = Circle::all();
        } else {
            $teacher = Auth::user()->teacher; // علاقة user -> teacher
            $circles = $teacher->circles()->get();     // علاقة teacher -> circles (belongsToMany)
        }

        // في حال المعلم عنده حلقة واحدة فقط
        if (!$selectedCircleId && isset($teacher) && $circles->count() === 1) {
            $selectedCircleId = $circles->first()->id;
        }

        // Base query for stats
        $statsBaseQuery = Subscription::query();

        if ($selectedCircleId) {
            $statsBaseQuery->where('circle_id', $selectedCircleId);
        }

        // 1. Revenue by Month (Last 6 Months)
        $monthlyRevenueQuery = Subscription::selectRaw("DATE_FORMAT(month, '%Y-%m') as month_label, SUM(amount) as total")
            ->where('status', 'مدفوع')
            ->groupBy('month_label')
            ->orderBy('month_label', 'asc')
            ->take(6);

        if ($selectedCircleId) {
            $monthlyRevenueQuery->where('circle_id', $selectedCircleId);
        }
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
        $recentQuery = Subscription::with(['student', 'circle', 'collectedBy'])
            ->orderBy('created_at', 'desc')
            ->take(10);

        if ($selectedCircleId) {
            $recentQuery->where('circle_id', $selectedCircleId);
        }
        if ($selectedMonth) {
            $recentQuery->where('month', $monthStart);
        }

        $recentSubscriptions = $recentQuery->get();

        // Calculate Payment Rate (Relative to Total Active Students)
        $studentsQuery = Student::where('status', 'active');
        if ($selectedCircleId) {
            $studentsQuery->where('circle_id', $selectedCircleId);
        }
        // Need circles for level info
        $students = $studentsQuery->with('circle')->get();
        $totalActiveStudents = $students->count();

        $collectedAmount = Subscription::where('month', $monthStart)
            ->where('status', 'مدفوع')
            ->when($selectedCircleId, fn($q) => $q->where('circle_id', $selectedCircleId))
            ->sum('amount');

        $paidSubsCount = Subscription::where('month', $monthStart)
            ->where('status', 'مدفوع')
            ->when($selectedCircleId, fn($q) => $q->where('circle_id', $selectedCircleId))
            ->count();

        $paymentRate = $totalActiveStudents > 0 ? round(($paidSubsCount / $totalActiveStudents) * 100, 1) : 0;

        // Calculate Total Expected Revenue & Unpaid Amount
        // 1. Load all prices
        $prices = \App\Models\SubscriptionPrice::all();

        $totalExpected = 0;
        foreach ($students as $student) {
            // Find price matching circle level & education level
            // Default 60 if no price set or no circle.
            $circleLevel = $student->circle?->level;
            $eduLevel = $student->education_level;

            $priceObj = $prices->where('circle_level', $circleLevel)
                ->where('education_level', $eduLevel)
                ->first();

            $amount = $priceObj?->amount ?? 60.00;
            $totalExpected += $amount;
        }

        // Unpaid = Expected - Collected
        // If collected is more than expected (e.g. donations/extra), clamp to 0? No, let's show true difference.
        // Actually, if Collected > Expected, it means we earned extra. Unpaid should be "Debts".
        // A simple Expected - Collected works for "Net Missing Revenue".
        // But if Student A paid 100 (exp 50) and Student B paid 0 (exp 50), Total Exp 100, Total Coll 100. Diff 0.
        // But Student B still owes 50!
        // So global subtraction hides individual debts if others overpay.
        // However, iterating again to find individual paid status is expensive (N+1 lookups).
        // optimization: Load all subscriptions for this month into memory (keyed by student_id).

        $paidSubscriptionsMap = Subscription::where('month', $monthStart)
            ->where('status', 'مدفوع')
            ->when($selectedCircleId, fn($q) => $q->where('circle_id', $selectedCircleId))
            ->pluck('amount', 'student_id');

        $realUnpaidAmount = 0;
        foreach ($students as $student) {
            // Expected for this student
            $circleLevel = $student->circle?->level;
            $eduLevel = $student->education_level;
            $priceObj = $prices->where('circle_level', $circleLevel)
                ->where('education_level', $eduLevel)
                ->first();
            $expected = $priceObj?->amount ?? 60.00;

            // Paid by this student
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
        $circles = Circle::with(['students' => function ($q) {
            $q->where('status', 'مقيد');
        }])->get();

        $students = Student::with(['circle', 'subscriptions'])->where('status', 'active')->get();

        $prices = \App\Models\SubscriptionPrice::all();

        return view('subscription.create', compact('circles', 'students', 'prices'));
    }

    //TODO: function to store subscription
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'circle_id' => 'required|exists:circles,id',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|date_format:Y-m',
            'status' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $month = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->format('Y-m-d');

        Subscription::create([
            'student_id' => $validated['student_id'],
            'circle_id' => $validated['circle_id'],
            'collected_by' => Auth::id(),
            'amount' => $validated['amount'],
            'month' => $month,
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'],
            'paid_at' => $validated['status'] === 'مدفوع' ? now() : null,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'تم تسجيل الاشتراك بنجاح');
    }
}
