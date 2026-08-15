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
        Schema::create('competition_levels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('level_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            // يمنع إضافة نفس المستوى أكثر من مرة لنفس المسابقة
            $table->unique(['competition_id', 'level_id']);

            $table->index('competition_id');
            $table->index('level_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_levels');
    }
};