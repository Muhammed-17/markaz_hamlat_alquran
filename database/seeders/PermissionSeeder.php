<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'view dashboard',           'display_name' => 'عرض لوحة التحكم'],
            ['name' => 'view students',            'display_name' => 'عرض الطلاب'],
            ['name' => 'create students',          'display_name' => 'إضافة طالب'],
            ['name' => 'edit students',            'display_name' => 'تعديل طالب'],
            ['name' => 'delete students',          'display_name' => 'حذف طالب'],
            ['name' => 'assign student to circle', 'display_name' => 'تسكين طالب بحلقة'],
            ['name' => 'view circles',             'display_name' => 'عرض الحلقات'],
            ['name' => 'create circles',           'display_name' => 'إضافة حلقة'],
            ['name' => 'edit circles',             'display_name' => 'تعديل حلقة'],
            ['name' => 'delete circles',           'display_name' => 'حذف حلقة'],
            ['name' => 'manage circle teachers',   'display_name' => 'إدارة معلمي الحلقة'],
            ['name' => 'view attendance',          'display_name' => 'عرض الحضور'],
            ['name' => 'create attendance',        'display_name' => 'تسجيل حضور'],
            ['name' => 'edit attendance',          'display_name' => 'تعديل حضور'],
            ['name' => 'view own attendance',      'display_name' => 'عرض حضوري'],
            ['name' => 'view subscriptions',       'display_name' => 'عرض الاشتراكات'],
            ['name' => 'create subscriptions',     'display_name' => 'إضافة اشتراك'],
            ['name' => 'edit subscriptions',       'display_name' => 'تعديل اشتراك'],
            ['name' => 'collect subscription',     'display_name' => 'تحصيل اشتراك'],
            ['name' => 'view own subscriptions',   'display_name' => 'عرض اشتراكاتي'],
            ['name' => 'view users',               'display_name' => 'عرض المستخدمين'],
            ['name' => 'create users',             'display_name' => 'إضافة مستخدم'],
            ['name' => 'edit users',               'display_name' => 'تعديل مستخدم'],
            ['name' => 'delete users',             'display_name' => 'حذف مستخدم'],
            ['name' => 'manage roles',             'display_name' => 'إدارة الصلاحيات'],
            ['name' => 'view prices',              'display_name' => 'عرض الأسعار'],
            ['name' => 'edit prices',              'display_name' => 'تعديل الأسعار'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(
                ['name' => $p['name']],
                ['display_name' => $p['display_name']]
            );
        }

        $this->command->info('✅ الصلاحيات تم إنشاؤها.');
    }
}