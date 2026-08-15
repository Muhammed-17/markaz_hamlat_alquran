<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Student Weekly Followups Table
 *
 * Description:
 * Each row represents one student's weekly follow-up.
 *
 * Group follow-ups are linked together using batch_id so they can
 * be displayed as a single record in the index page.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_weekly_followups', function (Blueprint $table) {
            $table->id();

            /**
             * Group identifier.
             * All records created from the same create-group request
             * share the same batch_id.
             */
            $table->uuid('batch_id')
                ->comment('Identifier used to group weekly follow-ups created together');

            /**
             * Creation source.
             */
            $table->string('plan_type', 20)
                ->comment('group or individual');

            /**
             * Center.
             */
            $table->unsignedBigInteger('center_id')
                ->nullable()
                ->comment('Center ID');

            /**
             * Circle.
             */
            $table->unsignedBigInteger('circle_id')
                ->nullable()
                ->comment('Circle ID');

            /**
             * Student.
             */
            $table->unsignedBigInteger('student_id')
                ->comment('Student ID');

            /**
             * Teacher.
             */
            $table->unsignedBigInteger('teacher_id')
                ->comment('Teacher ID');

            /**
             * Week.
             */
            $table->date('week_start');

            $table->date('week_end');

            /**
             * Study days.
             */
            $table->json('study_days');

            /**
             * General notes for this student.
             */
            $table->text('notes')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('batch_id', 'idx_followups_batch');
            $table->index('plan_type', 'idx_followups_type');
            $table->index('circle_id', 'idx_followups_circle');
            $table->index('student_id', 'idx_followups_student');
            $table->index('teacher_id', 'idx_followups_teacher');
            $table->index('week_start', 'idx_followups_week_start');
            $table->index('week_end', 'idx_followups_week_end');

            $table->index(
                ['batch_id', 'week_start'],
                'idx_followups_batch_week'
            );

            /*
            |--------------------------------------------------------------------------
            | Prevent duplicate follow-up for same student in same week
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['student_id', 'week_start'],
                'unique_student_week_followup'
            );
        });

        Schema::table('student_weekly_followups', function (Blueprint $table) {

            $table->foreign('circle_id', 'fk_followups_circle')
                ->references('id')
                ->on('circles')
                ->nullOnDelete();

            $table->foreign('student_id', 'fk_followups_student')
                ->references('id')
                ->on('students')
                ->cascadeOnDelete();

            $table->foreign('teacher_id', 'fk_followups_teacher')
                ->references('id')
                ->on('teachers')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_weekly_followups', function (Blueprint $table) {

            $table->dropForeign('fk_followups_circle');
            $table->dropForeign('fk_followups_student');
            $table->dropForeign('fk_followups_teacher');
        });

        Schema::dropIfExists('student_weekly_followups');
    }
};
