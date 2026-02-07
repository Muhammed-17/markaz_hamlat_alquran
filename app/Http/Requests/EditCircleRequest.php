<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCircleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'level' => 'required|string',
            'max_students' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            // 'teacher_id' => 'required|exists:teachers,id',
            // 'assistant_teacher_id' => 'nullable|exists:teachers,id',
            // 'supervisor_id' => 'required|exists:teachers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب.',
            'name.string' => 'حقل الاسم يجب أن يكون نصًا.',
            'name.max' => 'حقل الاسم لا يجب أن يتجاوز 255 حرفًا.',
            'type.required' => 'حقل النوع مطلوب.',
            'type.string' => 'حقل النوع يجب أن يكون نصًا.',
            'level.required' => 'حقل المستوى مطلوب.',
            'level.string' => 'حقل المستوى يجب أن يكون نصًا.',
            'max_students.integer' => 'حقل الحد الأقصى للطلاب يجب أن يكون رقمًا صحيحًا.',
            'max_students.min' => 'حقل الحد الأقصى للطلاب يجب أن يكون على الأقل 1.',
            'notes.string' => 'حقل الملاحظات يجب أن يكون نصًا.',
            // 'teacher_id.required' => 'حقل المعلم الرئيسي مطلوب.',
            // 'teacher_id.exists' => 'المعلم الرئيسي المحدد غير موجود.',
            // 'assistant_teacher_id.exists' => 'المعلم المساعد المحدد غير موجود.',
            // 'supervisor_id.required' => 'حقل المشرف مطلوب.',
            // 'supervisor_id.exists' => 'المشرف المحدد غير موجود.',
        ];
    }
}
