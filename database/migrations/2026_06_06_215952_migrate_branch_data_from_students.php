<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // ✅ SQLite: Subquery approach
            DB::statement("
                UPDATE teachers
                SET branch_id = (
                    SELECT s.center_id
                    FROM circle_teacher ct
                    INNER JOIN circles c ON c.id = ct.circle_id
                    INNER JOIN students s ON s.circle_id = c.id
                    WHERE ct.teacher_id = teachers.id
                    AND s.center_id IS NOT NULL
                    LIMIT 1
                )
                WHERE branch_id IS NULL
                AND EXISTS (
                    SELECT 1
                    FROM circle_teacher ct
                    INNER JOIN circles c ON c.id = ct.circle_id
                    INNER JOIN students s ON s.circle_id = c.id
                    WHERE ct.teacher_id = teachers.id
                    AND s.center_id IS NOT NULL
                )
            ");

            DB::statement("
                UPDATE users
                SET branch_id = (
                    SELECT t.branch_id
                    FROM teachers t
                    WHERE t.user_id = users.id
                    AND t.branch_id IS NOT NULL
                    LIMIT 1
                )
                WHERE branch_id IS NULL
                AND EXISTS (
                    SELECT 1
                    FROM teachers t
                    WHERE t.user_id = users.id
                    AND t.branch_id IS NOT NULL
                )
            ");

            DB::statement("
                UPDATE circles
                SET branch_id = (
                    SELECT s.center_id
                    FROM students s
                    WHERE s.circle_id = circles.id
                    AND s.center_id IS NOT NULL
                    LIMIT 1
                )
                WHERE branch_id IS NULL
                AND EXISTS (
                    SELECT 1
                    FROM students s
                    WHERE s.circle_id = circles.id
                    AND s.center_id IS NOT NULL
                )
            ");
        } else {
            // ✅ MySQL/MariaDB: UPDATE JOIN
            DB::statement("
                UPDATE teachers t
                JOIN circle_teacher ct ON ct.teacher_id = t.id
                JOIN circles c ON c.id = ct.circle_id
                JOIN students s ON s.circle_id = c.id
                SET t.branch_id = s.center_id
                WHERE t.branch_id IS NULL AND s.center_id IS NOT NULL
            ");

            DB::statement("
                UPDATE users u
                JOIN teachers t ON t.user_id = u.id
                SET u.branch_id = t.branch_id
                WHERE u.branch_id IS NULL AND t.branch_id IS NOT NULL
            ");

            DB::statement("
                UPDATE circles c
                JOIN students s ON s.circle_id = c.id
                SET c.branch_id = s.center_id
                WHERE c.branch_id IS NULL AND s.center_id IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        DB::table('teachers')->update(['branch_id' => null]);
        DB::table('users')->update(['branch_id' => null]);
        DB::table('circles')->update(['branch_id' => null]);
    }
};
