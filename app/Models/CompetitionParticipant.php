<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
/**
 * Class CompetitionParticipant
 *
 * Represents a participant registered in a competition.
 *
 * @property int $id
 * @property int $competition_id
 * @property int $competition_level_id
 * @property int|null $student_id
 * @property int|null $external_participant_id
 * @property int $registration_fee
 * @property int|null $tafsir_file_id
 * @property int $file_status
 * @property int|null $center_id
 * @property int|null $circle_id
 * @property int|null $supervisor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CompetitionParticipant extends Model
{
    public const FILE_NOT_REQUIRED = 0;
    public const FILE_NOT_RECEIVED = 1;
    public const FILE_RECEIVED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'competition_id',
        'competition_level_id',
        'center_id',
        'student_id',
        'external_participant_id',
        'circle_id',
        'file_status',
        'supervisor_id',
        'tafsir_file_id',
        'registration_fee',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registration_fee' => 'integer',
        'file_status' => 'integer',
        'tafsir_file_id' => 'integer',
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
     * Competition level.
     */
    public function competitionLevel(): BelongsTo
    {
        return $this->belongsTo(CompetitionLevel::class);
    }

    /**
     * Student participant.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * External participant.
     */
    public function externalParticipant(): BelongsTo
    {
        return $this->belongsTo(ExternalParticipant::class);
    }

    /**
     * Tafsir file.
     */
    public function tafsirFile(): BelongsTo
    {
        return $this->belongsTo(TafsirFile::class, 'tafsir_file_id');
    }

    /**
     * Participant answers.
     */
    public function competitionAnswers(): HasMany
    {
        return $this->hasMany(CompetitionAnswer::class);
    }

    /**
     * Competition result.
     */
    public function competitionResult(): HasOne
    {
        return $this->hasOne(CompetitionResult::class);
    }
    /**
     * Get participant display name.
     */
    public function getParticipantNameAttribute(): ?string
    {
        return $this->student?->name
            ?? $this->externalParticipant?->name;
    }

    /**
     * Get participant type.
     */
    public function getParticipantTypeAttribute(): string
    {
        return $this->student_id !== null
            ? 'student'
            : 'external';
    }

    /**
     * Determine whether the participant is internal.
     */
    public function isInternal(): bool
    {
        return $this->student_id !== null;
    }

    /**
     * Circle for internal/student participants.
     */
    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    /**
     * Determine whether the participant is external.
     */
    public function isExternal(): bool
    {
        return $this->external_participant_id !== null;
    }

    /**
     * Center.
     */
    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    /**
     * Supervisor.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
