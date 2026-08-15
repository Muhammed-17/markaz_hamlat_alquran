<?php

namespace App\Http\Requests\BehavioralNote;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request: Store Behavioral Note
 */
class StoreBehavioralNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id'     => ['required', 'integer', 'exists:students,id'],
            'circle_id'      => ['required', 'integer', 'exists:circles,id'],
            'teacher_id'     => ['required', 'integer', 'exists:teachers,id'],
            'incident_at'    => ['required', 'date'],
            'behavior'       => ['required', 'string', 'max:2000'],
            'current_status' => ['nullable', 'string', 'max:2000'],
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
        ];
    }
}
