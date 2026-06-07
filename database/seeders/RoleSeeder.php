<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // إضافة manager إلى مصفوفة الأدوار
        $roles = ['admin', 'manager', 'supervisor', 'teacher', 'guardian'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $this->command->info('✅ الأدوار الأساسية تم إنشاؤها وتحديثها.');
    }
}
