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
        Schema::create('competition_examiners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('examiner_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            // يمنع إضافة نفس المختبر أكثر من مرة لنفس المسابقة
            $table->unique(['competition_id', 'examiner_id']);

            $table->index('competition_id');
            $table->index('examiner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_examiners');
    }
};