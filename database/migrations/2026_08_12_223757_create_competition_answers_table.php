<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('competition_answers', function (Blueprint $table) {
            $table->id();

            // المشارك
            $table->foreignId('competition_participant_id')
                ->constrained()
                ->cascadeOnDelete();

            // السؤال
            $table->foreignId('competition_question_id')
                ->constrained()
                ->cascadeOnDelete();

            // المختبر الخارجي
            $table->foreignId('competition_examiner_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // المشرف أو المدير من داخل النظام
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // درجة السؤال
            $table->decimal('score', 5, 2)->default(0);

            // هل أجاب على السؤال
            $table->boolean('answered')->default(true);

            // أخطاء الحفظ
            $table->unsignedInteger('memorization_mistakes')->nullable()->default(0);

            // أخطاء التشكيل
            $table->unsignedInteger('tashkeel_mistakes')->nullable()->default(0);

            // ملاحظات المختبر
            $table->text('notes')->nullable();

            $table->timestamps();

            // يمنع تكرار تقييم نفس السؤال للمشارك نفسه
            $table->unique(
                ['competition_participant_id', 'competition_question_id'],
                'cp_answers_participant_question_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_answers');
    }
};
