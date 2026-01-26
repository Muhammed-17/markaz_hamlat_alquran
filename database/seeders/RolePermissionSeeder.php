<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Admin: جميع الصلاحيات
        Role::findByName('admin')->givePermissionTo(Permission::all());

        // Supervisor
        Role::findByName('supervisor')->syncPermissions([
            'view dashboard',
            'view students',
            'view circles',
            'view attendance',
            'view subscriptions',
            'view users',
        ]);

        // Teacher
        Role::findByName('teacher')->syncPermissions([
            'view dashboard',
            'view own attendance',
            'create attendance',
            'edit attendance',
            'view own subscriptions',
            'view students',
        ]);

        // Guardian
        Role::findByName('guardian')->syncPermissions([
            'view dashboard',
            'view students',
            'view attendance',
            'view own subscriptions',
        ]);

        $this->command->info('✅ توزيع الصلاحيات على الأدوار تم بنجاح.');
    }
}