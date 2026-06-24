<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // مسح الكاش لتفادي تداخل البيانات القديمة
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // هيكلة الصلاحيات: [ الصلاحية بالإنجليزية => [الاسم العربي, المجموعة] ]
        $permissionMap = [
            // ─────────────────────────────────────────────────────────
            // النظام واللوحة (system)
            // ─────────────────────────────────────────────────────────
            'view dashboard'              => ['عرض لوحة التحكم', 'system'],
            'view notifications'          => ['عرض الإشعارات', 'system'],
            'view settings'               => ['عرض الإعدادات', 'system'],
            'edit profile'                => ['تعديل الملف الشخصي', 'system'],
            'view reports'                => ['عرض التقارير', 'system'],
            'export data'                 => ['تصدير البيانات', 'system'],

            // ─────────────────────────────────────────────────────────
            // المستخدمين (users)
            // ─────────────────────────────────────────────────────────
            'view users'                  => ['عرض المستخدمين', 'users'],
            'create users'                => ['إضافة مستخدمين', 'users'],
            'edit users'                  => ['تعديل المستخدمين', 'users'],
            'delete users'                => ['حذف المستخدمين', 'users'],
            'manage roles'                => ['إدارة الأدوار والصلاحيات', 'users'],

            // ─────────────────────────────────────────────────────────
            // الفروع والمراكز (centers)
            // ─────────────────────────────────────────────────────────
            'view centers'                => ['عرض الفروع المشترك بها', 'centers'],
            'manage centers'              => ['إدارة الفروع (إضافة/تعديل/حذف)', 'centers'],

            // ─────────────────────────────────────────────────────────
            // الحلقات (circles)
            // ─────────────────────────────────────────────────────────
            'view circles'                => ['عرض الحلقات', 'circles'],
            'create circles'              => ['إنشاء حلقات جديدة', 'circles'],
            'edit circles'                => ['تعديل الحلقات', 'circles'],
            'delete circles'              => ['حذف الحلقات', 'circles'],
            'manage circle teachers'      => ['تعيين وإدارة معلمي الحلقات', 'circles'],

            // ─────────────────────────────────────────────────────────
            // المعلمين (teachers)
            // ─────────────────────────────────────────────────────────
            'view teachers'               => ['عرض المعلمين', 'teachers'],
            'create teachers'             => ['إضافة معلمين', 'teachers'],
            'edit teachers'               => ['تعديل بيانات المعلمين', 'teachers'],
            'delete teachers'             => ['حذف معلمين', 'teachers'],
            'toggle teacher status'       => ['تنشيط / إيقاف المعلم', 'teachers'],

            // ─────────────────────────────────────────────────────────
            // الطلاب (students)
            // ─────────────────────────────────────────────────────────
            'view students'               => ['عرض الطلاب', 'students'],
            'create students'             => ['إضافة طلاب', 'students'],
            'edit students'               => ['تعديل بيانات الطلاب', 'students'],
            'delete students'             => ['حذف طلاب', 'students'],
            'assign student to circle'    => ['تسكين الطالب في حلقة', 'students'],
            'manage student status'       => ['تحديث حالة الطالب', 'students'],

            // ─────────────────────────────────────────────────────────
            // الحضور والغياب (attendance)
            // ─────────────────────────────────────────────────────────
            'view attendance'             => ['عرض سجلات الحضور', 'attendance'],
            'create attendance'           => ['تسجيل حضور وغياب', 'attendance'],
            'edit attendance'             => ['تعديل الحضور والغياب', 'attendance'],
            'view own attendance'         => ['عرض حضور الأبناء الشخصي', 'attendance'],

            // ─────────────────────────────────────────────────────────
            // الاشتراكات والمالية (subscriptions)
            // ─────────────────────────────────────────────────────────
            'view subscriptions'          => ['عرض الاشتراكات والمدفوعات', 'subscriptions'],
            'create subscriptions'         => ['تسجيل دفع اشتراك', 'subscriptions'],
            'edit subscriptions'           => ['تعديل سندات الاشتراكات', 'subscriptions'],
            'manage subscription prices'  => ['إدارة وتحديد أسعار الاشتراكات', 'subscriptions'],
            'view subscription prices'    => ['عرض أسعار الاشتراكات', 'subscriptions'],
            'view subscriptions chart'    => ['عرض إحصائيات الرسوم والاشتراكات', 'subscriptions'],
            'view own subscriptions'      => ['عرض مدفوعات الأبناء الشخصية', 'subscriptions'],

            // ─────────────────────────────────────────────────────────
            // أولياء الأمور والأبناء (guardians)
            // ─────────────────────────────────────────────────────────
            'view own children'           => ['عرض الأبناء التابعين', 'guardians'],
        ];

        // 1. إنشاء وتحديث الصلاحيات ببياناتها العربية الكاملة والمجمعات
        foreach ($permissionMap as $name => $details) {
            Permission::updateOrCreate(
                ['name' => $name],
                [
                    'display_name' => $details[0],
                    'group'        => $details[1],
                    'guard_name'   => 'web'
                ]
            );
        }

        // 2. مصفوفة ربط الأدوار بالصلاحيات الخاصة بها (بناءً على التوزيع الخاص بك)
        $rolesPermissions = [
            'admin' => [
                'view dashboard',
                'view notifications',
                'view settings',
                'edit profile',
                'view reports',
                'export data',
                'view users',
                'create users',
                'edit users',
                'delete users',
                'manage roles',
                'view centers',
                'manage centers',
                'view circles',
                'create circles',
                'edit circles',
                'delete circles',
                'manage circle teachers',
                'view teachers',
                'create teachers',
                'edit teachers',
                'delete teachers',
                'toggle teacher status',
                'view students',
                'create students',
                'edit students',
                'delete students',
                'assign student to circle',
                'manage student status',
                'view attendance',
                'create attendance',
                'edit attendance',
                'view subscriptions',
                'create subscriptions',
                'edit subscriptions',
                'manage subscription prices',
                'view subscription prices',
                'view subscriptions chart'
            ],

            'general_manager' => [
                'view dashboard',
                'view notifications',
                'edit profile',
                'view reports',
                'export data',
                'view users',
                'view centers',
                'view circles',
                'create circles',
                'edit circles',
                'delete circles',
                'manage circle teachers',
                'view teachers',
                'create teachers',
                'edit teachers',
                'delete teachers',
                'toggle teacher status',
                'view students',
                'create students',
                'edit students',
                'delete students',
                'assign student to circle',
                'manage student status',
                'view attendance',
                'create attendance',
                'edit attendance',
                'view subscriptions',
                'create subscriptions',
                'edit subscriptions',
                'view subscription prices',
                'view subscriptions chart'
            ],

            'manager' => [
                'view dashboard',
                'view notifications',
                'view settings',
                'edit profile',
                'view centers',
                'view circles',
                'create circles',
                'edit circles',
                'delete circles',
                'manage circle teachers',
                'view teachers',
                'create teachers',
                'edit teachers',
                'delete teachers',
                'toggle teacher status',
                'view students',
                'create students',
                'edit students',
                'delete students',
                'assign student to circle',
                'manage student status',
                'view attendance',
                'create attendance',
                'edit attendance',
                'view subscriptions',
                'create subscriptions',
                'edit subscriptions',
                'view subscription prices',
                'view subscriptions chart'
            ],

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
                'view subscription prices'
            ],

            'teacher' => [
                'view dashboard',
                'view notifications',
                'edit profile',
                'view circles',
                'view students',
                'view attendance',
                'create attendance',
                'view subscriptions'
            ],

            'guardian' => [
                'view notifications',
                'edit profile',
                'view own children',
                'view own attendance',
                'view own subscriptions'
            ],
        ];

        // مسميات الأدوار العربية لـ display_name المتواجد بجدول الـ roles
        $roleDisplayNames = [
            'admin'           => 'مدير النظام',
            'general_manager' => 'مدير عام',
            'manager'         => 'مدير فرع',
            'supervisor'      => 'مشرف',
            'teacher'         => 'معلم',
            'guardian'        => 'ولي أمر',
        ];

        // 3. إنشاء وتحديث الأدوار ومزامنة صلاحياتها
        foreach ($rolesPermissions as $roleName => $rolePermissions) {
            $role = Role::updateOrCreate(
                ['name' => $roleName],
                [
                    'display_name' => $roleDisplayNames[$roleName] ?? $roleName,
                    'guard_name'   => 'web'
                ]
            );

            // مزامنة الصلاحيات المحددة لهذا الدور
            $role->syncPermissions($rolePermissions);

            $this->command->info("✓ تم ضبط الدور [{$roleDisplayNames[$roleName]}] ومزامنة (" . count($rolePermissions) . ") صلاحية بنجاح.");
        }

        // مسح كاش الصلاحيات النهائي ليعمل النظام فوراً بالإعدادات الجديدة
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info('✅ تم تحديث وهيكلة جدول الصلاحيات والأدوار (display_name & group) بنجاح كامل.');
    }
}
