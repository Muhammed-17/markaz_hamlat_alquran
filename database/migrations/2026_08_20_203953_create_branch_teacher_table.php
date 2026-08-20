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
        // pivot بسيط: كل صف يعني "هذا المعلم مشرف على هذا الفرع"
        // بدون عمود role (الفرع له نوع مسؤولية واحد فقط حاليًا: إشراف)
        Schema::create('branch_teacher', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['branch_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_teacher');
    }
};
