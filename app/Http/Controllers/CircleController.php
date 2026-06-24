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
    public function index(Request $request)
    {
        $this->authorize('viewAny', Circle::class);

        $user = Auth::user();

        $query = $this->getAccessibleCirclesQuery($user)
            ->with([
                'mainTeacher' => fn($q) => $q->withoutGlobalScope(\App\Models\Scopes\CenterScope::class),
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

        // ✅ التحقق من أن المركز متاح للمستخدم
        if (!$user->hasRole(['admin', 'general_manager'])) {
            $accessibleCenters = $this->getAccessibleCenters($user)->pluck('id');
            if (!$accessibleCenters->contains($centerId)) {
                abort(403, 'ليس لديك صلاحية إنشاء حلقة في هذا الفرع.');
            }
        }

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
    // ✅ FIX: التحقق من الصلاحية FIRST ثم جلب البيانات
    public function show(string $id)
    {
        $user = Auth::user();

        // ✅ جلب الحلقة عبر الاستعلام الآمن (مع CenterScope)
        $circleQuery = Circle::with([
            'mainTeacher' => fn($q) => $q->withoutGlobalScope(\App\Models\Scopes\CenterScope::class),
            'assistantTeacher' => fn($q) => $q->withoutGlobalScope(\App\Models\Scopes\CenterScope::class),
            'supervisors' => fn($q) => $q->withoutGlobalScope(\App\Models\Scopes\CenterScope::class),
            'students',
        ]);

        // ✅ تطبيق CenterScope يدوياً إذا لم يكن admin
        if (!$user->hasRole(['admin', 'general_manager'])) {
            $teacher = $this->getTeacherRecord($user);
            if ($teacher) {
                $circleQuery->where(function ($q) use ($teacher) {
                    $q->where('center_id', $teacher->center_id)
                        ->orWhereIn('id', function ($sub) use ($teacher) {
                            $sub->select('circle_id')
                                ->from('circle_teacher')
                                ->where('teacher_id', $teacher->id)
                                ->whereIn('role', ['main', 'assistant', 'supervisor']);
                        });
                });
            }
        }

        $circle = $circleQuery->findOrFail($id);

        // ✅ التحقق من الصلاحية بعد جلب البيانات المُصفَّاة
        $this->authorize('view', $circle);

        return view('circles.show', compact('circle'));
    }

    // ─────────────────────────────────────────
    // ✅ FIX: نفس الإصلاح لـ edit()
    public function edit(string $id)
    {
        $user = Auth::user();

        $circleQuery = Circle::with(['mainTeacher', 'assistantTeacher', 'supervisors']);

        if (!$user->hasRole(['admin', 'general_manager'])) {
            $teacher = $this->getTeacherRecord($user);
            if ($teacher) {
                $circleQuery->where(function ($q) use ($teacher) {
                    $q->where('center_id', $teacher->center_id)
                        ->orWhereIn('id', function ($sub) use ($teacher) {
                            $sub->select('circle_id')
                                ->from('circle_teacher')
                                ->where('teacher_id', $teacher->id)
                                ->whereIn('role', ['main', 'assistant', 'supervisor']);
                        });
                });
            }
        }

        $circle = $circleQuery->findOrFail($id);
        $this->authorize('update', $circle);

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

        // ✅ لا يسمح بتغيير الفرع إلا للإداريين
        $centerId = $user->hasRole(['admin', 'general_manager'])
            ? $request->center_id
            : $circle->center_id;

        // ✅ التحقق من أن المركز الجديد متاح
        if ($user->hasRole(['admin', 'general_manager']) && $request->has('center_id')) {
            $accessibleCenters = $this->getAccessibleCenters($user)->pluck('id');
            if (!$accessibleCenters->contains($centerId)) {
                abort(403, 'ليس لديك صلاحية نقل الحلقة لهذا الفرع.');
            }
        }

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

        // ✅ التحقق من عدم وجود طلاب مسجلين قبل الحذف
        if ($circle->students()->count() > 0) {
            return redirect()->back()->with(
                'error',
                'لا يمكن حذف الحلقة لوجود طلاب مسجلين فيها. يرجى نقل الطلاب أولاً.'
            );
        }

        DB::transaction(function () use ($circle) {
            $circle->teachers()->detach();
            $circle->delete();
        });

        return redirect()->route('circles.index')->with('success', 'تم حذف الحلقة بنجاح');
    }

    // ─────────────────────────────────────────
    // ✅ FIX: تحسين syncCircleStaff مع تحققات أمنية شاملة
    private function syncCircleStaff(Circle $circle, Request $request): void
    {
        $centerId = $circle->center_id;
        $user     = Auth::user();

        // ✅ جلب المعلمين المتاحين فقط للمستخدم
        $accessibleTeacherIds = $this->getAccessibleTeachers($user, $this->getTeacherRecord($user))
            ->pluck('id')
            ->toArray();

        // ✅ التحقق من المعلم الرئيسي
        if ($request->teacher_id) {
            $teacher = Teacher::find($request->teacher_id);
            if (!$teacher) {
                throw new \Exception('المعلم الرئيسي غير موجود.');
            }
            if ($teacher->center_id != $centerId) {
                throw new \Exception('المعلم الرئيسي يجب أن يكون في نفس الفرع.');
            }
            if (!in_array($teacher->id, $accessibleTeacherIds) && !$user->hasRole(['admin', 'general_manager'])) {
                throw new \Exception('ليس لديك صلاحية تعيين هذا المعلم.');
            }
        }

        // ✅ التحقق من المعلم المساعد
        if ($request->assistant_teacher_id) {
            $teacher = Teacher::find($request->assistant_teacher_id);
            if (!$teacher) {
                throw new \Exception('المعلم المساعد غير موجود.');
            }
            if ($teacher->center_id != $centerId) {
                throw new \Exception('المعلم المساعد يجب أن يكون في نفس الفرع.');
            }
            if (!in_array($teacher->id, $accessibleTeacherIds) && !$user->hasRole(['admin', 'general_manager'])) {
                throw new \Exception('ليس لديك صلاحية تعيين هذا المعلم.');
            }
        }

        // ✅ التحقق من المشرفين
        foreach ((array) $request->supervisor_ids as $supervisorId) {
            if (!$supervisorId) continue;
            $teacher = Teacher::find($supervisorId);
            if (!$teacher) {
                throw new \Exception('المشرف غير موجود.');
            }
            if ($teacher->center_id != $centerId) {
                throw new \Exception('المشرف يجب أن يكون في نفس الفرع.');
            }
            if (!in_array($teacher->id, $accessibleTeacherIds) && !$user->hasRole(['admin', 'general_manager'])) {
                throw new \Exception('ليس لديك صلاحية تعيين هذا المشرف.');
            }
        }

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
