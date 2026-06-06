<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Step 1
            'supervisor_id'   => 'nullable|integer|exists:teachers,id',
            'join_date'       => 'nullable|date',
            'applicant'       => 'nullable|string|max:50',
            'applicant_other' => 'nullable|string|max:255',

            // Step 2
            'student_code'                   => 'nullable|string|max:50|unique:students,student_code',
            'name'                           => 'required|string|max:255',
            'gender'                         => 'required|in:ذكر,أنثى',
            'date_of_birth'                  => 'nullable|date',
            'address'                        => 'nullable|string',
            'center_id'                      => 'nullable|integer|exists:centers,id',
            'whatsapp_number'                => 'nullable|string|max:20',
            'whatsapp_owner'                 => 'nullable|string|max:50',
            'whatsapp_owner_other'           => 'nullable|string|max:255',
            'second_phone'                   => 'nullable|string|max:20',
            'additional_contact_owner'       => 'nullable|string|max:50',
            'additional_contact_owner_other' => 'nullable|string|max:255',
            'guardian_id'                    => 'nullable|exists:users,id',

            // Step 3
            'educational_stage' => 'nullable|string|max:100',
            'education_type'    => 'nullable|string|max:100',
            'school_grade'      => 'nullable|string|max:100',
            'previous_school'   => 'nullable|string|max:255',

            // Step 4
            'health_status'               => 'nullable|string|max:100',
            'health_status_other'         => 'nullable|string|max:255',
            'learning_difficulties'       => 'nullable|string|max:255',
            'learning_difficulties_other' => 'nullable|string|max:255',
            'personal_traits'             => 'nullable|string|max:255',
            'personal_traits_other'       => 'nullable|string|max:255',
            'hobbies'                     => 'nullable|array',
            'hobbies.*'                   => 'string',
            'hobby_other'                 => 'nullable|string|max:255',
            'student_exit_status'         => 'nullable|string|max:100',
            'exit_details'                => 'nullable|string|max:255',

            // Step 5
            'reading'            => 'nullable|string|max:50',
            'center_entry_level' => 'nullable|in:construction,mastery,creativity',

            // Step 6 - Construction
            'current_surah'               => 'nullable|string|max:100',
            'study_system'                => 'nullable|string|max:100',
            'group_name'                  => 'nullable|string|max:255',
            'new_memorization_plan'       => 'nullable|string',
            'placement_evaluation'        => 'nullable|string',
            'old_memorization_plan'       => 'nullable|string',
            'old_memorization_plan_other' => 'nullable|string',

            // Step 7 - Mastery
            'previous_memorization_side' => 'nullable|string|max:255',
            'previous_khatamat_count'    => 'nullable|string',
            'current_review_amount'      => 'nullable|string|max:255',
            'self_evaluation'            => 'nullable|integer|min:1|max:10',
            'tajweed_matn'               => 'nullable|string|max:100',
            'tajweed_matn_other'         => 'nullable|string|max:255',
            'memorized_texts'            => 'nullable|string',
            'desired_path'               => 'nullable|string|max:255',
            'preferred_time'             => 'nullable|string|max:100',
            'teacher_name'               => 'nullable|string|max:255',
            'itqan_details'              => 'nullable|string',

            // Step 8 - Creativity
            'previous_licenses_and_chains' => 'nullable|string',
            'desired_narration_and_path'   => 'nullable|string|max:255',
            'supervisor_name'              => 'nullable|string|max:255',
            'ibda_details'                 => 'nullable|string',

            // Step 9
            'notes'             => 'nullable|string',
            'status'            => 'nullable|string|max:50',
            'decision'          => 'nullable|string|max:50',
            'subscription_fees' => 'nullable|string|max:50',
            'received_tools'    => 'nullable|string|max:100',
            'circle_id'         => 'nullable|exists:circles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'الاسم مطلوب',
            'gender.required'       => 'الجنس مطلوب',
            'gender.in'             => 'الجنس يجب أن يكون ذكر أو أنثى',
            'student_code.unique'   => 'كود الطالب مستخدم مسبقاً',
            'center_id.exists'      => 'المركز المختار غير موجود',
            'center_entry_level.in' => 'مستوى الالتحاق غير صحيح',
        ];
    }
}