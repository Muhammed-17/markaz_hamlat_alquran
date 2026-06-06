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
        if (!Schema::hasColumn('subscription_prices', 'school_grade')) {
            Schema::table('subscription_prices', function (Blueprint $table) {
                $table->string('school_grade')->nullable()->after('education_level');
            });
        }

        try {
            Schema::table('subscription_prices', function (Blueprint $table) {
                $table->dropUnique(['circle_level', 'education_level']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('subscription_prices', function (Blueprint $table) {
                $table->unique(['circle_level', 'education_level', 'school_grade'], 'sub_prices_circle_edu_grade_unique');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_prices', function (Blueprint $table) {
            $table->dropUnique('sub_prices_circle_edu_grade_unique');
            $table->unique(['circle_level', 'education_level']);
            $table->dropColumn('school_grade');
        });
    }
};
