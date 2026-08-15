<?php

namespace App\Http\Requests\WeeklyFollowup;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIndividualWeeklyFollowupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Basic info
            'circle_id'        => ['nullable', 'integer', 'exists:circles,id'],
            'student_id'       => ['required', 'integer', 'exists:students,id'],
            'teacher_id'       => ['required', 'integer', 'exists:teachers,id'],
            'week_start'       => ['required', 'date', 'date_format:Y-m-d'],
            'week_end'         => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:week_start'],
            'study_days'       => ['nullable', 'array'],
            'study_days.*'     => ['string', 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'],
            'notes'            => ['nullable', 'string'],

            // New Memorization (flat fields)
            'memorization_from_surah_id'       => ['nullable', 'integer', 'exists:surahs,id'],
            'memorization_from_ayah'           => ['nullable', 'integer', 'min:1'],
            'memorization_to_surah_id'         => ['nullable', 'integer', 'exists:surahs,id'],
            'memorization_to_ayah'             => ['nullable', 'integer', 'min:1'],
            'memorization_plan_comparison'     => ['nullable', 'string', 'max:255'],
            'memorization_progress_difference' => ['nullable', 'string', 'max:255'],
            'memorization_notes'               => ['nullable', 'string'],
            'new_memorization_level'           => ['nullable', 'string', 'in:ممتاز,جيد جداً,جيد,مقبول,ضعيف'],

            // Revision (flat fields)
            'revision_from_surah_id'       => ['nullable', 'integer', 'exists:surahs,id'],
            'revision_from_ayah'           => ['nullable', 'integer', 'min:1'],
            'revision_to_surah_id'         => ['nullable', 'integer', 'exists:surahs,id'],
            'revision_to_ayah'             => ['nullable', 'integer', 'min:1'],
            'revision_plan_comparison'     => ['nullable', 'string', 'max:255'],
            'revision_progress_difference' => ['nullable', 'string', 'max:255'],
            'revision_notes'               => ['nullable', 'string'],
            'revision_level'               => ['nullable', 'string', 'in:ممتاز,جيد جداً,جيد,مقبول,ضعيف'],

            // Old Memorization (flat fields)
            'old_revision_from_surah_id'       => ['nullable', 'integer', 'exists:surahs,id'],
            'old_revision_from_ayah'           => ['nullable', 'integer', 'min:1'],
            'old_revision_to_surah_id'         => ['nullable', 'integer', 'exists:surahs,id'],
            'old_revision_to_ayah'             => ['nullable', 'integer', 'min:1'],
            'old_revision_plan_comparison'     => ['nullable', 'string', 'max:255'],
            'old_revision_progress_difference' => ['nullable', 'string', 'max:255'],
            'old_revision_notes'               => ['nullable', 'string'],
            'old_memorization_level'           => ['nullable', 'string', 'in:ممتاز,جيد جداً,جيد,مقبول,ضعيف'],

            // Assessments
            'discipline_level'              => ['nullable', 'string', 'in:ممتاز,جيد جداً,جيد,مقبول,ضعيف'],
            'discipline_achievement'        => ['nullable', 'string', 'max:500'],
            'tajweed_level'                 => ['nullable', 'string', 'in:ممتاز,جيد جداً,جيد,مقبول,ضعيف'],
            'tajweed_achievement'           => ['nullable', 'string', 'max:500'],
            'foundation_level_level'        => ['nullable', 'string', 'in:ممتاز,جيد جداً,جيد,مقبول,ضعيف'],
            'foundation_level_achievement'  => ['nullable', 'string', 'max:500'],

            // Educational Lesson
            'educational_lesson_id'         => ['nullable', 'integer', 'exists:educational_lessons,id'],
            'educational_lesson_level'      => ['nullable', 'string', 'in:ممتاز,جيد جداً,جيد,مقبول,ضعيف'],
            'educational_lesson_notes'      => ['nullable', 'string', 'max:500'],

            // Activities
            'activities'                    => ['nullable', 'array'],
            'activities.*.activity_type'    => ['required_with:activities', 'string', 'max:100'],
            'activities.*.activity_name'    => ['required_with:activities', 'string', 'max:255'],
            'activities.*.activity_date'    => ['required_with:activities', 'date'],
            'activities.*.notes'            => ['nullable', 'string', 'max:500'],
            'activities.*._deleted'         => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'    => 'الطالب',
            'teacher_id'    => 'المعلم',
            'circle_id'     => 'الحلقة',
            'week_start'    => 'بداية الأسبوع',
            'week_end'      => 'نهاية الأسبوع',
            'study_days'    => 'أيام الدراسة',

            'memorization_from_surah_id'       => 'سورة البداية (حفظ جديد)',
            'memorization_from_ayah'           => 'آية البداية (حفظ جديد)',
            'memorization_to_surah_id'         => 'سورة النهاية (حفظ جديد)',
            'memorization_to_ayah'             => 'آية النهاية (حفظ جديد)',
            'memorization_plan_comparison'     => 'مقارنة الخطة (حفظ جديد)',
            'memorization_progress_difference' => 'فرق التقدم (حفظ جديد)',
            'new_memorization_level'           => 'مستوى الحفظ الجديد',

            'revision_from_surah_id'       => 'سورة البداية (مراجعة)',
            'revision_from_ayah'           => 'آية البداية (مراجعة)',
            'revision_to_surah_id'         => 'سورة النهاية (مراجعة)',
            'revision_to_ayah'             => 'آية النهاية (مراجعة)',
            'revision_plan_comparison'     => 'مقارنة الخطة (مراجعة)',
            'revision_progress_difference' => 'فرق التقدم (مراجعة)',
            'revision_level'               => 'مستوى المراجعة',

            'old_revision_from_surah_id'       => 'سورة البداية (حفظ قديم)',
            'old_revision_from_ayah'           => 'آية البداية (حفظ قديم)',
            'old_revision_to_surah_id'         => 'سورة النهاية (حفظ قديم)',
            'old_revision_to_ayah'             => 'آية النهاية (حفظ قديم)',
            'old_revision_plan_comparison'     => 'مقارنة الخطة (حفظ قديم)',
            'old_revision_progress_difference' => 'فرق التقدم (حفظ قديم)',
            'old_memorization_level'           => 'مستوى الحفظ القديم',

            'discipline_level'             => 'مستوى الانضباط',
            'tajweed_level'                => 'مستوى التجويد',
            'foundation_level_level'       => 'المستوى التأسيسي',
            'educational_lesson_level'     => 'مستوى الدرس التربوي',
        ];
    }
}
