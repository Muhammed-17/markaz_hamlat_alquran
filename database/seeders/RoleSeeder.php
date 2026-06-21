<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ قائمة الأدوار الأساسية في النظام.
        // general_manager مُضاف هنا لأنه مُستخدم فعلياً في الكونترولرز عبر hasRole()
        // و في PermissionSeeder، لكنه كان مفقوداً من هذه القائمة فلا يُنشأ كـ Role أبداً
        // عند migrate:fresh --seed، فيفشل ربط الصلاحيات به لاحقاً.
        $roles = ['admin', 'general_manager', 'manager', 'supervisor', 'teacher', 'guardian'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $this->command->info('✅ الأدوار الأساسية تم إنشاؤها وتحديثها.');
    }
}
