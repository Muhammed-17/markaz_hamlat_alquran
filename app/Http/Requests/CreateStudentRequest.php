<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudentRequest extends FormRequest
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
            'description' => 'nullable|string|max:255',
            'guardian_id' => 'nullable|exists:users,id',
            'circle_id' => 'nullable|exists:circles,id',
            'gender' => 'required',
            'education_level' => 'required',
            'age' => 'nullable|integer',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string',
            'second_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'required',
            'current_surah' => 'nullable|string|max:255',
            'enrollment_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.max' => 'الاسم يجب أن لا يتجاوز 255 حرف',
            'description.max' => 'الوصف يجب أن لا يتجاوز 255 حرف',
            'guardian_id.exists' => 'الولي غير موجود',
            'circle_id.exists' => 'الدائرة غير موجودة',
            'gender.required' => 'الجنس مطلوب',
            'education_level.required' => 'المستوى التعليمي مطلوب',
            'age.integer' => 'العمر يجب أن يكون رقم',
            'date_of_birth.date' => 'تاريخ الميلاد يجب أن يكون تاريخ',
            'phone.string' => 'رقم الهاتف يجب أن يكون نص',
            'second_phone.string' => 'رقم الهاتف الثاني يجب أن يكون نص',
            'address.string' => 'العنوان يجب أن يكون نص',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة يجب أن تكون مقيد,متوقف,مسافر',
            'current_surah.max' => 'السورة الحالية يجب أن لا تتجاوز 255 حرف',
            'enrollment_date.date' => 'تاريخ الالتحاق يجب أن يكون تاريخ',
        ];
    }
}
