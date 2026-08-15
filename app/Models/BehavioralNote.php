<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\CenterScope;

/**
 * Class BehavioralNote
 *
 * Behavior record tracking system.
 */
class BehavioralNote extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'behavioral_notes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'circle_id',
        'teacher_id',
        'incident_at',
        'behavior',
        'action_taken',
        'action_at',
        'current_status',
        'status',
        'follow_up_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'incident_at' => 'datetime',
        'action_at' => 'datetime',
        'follow_up_at' => 'date',
    ];

    // ==========================================
    // Constants — action-tracking status
    // ==========================================
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_ACTION_TAKEN = 'action_taken';

    public const STATUSES = [
        self::STATUS_PENDING      => 'قيد الانتظار',
        self::STATUS_UNDER_REVIEW => 'يتم التحقيق',
        self::STATUS_ACTION_TAKEN => 'تم اتخاذ الإجراء',
    ];

    // ==========================================
    // Boot - Apply CenterScope
    // ==========================================
    protected static function booted(): void
    {
        static::addGlobalScope(new CenterScope());
    }

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * Get the student associated with this behavioral note.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the circle associated with this behavioral note.
     */
    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class, 'circle_id');
    }

    /**
     * Get the teacher who recorded this behavioral note.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
