<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\CenterScope;

/**
 * Class GroupSessionPlan
 * 
 * Represents an individual session within a weekly group plan.
 */
class GroupSessionPlan extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'group_session_plans';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'circle_id',
        'session_name',
        'start_time',
        'end_time',
        'planned_content',
        'completed_content',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
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

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class, 'circle_id');
    }
}
