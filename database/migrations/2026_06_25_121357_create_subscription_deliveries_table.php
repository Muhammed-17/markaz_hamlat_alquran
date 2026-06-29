<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('month');
            $table->decimal('circle_total_amount', 10, 2);
            $table->decimal('admin_collected_amount', 10, 2)->default(0);
            $table->decimal('expected_from_teacher', 10, 2);
            $table->decimal('delivered_by_teacher', 10, 2);
            $table->boolean('confirmed_by_admin')->default(false);
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_deliveries');
    }
};
