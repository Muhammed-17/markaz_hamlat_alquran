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
            'manage guardians'             => ['إدارة حسابات أولياء الأمور', 'system'],
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


            'create student construction details' => ['إنشاء خطة حلقة', 'students'],
            'edit student construction details' => ['تعديل خطة حلقة', 'students'],
            'delete student construction details' => ['حذف خطة حلقة', 'students'],

            'view student construction details' => ['عرض خطة حلقة', 'students'],

            // ─────────────────────────────────────────────────────────
            // الحضور والغياب (attendance)
            // ─────────────────────────────────────────────────────────
            'view attendance'             => ['عرض سجلات الحضور', 'attendance'],
            'create attendance'           => ['تسجيل حضور وغياب', 'attendance'],
            'edit attendance'             => ['تعديل الحضور والغياب', 'attendance'],
            'delete attendance'           => ['حذف الحضور والغياب', 'attendance'],
            'notify attendance'           => ['إشعار أولياء الأمور بالغياب', 'attendance'],
            'view own attendance'         => ['عرض حضور الأبناء الشخصي', 'attendance'],

            // ─────────────────────────────────────────────────────────
            // الاشتراكات والمالية (subscriptions)
            // ─────────────────────────────────────────────────────────
            'view subscriptions'          => ['عرض الاشتراكات والمدفوعات', 'subscriptions'],
            'create subscriptions'         => ['تسجيل دفع اشتراك', 'subscriptions'],
            'delete subscriptions'         => ['حذف سندات الاشتراكات', 'subscriptions'],
            'view subscriptions chart'    => ['عرض رسوم الاشتراكات', 'subscriptions'],
            'notify unpaid subscriptions' => ['إشعار أولياء الأمور بالاشتراكات المتأخرة', 'subscriptions'],
            'manage subscription prices'  => ['إدارة وتحديد أسعار الاشتراكات', 'subscriptions'],
            'view subscription prices'    => ['عرض أسعار الاشتراكات', 'subscriptions'],
            'view own subscriptions'      => ['عرض مدفوعات الأبناء الشخصية', 'subscriptions'],

            // ─────────────────────────────────────────────────────────
            // التحصيلات (collection rounds)
            // ─────────────────────────────────────────────────────────
            'view collection rounds'      => ['عرض التحصيل', 'collection rounds'],
            'create collection rounds'    => ['إنشاء تحصيل', 'collection rounds'],
            'edit collection rounds'      => ['تعديل التحصيل', 'collection rounds'],
            'delete collection rounds'    => ['حذف التحصيل', 'collection rounds'],
            'confirm collection rounds'   => ['تأكيد التحصيل', 'collection rounds'],

            // ─────────────────────────────────────────────────────────
            // أولياء الأمور (guardians)
            // ─────────────────────────────────────────────────────────
            'view own children'           => ['عرض تفاصيل الأبناء', 'guardians'],
            'view notifications'          => ['عرض الإشعارات', 'guardians'],

            // ─────────────────────────────────────────────────────────
            // متابعة الطلاب الأسبوعية (student weekly followups)
            // ─────────────────────────────────────────────────────────
            'view student weekly followups'     => ['عرض متابعة الطلاب الأسبوعية', 'student weekly followups'],
            'create student weekly followups'   => ['إنشاء متابعة الطلاب الأسبوعية', 'student weekly followups'],
            'edit student weekly followups'     => ['تعديل متابعة الطلاب الأسبوعية', 'student weekly followups'],
            'delete student weekly followups'   => ['حذف متابعة الطلاب الأسبوعية', 'student weekly followups'],

            // ─────────────────────────────────────────────────────────
            // خطط الجلسات الجماعية (group session plans)
            // ─────────────────────────────────────────────────────────
            'view group session plans'    => ['عرض خطط الجلسات الجماعية', 'group session plans'],
            'create group session plans'  => ['إنشاء خطط الجلسات الجماعية', 'group session plans'],
            'edit group session plans'    => ['تعديل خطط الجلسات الجماعية', 'group session plans'],
            'delete group session plans'  => ['حذف خطط الجلسات الجماعية', 'group session plans'],

            // ─────────────────────────────────────────────────────────
            // الملاحظات السلوكية (behavioral notes)
            // ─────────────────────────────────────────────────────────
            'view behavioral notes'       => ['عرض الملاحظات السلوكية', 'behavioral notes'],
            'create behavioral notes'     => ['إنشاء ملاحظات سلوكية', 'behavioral notes'],
            'edit behavioral notes'       => ['تعديل الملاحظات السلوكية', 'behavioral notes'],
            'delete behavioral notes'     => ['حذف الملاحظات السلوكية', 'behavioral notes'],
            'approve behavioral notes'    => ['اعتماد الملاحظات السلوكية', 'behavioral notes'],

            // ─────────────────────────────────────────────────────────
            // اختبارات السور (surah tests)
            // ─────────────────────────────────────────────────────────
            'view surah tests' => ['عرض الاختبارات', 'surah tests'],
            'create surah tests' => ['إنشاء اختبارات', 'surah tests'],
            'update surah tests' => ['تعديل الاختبارات', 'surah tests'],
            'delete surah tests' => ['حذف الاختبارات', 'surah tests'],

            // ─────────────────────────────────────────────────────────
            // المسابقات (competitions)
            // ─────────────────────────────────────────────────────────
            'view competitions'   => ['عرض المسابقات', 'competitions'],
            'create competitions' => ['إنشاء مسابقات', 'competitions'],
            'edit competitions'   => ['تعديل المسابقات', 'competitions'],
            'delete competitions' => ['حذف المسابقات', 'competitions'],

            // ─────────────────────────────────────────────────────────
            // المستويات (levels)
            // ─────────────────────────────────────────────────────────
            'view levels'   => ['عرض المستويات', 'levels'],
            'create levels' => ['إنشاء مستويات', 'levels'],
            'edit levels'   => ['تعديل المستويات', 'levels'],
            'delete levels' => ['حذف المستويات', 'levels'],

            // ─────────────────────────────────────────────────────────
            // المختبرون (examiners)
            // ─────────────────────────────────────────────────────────
            'view examiners'   => ['عرض المختبرين', 'examiners'],
            'create examiners' => ['إنشاء مختبرين', 'examiners'],
            'edit examiners'   => ['تعديل المختبرين', 'examiners'],
            'delete examiners' => ['حذف المختبرين', 'examiners'],
            // ─────────────────────────────────────────────────────────
            // المشاركون من الخارج (external participants)
            // ─────────────────────────────────────────────────────────
            'view external participants'   => ['عرض المشاركين من الخارج', 'external participants'],
            'create external participants' => ['إنشاء مشاركين من الخارج', 'external participants'],
            'edit external participants'   => ['تعديل المشاركين من الخارج', 'external participants'],
            'delete external participants' => ['حذف المشاركين من الخارج', 'external participants'],
            // ─────────────────────────────────────────────────────────
            // مشاركو المسابقات (competition participants)
            // ─────────────────────────────────────────────────────────
            'view competition participants'    => ['عرض مشاركي المسابقات', 'competition participants'],
            'create competition participants'  => ['إضافة مشاركين للمسابقة', 'competition participants'],
            'edit competition participants'    => ['تعديل مشاركي المسابقات', 'competition participants'],
            'delete competition participants'  => ['حذف مشاركي المسابقات', 'competition participants'],
            'export competition participants'  => ['تصدير مشاركي المسابقات', 'competition participants'],
            'examine competition participants' => ['تصحيح/اختبار المشاركين', 'competition participants'],

            // ─────────────────────────────────────────────────────────
            // نتائج المسابقات (competition results)
            // ─────────────────────────────────────────────────────────
            'view competition results'     => ['عرض نتائج المسابقات', 'competition results'],
            'edit competition results'     => ['تعديل نتائج المسابقات', 'competition results'],
            'finalize competition results' => ['اعتماد نتائج المسابقات وإغلاقها', 'competition results'],
            'export competition results'   => ['تصدير نتائج المسابقات', 'competition results'],
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

        // 2. حذف أي صلاحية موجودة في قاعدة البيانات لكن لم تعد موجودة في الكود
        Permission::whereNotIn('name', array_keys($permissionMap))->delete();

        // 3. مصفوفة ربط الأدوار بالصلاحيات الخاصة بها
        $rolesPermissions = [
            'admin' => [
                // system
                'view dashboard',
                'manage guardians',
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
                'manage centers',
                // circles
                'view circles',
                'create circles',
                'edit circles',
                'delete circles',
                // teachers
                'view teachers',
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
                'view student construction details',
                'create student construction details',
                'edit student construction details',
                'delete student construction details',
                // attendance
                'view attendance',
                'create attendance',
                'edit attendance',
                'delete attendance',
                'notify attendance',
                // subscriptions
                'view subscriptions',
                'create subscriptions',
                'delete subscriptions',
                'notify unpaid subscriptions',
                'manage subscription prices',
                'view subscription prices',
                'view subscriptions chart',
                // collection rounds
                'view collection rounds',
                'create collection rounds',
                'edit collection rounds',
                'delete collection rounds',
                'confirm collection rounds',
                // group session plans
                'view group session plans',
                'create group session plans',
                'edit group session plans',
                'delete group session plans',
                // student weekly followups
                'view student weekly followups',
                'create student weekly followups',
                'edit student weekly followups',
                'delete student weekly followups',
                // behavioral notes
                'view behavioral notes',
                'create behavioral notes',
                'edit behavioral notes',
                'delete behavioral notes',
                'approve behavioral notes',
                // guardians
                'view notifications',

                // surah tests
                'view surah tests',
                'create surah tests',
                'update surah tests',
                'delete surah tests',

                // competitions
                'view competitions',
                'create competitions',
                'edit competitions',
                'delete competitions',

                // levels
                'view levels',
                'create levels',
                'edit levels',
                'delete levels',

                // examiners
                'view examiners',
                'create examiners',
                'edit examiners',
                'delete examiners',

                // external participants
                'view external participants',
                'create external participants',
                'edit external participants',
                'delete external participants',

                // competition participants
                'view competition participants',
                'create competition participants',
                'edit competition participants',
                'delete competition participants',
                'export competition participants',

                // competition results
                'view competition results',
                'edit competition results',
                'finalize competition results',
                'export competition results',
            ],

            'general_manager' => [
                // system
                'view dashboard',
                'manage guardians',
                'edit profile',
                'view reports',
                'export data',
                // users
                'view users',
                // centers
                'view centers',
                // circles
                'view circles',
                'edit circles',
                // teachers
                'view teachers',
                // students
                'view students',
                'assign student to circle',
                'manage student status',
                // attendance
                'view attendance',
                'create attendance',
                'edit attendance',
                'delete attendance',
                'notify attendance',
                // subscriptions
                'view subscriptions',
                'create subscriptions',
                'delete subscriptions',
                'notify unpaid subscriptions',
                'view subscriptions chart',
                // group session plans
                'view group session plans',
                'create group session plans',
                'edit group session plans',
                'delete group session plans',
                // student weekly followups
                'view student weekly followups',
                'create student weekly followups',
                'edit student weekly followups',
                'delete student weekly followups',
                // behavioral notes
                'view behavioral notes',
                'create behavioral notes',
                'edit behavioral notes',
                'delete behavioral notes',
                'approve behavioral notes',
                // guardians
                'view notifications',

                // surah tests
                'view surah tests',
                'create surah tests',
                'update surah tests',
                'delete surah tests',

                // competitions
                'view competitions',
                'create competitions',
                'edit competitions',
                'delete competitions',

                // competition participants
                'view competition participants',
                'create competition participants',
                'edit competition participants',

                // competition results
                'view competition results',
                'edit competition results',
                'finalize competition results',
            ],

            'manager' => [
                // system
                'view dashboard',
                'edit profile',
                'manage guardians',
                // centers
                'view centers',
                // circles
                'view circles',
                'edit circles',
                // teachers
                'view teachers',
                // students
                'view students',
                'create students',
                'edit students',
                'assign student to circle',
                'manage student status',
                // attendance
                'view attendance',
                'create attendance',
                'edit attendance',
                'delete attendance',
                'notify attendance',
                // subscriptions
                'view subscriptions',
                'create subscriptions',
                'delete subscriptions',
                'notify unpaid subscriptions',
                'view subscriptions chart',
                // collection rounds
                'create collection rounds',
                'view collection rounds',
                'edit collection rounds',
                'delete collection rounds',
                'confirm collection rounds',
                // group session plans
                'view group session plans',
                'create group session plans',
                'edit group session plans',
                'delete group session plans',
                // student weekly followups
                'view student weekly followups',
                'create student weekly followups',
                'edit student weekly followups',
                'delete student weekly followups',
                // behavioral notes
                'view behavioral notes',
                'create behavioral notes',
                'edit behavioral notes',
                'delete behavioral notes',
                'approve behavioral notes',
                // guardians
                'view notifications',

                // surah tests
                'view surah tests',
                'create surah tests',
                'update surah tests',
                'delete surah tests',

                // competitions
                'view competitions',
                'create competitions',
                'edit competitions',

                // competition participants
                'view competition participants',
                'create competition participants',
                'edit competition participants',

                // competition results
                'view competition results',
                'edit competition results',
                'finalize competition results',
            ],

            'supervisor' => [
                // system
                'edit profile',
                // circles
                'view circles',
                'edit circles',
                // students
                'view students',
                'assign student to circle',
                // attendance
                'view attendance',
                'create attendance',
                'edit attendance',
                // subscriptions
                'view subscriptions',
                'create subscriptions',
                'delete subscriptions',
                // collection rounds
                'create collection rounds',
                'view collection rounds',
                'confirm collection rounds',
                // group session plans
                'view group session plans',
                'create group session plans',
                'edit group session plans',
                'delete group session plans',
                // student weekly followups
                'view student weekly followups',
                'create student weekly followups',
                'edit student weekly followups',
                'delete student weekly followups',
                // behavioral notes
                'view behavioral notes',
                'create behavioral notes',
                'edit behavioral notes',
                'delete behavioral notes',
                'approve behavioral notes',

                // surah tests
                'view surah tests',
                'create surah tests',
                'update surah tests',
                'delete surah tests',

                // competitions
                'view competitions',

                // competition results
                'view competition results',
            ],

            'teacher' => [
                // system
                'edit profile',
                // students
                'view students',
                // circles
                'view circles',
                // attendance
                'view attendance',
                'create attendance',
                // subscriptions
                'view subscriptions',
                'create subscriptions',
                'delete subscriptions',
                // group session plans
                'view group session plans',
                // student weekly followups
                'view student weekly followups',
                'create student weekly followups',
                'edit student weekly followups',
                'delete student weekly followups',
                // behavioral notes
                'view behavioral notes',
                'create behavioral notes',
                'edit behavioral notes',
                'delete behavioral notes',
            ],

            'examiner' => [
                'examine competition participants',
            ],

            'guardian' => [
                // system
                'edit profile',
                // guardians
                'view own children',
                'view own attendance',
                'view own subscriptions',
                'view notifications',
            ],
        ];

        // مسميات الأدوار العربية لـ display_name المتواجد بجدول الـ roles
        $roleDisplayNames = [
            'admin'           => 'مدير النظام',
            'general_manager' => 'مدير عام',
            'manager'         => 'مدير فرع',
            'supervisor'      => 'مشرف',
            'teacher'         => 'معلم',
            'examiner'       => 'مختبر',
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
