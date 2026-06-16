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
            'student_id'     => 'required|exists:students,id',
            'circle_id'      => 'required|exists:circles,id',
            'amount'         => 'nullable|numeric|min:0',
            'month'          => 'required|date_format:Y-m',
            'status'         => 'required|in:مدفوع,معفي',
            'payment_method' => 'nullable|in:نقدي,تحويل بنكي,أخرى',
            'notes'          => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'الطالب مطلوب',
            'student_id.exists'    => 'الطالب غير موجود',
            'circle_id.required'  => 'الحلقة مطلوبة',
            'circle_id.exists'    => 'الحلقة غير موجودة',
            'amount.numeric'      => 'المبلغ يجب أن يكون رقم',
            'amount.min'          => 'المبلغ يجب أن يكون 0 على الأقل',
            'month.required'      => 'الشهر مطلوب',
            'month.date_format'   => 'صيغة الشهر يجب أن تكون Y-m',
            'status.required'     => 'الحالة مطلوبة',
            'status.in'           => 'حالة السداد غير صحيحة',
            'payment_method.in'   => 'طريقة الدفع غير صحيحة',
        ];
    }
}
