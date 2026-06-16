<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            // ─────────────────────────────────────────────────────────
            // ADMIN — كل الصلاحيات + إدارة النظام
            // ─────────────────────────────────────────────────────────
            'admin' => [
                // system
                'view dashboard',
                'view notifications',
                'view settings',
                'edit profile',
                'view reports',
                'export data',

                // users
                'view users',
                'create users',
                'edit users',
                'delete users',
                'manage roles',

                // centers
                'view centers',
                'view all centers',
                'manage centers',

                // circles
                'view circles',
                'view all circles',
                'create circles',
                'edit circles',
                'delete circles',
                'manage circle teachers',

                // teachers
                'view teachers',
                'view all teachers',
                'create teachers',
                'edit teachers',
                'delete teachers',
                'toggle teacher status',

                // students
                'view students',
                'view all students',
                'create students',
                'edit students',
                'delete students',
                'assign student to circle',
                'manage student status',

                // attendance
                'view attendance',
                'create attendance',
                'edit attendance',

                // subscriptions
                'view subscriptions',
                'create subscriptions',
                'edit subscriptions',
                'manage subscription prices',
                'view subscription prices',
                'view subscriptions chart',
            ],

            // ─────────────────────────────────────────────────────────
            // GENERAL MANAGER — المدير العام
            // يرى كل الفروع لكن لا يتحكم في النظام (users/roles/settings)
            // ─────────────────────────────────────────────────────────
            'general_manager' => [
                // system
                'view dashboard',
                'view notifications',
                'edit profile',
                'view reports',
                'export data',
                // ❌ view settings   — للـ admin فقط
                // ❌ manage roles    — للـ admin فقط

                // users — يرى فقط بدون إدارة
                'view users',
                // ❌ create/edit/delete users — للـ admin فقط
                // ❌ manage roles             — للـ admin فقط

                // centers — يرى كل الفروع
                'view centers',
                'view all centers',
                // ❌ manage centers — للـ admin فقط

                // circles — يرى ويدير في كل الفروع
                'view circles',
                'view all circles',
                'create circles',
                'edit circles',
                'delete circles',
                'manage circle teachers',

                // teachers — يرى ويدير في كل الفروع
                'view teachers',
                'view all teachers',
                'create teachers',
                'edit teachers',
                'delete teachers',
                'toggle teacher status',

                // students — يرى ويدير في كل الفروع
                'view students',
                'view all students',
                'create students',
                'edit students',
                'delete students',
                'assign student to circle',
                'manage student status',

                // attendance
                'view attendance',
                'create attendance',
                'edit attendance',

                // subscriptions
                'view subscriptions',
                'create subscriptions',
                'edit subscriptions',
                'view subscription prices',
                'view subscriptions chart',
                // ❌ manage subscription prices — للـ admin فقط
            ],

            // ─────────────────────────────────────────────────────────
            // MANAGER — مدير الفرع
            // يدير فرعه فقط
            // ─────────────────────────────────────────────────────────
            'manager' => [
                // system
                'view dashboard',
                'view notifications',
                'view settings',
                'edit profile',

                // centers
                'view centers',

                // circles
                'view circles',
                'view all circles',
                'create circles',
                'edit circles',
                'delete circles',
                'manage circle teachers',

                // teachers
                'view teachers',
                'view all teachers',
                'create teachers',
                'edit teachers',
                'delete teachers',
                'toggle teacher status',

                // students
                'view students',
                'create students',
                'edit students',
                'delete students',
                'assign student to circle',
                'manage student status',

                // attendance
                'view attendance',
                'create attendance',
                'edit attendance',

                // subscriptions
                'view subscriptions',
                'create subscriptions',
                'edit subscriptions',
                'view subscription prices',
                'view subscriptions chart',
            ],

            // ─────────────────────────────────────────────────────────
            // SUPERVISOR — المشرف
            // ─────────────────────────────────────────────────────────
            'supervisor' => [
                'view dashboard',
                'view notifications',
                'edit profile',
                'view circles',
                'view teachers',
                'view students',
                'create students',
                'edit students',
                'assign student to circle',
                'view attendance',
                'create attendance',
                'edit attendance',
                'view subscriptions',
                'view subscription prices',
            ],

            // ─────────────────────────────────────────────────────────
            // TEACHER — المعلم
            // ─────────────────────────────────────────────────────────
            'teacher' => [
                'view dashboard',
                'view notifications',
                'edit profile',
                'view circles',
                'view students',
                'view attendance',
                'create attendance',
                'view subscriptions',
            ],

            // ─────────────────────────────────────────────────────────
            // GUARDIAN — ولي الأمر
            // ─────────────────────────────────────────────────────────
            'guardian' => [
                'view notifications',
                'edit profile',
                'view own children',
                'view own attendance',
                'view own subscriptions',
            ],
        ];

        foreach ($permissions as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            $role->syncPermissions($rolePermissions);

            $this->command->info("✓ [{$roleName}] — " . count($rolePermissions) . " permissions");
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info('✅ تم ضبط جميع الأدوار والصلاحيات بنجاح');
    }
}
