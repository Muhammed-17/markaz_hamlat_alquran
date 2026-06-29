<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionDelivery extends Model
{
    protected $fillable = [
        'circle_id',
        'teacher_id',
        'supervisor_id',
        'admin_id',
        'month',
        'circle_total_amount',
        'admin_collected_amount',
        'expected_from_teacher',
        'delivered_by_teacher',
        'status',
        'confirmed_by_admin',
        'notes',
        'delivered_at',
        'confirmed_at',
    ];

    protected $casts = [
        'month' => 'date',
        'circle_total_amount' => 'float',
        'admin_collected_amount' => 'float',
        'expected_from_teacher' => 'float',
        'delivered_by_teacher' => 'float',
        'confirmed_by_admin' => 'boolean',
        'delivered_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->expected_from_teacher - $this->delivered_by_teacher;
    }

    public function getIsFullyDeliveredAttribute(): bool
    {
        return $this->delivered_by_teacher >= $this->expected_from_teacher;
    }
}
