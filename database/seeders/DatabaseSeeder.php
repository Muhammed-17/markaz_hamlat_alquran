<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CenterSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            SubscriptionPriceSeeder::class,
            AdminUserSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            SubscriptionSeeder::class,
            LateStudentsSeeder::class,
            SurahSeeder::class,
            RecommendationTemplateSeeder::class,
            AverageLevelsSeeder::class,
        ]);
    }
}
