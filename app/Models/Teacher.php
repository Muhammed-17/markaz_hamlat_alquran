<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'specialization'];

    // علاقة: المعلم ← مستخدمه
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة: المعلم ←→ حلقاته (Many-to-Many)
    public function circles()
    {
        return $this->belongsToMany(Circle::class, 'circle_teacher');
    }
}
