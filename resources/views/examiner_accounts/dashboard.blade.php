<x-layouts.markaz-layout>
    <x-slot name="title">لوحة تحكم المختبر</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <span>لوحة تحكم المختبر</span>
    </nav>

    <div dir="rtl" class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden shadow-xl mb-8">
        <div class="relative z-10">
            <h1 class="text-2xl md:text-3xl font-black mb-2">أهلاً، {{ $examiner->user?->name }}</h1>
            <p class="text-emerald-100/80 text-sm">لوحة تحكم المختبر</p>
        </div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-3xl font-black text-[#0a5c36]">{{ $competitionsCount }}</p>
            <p class="text-xs text-gray-500 mt-1">المسابقات</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-3xl font-black text-[#0a5c36]">{{ $levelsCount }}</p>
            <p class="text-xs text-gray-500 mt-1">المستويات</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-3xl font-black text-emerald-600">{{ $testedParticipants }}</p>
            <p class="text-xs text-gray-500 mt-1">تم اختبارهم</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-3xl font-black text-orange-500">{{ $remainingParticipants }}</p>
            <p class="text-xs text-gray-500 mt-1">المتبقون</p>
        </div>
    </div>

    <h2 class="text-lg font-bold text-gray-800 mb-4">مسابقاتي</h2>

    @if($competitions->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center text-gray-400">
            لا توجد مسابقات مسندة إليك حالياً.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($competitions as $competition)
                @php
                    $ce = $competition->competitionExaminers->first();
                    $levelsNames = $ce?->competitionExaminerLevels->map(fn($cel) => $cel->competitionLevel?->level?->name)->filter()->implode('، ');
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg mb-2">{{ $competition->name }}</h3>
                        <p class="text-xs text-gray-500 mb-4">المستويات: {{ $levelsNames ?: '—' }}</p>
                    </div>
                    <a href="{{ route('examiner.competitions.levels', $competition) }}"
                        class="w-full text-center px-4 py-2.5 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-xl transition-all">
                        دخول
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.markaz-layout>
