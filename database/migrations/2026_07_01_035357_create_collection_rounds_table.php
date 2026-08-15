<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_rounds', function (Blueprint $table) {
            $table->id();

            // ✅ المشرف الذي أنشأ الالتحصيل وسجّل استلامه من المعلم
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // ✅ المدير الذي أكّد الالتحصيل نهائيًا
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->unsignedTinyInteger('level')->comment('1 = teacher->supervisor, 2 = supervisor->manager');

            $table->foreignId('center_id')->constrained('centers')->cascadeOnDelete();
            $table->foreignId('circle_id')->constrained('circles')->cascadeOnDelete();

            $table->unsignedTinyInteger('round_number')->default(1);
            $table->date('period_month');

            $table->decimal('total_amount', 10, 2)->default(0);
            $table->unsignedInteger('students_count')->default(0);

            // ✅ string بدلاً من enum لتفادي الحاجة لـ migration عند تغيير القيم مستقبلاً
            // القيم المستخدَمة حاليًا في منطق التطبيق: 'pending', 'confirmed'
            $table->string('status', 20)->default('pending');
            $table->timestamp('confirmed_at')->nullable();

            // ✅ ملاحظتان منفصلتان بدلاً من عمود notes واحد
            $table->text('supervisor_note')->nullable()->comment('ملاحظة المشرف عند إنشاء أو تعديل الالتحصيل');
            $table->text('manager_note')->nullable()->comment('ملاحظة المدير توضح سبب بقاء الالتحصيل معلّق');

            // ✅ هل قام المشرف بمعالجة ملاحظة المدير عبر تعديل الالتحصيل؟
            $table->boolean('manager_note_addressed')->default(false)->comment('هل قام المشرف بتعديل الالتحصيل بعد كتابة ملاحظة المدير؟');

            $table->timestamps();

            // Indexes
            $table->index(['circle_id', 'period_month', 'level'], 'idx_circle_period_level');
            $table->index(['status', 'confirmed_by'], 'idx_status_confirmed_by');
            $table->index(['center_id', 'period_month'], 'idx_center_period');
            $table->index('created_by', 'idx_created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_rounds');
    }
};