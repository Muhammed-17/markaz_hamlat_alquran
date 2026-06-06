<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\auth\Access\Authorizable;
use App\Models\User;
use App\Models\Circle;
use App\Models\Attendance;
use App\Models\Subscription;
use App\Models\StudentConstructionDetail;
use App\Models\StudentItqanDetail;
use App\Models\StudentIbdaDetail;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string $gender
 * @property string|null $second_phone
 * @property string|null $address
 * @property int|null $guardian_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $suspended_at
 * @property int|null $circle_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $student_code
 * @property string|null $education_type
 * @property string|null $educational_stage
 * @property string|null $school_grade
 * @property string|null $previous_school
 * @property string|null $center_entry_level
 * @property \Illuminate\Support\Carbon|null $join_date
 * @property string|null $whatsapp_number
 * @property string|null $health_status
 * @property string|null $notes
 * @property int|null $supervisor_id
 * @property string|null $applicant
 * @property string|null $applicant_other
 * @property int|null $center_id
 * @property string|null $whatsapp_owner
 * @property string|null $whatsapp_owner_other
 * @property string|null $additional_contact_owner
 * @property string|null $additional_contact_owner_other
 * @property string|null $learning_difficulties
 * @property string|null $personal_traits
 * @property array<array-key, mixed>|null $hobbies
 * @property string|null $reading
 * @property string|null $exit_details
 * @property string|null $student_exit_status
 * @property string|null $decision
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \App\Models\Center|null $center
 * @property-read Circle|null $circle
 * @property-read StudentConstructionDetail|null $constructionDetail
 * @property-read int $overdue_months_count
 * @property-read float $suspended_past_debt
 * @property-read User|null $guardian
 * @property-read StudentIbdaDetail|null $ibdaDetail
 * @property-read StudentItqanDetail|null $itqanDetail
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAdditionalContactOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAdditionalContactOwnerOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereApplicant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereApplicantOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCenterEntryLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCircleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereEducationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereEducationalStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereExitDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereHealthStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereHobbies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereJoinDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereLearningDifficulties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student wherePersonalTraits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student wherePreviousSchool($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereReading($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSchoolGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSecondPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereStudentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereStudentExitStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereWhatsappNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereWhatsappOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereWhatsappOwnerOther($value)
 * @mixin \Eloquent
 */
class Student extends Model
{

    protected $fillable = [
        'name',
        'date_of_birth',
        'gender',
        'second_phone',
        'address',
        'guardian_id',
        'status',
        'suspended_at',
        'circle_id',
        'student_code',
        'education_type',
        'educational_stage',
        'school_grade',
        'previous_school',
        'center_entry_level',
        'join_date',
        'whatsapp_number',
        'health_status',
        'notes',
        'supervisor_id',
        'applicant',
        'applicant_other',
        'center_id',
        'whatsapp_owner',
        'whatsapp_owner_other',
        'additional_contact_owner',
        'additional_contact_owner_other',
        'learning_difficulties',
        'personal_traits',
        'hobbies',
        'reading',
        'exit_details',
        'student_exit_status',
        'decision',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'suspended_at'  => 'datetime',
            'join_date'     => 'date',
            'hobbies'       => 'array',
        ];
    }

    // علاقة: الطالب ← ولي أمره
    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    // علاقة: الطالب ← حلقتِه
    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }

    // علاقة: الطالب ← الفرع / المركز
    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    // علاقة: الطالب ← حضوره
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // علاقة: الطالب ← اشتراكاته
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function constructionDetail()
    {
        return $this->hasOne(StudentConstructionDetail::class);
    }

    public function itqanDetail()
    {
        return $this->hasOne(StudentItqanDetail::class);
    }

    public function ibdaDetail()
    {
        return $this->hasOne(StudentIbdaDetail::class);
    }

    public function getOverdueMonthsCountAttribute(): int
    {
        if ($this->status === 'inactive' && $this->suspended_at) {
            $endDate = $this->suspended_at->copy()->startOfMonth();
        } elseif ($this->status === 'active') {
            $endDate = now()->startOfMonth();
        } else {
            return 0;
        }

        $startDate = $this->join_date          // ✅ بدل enrollment_date
            ? $this->join_date->copy()->startOfMonth()
            : $this->created_at->copy()->startOfMonth();

        $paidKeys = $this->subscriptions
            ->where('status', 'مدفوع')
            ->pluck('month')
            ->map(fn($d) => $d->format('Y-m'))
            ->unique()
            ->toArray();

        $count = 0;
        $check = $startDate->copy();
        while ($check->lte($endDate)) {
            if (!in_array($check->format('Y-m'), $paidKeys)) {
                $count++;
            }
            $check->addMonth();
        }
        return $count;
    }

    public function getSuspendedPastDebtAttribute(): float
    {
        if ($this->status !== 'inactive' || !$this->suspended_at) {
            return 0;
        }
        return (float) $this->subscriptions
            ->where('status', '!=', 'مدفوع')
            ->where('month', '<=', $this->suspended_at)
            ->sum('amount');
    }
}
