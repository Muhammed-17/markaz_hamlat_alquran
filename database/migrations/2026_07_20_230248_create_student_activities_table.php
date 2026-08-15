<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Student Activities Table
 *
 * Description:
 * Records activities related to a weekly follow-up.
 * Activities can belong to an individual follow-up or a group follow-up
 * through the follow_id (batch_id).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_activities', function (Blueprint $table) {
            $table->id();

            // Weekly follow-up
            $table->foreignId('weekly_followup_id')
                ->constrained('student_weekly_followups')
                ->cascadeOnDelete();

            // Group follow-up identifier
            $table->uuid('follow_id')
                ->nullable()
                ->comment('Group follow-up identifier (batch_id)');

            // Activity classification
            $table->string('activity_type', 50)
                ->comment('Activity type: competition, workshop, event, camp, etc.');

            // Activity details
            $table->string('activity_name', 255)
                ->comment('Activity title');

            $table->date('activity_date')
                ->comment('Activity date');

            // Notes
            $table->text('notes')
                ->nullable()
                ->comment('Additional notes');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('weekly_followup_id', 'idx_activities_followup');
            $table->index('follow_id', 'idx_activities_follow');
            $table->index('activity_type', 'idx_activities_type');
            $table->index('activity_date', 'idx_activities_date');

            $table->index(
                ['follow_id', 'activity_date'],
                'idx_activities_follow_date'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_activities');
    }
};
