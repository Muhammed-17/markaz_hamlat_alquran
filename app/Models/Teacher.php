<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Teacher extends Model
{
    use HasRoles;
    protected $fillable = ['user_id', 'name',];

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
