<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateCompetitionResultRequest
 *
 * يستخدم عند حفظ/إعادة حساب نتيجة مشارك من لوحة الإدارة.
 */
class UpdateCompetitionResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('result'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['recalculate', 'save'])],
        ];
    }
}
