<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Subscription;
use Carbon\Carbon;

class LateStudentsSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ عدّل join_date للطلاب المقيدين لتواريخ قديمة
        $students = Student::where('status', 'مقيد')->get();

        $joinDates = [
            '2025-01-01',
            '2025-03-01',
            '2024-09-01',
            '2025-06-01',
            '2024-12-01',
            '2025-08-01',
            '2025-02-01',
        ];

        foreach ($students as $index => $student) {
            $student->update([
                'join_date' => $joinDates[$index] ?? '2025-01-01',
            ]);
        }

        $this->command->info('✅ تم تحديث join_date للطلاب');

        // ✅ احذف الاشتراكات الموجودة وأنشئ جديدة ببعض الشهور بس
        // عشان يظهر تعثر حقيقي
        $firstStudent = Student::where('status', 'مقيد')->first();
        if (!$firstStudent) return;

        // امسح اشتراكاته القديمة
        Subscription::where('student_id', $firstStudent->id)->delete();

        // أضف اشتراك واحد بس من 6 شهور — يعني 5 شهور متأخرة
        Subscription::create([
            'student_id'     => $firstStudent->id,
            'circle_id'      => $firstStudent->circle_id,
            'teacher_id'     => 1,
            'month'          => '2025-01-01',
            'status'         => 'مدفوع',
            'amount'         => 60,
            'payment_method' => 'cash',
            'paid_at'        => now(),
            'collected_by'   => 1,
        ]);

        $this->command->info('✅ تم إنشاء بيانات التعثر');
    }
}