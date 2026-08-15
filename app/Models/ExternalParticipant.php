<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ExternalParticipant
 *
 * Represents a participant from outside the Quran center.
 *
 * @property int $id
 * @property string $name
 * @property string|null $national_id
 * @property string|null $phone
 * @property string|null $secondary_phone
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string|null $gender
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ExternalParticipant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'national_id',
        'phone',
        'secondary_phone',
        'address',
        'date_of_birth',
        'gender',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /**
     * Competitions in which this participant is registered.
     */
    public function competitionParticipants(): HasMany
    {
        return $this->hasMany(CompetitionParticipant::class);
    }

    /**
     * Determine whether the participant has a national ID.
     */
    public function hasNationalId(): bool
    {
        return !empty($this->national_id);
    }

    /**
     * Determine whether the participant has a secondary phone.
     */
    public function hasSecondaryPhone(): bool
    {
        return !empty($this->secondary_phone);
    }

    /**
     * Determine whether the participant has notes.
     */
    public function hasNotes(): bool
    {
        return !empty($this->notes);
    }

    /**
     * Get the participant age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    /**
     * Get participant type.
     */
    public function getParticipantTypeAttribute(): string
    {
        return 'external';
    }
}
