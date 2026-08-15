<?php

namespace App\Http\Requests\Student;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditStudentRequest extends FormRequest
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

            // Step 2
            'student_code' => [
                'nullable',
                'digits:14',
                Rule::unique('students', 'student_code')->ignore($this->route('id') ?? $this->route('student')),
            ],
            'name'                           => 'required|string|max:255',
            'gender'                         => 'required|in:ذكر,أنثى',
            'date_of_birth'                  => 'nullable|date',
            'address'                        => 'required|string',
            'center_id'                      => 'nullable|integer|exists:centers,id',
            'whatsapp_number'                => 'nullable|string|max:20',
            'whatsapp_owner'                 => 'nullable|string|max:50',
            'second_phone'                   => 'nullable|string|max:20',
            'additional_contact_owner'       => 'nullable|string|max:50',
            'guardian_id' => [
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '') return;
                    if (in_array((string)$value, ['new', 'other', 'none'])) return;
                    if (!\App\Models\User::where('id', $value)->exists()) {
                        $fail('ولي الأمر المختار غير موجود.');
                    }
                },
            ],
            'guardian_name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($this->guardian_id === 'new' && empty($value)) {
                        $fail('اسم ولي الأمر مطلوب عند إضافة حساب جديد.');
                    }
                },
            ],

            'parent_email' => 'nullable|string|max:255',
            'password'     => 'nullable|string|min:6',

            // Step 3
            'educational_stage' => 'nullable|string|max:100',
            'education_type'    => 'nullable|string|max:100',
            'school_grade'      => 'nullable|string|max:100',
            'previous_school'   => 'nullable|string|max:255',

            // Step 4
            'health_status'               => 'nullable|string|max:100',
            'learning_difficulties'       => 'nullable|string|max:255',
            'personal_traits'             => 'nullable|string|max:255',
            'hobbies'                     => 'nullable|array',
            'hobbies.*'                   => 'string',
            'student_exit_status'         => 'nullable|string|max:100',

            // Step 5
            'reading'            => 'nullable|string|max:50',
            'center_entry_level' => 'nullable|in:construction,mastery,creativity',

            // Step 6 - Construction
            'circle_id'                   => 'nullable|integer|exists:circles,id',
            'current_surah_id'            => 'nullable|integer|exists:surahs,id',
            'study_system'                => 'nullable|in:group,individual',
            'new_memorization_plan'       => 'nullable|string',
            'revision_plan'               => 'nullable|string',
            'placement_evaluation'        => 'nullable|string',
            'old_memorization_plan'       => 'nullable|string',

            // Step 7 - Mastery
            'previous_memorization_side' => 'nullable|string|max:255',
            'previous_khatamat_count'    => 'nullable|string',
            'current_review_amount'      => 'nullable|string|max:255',
            'self_evaluation'            => 'nullable|integer|min:1|max:10',
            'tajweed_matn'               => 'nullable|string|max:100',
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
            'notes'              => 'nullable|string',
            'status'             => 'nullable|string|max:50',
            'decision'           => 'nullable|string|max:50',
            'subscription_fees'  => 'nullable|string|max:50',
            'received_tools'     => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'الاسم مطلوب',
            'gender.required'    => 'الجنس مطلوب',
            'gender.in'          => 'الجنس يجب أن يكون ذكر أو أنثى',
            'address.required'   => 'العنوان مطلوب',
            'center_id.exists'   => 'المركز المختار غير موجود',
            'student_code.digits' => 'الرقم القومي يجب أن يتكون من 14 رقمًا بالضبط',
            'student_code.unique' => 'الرقم القومي مستخدم مسبقاً لطالب آخر',
            'center_entry_level.in' => 'مستوى الالتحاق غير صحيح',
        ];
    }
}
