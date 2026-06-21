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
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'age', 'phone', 'is_guardian_contacted', 
                'circle_name', 'supervisor_name', 'enrollment_date', 
                'nationality', 'birth_place', 'id_number', 'religion', 
                'academic_notes', 'enrollment_type', 'memorization_level', 
                'emergency_contact_name', 'emergency_contact_phone', 'email', 
                'residence_type', 'guardian_relation', 'guardian_job', 
                'guardian_income', 'chronic_diseases', 'allergies', 
                'medical_notes', 'medication', 'blood_type', 'behavior_notes', 
                'disciplinary_actions', 'behavior_level', 'father_name', 
                'father_phone', 'mother_name', 'mother_phone', 
                'parents_marital_status', 'student_living_with', 
                'registration_channel', 'referral_name', 'center', 
                'parent_email', 'password', 'recommendations', 'current_surah'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // في حال أردت التراجع، يمكنك إعادة تعريفهم هنا كـ nullable
            $table->text('description')->nullable();
            $table->integer('age')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_guardian_contacted')->default(false);
            $table->string('circle_name')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->string('nationality')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('id_number')->nullable();
            $table->string('religion')->nullable();
            $table->text('academic_notes')->nullable();
            $table->string('enrollment_type')->nullable();
            $table->string('memorization_level')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('residence_type')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_job')->nullable();
            $table->string('guardian_income')->nullable();
            $table->text('chronic_diseases')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_notes')->nullable();
            $table->text('medication')->nullable();
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
            $table->string('referral_name')->nullable();
            $table->string('center')->nullable();
            $table->string('parent_email')->nullable();
            $table->string('password')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('current_surah')->nullable();
        });
    }
};