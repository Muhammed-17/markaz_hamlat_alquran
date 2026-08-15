<?php

namespace App\Http\Requests\TafsirFile;

use Illuminate\Foundation\Http\FormRequest;

class StoreTafsirFileRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:tafsir_files,name',
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
