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
        Schema::table('student_itqan_details', function (Blueprint $table) {
            // حذف العمود المطلوب
            $table->dropColumn('memorized_texts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_itqan_details', function (Blueprint $table) {
            // كود التراجع (إعادة إنشاء العمود في حال عمل Rollback)
            $table->text('memorized_texts')->nullable();
        });
    }
};