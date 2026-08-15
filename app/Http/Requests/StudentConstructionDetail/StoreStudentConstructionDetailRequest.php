<?php

namespace App\Http\Requests\StudentConstructionDetail;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentConstructionDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['nullable', 'exists:students,id'],
            'circle_id' => ['nullable', 'exists:circles,id'],
            'study_system' => ['required', 'string', 'in:group,individual'],
            'current_surah_id' => ['nullable', 'exists:surahs,id'],
            'new_memorization_plan' => ['required', 'string', 'max:255'],
            'revision_plan' => ['required', 'string', 'max:255'],
            'old_memorization_plan' => ['required', 'string', 'max:255'],
            'placement_evaluation' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.exists' => 'الطالب المحدد غير موجود.',
            'circle_id.exists' => 'الحلقة المحددة غير موجودة.',
            'study_system.required' => 'نظام الدراسة مطلوب.',
            'study_system.in' => 'نظام الدراسة يجب أن يكون فردي أو جماعي.',
            'current_surah_id.exists' => 'السورة المحددة غير موجودة.',
            'new_memorization_plan.required' => 'خطة الحفظ الجديد مطلوبة.',
            'new_memorization_plan.max' => 'خطة الحفظ الجديد يجب ألا تتجاوز 255 حرفاً.',
            'revision_plan.required' => 'خطة المراجعة مطلوبة.',
            'revision_plan.max' => 'خطة المراجعة يجب ألا تتجاوز 255 حرفاً.',
            'old_memorization_plan.required' => 'خطة الحفظ القديم مطلوبة.',
            'old_memorization_plan.max' => 'خطة الحفظ القديم يجب ألا تتجاوز 255 حرفاً.',
        ];
    }
}
