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
            'competition_level_id.required' => 'يجب اختيار المستوى.',
            'competition_level_id.integer'  => 'المستوى غير صالح.',
            'competition_level_id.exists'   => 'المستوى المحدد غير موجود.',

            'name.required' => 'اسم السؤال مطلوب.',
            'name.string'   => 'اسم السؤال يجب أن يكون نصًا.',
            'name.max'      => 'اسم السؤال يجب ألا يتجاوز :max حرفًا.',

            'type.required' => 'نوع السؤال مطلوب.',
            'type.string'   => 'نوع السؤال غير صالح.',
            'type.in'       => 'نوع السؤال المحدد غير صالح.',

            'score.required' => 'الدرجة مطلوبة.',
            'score.numeric'  => 'الدرجة يجب أن تكون رقمًا.',
            'score.min'      => 'الدرجة يجب ألا تقل عن :min.',

            'memorization_from_surah_id.integer' => 'السورة (من) غير صالحة.',
            'memorization_from_surah_id.exists'   => 'السورة المحددة (من) غير موجودة.',
            'memorization_from_ayah.integer'      => 'رقم الآية (من) يجب أن يكون رقمًا صحيحًا.',
            'memorization_from_ayah.min'          => 'رقم الآية (من) يجب ألا يقل عن :min.',

            'memorization_to_surah_id.integer' => 'السورة (إلى) غير صالحة.',
            'memorization_to_surah_id.exists'   => 'السورة المحددة (إلى) غير موجودة.',
            'memorization_to_ayah.integer'      => 'رقم الآية (إلى) يجب أن يكون رقمًا صحيحًا.',
            'memorization_to_ayah.min'          => 'رقم الآية (إلى) يجب ألا يقل عن :min.',

            'content.string' => 'المحتوى يجب أن يكون نصًا.',
            'notes.string'   => 'الملاحظات يجب أن تكون نصًا.',
        ];
    }

    public function attributes(): array
    {
        return [
            'competition_level_id' => 'المستوى',
            'name'  => 'اسم السؤال',
            'type'  => 'نوع السؤال',
            'score' => 'الدرجة',

            'memorization_from_surah_id' => 'السورة (من)',
            'memorization_from_ayah'     => 'الآية (من)',
            'memorization_to_surah_id'   => 'السورة (إلى)',
            'memorization_to_ayah'       => 'الآية (إلى)',

            'content' => 'المحتوى',
            'notes'   => 'الملاحظات',
        ];
    }
}
