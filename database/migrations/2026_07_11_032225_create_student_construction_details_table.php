<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_construction_details', function (Blueprint $table) {
            $table->id();
            // Foreign key: Student reference (nullable للخطة الجماعية)
            $table->foreignId('student_id')
                ->nullable()
                ->constrained('students')
                ->onDelete('set null')
                ->comment('Reference to the student (null for group plans)');

            // Foreign key: Circle reference
            $table->foreignId('circle_id')
                ->nullable()
                ->constrained('circles')
                ->onDelete('set null')
                ->comment('Reference to the circle/group');

            // Study system type
            $table->string('study_system', 50)->comment('Study system type: intensive, regular, etc.');

            // Current position in the Quran
            $table->foreignId('current_surah_id')
                ->nullable()
                ->constrained('surahs')
                ->onDelete('set null')
                ->comment('Current Surah the student is memorizing');

            // Plan configurations (nullable — group-system students read their plan from the circle, not this record)
            $table->string('new_memorization_plan')->nullable()->comment('Number of pages for new memorization per week');
            $table->string('revision_plan')->nullable()->comment('Number of pages for revision per week');
            $table->string('old_memorization_plan')->nullable()->comment('Number of pages for old memorization revision per week');
            
            // Placement evaluation result
            $table->text('placement_evaluation')->nullable()->comment('Initial placement test evaluation and notes');

            $table->timestamps();

            // Indexes
            $table->index('student_id');
            $table->index('circle_id');
            $table->index('current_surah_id');
            $table->index('study_system');

            // Ensure one construction detail per student
            $table->unique('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_construction_details');
    }
};
