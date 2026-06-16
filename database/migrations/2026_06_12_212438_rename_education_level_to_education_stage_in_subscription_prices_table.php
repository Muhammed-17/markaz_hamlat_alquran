<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_prices', function (Blueprint $table) {
            $table->renameColumn('education_level', 'education_stage');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_prices', function (Blueprint $table) {
            $table->renameColumn('education_stage', 'education_level');
        });
    }
};
