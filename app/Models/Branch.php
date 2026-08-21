<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'center_id',
        'name',
        'address',
        'established_at',
    ];

    protected $casts = [
        'established_at' => 'date',
    ];

    /**
     * الفرع ينتمي لمركز واحد
     */
    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    /**
     * المعلمون المشرفون على هذا الفرع
     */
    public function supervisors()
    {
        return $this->belongsToMany(Teacher::class, 'branch_teacher', 'branch_id', 'teacher_id')
            ->withTimestamps();
    }
}
