<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Class CompetitionAnswer
 *
 * Stores the evaluation for a participant's answer
 * to a specific competition question.
 *
 * @property int $id
 * @property int $competition_participant_id
 * @property int $competition_question_id
 * @property int|null $competition_examiner_id
 * @property int|null $user_id
 * @property float $score
 * @property bool $answered
 * @property string|null $memorization_mistakes
 * @property string|null $tashkeel_mistakes
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CompetitionAnswer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'competition_participant_id',
        'competition_question_id',

        'competition_examiner_id',
        'user_id',

        'score',
        'answered',

        'memorization_mistakes',
        'tashkeel_mistakes',

        'notes',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
    'score'                 => 'decimal:2',
    'answered'               => 'boolean',
    'memorization_mistakes'  => 'integer', 
    'tashkeel_mistakes'      => 'integer',   
    'created_at'             => 'datetime',
    'updated_at'              => 'datetime',
];

    /**
     * Participant.
     */
    public function competitionParticipant(): BelongsTo
    {
        return $this->belongsTo(CompetitionParticipant::class);
    }

    /**
     * Question.
     */
    public function competitionQuestion(): BelongsTo
    {
        return $this->belongsTo(CompetitionQuestion::class);
    }

    /**
     * External examiner.
     */
    public function competitionExaminer(): BelongsTo
    {
        return $this->belongsTo(CompetitionExaminer::class);
    }

    /**
     * Internal evaluator (Supervisor / Manager).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Competition Level.
     */
    public function competitionLevel(): HasOneThrough
    {
        return $this->hasOneThrough(
            CompetitionLevel::class,
            CompetitionQuestion::class,
            'id',
            'id',
            'competition_question_id',
            'competition_level_id'
        );
    }


    /**
     * Determine whether the participant has memorization mistakes.
     */
    public function hasMemorizationMistakes(): bool
    {
        return ! empty($this->memorization_mistakes);
    }

    /**
     * Determine whether the participant has tashkeel mistakes.
     */
    public function hasTashkeelMistakes(): bool
    {
        return ! empty($this->tashkeel_mistakes);
    }

    /**
     * Determine whether the answer contains any notes.
     */
    public function hasNotes(): bool
    {
        return ! empty($this->notes);
    }

    /**
     * Get score as float.
     */
    public function getScoreAttribute($value): float
    {
        return (float) $value;
    }

    /**
     * Determine whether the participant answered the question.
     */
    public function hasAnswered(): bool
    {
        return $this->answered;
    }

    /**
     * Get the evaluator name.
     */
    public function getEvaluatorNameAttribute(): ?string
    {
        if ($this->competitionExaminer) {
            return $this->competitionExaminer->examiner?->name;
        }

        return $this->user?->name;
    }
    /**
     * Determine whether the evaluation was performed by an external examiner.
     */
    public function evaluatedByExaminer(): bool
    {
        return ! is_null($this->competition_examiner_id);
    }

    /**
     * Determine whether the evaluation was performed by an internal user.
     */
    public function evaluatedByUser(): bool
    {
        return ! is_null($this->user_id);
    }
}
