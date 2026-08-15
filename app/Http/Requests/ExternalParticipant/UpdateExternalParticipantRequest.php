<?php

namespace App\Http\Requests\ExternalParticipant;

use App\Models\ExternalParticipant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateExternalParticipantRequest
 *
 * Validates data for updating an existing ExternalParticipant.
 */
class UpdateExternalParticipantRequest extends FormRequest
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
        /** @var ExternalParticipant $externalParticipant */
        $externalParticipant = $this->route('external_participant');

        return [
            'name'             => ['required', 'string', 'max:255'],
            'national_id'      => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('external_participants', 'national_id')->ignore($externalParticipant->id),
            ],
            'phone'            => ['nullable', 'string', 'max:20'],
            'secondary_phone'  => ['nullable', 'string', 'max:20'],
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
            'national_id.unique' => 'هذا الرقم القومي مسجَّل بالفعل لمشارك آخر.',
        ];
    }
}
