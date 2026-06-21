<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('circles', function (Blueprint $table) {
            // 1. حذف قيد المفتاح الأجنبي أولاً لفك الارتباط
            $table->dropForeign(['supervisor_id']);

            // 2. الآن يمكنك حذف الأعمدة الثلاثة دفعة واحدة بدون مشاكل
            $table->dropColumn(['supervisor_id', 'notes', 'max_students']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circles', function (Blueprint $table) {
            // إعادة إنشاء الأعمدة بالترتيب الأصلي في حال التراجع عن الهجرة
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('center_id');
            $table->text('notes')->nullable()->after('level');
            $table->integer('max_students')->nullable()->after('level');

            // إعادة بناء قيد العلاقة للمفتاح الأجنبي
            $table->foreign('supervisor_id')->references('id')->on('teachers')->onDelete('set null'); // 👈 تأكد من اسم جدول المدرسين (teachers أو users) حسب مشروعك
        });
    }
};
