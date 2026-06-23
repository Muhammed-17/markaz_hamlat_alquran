<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة فهارس مؤقتة/دائمة تدعم الـ Foreign Keys قبل حذف PRIMARY KEY
        DB::statement('ALTER TABLE circle_teacher ADD INDEX circle_teacher_circle_id_idx (circle_id)');
        DB::statement('ALTER TABLE circle_teacher ADD INDEX circle_teacher_teacher_id_idx (teacher_id)');

        // حذف PRIMARY KEY القديم (circle_id, teacher_id) بأمان الآن
        DB::statement('ALTER TABLE circle_teacher DROP PRIMARY KEY');

        // إضافة المفتاح الأساسي الجديد ليشمل role
        DB::statement('ALTER TABLE circle_teacher ADD PRIMARY KEY (circle_id, teacher_id, role)');

        // حذف فهرس circle_id المؤقت لأن PRIMARY KEY الجديد يغطيه (أول عمود فيه)
        DB::statement('ALTER TABLE circle_teacher DROP INDEX circle_teacher_circle_id_idx');

        // الاحتفاظ بفهرس teacher_id بشكل دائم
        // لأن teacher_id ثاني عمود في PRIMARY KEY المركب ولا يُغطى بمفرده تلقائيًا،
        // وهو مطلوب لدعم Foreign Key الخاص بجدول teachers + تحسين الأداء
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE circle_teacher ADD INDEX circle_teacher_circle_id_idx (circle_id)');

        DB::statement('ALTER TABLE circle_teacher DROP PRIMARY KEY');

        DB::statement('ALTER TABLE circle_teacher ADD PRIMARY KEY (circle_id, teacher_id)');

        DB::statement('ALTER TABLE circle_teacher DROP INDEX circle_teacher_circle_id_idx');

        // فهرس teacher_id الدائم يبقى كما هو (لم يُحذف في up)
    }
};
