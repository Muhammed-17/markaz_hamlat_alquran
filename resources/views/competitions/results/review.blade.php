<x-layouts.markaz-layout>
    <x-slot name="title">مراجعة الاختبار — {{ $participant->participant_name }}</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('admin.competitions.index') }}" class="hover:text-[#0a5c36]">المسابقات</a>
        <span>/</span>
        <a href="{{ route('admin.participants.index', $participant->competition_level_id) }}" class="hover:text-[#0a5c36]">المشاركون</a>
        <span>/</span>
        <span>{{ $participant->participant_name }}</span>
    </nav>

    <div dir="rtl" class="bg-[#0b3d2c] rounded-3xl p-6 text-white relative overflow-hidden shadow-xl mb-6">
        <div class="relative z-10">
            <h1 class="text-xl font-black mb-3">{{ $participant->participant_name }}</h1>
            <div class="flex items-center gap-6 text-xs text-emerald-100/80">
                <span>{{ $totalQuestions }} إجمالي الأسئلة</span>
                <span>{{ $answeredCount }} أجاب</span>
                <span>{{ $unansweredCount }} لم يجب</span>
            </div>
        </div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="px-6 py-3 text-right font-bold">السؤال</th>
                        <th class="px-6 py-3 text-right font-bold">أجاب؟</th>
                        <th class="px-6 py-3 text-right font-bold">أخطاء الحفظ</th>
                        <th class="px-6 py-3 text-right font-bold">أخطاء التشكيل</th>
                        <th class="px-6 py-3 text-right font-bold">الدرجة</th>
                        <th class="px-6 py-3 text-right font-bold">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($answers as $answer)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $answer->competitionQuestion?->name }}</td>
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
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.exam.show', $participant) }}?question={{ $answer->competition_question_id }}"
                                class="px-4 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold rounded-lg text-xs transition-all">
                                تعديل
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">لا توجد إجابات بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.exam.show', $participant) }}?question={{ $answers->first()?->competition_question_id }}"
            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition-all">
            الرجوع للأسئلة
        </a>

        <form method="POST" action="{{ route('admin.exam.finalize', $participant) }}">
            @csrf
            <button type="submit"
                class="px-6 py-2.5 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-xl text-sm transition-all">
                اعتماد النتيجة
            </button>
        </form>
    </div>
</x-layouts.markaz-layout>