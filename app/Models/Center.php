<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\HasMany;

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
    protected $fillable = ['name'];

    public function circles(): HasMany
    {
        return $this->hasMany(Circle::class);
    }
    public function educationalLessons(): HasMany
    {
        return $this->hasMany(EducationalLesson::class);
    }

    public function surahTests(): HasMany
    {
        return $this->hasMany(SurahTest::class);
    }
}
