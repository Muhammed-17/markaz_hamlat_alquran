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
            'circle_id'          => ['required', 'integer', 'exists:circles,id'],
            'session_name'       => ['required', 'string', 'max:255'],
            'start_time'         => ['required', 'date_format:H:i'],
            'end_time'           => ['required', 'date_format:H:i', 'after:start_time'],
            'planned_content'    => ['required', 'string', 'max:2000'],
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
