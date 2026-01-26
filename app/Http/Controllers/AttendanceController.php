<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $students = [
            [
                'id' => 1,
                'name' => 'إبراهيم سعيد',
                'status' => 'not_recorded',
            ],
            [
                'id' => 2,
                'name' => 'عبد الرحمن علي',
                'status' => 'not_recorded',
            ],
        ];

        return view('attendance.index', compact('students'));
    }
}
