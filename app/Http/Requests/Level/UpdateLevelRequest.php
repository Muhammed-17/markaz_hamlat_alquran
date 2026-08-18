<?php

namespace App\Http\Requests\Level;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateLevelRequest
 *
 * Validates data for updating an existing Level.
 */
class UpdateLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'                   => ['required', 'string', 'max:255'],
            'description'            => ['nullable', 'string'],
            'memorization_part'      => ['nullable', 'string', 'max:255'],
            'memorization_from_part' => ['nullable', 'string', 'max:255'],
            'memorization_to_part'   => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم المستوى مطلوب.',
            'name.string'   => 'اسم المستوى يجب أن يكون نصًا.',
            'name.max'      => 'اسم المستوى يجب ألا يتجاوز :max حرفًا.',

            'description.string' => 'الوصف يجب أن يكون نصًا.',

            'memorization_part.string' => 'الجزء المحفوظ يجب أن يكون نصًا.',
            'memorization_part.max'    => 'الجزء المحفوظ يجب ألا يتجاوز :max حرفًا.',

            'memorization_from_part.string' => 'حقل "من جزء" يجب أن يكون نصًا.',
            'memorization_from_part.max'    => 'حقل "من جزء" يجب ألا يتجاوز :max حرفًا.',

            'memorization_to_part.string' => 'حقل "إلى جزء" يجب أن يكون نصًا.',
            'memorization_to_part.max'    => 'حقل "إلى جزء" يجب ألا يتجاوز :max حرفًا.',
        ];
    }
}
