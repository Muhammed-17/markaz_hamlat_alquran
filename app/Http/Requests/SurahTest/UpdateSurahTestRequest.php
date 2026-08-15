<?php

namespace App\Http\Requests\SurahTest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\StudentSurahTestResult;

/**
 * Only the per-student results are editable after creation.
 * Surah, circle, test type, teacher, and date are immutable by design
 * (per project requirement) — this request intentionally does not
 * accept them.
 */
class UpdateSurahTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('surah_test'));
    }

    protected function prepareForValidation(): void
    {
        $results = $this->input('results', []);

        if (!is_array($results)) {
            return;
        }

        foreach ($results as $index => $result) {
            foreach (['prompt_errors', 'tashkeel_errors', 'percentage'] as $field) {
                if (!isset($result[$field])) {
                    continue;
                }

                $value = $this->normalizeDigits($result[$field]);

                $results[$index][$field] = ($value === '' || !is_numeric($value))
                    ? 0
                    : (int) $value;
            }
        }

        $this->merge(['results' => $results]);
    }

    protected function normalizeDigits(mixed $value): string
    {
        if (!is_string($value) && !is_numeric($value)) {
            return (string) $value;
        }

        $arabicIndic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $persian     = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $latin       = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $value = str_replace($arabicIndic, $latin, (string) $value);
        $value = str_replace($persian, $latin, $value);

        return trim($value);
    }

    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'exists:teachers,id'],
            'circle_id'  => ['required', 'exists:circles,id'],
            'student_id' => ['required_if:test_type_context,individual', 'nullable', 'exists:students,id'],
            'surah_id'   => ['required', 'exists:surahs,id'],
            'test_date'  => ['required', 'date'],
            'notes'      => ['nullable', 'string'],

            'results'                   => ['required', 'array', 'min:1'],
            'results.*.id'              => ['required', 'integer'],
            'results.*.prompt_errors'   => ['required', 'integer', 'min:0'],
            'results.*.tashkeel_errors' => ['required', 'integer', 'min:0'],
            'results.*.percentage'      => ['required', 'integer', 'min:0', 'max:100'],
            'results.*.level'           => ['nullable', 'string', 'in:' . implode(',', StudentSurahTestResult::LEVELS)],
            'results.*.notes'           => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'results.required' => 'يجب إدخال نتيجة طالب واحد على الأقل.',
        ];
    }

    /**
     * تحقق إضافي بعد الفاليديشن الأساسية.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $results = $this->input('results', []);
            $ids = collect($results)->pluck('id')->filter()->unique()->values()->all();

            if (empty($ids)) {
                return;
            }

            $existingIds = StudentSurahTestResult::whereIn('id', $ids)
                ->pluck('id')
                ->all();

            $missing = array_diff($ids, $existingIds);

            if (!empty($missing)) {
                $validator->errors()->add('results', 'بعض النتائج المرسلة غير موجودة.');
            }
        });
    }
}
