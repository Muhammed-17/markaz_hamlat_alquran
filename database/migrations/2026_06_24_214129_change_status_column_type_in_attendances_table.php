<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ توحيد أي قيم غير صالحة قبل تغيير الـ enum
        DB::table('attendances')
            ->whereNotIn('status', ['present', 'absent', 'late', 'excused'])
            ->update(['status' => 'present']);

        // ✅ تعديل الـ enum مباشرة عبر SQL خام — بدون الحاجة لـ doctrine/dbal
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('present', 'absent', 'late', 'excused') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status VARCHAR(255) NOT NULL");
    }
};