<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Recommendation Templates Table
 * Description: Stores ready-made recommendation sentences keyed by
 * (category, level). The system picks the matching sentence for each
 * section's level when generating a weekly follow-up recommendation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_templates', function (Blueprint $table) {
            $table->id();

            // Which section this sentence belongs to
            $table->enum('category', [
                'new_memorization',  // الحفظ الجديد
                'revision',          // المراجعة
                'old_memorization',  // الحفظ القديم
                'discipline',        // الانضباط
                'tajweed',           // التجويد
                'foundation',        // مستوى التأسيس
                'educational_lesson', // الدرس التربوي
                'attendance',        // نسبة الالتزام بالحضور
            ])->comment('The weekly follow-up section this sentence applies to');

            // Which level this sentence corresponds to
            $table->enum('level', [
                'ممتاز',
                'جيد جداً',
                'جيد',
                'مقبول',
                'ضعيف',
            ])->comment('The assessment level this sentence corresponds to');

            // The ready-made sentence
            $table->text('sentence')
                ->comment('Ready-made recommendation sentence for this category/level combination');

            $table->boolean('is_active')
                ->default(true)
                ->comment('Allows disabling a sentence without deleting it');

            $table->timestamps();

            // Ensure only one active sentence per category+level combination
            $table->unique(['category', 'level'], 'uniq_recommendation_template_category_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_templates');
    }
};
