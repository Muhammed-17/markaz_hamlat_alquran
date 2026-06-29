<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected $startDate;
    protected $endDate;
    protected $circleId;

    public function __construct($startDate, $endDate, $circleId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->circleId = $circleId;
    }

    public function query()
    {
        $query = Attendance::with(['student.circle', 'user'])
            ->whereBetween('date', [$this->startDate, $this->endDate]);

        if ($this->circleId) {
            $query->whereHas('student', fn($q) => $q->where('circle_id', $this->circleId));
        }

        return $query->orderBy('date', 'desc');
    }

    public function headings(): array
    {
        return [
            '#',
            'التاريخ',
            'اسم الطالب',
            'الحلقة',
            'الحالة',
            'ملاحظات',
            'المسجل',
            'تاريخ التسجيل',
        ];
    }

    public function map($attendance): array
    {
        static $row = 0;
        $row++;

        $statusLabels = [
            'present' => 'حاضر',
            'absent' => 'غائب',
            'late' => 'متأخر',
            'excused' => 'بعذر',
        ];

        return [
            $row,
            $attendance->date->format('Y-m-d'),
            $attendance->student->name ?? '-',
            $attendance->student->circle->name ?? '-',
            $statusLabels[$attendance->status] ?? $attendance->status,
            $attendance->notes ?? '-',
            $attendance->user->name ?? 'نظام',
            $attendance->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}