<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $student_id
 * @property string|null $previous_licenses_and_chains
 * @property string|null $desired_narration_and_path
 * @property string|null $preferred_time
 * @property string|null $supervisor_name
 * @property string|null $ibda_details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereDesiredNarrationAndPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereIbdaDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail wherePreferredTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail wherePreviousLicensesAndChains($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereSupervisorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StudentIbdaDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentIbdaDetail extends Model
{
    protected $fillable = [
        'student_id',
        'previous_licenses_and_chains',
        'desired_narration_and_path',
        'preferred_time',
        'supervisor_name',
        'ibda_details',
    ];
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
