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
        Schema::create('collection_round_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_round_id')->constrained('collection_rounds')->cascadeOnDelete();
            $table->text('description');
            $table->foreignId('created_by')->constrained('users');

            // إضافة حقل created_at فقط لأن السجل لا يُعدَّل
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_round_logs');
    }
};
