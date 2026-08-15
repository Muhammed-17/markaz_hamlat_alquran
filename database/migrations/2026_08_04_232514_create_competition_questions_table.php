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
        Schema::create('competition_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_level_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('competition_examiner_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');

            $table->string('type');

            $table->decimal('score', 5, 2);

            $table->foreignId('memorization_from_surah_id')
                ->nullable()
                ->constrained('surahs')
                ->nullOnDelete();

            $table->unsignedSmallInteger('memorization_from_ayah')->nullable();

            $table->foreignId('memorization_to_surah_id')
                ->nullable()
                ->constrained('surahs')
                ->nullOnDelete();

            $table->unsignedSmallInteger('memorization_to_ayah')->nullable();

            $table->text('content')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('type');

            // منع تكرار اسم السؤال لنفس المختبر وفي نفس المستوى
            $table->unique(
                ['competition_level_id', 'competition_examiner_id', 'name'],
                'cp_questions_level_examiner_name_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_questions');
    }
};
