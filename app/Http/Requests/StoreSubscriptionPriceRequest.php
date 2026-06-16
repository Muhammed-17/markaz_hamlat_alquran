<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'circle_level' => 'required|string|max:255',
            'education_stage' => 'required|string|max:255',
            'school_grade' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'circle_level.required' => 'مستوى الحلقة مطلوب',
            'education_stage.required' => 'المستوى التعليمي مطلوب',
            'school_grade.string' => 'الصف الدراسي يجب أن يكون نص',
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقم',
            'amount.min' => 'المبلغ يجب أن يكون 0 على الأقل',
        ];
    }
}
