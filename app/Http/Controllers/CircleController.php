<?php

namespace App\Http\Controllers;

use App\Http\Requests\Circle\CreateCircleRequest;
use App\Http\Requests\Circle\EditCircleRequest;
use App\Models\Circle;
use App\Models\Teacher;
use App\Traits\ResolvesUserScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CircleController extends Controller
{
    use ResolvesUserScope;

    // ─────────────────────────────────────────
    // دالة index بعد التعديل 🚀
    public function index(Request $request)
    {
        $this->authorize('viewAny', Circle::class);

        $user = Auth::user();

        $query = $this->getAccessibleCirclesQuery($user)
            ->with([
                // تصفية علاقة المعلم الرئيسي لتتخطى الـ Global Scope لكي يظهر الاسم لمدير الفرع
                'mainTeacher' => fn($q) => $q->withoutGlobalScope(\App\Models\Scopes\CenterScope::class),

                // تصفية علاقة المعلم المساعد لتتخطى الـ Global Scope
                'assistantTeacher' => fn($q) => $q->withoutGlobalScope(\App\Models\Scopes\CenterScope::class),

                'supervisors.user',
                'center',
            ])
            ->withCount('students');

        $query->when($request->q, fn($q, $v) => $q->where('name', 'like', "%{$v}%"));
        $query->when($request->center_id, fn($q, $v) => $q->where('center_id', $v));
        $query->when($request->type, fn($q, $v) => $q->where('type', $v));
        $query->when($request->level, fn($q, $v) => $q->where('level', $v));

        $allowedSorts = ['name', 'type', 'level', 'students_count'];
        $sortField    = in_array($request->sort, $allowedSorts) ? $request->sort : 'name';
        $sortDir      = $request->dir === 'desc' ? 'desc' : 'asc';

        if ($sortField === 'students_count') {
            $query->reorder()->orderBy('students_count', $sortDir);
        } else {
            $query->reorder()->orderBy($sortField, $sortDir);
        }

        $circles = $query->paginate(20)->withQueryString();
        $centers = $this->getAccessibleCenters($user);

        return view('circles.index', compact('circles', 'centers'));
    }

    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Circle::class);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        if ($user->can('view all supervisors')) {
            $supervisors      = $this->getAccessibleSupervisors($user, $teacher);
            $lockedSupervisor = null;
        } else {
            $supervisors      = $this->getAccessibleSupervisors($user, $teacher);
            $lockedSupervisor = $teacher ? Teacher::with('user.roles')->find($teacher->id) : null;
        }

        return view('circles.create', [
            'circle'                => new Circle(),
            'teachers'              => $this->getAccessibleTeachers($user, $teacher),
            'supervisors'           => $supervisors,
            'lockedSupervisor'      => $lockedSupervisor,
            'centers'               => $this->getAccessibleCenters($user),
            'canManageCenters'      => $user->can('manage centers'),
            'selectedSupervisorIds' => [],
        ]);
    }

    // ─────────────────────────────────────────
    public function store(CreateCircleRequest $request)
    {
        $this->authorize('create', Circle::class);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        $centerId = $user->hasRole(['admin', 'general_manager'])
            ? $request->center_id
            : $teacher?->center_id;

        $circle = Circle::create([
            'name'      => $request->name,
            'type'      => $request->type,
            'level'     => $request->level,
            'center_id' => $centerId,
            'is_active' => true,
        ]);

        $this->syncCircleStaff($circle, $request);

        return redirect()->route('circles.index')->with('success', 'تم إنشاء الحلقة بنجاح');
    }

    // ─────────────────────────────────────────
    // دالة show بعد التعديل 🚀
    public function show(string $id)
    {
        $circle = Circle::with([
            'mainTeacher' => fn($q) => $q->withoutGlobalScope(\App\Models\Scopes\CenterScope::class),
            'assistantTeacher' => fn($q) => $q->withoutGlobalScope(\App\Models\Scopes\CenterScope::class),
            'supervisors' => fn($q) => $q->withoutGlobalScope(\App\Models\Scopes\CenterScope::class),
            'students',
        ])->findOrFail($id);

        $this->authorize('view', $circle);

        return view('circles.show', compact('circle'));
    }

    // ─────────────────────────────────────────
    public function edit(string $id)
    {
        $circle = Circle::with(['mainTeacher', 'assistantTeacher', 'supervisors'])
            ->findOrFail($id);

        $this->authorize('update', $circle);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        if ($user->can('view all supervisors')) {
            $supervisors      = $this->getAccessibleSupervisors($user, $teacher);
            $lockedSupervisor = null;
        } else {
            $supervisors      = $this->getAccessibleSupervisors($user, $teacher);
            $lockedSupervisor = $teacher ? Teacher::with('user.roles')->find($teacher->id) : null;
        }

        $selectedSupervisorIds = $circle->supervisors
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->all();

        return view('circles.edit', [
            'circle'                => $circle,
            'teachers'              => $this->getAccessibleTeachers($user, $teacher),
            'supervisors'           => $supervisors,
            'lockedSupervisor'      => $lockedSupervisor,
            'centers'               => $this->getAccessibleCenters($user),
            'canManageCenters'      => $user->can('manage centers'),
            'selectedSupervisorIds' => $selectedSupervisorIds,
        ]);
    }

    // ─────────────────────────────────────────
    public function update(EditCircleRequest $request, string $id)
    {
        $circle = Circle::findOrFail($id);
        $this->authorize('update', $circle);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        $centerId = $user->hasRole(['admin', 'general_manager'])
            ? $request->center_id
            : $teacher?->center_id ?? $circle->center_id;

        $circle->update([
            'name'      => $request->name,
            'type'      => $request->type,
            'level'     => $request->level,
            'center_id' => $centerId,
            'is_active' => true,
        ]);

        $this->syncCircleStaff($circle, $request);

        return redirect()->route('circles.index')->with('success', 'تم تحديث الحلقة بنجاح');
    }

    // ─────────────────────────────────────────
    public function destroy(string $id)
    {
        $circle = Circle::findOrFail($id);
        $this->authorize('delete', $circle);

        $circle->teachers()->detach();
        $circle->delete();

        return redirect()->route('circles.index')->with('success', 'تم حذف الحلقة بنجاح');
    }

    // ─────────────────────────────────────────
    // ✅ delete + insert يدوي بدل sync() لأن sync() لا يفهم role كجزء من
    //    المفتاح المركب (circle_id+teacher_id+role)، فيحاول تحديث صف موجود
    //    بدور مختلف ويتصادم مع صف آخر له نفس الدور الجديد.
    private function syncCircleStaff(Circle $circle, Request $request): void
    {
        // 1) المعلم الرئيسي/المساعد — حذف القديم ثم إدراج الجديد
        DB::table('circle_teacher')
            ->where('circle_id', $circle->id)
            ->whereIn('role', ['main', 'assistant'])
            ->delete();

        $rows = [];
        if ($request->teacher_id) {
            $rows[] = [
                'circle_id'  => $circle->id,
                'teacher_id' => (int) $request->teacher_id,
                'role'       => 'main',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if ($request->assistant_teacher_id) {
            $rows[] = [
                'circle_id'  => $circle->id,
                'teacher_id' => (int) $request->assistant_teacher_id,
                'role'       => 'assistant',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if ($rows) {
            DB::table('circle_teacher')->insert($rows);
        }

        // 2) المشرفون (متعددون) — حذف القديم ثم إدراج الجديد
        DB::table('circle_teacher')
            ->where('circle_id', $circle->id)
            ->where('role', 'supervisor')
            ->delete();

        $supervisorIds = array_unique(array_filter(
            (array) ($request->supervisor_ids ?? []),
            fn($id) => $id !== null && $id !== ''
        ));

        $supervisorRows = [];
        foreach ($supervisorIds as $supervisorId) {
            $supervisorRows[] = [
                'circle_id'  => $circle->id,
                'teacher_id' => (int) $supervisorId,
                'role'       => 'supervisor',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if ($supervisorRows) {
            DB::table('circle_teacher')->insert($supervisorRows);
        }
    }
}
