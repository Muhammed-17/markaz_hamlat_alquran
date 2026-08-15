<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompetitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'string', 'in:draft,active,closed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => 'اسم المسابقة',
            'description' => 'الوصف',
            'status'      => 'الحالة',
        ];
    }
}
