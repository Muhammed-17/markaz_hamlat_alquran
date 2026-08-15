<?php

namespace App\Http\Requests\GuardianAccounts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class StoreGuardianAccountsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage guardians');
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'center_id' => ['nullable', 'exists:centers,id'],
            'password'  => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'      => 'الاسم',
            'email'     => 'البريد الإلكتروني',
            'center_id' => 'الفرع',
            'password'  => 'كلمة المرور',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'حقل :attribute مطلوب.',
            'name.max'            => 'يجب ألا يتجاوز :attribute :max حرفاً.',
            'email.required'      => 'حقل :attribute مطلوب.',
            'email.email'         => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالحاً.',
            'email.max'           => 'يجب ألا يتجاوز :attribute :max حرفاً.',
            'email.unique'        => 'هذا :attribute مستخدم بالفعل.',
            'center_id.exists'    => 'الفرع المحدد غير موجود.',
            'password.required'   => 'حقل :attribute مطلوب.',
            'password.confirmed'  => 'تأكيد :attribute غير متطابق.',
            'password.min'        => 'يجب أن تكون :attribute على الأقل :min أحرف.',
        ];
    }
}