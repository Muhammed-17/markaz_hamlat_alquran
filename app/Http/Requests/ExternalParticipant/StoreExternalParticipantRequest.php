<?php

namespace App\Http\Requests\ExternalParticipant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreExternalParticipantRequest
 *
 * Validates data for creating a new ExternalParticipant.
 */
class StoreExternalParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'national_id'      => [
                'nullable',
                'string',
                'digits:14',
                Rule::unique('external_participants', 'national_id'),
            ],
            'phone'            => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'secondary_phone'  => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'address'          => ['nullable', 'string', 'max:255'],
            'date_of_birth'    => ['nullable', 'date', 'before:today'],
            'gender'           => ['nullable', 'string', Rule::in(['male', 'female'])],
            'notes'            => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name'            => 'الاسم',
            'national_id'     => 'الرقم القومي',
            'phone'           => 'رقم الهاتف',
            'secondary_phone' => 'رقم الهاتف الإضافي',
            'address'         => 'العنوان',
            'date_of_birth'   => 'تاريخ الميلاد',
            'gender'          => 'النوع',
            'notes'           => 'ملاحظات',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'national_id.unique'  => 'هذا الرقم القومي مسجَّل بالفعل لمشارك آخر.',
            'national_id.digits'  => 'الرقم القومي يجب أن يتكوّن من 14 رقمًا بالضبط.',
            'phone.regex'         => 'رقم الهاتف يجب أن يحتوي على أرقام فقط.',
            'secondary_phone.regex' => 'رقم الهاتف الإضافي يجب أن يحتوي على أرقام فقط.',
        ];
    }
}
