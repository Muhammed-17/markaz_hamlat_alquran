<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');
        return [
            'name'      => 'required|string|max:255',
            'email'     => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($teacher->user_id),
            ],
            'password'  => 'nullable|string|min:8',
            'center_id' => 'required|integer|exists:centers,id', // ✅
            'roles'   => 'required|array|size:1',
            'roles.*' => 'string|exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'الاسم مطلوب',
            'name.max'           => 'الاسم يجب أن لا يتجاوز 255 حرف',
            'email.required'     => 'البريد الإلكتروني مطلوب',
            'email.email'        => 'يجب أن يكون البريد الإلكتروني صالحًا',
            'email.unique'       => 'البريد الإلكتروني مستخدم بالفعل',
            'password.min'       => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف',
            'center_id.required' => 'الفرع مطلوب',
            'center_id.exists'   => 'الفرع المختار غير موجود',
            'roles.required'     => 'يجب اختيار دور واحد على الأقل',
            'roles.size'         => 'يجب اختيار دور واحد فقط',
            'roles.*.exists'     => 'أحد الأدوار المختارة غير موجود',
        ];
    }
}
