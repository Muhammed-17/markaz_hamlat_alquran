<?php

namespace App\Http\Requests\Teacher;

use App\Traits\HasAllowedRoles;
use App\Traits\ResolvesUserScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EditTeacherRequest extends FormRequest
{
    use ResolvesUserScope;
    use HasAllowedRoles;

    public function authorize(): bool
    {
        return $this->user()->can('edit teachers');
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');
        $userId = $teacher?->user_id;

        $accessibleCenterIds = $this->getAccessibleCenters($this->user())->pluck('id');

        // ✅ استخدام الـ Trait مع hasRole
        $allowedRoles = $this->getAllowedRolesForEdit($this->user(), $teacher)->pluck('name');

        $rules = [
            'name'              => 'required|string|max:255',
            'email'             => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password'          => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'center_id'         => ['required', 'integer', Rule::in($accessibleCenterIds)],
            'roles'             => 'required|array|size:1',
            'roles.*'           => ['string', Rule::in($allowedRoles)],
            'is_administrative' => 'nullable|boolean',
        ];

        // ✅ current_password: إلزامي فقط إذا:
        // 1. تم إرسال password (يريد تغييره)
        // 2. المستخدم ليس admin/general_manager/manager
        // 3. المستخدم يعدل نفسه
        if (
            $this->filled('password') &&
            !$this->user()->hasRole(['admin', 'general_manager', 'manager']) &&
            $this->user()->id === $userId
        ) {
            $rules['current_password'] = 'required|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'                  => 'الاسم مطلوب',
            'name.max'                       => 'الاسم يجب أن لا يتجاوز 255 حرف',
            'email.required'                 => 'البريد الإلكتروني مطلوب',
            'email.email'                    => 'يجب أن يكون البريد الإلكتروني صالحًا',
            'email.unique'                   => 'البريد الإلكتروني مستخدم بالفعل',
            'password.min'                   => 'يجب أن تكون كلمة المرور على الأقل 8 أحرف',
            'password.mixed'                 => 'يجب أن تحتوي كلمة المرور على حرف كبير وحرف صغير على الأقل',
            'password.numbers'               => 'يجب أن تحتوي كلمة المرور على رقم على الأقل',
            'password.symbols'               => 'يجب أن تحتوي كلمة المرور على رمز خاص على الأقل',
            'current_password.required'      => 'يجب إدخال كلمة المرور الحالية لتغييرها',
            'current_password.string'          => 'يجب أن تكون كلمة المرور الحالية نصاً',
            'center_id.required'             => 'الفرع مطلوب',
            'center_id.in'                   => 'لا يحق لك نقل المعلم لهذا الفرع',
            'roles.required'                 => 'يجب اختيار دور واحد على الأقل',
            'roles.size'                     => 'يجب اختيار دور واحد فقط',
            'roles.*.in'                     => 'الدور المختار غير مسموح به',
        ];
    }
}
