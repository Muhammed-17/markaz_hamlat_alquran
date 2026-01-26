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
        Schema::create('subscription_prices', function (Blueprint $table) {
            $table->id();
            $table->enum('circle_level', ['Foundation', 'Advanced', 'Creative', 'بناء', 'إتقان', 'إبداع']);
            $table->enum('education_level', ['Primary', 'Secondary', 'High School', 'ابتدائي', 'متوسط', 'ثانوي']);
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->unique(['circle_level', 'education_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_prices');
    }
};
