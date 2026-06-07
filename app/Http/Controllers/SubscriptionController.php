<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Circle;
use App\Models\Student;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Traits\ResolvesUserScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    use ResolvesUserScope;

    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Subscription::class);

        $user             = Auth::user();
        $selectedCircleId = $request->get('circle_id');
        $selectedMonth    = $request->get('month', now()->format('Y-m'));
        $monthStart       = Carbon::createFromFormat('Y-m', $selectedMonth)
            ->startOfMonth()->format('Y-m-d');

        // الحلقات المتاحة — من الـ Trait
        $circles   = $this->getAccessibleCircles($user);
        $circleIds = $circles->pluck('id');

        // اختيار تلقائي لو حلقة واحدة بس
        if (!$selectedCircleId && $circles->count() === 1) {
            $selectedCircleId = $circles->first()->id;
        }

        // Base query مفلتر بالحلقات المتاحة
        $statsBaseQuery = Subscription::query();

        if ($user->hasRole('guardian')) {
            $statsBaseQuery->whereIn('student_id', $user->students()->pluck('id'));
        } elseif (!$user->hasRole('admin')) {
            $circleIds->isEmpty()
                ? $statsBaseQuery->whereRaw('1=0')
                : $statsBaseQuery->whereIn('circle_id', $circleIds);
        }

        if ($selectedCircleId) {
            $statsBaseQuery->where('circle_id', $selectedCircleId);
        }

        // 1. إيرادات آخر 6 أشهر
        $monthlyRevenue = (clone $statsBaseQuery)
            ->selectRaw("DATE_FORMAT(month, '%Y-%m') as month_label, SUM(amount) as total")
            ->where('status', 'مدفوع')
            ->groupBy('month_label')
            ->orderBy('month_label', 'asc')
            ->take(6)
            ->get();

        // 2. توزيع الحالات
        $statusStats = (clone $statsBaseQuery)
            ->selectRaw('status, count(*) as count, SUM(amount) as total_amount')
            ->where('month', $monthStart)
            ->groupBy('status')
            ->get();

        // 3. طرق الدفع
        $paymentStats = (clone $statsBaseQuery)
            ->selectRaw('payment_method, count(*) as count')
            ->where('status', 'مدفوع')
            ->where('month', $monthStart)
            ->groupBy('payment_method')
            ->get();

        // 4. آخر الاشتراكات
        $recentSubscriptions = (clone $statsBaseQuery)
            ->with(['student', 'circle', 'collectedBy'])
            ->where('month', $monthStart)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // 5. نسبة الدفع والمبلغ غير المدفوع
        $studentsQuery = Student::where('status', 'active');

        if ($user->hasRole('guardian')) {
            $studentsQuery->where('guardian_id', $user->id);
        } elseif (!$user->hasRole('admin')) {
            $circleIds->isEmpty()
                ? $studentsQuery->whereRaw('1=0')
                : $studentsQuery->whereIn('circle_id', $circleIds);
        }

        if ($selectedCircleId) {
            $studentsQuery->where('circle_id', $selectedCircleId);
        }

        $students            = $studentsQuery->with('circle')->get();
        $totalActiveStudents = $students->count();

        $paidSubsCount = (clone $statsBaseQuery)
            ->where('month', $monthStart)
            ->where('status', 'مدفوع')
            ->count();

        $paymentRate = $totalActiveStudents > 0
            ? round(($paidSubsCount / $totalActiveStudents) * 100, 1)
            : 0;

        $prices = \App\Models\SubscriptionPrice::all();

        $paidSubscriptionsMap = (clone $statsBaseQuery)
            ->where('month', $monthStart)
            ->where('status', 'مدفوع')
            ->pluck('amount', 'student_id');

        $unpaidAmount = 0;
        foreach ($students as $student) {
            $priceObj = $prices
                ->where('circle_level', $student->circle?->level)
                ->where('education_level', $student->education_level)
                ->first();
            $expected = $priceObj?->amount ?? 60.00;
            $paid     = $paidSubscriptionsMap[$student->id] ?? 0;

            if ($paid < $expected) {
                $unpaidAmount += ($expected - $paid);
            }
        }

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

    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Subscription::class);

        $user      = Auth::user();
        $circles   = $this->getAccessibleCircles($user)->load([
            'students' => fn($q) =>
            $q->where('status', 'active')
        ]);
        $circleIds = $circles->pluck('id');

        $students = Student::with(['circle', 'subscriptions'])
            ->where('status', 'active')
            ->whereIn('circle_id', $circleIds)
            ->get();

        $prices = \App\Models\SubscriptionPrice::all();

        return view('subscription.create', compact('circles', 'students', 'prices'));
    }

    // ─────────────────────────────────────────
    public function store(StoreSubscriptionRequest $request)
    {
        $this->authorize('create', Subscription::class);

        $validated = $request->validated();
        $month     = Carbon::createFromFormat('Y-m', $validated['month'])
            ->startOfMonth()->format('Y-m-d');

        Subscription::create([
            'student_id'     => $validated['student_id'],
            'circle_id'      => $validated['circle_id'],
            'collected_by'   => Auth::id(),
            'amount'         => $validated['amount'],
            'month'          => $month,
            'status'         => $validated['status'],
            'payment_method' => $validated['payment_method'],
            'paid_at'        => $validated['status'] === 'مدفوع' ? now() : null,
            'notes'          => $validated['notes'] ?? null,
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'تم تسجيل الاشتراك بنجاح');
    }

    // ─────────────────────────────────────────
    public function lateAndUnpaid(Request $request)
    {
        $this->authorize('viewAny', Subscription::class);

        $user             = Auth::user();
        $selectedCircleId = $request->get('circle_id');
        $circles          = $this->getAccessibleCircles($user);
        $circleIds        = $circles->pluck('id');

        $query = Student::where('status', 'active')
            ->with([
                'subscriptions' => fn($q) =>
                $q->where('status', 'مدفوع'),
                'circle'
            ]);

        if ($user->hasRole('guardian')) {
            $query->where('guardian_id', $user->id);
        } elseif (!$user->hasRole('admin')) {
            $circleIds->isEmpty()
                ? $query->whereRaw('1=0')
                : $query->whereIn('circle_id', $circleIds);
        }

        if ($selectedCircleId && $selectedCircleId !== 'all') {
            $query->where('circle_id', $selectedCircleId);
        }

        $students = $query->get()->map(function ($student) {
            $startDate = $student->join_date             // ✅ موحد مع باقي الكود
                ? $student->join_date->copy()->startOfMonth()
                : $student->created_at->copy()->startOfMonth();

            $paidMonths = $student->subscriptions
                ->pluck('month')
                ->map(fn($d) => $d->format('Y-m'))
                ->unique()
                ->toArray();

            $unpaidMonthsList = [];
            $checkDate        = $startDate->copy();

            while ($checkDate->lte(now()->startOfMonth())) {
                $monthStr = $checkDate->format('Y-m');
                if (!in_array($monthStr, $paidMonths)) {
                    $unpaidMonthsList[] = $checkDate->locale('ar')->monthName
                        . ' ' . $checkDate->format('Y');
                }
                $checkDate->addMonth();
            }

            $student->unpaid_months_list  = $unpaidMonthsList;
            $student->unpaid_months_count = count($unpaidMonthsList);
            return $student;
        })->filter(fn($s) => $s->unpaid_months_count > 0);

        return view('subscription.late_and_unpaid', compact(
            'students',
            'circles',
            'selectedCircleId'
        ));
    }
}
