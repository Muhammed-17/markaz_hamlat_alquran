<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Examiner
 *
 * Represents an examiner who participates in Quran competitions.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $phone
 * @property string|null $secondary_phone
 * @property string|null $address
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Examiner extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'phone',
        'secondary_phone',
        'address',
        'notes',
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
     * User account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Competitions assigned to this examiner.
     */
    public function competitionExaminers(): HasMany
    {
        return $this->hasMany(CompetitionExaminer::class);
    }

    /**
     * Questions created by this examiner.
     *
     * الوصول يكون عبر CompetitionExaminer.
     */
    public function competitionQuestions(): HasManyThrough
    {
        return $this->hasManyThrough(
            CompetitionQuestion::class,
            CompetitionExaminer::class,
            'examiner_id',
            'competition_examiner_id',
            'id',
            'id'
        );
    }

    /**
     * Answers evaluated by this examiner.
     *
     * الوصول يكون عبر CompetitionExaminer.
     */
    public function competitionAnswers(): HasManyThrough
    {
        return $this->hasManyThrough(
            CompetitionAnswer::class,
            CompetitionExaminer::class,
            'examiner_id',
            'competition_examiner_id',
            'id',
            'id'
        );
    }

    /**
     * Get examiner name.
     */
    public function getNameAttribute(): ?string
    {
        return $this->user?->name;
    }

    /**
     * Determine whether the examiner has a secondary phone.
     */
    public function hasSecondaryPhone(): bool
    {
        return !empty($this->secondary_phone);
    }

    /**
     * Determine whether the examiner has notes.
     */
    public function hasNotes(): bool
    {
        return !empty($this->notes);
    }
}
