<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Attendance extends Model
{
    use HasRoles;
    protected $fillable = ['student_id', 'date', 'status', 'notes'];

    protected $casts = [
        'date' => 'date',
    ];

    // علاقة: الحضور ← طالبه
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}