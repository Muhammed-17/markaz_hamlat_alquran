<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AttendanceMonthlyReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $month;
    protected $circleId;

    public function __construct($month, $circleId = null)
    {
        $this->month = $month;
        $this->circleId = $circleId;
    }

    public function collection()
    {
        $startOfMonth = Carbon::parse($this->month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $studentQuery = Student::with([
            'attendances' => fn($q) => $q
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
        ])->where('status', 'مقيد');

        if ($this->circleId) {
            $studentQuery->where('circle_id', $this->circleId);
        }

        $students = $studentQuery->get();

        return $students->map(function ($student, $index) {
            $total = $student->attendances->count();
            $present = $student->attendances->where('status', 'present')->count();
            $absent = $student->attendances->where('status', 'absent')->count();
            $late = $student->attendances->where('status', 'late')->count();
            $excused = $student->attendances->where('status', 'excused')->count();

            return [
                'id' => $index + 1,
                'name' => $student->name,
                'circle' => $student->circle->name ?? '-',
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'excused' => $excused,
                'rate' => $total > 0 ? round($present / $total * 100, 1) . '%' : '0%',
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'اسم الطالب',
            'الحلقة',
            'إجمالي الأيام',
            'حاضر',
            'غائب',
            'متأخر',
            'بعذر',
            'نسبة الحضور',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '059669']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
