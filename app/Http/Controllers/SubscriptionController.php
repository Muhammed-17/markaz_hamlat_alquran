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
        // view subscriptions = admin/general_manager/manager/supervisor/teacher
        // view own subscriptions = guardian
        if (!Auth::user()->canAny(['view subscriptions', 'view own subscriptions'])) {
            abort(403);
        }

        $user             = Auth::user();
        $selectedMonth    = $request->get('month', now()->format('Y-m'));
        $monthStart       = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth()->format('Y-m-d');
        $selectedStatus   = $request->get('status');
        $search           = $request->get('search');

        $circles   = $this->getAccessibleCircles($user);
        $circleIds = $circles->pluck('id');

        // ─── التحقق من circle_id: لازم يكون ضمن حلقات المستخدم ──
        $selectedCircleId = $request->get('circle_id');
        if ($selectedCircleId && !$circleIds->contains($selectedCircleId)) {
            abort(403, 'ليس لديك صلاحية لعرض هذه الحلقة.');
        }

        if (!$selectedCircleId && $circles->count() === 1) {
            $selectedCircleId = $circles->first()->id;
        }

        // ─── guardian لا يملك search ─────────────────────────────
        if ($user->can('view own children')) {
            $search = null;
        }

        // ─── Base query ───────────────────────────────────────────
        $statsBaseQuery = Subscription::query();

        if ($user->can('view own subscriptions') && !$user->can('view subscriptions')) {
            // guardian: يرى اشتراكات أبنائه فقط
            $statsBaseQuery->whereIn('student_id', $user->students()->pluck('id'));
        } else {
            $this->applyCircleFilter($statsBaseQuery, $user, $circleIds);
        }

        if ($selectedCircleId) {
            $statsBaseQuery->where('circle_id', $selectedCircleId);
        }

        // ─── إيرادات آخر 6 أشهر (حسب paid_at) — فقط لمن يملك view subscriptions chart ──
        $monthlyRevenue = collect();
        $monthlyCollected = 0;
        $statusStats = collect();
        $paymentStats = collect();
        $paymentRate = 0;
        $unpaidAmount = 0;

        if ($user->can('view subscriptions chart')) {
            $monthlyRevenue = (clone $statsBaseQuery)
                ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month_label, SUM(amount) as total")
                ->where('status', 'مدفوع')
                ->whereNotNull('paid_at')
                ->groupBy('month_label')
                ->orderBy('month_label')
                ->take(6)
                ->get();

            // إجمالي التحصيل الفعلي للشهر المختار (paid_at)
            $monthlyCollected = (clone $statsBaseQuery)
                ->where('status', 'مدفوع')
                ->whereNotNull('paid_at')
                ->whereRaw("DATE_FORMAT(paid_at, '%Y-%m') = ?", [$selectedMonth])
                ->sum('amount');

            // إحصائيات الحالة (حسب شهر الاستحقاق)
            $statusStats = (clone $statsBaseQuery)
                ->selectRaw('status, count(*) as count, SUM(amount) as total_amount')
                ->where('month', $monthStart)
                ->groupBy('status')
                ->get();

            // طرق الدفع
            $paymentStats = (clone $statsBaseQuery)
                ->selectRaw('payment_method, count(*) as count')
                ->where('status', 'مدفوع')
                ->where('month', $monthStart)
                ->groupBy('payment_method')
                ->get();

            // ─── نسبة السداد والمبالغ غير المحصلة ────────────────
            $studentsQuery = Student::where('status', 'مقيد');

            if ($user->can('view own subscriptions') && !$user->can('view subscriptions')) {
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

            $prices   = \App\Models\SubscriptionPrice::all();
            $priceMap = [];
            foreach ($prices as $p) {
                $priceMap[$p->circle_level . '|' . $p->education_stage] = (float) $p->amount;
            }

            $paidSubscriptionsMap = (clone $statsBaseQuery)
                ->where('month', $monthStart)
                ->where('status', 'مدفوع')
                ->pluck('amount', 'student_id');

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
        }

        // ─── سجل الاشتراكات (Paginated) ───────────────────────────
        $recentSubscriptionsQuery = (clone $statsBaseQuery)
            ->with(['student', 'collectedBy'])
            ->where(function ($q) use ($monthStart, $selectedMonth) {
                $q->where('month', $monthStart)
                    ->orWhere(function ($q2) use ($selectedMonth) {
                        $q2->where('status', 'مدفوع')
                            ->whereNotNull('paid_at')
                            ->whereRaw("DATE_FORMAT(paid_at, '%Y-%m') = ?", [$selectedMonth]);
                    });
            });

        // البحث: متاح فقط لمن يملك view subscriptions (وليس guardian)
        if ($search && $user->can('view subscriptions')) {
            $recentSubscriptionsQuery->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($selectedStatus) {
            $recentSubscriptionsQuery->where('status', $selectedStatus);
        }

        $recentSubscriptions = $recentSubscriptionsQuery
            ->orderByRaw('COALESCE(paid_at, created_at) DESC')
            ->paginate(15)
            ->withQueryString();

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
        // create subscriptions فقط
        $this->authorize('create subscriptions');

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
        $this->authorize('create subscriptions');

        $validated = $request->validated();
        $month     = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()->format('Y-m-d');
        $isExempt  = $validated['status'] === 'معفي';

        // التحقق إن الـ circle_id ضمن حلقات المستخدم
        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);
        if (!$circleIds->contains($validated['circle_id'])) {
            abort(403, 'ليس لديك صلاحية لإضافة اشتراك لهذه الحلقة.');
        }

        $data = [
            'student_id'     => $validated['student_id'],
            'circle_id'      => $validated['circle_id'],
            'month'          => $month,
            'status'         => $validated['status'],
            'amount'         => $isExempt ? 0 : $validated['amount'],
            'payment_method' => $isExempt ? null : ($validated['payment_method'] ?? null),
            'paid_at'        => $validated['status'] === 'مدفوع' ? now() : null,
            'collected_by'   => $validated['status'] === 'مدفوع' ? Auth::id() : null,
            'notes'          => $validated['notes'] ?? null,
        ];

        Subscription::create($data);

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم تسجيل الاشتراك بنجاح');
    }

    // ─────────────────────────────────────────
    public function lateAndUnpaid(Request $request)
    {
        if (!Auth::user()->can('view subscriptions')) {
            abort(403);
        }

        $user             = Auth::user();
        $selectedCircleId = $request->get('circle_id');
        $circles          = $this->getAccessibleCircles($user);
        $circleIds        = $circles->pluck('id');

        // التحقق من circle_id
        if ($selectedCircleId && !$circleIds->contains($selectedCircleId)) {
            abort(403, 'ليس لديك صلاحية لعرض هذه الحلقة.');
        }

        $query = Student::where('status', 'مقيد')
            ->with([
                'subscriptions' => fn($q) => $q->where('status', 'مدفوع'),
                'circle'
            ]);

        $this->applyCircleFilter($query, $user, $circleIds);

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
        $this->authorize('edit subscriptions');

        // التحقق إن الاشتراك ضمن نطاق المستخدم
        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);
        if (!$circleIds->contains($subscription->circle_id)) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا الاشتراك.');
        }

        $circles  = $this->getAccessibleCircles($user);
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
        $this->authorize('edit subscriptions');

        // التحقق إن الاشتراك ضمن نطاق المستخدم
        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);
        if (!$circleIds->contains($subscription->circle_id)) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا الاشتراك.');
        }

        $validated = $request->validate([
            'circle_id'      => 'required|exists:circles,id',
            'student_id'     => 'required|exists:students,id',
            'month'          => 'required|date_format:Y-m',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'required|in:مدفوع,معفي',
            'payment_method' => 'nullable|in:نقدي,تحويل بنكي,أخرى',
            'notes'          => 'nullable|string|max:500',
        ]);

        // التحقق إن الـ circle_id الجديد ضمن نطاق المستخدم أيضاً
        if (!$circleIds->contains($validated['circle_id'])) {
            abort(403, 'ليس لديك صلاحية لنقل الاشتراك لهذه الحلقة.');
        }

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
            'paid_at'        => $validated['status'] === 'مدفوع' ? ($subscription->paid_at ?? now()) : null,
            'collected_by'   => $validated['status'] === 'مدفوع' ? ($subscription->collected_by ?? Auth::id()) : null,
            'notes'          => $validated['notes'] ?? null,
        ];

        $subscription->update($data);

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    // ─────────────────────────────────────────
    public function destroy(Subscription $subscription)
    {
        $this->authorize('delete subscriptions');

        // التحقق إن الاشتراك ضمن نطاق المستخدم
        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);
        if (!$circleIds->contains($subscription->circle_id)) {
            abort(403, 'ليس لديك صلاحية لحذف هذا الاشتراك.');
        }

        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم حذف الاشتراك بنجاح');
    }
}
