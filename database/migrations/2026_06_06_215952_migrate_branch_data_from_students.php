<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // تم إلغاؤها — كانت backfill لعمود branch_id القديم (المربوط بـ centers خطأً).
        // الـ backfill الصحيح بيحصل في ميجريشن circles الجديدة (الفرع الرئيسي الافتراضي).
    }

    public function down(): void
    {
        //
    }
};