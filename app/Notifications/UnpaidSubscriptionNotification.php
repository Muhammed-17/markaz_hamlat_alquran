<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UnpaidSubscriptionNotification extends Notification
{
    use Queueable;

    public Student $student;
    public int $unpaidMonthsCount;
    public ?string $customMessage;
    public array $unpaidMonths;

    public function __construct(
        Student $student,
        int $unpaidMonthsCount,
        ?string $customMessage = null,
        array $unpaidMonths = []
    ) {
        $this->student = $student;
        $this->unpaidMonthsCount = $unpaidMonthsCount;
        $this->customMessage = $customMessage;
        $this->unpaidMonths = $unpaidMonths;
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
            'unpaid_months_count' => $this->unpaidMonthsCount,
            'unpaid_months' => $this->unpaidMonths,
            'custom_message' => $this->customMessage,
            'message_ar' => $this->buildArabicMessage(),
            'message_en' => $this->student->name . ' has ' . $this->unpaidMonthsCount . ' unpaid subscription month(s). Please contact the administration.',
        ];
    }

    public function buildArabicMessage(): string
    {
        if ($this->customMessage) {
            return $this->customMessage;
        }

        if (!empty($this->unpaidMonths)) {
            $monthsList = implode('، ', $this->unpaidMonths);
            return 'نود تذكيركم بوجود ' . $this->unpaidMonthsCount . ' أشهر متأخرة السداد لابنكم ' . $this->student->name . ' (' . $monthsList . '). يرجى التواصل مع الإدارة لتسوية الموقف.';
        }

        return 'نود تذكيركم بوجود ' . $this->unpaidMonthsCount . ' أشهر متأخرة السداد لابنكم ' . $this->student->name . '. يرجى التواصل مع الإدارة لتسوية الموقف.';
    }
}