<?php

namespace App\Http\Requests\Teacher;

use App\Traits\HasAllowedRoles;
use App\Traits\ResolvesUserScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateTeacherRequest extends FormRequest
{
    use ResolvesUserScope;
    use HasAllowedRoles;

    public function authorize(): bool
    {
        return $this->user()->can('create teachers');
    }

    public function rules(): array
    {
        $accessibleCenterIds = $this->getAccessibleCenters($this->user())->pluck('id');
        
        // ✅ استخدام الـ Trait مع hasRole
        $allowedRoles = $this->getAllowedRolesForCreate($this->user())->pluck('name');

        return [
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'password'          => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'center_id'         => ['required', 'integer', Rule::in($accessibleCenterIds)],
            'roles'             => 'required|array|size:1',
            'roles.*'           => ['string', Rule::in($allowedRoles)],
            'is_administrative' => 'nullable|boolean',
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
            'password.min'       => 'يجب أن تكون كلمة المرور على الأقل 8 أحرف',
            'password.mixed'     => 'يجب أن تحتوي كلمة المرور على حرف كبير وحرف صغير على الأقل',
            'password.numbers'   => 'يجب أن تحتوي كلمة المرور على رقم على الأقل',
            'password.symbols'   => 'يجب أن تحتوي كلمة المرور على رمز خاص على الأقل',
            'center_id.required' => 'الفرع مطلوب',
            'center_id.in'       => 'لا يحق لك إضافة معلم لهذا الفرع',
            'roles.required'     => 'يجب اختيار دور واحد على الأقل',
            'roles.size'         => 'يجب اختيار دور واحد فقط',
            'roles.*.in'         => 'الدور المختار غير مسموح به',
        ];
    }
}