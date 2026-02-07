<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTeacherRequest;
use App\Http\Requests\EditTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->get();
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

        return view('teachers.edit', [
            "teacher" => $teacher,
        ]);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->user->id,
            'password' => 'nullable|min:6',
        ]);

        // تحديث بيانات المعلم
        $teacher->update([
            'name' => $request->name,
        ]);

        // تحديث بيانات اليوزر المرتبط
        $teacher->user->update([
            'email' => $request->email,
            'password' => $request->password
                ? Hash::make($request->password)
                : $teacher->user->password,
        ]);

        return redirect()->route('teachers.index')->with('success', 'تم تحديث البيانات بنجاح');
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
