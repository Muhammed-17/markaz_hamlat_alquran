<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $student_id
 * @property string|null $current_surah
 * @property string|null $study_system
 * @property string|null $group_name
 * @property string|null $new_memorization_plan
 * @property string|null $placement_evaluation
 * @property string|null $old_memorization_plan
 * @property string|null $old_memorization_plan_other
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereCurrentSurah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereNewMemorizationPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereOldMemorizationPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereOldMemorizationPlanOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail wherePlacementEvaluation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereStudySystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentConstructionDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentConstructionDetail extends Model
{
    protected $fillable = [
        'student_id',
        'current_surah',
        'study_system',
        'group_name',
        'new_memorization_plan',
        'placement_evaluation',
        'old_memorization_plan',
        'old_memorization_plan_other',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
