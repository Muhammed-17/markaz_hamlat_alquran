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
        Schema::create('competition_examiner_levels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_examiner_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('competition_level_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            // يمنع إسناد نفس المستوى للمختبر أكثر من مرة
            $table->unique(
                ['competition_examiner_id', 'competition_level_id'],
                'cp_examiner_level_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_examiner_levels');
    }
};
