ؤ<?php

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
                'type' => 'group',
                'level' => 'build',
                'is_active' => true,
                'notes' => 'حلقة تعليمية للمبتدئين',
            ]
        );

        // 2. Ensure Guardian User exists
        $guardian = User::updateOrCreate(
            ['email' => 'mohamed@markaz.com'],
            [
                'name' => "محمد السيد الشعراوي",
                'password' => Hash::make('12345678'),
            ]
        );

        // 3. Students Data
        $students = [
            ['name' => 'محمد أحمد الصالح', 'gender' => 'Male'],
            ['name' => 'عبدالرحمن علي يوسف', 'gender' => 'Male'],
            ['name' => 'يوسف إبراهيم عبدالكريم', 'gender' => 'Male'],
            ['name' => 'عمر خالد عفرات', 'gender' => 'Male'],
            ['name' => 'عثمان عفان النور', 'gender' => 'Male'],
            ['name' => 'علي عمر خالد', 'gender' => 'Male'],
            ['name' => 'حسن محمد أحمد', 'gender' => 'Female'],
            ['name' => 'أبوعبيدة أحمد السيد ', 'gender' => 'Female'],
            ['name' => 'سيف عبدالله عمرو', 'gender' => 'Female'],
            ['name' => 'عمرو السيد عبدالسميع', 'gender' => 'Female'],
        ];

        foreach ($students as $data) {
            Student::updateOrCreate(
                ['name' => $data['name'], 'guardian_id' => $guardian->id],
                [
                    'gender' => $data['gender'],
                    'education_level' => 'primary',
                    'phone' => '05' . rand(10000000, 99999999),
                    'circle_id' => $circle->id,
                    'current_surah' => 'الفاتحة',
                    'enrollment_date' => now()->toDateString(),
                    'status' => 'active',
                    'date_of_birth' => now()->subYears(10)->toDateString(),
                ]
            );
        }

        $this->command->info('✅ تم إضافة الطلاب بنجاح.');
    }
}
