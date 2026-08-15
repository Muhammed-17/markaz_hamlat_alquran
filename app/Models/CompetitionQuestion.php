<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
/**
 * Class CompetitionQuestion
 *
 * Represents a question assigned to an examiner within
 * a competition level.
 *
 * @property int $id
 * @property int|null $competition_examiner_id
 * @property int $competition_level_id
 * @property string $name
 * @property string $type
 * @property int|null $memorization_from_surah_id
 * @property int|null $memorization_from_ayah
 * @property int|null $memorization_to_surah_id
 * @property int|null $memorization_to_ayah
 * @property float $score
 * @property string|null $content
 * @property string|null $notes
 */
class CompetitionQuestion extends Model
{
    /**
     * Human-readable labels for the `type` attribute.
     *
     * @var array<string, string>
     */
    public const TYPE_LABELS = [
        'memorization' => 'قرآن',
        'tajweed'      => 'تجويد',
        'tafsir'       => 'تفسير',
        'general'      => 'عام',
        'attendance'   => 'حضور',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'competition_level_id',
        'name',
        'type',

        'memorization_from_surah_id',
        'memorization_from_ayah',

        'memorization_to_surah_id',
        'memorization_to_ayah',

        'score',

        'content',

        'notes',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score'      => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Examiners who claimed this question.
     */
    public function competitionExaminers(): BelongsToMany
    {
        return $this->belongsToMany(
            CompetitionExaminer::class,
            'competition_question_examiner'
        )->withTimestamps();
    }

    /**
     * Competition level.
     */
    public function competitionLevel(): BelongsTo
    {
        return $this->belongsTo(CompetitionLevel::class);
    }

    /**
     * From Surah.
     */
    public function memorizationFromSurah(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'memorization_from_surah_id');
    }

    /**
     * To Surah.
     */
    public function memorizationToSurah(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'memorization_to_surah_id');
    }

    /**
     * Answers submitted for this question.
     */
    public function competitionAnswers(): HasMany
    {
        return $this->hasMany(CompetitionAnswer::class);
    }

    /**
     * Scope: questions not yet claimed by any examiner.
     */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->doesntHave('competitionExaminers');
    }

    /**
     * Determine whether the question is claimed by anyone.
     */
    public function isClaimed(): bool
    {
        return $this->competitionExaminers()->exists();
    }

    /**
     * Determine whether the question is claimed by a specific examiner.
     */
    public function isClaimedBy(int $competitionExaminerId): bool
    {
        return $this->relationLoaded('competitionExaminers')
            ? $this->competitionExaminers->contains('id', $competitionExaminerId)
            : $this->competitionExaminers()->where('competition_examiners.id', $competitionExaminerId)->exists();
    }
    /**
     * Human-readable label for this question's type.
     */
    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }
}
