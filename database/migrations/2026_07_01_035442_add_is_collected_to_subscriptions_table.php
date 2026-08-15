<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('is_collected')->default(false)->after('status');
        });

        // Composite index يغطي استعلام "المستحقات الجديدة غير المجمّعة"
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(
                ['collected_by', 'month', 'is_collected', 'status'],
                'idx_collected_by_month_collected_status'
            );
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('idx_collected_by_month_collected_status');
            $table->dropColumn('is_collected');
        });
    }
};