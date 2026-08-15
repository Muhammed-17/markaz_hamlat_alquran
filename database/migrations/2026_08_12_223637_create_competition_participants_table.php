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
        Schema::create('competition_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('competition_level_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('center_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('external_participant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('student_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('circle_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedTinyInteger('file_status')
                ->default(0);

            $table->foreignId('tafsir_file_id')
                ->nullable()
                ->constrained('tafsir_files')
                ->nullOnDelete();

            $table->foreignId('supervisor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->unsignedInteger('registration_fee')
                ->default(0);

            $table->timestamps();

            // يمنع تسجيل الطالب نفسه أكثر من مرة في نفس المسابقة
            $table->unique(
                ['competition_id', 'student_id'],
                'cp_participants_comp_student_unique'
            );

            // يمنع تسجيل المشارك الخارجي أكثر من مرة في نفس المسابقة
            $table->unique(
                ['competition_id', 'external_participant_id'],
                'cp_participants_comp_external_unique'
            );

            $table->index('tafsir_file_id');
            $table->index('supervisor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_participants');
    }
};
