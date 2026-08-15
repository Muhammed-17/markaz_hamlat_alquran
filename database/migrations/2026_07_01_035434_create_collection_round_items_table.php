<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_round_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('collection_round_id')->constrained('collection_rounds')->cascadeOnDelete();

            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();

            $table->decimal('amount_at_collection', 10, 2);

            // ✅ نسخة مجمَّدة من subscriptions.collected_by وقت إضافة العنصر للالتحصيل
            $table->foreignId('collected_by_snapshot')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Unique — يمنع دخول أي اشتراك في أكتر من التحصيل
            $table->unique('subscription_id', 'uniq_subscription_per_round');

            // للجلب السريع لعناصر التحصيل معينة
            $table->index('collection_round_id', 'idx_round_id');

            // ✅ للتجميع السريع حسب المحصّل داخل الالتحصيل (لعرض شاشة زي الصورة)
            $table->index(['collection_round_id', 'collected_by_snapshot'], 'idx_round_collected_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_round_items');
    }
};