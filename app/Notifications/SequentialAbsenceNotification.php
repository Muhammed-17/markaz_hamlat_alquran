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
    public bool $isFullMonth;

    public function __construct(Student $student, int $absenceDays, ?string $customMessage = null, bool $isFullMonth = false)
    {
        $this->student = $student;
        $this->absenceDays = $absenceDays;
        $this->customMessage = $customMessage;
        $this->isFullMonth = $isFullMonth;
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
            'is_full_month' => $this->isFullMonth,
            'message_ar' => $this->customMessage ?? 'تم رصد ' . $this->absenceDays . ' أيام غياب لابنكم ' . $this->student->name . ' خلال الشهر الحالي. يرجى التواصل مع إدارة.',
            'message_en' => $this->student->name . ' has accumulated ' . $this->absenceDays . ' absence days this month. Please contact the supervisor.',
        ];
    }
}
