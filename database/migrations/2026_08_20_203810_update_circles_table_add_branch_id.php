<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('center_id')->nullable()
                ->constrained('centers')->nullOnDelete();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('center_id')->nullable()
                ->constrained('centers')->nullOnDelete();
        });

        Schema::table('circles', function (Blueprint $table) {
            $table->foreignId('center_id')->nullable()
                ->constrained('centers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');
        });

        Schema::table('circles', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');
        });
    }
};
