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
        Schema::table('students', function (Blueprint $table) {
            $table->string('health_status_other')->nullable()->after('health_status');
            $table->string('learning_difficulties_other')->nullable()->after('learning_difficulties');
            $table->string('personal_traits_other')->nullable()->after('personal_traits');
            $table->string('hobby_other')->nullable()->after('hobbies');
            $table->string('decision')->nullable();
            $table->decimal('subscription_fees', 8, 2)->nullable();
            $table->string('received_tools')->nullable()->after('subscription_fees');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'health_status_other',
                'learning_difficulties_other',
                'personal_traits_other',
                'hobby_other',
                'decision',
                'subscription_fees',
                'received_tools',
            ]);
        });
    }
};
