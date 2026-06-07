<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property int $student_id
 * @property int $circle_id
 * @property int|null $collected_by
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $month
 * @property string $status
 * @property string|null $payment_method
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Circle $circle
 * @property-read \App\Models\User|null $collectedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCircleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCollectedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    use HasRoles;
    protected $fillable = [
        'student_id', 'circle_id', 'collected_by',
        'amount', 'month', 'status', 'payment_method',
        'paid_at', 'notes'
    ];

    protected $casts = [
        'month' => 'date:Y-m',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    
    // علاقة: الاشتراك ← طالبه
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // علاقة: الاشتراك ← حلقتِه
    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }

    // علاقة: الاشتراك ← من استلمه (مستخدم: معلم/مشرف/مدير)
    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // دالة حساب المبلغ (تُستخدم عند الإنشاء)
    public static function calculateAmount(Student $student, Circle $circle): float
    {
        $price = SubscriptionPrice::where('circle_level', $circle->level)
                   ->where('education_level', $student->education_level)
                   ->first();
        return $price?->amount ?? 60.00;
    }
}
