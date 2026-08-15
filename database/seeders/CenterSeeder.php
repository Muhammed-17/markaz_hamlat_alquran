<?php

namespace Database\Seeders;

use App\Models\Center;
use Illuminate\Database\Seeder;

class CenterSeeder extends Seeder
{
    public function run(): void
    {
        // تثبيت الـ IDs والأسماء المباشرة (العواسجة و الرئيسي)
        Center::updateOrCreate(['id' => 1], ['name' => 'الرئيسي']);
        Center::updateOrCreate(['id' => 2], ['name' => 'العواسجة']);

        $this->command->info('✅ تم إدخال الفروع بنجاح (العواسجة + الرئيسي).');
    }
}