<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
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
        $monthStart       = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth()->format('Y-m-d');
        $selectedStatus   = $request->get('status');
        $search           = $request->get('search');

        $circles   = $this->getAccessibleCircles($user);
        $circleIds = $circles->pluck('id');

        if (!$selectedCircleId && $circles->count() === 1) {
            $selectedCircleId = $circles->first()->id;
        }

        // ─── Base query ───────────────────────────────────────────
        $statsBaseQuery = Subscription::query();

        if ($user->hasRole('guardian')) {
            $statsBaseQuery->whereIn('student_id', $user->students()->pluck('id'));
        } else {
            $this->applyCircleFilter($statsBaseQuery, $user, $circleIds);
        }

        if ($selectedCircleId) {
            $statsBaseQuery->where('circle_id', $selectedCircleId);
        }

        // ─── إيرادات آخر 6 أشهر (حسب تاريخ الدفع الفعلي paid_at) ──
        $monthlyRevenue = (clone $statsBaseQuery)
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month_label, SUM(amount) as total")
            ->where('status', 'مدفوع')
            ->whereNotNull('paid_at')
            ->groupBy('month_label')
            ->orderBy('month_label')
            ->take(6)
            ->get();

        // ─── إجمالي التحصيل الفعلي للشهر المختار (paid_at) ──────
        $monthlyCollected = (clone $statsBaseQuery)
            ->where('status', 'مدفوع')
            ->whereNotNull('paid_at')
            ->whereRaw("DATE_FORMAT(paid_at, '%Y-%m') = ?", [$selectedMonth])
            ->sum('amount');

        // ─── إحصائيات الحالة (حسب شهر الاستحقاق month) ──────────
        $statusStats = (clone $statsBaseQuery)
            ->selectRaw('status, count(*) as count, SUM(amount) as total_amount')
            ->where('month', $monthStart)
            ->groupBy('status')
            ->get();

        // ─── طرق الدفع (حسب شهر الاستحقاق) ──────────────────────
        $paymentStats = (clone $statsBaseQuery)
            ->selectRaw('payment_method, count(*) as count')
            ->where('status', 'مدفوع')
            ->where('month', $monthStart)
            ->groupBy('payment_method')
            ->get();

        // ─── سجل الاشتراكات (كل ما دُفع في الشهر المختار بـ paid_at) ──
        $recentSubscriptionsQuery = (clone $statsBaseQuery)
            ->with(['student', 'collectedBy'])
            ->where(function ($q) use ($monthStart, $selectedMonth) {
                // اشتراكات الشهر المختار (حسب الاستحقاق)
                $q->where('month', $monthStart)
                    // أو اشتراكات دُفعت فعلياً في الشهر المختار (شهور سابقة دُفعت متأخرة)
                    ->orWhere(function ($q2) use ($selectedMonth) {
                        $q2->where('status', 'مدفوع')
                            ->whereNotNull('paid_at')
                            ->whereRaw("DATE_FORMAT(paid_at, '%Y-%m') = ?", [$selectedMonth]);
                    });
            });

        if ($search) {
            $recentSubscriptionsQuery->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($selectedStatus) {
            $recentSubscriptionsQuery->where('status', $selectedStatus);
        }

        // ─── الترتيب: آخر عملية دفع أولاً ───────────────────────
        $recentSubscriptions = $recentSubscriptionsQuery
            ->orderByRaw('COALESCE(paid_at, created_at) DESC')
            ->paginate(15)
            ->withQueryString();

        // ─── حساب نسبة الدفع والمبلغ غير المدفوع ─────────────────
        $studentsQuery = Student::where('status', 'مقيد');

        if ($user->hasRole('guardian')) {
            $studentsQuery->where('guardian_id', $user->id);
        } else {
            $this->applyCircleFilter($studentsQuery, $user, $circleIds);
        }

        if ($selectedCircleId) {
            $studentsQuery->where('circle_id', $selectedCircleId);
        }

        $totalActiveStudents = (clone $studentsQuery)->count();

        $paidSubsCount = (clone $statsBaseQuery)
            ->where('month', $monthStart)
            ->where('status', 'مدفوع')
            ->count();

        $paymentRate = $totalActiveStudents > 0
            ? round(($paidSubsCount / $totalActiveStudents) * 100, 1)
            : 0;

        $prices = \App\Models\SubscriptionPrice::all();

        $priceMap = [];
        foreach ($prices as $p) {
            $priceMap[$p->circle_level . '|' . $p->education_stage] = (float) $p->amount;
        }

        $paidSubscriptionsMap = (clone $statsBaseQuery)
            ->where('month', $monthStart)
            ->where('status', 'مدفوع')
            ->pluck('amount', 'student_id');

        $unpaidAmount = 0;

        (clone $studentsQuery)
            ->with('circle:id,level')
            ->select('id', 'circle_id', 'educational_stage')
            ->chunk(500, function ($chunk) use (&$unpaidAmount, $priceMap, $paidSubscriptionsMap) {
                foreach ($chunk as $student) {
                    $key      = ($student->circle?->level) . '|' . $student->educational_stage;
                    $expected = $priceMap[$key] ?? 60.00;
                    $paid     = $paidSubscriptionsMap[$student->id] ?? 0;
                    $unpaidAmount += max(0, $expected - $paid);
                }
            });

        return view('subscription.index', compact(
            'monthlyRevenue',
            'monthlyCollected',
            'statusStats',
            'paymentStats',
            'recentSubscriptions',
            'circles',
            'selectedCircleId',
            'selectedMonth',
            'paymentRate',
            'unpaidAmount',
            'search',
            'selectedStatus'
        ));
    }

    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Subscription::class);

        $user      = Auth::user();
        $circles   = $this->getAccessibleCircles($user);
        $circleIds = $circles->pluck('id');

        $students = Student::with(['circle', 'subscriptions'])
            ->where('status', 'مقيد')
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
        $month     = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()->format('Y-m-d');
        $isExempt  = $validated['status'] === 'معفي';

        $data = [
            'student_id'     => $validated['student_id'],
            'circle_id'      => $validated['circle_id'],
            'month'          => $month,
            'status'         => $validated['status'],
            'amount'         => $isExempt ? 0 : $validated['amount'],
            'payment_method' => $isExempt ? null : ($validated['payment_method'] ?? null),
            'paid_at'        => $validated['status'] === 'مدفوع' ? now() : null,
            'notes'          => $validated['notes'] ?? null,
        ];

        Subscription::create($data);

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم تسجيل الاشتراك بنجاح');
    }

    // ─────────────────────────────────────────
    public function lateAndUnpaid(Request $request)
    {
        $this->authorize('viewAny', Subscription::class);

        $user             = Auth::user();
        $selectedCircleId = $request->get('circle_id');
        $circles          = $this->getAccessibleCircles($user);
        $circleIds        = $circles->pluck('id');

        $query = Student::where('status', 'مقيد')
            ->with([
                'subscriptions' => fn($q) => $q->where('status', 'مدفوع'),
                'circle'
            ]);

        if ($user->hasRole('guardian')) {
            $query->where('guardian_id', $user->id);
        } else {
            $this->applyCircleFilter($query, $user, $circleIds);
        }

        if ($selectedCircleId && $selectedCircleId !== 'all') {
            $query->where('circle_id', $selectedCircleId);
        }

        $students = $query->get()->map(function ($student) {
            $startDate = $student->join_date
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

    // ─────────────────────────────────────────
    public function edit(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $user    = Auth::user();
        $circles = $this->getAccessibleCircles($user);

        $students = Student::with(['circle', 'subscriptions'])
            ->where('status', 'مقيد')
            ->whereIn('circle_id', $circles->pluck('id'))
            ->get();

        $prices = \App\Models\SubscriptionPrice::all();

        return view('subscription.edit', compact('subscription', 'circles', 'students', 'prices'));
    }

    // ─────────────────────────────────────────
    public function update(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'circle_id'      => 'required|exists:circles,id',
            'student_id'     => 'required|exists:students,id',
            'month'          => 'required|date_format:Y-m',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'required|in:مدفوع,معفي',
            'payment_method' => 'nullable|in:نقدي,تحويل بنكي,أخرى',
            'notes'          => 'nullable|string|max:500',
        ]);

        $month    = Carbon::createFromFormat('Y-m', $validated['month'])
            ->startOfMonth()->format('Y-m-d');
        $isExempt = $validated['status'] === 'معفي';

        $data = [
            'student_id'     => $validated['student_id'],
            'circle_id'      => $validated['circle_id'],
            'month'          => $month,
            'status'         => $validated['status'],
            'amount'         => $isExempt ? 0 : $validated['amount'],
            'payment_method' => $isExempt ? null : ($validated['payment_method'] ?? null),
            'paid_at'        => $validated['status'] === 'مدفوع' ? now() : null,
            'notes'          => $validated['notes'] ?? null,
        ];

        $subscription->update($data);

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    // ─────────────────────────────────────────
    public function destroy(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);

        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم حذف الاشتراك بنجاح');
    }
}
