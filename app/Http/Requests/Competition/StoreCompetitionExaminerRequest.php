<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitionExaminerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // الصلاحية الفعلية متحققة داخل الـ Controller عبر $this->authorize('update', $competition)
        // لأن الـ Competition model متاح فقط عبر route model binding داخل الـ Controller.
        return true;
    }

    public function rules(): array
    {
        return [
            'examiner_id'              => ['required', 'integer', 'exists:examiners,id'],
            'competition_level_ids'    => ['nullable', 'array'],
            'competition_level_ids.*'  => ['integer', 'exists:competition_levels,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'examiner_id.required' => 'يجب اختيار المختبر.',
            'examiner_id.exists'   => 'المختبر المحدد غير موجود.',
            'competition_level_ids.*.exists' => 'أحد المستويات المحددة غير موجود.',
        ];
    }
}