<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // construction — فقط old_memorization_plan_other ناقص
        if (!Schema::hasColumn('student_construction_details', 'old_memorization_plan_other')) {
            Schema::table('student_construction_details', function (Blueprint $table) {
                $table->string('old_memorization_plan_other')->nullable()->after('old_memorization_plan');
            });
        }

        // itqan
        Schema::table('student_itqan_details', function (Blueprint $table) {
            if (!Schema::hasColumn('student_itqan_details', 'tajweed_matn')) {
                $table->string('tajweed_matn')->nullable()->after('self_evaluation');
            }
            if (!Schema::hasColumn('student_itqan_details', 'tajweed_matn_other')) {
                $table->string('tajweed_matn_other')->nullable()->after('tajweed_matn');
            }
        });

        // ibda
        if (!Schema::hasColumn('student_ibda_details', 'supervisor_name')) {
            Schema::table('student_ibda_details', function (Blueprint $table) {
                $table->string('supervisor_name')->nullable()->after('preferred_time');
            });
        }
    }

    public function down()
    {
        Schema::table('student_construction_details', function (Blueprint $table) {
            $table->dropColumn('old_memorization_plan_other');
        });
        Schema::table('student_itqan_details', function (Blueprint $table) {
            $table->dropColumn(['tajweed_matn', 'tajweed_matn_other']);
        });
        Schema::table('student_ibda_details', function (Blueprint $table) {
            $table->dropColumn('supervisor_name');
        });
    }
};
