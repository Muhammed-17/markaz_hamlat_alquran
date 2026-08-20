<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}