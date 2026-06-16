<?php

namespace App\Http\Controllers;

use App\Http\Requests\Circle\CreateCircleRequest;
use App\Http\Requests\Circle\EditCircleRequest;
use App\Models\Circle;
use App\Models\Teacher;
use App\Traits\ResolvesUserScope;
use Illuminate\Support\Facades\Auth;

class CircleController extends Controller
{
    use ResolvesUserScope;

    // ─────────────────────────────────────────
    public function index()
    {
        $this->authorize('viewAny', Circle::class);

        $user    = Auth::user();
        $circles = $this->getAccessibleCirclesQuery($user)
            ->with([
                'mainTeacher',
                'assistantTeacher',
                'supervisor.user',
                'center',
            ])
            ->withCount('students')
            ->get();
        return view('circles.index', compact('circles'));
    }
    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Circle::class);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        // ✅ المشرف المقيد أو القائمة الكاملة
        if ($user->can('view all supervisors')) {
            $supervisors      = $this->getAccessibleSupervisors($teacher);
            $lockedSupervisor = null;
        } else {
            $supervisors      = $this->getAccessibleSupervisors($teacher);
            $lockedSupervisor = $teacher ? Teacher::with('user.roles')->find($teacher->id) : null;
        }

        return view('circles.create', [
            'circle'          => new Circle(),
            'teachers'        => $this->getAccessibleTeachers($teacher),
            'supervisors'     => $supervisors,
            'lockedSupervisor' => $lockedSupervisor,
            'centers' => $this->getAccessibleCenters($user),
            'canManageCenters' => $user->can('manage centers'),
        ]);
    }

    // ─────────────────────────────────────────
    public function store(CreateCircleRequest $request)
    {
        $this->authorize('create', Circle::class);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        // center_id — admin يختار، الباقي فرعه تلقائياً
        $centerId = $user->hasRole('admin')
            ? $request->center_id
            : $teacher?->center_id;

        $nameInput = trim($request->name);

        if (!str_starts_with($nameInput, 'حلقة')) {
            $finalName = 'حلقة ' . $nameInput;
        } else {
            $finalName = $nameInput;
        }

        $circle = Circle::create([
            'name'          => $finalName,
            'type'          => $request->type,
            'level'         => $request->level,
            'notes'         => $request->notes,
            'supervisor_id' => $request->supervisor_id ?? null,
            'center_id'     => $centerId,
            'is_active'     => true,
        ]);

        if ($request->teacher_id) {
            $circle->teachers()->attach($request->teacher_id, ['role' => 'main']);
        }
        if ($request->assistant_teacher_id) {
            $circle->teachers()->attach($request->assistant_teacher_id, ['role' => 'assistant']);
        }

        return redirect()->route('circles.index')->with('success', 'تم إنشاء الحلقة بنجاح');
    }

    // ─────────────────────────────────────────
    public function show(string $id)
    {
        $circle = Circle::with([
            'mainTeacher',
            'assistantTeacher',
            'supervisor',
            'students',
        ])->findOrFail($id);

        $this->authorize('view', $circle);

        return view('circles.show', compact('circle'));
    }

    // ─────────────────────────────────────────
    public function edit(string $id)
    {
        $circle = Circle::with(['mainTeacher', 'assistantTeacher', 'supervisor'])
            ->findOrFail($id);

        $this->authorize('update', $circle);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        if ($user->can('view all supervisors')) {
            $supervisors      = $this->getAccessibleSupervisors($teacher);
            $lockedSupervisor = null;
        } else {
            $supervisors      = $this->getAccessibleSupervisors($teacher);
            $lockedSupervisor = $teacher ? Teacher::with('user.roles')->find($teacher->id) : null;
        }

        return view('circles.edit', [
            'circle'          => $circle,
            'teachers'        => $this->getAccessibleTeachers($teacher),
            'supervisors'     => $supervisors,
            'lockedSupervisor' => $lockedSupervisor,
            'centers'         => $this->getAccessibleCenters($user),   // ✅
            'canManageCenters' => $user->can('manage centers'),         // ✅
        ]);
    }

    // ─────────────────────────────────────────
    public function update(EditCircleRequest $request, string $id)
    {
        $circle = Circle::findOrFail($id);
        $this->authorize('update', $circle);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        // center_id — admin يغير، الباقي فرعه بس
        $centerId = $user->hasRole('admin')
            ? $request->center_id
            : $teacher?->center_id ?? $circle->center_id;

        $nameInput = trim($request->name);

        if (!str_starts_with($nameInput, 'حلقة')) {
            $finalName = 'حلقة ' . $nameInput;
        } else {
            $finalName = $nameInput;
        }

        $circle->update([
            'name'          => $finalName,
            'type'          => $request->type,
            'level'         => $request->level,
            'notes'         => $request->notes ?? null,
            'supervisor_id' => $request->supervisor_id ?? null,
            'center_id'     => $centerId,
            'is_active'     => true,
        ]);

        $teachers = [];
        if ($request->teacher_id) {
            $teachers[$request->teacher_id] = ['role' => 'main'];
        }
        if ($request->assistant_teacher_id) {
            $teachers[$request->assistant_teacher_id] = ['role' => 'assistant'];
        }
        $circle->teachers()->sync($teachers);

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
}
