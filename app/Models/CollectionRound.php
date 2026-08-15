<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollectionRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'confirmed_by',
        'level',
        'center_id',
        'circle_id',
        'round_number',
        'period_month',
        'total_amount',
        'students_count',
        'status',
        'confirmed_at',
        'supervisor_note',
        'manager_note',
        'manager_note_addressed',
    ];

    protected $casts = [
        'period_month' => 'date',
        'confirmed_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'manager_note_addressed' => 'boolean',
    ];

    /**
     * الحلقة المرتبطة بالتحصيل
     */
    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    /**
     * المركز المرتبط بالتحصيل
     */
    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    /**
     * المشرف الذي أنشأ التحصيل
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * المدير الذي أكّد التحصيل
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * عناصر التحصيل (الاشتراكات المدرجة فيها)
     */
    public function items(): HasMany
    {
        return $this->hasMany(CollectionRoundItem::class);
    }

    /**
     * سجلات التعديلات التاريخية
     */
    public function logs(): HasMany
    {
        return $this->hasMany(CollectionRoundLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * نطاق: التحصيلات المعلّق
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * نطاق: التحصيلات المؤكَّد
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * نطاق: فلترة بحلقة وشهر محددين
     */
    public function scopeForCircleAndMonth($query, $circleId, $month)
    {
        return $query->where('circle_id', $circleId)
            ->where('period_month', $month);
    }

    /**
     * هل التحصيل مؤكَّد؟
     */
    public function getIsConfirmedAttribute(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * هل هناك ملاحظة مدير غير معالجة؟
     */
    public function getHasUnaddressedManagerNoteAttribute(): bool
    {
        return !empty($this->manager_note) && !$this->manager_note_addressed;
    }
}
