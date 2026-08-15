<?php

namespace App\Models;

use App\Models\Scopes\CenterScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Circle;

class SurahTest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'test_type',
        'center_id',
        'circle_id',
        'teacher_id',
        'surah_id',
        'test_date',
        'notes',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'test_date' => 'date',
    ];

    // =====================================================
    // Boot
    // =====================================================

    protected static function booted(): void
    {
        static::addGlobalScope(new CenterScope());

        static::creating(function (self $model) {

            if (!$model->center_id && auth()->check() && auth()->user()->center_id) {
                $model->center_id = auth()->user()->center_id;
            }

            if (!$model->center_id && $model->circle_id) {
                $model->center_id = Circle::withoutGlobalScope(CenterScope::class)
                    ->find($model->circle_id)?->center_id;
            }
        });
    }

    // =====================================================
    // Relationships
    // =====================================================

    /**
     * Center.
     */
    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    /**
     * Circle.
     */
    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    /**
     * Teacher.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class)
            ->withoutGlobalScope(CenterScope::class);
    }

    /**
     * Surah.
     */
    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }

    /**
     * Student test results.
     */
    public function results(): HasMany
    {
        return $this->hasMany(StudentSurahTestResult::class);
    }

    // =====================================================
    // Scopes
    // =====================================================

    public function scopeGroup($query)
    {
        return $query->where('test_type', 'group');
    }

    public function scopeIndividual($query)
    {
        return $query->where('test_type', 'individual');
    }
}
