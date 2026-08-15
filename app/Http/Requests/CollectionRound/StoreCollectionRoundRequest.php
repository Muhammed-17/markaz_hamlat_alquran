<?php

namespace App\Http\Requests\CollectionRound;

use App\Traits\ResolvesUserScope;
use Illuminate\Foundation\Http\FormRequest;

class StoreCollectionRoundRequest extends FormRequest
{
    use ResolvesUserScope;

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
        $rules = [
            'circle_id'                    => 'required|exists:circles,id',
            'period_month'                 => 'required|date_format:Y-m',
            'selected_subscription_ids'    => 'required|array|min:1',
            'selected_subscription_ids.*'  => 'required|integer|exists:subscriptions,id',
            'total_amount'                 => 'required|numeric|min:0',
            'supervisor_note'              => 'nullable|string|max:1000',
        ];

        // فقط admin/general_manager يمكنهم إرسال created_by
        if ($this->user()->hasAnyRole(['admin', 'general_manager'])) {
            $rules['created_by'] = [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (empty($value)) return;

                    $selectedUser = \App\Models\User::find($value);

                    if (!$selectedUser) {
                        return;
                    }

                    $hasRole = $selectedUser->hasAnyRole(['supervisor', 'manager', 'general_manager']);

                    if (!$hasRole) {
                        $fail('المنشئ المختار لا يملك الصلاحية (يجب أن يكون مشرف أو مدير).');
                        return;
                    }

                    // تحقق إضافي: هل المنشئ المختار فعليًا مسؤول عن هذه الحلقة تحديدًا؟
                    $circleId = (int) $this->input('circle_id');
                    if ($circleId && !$this->getAccessibleCircleIds($selectedUser)->contains($circleId)) {
                        $fail('المنشئ المختار ليس مسؤولاً عن هذه الحلقة، لا يمكن تسجيل التحصيل باسمه.');
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
            'circle_id.required'                 => 'الحلقة مطلوبة',
            'circle_id.exists'                   => 'الحلقة المحددة غير موجودة',
            'period_month.required'              => 'الشهر مطلوب',
            'period_month.date_format'           => 'صيغة الشهر غير صحيحة (يجب أن تكون Y-m)',
            'selected_subscription_ids.required' => 'يجب تحديد اشتراك واحد على الأقل',
            'selected_subscription_ids.array'    => 'يجب إرسال قائمة بالاشتراكات المختارة',
            'selected_subscription_ids.min'      => 'يجب تحديد اشتراك واحد على الأقل',
            'selected_subscription_ids.*.integer'  => 'معرّف الاشتراك يجب أن يكون رقمًا',
            'selected_subscription_ids.*.exists' => 'الاشتراك المحدد غير موجود',
            'total_amount.required'              => 'إجمالي المبلغ مطلوب',
            'total_amount.numeric'               => 'المبلغ يجب أن يكون رقمًا',
            'total_amount.min'                   => 'المبلغ لا يمكن أن يكون أقل من صفر',
            'supervisor_note.max'                => 'ملاحظة المشرف لا يمكن أن تتجاوز 1000 حرف',
            'created_by.exists'                  => 'المنشئ المختار غير موجود.',
        ];
    }
}
