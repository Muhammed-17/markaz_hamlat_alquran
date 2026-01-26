<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Circle extends Model{
    protected $fillable = [
        'name', 'type', 'level', 'max_students',
        'notes', 'is_active'
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')->withTimestamps();
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
