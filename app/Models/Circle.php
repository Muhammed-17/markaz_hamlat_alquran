<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $level
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Circle extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'name',
        'type',
        'level',
        'center_id',
    ];

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'supervisor')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function mainTeacher(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'main')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assistantTeacher(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'assistant')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function getLevelArabicAttribute(): string
    {
        return match ($this->level) {
            'build'      => 'بناء',
            'mastery'    => 'إتقان',
            'creativity' => 'إبداع',
            default      => $this->level,
        };
    }

    public function getTypeArabicAttribute(): string
    {
        return match ($this->type) {
            'group'      => 'جماعية',
            'individual' => 'فردية',
            default      => $this->type,
        };
    }
}
