<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Circle;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();

        return view('students.index', ["students"=>$students]);
    }

    public function create()
    {
        $circles = Circle::all();
        // Assuming we want users who can be guardians. For now, let's just get all users.
        // If roles are implemented, we could filter by role 'guardian'.
        $guardians = \App\Models\User::all(); 
        $subscriptionPrices = DB::table('subscription_prices')->get();
        
        return view('students.create', [
            "circles" => $circles,
            "guardians" => $guardians,
            "subscriptionPrices" => $subscriptionPrices
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'guardian_id' => 'required|exists:users,id',
            'circle_id' => 'nullable|exists:circles,id',
            'gender' => 'required|in:Male,Female',
            'education_level' => 'required|in:Primary,Secondary,High School,University,Other',
            'age' => 'nullable|integer',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'required|in:Active,Inactive,Away',
            'enrollment_date' => 'nullable|date',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'تم إضافة الطالب بنجاح');
    }
}
