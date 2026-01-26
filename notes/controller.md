// app/Http/Controllers/StudentController.php

use App\Models\User;
use App\Models\Student;

public function store(Request $request)
{
    $request->validate([
        'student.name' => 'required|string|max:255',
        'student.education_level' => 'required|in:primary,preparatory,secondary',
        'guardian_id' => 'required',
    ]);

    // تحديد ولي الأمر
    if ($request->guardian_id === 'new') {
        $request->validate([
            'guardian.name' => 'required|string|max:255',
            'guardian.email' => 'required|email|unique:users,email',
        ]);

        // إنشاء ولي أمر جديد
        $guardian = User::create([
            'name' => $request->guardian['name'],
            'email' => $request->guardian['email'],
            'password' => bcrypt('password123'), // كلمة مرور مؤقتة
        ]);

        // تعيين الدور
        $guardian->assignRole('guardian');

        $guardianId = $guardian->id;
    } else {
        // استخدام ولي أمر موجود
        $guardianId = $request->guardian_id;
    }

    // إنشاء الطالب
    Student::create([
        'name' => $request->student['name'],
        'education_level' => $request->student['education_level'],
        'guardian_id' => $guardianId,
        'status' => 'active',
        // ... باقي الحقول
    ]);

    return redirect()->route('students.index')
                     ->with('success', 'تم تسجيل الطالب وولي أمره بنجاح!');
}


جلب أولياء الأمور الموجودين
public function create()
{
    $existingGuardians = User::role('guardian')->get();
    return view('students.create', compact('existingGuardians'));
}