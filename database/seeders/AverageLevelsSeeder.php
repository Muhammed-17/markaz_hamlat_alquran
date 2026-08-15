<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder: Average Levels
 *
 * Defines performance level ratings as constants.
 * Used across various assessments (average_level columns).
 * No database table required — values are embedded in code.
 */
class AverageLevelsSeeder extends Seeder
{
    /**
     * Performance level definitions.
     */
    public const LEVELS = [
        ['key' => 'excellent',  'name' => 'ممتاز',    'sort_order' => 1],
        ['key' => 'very_good',  'name' => 'جيد جداً', 'sort_order' => 2],
        ['key' => 'good',       'name' => 'جيد',      'sort_order' => 3],
        ['key' => 'acceptable', 'name' => 'مقبول',    'sort_order' => 4],
        ['key' => 'weak',       'name' => 'ضعيف',     'sort_order' => 5],
    ];

    /**
     * No database seeding required.
     */
    public function run(): void
    {
        // Intentionally empty — data is stored in constants, not the database.
    }

    /**
     * Get all levels sorted by sort_order.
     *
     * @return array
     */
    public static function all(): array
    {
        return collect(self::LEVELS)
            ->sortBy('sort_order')
            ->values()
            ->all();
    }

    /**
     * Get a single level by its key.
     *
     * @param string $key
     * @return array|null
     */
    public static function get(string $key): ?array
    {
        return collect(self::LEVELS)
            ->firstWhere('key', $key);
    }

    /**
     * Get levels as a key-value array for dropdowns.
     *
     * @return array<string, string>
     */
    public static function forSelect(): array
    {
        return collect(self::LEVELS)
            ->sortBy('sort_order')
            ->pluck('name', 'key')
            ->all();
    }

    /**
     * Get all keys as an array for validation rules.
     *
     * @return array<string>
     */
    public static function keys(): array
    {
        return collect(self::LEVELS)
            ->pluck('key')
            ->all();
    }

    /**
     * Get levels ordered from best to worst for display.
     *
     * @return array<string, string>
     */
    public static function forRating(): array
    {
        return collect(self::LEVELS)
            ->sortBy('sort_order')
            ->pluck('name', 'key')
            ->all();
    }
}
