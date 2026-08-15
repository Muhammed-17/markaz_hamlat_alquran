<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class FinalizeCompetitionRequest
 *
 * يستخدم عند إعادة حساب الترتيب / اعتماد النتائج / إغلاق المسابقة.
 */
class FinalizeCompetitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finalize', \App\Models\CompetitionResult::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['recalculate_rank', 'finalize', 'close'])],
        ];
    }
}
