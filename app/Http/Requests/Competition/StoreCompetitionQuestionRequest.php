<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitionQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'memorization_from_ayah' => $this->memorization_from_ayah !== null
                ? (int) $this->memorization_from_ayah : null,
            'memorization_to_ayah' => $this->memorization_to_ayah !== null
                ? (int) $this->memorization_to_ayah : null,
            'score' => $this->score !== null ? (float) $this->score : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'competition_level_id' => ['required', 'integer', 'exists:competition_levels,id'],
            'name'  => ['required', 'string', 'max:255'],
            'type'  => ['required', 'string', 'in:memorization,tajweed,tafsir,general,attendance'],
            'score' => ['required', 'numeric', 'min:0'],

            'memorization_from_surah_id' => ['nullable', 'integer', 'exists:surahs,id'],
            'memorization_from_ayah'     => ['nullable', 'integer', 'min:1'],
            'memorization_to_surah_id'   => ['nullable', 'integer', 'exists:surahs,id'],
            'memorization_to_ayah'       => ['nullable', 'integer', 'min:1'],

            'content' => ['nullable', 'string'],
            'notes'   => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'competition_level_id.required' => 'يجب اختيار مستوى المسابقة.',
            'competition_level_id.integer'  => 'مستوى المسابقة غير صالح.',
            'competition_level_id.exists'   => 'مستوى المسابقة المحدد غير موجود.',

            'name.required' => 'عنوان السؤال مطلوب.',
            'name.string'   => 'عنوان السؤال يجب أن يكون نصًا.',
            'name.max'      => 'عنوان السؤال يجب ألا يتجاوز :max حرفًا.',

            'type.required' => 'نوع السؤال مطلوب.',
            'type.string'   => 'نوع السؤال غير صالح.',
            'type.in'       => 'نوع السؤال المحدد غير صالح.',

            'score.required' => 'درجة السؤال مطلوبة.',
            'score.numeric'  => 'درجة السؤال يجب أن تكون رقمًا.',
            'score.min'      => 'درجة السؤال يجب ألا تقل عن :min.',

            'memorization_from_surah_id.integer' => 'بداية السورة غير صالحة.',
            'memorization_from_surah_id.exists'  => 'بداية السورة المحددة غير موجودة.',
            'memorization_from_ayah.integer'     => 'بداية الآية يجب أن تكون رقمًا صحيحًا.',
            'memorization_from_ayah.min'         => 'بداية الآية يجب ألا تقل عن :min.',

            'memorization_to_surah_id.integer' => 'نهاية السورة غير صالحة.',
            'memorization_to_surah_id.exists'  => 'نهاية السورة المحددة غير موجودة.',
            'memorization_to_ayah.integer'     => 'نهاية الآية يجب أن تكون رقمًا صحيحًا.',
            'memorization_to_ayah.min'         => 'نهاية الآية يجب ألا تقل عن :min.',

            'content.string' => 'نص المحتوى يجب أن يكون نصًا.',
            'notes.string'   => 'الملاحظات الإضافية يجب أن تكون نصًا.',
        ];
    }
}
