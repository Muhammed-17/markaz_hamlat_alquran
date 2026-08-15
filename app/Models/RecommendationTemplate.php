<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RecommendationTemplate
 *
 * Ready-made recommendation sentences, one per (category, level)
 * combination. Used to build the final generated_recommendation text
 * for a StudentWeeklyFollowup based on each section's assessed level.
 */
class RecommendationTemplate extends Model
{
    protected $table = 'recommendation_templates';

    protected $fillable = [
        'category',
        'level',
        'sentence',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active sentence for a given category + level.
     */
    public static function sentenceFor(string $category, ?string $level): ?string
    {
        if (!$level) {
            return null;
        }

        return static::query()
            ->where('category', $category)
            ->where('level', $level)
            ->where('is_active', true)
            ->value('sentence');
    }
}
