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
        Schema::table('circles', function (Blueprint $table) {
            $table->foreignId('supervisor_id')->nullable()->constrained('teachers')->cascadeOnDelete();        // لو المعلم اتحذف تحذف الدائرة
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circles', function (Blueprint $table) {
            
            // حذف العمود الجديد
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');

            // إعادة العمود القديم لو رجعت بالـ rollback
            $table->boolean('is_active')->default(true);
        });
    }
};
