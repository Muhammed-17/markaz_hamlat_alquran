<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;

class CreateAttendanceRequest extends FormRequest
{
    // ── تحقق من الصلاحية ────────────────────────────────────────
    public function authorize(): bool
    {
        return $this->user()->can('create', Attendance::class);
    }

    // ── قواعد التحقق ────────────────────────────────────────────
    public function rules(): array
    {
        return [
            'date'                        => 'required|date',
            'circle_id'                   => 'required|exists:circles,id',
            'attendance'                  => 'required|array',
            'attendance.*.student_id'     => 'required|exists:students,id',
            'attendance.*.status'         => 'required|in:present,absent,late,excused',
            'attendance.*.notes'          => 'nullable|string|max:500',
        ];
    }

    // ── رسائل الخطأ ─────────────────────────────────────────────
    public function messages(): array
    {
        return [
            'date.required'                    => 'التاريخ مطلوب',
            'date.date'                        => 'التاريخ يجب أن يكون تاريخاً صالحاً',
            'circle_id.required'               => 'الحلقة مطلوبة',
            'circle_id.exists'                 => 'الحلقة المحددة غير موجودة',
            'attendance.required'              => 'بيانات الحضور مطلوبة',
            'attendance.array'                 => 'بيانات الحضور يجب أن تكون مصفوفة',
            'attendance.*.student_id.required' => 'الطالب مطلوب',
            'attendance.*.student_id.exists'   => 'الطالب غير موجود',
            'attendance.*.status.required'     => 'حالة الحضور مطلوبة',
            'attendance.*.status.in'           => 'حالة الحضور يجب أن تكون: present, absent, late, excused',
        ];
    }
}
