<?php

namespace App\Http\Requests\Examiner;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCompetitionAnswerRequest
 *
 * يستخدم من الإدارة (Supervisor/Manager) لتعديل درجة سؤال معتمد أو غير معتمد.
 */
class UpdateCompetitionAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('answer'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'answered'               => ['required', 'boolean'],
            'score'                  => ['required_if:answered,1', 'nullable', 'numeric', 'min:0', 'max:100'],
            'memorization_mistakes'  => ['nullable', 'string', 'max:2000'],
            'tashkeel_mistakes'      => ['nullable', 'string', 'max:2000'],
            'notes'                  => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'score'                 => 'الدرجة',
            'memorization_mistakes' => 'أخطاء الحفظ',
            'tashkeel_mistakes'     => 'أخطاء التشكيل',
        ];
    }
}
