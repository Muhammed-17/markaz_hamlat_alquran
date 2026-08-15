<?php

namespace App\Http\Requests\BehavioralNote;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request: Update Behavioral Note
 */
class UpdateBehavioralNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id'     => ['sometimes', 'integer', 'exists:students,id'],
            'circle_id'      => ['sometimes', 'integer', 'exists:circles,id'],
            'teacher_id'     => ['sometimes', 'integer', 'exists:teachers,id'],
            'incident_at'    => ['sometimes', 'date'],
            'behavior'       => ['sometimes', 'string', 'max:2000'],
            'current_status' => ['nullable', 'string', 'max:2000'],
            'follow_up_at'   => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_id'     => 'الطالب',
            'circle_id'      => 'الحلقة',
            'teacher_id'     => 'المعلم',
            'incident_at'    => 'وقت الحادثة',
            'behavior'       => 'السلوك',
            'current_status' => 'الحالة الحالية',
            'follow_up_at'   => 'تاريخ المتابعة',
        ];
    }
}
