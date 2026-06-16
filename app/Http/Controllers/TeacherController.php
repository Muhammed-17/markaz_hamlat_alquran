<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\CreateTeacherRequest;
use App\Http\Requests\Teacher\EditTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use App\Traits\ResolvesUserScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\Center;

class TeacherController extends Controller
{
    use ResolvesUserScope;

    // ─────────────────────────────────────────
    public function index(Request $request){
        $this->authorize('viewAny', Teacher::class);

        $user    = Auth::user();
        $teacher = $this->getTeacherRecord($user);

        $query = Teacher::with(['user.roles', 'center']);

        // فلترة بالفرع — admin يرى الكل
        if (!$user->can('view all teachers')) {
            $teacher
                ? $query->where('center_id', $teacher->center_id)
                : $query->whereRaw('1=0');
        }

        // بحث بالاسم
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // فلتر الفرع
        if ($request->filled('center_id') && $user->can('filter teachers by center')) {
            $query->where('center_id', $request->center_id);
        }

        // فلتر الدور
        if ($request->filled('role') && $user->can('filter teachers by role')) {
            $query->whereHas('user.roles', fn($q) =>
                $q->where('name', $request->role)
            );
        }

        $teachers = $query->get();
        $centers  = $this->getAccessibleCenters($user);
        $roles    = Role::orderBy('name')->get();

        return view('teachers.index', compact('teachers', 'centers', 'roles'));
    }

    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Teacher::class);

        $user    = Auth::user();
        $centers = $this->getAccessibleCenters($user);

        $roles = Role::whereNotIn('name', ['admin', 'guardian'])
            ->orderBy('name')
            ->get();

        return view('teachers.create', compact('centers', 'roles'));
    }

    // ─────────────────────────────────────────
    public function store(CreateTeacherRequest $request)
    {
        $this->authorize('create', Teacher::class);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'center_id' => $request->center_id,
        ]);

        $user->syncRoles($request->roles ?? []);

        Teacher::create([
            'user_id'   => $user->id,
            'name'      => $request->name,
            'center_id' => $request->center_id,
        ]);

        return redirect()->route('teachers.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    // ─────────────────────────────────────────
    public function edit(Teacher $teacher)
    {
        $this->authorize('update', $teacher);

        $user    = Auth::user();
        $centers = $this->getAccessibleCenters($user);

        $teacher->load('user.roles');

        $roles = Role::whereNotIn('name', ['admin', 'guardian'])
            ->orderBy('name')
            ->get();

        $currentRoles = $teacher->user->roles->pluck('name')->toArray();

        return view('teachers.edit', compact('teacher', 'centers', 'roles', 'currentRoles'));
    }

    // ─────────────────────────────────────────
    public function update(EditTeacherRequest $request, Teacher $teacher)
    {
        $this->authorize('update', $teacher);

        $teacher->update([
            'name'      => $request->name,
            'center_id' => $request->center_id,
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'center_id' => $request->center_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->user->update($data);
        $teacher->user->syncRoles($request->roles ?? []);

        return redirect()->route('teachers.index')->with('success', 'تم تحديث البيانات بنجاح');
    }

    // ─────────────────────────────────────────
    public function destroy(Teacher $teacher)
    {
        $this->authorize('delete', $teacher);

        $teacher->user->delete();
        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'تم الحذف بنجاح');
    }

    // ─────────────────────────────────────────
    public function toggle(Teacher $teacher)
    {
        $this->authorize('toggle', $teacher);

        $teacher->user->update([
            'status' => $teacher->user->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}