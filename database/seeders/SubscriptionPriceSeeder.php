<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPrice;

class SubscriptionPriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            ['circle_level' => 'build',      'education_level' => 'ابتدائي', 'amount' => 60.00],
            ['circle_level' => 'mastery',    'education_level' => 'ثانوي',   'amount' => 100.00],
            ['circle_level' => 'creativity', 'education_level' => 'ثانوي',   'amount' => 100.00],
        ];

        foreach ($prices as $data) {
            SubscriptionPrice::updateOrCreate(
                [
                    'circle_level'    => $data['circle_level'],
                    'education_stage' => $data['education_stage'],
                ],
                ['amount' => $data['amount']]
            );
        }

        $this->command->info('✅ تم تحميل جدول الأسعار الافتراضية.');
    }
}
