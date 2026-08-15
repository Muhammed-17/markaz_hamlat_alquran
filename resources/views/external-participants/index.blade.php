@php
$sortLink = fn($field) => request()->fullUrlWithQuery([
'sort' => $field,
'dir' => request('sort') === $field && request('dir', 'asc') === 'asc' ? 'desc' : 'asc',
]);
$sortIcon = fn($field) => request('sort') === $field
? (request('dir', 'asc') === 'asc' ? '↑' : '↓')
: '';
@endphp

<x-layouts.markaz-layout>
    <div dir="rtl"
        class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
        <div class="text-right w-full md:w-auto z-10">
            <h1 class="text-3xl font-black mb-2">إدارة المشاركين الخارجيين</h1>
            <p class="text-emerald-100/80 text-sm font-medium">
                @if(request()->anyFilled(['q']))
                {{ $externalParticipants->total() }} نتيجة
                @else
                {{ $externalParticipants->total() }} مشارك خارجي مسجل في النظام
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
            @can('create external participants')
            <a href="{{ route('external-participants.create') }}"
                class="w-full md:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                جديد
            </a>
            @endcan
        </div>

        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('external-participants.index') }}" class="flex flex-col lg:flex-row gap-4 items-end" dir="rtl">
            <div class="w-full lg:flex-1">
                <label for="filter_q" class="block text-xs font-bold text-gray-400 mb-1.5">البحث بالاسم أو الهاتف أو الرقم القومي</label>
                <input id="filter_q" type="search" name="q" value="{{ request('q') }}"
                    placeholder="ابحث عن مشارك..."
                    class="w-full p-2.5 px-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all bg-white text-right">
            </div>

            <button type="submit"
                class="w-full lg:w-auto px-5 py-2.5 bg-[#0a5c36] hover:bg-[#08492a] text-white font-bold rounded-xl text-sm transition-all text-center">
                بحث
            </button>

            @if(request()->anyFilled(['q']))
            <a href="{{ route('external-participants.index') }}"
                class="w-full lg:w-auto px-5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-500 hover:text-gray-700 font-bold border border-gray-200 rounded-xl text-sm transition-all text-center">
                مسح الفلاتر
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 select-none">
                            <a href="{{ $sortLink('name') }}" class="flex items-center gap-1 hover:text-gray-800">
                                <span>الاسم</span>
                                <span class="text-xs text-gray-400">{{ $sortIcon('name') }}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">رقم الهاتف</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الرقم القومي</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 text-center">عدد المسابقات</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 text-left">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($externalParticipants as $participant)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-800">
                            {{ $participant->name }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $participant->phone ?: '—' }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $participant->national_id ?: '—' }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-center">
                            {{ $participant->competition_participants_count }}
                        </td>
                        <td class="px-6 py-4 text-left">
                            <div class="flex items-center justify-end gap-3">
                                @can('view external participants')
                                <a href="{{ route('external-participants.show', $participant) }}"
                                    class="text-emerald-600 hover:text-emerald-800 transition" title="عرض المشارك">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @endcan

                                @can('edit external participants')
                                <a href="{{ route('external-participants.edit', $participant) }}" class="text-blue-500 hover:text-blue-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan

                                @can('delete external participants')
                                <form method="POST" action="{{ route('external-participants.destroy', $participant) }}"
                                    onsubmit="confirmDelete(event, { name: '{{ e($participant->name) }}', type: 'المشارك' })"
                                    class="text-red-400 hover:text-red-600 transition">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            @if(request()->anyFilled(['q']))
                            لا يوجد مشاركون يطابقون البحث.
                            @else
                            لا يوجد مشاركون خارجيون مسجلون حالياً.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$externalParticipants" />
    </div>
</x-layouts.markaz-layout>
