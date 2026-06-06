<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CreateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string|max:500',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $date = $this->input('date');
            if (!$date) {
                return;
            }

            $start = \App\Models\Setting::getValue('tracking_start', env('TRACKING_START_TIME', '14:00'));
            $end = \App\Models\Setting::getValue('tracking_end', env('TRACKING_END_TIME', '17:00'));
            $now = Carbon::now();

            $sessionStart = Carbon::parse($date . ' ' . $start);
            $sessionEnd = Carbon::parse($date . ' ' . $end);

            if ($now->lessThan($sessionStart)) {
                $validator->errors()->add('date', 'لا يمكن تسجيل الحضور قبل بداية وقت الحلقة (' . $start . ').');
            }

            if ($now->greaterThan($sessionEnd)) {
                $validator->errors()->add('date', 'انتهى وقت تسجيل الحضور (' . $end . '). يمكن تسجيل الغياب فقط من خلال المشرف.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'date.required' => 'التاريخ مطلوب',
            'date.date' => 'التاريخ يجب أن يكون تاريخ صالح',
            'attendance.required' => 'بيانات الحضور مطلوبة',
            'attendance.array' => 'بيانات الحضور يجب أن تكون مصفوفة',
            'attendance.*.student_id.required' => 'الطالب مطلوب',
            'attendance.*.student_id.exists' => 'الطالب غير موجود',
            'attendance.*.status.required' => 'حالة الحضور مطلوبة',
            'attendance.*.status.in' => 'حالة الحضور يجب أن تكون present, absent, late, excused',
        ];
    }
}
