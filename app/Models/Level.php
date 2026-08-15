<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Level
 *
 * Represents a competition level.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $memorization_part
 * @property string|null $memorization_from_part
 * @property string|null $memorization_to_part
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Level extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'memorization_part',
        'memorization_from_part',
        'memorization_to_part',
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
     * Competition levels that use this level.
     */
    public function competitionLevels(): HasMany
    {
        return $this->hasMany(CompetitionLevel::class);
    }
}
