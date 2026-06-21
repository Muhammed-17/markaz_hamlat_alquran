<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Circle;
use App\Models\Center;
use App\Models\Attendance;
use App\Models\Subscription;
use App\Models\StudentConstructionDetail;
use App\Models\StudentItqanDetail;
use App\Models\StudentIbdaDetail;
use App\Models\Scopes\CenterScope;

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
 * @property string|null $health_status_other
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
 * @property string|null $learning_difficulties_other
 * @property string|null $personal_traits
 * @property string|null $personal_traits_other
 * @property array<array-key, mixed>|null $hobbies
 * @property string|null $hobby_other
 * @property string|null $reading
 * @property string|null $exit_details
 * @property string|null $student_exit_status
 * @property string|null $decision
 * @property float|null $subscription_fees
 * @property string|null $received_tools
 * * @property-read \Illuminate\Database\Eloquent\Collection<int, Attendance> $attendances
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
        'health_status_other',
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
        'learning_difficulties_other',
        'personal_traits',
        'personal_traits_other',
        'hobbies',
        'hobby_other',
        'reading',
        'exit_details',
        'student_exit_status',
        'decision',
        'subscription_fees',
        'received_tools',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CenterScope());

        static::creating(function ($model) {
            if (!$model->center_id && auth()->check()) {
                $model->center_id = auth()->user()->center_id;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'date_of_birth'     => 'date',
            'suspended_at'      => 'datetime',
            'join_date'         => 'date',
            'hobbies'           => 'array',
            'subscription_fees' => 'float',
        ];
    }

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Teacher::class, 'supervisor_id')
            ->withoutGlobalScope(CenterScope::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function constructionDetail()
    {
        return $this->hasOne(StudentConstructionDetail::class, 'student_id');
    }

    public function itqanDetail()
    {
        return $this->hasOne(StudentItqanDetail::class, 'student_id');
    }

    public function ibdaDetail()
    {
        return $this->hasOne(StudentIbdaDetail::class, 'student_id');
    }

    // ==========================================
    // الخصائص الديناميكية (Accessors)
    // ==========================================

    public function getOverdueMonthsCountAttribute(): int
    {
        if ($this->status === 'متوقف' && $this->suspended_at) {
            $endDate = $this->suspended_at->copy()->startOfMonth();
        } elseif ($this->status === 'مقيد') {
            $endDate = now()->startOfMonth();
        } else {
            return 0;
        }

        $startDate = $this->join_date
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
        if ($this->status !== 'متوقف' || !$this->suspended_at) {
            return 0;
        }
        return (float) $this->subscriptions
            ->where('status', '!=', 'مدفوع')
            ->where('month', '<=', $this->suspended_at)
            ->sum('amount');
    }
}
