<?php

namespace App\Http\Requests\Teacher;

use App\Traits\ResolvesUserScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class CreateTeacherRequest extends FormRequest
{
    use ResolvesUserScope;

    public function authorize(): bool
    {
        return $this->user()->can('create teachers');
    }

    public function rules(): array
    {
        $accessibleCenterIds = $this->getAccessibleCenters($this->user())->pluck('id');
        $allowedRoles        = $this->allowedRolesFor($this->user()); // ⬅️ تغيّر هنا

        return [
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'password'          => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'center_id'         => ['required', 'integer', Rule::in($accessibleCenterIds)],
            'roles'             => 'required|array|size:1',
            'roles.*'           => ['string', Rule::in($allowedRoles)], // ⬅️ بقي كما هو
            'is_administrative' => 'nullable|boolean', // ⬅️ إضافة جديدة
        ];
    }

    // ⬅️ دالة جديدة تُضاف داخل الكلاس
    private function allowedRolesFor($user): \Illuminate\Support\Collection
    {
        if ($user->can('assign any role')) {
            return Role::whereNotIn('name', ['admin', 'guardian'])->pluck('name');
        }

        if ($user->can('assign manager role')) {
            return Role::whereNotIn('name', ['admin', 'guardian', 'general_manager'])->pluck('name');
        }

        return Role::whereNotIn('name', ['admin', 'guardian', 'general_manager', 'manager'])->pluck('name');
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
            'center_id.in'       => 'لا يحق لك إضافة معلم لهذا الفرع',
            'roles.required'     => 'يجب اختيار دور واحد على الأقل',
            'roles.size'         => 'يجب اختيار دور واحد فقط',
            'roles.*.in'         => 'الدور المختار غير مسموح به',
        ];
    }
}
