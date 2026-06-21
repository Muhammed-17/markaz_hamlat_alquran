<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPrice;

class SubscriptionPriceSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ المفتاح هو 'education_stage' (وليس 'education_level') ليطابق اسم
        // العمود الفعلي في جدول subscription_prices بعد migration
        // 2026_06_12_212438_rename_education_level_to_education_stage...
        // كان الـ Seeder يستخدم الاسم القديم فيفشل بـ "Undefined array key".
        $prices = [
            ['circle_level' => 'build',      'education_stage' => 'ابتدائي', 'amount' => 60.00],
            ['circle_level' => 'mastery',    'education_stage' => 'ثانوي',   'amount' => 100.00],
            ['circle_level' => 'creativity', 'education_stage' => 'ثانوي',   'amount' => 100.00],
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
