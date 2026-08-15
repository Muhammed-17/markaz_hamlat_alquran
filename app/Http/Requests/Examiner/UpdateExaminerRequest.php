<?php

namespace App\Http\Requests\Examiner;

use App\Models\Examiner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateExaminerRequest
 *
 * Validates data for updating an existing Examiner.
 */
class UpdateExaminerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Examiner $examiner */
        $examiner = $this->route('examiner');

        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($examiner->user_id)],
            'password'        => ['nullable', 'string', 'min:8'],
            'status'          => ['required', Rule::in(['active', 'inactive'])],
            'center_id'       => ['nullable', 'integer', 'exists:centers,id'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
            'address'         => ['nullable', 'string', 'max:255'],
            'notes'           => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // الاسم
            'name.required'     => 'يرجى إدخال اسم المحكم.',
            'name.string'       => 'يجب أن يكون الاسم نصاً صالحاً.',
            'name.max'          => 'يجب ألا يتجاوز الاسم 255 حرفاً.',

            // البريد الإلكتروني
            'email.required'    => 'يرجى إدخال البريد الإلكتروني.',
            'email.email'       => 'يرجى إدخال بريد إلكتروني صحيح.',
            'email.max'         => 'يجب ألا يتجاوز البريد الإلكتروني 255 حرفاً.',
            'email.unique'      => 'هذا البريد الإلكتروني مستخدم بالفعل.',

            // كلمة المرور
            'password.string'   => 'كلمة المرور غير صالحة.',
            'password.min'      => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',

            // الحالة
            'status.required'   => 'يرجى تحديد حالة المحكم.',
            'status.in'         => 'القيمة المختارة للحالة غير صالحة.',

            // المركز
            'center_id.integer' => 'معرف المركز يجب أن يكون رقماً صحيحاً.',
            'center_id.exists'  => 'المركز المختار غير موجود.',

            // أرقام الهواتف والعنوان
            'phone.max'           => 'يجب ألا يتجاوز رقم الهاتف 20 حرفاً.',
            'secondary_phone.max' => 'يجب ألا يتجاوز رقم الهاتف الإضافي 20 حرفاً.',
            'address.max'         => 'يجب ألا يتجاوز العنوان 255 حرفاً.',
        ];
    }
}
