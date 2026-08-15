<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionRoundLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_round_id',
        'description',
        'created_by',
    ];

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * جولة التحصيل المرتبطة بالسجل
     */
    public function collectionRound(): BelongsTo
    {
        return $this->belongsTo(CollectionRound::class);
    }

    /**
     * المستخدم الذي قام بالتعديل
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
