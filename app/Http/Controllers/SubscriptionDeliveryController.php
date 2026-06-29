<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Subscription;
use App\Models\SubscriptionDelivery;
use App\Models\User;
use App\Models\Center;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionDeliveryController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));
        $monthDate = Carbon::parse($month);

        $circles = $this->getTeacherCircles($user);

        $deliveredCircleIds = SubscriptionDelivery::whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->pluck('circle_id')
            ->toArray();

        $circles = $circles->reject(function ($circle) use ($deliveredCircleIds) {
            return in_array($circle->id, $deliveredCircleIds);
        });

        $circlesWithTeachers = Circle::with([
            'mainTeachers.user',
            'assistantTeachers.user',
            'supervisors.user'
        ])
            ->when(!$user->hasRole(['admin', 'general_manager']), function ($q) use ($deliveredCircleIds) {
                $q->whereNotIn('id', $deliveredCircleIds);
            })
            ->get()
            ->map(function ($circle) use ($monthDate, $deliveredCircleIds) {

                $allTeachers = collect();

                foreach ($circle->mainTeachers as $teacher) {
                    if ($teacher->user) {
                        $allTeachers->push([
                            'id'   => $teacher->user_id,
                            'name' => $teacher->user->name . ' (معلم رئيسي)',
                            'role' => 'main'
                        ]);
                    }
                }

                foreach ($circle->assistantTeachers as $teacher) {
                    if ($teacher->user) {
                        $allTeachers->push([
                            'id'   => $teacher->user_id,
                            'name' => $teacher->user->name . ' (معلم مساعد)',
                            'role' => 'assistant'
                        ]);
                    }
                }

                $supervisors = collect();
                foreach ($circle->supervisors as $supervisor) {
                    if ($supervisor->user) {
                        $supervisors->push([
                            'id'   => $supervisor->user_id,
                            'name' => $supervisor->user->name,
                        ]);
                    }
                }

                $circleTotal = Subscription::where('circle_id', $circle->id)
                    ->whereYear('month', $monthDate->year)
                    ->whereMonth('month', $monthDate->month)
                    ->sum('amount');

                $lastDelivery = SubscriptionDelivery::where('circle_id', $circle->id)
                    ->whereYear('month', $monthDate->year)
                    ->whereMonth('month', $monthDate->month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $adminCollected = $lastDelivery?->admin_collected_amount ?? 0;
                $expectedFromTeacher = max(0, $circleTotal - $adminCollected);

                return [
                    'id'                    => $circle->id,
                    'name'                  => $circle->name,
                    'teachers'              => $allTeachers->toArray(),
                    'supervisors'           => $supervisors->toArray(),
                    'default_supervisor_id' => $supervisors->first()['id'] ?? null,
                    'circle_total'          => (float) $circleTotal,
                    'admin_collected'       => (float) $adminCollected,
                    'expected_from_teacher' => (float) $expectedFromTeacher,
                ];
            });

        $teachersWithCircles = collect();
        foreach ($circlesWithTeachers as $circle) {
            foreach ($circle['teachers'] as $teacher) {
                $existingIndex = $teachersWithCircles->search(fn($t) => $t['id'] == $teacher['id']);

                $circleData = [
                    'id'                    => $circle['id'],
                    'name'                  => $circle['name'],
                    'circle_total'          => $circle['circle_total'],
                    'expected_from_teacher' => $circle['expected_from_teacher'],
                    'supervisors'           => $circle['supervisors'],
                    'default_supervisor_id' => $circle['default_supervisor_id'],
                ];

                if ($existingIndex !== false) {
                    $existing = $teachersWithCircles[$existingIndex];
                    $existing['circles'][] = $circleData;
                    $teachersWithCircles[$existingIndex] = $existing;
                } else {
                    $teachersWithCircles->push([
                        'id'      => $teacher['id'],
                        'name'    => $teacher['name'],
                        'circles' => [$circleData],
                    ]);
                }
            }
        }

        $supervisors = collect();
        if ($user->hasRole(['admin', 'general_manager'])) {
            $supervisors = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['supervisor', 'manager', 'general_manager']);
            })->get(['id', 'name']);
        }

        return view('subscription_deliveries.create', compact(
            'circles',
            'circlesWithTeachers',
            'teachersWithCircles',
            'supervisors',
            'month'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'circle_id'            => 'required|exists:circles,id',
            'teacher_id'           => 'required|exists:users,id',
            'supervisor_id'        => 'nullable|exists:users,id',
            'month'                => 'required|date_format:Y-m',
            'expected_from_teacher' => 'nullable|numeric|min:0',
            'delivered_by_teacher' => 'required|numeric|min:0',
            'notes'                => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $monthDate = Carbon::parse($validated['month']);

        // ✅ التحقق: هل تم تسليم هذه الحلقة في هذا الشهر؟
        $existingDelivery = SubscriptionDelivery::where('circle_id', $validated['circle_id'])
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->first();

        if ($existingDelivery) {
            return back()->with('error', 'تم تسليم اشتراكات هذه الحلقة في هذا الشهر مسبقاً');
        }

        $circleTotal = Subscription::where('circle_id', $validated['circle_id'])
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->sum('amount');

        DB::transaction(function () use ($validated, $user, $circleTotal, $monthDate) {
            SubscriptionDelivery::create([
                'circle_id'             => $validated['circle_id'],
                'teacher_id'            => $validated['teacher_id'],
                'supervisor_id'         => $validated['supervisor_id'],
                'month'                 => $monthDate->format('Y-m-01'),
                'circle_total_amount'   => $circleTotal,
                'admin_collected_amount' => 0,
                'expected_from_teacher' => $validated['expected_from_teacher'] ?? $circleTotal,
                'delivered_by_teacher'  => $validated['delivered_by_teacher'],
                'confirmed_by_admin'    => false,
                'notes'                 => $validated['notes'] ?? null,
                'delivered_at'          => now(),
            ]);
        });

        return redirect()->route('subscription-deliveries.index')
            ->with('success', 'تم تسجيل التسليم بنجاح');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));

        $query = SubscriptionDelivery::with(['circle', 'teacher', 'supervisor', 'admin'])
            ->whereMonth('month', Carbon::parse($month)->month)
            ->whereYear('month', Carbon::parse($month)->year);

        if (!$user->hasRole(['admin', 'general_manager'])) {
            $circleIds = $this->getSupervisorCircleIds($user);
            $query->whereIn('circle_id', $circleIds);
        }

        if ($request->filled('circle_id')) {
            $query->where('circle_id', $request->circle_id);
        }

        if ($request->filled('center_id')) {
            $query->whereHas('circle', fn($q) => $q->where('center_id', $request->center_id));
        }

        $deliveries = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = $this->calculateStats($query, $month);

        $circles = $this->getFilterCircles($user);
        $centers = $this->getFilterCenters($user);

        return view('subscription_deliveries.index', compact(
            'deliveries',
            'stats',
            'month',
            'circles',
            'centers'
        ));
    }

    // ═══════════════════════════════════════════════════════
    // ✅ EDIT: تعديل تسليم موجود
    // ═══════════════════════════════════════════════════════
    public function edit(SubscriptionDelivery $delivery)
    {
        $this->authorize('update', $delivery);

        $user = Auth::user();
        $month = Carbon::parse($delivery->month)->format('Y-m');
        $monthDate = Carbon::parse($month);

        if ($delivery->confirmed_by_admin) {
            return redirect()->route('subscription-deliveries.index')
                ->with('error', 'لا يمكن تعديل تسليم مُعتمد من المدير');
        }

        $circles = $this->getTeacherCircles($user);

        $deliveredCircleIds = SubscriptionDelivery::whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->where('id', '!=', $delivery->id)
            ->pluck('circle_id')
            ->toArray();

        $circles = $circles->reject(function ($circle) use ($deliveredCircleIds) {
            return in_array($circle->id, $deliveredCircleIds);
        });

        $circles->push($delivery->circle);

        $circlesWithTeachers = Circle::with([
            'mainTeachers.user',
            'assistantTeachers.user',
            'supervisors.user'
        ])
            ->when(!$user->hasRole(['admin', 'general_manager']), function ($q) use ($deliveredCircleIds) {
                $q->whereNotIn('id', $deliveredCircleIds);
            })
            ->get()
            ->map(function ($circle) use ($monthDate, $deliveredCircleIds) {
                $allTeachers = collect();

                foreach ($circle->mainTeachers as $teacher) {
                    if ($teacher->user) {
                        $allTeachers->push([
                            'id'   => $teacher->user_id,
                            'name' => $teacher->user->name . ' (معلم رئيسي)',
                            'role' => 'main'
                        ]);
                    }
                }

                foreach ($circle->assistantTeachers as $teacher) {
                    if ($teacher->user) {
                        $allTeachers->push([
                            'id'   => $teacher->user_id,
                            'name' => $teacher->user->name . ' (معلم مساعد)',
                            'role' => 'assistant'
                        ]);
                    }
                }

                $supervisors = collect();
                foreach ($circle->supervisors as $supervisor) {
                    if ($supervisor->user) {
                        $supervisors->push([
                            'id'   => $supervisor->user_id,
                            'name' => $supervisor->user->name,
                        ]);
                    }
                }

                $circleTotal = Subscription::where('circle_id', $circle->id)
                    ->whereYear('month', $monthDate->year)
                    ->whereMonth('month', $monthDate->month)
                    ->sum('amount');

                $adminCollected = SubscriptionDelivery::where('circle_id', $circle->id)
                    ->whereYear('month', $monthDate->year)
                    ->whereMonth('month', $monthDate->month)
                    ->orderBy('created_at', 'desc')
                    ->value('admin_collected_amount') ?? 0;

                $expectedFromTeacher = max(0, $circleTotal - $adminCollected);

                return [
                    'id'                    => $circle->id,
                    'name'                  => $circle->name,
                    'teachers'              => $allTeachers->toArray(),
                    'supervisors'           => $supervisors->toArray(),
                    'default_supervisor_id' => $supervisors->first()['id'] ?? null,
                    'circle_total'          => (float) $circleTotal,
                    'admin_collected'       => (float) $adminCollected,
                    'expected_from_teacher' => (float) $expectedFromTeacher,
                ];
            });

        $teachersWithCircles = collect();
        foreach ($circlesWithTeachers as $circle) {
            foreach ($circle['teachers'] as $teacher) {
                $existingIndex = $teachersWithCircles->search(fn($t) => $t['id'] == $teacher['id']);

                $circleData = [
                    'id'                    => $circle['id'],
                    'name'                  => $circle['name'],
                    'circle_total'          => $circle['circle_total'],
                    'expected_from_teacher' => $circle['expected_from_teacher'],
                    'supervisors'           => $circle['supervisors'],
                    'default_supervisor_id' => $circle['default_supervisor_id'],
                ];

                if ($existingIndex !== false) {
                    $existing = $teachersWithCircles[$existingIndex];
                    $existing['circles'][] = $circleData;
                    $teachersWithCircles[$existingIndex] = $existing;
                } else {
                    $teachersWithCircles->push([
                        'id'      => $teacher['id'],
                        'name'    => $teacher['name'],
                        'circles' => [$circleData],
                    ]);
                }
            }
        }

        $supervisors = collect();
        if ($user->hasRole(['admin', 'general_manager'])) {
            $supervisors = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['supervisor', 'manager', 'general_manager']);
            })->get(['id', 'name']);
        }

        return view('subscription_deliveries.create', compact(
            'circles',
            'circlesWithTeachers',
            'teachersWithCircles',
            'supervisors',
            'month',
            'delivery'
        ));
    }

    // ═══════════════════════════════════════════════════════
    // ✅ UPDATE: تحديث تسليم موجود
    // ═══════════════════════════════════════════════════════
    public function update(Request $request, SubscriptionDelivery $delivery)
    {
        $this->authorize('update', $delivery);

        // ❌ لا يمكن تعديل تسليم مُعتمد
        if ($delivery->confirmed_by_admin) {
            return redirect()->route('subscription-deliveries.index')
                ->with('error', 'لا يمكن تعديل تسليم مُعتمد من المدير');
        }

        $validated = $request->validate([
            'circle_id'            => 'required|exists:circles,id',
            'teacher_id'           => 'required|exists:users,id',
            'supervisor_id'        => 'nullable|exists:users,id',
            'month'                => 'required|date_format:Y-m',
            'expected_from_teacher' => 'nullable|numeric|min:0',
            'delivered_by_teacher' => 'required|numeric|min:0',
            'notes'                => 'nullable|string|max:1000',
        ]);

        $monthDate = Carbon::parse($validated['month']);

        // ✅ التحقق: هل تم تسليم حلقة أخرى في هذا الشهر؟
        if ($validated['circle_id'] != $delivery->circle_id) {
            $existingDelivery = SubscriptionDelivery::where('circle_id', $validated['circle_id'])
                ->whereYear('month', $monthDate->year)
                ->whereMonth('month', $monthDate->month)
                ->where('id', '!=', $delivery->id)
                ->first();

            if ($existingDelivery) {
                return back()->with('error', 'تم تسليم اشتراكات هذه الحلقة في هذا الشهر مسبقاً');
            }
        }

        $circleTotal = Subscription::where('circle_id', $validated['circle_id'])
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->sum('amount');

        DB::transaction(function () use ($validated, $delivery, $circleTotal, $monthDate) {
            $delivery->update([
                'circle_id'             => $validated['circle_id'],
                'teacher_id'            => $validated['teacher_id'],
                'supervisor_id'         => $validated['supervisor_id'],
                'month'                 => $monthDate->format('Y-m-01'),
                'circle_total_amount'   => $circleTotal,
                'expected_from_teacher' => $validated['expected_from_teacher'] ?? $circleTotal,
                'delivered_by_teacher'  => $validated['delivered_by_teacher'],
                'notes'                 => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('subscription-deliveries.index')
            ->with('success', 'تم تحديث التسليم بنجاح');
    }

    // ═══════════════════════════════════════════════════════
    // ✅ DESTROY: حذف تسليم
    // ═══════════════════════════════════════════════════════
    public function destroy(SubscriptionDelivery $delivery)
    {
        $this->authorize('delete', $delivery);

        // ❌ لا يمكن حذف تسليم مُعتمد
        if ($delivery->confirmed_by_admin) {
            return redirect()->route('subscription-deliveries.index')
                ->with('error', 'لا يمكن حذف تسليم مُعتمد من المدير');
        }

        $delivery->delete();

        return redirect()->route('subscription-deliveries.index')
            ->with('success', 'تم حذف التسليم بنجاح');
    }

    public function adminReview(SubscriptionDelivery $delivery)
    {
        $this->authorize('adminConfirm', $delivery);

        $user = Auth::user();
        $circle = $delivery->circle;

        $circle->load(['students.subscriptions']);

        $circleTotal = $circle->students()
            ->where('status', 'مقيد')
            ->sum('subscription_fees') ?? 0;

        $reviewers = collect();
        if ($user->hasRole('admin')) {
            $reviewers = \App\Models\User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['supervisor', 'manager', 'general_manager']);
            })->with('roles')->get(['id', 'name']);
        }

        return view('subscription_deliveries.admin_review', compact(
            'delivery',
            'circleTotal',
            'reviewers'
        ));
    }

    public function adminReviewUpdate(Request $request, SubscriptionDelivery $delivery)
    {
        $this->authorize('adminConfirm', $delivery);

        $user = Auth::user();

        $isAdmin = $user->hasRole('admin');
        $isReviewer = $user->hasRole(['supervisor', 'manager', 'general_manager']);

        if (!$isAdmin && !$isReviewer) {
            return back()->with('error', 'لا تملك صلاحية المراجعة');
        }

        $validated = $request->validate([
            'admin_id' => 'nullable|exists:users,id',
            'admin_collected_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'confirm' => 'required|in:0,1',
        ]);

        if ($isAdmin && !empty($validated['admin_id'])) {
            $adminId = $validated['admin_id'];
        } else {
            $adminId = $user->id;
        }

        $updateData = [
            'admin_id' => $adminId,
            'admin_collected_amount' => $validated['admin_collected_amount'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ];

        if ($validated['confirm'] == '1') {
            $updateData['confirmed_by_admin'] = true;
            $updateData['confirmed_at'] = now();
        }

        $delivery->update($updateData);
        $delivery->refresh();

        $message = $validated['confirm'] == '1'
            ? 'تم تأكيد واعتماد التسليم بنجاح'
            : 'تم حفظ البيانات بنجاح';

        return redirect()->route('subscription-deliveries.index')->with('success', $message);
    }

    private function getTeacherCircles(User $user)
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Circle::all();
        }

        $teacher = $user->teacher;
        return $teacher ? $teacher->circles : collect();
    }

    private function getSupervisorCircleIds(User $user)
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Circle::pluck('id');
        }

        $teacher = $user->teacher;
        return $teacher ? $teacher->supervisorCircles->pluck('id') : collect();
    }

    private function calculateStats($query, $month)
    {
        return [
            'total_deliveries' => (clone $query)->count(),
            'total_expected' => (clone $query)->sum('expected_from_teacher'),
            'total_delivered' => (clone $query)->sum('delivered_by_teacher'),
            'total_admin_collected' => (clone $query)->sum('admin_collected_amount'),
        ];
    }

    private function getFilterCircles(User $user)
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Circle::with('center')->orderBy('name')->get();
        }

        $teacher = $user->teacher;
        return $teacher ? $teacher->supervisorCircles()->orderBy('name')->get() : collect();
    }

    private function getFilterCenters(User $user)
    {
        if ($user->hasRole(['admin', 'general_manager'])) {
            return Center::orderBy('name')->get();
        }

        $teacher = $user->teacher;
        if (!$teacher) return collect();

        $centerIds = $teacher->supervisorCircles()->pluck('center_id')->unique();
        return Center::whereIn('id', $centerIds)->orderBy('name')->get();
    }
}
