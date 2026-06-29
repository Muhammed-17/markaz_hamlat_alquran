<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure all existing statuses are valid
        DB::table('attendances')
            ->whereNotIn('status', ['present', 'absent', 'late', 'excused'])
            ->update(['status' => 'present']);

        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['present', 'absent', 'late', 'excused'])
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('status')->change();
        });
    }
};