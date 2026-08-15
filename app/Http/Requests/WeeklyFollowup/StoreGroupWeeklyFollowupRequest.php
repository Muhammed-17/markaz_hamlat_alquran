<?php

namespace App\Http\Requests\WeeklyFollowup;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupWeeklyFollowupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Week Data
            'week_start'    => ['required', 'date', 'date_format:Y-m-d'],
            'week_end'      => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:week_start'],
            'circle_id'     => ['required', 'integer', 'exists:circles,id'],
            'teacher_id'    => ['required', 'integer', 'exists:teachers,id'],
            'study_days'    => ['nullable', 'array'],
            'study_days.*'  => ['string', 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'],

            // Plan Data - New Memorization
            'new_memorization.from_surah_id'       => ['nullable', 'integer', 'exists:surahs,id'],
            'new_memorization.from_ayah'           => ['nullable', 'integer', 'min:1'],
            'new_memorization.to_surah_id'         => ['nullable', 'integer', 'exists:surahs,id'],
            'new_memorization.to_ayah'             => ['nullable', 'integer', 'min:1'],
            'new_memorization.plan_comparison'     => ['nullable', 'string', 'max:255'],
            'new_memorization.progress_difference' => ['nullable', 'string', 'max:255'],
            'new_memorization.notes'               => ['nullable', 'string'],

            // Plan Data - Revision
            'revision.from_surah_id'       => ['nullable', 'integer', 'exists:surahs,id'],
            'revision.from_ayah'           => ['nullable', 'integer', 'min:1'],
            'revision.to_surah_id'         => ['nullable', 'integer', 'exists:surahs,id'],
            'revision.to_ayah'             => ['nullable', 'integer', 'min:1'],
            'revision.plan_comparison'     => ['nullable', 'string', 'max:255'],
            'revision.progress_difference' => ['nullable', 'string', 'max:255'],
            'revision.notes'               => ['nullable', 'string'],

            // Plan Data - Old Memorization
            'old_memorization.from_surah_id'       => ['nullable', 'integer', 'exists:surahs,id'],
            'old_memorization.from_ayah'           => ['nullable', 'integer', 'min:1'],
            'old_memorization.to_surah_id'         => ['nullable', 'integer', 'exists:surahs,id'],
            'old_memorization.to_ayah'             => ['nullable', 'integer', 'min:1'],
            'old_memorization.plan_comparison'     => ['nullable', 'string', 'max:255'],
            'old_memorization.progress_difference' => ['nullable', 'string', 'max:255'],
            'old_memorization.notes'               => ['nullable', 'string'],

            // Shared Achievement Notes (batch-level, replicated per student row)
            'discipline_achievement'       => ['nullable', 'string'],
            'tajweed_achievement'          => ['nullable', 'string'],
            'foundation_level_achievement' => ['nullable', 'string'],

            // Educational Lesson
            'educational_lesson_id' => ['nullable', 'integer', 'exists:educational_lessons,id'],
            'educational_lesson_achievement' => 'nullable|string',

            // Activities
            'activities'              => ['nullable', 'array'],
            'activities.*.id'         => ['nullable', 'integer', 'exists:student_activities,id'],
            'activities.*.activity_type'  => ['nullable', 'string', 'max:255'],
            'activities.*.activity_name'  => ['nullable', 'string', 'max:255'],
            'activities.*.activity_date'  => ['nullable', 'date'],
            'activities.*.notes'          => ['nullable', 'string'],
            'activities.*._deleted'     => ['nullable'],

            // Students Data
            'students'              => ['required', 'array', 'min:1'],
            'students.*.student_id' => ['required', 'integer', 'exists:students,id'],

            // Student assessment levels
            'students.*.discipline_level'           => ['nullable', 'string', 'max:50'],
            'students.*.tajweed_level'              => ['nullable', 'string', 'max:50'],
            'students.*.educational_lesson_level'   => ['nullable', 'string', 'max:50'],
            'students.*.educational_lesson_notes'   => ['nullable', 'string'],
            'students.*.foundation_level_level'     => ['nullable', 'string', 'max:50'],
            'students.*.new_memorization_level'     => ['nullable', 'string', 'max:50'],
            'students.*.revision_level'             => ['nullable', 'string', 'max:50'],
            'students.*.old_memorization_level'     => ['nullable', 'string', 'max:50'],
            'students.*.general_notes'              => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'required'       => 'حقل :attribute مطلوب.',
            'integer'        => 'حقل :attribute يجب أن يكون رقمًا صحيحًا.',
            'string'         => 'حقل :attribute يجب أن يكون نصًا.',
            'array'          => 'حقل :attribute يجب أن يكون قائمة.',
            'min'            => 'حقل :attribute يجب ألا يقل عن :min.',
            'max'            => 'حقل :attribute يجب ألا يزيد عن :max حرفًا.',
            'date'           => 'حقل :attribute يجب أن يكون تاريخًا صحيحًا.',
            'date_format'    => 'صيغة حقل :attribute غير صحيحة.',
            'after_or_equal' => 'حقل :attribute يجب أن يكون بعد أو مساويًا لـ :date.',
            'exists'         => 'القيمة المختارة في حقل :attribute غير موجودة.',
            'in'             => 'القيمة المختارة في حقل :attribute غير صحيحة.',
            'students.required' => 'يجب إضافة طالب واحد على الأقل.',
            'students.min'       => 'يجب إضافة طالب واحد على الأقل.',
        ];
    }

    public function attributes(): array
    {
        return [
            'week_start'    => 'بداية الأسبوع',
            'week_end'      => 'نهاية الأسبوع',
            'circle_id'     => 'الحلقة',
            'teacher_id'    => 'المعلم',
            'study_days'    => 'أيام الدراسة',
            'students'      => 'الطلاب',
            'students.*.student_id' => 'الطالب',
            'activities.*.activity_type' => 'نوع النشاط',
            'activities.*.activity_name' => 'اسم النشاط',
            'activities.*.activity_date' => 'تاريخ النشاط',
        ];
    }
}
