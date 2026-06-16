<?php

namespace App\Http\Requests\Student;


use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Step 1
            'supervisor_id'   => 'required|integer|exists:teachers,id',
            'join_date'       => 'required|date',
            'applicant'       => 'nullable|string|max:50',
            'applicant_other' => 'nullable|string|max:255',

            // Step 2
            'student_code'                    => 'required|string|max:50|unique:students,student_code',
            'name'                            => 'required|string|max:255',
            'gender'                          => 'required|in:ذكر,أنثى',
            'date_of_birth'                   => 'nullable|date',
            'address'                         => 'required|string',
            'center_id'                       => 'required|integer|exists:centers,id', // ✅ مصحح
            'whatsapp_number'                 => 'nullable|string|max:20',
            'whatsapp_owner'                  => 'nullable|string|max:50',
            'whatsapp_owner_other'            => 'nullable|string|max:255',
            'second_phone'                    => 'nullable|string|max:20',
            'additional_contact_owner'        => 'nullable|string|max:50',
            'additional_contact_owner_other'  => 'nullable|string|max:255',
            'parent_email'                    => 'nullable|string|max:255',
            'password'                        => 'nullable|string|min:6',
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

            // Step 3
            'educational_stage' => 'required|string|max:100',
            'education_type'    => 'required|string|max:100',
            'school_grade'      => 'required|string|max:100',
            'previous_school'   => 'required|string|max:255',

            // Step 4
            'health_status'        => 'required|string|max:100',
            'health_status_other'  => 'nullable|string|max:255',
            'learning_difficulties'       => 'required|string|max:255',
            'learning_difficulties_other' => 'nullable|string|max:255',
            'personal_traits'       => 'required|string|max:255',
            'personal_traits_other' => 'nullable|string|max:255',
            'hobbies'               => 'nullable|array',
            'hobbies.*'             => 'string',
            'hobby_other'           => 'nullable|string|max:255',
            'student_exit_status'   => 'required|string|max:100',
            'exit_details'          => 'nullable|string|max:255',

            // Step 5
            'reading'            => 'required|string|max:50',
            'center_entry_level' => 'required|in:construction,mastery,creativity', // ✅ مصحح

            // Step 6 - Construction
            'current_surah'          => 'required_if:center_entry_level,construction|nullable|string|max:100',
            'study_system'           => 'required_if:center_entry_level,construction|nullable|string|max:100',
            'group_name'             => 'required_if:center_entry_level,construction|nullable|string|max:255',
            'new_memorization_plan'  => 'nullable|string',
            'placement_evaluation'   => 'nullable|string',
            'old_memorization_plan'  => 'nullable|string',
            'old_memorization_plan_other' => 'nullable|string',

            // Step 7 - Mastery ✅ مصحح
            'previous_memorization_side' => 'required_if:center_entry_level,mastery|nullable|string|max:255',
            'previous_khatamat_count'    => 'required_if:center_entry_level,mastery|nullable|string',
            'current_review_amount'      => 'required_if:center_entry_level,mastery|nullable|string|max:255',
            'self_evaluation'            => 'nullable|integer|min:1|max:10',
            'tajweed_matn'               => 'nullable|string|max:100',
            'tajweed_matn_other'         => 'nullable|string|max:255',
            'desired_path'               => 'required_if:center_entry_level,mastery|nullable|string|max:255',
            'preferred_time'             => 'nullable|string|max:100',
            'teacher_name'               => 'nullable|string|max:255',
            'itqan_details'              => 'nullable|string',

            // Step 8 - Creativity ✅ مصحح
            'previous_licenses_and_chains' => 'required_if:center_entry_level,creativity|nullable|string',
            'desired_narration_and_path'   => 'required_if:center_entry_level,creativity|nullable|string|max:255',
            'supervisor_name'              => 'nullable|string|max:255',
            'ibda_details'                 => 'nullable|string',

            // Step 9
            'notes'    => 'nullable|string',
            'status'   => 'nullable|string|max:50',
            'decision' => 'nullable|string|max:50',
            'subscription_fees' => 'nullable|string|max:50',
            'received_tools'    => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'supervisor_id.required'    => 'المشرف مطلوب',
            'join_date.required'        => 'تاريخ التسجيل مطلوب',
            'student_code.required'     => 'كود الطالب مطلوب',
            'student_code.unique'       => 'كود الطالب مستخدم مسبقاً',
            'name.required'             => 'الاسم مطلوب',
            'gender.required'           => 'الجنس مطلوب',
            'gender.in'                 => 'الجنس يجب أن يكون ذكر أو أنثى',
            'address.required'          => 'العنوان مطلوب',
            'center_id.required'        => 'المركز مطلوب',
            'center_id.exists'          => 'المركز المختار غير موجود',
            'parent_email.required_if'  => 'البريد الإلكتروني مطلوب عند إضافة ولي أمر جديد',
            'password.required_if'      => 'كلمة المرور مطلوبة عند إضافة ولي أمر جديد',
            'educational_stage.required' => 'المرحلة الدراسية مطلوبة',
            'education_type.required'   => 'نوع التعليم مطلوب',
            'school_grade.required'     => 'الصف الدراسي مطلوب',
            'previous_school.required'  => 'المؤسسة التعليمية مطلوبة',
            'health_status.required'    => 'الحالة الصحية مطلوبة',
            'learning_difficulties.required' => 'صعوبات التعلم مطلوبة',
            'personal_traits.required'  => 'السمات الشخصية مطلوبة',
            'student_exit_status.required' => 'حالة خروج الطالب مطلوبة',
            'reading.required'          => 'مستوى القراءة مطلوب',
            'center_entry_level.required' => 'مستوى الالتحاق مطلوب',
            'center_entry_level.in'     => 'مستوى الالتحاق غير صحيح',
            'current_surah.required_if' => 'سورة الالتحاق مطلوبة لمستوى البناء',
            'study_system.required_if'  => 'نظام الدراسة مطلوب لمستوى البناء',
            'group_name.required_if'    => 'اسم الحلقة مطلوب لمستوى البناء',
            'previous_memorization_side.required_if' => 'جهة الحفظ السابقة مطلوبة لمستوى الإتقان',
            'previous_khatamat_count.required_if'    => 'عدد الختمات السابقة مطلوب لمستوى الإتقان',
            'current_review_amount.required_if'      => 'مقدار المراجعة الحالي مطلوب لمستوى الإتقان',
            'desired_path.required_if'               => 'المسار المرغوب مطلوب لمستوى الإتقان',
            'previous_licenses_and_chains.required_if' => 'الإجازات والأسانيد مطلوبة لمستوى الإبداع',
            'desired_narration_and_path.required_if'   => 'الرواية المراد دراستها مطلوبة لمستوى الإبداع',
        ];
    }
}
