<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('circle_assignment_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('circle_id')
                ->constrained('circles')
                ->cascadeOnDelete();

            // تاريخ بداية الانتساب لهذي الحلقة
            $table->date('from_date');

            // تاريخ نهاية الانتساب — null يعني "لا يزال في هذي الحلقة حتى الآن"
            $table->date('to_date')->nullable();

            $table->timestamps();

            // فهرس لتسريع استعلامات: "أي حلقة كان الطالب فيها بتاريخ معين؟"
            $table->index(['student_id', 'from_date', 'to_date'], 'cah_student_date_idx');

            // فهرس لتسريع استعلامات: "من كان في هذي الحلقة بتاريخ معين؟"
            $table->index(['circle_id', 'from_date', 'to_date'], 'cah_circle_date_idx');
        });

        // ✅ تعبئة أولية (backfill): كل طالب له سجل واحد يبدأ من join_date
        // (أو created_at إن لم يوجد join_date) بحلقته الحالية، وto_date = null
        // بما أنه لا تاريخ سابق معروف، هذا أفضل تقدير ممكن دون بيانات تاريخية فعلية.
        $students = DB::table('students')
            ->whereNotNull('circle_id')
            ->select('id', 'circle_id', 'join_date', 'created_at')
            ->get();

        $now = now();
        $rows = [];

        foreach ($students as $student) {
            $fromDate = $student->join_date ?? $student->created_at;

            $rows[] = [
                'student_id' => $student->id,
                'circle_id'  => $student->circle_id,
                'from_date'  => $fromDate,
                'to_date'    => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // إدراج على دفعات لتجنب تجاوز حد عدد المعاملات في استعلام واحد
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('circle_assignment_history')->insert($chunk);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circle_assignment_history');
    }
};