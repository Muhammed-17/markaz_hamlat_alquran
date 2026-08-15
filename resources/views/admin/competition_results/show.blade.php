<x-layouts.markaz-layout>
    <x-slot name="title">تفاصيل النتيجة</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('admin.competition-results.index') }}" class="hover:text-[#0a5c36]">النتائج</a>
        <span>/</span>
        <span>تفاصيل النتيجة</span>
    </nav>

    <div dir="rtl" class="bg-[#0b3d2c] rounded-3xl p-6 text-white shadow-xl mb-6">
        <h1 class="text-xl font-black mb-1">{{ $result->competitionParticipant->participant_name }}</h1>
        <p class="text-emerald-100/80 text-sm">
            {{ $result->competitionParticipant->competition?->name }} — {{ $result->competitionParticipant->competitionLevel?->level?->name }}
        </p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-2xl font-black text-[#0a5c36]">{{ $result->total_score }}</p>
            <p class="text-xs text-gray-500 mt-1">الدرجة النهائية</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-2xl font-black text-amber-600">{{ $result->rank ?? '—' }}</p>
            <p class="text-xs text-gray-500 mt-1">الترتيب</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-2xl font-black text-gray-800">{{ $totalQuestions }}</p>
            <p class="text-xs text-gray-500 mt-1">عدد الأسئلة</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-2xl font-black text-emerald-600">{{ $answeredCount }}</p>
            <p class="text-xs text-gray-500 mt-1">مُجاب عنها</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="px-6 py-3 text-right font-bold">السؤال</th>
                        <th class="px-6 py-3 text-right font-bold">نوع السؤال</th>
                        <th class="px-6 py-3 text-right font-bold">الدرجة</th>
                        <th class="px-6 py-3 text-right font-bold">الملاحظات</th>
                        <th class="px-6 py-3 text-right font-bold">تعديل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($answers as $answer)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $answer->competitionQuestion?->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $answer->competitionQuestion?->type }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $answer->score }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs">{{ $answer->notes ?: '—' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.competition-answers.edit', $answer) }}"
                                    class="text-xs font-bold text-[#0a5c36] hover:underline">تعديل</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.markaz-layout>
