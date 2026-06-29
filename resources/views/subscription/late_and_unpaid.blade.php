<x-layouts.markaz-layout>
    <div class="space-y-6">

        {{-- ─── Header ────────────────────────────────────────────── --}}
        <div class="bg-[#0b3d2c] rounded-3xl p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="text-right w-full md:w-auto z-10 wrap-break-word">
                <h1 class="text-3xl font-black mb-2">الطلاب المتعثرين</h1>
                <p class="text-emerald-100/80 text-sm font-medium">قائمة بالطلاب المتأخرين عن سداد الاشتراكات</p>
            </div>
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <a href="{{ route('subscriptions.index') }}"
                    class="w-full md:w-auto px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/10 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    عودة لإدارة الاشتراكات
                </a>
            </div>
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        {{-- ─── Filters ───────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6"
            x-data="lateUnpaidFilters()"
            x-init="init()">

            <form method="GET" action="{{ route('subscriptions.late_and_unpaid') }}" id="filterForm">

                {{-- الصف الأول: البحث + الأزرار --}}
                <div class="flex items-end gap-3 pb-4 mb-4 border-b border-gray-100">
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">بحث باسم الطالب</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search', $search) }}"
                                placeholder="اكتب اسم الطالب للبحث بشكل سريع..."
                                class="w-full rounded-xl border-gray-200 pr-10 focus:border-[#0b3d2c] focus:ring-[#0b3d2c] text-sm h-11"
                                oninput="debounceSearch(this)">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    @if($hasActiveFilters)
                    <a href="{{ route('subscriptions.late_and_unpaid') }}"
                        class="shrink-0 bg-gray-100 text-gray-600 px-5 h-11 rounded-xl font-bold hover:bg-gray-200 transition flex items-center gap-2 text-sm whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 4H15" />
                        </svg>
                        إعادة تعيين
                    </a>
                    @endif

                    <button type="submit"
                        class="shrink-0 bg-[#0b3d2c] text-white px-6 h-11 rounded-xl font-bold hover:bg-[#0a3324] shadow-sm transition flex items-center gap-2 text-sm whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                        تطبيق التصفية
                    </button>
                </div>

                {{-- الصف الثاني: الحالة + الفرع + الحلقة + المعلم --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 {{ $centers->count() > 0 ? 'lg:grid-cols-4' : '' }} gap-4">

                    <div class="w-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة</label>
                        <select name="status"
                            class="w-full rounded-xl border-gray-200 focus:border-[#0b3d2c] focus:ring-[#0b3d2c] text-sm h-11">
                            <option value="">جميع الحالات</option>
                            <option value="مقيد" {{ request('status', $selectedStatus ?? '') === 'مقيد'  ? 'selected' : '' }}>🟢 مقيد</option>
                            <option value="متوقف" {{ request('status', $selectedStatus ?? '') === 'متوقف' ? 'selected' : '' }}>🟠 متوقف</option>
                        </select>
                    </div>

                    @if($centers->count() > 0)
                    <div class="w-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الفرع</label>
                        <select name="center_id"
                            x-model="selectedCenter"
                            @change="onCenterChange()"
                            class="w-full rounded-xl border-gray-200 focus:border-[#0b3d2c] focus:ring-[#0b3d2c] text-sm h-11">
                            <option value="">جميع الفروع</option>
                            <template x-for="center in centers" :key="center.id">
                                <option :value="center.id" :selected="center.id == selectedCenter" x-text="center.name"></option>
                            </template>
                        </select>
                    </div>
                    @endif

                    <div class="w-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الحلقة</label>
                        <x-searchable-select
                            name="circle_id"
                            placeholder="جميع الحلقات"
                            search-placeholder="ابحث عن حلقة..."
                            default-option="جميع الحلقات"
                            default-value="{{ request('circle_id', $selectedCircleId ?? '') }}"
                            :options="$circles->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->values()->toJson()" />
                    </div>

                    <div class="w-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">المعلم</label>
                        <x-searchable-select
                            name="teacher_id"
                            placeholder="جميع المعلمين"
                            search-placeholder="ابحث عن معلم..."
                            default-option="جميع المعلمين"
                            default-value="{{ request('teacher_id', $selectedTeacherId ?? '') }}"
                            :options="$teachers->map(fn($t) => ['value' => $t->id, 'label' => $t->name])->values()->toJson()" />
                    </div>

                </div>
            </form>
        </div>

        {{-- ─── Statistics Cards ──────────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-red-100 transition-all">
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-bold">عدد الطلاب المتعثرين</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $totalStudents }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-amber-100 transition-all">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-bold">إجمالي الأشهر المتأخرة</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $totalUnpaidMonths }}</h3>
                </div>
            </div>

        </div>

        {{-- ─── Results Table ─────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">قائمة الطلاب المتعثرين</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">#</th>
                            @php
                            $currentSort = request('sort', 'unpaid_months');
                            $currentDir = request('direction', 'desc');

                            $sortLink = fn(string $col) =>
                            request()->fullUrlWithQuery([
                            'sort' => $col,
                            'direction' => ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc',
                            ]);

                            $sortIcon = function(string $col) use ($currentSort, $currentDir): string {
                            if ($currentSort !== $col) {
                            return '<svg class="w-3.5 h-3.5 opacity-30 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                            </svg>';
                            }
                            return $currentDir === 'asc'
                            ? '<svg class="w-3.5 h-3.5 text-[#0b3d2c] inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>'
                            : '<svg class="w-3.5 h-3.5 text-[#0b3d2c] inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>';
                            };

                            $thBase = 'px-6 py-4 text-sm font-semibold cursor-pointer select-none hover:bg-gray-100 transition-colors';
                            $thActive = 'text-[#0b3d2c]';
                            $thMuted = 'text-gray-600';
                            @endphp

                            {{-- اسم الطالب --}}
                            <th class="{{ $thBase }} {{ $currentSort === 'name' ? $thActive : $thMuted }}"
                                onclick="window.location='{{ $sortLink('name') }}'">
                                <div class="flex items-center justify-end gap-1">
                                    اسم الطالب
                                    {!! $sortIcon('name') !!}
                                </div>
                            </th>

                            {{-- الحالة --}}
                            <th class="{{ $thBase }} {{ $currentSort === 'status' ? $thActive : $thMuted }}"
                                onclick="window.location='{{ $sortLink('status') }}'">
                                <div class="flex items-center justify-end gap-1">
                                    الحالة
                                    {!! $sortIcon('status') !!}
                                </div>
                            </th>

                            {{-- الحلقة --}}
                            <th class="{{ $thBase }} {{ $currentSort === 'circle' ? $thActive : $thMuted }}"
                                onclick="window.location='{{ $sortLink('circle') }}'">
                                <div class="flex items-center justify-end gap-1">
                                    الحلقة
                                    {!! $sortIcon('circle') !!}
                                </div>
                            </th>

                            {{-- المركز --}}
                            <th class="{{ $thBase }} {{ $currentSort === 'center' ? $thActive : $thMuted }}"
                                onclick="window.location='{{ $sortLink('center') }}'">
                                <div class="flex items-center justify-end gap-1">
                                    المركز
                                    {!! $sortIcon('center') !!}
                                </div>
                            </th>

                            {{-- عدد الأشهر المتأخرة --}}
                            <th class="{{ $thBase }} {{ $currentSort === 'unpaid_months' ? $thActive : $thMuted }}"
                                onclick="window.location='{{ $sortLink('unpaid_months') }}'">
                                <div class="flex items-center justify-end gap-1">
                                    عدد الأشهر المتأخرة
                                    {!! $sortIcon('unpaid_months') !!}
                                </div>
                            </th>

                            {{-- إجراءات - بدون ترتيب --}}
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-400 text-sm font-medium">
                                {{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $student->name }}</td>
                            <td class="px-6 py-4">
                                @if($student->status === 'مقيد')
                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 w-fit">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                    مقيد
                                </span>
                                @elseif($student->status === 'متوقف')
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 w-fit">
                                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                    متوقف
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->circle?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->circle?->center?->name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-bold">
                                    {{ $student->unpaid_months_count }} أشهر
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('subscriptions.details_unpaid', $student->id) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm  px-3 py-1 rounded-lg hover:bg-blue-50 transition">
                                    عرض التفاصيل
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">جميع الطلاب ملتزمون بالسداد!</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <x-pagination :paginator="$students" />

    </div>

    <script>
        let searchTimeout;

        function debounceSearch(input) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => input.form.submit(), 500);
        }

        function lateUnpaidFilters() {
            return {
                selectedCenter: '{{ request("center_id", $selectedCenterId ?? "") }}',
                selectedCircle: '{{ request("circle_id", $selectedCircleId ?? "all") }}',
                selectedTeacher: '{{ request("teacher_id", $selectedTeacherId ?? "") }}',

                centers: @json($centers),
                circles: @json($circles),
                teachers: @json($teachers),

                filterUrl: '{{ route("subscriptions.filter-options") }}',

                init() {
                    window.addEventListener('searchable-change', (e) => {
                        if (e.detail.name === 'circle_id') {
                            this.selectedCircle = e.detail.value || 'all';
                            this.onCircleChange();
                        }
                        if (e.detail.name === 'teacher_id') {
                            this.selectedTeacher = e.detail.value;
                            this.onTeacherChange();
                        }
                    });

                    if (this.selectedCenter || (this.selectedCircle && this.selectedCircle !== 'all') || this.selectedTeacher) {
                        this.fetchOptions();
                    }
                },

                async fetchOptions() {
                    const params = new URLSearchParams();
                    if (this.selectedCenter) params.append('center_id', this.selectedCenter);
                    if (this.selectedCircle && this.selectedCircle !== 'all') params.append('circle_id', this.selectedCircle);
                    if (this.selectedTeacher) params.append('teacher_id', this.selectedTeacher);

                    try {
                        const res = await fetch(`${this.filterUrl}?${params}`);
                        const data = await res.json();

                        this.centers = data.centers;
                        this.circles = data.circles;
                        this.teachers = data.teachers;

                        // ✅ حدّث الكومبوننتات (الحلقة والمعلم)
                        window.dispatchEvent(new CustomEvent('update-options', {
                            detail: {
                                name: 'circle_id',
                                options: data.circles.map(c => ({
                                    value: c.id,
                                    label: c.name
                                }))
                            }
                        }));
                        window.dispatchEvent(new CustomEvent('update-options', {
                            detail: {
                                name: 'teacher_id',
                                options: data.teachers.map(t => ({
                                    value: t.id,
                                    label: t.name
                                }))
                            }
                        }));

                        if (this.selectedCenter && !data.centers.find(c => c.id == this.selectedCenter)) this.selectedCenter = '';
                        if (this.selectedCircle !== 'all' && !data.circles.find(c => c.id == this.selectedCircle)) this.selectedCircle = 'all';
                        if (this.selectedTeacher && !data.teachers.find(t => t.id == this.selectedTeacher)) this.selectedTeacher = '';
                    } catch (e) {
                        console.error('Filter fetch error:', e);
                    }
                },

                onCenterChange() {
                    this.selectedCircle = 'all';
                    this.selectedTeacher = '';
                    this.fetchOptions();
                },
                onCircleChange() {
                    this.selectedTeacher = '';
                    this.fetchOptions();
                },
                onTeacherChange() {
                    this.selectedCircle = 'all';
                    this.fetchOptions();
                },
            }
        }
    </script>
</x-layouts.markaz-layout>