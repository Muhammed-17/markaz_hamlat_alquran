<x-layouts.markaz-layout>
    <x-slot name="title">نتائج المسابقات</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <span>نتائج المسابقات</span>
    </nav>

    <h1 class="text-2xl font-black text-gray-800 mb-6">نتائج المسابقات</h1>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <select name="competition_id" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none">
                <option value="">-- كل المسابقات --</option>
                @foreach($competitions as $c)
                    <option value="{{ $c->id }}" {{ request('competition_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="competition_level_id" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none">
                <option value="">-- كل المستويات --</option>
                @foreach($levels as $l)
                    <option value="{{ $l->id }}" {{ request('competition_level_id') == $l->id ? 'selected' : '' }}>{{ $l->level?->name }}</option>
                @endforeach
            </select>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث باسم المشارك..."
                class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none">
            <button type="submit" class="px-4 py-2.5 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-xl text-sm">
                تصفية
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="px-6 py-3 text-right font-bold">المشارك</th>
                        <th class="px-6 py-3 text-right font-bold">المستوى</th>
                        <th class="px-6 py-3 text-right font-bold">المجموع</th>
                        <th class="px-6 py-3 text-right font-bold">الترتيب</th>
                        <th class="px-6 py-3 text-right font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($results as $result)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $result->competitionParticipant->participant_name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $result->competitionParticipant->competitionLevel?->level?->name }}</td>
                            <td class="px-6 py-4 font-bold text-[#0a5c36]">{{ $result->total_score }}</td>
                            <td class="px-6 py-4">
                                @if($result->rank)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">#{{ $result->rank }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 flex items-center gap-3">
                                <a href="{{ route('admin.competition-results.show', $result->competition_participant_id) }}"
                                    class="text-xs font-bold text-gray-600 hover:underline">عرض</a>
                                <a href="{{ route('admin.competition-results.edit', $result->competition_participant_id) }}"
                                    class="text-xs font-bold text-[#0a5c36] hover:underline">تعديل</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">لا توجد نتائج.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$results" />
    </div>
</x-layouts.markaz-layout>
