<x-layouts.markaz-layout>
    <x-slot name="title">مستويات {{ $competition->name }}</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('admin.competitions.index') }}" class="hover:text-[#0a5c36]">المسابقات</a>
        <span>/</span>
        <span>{{ $competition->name }}</span>
    </nav>

    <h1 class="text-2xl font-black text-gray-800 mb-6">{{ $competition->name }} — المستويات</h1>

    @if($levels->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center text-gray-400">
        لا توجد مستويات لهذه المسابقة.
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($levels as $competitionLevel)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 text-lg mb-4">{{ $competitionLevel->level?->name }}</h3>

            <div class="grid grid-cols-2 gap-3 mb-5">
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-xl font-black text-[#0a5c36]">{{ $competitionLevel->participantsCount }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">المشاركون</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-xl font-black text-emerald-600">{{ $competitionLevel->completedCount }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">مكتمل</p>
                </div>
            </div>

            <a href="{{ route('admin.participants.index', $competitionLevel) }}"
                class="w-full block text-center px-4 py-2.5 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-xl transition-all">
                عرض المشاركين
            </a>
        </div>
        @endforeach
    </div>
    @endif
</x-layouts.markaz-layout>