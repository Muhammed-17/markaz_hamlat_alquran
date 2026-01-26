<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPrice;

class SubscriptionPriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            [
                'circle_level' => 'بناء',
                'education_level' => 'ابتدائي',
                'amount' => 100.00,
            ],
            [
                'circle_level' => 'إتقان',
                'education_level' => 'ثانوي',
                'amount' => 150.00,
            ],
            [
                'circle_level' => 'إبداع',
                'education_level' => 'ثانوي',
                'amount' => 200.00,
            ],
        ];

        foreach ($prices as $data) {
            SubscriptionPrice::updateOrCreate(
                [
                    'circle_level' => $data['circle_level'],
                    'education_level' => $data['education_level'],
                ],
                ['amount' => $data['amount']]
            );
        }

        $this->command->info('✅ تم تحميل جدول الأسعار الافتراضية.');
    }
}