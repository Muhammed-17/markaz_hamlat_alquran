<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $level
 * @property int $center_id
 * @property int|null $branch_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Circle query()
 * @mixin \Eloquent
 */

class Circle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'level',
        'center_id',
        'branch_id',
    ];

    // ─── العلاقات الجديدة ───
    public function groupSessionPlans(): HasMany
    {
        return $this->hasMany(GroupSessionPlan::class);
    }

    public function studentConstructionDetails(): HasMany
    {
        return $this->hasMany(StudentConstructionDetail::class);
    }

    // ─── العلاقات الموجودة ───
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

    public function mainTeachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'main')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assistantTeachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'circle_teacher')
            ->wherePivot('role', 'assistant')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getMainTeacherAttribute(): ?Teacher
    {
        return $this->mainTeachers->first();
    }

    public function getAssistantTeacherAttribute(): ?Teacher
    {
        return $this->assistantTeachers->first();
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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
            'group'      => 'جماعي',
            'individual' => 'فردي',
            default      => $this->type,
        };
    }

    public static function circleIdsForTeacher(int $teacherId, array $roles): Collection
    {
        return DB::table('circle_teacher')
            ->where('teacher_id', $teacherId)
            ->whereIn('role', $roles)
            ->pluck('circle_id');
    }

    public function surahTests(): HasMany
    {
        return $this->hasMany(SurahTest::class);
    }

    public function competitionParticipants(): HasMany
    {
        return $this->hasMany(CompetitionParticipant::class);
    }
}
