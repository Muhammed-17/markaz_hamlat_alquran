<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. إضافة العمود nullable مبدئيًا عشان نقدر نعمل backfill.
        if (!Schema::hasColumn('circles', 'branch_id')) {
            Schema::table('circles', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('center_id')
                    ->constrained('branches')
                    ->cascadeOnDelete();
            });
        }

        // 2. إنشاء "الفرع الرئيسي" الافتراضي لكل مركز عنده حلقات فعلاً
        //    (updateOrInsert يمنع التكرار لو الميجريشن اتشغلت أكتر من مرة).
        $centerIds = DB::table('circles')->whereNotNull('center_id')->distinct()->pluck('center_id');

        foreach ($centerIds as $centerId) {
            DB::table('branches')->updateOrInsert(
                ['center_id' => $centerId, 'name' => 'الفرع الرئيسي'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // 3. تحويل كل حلقة لتاخد "الفرع الرئيسي" بتاع مركزها الحالي.
        DB::statement("
            UPDATE circles c
            JOIN branches b ON b.center_id = c.center_id AND b.name = 'الفرع الرئيسي'
            SET c.branch_id = b.id
            WHERE c.branch_id IS NULL AND c.center_id IS NOT NULL
        ");

        // ملاحظة: عمود center_id اتسيب موجود عمدًا (قرار غير نهائي بعد) —
        // لو اتقرر لاحقًا حذفه، دي هتبقى ميجريشن منفصلة بعد التأكد إن
        // كل الكود/الصلاحيات بقت بتعتمد على branch->center_id بدل circles.center_id مباشرة.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('circles', 'branch_id')) {
            Schema::table('circles', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
