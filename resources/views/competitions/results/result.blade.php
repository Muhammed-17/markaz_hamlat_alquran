<x-layouts.markaz-layout>
    <x-slot name="title">نتيجة — {{ $participant->participant_name }}</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('admin.competitions.index') }}" class="hover:text-[#0a5c36]">المسابقات</a>
        <span>/</span>
        <a href="{{ route('admin.participants.index', $participant->competition_level_id) }}" class="hover:text-[#0a5c36]">المشاركون</a>
        <span>/</span>
        <span>{{ $participant->participant_name }}</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-black text-gray-800 mb-1">{{ $participant->participant_name }}</h1>
                <p class="text-xs text-gray-400">
                    {{ $participant->participant_type === 'student' ? 'طالب' : 'مشارك خارجي' }}
                </p>
            </div>
            <div class="flex items-center gap-6 text-center">
                <div>
                    <p class="text-xl font-black text-[#0a5c36]">{{ $answeredCount }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">أجاب</p>
                </div>
                <div>
                    <p class="text-xl font-black text-red-500">{{ $unansweredCount }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">لم يجب</p>
                </div>
                <div>
                    <p class="text-xl font-black text-gray-700">{{ $totalQuestions }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">إجمالي الأسئلة</p>
                </div>
                @if($participant->competitionResult)
                    <div>
                        <p class="text-xl font-black text-emerald-600">{{ $participant->competitionResult->total_score }}</p>
                        <p class="text-[11px] text-gray-500 mt-1">المجموع الكلي</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="px-6 py-3 text-right font-bold">السؤال</th>
                        <th class="px-6 py-3 text-right font-bold">المختبر</th>
                        <th class="px-6 py-3 text-right font-bold">أجاب؟</th>
                        <th class="px-6 py-3 text-right font-bold">أخطاء الحفظ</th>
                        <th class="px-6 py-3 text-right font-bold">أخطاء التشكيل</th>
                        <th class="px-6 py-3 text-right font-bold">الدرجة</th>
                        <th class="px-6 py-3 text-right font-bold">ملاحظات</th>
                        <th class="px-6 py-3 text-right font-bold">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($answers as $answer)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $answer->competitionQuestion?->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $answer->competitionExaminer?->examiner?->name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @if($answer->answered)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">نعم</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">لا</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $answer->memorization_mistakes ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $answer->tashkeel_mistakes ?? '—' }}</td>
                            <td class="px-6 py-4 font-bold text-[#0a5c36]">{{ $answer->score }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $answer->notes ?: '—' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.exam.show', $participant) }}?question={{ $answer->competition_question_id }}"
                                    class="px-4 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold rounded-lg text-xs transition-all">
                                    تعديل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-400">لا توجد إجابات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.markaz-layout>