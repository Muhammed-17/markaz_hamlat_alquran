<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // مصفوفة الموظفين الموسعة والموزعة بدقة بين الفروع والأدوار
        $staffMembers = [
            // --- الموظفون السابقون ---
            [
                'name'              => 'سعد أحمد سعد الشعراوي',
                'email'             => '01212345678@markaz.com',
                'role'              => 'supervisor',
                'password'          => '12345678',
                'is_administrative' => 1,
                'center_id'         => 2, // فرع العواسجة
            ],
            [
                'name'              => 'عبدالفتاح أحمد سعدون',
                'email'             => '01150175090@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 2, // فرع العواسجة
            ],
            [
                'name'              => 'عبدالبديع أبوالمعاطي',
                'email'             => 'adbelbadea@markaz.com',
                'role'              => 'supervisor',
                'password'          => '12345678',
                'is_administrative' => 1,
                'center_id'         => 3, // الفرع الرئيسي
            ],

            // --- الموظفون المضافون حديثاً لفرع العواسجة (Center ID: 2) ---
            [
                'name'              => 'محمد جمال عبد الحميد',
                'email'             => 'mohamed.gamal@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 2,
            ],
            [
                'name'              => 'أحمد محمود الرفاعي',
                'email'             => 'ahmed.refaei@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 2,
            ],
            [
                'name'              => 'خالد وليد الشربيني',
                'email'             => 'khaled.sharbini@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 2,
            ],
            [
                'name'              => 'محمود عبد العزيز غانم',
                'email'             => 'mahmoud.ghanem@markaz.com',
                'role'              => 'supervisor',
                'password'          => '12345678',
                'is_administrative' => 1,
                'center_id'         => 2,
            ],
            [
                'name'              => 'مصطفى هاني القاضي',
                'email'             => 'mostafa.qadi@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 2,
            ],

            // --- الموظفون المضافون حديثاً للفرع الرئيسي (Center ID: 3) ---
            [
                'name'              => 'إبراهيم علي الدسوقي',
                'email'             => 'ibrahim.desouky@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 3,
            ],
            [
                'name'              => 'حسن بيومي المتولي',
                'email'             => 'hasan.metwally@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 3,
            ],
            [
                'name'              => 'صلاح الدين الأيوبي جاد',
                'email'             => 'salah.gad@markaz.com',
                'role'              => 'supervisor',
                'password'          => '12345678',
                'is_administrative' => 1,
                'center_id'         => 3,
            ],
            [
                'name'              => 'عبد الرحمن محمد الشافعي',
                'email'             => 'shafei@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 3,
            ],
            [
                'name'              => 'يوسف طارق الباز',
                'email'             => 'youssef.baz@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 3,
            ],
            [
                'name'              => 'بلال عوض القرشي',
                'email'             => 'belal.qurashi@markaz.com',
                'role'              => 'teacher',
                'password'          => '12345678',
                'is_administrative' => 0,
                'center_id'         => 3,
            ]
        ];

        foreach ($staffMembers as $member) {
            // 1. إنشاء أو تحديث بيانات المستخدم الأساسية وربطه بفرعه الصحيح
            $user = User::updateOrCreate(
                ['email' => $member['email']],
                [
                    'name'      => $member['name'],
                    'password'  => Hash::make($member['password']),
                    'status'    => 'active',
                    'center_id' => $member['center_id'],
                ]
            );

            // 2. مزامنة الدور (Role) الخاص بالمستخدم من Spatie
            $user->syncRoles([$member['role']]);

            // 3. إنشاء أو تحديث سجل المعلم (Teacher Record) وربطه بنفس الفرع
            Teacher::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'              => $user->name,
                    'center_id'         => $member['center_id'],
                    'is_administrative' => $member['is_administrative'],
                ]
            );
        }

        $this->command->info('✅ تم تحديث وتوسيع طاقم العمل وتوزيعهم على فرعي العواسجة والرئيسي بنجاح.');
    }
}
