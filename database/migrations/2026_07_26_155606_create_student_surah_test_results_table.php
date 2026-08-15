<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Student Surah Test Results Table
 *
 * Description:
 * Stores each student's result for a Surah test.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_surah_test_results', function (Blueprint $table) {
            $table->id();

            // Parent test
            $table->foreignId('surah_test_id')
                ->constrained('surah_tests')
                ->cascadeOnDelete();

            // Student
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Number of prompts/openings by examiner
            $table->unsignedSmallInteger('prompt_errors')
                ->default(0)
                ->comment('Number of prompts by examiner');

            // Number of pronunciation/tashkeel mistakes
            $table->unsignedSmallInteger('tashkeel_errors')
                ->default(0)
                ->comment('Number of pronunciation mistakes');

            // Final percentage
            $table->unsignedTinyInteger('percentage')
                ->nullable()
                ->comment('Final percentage (0-100)');

            // Final evaluation level
            $table->string('level', 20)
                ->nullable()
                ->comment('Final evaluation level');

            // Notes
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('surah_test_id');
            $table->index('student_id');
            $table->index('percentage');
            $table->index('level');

            // Prevent duplicate student in the same test session
            $table->unique(
                ['surah_test_id', 'student_id'],
                'unique_student_surah_test'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_surah_test_results');
    }
};
