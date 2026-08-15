<?php

namespace App\Models;

use App\Models\Scopes\CenterScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentActivity extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'student_activities';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'weekly_followup_id',
        'follow_id',
        'activity_type',
        'activity_name',
        'activity_date',
        'notes',
    ];
    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'activity_date' => 'date',
    ];

    // =====================================================
    // Relationships
    // =====================================================

    /**
     * Weekly follow-up.
     */
    public function weeklyFollowup(): BelongsTo
    {
        return $this->belongsTo(
            StudentWeeklyFollowup::class,
            'weekly_followup_id'
        );
    }

    /**
     * Student.
     */
    public function student(): BelongsTo
    {
        return $this->weeklyFollowup()->getRelated()->student();
    }

    /**
     * Teacher.
     */
    public function teacher(): BelongsTo
    {
        return $this->weeklyFollowup()->getRelated()->teacher();
    }

    /**
     * Circle.
     */
    public function circle(): BelongsTo
    {
        return $this->weeklyFollowup()->getRelated()->circle();
    }
}
