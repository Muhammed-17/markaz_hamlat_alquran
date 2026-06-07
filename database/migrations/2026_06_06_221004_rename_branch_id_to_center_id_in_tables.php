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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('branch_id', 'center_id');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->renameColumn('branch_id', 'center_id');
        });

        Schema::table('circles', function (Blueprint $table) {
            $table->renameColumn('branch_id', 'center_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('center_id', 'branch_id');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->renameColumn('center_id', 'branch_id');
        });

        Schema::table('circles', function (Blueprint $table) {
            $table->renameColumn('center_id', 'branch_id');
        });
    }
};
