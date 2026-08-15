<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentUnpaidMonths extends Model
{
    protected $fillable = [
        'student_id',
        'unpaid_months_count',
        'last_calculated_at',
    ];

    protected $casts = [
        'last_calculated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}