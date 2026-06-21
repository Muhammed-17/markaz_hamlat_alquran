<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            // ⚠️ RolePermissionSeeder لم يعد يُستدعى هنا عمداً: كان يُشغَّل بعد
            // PermissionSeeder ويستبدل صلاحيات supervisor/teacher/guardian
            // بقوائم مختصرة متعارضة (مثلاً يحذف 'create students' و
            // 'create attendance' من supervisor). راجع التعليق أعلى الملف
            // نفسه لتفاصيل أكثر. PermissionSeeder هو المصدر الوحيد الآن.
            SubscriptionPriceSeeder::class,
            AdminUserSeeder::class,
            StaffSeeder::class,
            StudentSeeder::class,
        ]);
    }
}
