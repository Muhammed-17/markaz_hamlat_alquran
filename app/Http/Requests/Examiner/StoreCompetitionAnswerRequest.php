<?php

namespace App\Http\Requests\Examiner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreCompetitionAnswerRequest
 *
 * يتحقق من صحة بيانات تقييم سؤال أثناء الاختبار.
 */
class StoreCompetitionAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('examine', $this->route('participant'));
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'answered'              => $this->boolean('answered'),
            'memorization_mistakes' => $this->input('memorization_mistakes', 0),
            'tashkeel_mistakes'     => $this->input('tashkeel_mistakes', 0),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'competition_question_id' => ['required', 'integer', 'exists:competition_questions,id'],
            'answered'                => ['required', 'boolean'],
            'memorization_mistakes'   => ['nullable', 'integer', 'min:0'],
            'tashkeel_mistakes'       => ['nullable', 'integer', 'min:0'],
            'notes'                   => ['nullable', 'string', 'max:2000'],
            'action'                  => ['required', Rule::in(['previous', 'save', 'next', 'finish'])],
        ];
    }
    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'competition_question_id.required' => 'يجب تحديد السؤال المراد تقييمه.',
            'competition_question_id.exists'   => 'السؤال المحدد غير موجود في النظام.',

            'answered.required'                => 'يرجى تحديد ما إذا كان المشارك قد أجاب على السؤال أم لا.',

            'memorization_mistakes.integer'    => 'عدد أخطاء الحفظ يجب أن يكون رقماً صحيحاً.',
            'memorization_mistakes.min'        => 'عدد أخطاء الحفظ لا يمكن أن يكون بالسالب.',

            'tashkeel_mistakes.integer'        => 'عدد أخطاء التشكيل يجب أن يكون رقماً صحيحاً.',
            'tashkeel_mistakes.min'            => 'عدد أخطاء التشكيل لا يمكن أن يكون بالسالب.',

            'notes.max'                        => 'الملاحظات يجب ألا تتجاوز 2000 حرف.',

            'action.required'                  => 'يرجى تحديد الإجراء المطلوب.',
            'action.in'                        => 'الإجراء المطلوب غير صالح.',
        ];
    }
}
