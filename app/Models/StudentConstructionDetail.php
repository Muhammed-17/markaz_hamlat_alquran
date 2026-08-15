<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\CenterScope;

/**
 * Class StudentConstructionDetail
 * 
 * Represents the current Quranic construction data for a student.
 */
class StudentConstructionDetail extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'student_construction_details';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'circle_id',
        'study_system',
        'current_surah_id',
        'new_memorization_plan',
        'revision_plan',
        'old_memorization_plan',
        'placement_evaluation',
    ];

    protected $casts = [
        'new_memorization_plan' => 'string',
        'revision_plan' => 'string',
        'old_memorization_plan' => 'string',
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
     * Get the student that owns this construction detail.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class, 'circle_id');
    }

    /**
     * Get the current surah the student is memorizing.
     */
    public function currentSurah(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'current_surah_id');
    }

    // ==========================================
    // Accessors - قراءة بيانات الخطة الجماعية من سجل الحلقة الرئيسي
    // ==========================================

    protected function groupMasterPlan()
    {
        if ($this->study_system !== 'group' || !$this->circle_id) {
            return null;
        }

        return static::withoutGlobalScope(\App\Models\Scopes\CenterScope::class)
            ->where('circle_id', $this->circle_id)
            ->whereNull('student_id')
            ->latest('updated_at')
            ->first();
    }

    public function getEffectiveCurrentSurahIdAttribute()
    {
        return $this->current_surah_id ?? $this->groupMasterPlan()?->current_surah_id;
    }

    public function getEffectiveCurrentSurahAttribute()
    {
        if ($this->current_surah_id) {
            return $this->currentSurah;
        }

        return $this->groupMasterPlan()?->currentSurah;
    }

    public function getEffectiveNewMemorizationPlanAttribute()
    {
        return $this->new_memorization_plan ?? $this->groupMasterPlan()?->new_memorization_plan;
    }

    public function getEffectiveRevisionPlanAttribute()
    {
        return $this->revision_plan ?? $this->groupMasterPlan()?->revision_plan;
    }

    public function getEffectiveOldMemorizationPlanAttribute()
    {
        return $this->old_memorization_plan ?? $this->groupMasterPlan()?->old_memorization_plan;
    }
}
