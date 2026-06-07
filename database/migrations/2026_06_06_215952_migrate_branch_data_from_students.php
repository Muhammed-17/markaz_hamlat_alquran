<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // teachers يرثون branch_id من طلابهم عبر circle_teacher
        DB::statement("
            UPDATE teachers t
            JOIN circle_teacher ct ON ct.teacher_id = t.id
            JOIN circles c ON c.id = ct.circle_id
            JOIN students s ON s.circle_id = c.id
            SET t.branch_id = s.center_id
            WHERE t.branch_id IS NULL AND s.center_id IS NOT NULL
        ");

        // users يرثون من teachers
        DB::statement("
            UPDATE users u
            JOIN teachers t ON t.user_id = u.id
            SET u.branch_id = t.branch_id
            WHERE u.branch_id IS NULL AND t.branch_id IS NOT NULL
        ");

        // circles يرثون من طلابهم
        DB::statement("
            UPDATE circles c
            JOIN students s ON s.circle_id = c.id
            SET c.branch_id = s.center_id
            WHERE c.branch_id IS NULL AND s.center_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        DB::table('teachers')->update(['branch_id' => null]);
        DB::table('users')->update(['branch_id' => null]);
        DB::table('circles')->update(['branch_id' => null]);
    }
};