<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Surah Tests Table
 *
 * Description:
 * Represents a Quran Surah test session.
 * The test may be individual or group.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surah_tests', function (Blueprint $table) {
            $table->id();

            // Test type
            $table->string('test_type', 20)
                ->comment('group or individual');

            // Center
            $table->foreignId('center_id')
                ->constrained('centers')
                ->cascadeOnDelete();

            // Circle (nullable for individual test)
            $table->foreignId('circle_id')
                ->nullable()
                ->constrained('circles')
                ->nullOnDelete();

            // Teacher
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->restrictOnDelete();

            // Surah
            $table->foreignId('surah_id')
                ->constrained('surahs')
                ->restrictOnDelete();

            // Test date
            $table->date('test_date');

            // General notes
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('test_type');
            $table->index('center_id');
            $table->index('circle_id');
            $table->index('teacher_id');
            $table->index('surah_id');
            $table->index('test_date');

            $table->index(
                ['circle_id', 'test_date'],
                'idx_surah_tests_circle_date'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surah_tests');
    }
};
