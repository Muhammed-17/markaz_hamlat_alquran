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
        Schema::table('teachers', function (Blueprint $table) {
            // إضافة الحقل بعد عمود الـ center_id، وجعله false بشكل افتراضي
            $table->boolean('is_administrative')
                ->default(false)
                ->after('center_id')
                ->comment('هل للمعلم صفة إدارية داخل المركز؟');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // تراجع عن إضافة العمود في حال عمل rollback
            $table->dropColumn('is_administrative');
        });
    }
};
