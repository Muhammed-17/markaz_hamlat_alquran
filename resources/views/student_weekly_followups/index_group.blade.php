<x-layouts.markaz-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-[#0a5c36] text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                    المتابعات الأسبوعية — جماعي
                </h2>
                <p class="text-gray-500 mt-1 text-sm">إدارة خطط الحفظ والمراجعة الأسبوعية للحلقات</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('student-weekly-followups.create-group') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#0a5c36] text-white font-medium hover:bg-[#0d7a48] transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    متابعة جماعية جديدة
                </a>
                <a href="{{ route('student-weekly-followups.index-individual') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-sky-600 text-sky-600 font-medium hover:bg-sky-50 transition-colors">
                    عرض المتابعات الفردية
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">إجمالي المتابعات</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <span class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">المتابعات الجماعية</p>
                        <p class="text-2xl font-bold text-[#0a5c36] mt-1">{{ $stats['group_batches'] }}</p>
                    </div>
                    <span class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">هذا الأسبوع</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['this_week'] }}</p>
                    </div>
                    <span class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <form method="GET" action="{{ route('student-weekly-followups.index-group') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <x-searchable-select
                    name="center_id"
                    :options="$filters['centers']->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->values()"
                    placeholder="كل المراكز"
                    searchPlaceholder="ابحث عن مركز..."
                    :defaultValue="request('center_id', '')" />

                <x-searchable-select
                    name="circle_id"
                    :options="$filters['circles']->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->values()"
                    placeholder="كل الحلقات"
                    searchPlaceholder="ابحث عن حلقة..."
                    :defaultValue="request('circle_id', '')" />

                <x-searchable-select
                    name="teacher_id"
                    :options="$filters['teachers']->map(fn($t) => ['value' => $t->id, 'label' => $t->user->name ?? $t->name])->values()"
                    placeholder="كل المعلمين"
                    searchPlaceholder="ابحث عن معلم..."
                    :defaultValue="request('teacher_id', '')" />
                <input type="date" name="week_start"
                    class="rounded-lg border-gray-300 focus:border-[#0a5c36] focus:ring-[#0a5c36] text-sm"
                    value="{{ request('week_start') }}" placeholder="من تاريخ">
                <input type="date" name="week_end"
                    class="rounded-lg border-gray-300 focus:border-[#0a5c36] focus:ring-[#0a5c36] text-sm"
                    value="{{ request('week_end') }}" placeholder="إلى تاريخ">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#0a5c36] text-white px-4 py-2 text-sm font-medium hover:bg-[#0d7a48] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    تصفية
                </button>
            </form>
        </div>

        @include('student_weekly_followups.index_table', [
            'planType' => 'group',
        ])
    </div>
</x-layouts.markaz-layout>