<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
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
