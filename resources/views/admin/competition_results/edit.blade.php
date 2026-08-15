<x-layouts.markaz-layout>
    <x-slot name="title">تعديل النتيجة</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('admin.competition-results.index') }}" class="hover:text-[#0a5c36]">النتائج</a>
        <span>/</span>
        <span>تعديل النتيجة</span>
    </nav>

    <div dir="rtl" class="bg-[#0b3d2c] rounded-3xl p-6 text-white shadow-xl mb-6">
        <h1 class="text-xl font-black mb-1">{{ $result->competitionParticipant->participant_name }}</h1>
        <p class="text-emerald-100/80 text-sm">{{ $result->competitionParticipant->competitionLevel?->level?->name }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xl font-black text-gray-800">{{ $totalQuestions }}</p>
                <p class="text-[11px] text-gray-500 mt-1">عدد الأسئلة</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xl font-black text-emerald-600">{{ $answeredCount }}</p>
                <p class="text-[11px] text-gray-500 mt-1">مُجاب</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xl font-black text-red-500">{{ $unansweredCount }}</p>
                <p class="text-[11px] text-gray-500 mt-1">غير مُجاب</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xl font-black text-[#0a5c36]">{{ $result->total_score }}</p>
                <p class="text-[11px] text-gray-500 mt-1">المجموع الحالي</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xl font-black text-amber-600">{{ $result->rank ?? '—' }}</p>
                <p class="text-[11px] text-gray-500 mt-1">الترتيب</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-3">
        <form method="POST" action="{{ route('admin.competition-results.update', $result->competition_participant_id) }}" class="flex-1">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="recalculate">
            <button type="submit" class="w-full px-5 py-3 bg-white border border-[#0a5c36] text-[#0a5c36] hover:bg-emerald-50 font-bold rounded-xl transition-all">
                إعادة حساب المجموع
            </button>
        </form>

        <form method="POST" action="{{ route('admin.competition-results.update', $result->competition_participant_id) }}" class="flex-1">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="save">
            <button type="submit" class="w-full px-5 py-3 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-xl transition-all">
                حفظ النتيجة
            </button>
        </form>

        <a href="{{ route('admin.competition-results.index') }}"
            class="flex-1 text-center px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all">
            إلغاء
        </a>
    </div>
</x-layouts.markaz-layout>
