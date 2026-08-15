<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class TafsirFile
 *
 * Represents a tafsir file that can be used
 * as a reference for tafsir questions.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TafsirFile extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'tafsir_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
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
     * Competition participants using this tafsir file.
     */
    public function competitionParticipants(): HasMany
    {
        return $this->hasMany(
            CompetitionParticipant::class,
            'tafsir_file_id'
        );
    }
}
