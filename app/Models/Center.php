<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Center whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Center extends Model
{
    protected $fillable = ['name', 'established_at'];

    protected $casts = [
        'established_at' => 'date',
    ];

    public function circles(): HasManyThrough
    {
        return $this->hasManyThrough(Circle::class, Branch::class);
    }
    public function educationalLessons(): HasMany
    {
        return $this->hasMany(EducationalLesson::class);
    }

    public function surahTests(): HasMany
    {
        return $this->hasMany(SurahTest::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
