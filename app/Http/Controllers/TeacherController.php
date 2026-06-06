<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\CreateTeacherRequest;
use App\Http\Requests\Teacher\EditTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class TeacherController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Teacher::with('user');

        if ($user->hasRole('supervisor')) {
            $query->whereHas('user', function ($q) {
                $q->whereHas('roles', function ($rq) {
                    $rq->whereIn('name', ['teacher', 'supervisor']);
                });
            });
        }

        $teachers = $query->get();
        return view('teachers.index', ["teachers" => $teachers]);
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(CreateTeacherRequest $request)
    {
        // إنشاء المستخدم
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // تعيين الدور (teacher / supervisor)
        $user->assignRole($request->role);

        // إنشاء سجل teacher مربوط بالمستخدم
        Teacher::create([
            'user_id' => $user->id,
            'name'    => $request->name,
        ]);

        return redirect()
            ->route('teachers.index')
            ->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        $role = $teacher->user->roles->first()?->name;
        return view('teachers.edit', [
            "teacher" => $teacher,
            "role" => $role
        ]);
    }

    public function update(EditTeacherRequest $request, Teacher $teacher)
    {
        // تحديث بيانات المعلم
        $teacher->update([
            'name' => $request->name,
        ]);

        // تحديث بيانات المستخدم المرتبط
        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // تحديث الباسورد فقط لو تم إدخاله
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->user->update($data);

        return redirect()
            ->route('teachers.index')
            ->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function destroy(Teacher $teacher)
    {
        // حذف المستخدم المرتبط (اختياري حسب تصميمك)
        $teacher->user->delete();
        $teacher->delete();

        return redirect()
            ->route('teachers.index')
            ->with('success', 'تم الحذف بنجاح');
    }

    public function toggle(Teacher $teacher)
    {
        $teacher->user->update([
            'status' => $teacher->user->status === 'active' ? 'inactive' : 'active'
        ]);
        // dd($teacher->user->status);
        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
