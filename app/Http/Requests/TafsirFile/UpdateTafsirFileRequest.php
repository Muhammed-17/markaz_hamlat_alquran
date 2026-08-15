<?php

namespace App\Http\Requests\TafsirFile;

use App\Models\TafsirFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTafsirFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        /** @var TafsirFile $tafsirFile */
        $tafsirFile = $this->route('tafsirFile');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tafsir_files', 'name')
                    ->ignore($tafsirFile?->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    /**
     * Validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم ملف التفسير مطلوب.',
            'name.string' => 'اسم ملف التفسير يجب أن يكون نصًا.',
            'name.max' => 'اسم ملف التفسير يجب ألا يتجاوز 255 حرفًا.',
            'name.unique' => 'ملف التفسير بهذا الاسم موجود بالفعل.',

            'description.string' => 'الوصف يجب أن يكون نصًا.',
            'description.max' => 'الوصف يجب ألا يتجاوز 2000 حرف.',
        ];
    }

    /**
     * Validation attributes.
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم ملف التفسير',
            'description' => 'الوصف',
        ];
    }
}
