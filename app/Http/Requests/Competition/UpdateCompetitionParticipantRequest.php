<?php

namespace App\Http\Requests\Competition;

use App\Models\CompetitionParticipant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCompetitionParticipantRequest extends FormRequest
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

    /**
     * تجهيز البيانات قبل التحقق.
     */
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

    public function messages(): array
    {
        return [
            'competition_level_id.required' =>
            'يجب اختيار المستوى.',

            'competition_level_id.exists' =>
            'المستوى المختار غير موجود.',

            'center_id.required' =>
            'يجب اختيار المركز.',

            'center_id.exists' =>
            'المركز المختار غير موجود.',

            'participant_type.required' =>
            'يجب تحديد نوع المشارك.',

            'participant_type.in' =>
            'نوع المشارك غير صالح.',

            'circle_id.required_if' =>
            'يجب اختيار الحلقة.',

            'circle_id.exists' =>
            'الحلقة المختارة غير موجودة.',

            'student_id.required_if' =>
            'يجب اختيار الطالب.',

            'student_id.exists' =>
            'الطالب المختار غير موجود.',

            'external_participant_id.required_if' =>
            'يجب اختيار المشارك الخارجي.',

            'external_participant_id.exists' =>
            'المشارك الخارجي المختار غير موجود.',

            'registration_fee.integer' =>
            'رسوم التسجيل يجب أن تكون رقماً صحيحاً.',

            'registration_fee.min' =>
            'رسوم التسجيل لا يمكن أن تكون أقل من 0.',

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

    /**
     * التحقق المتقدم:
     * - منع تسجيل الطالب مرتين في نفس المسابقة.
     * - منع تسجيل المشارك الخارجي مرتين.
     * - تصفية الحقول غير المستخدمة حسب نوع المشارك.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $competition = $this->route('competition');
            $participant = $this->route('participant');

            if (!$competition || !$participant) {
                return;
            }

            $type = $this->input('participant_type');

            /*
             * الطالب الداخلي.
             */
            if ($type === 'student' && $this->filled('student_id')) {

                $exists = CompetitionParticipant::query()
                    ->where('competition_id', $competition->id)
                    ->where('student_id', $this->student_id)
                    ->where('id', '!=', $participant->id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'student_id',
                        'هذا الطالب مسجل بالفعل في هذه المسابقة.'
                    );
                }

                /*
                 * الطالب لا يحتاج external participant.
                 */
                $this->merge([
                    'external_participant_id' => null,
                ]);
            }

            /*
             * المشارك الخارجي.
             */
            if ($type === 'external' && $this->filled('external_participant_id')) {

                $exists = CompetitionParticipant::query()
                    ->where('competition_id', $competition->id)
                    ->where(
                        'external_participant_id',
                        $this->external_participant_id
                    )
                    ->where('id', '!=', $participant->id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'external_participant_id',
                        'هذا المشارك الخارجي مسجل بالفعل في هذه المسابقة.'
                    );
                }

                /*
                 * المشارك الخارجي لا يحتاج:
                 * student_id
                 * circle_id
                 */
                $this->merge([
                    'student_id' => null,
                    'circle_id' => null,
                ]);
            }
        });
    }
}
