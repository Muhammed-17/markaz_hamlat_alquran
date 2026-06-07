<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class CreateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8',
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
            'password.required'  => 'كلمة المرور مطلوبة',
            'password.min'       => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف',
            'center_id.required' => 'الفرع مطلوب',
            'center_id.exists'   => 'الفرع المختار غير موجود',
            'roles.required'     => 'يجب اختيار دور واحد على الأقل',
            'roles.size'         => 'يجب اختيار دور واحد فقط',
            'roles.*.exists'     => 'أحد الأدوار المختارة غير موجود',
        ];
    }
}
