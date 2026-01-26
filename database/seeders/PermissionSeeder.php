<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // لوحة التحكم
            'view dashboard',

            // الطلاب
            'view students',
            'create students',
            'edit students',
            'delete students',
            'assign student to circle',

            // الحلقات
            'view circles',
            'create circles',
            'edit circles',
            'delete circles',
            'manage circle teachers',

            // الحضور
            'view attendance',
            'create attendance',
            'edit attendance',
            'view own attendance',

            // الاشتراكات
            'view subscriptions',
            'create subscriptions',
            'edit subscriptions',
            'collect subscription',
            'view own subscriptions',

            // المستخدمون
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',

            // جدول الأسعار
            'view prices',
            'edit prices',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('✅ الصلاحيات تم إنشاؤها.');
    }
}