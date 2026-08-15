<?php

namespace App\Exports;

use App\Models\Competition;
use App\Models\CompetitionParticipant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class CompetitionParticipantsExport
 *
 * تصدير قائمة المشاركين في مسابقة معيّنة إلى Excel، مع تطبيق
 * نفس فلاتر صفحة المشاركين (البحث، المستوى، المركز).
 */
class CompetitionParticipantsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected Competition $competition,
        protected Request $request
    ) {}

    public function collection()
    {
        return CompetitionParticipant::query()
            ->where('competition_id', $this->competition->id)
            ->with([
                'competitionLevel.level',
                'student',
                'externalParticipant',
                'center',
                'circle',
            ])
            ->when($this->request->filled('level_id'), function ($query) {
                $query->where('competition_level_id', $this->request->level_id);
            })
            ->when($this->request->filled('center_id'), function ($query) {
                $query->where('center_id', $this->request->center_id);
            })
            ->when($this->request->filled('search'), function ($query) {
                $search = $this->request->search;

                $query->where(function ($q) use ($search) {
                    $q->whereHas('student', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('externalParticipant', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'الاسم',
            'النوع',
            'المستوى',
            'المركز',
        ];
    }

    public function map($participant): array
    {
        return [
            $participant->student->name ?? $participant->externalParticipant->name ?? '-',
            $participant->student_id ? 'طالب' : 'خارجي',
            $participant->competitionLevel->level->name ?? '-',
            $participant->center->name ?? '-',
        ];
    }
}
