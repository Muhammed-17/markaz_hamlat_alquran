<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Student Weekly Educational Lessons Table
 *
 * Description:
 * Stores the student's evaluation for the educational lesson
 * assigned to the weekly follow-up.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_weekly_educational_lessons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('weekly_followup_id')
                ->constrained('student_weekly_followups')
                ->cascadeOnDelete();

            // Educational lesson
            $table->foreignId('educational_lesson_id')
                ->constrained('educational_lessons')
                ->restrictOnDelete()
                ->comment('Reference to the educational lesson');

            // Student evaluation
            $table->string('level', 20)
                ->nullable()
                ->comment('Student evaluation for the educational lesson');

            // Teacher notes
            $table->text('notes')
                ->nullable()
                ->comment('Additional teacher notes');

            $table->timestamps();

            // One educational lesson evaluation per weekly follow-up
            $table->unique('weekly_followup_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_weekly_educational_lessons');
    }
};
