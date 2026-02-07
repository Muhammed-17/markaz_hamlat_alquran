<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStudentRequest;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{


    public function index()
    {
        $user = Auth::user();

        switch (true) {
            case $user->hasRole('admin'):
                $students = Student::all();
                break;

            case $user->hasRole('teacher'):
                $teacher = $user->teacher;
                $students = Student::whereIn('circle_id', $teacher->circles->pluck('id'))
                    ->where('status', 'active')
                    ->get();
                break;

            case $user->hasRole('guardian'):
                $students = Student::where('guardian_id', $user->id)->get();
                break;

            default:
                $students = collect();
                break;
        }

        // $students = Student::all();
        return view('students.index', ["students" => $students]);
    }

    public function create()
    {
        $circles = Circle::all();
        $guardians = User::all();
        $subscriptionPrices = DB::table('subscription_prices')->get();

        return view('students.create', [
            "circles" => $circles,
            "guardians" => $guardians,
            "subscriptionPrices" => $subscriptionPrices
        ]);
    }

    public function store(CreateStudentRequest $request)
    {
        // Create the student using validated data from CreateStudentRequest
        Student::create($request->validated());

        return redirect()->route('students.index')->with('success', 'تم إضافة الطالب بنجاح');
    }
    public function show($id)
    {
        $student = Student::findOrFail($id);
        return view('students.show', ["student" => $student]);
    }
    public function edit($id)
    {
        $circles = Circle::all();

        $guardians = User::all();
        $subscriptionPrices = DB::table('subscription_prices')->get();

        $student = Student::findOrFail($id);
        return view('students.edit', [
            "student" => $student,
            "circles" => $circles,
            "guardians" => $guardians,
            "subscriptionPrices" => $subscriptionPrices
        ]);
    }
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'guardian_id' => 'nullable|exists:users,id',
            'circle_id' => 'nullable|exists:circles,id',
            'gender' => 'required',
            'education_level' => 'required',
            'age' => 'nullable|integer',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string',
            'second_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'required',
            'current_surah' => 'nullable|string|max:255',
            'enrollment_date' => 'nullable|date',
        ]);
        $student->update($validated);
        return redirect()->route('students.index')->with('success', 'تم تحديث الطالب بنجاح');
    }
    public function destroy($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->route('students.index')->with('success', 'تم حذف الطالب بنجاح');
    }
}
