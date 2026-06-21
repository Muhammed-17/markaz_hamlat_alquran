<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->unsigned()->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->string('phone')->nullable();
            $table->string('second_phone')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('guardian_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['Active', 'Inactive', 'Away'])->default('Active');
            $table->foreignId('circle_id')->nullable()->constrained()->onDelete('set null');
            $table->string('current_surah')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
