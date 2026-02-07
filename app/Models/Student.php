<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Student extends Model{
    use HasRoles;
    protected $fillable = [
        'name', 'description', 'date_of_birth', 'age', 'gender',
        'education_level', 'phone', 'second_phone', 'address', 'guardian_id',
        'circle_id', 'current_surah', 'enrollment_date',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
    ];

    // علاقة: الطالب ← ولي أمره
    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    // علاقة: الطالب ← حلقتِه
    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }

    // علاقة: الطالب ← حضوره
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // علاقة: الطالب ← اشتراكاته
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}