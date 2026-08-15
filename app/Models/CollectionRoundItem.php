<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionRoundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_round_id',
        'subscription_id',
        'amount_at_collection',
        'collected_by_snapshot',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    protected $casts = [
        'amount_at_collection' => 'decimal:2',
    ];

    /**
     * التحصيل التي ينتمي إليها العنصر
     */
    public function collectionRound(): BelongsTo
    {
        return $this->belongsTo(CollectionRound::class);
    }

    /**
     * الاشتراك المرتبط
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * نسخة مجمَّدة من جامع النقود وقت الإضافة
     */
    public function collectedBySnapshot(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by_snapshot');
    }
}
