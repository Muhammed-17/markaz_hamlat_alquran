<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompetitionExaminerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // الصلاحية الفعلية متحققة داخل الـ Controller عبر $this->authorize('update', $competition)
        return true;
    }

    public function rules(): array
    {
        return [
            'competition_level_ids'    => ['nullable', 'array'],
            'competition_level_ids.*'  => ['integer', 'exists:competition_levels,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'competition_level_ids.*.exists' => 'أحد المستويات المحددة غير موجود.',
        ];
    }
}
