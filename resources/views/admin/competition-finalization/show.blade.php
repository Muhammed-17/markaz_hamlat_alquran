<x-layouts.markaz-layout>
    <x-slot name="title">إنهاء المسابقة</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <span>{{ $competition->name }}</span>
        <span>/</span>
        <span>{{ $competitionLevel->level?->name }}</span>
        <span>/</span>
        <span>إنهاء المسابقة</span>
    </nav>

    <div dir="rtl" class="bg-[#0b3d2c] rounded-3xl p-6 text-white shadow-xl mb-6">
        <h1 class="text-xl font-black mb-1">{{ $competition->name }}</h1>
        <p class="text-emerald-100/80 text-sm">{{ $competitionLevel->level?->name }}</p>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-2xl font-black text-gray-800">{{ $participants->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">عدد المشاركين</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-2xl font-black text-emerald-600">{{ $completedCount }}</p>
            <p class="text-xs text-gray-500 mt-1">مكتمل</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-2xl font-black text-red-500">{{ $incompleteCount }}</p>
            <p class="text-xs text-gray-500 mt-1">غير مكتمل</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="px-6 py-3 text-right font-bold">المشارك</th>
                        <th class="px-6 py-3 text-right font-bold">المجموع</th>
                        <th class="px-6 py-3 text-right font-bold">الترتيب</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($results as $result)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $result->competitionParticipant->participant_name }}</td>
                            <td class="px-6 py-4 font-bold text-[#0a5c36]">{{ $result->total_score }}</td>
                            <td class="px-6 py-4">
                                @if($result->rank)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">#{{ $result->rank }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400">لا توجد نتائج بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-3">
        <form method="POST" action="{{ route('admin.competitions.finalization.update', [$competition->id, $competitionLevel->id]) }}" class="flex-1">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="recalculate_rank">
            <button type="submit" class="w-full px-5 py-3 bg-white border border-[#0a5c36] text-[#0a5c36] hover:bg-emerald-50 font-bold rounded-xl transition-all">
                إعادة حساب الترتيب
            </button>
        </form>

        <form method="POST" action="{{ route('admin.competitions.finalization.update', [$competition->id, $competitionLevel->id]) }}" class="flex-1"
            onsubmit="return confirm('هل أنت متأكد من اعتماد النتائج؟');">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="finalize">
            <button type="submit" class="w-full px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all">
                اعتماد النتائج
            </button>
        </form>

        <form method="POST" action="{{ route('admin.competitions.finalization.update', [$competition->id, $competitionLevel->id]) }}" class="flex-1"
            onsubmit="return confirm('هل أنت متأكد من إغلاق المسابقة؟ لا يمكن التراجع.');">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="close">
            <button type="submit" class="w-full px-5 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all">
                إغلاق المسابقة
            </button>
        </form>
    </div>
</x-layouts.markaz-layout>
