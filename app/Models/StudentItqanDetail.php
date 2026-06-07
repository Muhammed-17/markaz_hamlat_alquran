<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $student_id
 * @property string|null $previous_memorization_side
 * @property int|null $previous_khatamat_count
 * @property string|null $current_review_amount
 * @property string|null $self_evaluation
 * @property string|null $tajweed_matn
 * @property string|null $tajweed_matn_other
 * @property string|null $desired_path
 * @property string|null $preferred_time
 * @property string|null $teacher_name
 * @property string|null $itqan_details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereCurrentReviewAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereDesiredPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereItqanDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereMemorizedTexts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail wherePreferredTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail wherePreviousKhatamatCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail wherePreviousMemorizationSide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereSelfEvaluation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereTajweedMatn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereTajweedMatnOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereTeacherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentItqanDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentItqanDetail extends Model
{
    protected $fillable = [
        'student_id',
        'previous_memorization_side',
        'previous_khatamat_count',
        'current_review_amount',
        'self_evaluation',
        'tajweed_matn',
        'tajweed_matn_other',
        'desired_path',
        'preferred_time',
        'teacher_name',
        'itqan_details',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
