<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $center_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Branch extends Model
{
    protected $fillable = [
        'center_id',
        'name',
    ];

    /**
     * المركز التابع له الفرع.
     */
    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    /**
     * الحلقات التابعة للفرع.
     */
    public function circles(): HasMany
    {
        return $this->hasMany(Circle::class);
    }

    /**
     * المعلمون التابعون للفرع مباشرة (branch_id على teachers).
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * مشرفو الفرع (many-to-many عبر branch_teacher).
     * فرع واحد ممكن يكون له أكتر من مشرف.
     */
    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'branch_teacher')
            ->withTimestamps();
    }
}
