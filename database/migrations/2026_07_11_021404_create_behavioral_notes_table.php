<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Behavioral Notes Table
 * Description: Behavior record tracking system.
 * Records behavioral incidents, actions taken, and follow-up status.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('behavioral_notes', function (Blueprint $table) {
            $table->id();

            // 💡 تعريف الأعمدة الأساسية للربط
            $table->unsignedBigInteger('student_id')->comment('Link to the students table');
            $table->unsignedBigInteger('circle_id')->comment('Link to the circles table');
            $table->unsignedBigInteger('teacher_id')->comment('Teacher who reported the incident');

            // Incident details
            $table->timestamp('incident_at')->comment('Date and time when the incident occurred');
            $table->text('behavior')->comment('Description of the behavioral incident');
            $table->text('action_taken')->nullable()->comment('Action taken in response to the behavior');
            $table->timestamp('action_at')->nullable()->comment('Date and time when the supervisor took action');

            $table->string('current_status', 50)->nullable()->comment('Current status of the behavioral case');
            $table->string('status', 20)->default('pending')->comment('pending = no action recorded yet, action_taken = action has been recorded');

            // Follow-up scheduling
            $table->date('follow_up_at')->nullable()->comment('Scheduled follow-up date');

            // Timestamps
            $table->timestamps();

            // 💡 إعداد الكشافات للأعمدة
            $table->index('student_id', 'idx_behavior_student');
            $table->index('circle_id', 'idx_behavior_circle');
            $table->index('teacher_id', 'idx_behavior_teacher');
            $table->index('current_status', 'idx_behavior_status');
            $table->index('incident_at', 'idx_behavior_incident');
            $table->index('follow_up_at', 'idx_behavior_followup');

            // Composite indexes
            $table->index(['current_status', 'follow_up_at'], 'idx_behavior_status_followup');
            $table->index(['teacher_id', 'incident_at'], 'idx_behavior_teacher_date');
        });

        // 💡 إضافة قيود الربط الأجنبي (Foreign Keys) بعد إنشاء الجدول بنجاح
        Schema::table('behavioral_notes', function (Blueprint $table) {
            $table->foreign('student_id', 'fk_behavior_student')
                ->references('id')
                ->on('students')
                ->cascadeOnDelete();

            $table->foreign('circle_id', 'fk_behavior_circle')
                ->references('id')
                ->on('circles')
                ->restrictOnDelete();

            $table->foreign('teacher_id', 'fk_behavior_teacher')
                ->references('id')
                ->on('teachers')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('behavioral_notes', function (Blueprint $table) {
            $table->dropForeign('fk_behavior_student');
            $table->dropForeign('fk_behavior_circle');
            $table->dropForeign('fk_behavior_teacher');
        });

        Schema::dropIfExists('behavioral_notes');
    }
};
