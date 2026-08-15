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
        Schema::create('student_weekly_new_memorizations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('weekly_followup_id')
                ->constrained('student_weekly_followups')
                ->cascadeOnDelete();

            $table->foreignId('plan_from_surah_id')
                ->constrained('surahs')
                ->restrictOnDelete();

            $table->unsignedInteger('plan_from_ayah');

            $table->foreignId('plan_to_surah_id')
                ->constrained('surahs')
                ->restrictOnDelete();

            $table->unsignedInteger('plan_to_ayah');

            $table->string('plan_comparison', 20)->nullable();

            $table->text('progress_difference')->nullable();

            $table->string('average_level', 20)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_weekly_new_memorizations');
    }
};
