<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Center;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ مركز افتراضي حقيقي بدل ترك center_id فارغاً.
        // CenterScope (راجع app/Models/Scopes/CenterScope.php) يحجب تماماً
        // أي teacher/supervisor بـ center_id IS NULL عبر whereRaw('1 = 0')،
        // فكان أي حساب يُنشأ بهذا الـ Seeder بدون center_id يدخل النظام بنجاح
        // لكن لا يرى أي بيانات إطلاقاً — حساب معطّل وظيفياً بالكامل.
        $center = Center::firstOrCreate(['name' => 'المركز الرئيسي']);

        $staffMembers = [
            [
                'name' => 'سعد أحمد سعد الشعراوي',
                'email' => '01212345678@markaz.com',
                'role' => 'supervisor', // مشرف
                'password' => 'password',
            ],
            [
                'name' => 'عبدالفتاح أحمد سعدون',
                'email' => '01150175090@markaz.com',
                'role' => 'teacher', // معلم
                'password' => 'password',
            ],
            [
                'name' => 'عبدالبديع أبوالمعاطي',
                'email' => 'adbelbadea@markaz.com',
                'role' => 'supervisor', // مشرف
                'password' => 'password',
            ]
        ];

        foreach ($staffMembers as $member) {
            // Create or update the user
            $user = User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make($member['password']),
                    'status' => 'active', // assuming status exists
                    // ✅ users.center_id (راجع CenterScope وUser model) يُستخدم
                    // أيضاً في أماكن أخرى من النظام لتحديد فرع المستخدم.
                    'center_id' => $center->id,
                ]
            );

            // Assign role
            if (!$user->hasRole($member['role'])) {
                $user->assignRole($member['role']);
            }

            // Create teacher record
            // ✅ center_id إلزامي هنا — بدونه يصبح هذا المستخدم غير قادر على
            // رؤية أي حلقة/طالب/معلم رغم تسجيل دخوله بنجاح.
            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'center_id' => $center->id,
                ]
            );
        }

        $this->command->info('✅ تم إنشاء أعضاء فريق العمل بنجاح.');
    }
}
