<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_code')->nullable()->unique();
            $table->string('nationality')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('id_number')->nullable();
            $table->string('religion')->nullable();
            $table->string('education_type')->nullable();
            $table->string('educational_stage')->nullable();
            $table->string('school_grade')->nullable();
            $table->string('previous_school')->nullable();
            $table->text('academic_notes')->nullable();
            $table->string('center_entry_level')->nullable();
            $table->string('circle_name')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('enrollment_type')->nullable();
            $table->string('memorization_level')->nullable();
            $table->date('join_date')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('residence_type')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_job')->nullable();
            $table->string('guardian_income')->nullable();
            $table->string('health_status')->nullable();
            $table->string('chronic_diseases')->nullable();
            $table->string('allergies')->nullable();
            $table->text('medical_notes')->nullable();
            $table->string('medication')->nullable();
            $table->string('blood_type')->nullable();
            $table->text('behavior_notes')->nullable();
            $table->text('disciplinary_actions')->nullable();
            $table->string('behavior_level')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('parents_marital_status')->nullable();
            $table->string('student_living_with')->nullable();
            $table->string('registration_channel')->nullable();
            $table->text('notes')->nullable();
            $table->string('referral_name')->nullable();
            
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->string('applicant')->nullable();
            $table->string('applicant_other')->nullable();
            $table->string('center')->nullable();
            $table->string('whatsapp_owner')->nullable();
            $table->string('whatsapp_owner_other')->nullable();
            $table->string('additional_contact_owner')->nullable();
            $table->string('additional_contact_owner_other')->nullable();
            $table->string('parent_email')->nullable();
            $table->string('password')->nullable();
            $table->text('learning_difficulties')->nullable();
            $table->text('personal_traits')->nullable();
            $table->json('hobbies')->nullable();
            $table->text('reading')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('exit_details')->nullable();
        });

        Schema::create('student_construction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('study_system')->nullable();
            $table->string('group_name')->nullable();
            $table->string('new_memorization_plan')->nullable();
            $table->string('placement_evaluation')->nullable();
            $table->string('old_memorization_plan')->nullable();
            // current_surah already exists in students table, we can just use the one in students, but it was in constructionFields.
            $table->timestamps();
        });

        Schema::create('student_itqan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('previous_memorization_side')->nullable();
            $table->integer('previous_khatamat_count')->nullable();
            $table->string('current_review_amount')->nullable();
            $table->string('self_evaluation')->nullable();
            $table->text('memorized_texts')->nullable();
            $table->string('desired_path')->nullable();
            $table->string('preferred_time')->nullable();
            $table->string('teacher_name')->nullable();
            $table->text('itqan_details')->nullable();
            $table->timestamps();
        });

        Schema::create('student_ibda_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->text('previous_licenses_and_chains')->nullable();
            $table->string('desired_narration_and_path')->nullable();
            $table->string('preferred_time')->nullable();
            $table->text('ibda_details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_ibda_details');
        Schema::dropIfExists('student_itqan_details');
        Schema::dropIfExists('student_construction_details');

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'student_code', 'nationality', 'birth_place', 'id_number', 'religion',
                'education_type', 'educational_stage', 'school_grade', 'previous_school', 'academic_notes',
                'center_entry_level', 'circle_name', 'supervisor_name', 'enrollment_type', 'memorization_level', 'join_date',
                'emergency_contact_name', 'emergency_contact_phone', 'whatsapp_number', 'email', 'residence_type',
                'guardian_relation', 'guardian_job', 'guardian_income',
                'health_status', 'chronic_diseases', 'allergies', 'medical_notes', 'medication', 'blood_type',
                'behavior_notes', 'disciplinary_actions', 'behavior_level',
                'father_name', 'father_phone', 'mother_name', 'mother_phone', 'parents_marital_status', 'student_living_with',
                'registration_channel', 'notes', 'referral_name',
                'supervisor_id', 'applicant', 'applicant_other', 'center', 'whatsapp_owner', 'whatsapp_owner_other',
                'additional_contact_owner', 'additional_contact_owner_other', 'parent_email', 'password',
                'learning_difficulties', 'personal_traits', 'hobbies', 'reading', 'recommendations', 'exit_details'
            ]);
        });
    }
};
