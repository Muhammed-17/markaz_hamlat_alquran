<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Circle;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. جلب البيانات الأساسية للربط الآمن والمطابق لبيانات النظام
        $students = Student::all();
        $circle   = Circle::first();
        $teacher  = Teacher::first();

        if ($students->isEmpty()) {
            $this->command->warn('⚠️ لا يوجد طلاب في قاعدة البيانات. يرجى تشغيل StudentSeeder أولاً.');
            return;
        }

        // 2. ضبط تواريخ الاستحقاق والسداد:
        // جعل التاريخ بصيغة تاريخ كامل (أول يوم في شهر أبريل) لتجنب خطأ الـ MySQL
        $subscriptionMonth = '2026-04-01'; 
        
        // تاريخ السداد الفعلي يكون في شهر مايو 2026
        $paymentMonthDate = Carbon::create(2026, 5, 1); 

        // الحالات المتاحة وطرق الدفع بناءً على شروط الـ Controller والـ Form
        $statuses = ['مدفوع', 'معفي']; 
        $paymentMethods = ['نقدي', 'تحويل بنكي', 'أخرى'];

        // 3. توليد وحقن البيانات بنفس منطق مصفوفة الـ Store $data
        foreach ($students as $student) {
            
            // اختيار حالة عشوائية للطالب
            $status = $statuses[array_rand($statuses)];
            
            // تحقق منطق الإعفاء (Is Exempt?) المماثل للـ Controller
            $isExempt = ($status === 'معفي');

            // إسناد المعلم المسؤول (teacher_id)
            $targetTeacherId = $student->teacher_id ?? $student->circle?->teacher_id ?? $teacher?->id;

            DB::table('subscriptions')->updateOrInsert(
                [
                    'student_id' => $student->id,
                    'month'      => $subscriptionMonth, // 👈 تاريخ كامل الآن (2026-04-01)
                ],
                [
                    'circle_id'      => $student->circle_id ?? $circle?->id,
                    'teacher_id'     => $targetTeacherId,
                    'status'         => $status,
                    
                    // 'amount' => $isExempt ? 0 : $validated['amount']
                    'amount'         => $isExempt ? 0 : 60.00,
                    
                    // 'payment_method' => $isExempt ? null : ...
                    'payment_method' => $isExempt ? null : $paymentMethods[array_rand($paymentMethods)],
                    
                    // 'paid_at' => تاريخ السداد الفعلي تم في شهر مايو (بين 1 إلى 15 مايو)
                    'paid_at'        => $status === 'مدفوع' ? Carbon::parse($paymentMonthDate)->addDays(rand(1, 15))->format('Y-m-d H:i:s') : null,
                    
                    // 'collected_by' => يحاكي قيام الأدمن بالتسجيل وإسناد القيمة للمعلم المختار
                    'collected_by'   => $status === 'مدفوع' ? $targetTeacherId : null,
                    
                    'notes'          => $isExempt ? 'الطالب معفي من الاشتراك — لا قيمة مطلوبة' : 'تم السداد المتأخر لاشتراك أبريل خلال شهر مايو بنجاح',
                    'created_at'     => Carbon::parse($paymentMonthDate)->setHour(12)->setMinute(0),
                    'updated_at'     => now(),
                ]
            );
        }

        $this->command->info('✅ تم توليد اشتراكات شهر أبريل (بصيغة Date كاملة) وتاريخ السداد في مايو بنجاح دون أخطاء!');
    }
}