<?php

namespace App\Http\Requests\BehavioralNote;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\BehavioralNote;

class UpdateBehavioralNoteActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الـ authorize الفعلي بيتم في الـ controller عبر $this->authorize()
    }

    public function rules(): array
    {
        return [
            'action_taken' => ['required', 'string', 'max:2000'],
            'status'       => ['required', 'string', Rule::in(array_keys(BehavioralNote::STATUSES))],
            'follow_up_at' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'action_taken' => 'الإجراء المتخذ',
            'status'       => 'حالة الإجراء',
            'follow_up_at' => 'تاريخ المتابعة',
        ];
    }
}
