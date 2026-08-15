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
        Schema::create('competition_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_participant_id')
                ->constrained()
                ->cascadeOnDelete();

            // المجموع النهائي
            $table->decimal('total_score', 5, 2)->default(0);

            // الترتيب النهائي داخل المستوى
            $table->unsignedInteger('rank')->nullable();

            $table->timestamps();

            // يمنع وجود أكثر من نتيجة لنفس المشارك
            $table->unique('competition_participant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_results');
    }
};