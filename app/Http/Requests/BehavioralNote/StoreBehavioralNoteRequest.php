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

    public function messages(): array
    {
        return [
            // Student
            'student_id.required'     => 'يرجى اختيار الطالب.',
            'student_id.integer'      => 'معرف الطالب يجب أن يكون رقمًا صحيحًا.',
            'student_id.exists'        => 'الطالب المحدد غير موجود.',

            // Circle
            'circle_id.required'      => 'يرجى اختيار الحلقة.',
            'circle_id.integer'       => 'معرف الحلقة يجب أن يكون رقمًا صحيحًا.',
            'circle_id.exists'        => 'الحلقة المختارة غير موجودة.',

            // Teacher
            'teacher_id.required'     => 'يرجى اختيار المعلم.',
            'teacher_id.integer'      => 'معرف المعلم يجب أن يكون رقمًا صحيحًا.',
            'teacher_id.exists'        => 'المعلم المحدد غير موجود.',

            // Incident Date
            'incident_at.required'    => 'يرجى تحديد تاريخ السلوك / الواقعة.',
            'incident_at.date'        => 'تاريخ السلوك يجب أن يكون تاريخاً صحيحاً.',

            // Behavior
            'behavior.required'       => 'يرجى إدخال وصف السلوك.',
            'behavior.string'         => 'وصف السلوك يجب أن يكون نصاً.',
            'behavior.max'            => 'وصف السلوك يجب ألا يتجاوز 2000 حرف.',

            // Current Status
            'current_status.string'   => 'الإجراء المتخذ / الحالة الحالية يجب أن تكون نصاً.',
            'current_status.max'      => 'الإجراء المتخذ / الحالة الحالية يجب ألا تتجاوز 2000 حرف.',
        ];
    }
}