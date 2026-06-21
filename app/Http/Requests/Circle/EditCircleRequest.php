<?php

namespace App\Http\Requests\Circle;

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
            'name' => ['required', 'string', 'max:255', 'unique:circles,name,' . $this->route('circle')],
            'type' => 'required|string',
            'level' => 'required|string',
            'center_id' => 'required|exists:centers,id',
            'teacher_id' => 'required|exists:teachers,id',
            'assistant_teacher_id' => 'nullable|exists:teachers,id',
            'supervisor_ids' => 'required|array|min:1',
            'supervisor_ids.*' => 'exists:teachers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب.',
            'name.string' => 'حقل الاسم يجب أن يكون نصًا.',
            'name.max' => 'حقل الاسم لا يجب أن يتجاوز 255 حرفًا.',
            'name.unique' => 'اسم الحلقة مستخدم بالفعل، يرجى اختيار اسم آخر.',
            'type.required' => 'حقل النوع مطلوب.',
            'type.string' => 'حقل النوع يجب أن يكون نصًا.',
            'level.required' => 'حقل المستوى مطلوب.',
            'level.string' => 'حقل المستوى يجب أن يكون نصًا.',
            'center_id.required' => 'حقل الفرع مطلوب.',
            'center_id.exists' => 'الفرع المحدد غير موجود.',
            'teacher_id.required' => 'حقل المعلم الرئيسي مطلوب.',
            'teacher_id.exists' => 'المعلم الرئيسي المحدد غير موجود.',
            'assistant_teacher_id.exists' => 'المعلم المساعد المحدد غير موجود.',
            'supervisor_ids.required' => 'يجب اختيار مشرف واحد على الأقل.',
            'supervisor_ids.array' => 'حقل المشرفين يجب أن يكون قائمة.',
            'supervisor_ids.min' => 'يجب اختيار مشرف واحد على الأقل.',
            'supervisor_ids.*.exists' => 'أحد المشرفين المحددين غير موجود.',
        ];
    }
    protected function prepareForValidation(): void
    {
        $name = trim($this->name);
        $this->merge([
            'name' => str_starts_with($name, 'حلقة') ? $name : 'حلقة ' . $name,
        ]);
    }
}
