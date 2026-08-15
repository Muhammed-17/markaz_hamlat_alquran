<?php

namespace App\Http\Requests\Competition;

use App\Models\CompetitionParticipant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCompetitionParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'competition_level_id' => [
                'required',
                'integer',
                'exists:competition_levels,id',
            ],

            'center_id' => [
                'required',
                'integer',
                'exists:centers,id',
            ],

            'participant_type' => [
                'required',
                'in:student,external',
            ],

            'circle_id' => [
                'nullable',
                'required_if:participant_type,student',
                'integer',
                'exists:circles,id',
            ],

            'student_id' => [
                'nullable',
                'required_if:participant_type,student',
                'integer',
                'exists:students,id',
            ],

            'external_participant_id' => [
                'nullable',
                'required_if:participant_type,external',
                'integer',
                'exists:external_participants,id',
            ],

            'registration_fee' => [
                'nullable',
                'integer',
                'min:0',
            ],

            /*
             * حالة ملف التفسير
             *
             * 0 = لا يحتاج ملف
             * 1 = لم يستلم
             * 2 = تم الاستلام
             */
            'file_status' => [
                'nullable',
                'integer',
                'in:0,1,2',
            ],

            /*
             * ملف التفسير المختار.
             */
            'tafsir_file_id' => [
                'nullable',
                'integer',
                'exists:tafsir_files,id',
            ],

            /*
             * المشرف.
             */
            'supervisor_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'competition_level_id.required' =>
            'يرجى اختيار المستوى.',

            'competition_level_id.exists' =>
            'المستوى المختار غير موجود.',

            'center_id.required' =>
            'يرجى اختيار الفرع / المركز.',

            'center_id.exists' =>
            'المركز المختار غير موجود.',

            'participant_type.required' =>
            'يرجى تحديد نوع المشارك.',

            'participant_type.in' =>
            'نوع المشارك غير صالح.',

            'student_id.required_if' =>
            'يرجى اختيار الطالب.',

            'student_id.exists' =>
            'الطالب المختار غير موجود.',

            'external_participant_id.required_if' =>
            'يرجى اختيار المشارك الخارجي.',

            'external_participant_id.exists' =>
            'المشارك الخارجي المختار غير موجود.',

            'circle_id.required_if' =>
            'يرجى اختيار الحلقة.',

            'circle_id.exists' =>
            'الحلقة المختارة غير موجودة.',

            'registration_fee.integer' =>
            'رسوم التسجيل يجب أن تكون رقماً صحيحاً.',

            'registration_fee.min' =>
            'رسوم التسجيل يجب ألا تكون بالسالب.',

            'file_status.integer' =>
            'حالة ملف التفسير غير صحيحة.',

            'file_status.in' =>
            'حالة ملف التفسير يجب أن تكون 0 أو 1 أو 2.',

            'tafsir_file_id.exists' =>
            'ملف التفسير المختار غير موجود.',

            'supervisor_id.exists' =>
            'المشرف المختار غير موجود.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tafsir_file_id'           => $this->tafsir_file_id ?: null,
            'external_participant_id'  => $this->external_participant_id ?: null,
            'supervisor_id'            => $this->supervisor_id ?: null,
            'circle_id'                => $this->circle_id ?: null,
            'student_id'                => $this->student_id ?: null,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $competition = $this->route('competition');

            if (!$competition) {
                return;
            }

            $type = $this->input('participant_type');

            if ($type === 'student' && $this->filled('student_id')) {

                $exists = CompetitionParticipant::query()
                    ->where('competition_id', $competition->id)
                    ->where('student_id', $this->student_id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'student_id',
                        'هذا الطالب مسجل بالفعل في هذه المسابقة.'
                    );
                }
            }

            if ($type === 'external' && $this->filled('external_participant_id')) {

                $exists = CompetitionParticipant::query()
                    ->where('competition_id', $competition->id)
                    ->where('external_participant_id', $this->external_participant_id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'external_participant_id',
                        'هذا المشارك الخارجي مسجل بالفعل في هذه المسابقة.'
                    );
                }
            }
        });
    }
}
