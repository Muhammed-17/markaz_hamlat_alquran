<?php

namespace App\Http\Requests\CollectionRound;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmCollectionRoundRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مصرّحًا له بتقديم هذا الطلب
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق من صحة المدخلات
     */
    public function rules(): array
    {
        $rules = [];

        // فقط admin يمكنه اختيار مدير آخر لتسجيل التأكيد باسمه
        if ($this->user()->hasRole('admin')) {
            $rules['confirmed_by'] = [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $hasRole = \App\Models\User::where('id', $value)
                        ->whereHas('roles', function ($q) {
                            $q->whereIn('name', ['manager', 'general_manager']);
                        })
                        ->exists();

                    if (!$hasRole) {
                        $fail('المستخدم المختار يجب أن يكون مديرًا أو مديرًا عامًا.');
                    }
                },
            ];
        }

        return $rules;
    }

    /**
     * رسائل الخطأ المخصّصة بالعربية
     */
    public function messages(): array
    {
        return [
            'confirmed_by.required' => 'يجب اختيار مدير لتسجيل التأكيد باسمه.',
            'confirmed_by.exists'   => 'المستخدم المختار غير موجود.',
        ];
    }
}
