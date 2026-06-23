<?php

namespace App\Http\Requests\Teacher;

use App\Traits\ResolvesUserScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class EditTeacherRequest extends FormRequest
{
    use ResolvesUserScope;

    public function authorize(): bool
    {
        return $this->user()->can('edit teachers');
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');

        $accessibleCenterIds = $this->getAccessibleCenters($this->user())->pluck('id');
        $allowedRoles        = $this->allowedRolesFor($this->user()); // ⬅️ تغيّر هنا

        return [
            'name'              => 'required|string|max:255',
            'email'             => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($teacher->user_id),
            ],
            'password'          => 'nullable|string|min:8',
            'current_password' => 'required_with:password|string',
            'center_id'         => ['required', 'integer', Rule::in($accessibleCenterIds)],
            'roles'             => 'required|array|size:1',
            'roles.*'           => ['string', Rule::in($allowedRoles)],
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
            'password.min'       => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف',
            'current_password.required_with' => 'يجب إدخال كلمة المرور الحالية لتغييرها.',
            'center_id.required' => 'الفرع مطلوب',
            'center_id.in'       => 'لا يحق لك نقل المعلم لهذا الفرع',
            'roles.required'     => 'يجب اختيار دور واحد على الأقل',
            'roles.size'         => 'يجب اختيار دور واحد فقط',
            'roles.*.in'         => 'الدور المختار غير مسموح به',
        ];
    }
}
