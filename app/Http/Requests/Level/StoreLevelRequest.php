<?php

namespace App\Http\Requests\Level;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreLevelRequest
 *
 * Validates data for creating a new Level.
 */
class StoreLevelRequest extends FormRequest
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
            'name'                       => ['required', 'string', 'max:255'],
            'description'                => ['nullable', 'string'],
            'memorization_part'          => ['nullable', 'string', 'max:255'],
            'memorization_from_part'     => ['nullable', 'string', 'max:255'],
            'memorization_to_part'       => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name'                   => 'اسم المستوى',
            'description'            => 'الوصف',
            'memorization_part'      => 'الجزء المحفوظ',
            'memorization_from_part' => 'من جزء',
            'memorization_to_part'   => 'إلى جزء',
        ];
    }
}
