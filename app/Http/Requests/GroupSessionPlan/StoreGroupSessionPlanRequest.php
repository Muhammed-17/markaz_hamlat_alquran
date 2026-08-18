<?php

namespace App\Http\Requests\GroupSessionPlan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request: Store Group Session Plan
 */
class StoreGroupSessionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'circle_id'         => ['required', 'integer', 'exists:circles,id'],
            'session_name'      => ['required', 'string', 'max:255'],
            'start_time'        => ['required', 'date_format:H:i'],
            'end_time'          => ['required', 'date_format:H:i', 'after:start_time'],
            'planned_content'   => ['required', 'string', 'max:2000'],
            'completed_content' => ['nullable', 'string', 'max:2000'],
            'notes'             => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'circle_id.required' => 'اختيار الحلقة مطلوب.',
            'circle_id.integer'  => 'الحلقة المحددة غير صالحة.',
            'circle_id.exists'   => 'الحلقة المحددة غير موجودة.',

            'session_name.required' => 'اسم الجلسة مطلوب.',
            'session_name.string'   => 'اسم الجلسة يجب أن يكون نصًا.',
            'session_name.max'      => 'اسم الجلسة يجب ألا يتجاوز :max حرفًا.',

            'start_time.required'    => 'وقت البداية مطلوب.',
            'start_time.date_format' => 'وقت البداية يجب أن يكون بتنسيق صحيح (ساعة:دقيقة).',

            'end_time.required'    => 'وقت النهاية مطلوب.',
            'end_time.date_format' => 'وقت النهاية يجب أن يكون بتنسيق صحيح (ساعة:دقيقة).',
            'end_time.after'       => 'وقت النهاية يجب أن يكون بعد وقت البداية.',

            'planned_content.required' => 'المحتوى المخطط مطلوب.',
            'planned_content.string'   => 'المحتوى المخطط يجب أن يكون نصًا.',
            'planned_content.max'      => 'المحتوى المخطط يجب ألا يتجاوز :max حرفًا.',

            'completed_content.string' => 'المحتوى المنجز يجب أن يكون نصًا.',
            'completed_content.max'    => 'المحتوى المنجز يجب ألا يتجاوز :max حرفًا.',

            'notes.string' => 'الملاحظات يجب أن تكون نصًا.',
            'notes.max'    => 'الملاحظات يجب ألا تتجاوز :max حرفًا.',
        ];
    }
}
