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
        Schema::create('external_participants', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('national_id', 14)->nullable();

            $table->string('phone', 20)->nullable();
            $table->string('secondary_phone', 20)->nullable();

            $table->string('address')->nullable();

            $table->date('date_of_birth')->nullable();

            $table->string('gender')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('phone');
            $table->unique('national_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_participants');
    }
};