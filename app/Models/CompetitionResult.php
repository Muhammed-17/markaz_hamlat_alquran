<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CompetitionParticipant;

/**
 * Class CompetitionResult
 *
 * Represents the final result of a participant in a competition.
 *
 * @property int $id
 * @property int $competition_participant_id
 * @property float $total_score
 * @property int|null $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CompetitionResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'competition_participant_id',
        'total_score',
        'rank',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_score' => 'decimal:2',
        'rank'        => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Competition participant.
     */
    public function competitionParticipant(): BelongsTo
    {
        return $this->belongsTo(CompetitionParticipant::class);
    }

    /**
     * الوصول السريع للمسابقة عبر المشارك.
     */
    public function getCompetitionAttribute(): ?Competition
    {
        return $this->competitionParticipant?->competition;
    }
}
