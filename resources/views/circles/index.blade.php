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
    <!-- Header Card -->
    <div dir="rtl"
        class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
        <!-- العنوان أولًا في HTML -->
        <div class="text-right w-full md:w-auto z-10">
            <h1 class="text-3xl font-black mb-2">إدارة الحلقات</h1>
            <p class="text-emerald-100/80 text-sm font-medium">
                @if(request()->anyFilled(['q', 'center_id', 'type', 'level']))
                    {{ $circles->total() }} نتيجة
                @else
                    {{ $circles->total() }} حلقة مسجلة في النظام
                @endif
            </p>
        </div>

        <!-- الزر ثانيًا في HTML -->
        <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
            @can('create circles')
            <a href="{{ route('circles.create') }}"
                class="w-full md:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                جديد
            </a>
            @endcan
        </div>

        <!-- Decorative Element -->
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    {{-- ─── فلاتر التصفية ─── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('circles.index') }}" class="flex flex-col lg:flex-row gap-4 items-end" dir="rtl">

            {{-- البحث --}}
            <div class="w-full lg:flex-1">
                <label class="block text-xs font-bold text-gray-400 mb-1.5">البحث بالاسم</label>
                <input type="search" name="q" value="{{ request('q') }}"
                    placeholder="ابحث باسم الحلقة..."
                    class="w-full p-2.5 px-4 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all bg-white text-right">
            </div>

            {{-- فلتر الفرع --}}
            <div class="w-full lg:w-48">
                <label class="block text-xs font-bold text-gray-400 mb-1.5">الفرع</label>
                <select name="center_id"
                    class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all bg-white appearance-none">
                    <option value="">كل الفروع</option>
                    @foreach($centers as $center)
                    <option value="{{ $center->id }}" @selected((string) request('center_id') === (string) $center->id)>
                        {{ $center->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- فلتر النوع --}}
            <div class="w-full lg:w-48">
                <label class="block text-xs font-bold text-gray-400 mb-1.5">النوع</label>
                <select name="type"
                    class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all bg-white appearance-none">
                    <option value="">كل الأنواع</option>
                    <option value="group" @selected(request('type') === 'group')>جماعية</option>
                    <option value="individual" @selected(request('type') === 'individual')>فردية</option>
                </select>
            </div>

            {{-- فلتر المستوى --}}
            <div class="w-full lg:w-48">
                <label class="block text-xs font-bold text-gray-400 mb-1.5">المستوى</label>
                <select name="level"
                    class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all bg-white appearance-none">
                    <option value="">كل المستويات</option>
                    <option value="build" @selected(request('level') === 'build')>بناء</option>
                    <option value="mastery" @selected(request('level') === 'mastery')>إتقان</option>
                    <option value="creativity" @selected(request('level') === 'creativity')>إبداع</option>
                </select>
            </div>

            {{-- زر البحث --}}
            <button type="submit"
                class="w-full lg:w-auto px-5 py-2.5 bg-[#0a5c36] hover:bg-[#08492a] text-white font-bold rounded-xl text-sm transition-all text-center">
                بحث
            </button>

            {{-- زر مسح الفلاتر --}}
            @if(request()->anyFilled(['q', 'center_id', 'type', 'level']))
            <a href="{{ route('circles.index') }}"
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
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 select-none">
                            <a href="{{ $sortLink('type') }}" class="flex items-center gap-1 hover:text-gray-800">
                                <span>النوع</span>
                                <span class="text-xs text-gray-400">{{ $sortIcon('type') }}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 select-none">
                            <a href="{{ $sortLink('level') }}" class="flex items-center gap-1 hover:text-gray-800">
                                <span>المستوى</span>
                                <span class="text-xs text-gray-400">{{ $sortIcon('level') }}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">الفرع</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 select-none">
                            <a href="{{ $sortLink('students_count') }}" class="flex items-center gap-1 hover:text-gray-800 justify-center">
                                <span>الفعلي عدد</span>
                                <span class="text-xs text-gray-400">{{ $sortIcon('students_count') }}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المعلم</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المعلم المساعد</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600">المشرف</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-600 text-left">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($circles as $circle)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-800">
                            {{ $circle->name }}
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            @if ($circle->type == 'group')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600">
                                جماعية
                            </span>
                            @elseif ($circle->type == 'individual')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-600">
                                فردية
                            </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            @php
                            $levelStyles = match ($circle->level) {
                            'build' => 'bg-blue-50 text-blue-600',
                            'mastery' => 'bg-purple-50 text-purple-600',
                            'creativity' => 'bg-amber-50 text-amber-600',
                            default => 'bg-gray-50 text-gray-600',
                            };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $levelStyles }}">
                                @if ($circle->level == 'build')
                                بناء
                                @elseif ($circle->level == 'mastery')
                                إتقان
                                @elseif ($circle->level == 'creativity')
                                إبداع
                                @endif
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $circle->center?->name ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-gray-600 text-center">
                            {{ $circle->students_count ?? $circle->students?->count() ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $circle->mainTeacher->first()?->name ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $circle->assistantTeacher->first()?->name ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $circle->supervisor?->name ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-left">
                            <div class="flex items-center justify-end gap-3">
                                <!-- Edit -->
                                @can('edit circles')
                                <a href="{{ route('circles.edit', $circle) }}"
                                    class="text-blue-500 hover:text-blue-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan

                                <!-- Delete -->
                                @can('delete circles')
                                <form method="POST" action="{{ route('circles.destroy', $circle->id) }}"
                                    class="text-red-400 hover:text-red-600 transition">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
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
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            @if(request()->anyFilled(['q', 'center_id', 'type', 'level']))
                                لا توجد حلقات تطابق الفلاتر المحددة.
                            @else
                                لا توجد حلقات مسجلة حالياً.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ─── الترقيم الموحّد ─── --}}
        <x-pagination :paginator="$circles" />
    </div>
</x-layouts.markaz-layout>