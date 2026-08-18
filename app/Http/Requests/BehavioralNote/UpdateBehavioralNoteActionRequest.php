<?php

namespace App\Http\Requests\BehavioralNote;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\BehavioralNote;

class UpdateBehavioralNoteActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action_taken' => ['required', 'string', 'max:2000'],
            'status'       => ['required', 'string', Rule::in(array_keys(BehavioralNote::STATUSES))],
            'follow_up_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            // Action Taken
            'action_taken.required' => 'يرجى إدخال الإجراء المتخذ.',
            'action_taken.string'   => 'الإجراء المتخذ يجب أن يكون نصاً.',
            'action_taken.max'      => 'الإجراء المتخذ يجب ألا يتجاوز 2000 حرف.',

            // Status
            'status.required'       => 'يرجى تحديد حالة الملاحظة.',
            'status.in'             => 'حالة الملاحظة المختارة غير صالحة.',

            // Follow-up Date
            'follow_up_at.date'     => 'تاريخ المتابعة يجب أن يكون تاريخاً صحيحاً.',
        ];
    }
}
