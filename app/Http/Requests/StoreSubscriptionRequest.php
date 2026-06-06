<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'circle_id' => 'required|exists:circles,id',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|date_format:Y-m',
            'status' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'الطالب مطلوب',
            'student_id.exists' => 'الطالب غير موجود',
            'circle_id.required' => 'الحلقة مطلوبة',
            'circle_id.exists' => 'الحلقة غير موجودة',
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقم',
            'amount.min' => 'المبلغ يجب أن يكون 0 على الأقل',
            'month.required' => 'الشهر مطلوب',
            'month.date_format' => 'صيغة الشهر يجب أن تكون Y-m',
            'status.required' => 'الحالة مطلوبة',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
        ];
    }
}
