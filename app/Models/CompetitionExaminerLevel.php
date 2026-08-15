<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
/**
 * Class CompetitionExaminerLevel
 *
 * Represents the assignment of a competition examiner
 * to a specific competition level.
 *
 * @property int $id
 * @property int $competition_examiner_id
 * @property int $competition_level_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CompetitionExaminerLevel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'competition_examiner_id',
        'competition_level_id',
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
     * Competition examiner.
     */
    public function competitionExaminer(): BelongsTo
    {
        return $this->belongsTo(CompetitionExaminer::class);
    }

    /**
     * Competition level.
     */
    public function competitionLevel(): BelongsTo
    {
        return $this->belongsTo(CompetitionLevel::class);
    }

    /**
     * Get the competition through the examiner assignment.
     */
    /**
     * Get the competition through the examiner assignment.
     */
    public function competition(): HasOneThrough
    {
        return $this->hasOneThrough(
            Competition::class,
            CompetitionExaminer::class,
            'id',                      // FK on CompetitionExaminer (local key of intermediate)
            'id',                      // FK on Competition (local key of final)
            'competition_examiner_id', // FK on this model pointing to CompetitionExaminer
            'competition_id'           // FK on CompetitionExaminer pointing to Competition
        );
    }
}
