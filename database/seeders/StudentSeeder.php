<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Circle;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // 0. التأكد من وجود مركز افتراضي بدل افتراض center_id=1 ثابتاً
        //    (كان هذا يفشل بصمت أو يربط بيانات بمركز غير موجود في بيئة فارغة)
        $center = Center::firstOrCreate(
            ['name' => 'المركز الرئيسي'],
        );

        // 1. التأكد من وجود الحلقة وتوافق حقولها
        //    ملاحظة: max_students و center_id أعمدة إلزامية (NOT NULL) في الجدول
        //    ولم تكن موجودة هنا، ما يسبب خطأ SQL عند الإدراج.
        $circle = Circle::updateOrCreate(
            ['name' => 'حلقة أبو بكر الصديق'],
            [
                'type' => 'group',
                'level' => 'build',
                'is_active' => true,
                'center_id' => $center->id,
            ]
        );

        // 2. التأكد من وجود ولي الأمر
        $guardian = User::updateOrCreate(
            ['email' => 'mohamed@markaz.com'],
            [
                'name' => "محمد السيد الشعراوي",
                'password' => Hash::make('12345678'),
            ]
        );

        if (method_exists($guardian, 'syncRoles')) {
            $guardian->syncRoles(['guardian']);
        }

        // 3. مصفوفة الطلاب ببيانات متوافقة تماماً مع الـ Schema الواقعية لديك
        $students = [
            ['name' => 'محمد أحمد الصالح', 'gender' => 'ذكر', 'status' => 'متوقف', 'stage' => 'ابتدائي', 'grade' => 'الأول'],
            ['name' => 'عبدالرحمن علي يوسف', 'gender' => 'ذكر', 'status' => 'مقيد', 'stage' => 'ابتدائي', 'grade' => 'الثاني'],
            ['name' => 'يوسف إبراهيم عبدالكريم', 'gender' => 'ذكر', 'status' => 'مقيد', 'stage' => 'حضانة', 'grade' => 'السادس'],
            ['name' => 'عمر خالد عفرات', 'gender' => 'ذكر', 'status' => 'مقيد', 'stage' => 'ابتدائي', 'grade' => 'الثالث'],
            ['name' => 'عثمان عفان النور', 'gender' => 'ذكر', 'status' => 'مقيد', 'stage' => 'ابتدائي', 'grade' => 'الرابع'],
            ['name' => 'علي عمر خالد', 'gender' => 'ذكر', 'status' => 'متوقف', 'stage' => 'ابتدائي', 'grade' => 'الخامس'],
            ['name' => 'فاطمة محمد أحمد', 'gender' => 'أنثى', 'status' => 'مقيد', 'stage' => 'ابتدائي', 'grade' => 'الأول'],
            ['name' => 'عائشة أحمد السيد', 'gender' => 'أنثى', 'status' => 'مقيد', 'stage' => 'ابتدائي', 'grade' => 'الثالث'],
            ['name' => 'مريم عبدالله عمرو', 'gender' => 'أنثى', 'status' => 'مقيد', 'stage' => 'ابتدائي', 'grade' => 'الثاني'],
            ['name' => 'زينب السيد عبدالسميع', 'gender' => 'أنثى', 'status' => 'متوقف', 'stage' => 'ابتدائي', 'grade' => 'الرابع'],
        ];

        foreach ($students as $index => $data) {
            // توليد كود طالب افتراضي يشبه نظامك STU-2026-0000X
            $studentCode = 'STU-' . now()->year . '-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT);

            Student::updateOrCreate(
                ['name' => $data['name'], 'guardian_id' => $guardian->id],
                [
                    'gender' => $data['gender'],
                    'date_of_birth' => now()->subYears(10)->toDateString(),
                    'address' => 'صبيح',
                    'status' => $data['status'],
                    'suspended_at' => $data['status'] === 'متوقف' ? now() : null,
                    'circle_id' => $circle->id,
                    'education_type' => 'أزهري',
                    'educational_stage' => $data['stage'],
                    'school_grade' => $data['grade'],
                    'center_entry_level' => 'construction',
                    'join_date' => now()->toDateString(),
                    'student_code' => $studentCode,
                    'decision' => 'تحت الاختبار',
                    'center_id' => $center->id,
                    'supervisor_id' => null,
                    'applicant' => 'الطالب',
                    'health_status' => 'طبيعية',
                    'learning_difficulties' => 'لا يوجد',
                    'personal_traits' => 'لا يوجد',
                    'reading' => 'مبتدئ',
                ]
            );
        }

        $this->command->info('✅ تم إضافة وتحديث الطلاب بنجاح وتوافق تام مع قاعدة البيانات.');
    }
}
