<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Circle extends Model
{
    use HasRoles;
    protected $fillable = [
        'name',
        'type',
        'level',
        'max_students',
        'notes',
        'is_active',
        'supervisor_id'
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class)->withPivot('role')->withTimestamps();
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    
    public function supervisor()
    {
        return $this->belongsTo(Teacher::class, 'supervisor_id');
    }

    public function mainTeacher()
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'main')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assistantTeacher()
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'assistant')
            ->withPivot('role')
            ->withTimestamps();
    }
}
