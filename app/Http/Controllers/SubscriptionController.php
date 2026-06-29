<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Student;
use App\Models\User;
use App\Models\Center;
use App\Models\Circle;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
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
        if (!Auth::user()->canAny(['view subscriptions', 'view own subscriptions'])) {
            abort(403);
        }

        $user           = Auth::user();
        $selectedMonth  = $request->get('month');
        $monthStart     = $selectedMonth
            ? Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth()->format('Y-m-d')
            : now()->startOfMonth()->format('Y-m-d');
        $selectedStatus = $request->get('status');
        $search         = $request->get('search');
        $statsMonth     = $selectedMonth ?? now()->format('Y-m');

        $circles   = $this->getAccessibleCircles($user);
        $circleIds = $circles->pluck('id');

        $selectedCircleId = $request->get('circle_id');
        if ($selectedCircleId && !$circleIds->contains($selectedCircleId)) {
            abort(403, 'ليس لديك صلاحية لعرض هذه الحلقة.');
        }

        if (!$selectedCircleId && $circles->count() === 1) {
            $selectedCircleId = $circles->first()->id;
        }

        $selectedCenterId  = $request->get('center_id');
        $selectedTeacherId = $request->get('teacher_id');

        $teachers = collect();
        if ($user->can('view subscriptions')) {
            $teachers = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['supervisor', 'manager', 'general_manager', 'teacher']);
            })->orderBy('name')->get(['id', 'name']);
        }

        if ($user->can('view own children')) {
            $search = null;
        }

        $statsBaseQuery = Subscription::query();

        // ✅ تطبيق فلتر المعلم أولاً
        if ($selectedTeacherId) {
            $statsBaseQuery->where('teacher_id', $selectedTeacherId);
        }

        $isGuardian = $user->can('view own subscriptions') && !$user->can('view subscriptions') && $user->hasRole('guardian');

        if ($isGuardian) {
            $statsBaseQuery->whereIn('student_id', $user->students()->pluck('id'));
        } elseif (!$user->hasRole(['admin', 'general_manager'])) {
            // مشرف/معلم/مدير فرع — سيُضيَّق لاحقاً بـ relevantStudentIds بدل circle_id
            // applyCircleFilter هنا مؤقت كحد أدنى للأمان، يُستبدل أدناه عند حساب relevantStudentIds
            $this->applyCircleFilter($statsBaseQuery, $user, $circleIds);
        } else {
            $this->applyCircleFilter($statsBaseQuery, $user, $circleIds);
        }

        if ($selectedCircleId) {
            $statsBaseQuery->where('circle_id', $selectedCircleId);
        }

        if ($selectedCenterId) {
            $statsBaseQuery->whereHas('circle', function ($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId);
            });
        }

        $monthlyRevenue          = collect();
        $monthlyPaymentStats     = collect();
        $monthlyCollected        = 0;
        $paymentRate             = 0;
        $paidAndExemptRate       = 0;
        $unpaidAmount            = 0;
        $dueMonthRevenue         = 0;
        $paidMonthCount          = 0;
        $collectedForOtherMonths = 0;
        $paidOrExemptCount       = 0;
        $totalActiveStudents     = 0;
        $paidOnlyCount           = 0;
        $exemptOnlyCount         = 0;

        // ✅ هل نطاق المستخدم محدود بحلقات (غير ولي أمر، وغير admin/general_manager)؟
        $isScopedByCircles = !$isGuardian && !$user->hasRole(['admin', 'general_manager']);

        if ($user->can('view subscriptions')) {

            // ─── حسابات البطاقات (لكل من عنده view subscriptions) ───

            $studentsQuery = Student::withoutGlobalScopes()
                ->where('status', 'مقيد');

            if ($isGuardian) {
                $studentsQuery->where('guardian_id', $user->id);
            } elseif ($isScopedByCircles) {
                $relevantStudentIds = \App\Models\CircleAssignmentHistory::studentIdsInCirclesAt($circleIds, $monthStart);
                $studentsQuery->whereIn('id', $relevantStudentIds);
                $statsBaseQuery->whereIn('student_id', $relevantStudentIds);
            } else {
                // admin / general_manager
                $studentsQuery->whereIn('circle_id', $circleIds);
            }

            if ($selectedCircleId) {
                $studentsQuery->where('circle_id', $selectedCircleId);
            }

            if ($selectedCenterId) {
                $studentsQuery->whereHas('circle', function ($q) use ($selectedCenterId) {
                    $q->where('center_id', $selectedCenterId);
                });
            }

            if ($selectedTeacherId) {
                $studentsQuery->whereHas('circle.teachers', function ($q) use ($selectedTeacherId) {
                    $q->whereHas('user', function ($uq) use ($selectedTeacherId) {
                        $uq->where('id', $selectedTeacherId);
                    });
                });
            }

            $collectedForOtherMonths = (clone $statsBaseQuery)
                ->where('month', '!=', $monthStart)
                ->where('status', 'مدفوع')
                ->whereRaw("DATE_FORMAT(paid_at, '%Y-%m') = ?", [$statsMonth])
                ->sum('amount');

            $dueMonthRevenue = (clone $statsBaseQuery)
                ->where('month', $monthStart)
                ->where('status', 'مدفوع')
                ->sum('amount');

            $paidMonthCount = (clone $statsBaseQuery)
                ->where('status', 'مدفوع')
                ->whereNotNull('paid_at')
                ->whereRaw("DATE_FORMAT(paid_at, '%Y-%m') = ?", [$statsMonth])
                ->count();

            $monthlyCollected = $dueMonthRevenue + $collectedForOtherMonths;

            $paidOnlyCount = (clone $statsBaseQuery)
                ->where('month', $monthStart)
                ->where('status', 'مدفوع')
                ->count();

            $exemptOnlyCount = (clone $statsBaseQuery)
                ->where('month', $monthStart)
                ->where('status', 'معفي')
                ->count();

            $paidOrExemptCount = $paidOnlyCount + $exemptOnlyCount;
            $totalActiveStudents = (clone $studentsQuery)->count();

            $paymentRate = $totalActiveStudents > 0
                ? round(($paidOnlyCount / $totalActiveStudents) * 100, 1)
                : 0;

            $paidAndExemptRate = $totalActiveStudents > 0
                ? round(($paidOrExemptCount / $totalActiveStudents) * 100, 1)
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

            // ─── حسابات الـ Charts (لمن عنده view subscriptions chart فقط) ───

            if ($user->can('view subscriptions chart')) {

                $monthlyRevenue = (clone $statsBaseQuery)
                    ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month_label, SUM(amount) as total")
                    ->where('status', 'مدفوع')
                    ->whereNotNull('paid_at')
                    ->groupBy('month_label')
                    ->orderBy('month_label', 'desc')
                    ->take(6)
                    ->get()
                    ->sortBy('month_label')
                    ->values();

                $monthlyPaymentStats = collect();
                $last6MonthsStarts = collect(range(5, 0))->map(
                    fn($i) => Carbon::parse($monthStart)->subMonths($i)->startOfMonth()
                );

                $paidCountsByMonth = (clone $statsBaseQuery)
                    ->whereIn('month', $last6MonthsStarts->map(fn($d) => $d->format('Y-m-d')))
                    ->where('status', 'مدفوع')
                    ->selectRaw("DATE_FORMAT(month, '%Y-%m') as month_label, COUNT(*) as cnt")
                    ->groupBy('month_label')
                    ->pluck('cnt', 'month_label');

                $exemptCountsByMonth = (clone $statsBaseQuery)
                    ->whereIn('month', $last6MonthsStarts->map(fn($d) => $d->format('Y-m-d')))
                    ->where('status', 'معفي')
                    ->selectRaw("DATE_FORMAT(month, '%Y-%m') as month_label, COUNT(*) as cnt")
                    ->groupBy('month_label')
                    ->pluck('cnt', 'month_label');

                $studentsForStatsQuery = Student::withoutGlobalScopes()
                    ->where('status', 'مقيد')
                    ->select('id', 'join_date', 'created_at');

                if ($isGuardian) {
                    $studentsForStatsQuery->where('guardian_id', $user->id);
                } elseif (!$isScopedByCircles) {
                    // admin / general_manager — تقييد ثابت بالحلقات (لا انتقالات تُحسب)
                    $studentsForStatsQuery->whereIn('circle_id', $circleIds);
                }
                // ✅ لو $isScopedByCircles=true: لا نقيّد هنا، الفلترة التاريخية تتم لكل شهر أدناه

                if ($selectedCircleId) {
                    $studentsForStatsQuery->where('circle_id', $selectedCircleId);
                }

                if ($selectedCenterId) {
                    $studentsForStatsQuery->whereHas('circle', function ($q) use ($selectedCenterId) {
                        $q->where('center_id', $selectedCenterId);
                    });
                }

                if ($selectedTeacherId) {
                    $studentsForStatsQuery->whereHas('circle.teachers', function ($q) use ($selectedTeacherId) {
                        $q->whereHas('user', function ($uq) use ($selectedTeacherId) {
                            $uq->where('id', $selectedTeacherId);
                        });
                    });
                }

                $studentsForStats = $studentsForStatsQuery->get();

                // ✅ استعلام واحد فقط يجلب معرفات الطلاب لكل الأشهر الستة دفعة واحدة
                // (بدل 6 استعلامات منفصلة) — يُحسب فقط لو المستخدم محدود بحلقات
                $historicalStudentIdsByMonth = $isScopedByCircles
                    ? \App\Models\CircleAssignmentHistory::studentIdsInCirclesAtMultipleDates($circleIds, $last6MonthsStarts)
                    : [];

                foreach ($last6MonthsStarts as $monthDate) {
                    $monthLabel  = $monthDate->format('Y-m');
                    $monthEndCut = $monthDate->copy()->endOfMonth();

                    $totalCount = $studentsForStats->filter(function ($student) use ($monthEndCut, $monthLabel, $historicalStudentIdsByMonth, $isScopedByCircles) {
                        $joinDate = $student->join_date ?? $student->created_at;
                        $joinedBeforeCutoff = $joinDate <= $monthEndCut;

                        if ($isScopedByCircles) {
                            return $joinedBeforeCutoff
                                && in_array($student->id, $historicalStudentIdsByMonth[$monthLabel] ?? []);
                        }

                        return $joinedBeforeCutoff;
                    })->count();

                    $paidCount   = $paidCountsByMonth[$monthLabel] ?? 0;
                    $exemptCount = $exemptCountsByMonth[$monthLabel] ?? 0;

                    $monthlyPaymentStats->push([
                        'month_label'  => $monthLabel,
                        'paid_count'   => $paidCount,
                        'exempt_count' => $exemptCount,
                        'unpaid_count' => max(0, $totalCount - $paidCount - $exemptCount),
                        'total_count'  => $totalCount,
                        'rate'         => $totalCount > 0 ? round(($paidCount / $totalCount) * 100, 1) : 0,
                    ]);
                }
            }
        }

        // ✅ سجل الاشتراكات - تطبيق فلتر الشهر بشكل صحيح
        $recentSubscriptionsQuery = (clone $statsBaseQuery)
            ->with([
                'student' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'circle.center',
                'collectedBy.roles',
                'teacher',
            ]);
        if ($request->filled('month')) {
            $recentSubscriptionsQuery->where('month', $monthStart);
        }
        if ($search && $user->can('view subscriptions')) {
            $recentSubscriptionsQuery->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($selectedStatus) {
            $recentSubscriptionsQuery->where('status', $selectedStatus);
        }

        // ✅ الترتيب
        $sort      = $request->get('sort', 'paid_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        switch ($sort) {
            case 'student':
                $recentSubscriptionsQuery
                    ->join('students', 'students.id', '=', 'subscriptions.student_id')
                    ->orderBy('students.name', $direction)
                    ->select('subscriptions.*');
                break;

            case 'circle':
                $recentSubscriptionsQuery
                    ->join('circles', 'circles.id', '=', 'subscriptions.circle_id')
                    ->orderBy('circles.name', $direction)
                    ->select('subscriptions.*');
                break;

            case 'center':
                $recentSubscriptionsQuery
                    ->join('circles', 'circles.id', '=', 'subscriptions.circle_id')
                    ->join('centers', 'centers.id', '=', 'circles.center_id')
                    ->orderBy('centers.name', $direction)
                    ->select('subscriptions.*');
                break;

            case 'month':
                $recentSubscriptionsQuery->orderBy('month', $direction);
                break;

            case 'amount':
                $recentSubscriptionsQuery->orderBy('amount', $direction);
                break;

            case 'status':
                $recentSubscriptionsQuery->orderBy('status', $direction);
                break;

            case 'payment_method':
                $recentSubscriptionsQuery->orderBy('payment_method', $direction);
                break;

            case 'paid_at':
                $recentSubscriptionsQuery->orderBy('paid_at', $direction);
                break;

            default:
                $recentSubscriptionsQuery->orderByRaw('COALESCE(paid_at, created_at) ' . $direction);
        }

        $recentSubscriptions = $recentSubscriptionsQuery
            ->paginate(20)
            ->withQueryString();

        $centers = collect();
        if ($user->can('view subscriptions')) {
            $centers = Center::orderBy('name')->get(['id', 'name']);
        } else {
            $centerIds = $circles->pluck('center_id')->unique()->filter();
            $centers = Center::whereIn('id', $centerIds)->orderBy('name')->get(['id', 'name']);
        }

        // ✅ التحقق مما إذا كانت هناك فلاتر نشطة لإظهار/إخفاء زر إعادة التعيين
        $hasActiveFilters = $request->anyFilled(['center_id', 'circle_id', 'status', 'teacher_id', 'search'])
            || ($request->filled('month') && $request->get('month') !== now()->format('Y-m'));

        return view('subscription.index', compact(
            'monthlyRevenue',
            'monthlyPaymentStats',
            'monthlyCollected',
            'recentSubscriptions',
            'circles',
            'selectedCircleId',
            'selectedMonth',
            'paymentRate',
            'paidAndExemptRate',
            'unpaidAmount',
            'search',
            'selectedStatus',
            'centers',
            'selectedCenterId',
            'teachers',
            'selectedTeacherId',
            'dueMonthRevenue',
            'paidMonthCount',
            'collectedForOtherMonths',
            'hasActiveFilters',
            'paidOrExemptCount',
            'totalActiveStudents',
            'paidOnlyCount',
            'exemptOnlyCount',
            'statsMonth',
            'sort',
            'direction',
        ));
    }
    public function create()
    {
        $this->authorize('create subscriptions');

        $user      = Auth::user();
        $circles   = $this->getAccessibleCircles($user);
        $circleIds = $circles->pluck('id');

        $students = Student::with(['circle', 'subscriptions'])
            ->where('status', 'مقيد')
            ->whereIn('circle_id', $circleIds)
            ->get();

        $prices = \App\Models\SubscriptionPrice::all();

        $teachers = collect();
        if ($user->hasRole(['admin', 'general_manager'])) {
            $teachers = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['supervisor', 'manager', 'general_manager', 'teacher']);
            })->with('roles')->get(['id', 'name']);
        }

        return view('subscription.create', compact(
            'circles',
            'students',
            'prices',
            'teachers'
        ));
    }

    public function store(StoreSubscriptionRequest $request)
    {
        $this->authorize('create subscriptions');

        $validated = $request->validated();
        $user      = Auth::user();
        $month     = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()->format('Y-m-d');
        $isExempt  = $validated['status'] === 'معفي';
        $isUnpaid  = $validated['status'] === 'غير مدفوع';

        $circleIds = $this->getAccessibleCircleIds($user);
        if (!$circleIds->contains($validated['circle_id'])) {
            abort(403, 'ليس لديك صلاحية لإضافة اشتراك لهذه الحلقة.');
        }

        $teacherId = $user->hasRole(['admin', 'general_manager'])
            ? ($validated['teacher_id'] ?? $user->id)
            : $user->id;

        $data = [
            'student_id'     => $validated['student_id'],
            'circle_id'      => $validated['circle_id'],
            'teacher_id'     => $teacherId,
            'month'          => $month,
            'status'         => $validated['status'],
            'amount'         => ($isExempt || $isUnpaid) ? 0 : $validated['amount'],
            'payment_method' => ($isExempt || $isUnpaid) ? null : ($validated['payment_method'] ?? null),
            'paid_at'      => $validated['status'] === 'مدفوع' ? now() : null,
            'collected_by' => $validated['status'] === 'مدفوع' ? ($user->hasRole('admin') ? $teacherId : $user->id) : null,
            'notes'          => $validated['notes'] ?? null,
        ];
        Subscription::create($data);

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم تسجيل الاشتراك بنجاح');
    }

    // ─── 2. lateAndUnpaid() - الدالة كاملة ──────────────────────────────
    public function lateAndUnpaid(Request $request)
    {
        if (!Auth::user()->can('view subscriptions')) {
            abort(403);
        }

        $user              = Auth::user();
        $selectedCircleId  = $request->get('circle_id');
        $selectedCenterId  = $request->get('center_id');
        $selectedTeacherId = $request->get('teacher_id');
        $search            = $request->get('search');
        $selectedStatus    = $request->get('status');

        $circles   = $this->getAccessibleCircles($user);
        $circleIds = $circles->pluck('id');

        if ($selectedCircleId && $selectedCircleId !== 'all' && !$circleIds->contains($selectedCircleId)) {
            abort(403, 'ليس لديك صلاحية لعرض هذه الحلقة.');
        }

        $query = Student::whereIn('status', ['مقيد', 'متوقف'])
            ->with(['circle.center']);

        $this->applyCircleFilter($query, $user, $circleIds);

        if ($selectedCircleId && $selectedCircleId !== 'all') {
            $query->where('circle_id', $selectedCircleId);
        }

        if ($selectedCenterId) {
            $query->whereHas('circle', function ($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId);
            });
        }

        if ($selectedTeacherId) {
            $query->whereHas('circle.teachers', function ($q) use ($selectedTeacherId) {
                $q->whereHas('user', function ($uq) use ($selectedTeacherId) {
                    $uq->where('id', $selectedTeacherId);
                });
            });
        }

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        $students   = $query->get();
        $studentIds = $students->pluck('id');

        // ✅ يحسب المدفوعين والمعفيين معاً
        $paidCountPerStudent = Subscription::query()
            ->whereIn('status', ['مدفوع', 'معفي'])
            ->whereIn('student_id', $studentIds)
            ->selectRaw('student_id, COUNT(DISTINCT DATE_FORMAT(month, "%Y-%m")) as paid_count')
            ->groupBy('student_id')
            ->pluck('paid_count', 'student_id');

        $sort      = $request->get('sort', 'unpaid_months');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $students = $students->map(function ($student) use ($paidCountPerStudent) {
            $startDate = $student->join_date
                ? $student->join_date->copy()->startOfMonth()
                : $student->created_at->copy()->startOfMonth();

            $endDate = ($student->status === 'متوقف')
                ? ($student->suspended_at
                    ? $student->suspended_at->copy()->startOfMonth()
                    : $student->updated_at->copy()->startOfMonth())
                : now()->startOfMonth();

            $expectedMonths = $startDate->diffInMonths($endDate) + 1;
            $paidMonths     = $paidCountPerStudent[$student->id] ?? 0;

            $student->unpaid_months_count = max(0, $expectedMonths - $paidMonths);
            $student->unpaid_months_list  = [];
            return $student;
        })->filter(fn($s) => $s->unpaid_months_count > 0);

        $students = match ($sort) {
            'name'   => $direction === 'asc' ? $students->sortBy('name')                              : $students->sortByDesc('name'),
            'status' => $direction === 'asc' ? $students->sortBy('status')                            : $students->sortByDesc('status'),
            'circle' => $direction === 'asc' ? $students->sortBy(fn($s) => $s->circle?->name)        : $students->sortByDesc(fn($s) => $s->circle?->name),
            'center' => $direction === 'asc' ? $students->sortBy(fn($s) => $s->circle?->center?->name) : $students->sortByDesc(fn($s) => $s->circle?->center?->name),
            default  => $direction === 'asc' ? $students->sortBy('unpaid_months_count')               : $students->sortByDesc('unpaid_months_count'),
        };

        $students = $students->values();
        $totalStudents     = $students->count();
        $totalUnpaidMonths = $students->sum('unpaid_months_count');
        $perPage = 30;
        $currentPage = request()->get('page', 1);
        $students = new \Illuminate\Pagination\LengthAwarePaginator(
            $students->forPage($currentPage, $perPage),
            $students->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        $centers = collect();
        if ($user->can('view subscriptions')) {
            $centers = Center::orderBy('name')->get(['id', 'name']);
        } else {
            $centerIds = $circles->pluck('center_id')->unique()->filter();
            $centers   = Center::whereIn('id', $centerIds)->orderBy('name')->get(['id', 'name']);
        }

        $teachers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['supervisor', 'manager', 'general_manager', 'teacher']);
        })->orderBy('name')->get(['id', 'name']);

        $hasActiveFilters = $request->anyFilled(['center_id', 'teacher_id', 'search', 'status'])
            || ($selectedCircleId && $selectedCircleId !== 'all');

        return view('subscription.late_and_unpaid', compact(
            'students',
            'circles',
            'centers',
            'teachers',
            'selectedCircleId',
            'selectedCenterId',
            'selectedTeacherId',
            'selectedStatus',
            'search',
            'totalUnpaidMonths',
            'hasActiveFilters',
            'sort',
            'direction',
            'totalStudents',
        ));
    }

    public function lateDetail(Student $student)
    {
        if (!Auth::user()->can('view subscriptions')) {
            abort(403);
        }

        $startDate = $student->join_date
            ? $student->join_date->copy()->startOfMonth()
            : $student->created_at->copy()->startOfMonth();

        $paidMonths = $student->subscriptions()
            ->where('status', 'مدفوع')
            ->pluck('month')
            ->map(fn($d) => $d->format('Y-m'))
            ->toArray();

        $unpaid = [];
        $check  = $startDate->copy();

        while ($check->lte(now()->startOfMonth())) {
            if (!in_array($check->format('Y-m'), $paidMonths)) {
                $unpaid[] = $check->locale('ar')->monthName . ' ' . $check->format('Y');
            }
            $check->addMonth();
        }

        return response()->json([
            'student' => $student->name,
            'months'  => $unpaid,
            'count'   => count($unpaid),
        ]);
    }

    public function DetailsUnpaid(Student $student)
    {
        if (!Auth::user()->can('view subscriptions')) {
            abort(403);
        }

        $startDate = $student->join_date
            ? $student->join_date->copy()->startOfMonth()
            : $student->created_at->copy()->startOfMonth();

        $subscriptions = $student->subscriptions()
            ->select('month', 'status', 'paid_at')
            ->get();

        $paidMonths = $subscriptions
            ->where('status', 'مدفوع')
            ->pluck('month')
            ->map(fn($d) => $d->format('Y-m'))
            ->toArray();

        $exemptMonths = $subscriptions
            ->where('status', 'معفي')
            ->pluck('month')
            ->map(fn($d) => $d->format('Y-m'))
            ->toArray();

        $timeline = [];
        $check    = $startDate->copy();

        while ($check->lte(now()->startOfMonth())) {
            $monthStr    = $check->format('Y-m');
            $isPaid      = in_array($monthStr, $paidMonths);
            $isExempt    = in_array($monthStr, $exemptMonths);

            $timeline[]  = [
                'month_str'    => $monthStr,
                'month_label'  => $check->locale('ar')->monthName . ' ' . $check->format('Y'),
                'is_paid'      => $isPaid,
                'is_exempt'    => $isExempt,
                'is_unpaid'    => !$isPaid && !$isExempt,
                'days_overdue' => !$isPaid && !$isExempt ? now()->diffInDays($check->endOfMonth()) : 0,
            ];
            $check->addMonth();
        }

        // ترتيب من الأحدث للأقدم
        $timeline = array_reverse($timeline);

        // بيانات الإحصائيات
        $totalMonths = count($timeline);
        $paidCount = collect($timeline)->where('is_paid', true)->count();
        $exemptCount = collect($timeline)->where('is_exempt', true)->count();
        $unpaidCount = collect($timeline)->where('is_unpaid', true)->count();

        // بيانات الـ Chart
        $chartData = [
            'labels' => ['مدفوع', 'معفي', 'غير مدفوع'],
            'data' => [$paidCount, $exemptCount, $unpaidCount],
            'colors' => ['#10b981', '#3b82f6', '#ef4444'],
        ];

        return view('subscription.details_unpaid', compact(
            'student',
            'timeline',
            'chartData',
            'totalMonths',
            'paidCount',
            'exemptCount',
            'unpaidCount'
        ));
    }

    public function edit(Subscription $subscription)
    {
        $this->authorize('edit subscriptions');

        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);
        if (!$circleIds->contains($subscription->circle_id)) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا الاشتراك.');
        }

        $circles  = $this->getAccessibleCircles($user);
        // في SubscriptionController - edit()
        $students = Student::with(['circle', 'subscriptions'])
            ->where(function ($q) use ($subscription, $circles) {
                $q->where('status', 'مقيد')
                    ->whereIn('circle_id', $circles->pluck('id'));
            })
            ->orWhere('id', $subscription->student_id) // ✅ الطالب الحالي دايماً
            ->get();
        $prices = \App\Models\SubscriptionPrice::all();

        // ✅ أضف هذا الجزء - جلب المعلمين للأدمن
        $teachers = collect();
        if ($user->hasRole(['admin', 'general_manager'])) {
            $teachers = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['supervisor', 'manager', 'general_manager', 'teacher']);
            })->with('roles')->get(['id', 'name']);
        }

        return view('subscription.edit', compact('subscription', 'circles', 'students', 'prices', 'teachers')); // ✅ أضف 'teachers'
    }


    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        $this->authorize('edit subscriptions');

        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);
        if (!$circleIds->contains($subscription->circle_id)) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا الاشتراك.');
        }

        $validated = $request->validated();

        if (!$circleIds->contains($validated['circle_id'])) {
            abort(403, 'ليس لديك صلاحية لنقل الاشتراك لهذه الحلقة.');
        }

        $month    = Carbon::createFromFormat('Y-m', $validated['month'])
            ->startOfMonth()->format('Y-m-d');
        $isExempt = $validated['status'] === 'معفي';
        $isUnpaid = $validated['status'] === 'غير مدفوع';

        $teacherId = $user->hasRole(['admin', 'general_manager'])
            ? ($validated['teacher_id'] ?? $subscription->teacher_id)
            : $subscription->teacher_id;

        $data = [
            'student_id'     => $validated['student_id'],
            'circle_id'      => $validated['circle_id'],
            'teacher_id'     => $teacherId,
            'month'          => $month,
            'status'         => $validated['status'],
            'amount'         => ($isExempt || $isUnpaid) ? 0 : ($validated['amount'] ?? $subscription->amount),
            'payment_method' => ($isExempt || $isUnpaid) ? null : ($validated['payment_method'] ?? null),
            'paid_at'        => $validated['status'] === 'مدفوع' ? ($subscription->paid_at ?? now()) : null,
            'collected_by'   => $validated['status'] === 'مدفوع' ? ($subscription->collected_by ?? Auth::id()) : null,
            'notes'          => $validated['notes'] ?? null,
        ];

        $subscription->update($data);

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    public function destroy(Subscription $subscription)
    {
        $this->authorize('delete subscriptions');

        $user      = Auth::user();
        $circleIds = $this->getAccessibleCircleIds($user);
        if (!$circleIds->contains($subscription->circle_id)) {
            abort(403, 'ليس لديك صلاحية لحذف هذا الاشتراك.');
        }

        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'تم حذف الاشتراك بنجاح');
    }
    public function getFilterOptions(Request $request)
    {
        if (!Auth::user()->canAny(['view subscriptions', 'view own subscriptions'])) {
            abort(403);
        }

        $user      = Auth::user();
        $centerId  = $request->get('center_id');
        $circleId  = $request->get('circle_id');
        $teacherId = $request->get('teacher_id');

        $circles   = $this->getAccessibleCircles($user);
        $circleIds = $circles->pluck('id');

        // ─── فلتر الحلقات ───────────────────────────────────────
        if ($user->hasRole(['admin', 'general_manager'])) {
            $circlesQuery = Circle::orderBy('name');
        } else {
            $circlesQuery = Circle::whereIn('id', $circleIds)->orderBy('name');
        }

        if ($centerId) {
            $circlesQuery->where('center_id', $centerId);
        }

        if ($teacherId) {
            $teacher = \App\Models\Teacher::where('user_id', $teacherId)->first();
            if ($teacher) {
                $teacherCircleIds = \DB::table('circle_teacher')
                    ->where('teacher_id', $teacher->id)
                    ->pluck('circle_id');
                $circlesQuery->whereIn('id', $teacherCircleIds);
            }
        }

        // ─── فلتر المعلمين ──────────────────────────────────────
        $teachersQuery = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['supervisor', 'manager', 'general_manager', 'teacher']);
        })->orderBy('name');

        if ($centerId) {
            $teachersQuery->whereHas('teacher', fn($q) => $q->where('center_id', $centerId));
        }

        if ($circleId) {
            $teachersQuery->whereHas('teacher', function ($q) use ($circleId) {
                $q->whereHas('circles', fn($cq) => $cq->where('circles.id', $circleId));
            });
        }

        // ─── فلتر الفروع ────────────────────────────────────────
        $centersQuery = Center::orderBy('name');

        if ($circleId) {
            $circle = Circle::find($circleId);
            if ($circle) {
                $centersQuery->where('id', $circle->center_id);
            }
        }

        if ($teacherId) {
            $teacher = $teacher ?? \App\Models\Teacher::where('user_id', $teacherId)->first();
            if ($teacher) {
                $centersQuery->where('id', $teacher->center_id);
            }
        }

        return response()->json([
            'circles'  => $circlesQuery->get(['id', 'name']),
            'teachers' => $teachersQuery->get(['id', 'name']),
            'centers'  => $centersQuery->get(['id', 'name']),
        ]);
    }
}
