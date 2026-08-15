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
        Schema::create('student_weekly_tajweed_assessments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('weekly_followup_id')
                ->constrained('student_weekly_followups')
                ->cascadeOnDelete();

            $table->string('level', 20)
                ->nullable()
                ->comment('Tajweed level');

            $table->text('notes')
                ->nullable()
                ->comment('Tajweed notes');

            $table->timestamps();

            $table->unique('weekly_followup_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_weekly_tajweed_assessments');
    }
};
