<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CompetitionExaminer
 *
 * Represents an examiner assigned to a competition.
 *
 * @property int $id
 * @property int $competition_id
 * @property int $examiner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CompetitionExaminer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'competition_id',
        'examiner_id',
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
     * Examiner.
     */
    public function examiner(): BelongsTo
    {
        return $this->belongsTo(Examiner::class);
    }

    /**
     * Levels assigned to this examiner within the competition.
     */
    public function competitionExaminerLevels(): HasMany
    {
        return $this->hasMany(CompetitionExaminerLevel::class);
    }

    /**
     * Questions created by this examiner.
     */
    public function competitionQuestions(): HasMany
    {
        return $this->hasMany(CompetitionQuestion::class);
    }

    /**
     * Answers evaluated by this examiner.
     */
    public function competitionAnswers(): HasMany
    {
        return $this->hasMany(CompetitionAnswer::class);
    }
}
