<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
public function run(): void
{
    // تعريف الأدوار مع المسميات العربية المقابلة لها
    $roles = [
        'admin'           => 'مدير النظام',
        'general_manager' => 'مدير عام',
        'manager'         => 'مدير فرع',
        'supervisor'      => 'مشرف',
        'teacher'         => 'معلم',
        'guardian'        => 'ولي أمر',
    ];

    foreach ($roles as $name => $displayName) {
        Role::updateOrCreate(
            ['name' => $name], // البحث بناءً على الاسم الفريد للدور
            [
                'display_name' => $displayName,
                'guard_name'   => 'web' // التأمين والتوافق مع حزمة الصلاحيات
            ]
        );
    }

    $this->command->info('✅ الأدوار الأساسية تم إنشاؤها وتحديث المسميات العربية (display_name) بنجاح.');
}
}
