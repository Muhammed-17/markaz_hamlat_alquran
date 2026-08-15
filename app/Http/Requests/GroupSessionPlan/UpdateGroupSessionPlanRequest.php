<?php

namespace App\Http\Requests\GroupSessionPlan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request: Update Group Session Plan
 */
class UpdateGroupSessionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'circle_id'          => ['sometimes', 'integer', 'exists:circles,id'],
            'session_name'       => ['sometimes', 'string', 'max:255'],
            'start_time'         => ['sometimes', 'date_format:H:i'],
            'end_time'           => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'planned_content'    => ['sometimes', 'string', 'max:2000'],
            'completed_content'  => ['nullable', 'string', 'max:2000'],
            'notes'              => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'circle_id'         => 'الحلقة',
            'session_name'      => 'اسم الجلسة',
            'start_time'        => 'وقت البداية',
            'end_time'          => 'وقت النهاية',
            'planned_content'   => 'المحتوى المخطط',
            'completed_content' => 'المحتوى المنجز',
            'notes'             => 'الملاحظات',
        ];
    }
}
