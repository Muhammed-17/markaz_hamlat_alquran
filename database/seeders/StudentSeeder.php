<?php

namespace Database\Seeders;

use App\Models\Circle;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Circle exists
        $circle = Circle::updateOrCreate(
            ['name' => 'حلقة أبو بكر الصديق'],
            [
                'type' => 'جماعية',
                'level' => 'بناء',
                'is_active' => true,
                'notes' => 'حلقة تعليمية للمبتدئين',
            ]
        );

        // 2. Ensure Guardian User exists
        $guardian = User::updateOrCreate(
            ['email' => 'guardian@markaz.com'],
            [
                'name' => 'ولي أمر افتراضي',
                'password' => Hash::make('password'),
            ]
        );

        // 3. Students Data
        $students = [
            ['name' => 'محمد أحمد الصالح', 'gender' => 'Male'],
            ['name' => 'عبد الرحمن علي يوسف', 'gender' => 'Male'],
            ['name' => 'يوسف إبراهيم الخليل', 'gender' => 'Male'],
            ['name' => 'عمر خالد الوليد', 'gender' => 'Male'],
            ['name' => 'عثمان عفان النور', 'gender' => 'Male'],
            ['name' => 'علي أبي طالب', 'gender' => 'Male'],
            ['name' => 'فاطمة الزهراء حسن', 'gender' => 'Female'],
            ['name' => 'مريم عمران عيسى', 'gender' => 'Female'],
            ['name' => 'عائشة عبدالله بكر', 'gender' => 'Female'],
            ['name' => 'خديجة خويلد أسد', 'gender' => 'Female'],
        ];

        foreach ($students as $data) {
            Student::updateOrCreate(
                ['name' => $data['name'], 'guardian_id' => $guardian->id],
                [
                    'gender' => $data['gender'],
                    'education_level' => 'Primary',
                    'phone' => '05' . rand(10000000, 99999999),
                    'circle_id' => $circle->id,
                    'current_surah' => 'الفاتحة',
                    'enrollment_date' => now()->toDateString(),
                    'status' => 'Active',
                    'date_of_birth' => now()->subYears(10)->toDateString(),
                ]
            );
        }

        $this->command->info('✅ تم إضافة الطلاب بنجاح.');
    }
}
