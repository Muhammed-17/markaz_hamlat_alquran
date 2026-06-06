<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SequentialAbsenceNotification extends Notification
{
    use Queueable;

    public Student $student;
    public int $absenceDays;
    public ?string $customMessage;

    public function __construct(Student $student, int $absenceDays, ?string $customMessage = null)
    {
        $this->student = $student;
        $this->absenceDays = $absenceDays;
        $this->customMessage = $customMessage;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'circle_name' => $this->student->circle?->name,
            'absence_days' => $this->absenceDays,
            'custom_message' => $this->customMessage,
            'message_ar' => $this->customMessage ?? 'تم رصد غياب متتالٍ لابنكم ' . $this->student->name . ' لمدة ' . $this->absenceDays . ' أيام. يرجى التواصل مع المشرف.',
            'message_en' => 'Sequential absences detected for ' . $this->student->name . ' (' . $this->absenceDays . ' days). Please contact the supervisor.',
        ];
    }
}
