<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Competition
 *
 * Represents a Quran competition.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Competition extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Competition levels.
     */
    public function competitionLevels(): HasMany
    {
        return $this->hasMany(CompetitionLevel::class);
    }

    /**
     * Competition examiners.
     */
    public function competitionExaminers(): HasMany
    {
        return $this->hasMany(CompetitionExaminer::class);
    }

    /**
     * Competition participants.
     */
    public function competitionParticipants(): HasMany
    {
        return $this->hasMany(CompetitionParticipant::class);
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class, 'competition_levels');
    }

    /**
     * Competition results.
     *
     * ملاحظة: جدول competition_results لا يحتوي على عمود competition_id
     * (راجع CompetitionResult) — استخدم hasManyThrough بدلاً من hasMany.
     */
    public function competitionResults(): HasManyThrough
    {
        return $this->hasManyThrough(
            CompetitionResult::class,
            CompetitionParticipant::class,
            'competition_id',           // FK on CompetitionParticipant
            'competition_participant_id', // FK on CompetitionResult
            'id',
            'id'
        );
    }
}
