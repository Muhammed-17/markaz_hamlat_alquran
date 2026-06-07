<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('status')
                ->constrained('centers')->nullOnDelete();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('user_id')
                ->constrained('centers')->nullOnDelete();
        });

        Schema::table('circles', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('supervisor_id')
                ->constrained('centers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('circles', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
