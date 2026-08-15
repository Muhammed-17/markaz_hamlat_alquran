<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CompetitionLevel
 *
 * Represents a level assigned to a specific competition.
 *
 * @property int $id
 * @property int $competition_id
 * @property int $level_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CompetitionLevel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'competition_id',
        'level_id',
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
     * Competition.
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Original level.
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Examiners assigned to this competition level.
     */
    public function competitionExaminerLevels(): HasMany
    {
        return $this->hasMany(CompetitionExaminerLevel::class);
    }

    /**
     * Participants registered in this competition level.
     */
    public function competitionParticipants(): HasMany
    {
        return $this->hasMany(CompetitionParticipant::class);
    }

    /**
     * Questions belonging to this competition level.
     */
    public function competitionQuestions(): HasMany
    {
        return $this->hasMany(CompetitionQuestion::class);
    }
}
