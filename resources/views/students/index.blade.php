@php
$circlesList = $circles->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]);
$centersList = $centers->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values();

// ✅ تحديد الأدوار مرة واحدة في PHP لاستخدامها في الـ View
$isGuardian = auth()->user()->hasRole('guardian');
$canViewStudents = auth()->user()->can('view students');
@endphp

<x-layouts.markaz-layout>

    <script>
        function studentsIndex() {
            return {
                students: [],
                meta: {
                    total: 0,
                    last_page: 1,
                    current_page: 1,
                    per_page: 20
                },
                loading: true,

                circles: @json($circlesList),
                centers: @json($centersList),

                q: '',
                status: '',
                circleId: '',
                educationalStage: '',
                centerId: '',
                // ✅ clamp العمر عند التغيير
                _ageMin: '',
                _ageMax: '',
                get ageMin() { return this._ageMin; },
                set ageMin(v) {
                    const n = parseInt(v);
                    this._ageMin = (v === '' || isNaN(n)) ? '' : Math.max(1, Math.min(99, n));
                },
                get ageMax() { return this._ageMax; },
                set ageMax(v) {
                    const n = parseInt(v);
                    this._ageMax = (v === '' || isNaN(n)) ? '' : Math.max(1, Math.min(99, n));
                },
                schoolGrade: '',
                decision: '',
                currentPage: 1,
                sortField: 'name',
                sortAsc: true,

                _debounceTimer: null,

                init() {
                    this.$watch(
                        () => [
                            this.status, this.circleId, this.educationalStage,
                            this.centerId, this.schoolGrade, this.decision,
                            this.sortField, this.sortAsc,
                        ],
                        () => {
                            this.currentPage = 1;
                            this.fetch();
                        }
                    );

                    // البحث النصي مع debounce
                    this.$watch('q', () => {
                        clearTimeout(this._debounceTimer);
                        this._debounceTimer = setTimeout(() => {
                            this.currentPage = 1;
                            this.fetch();
                        }, 400);
                    });

                    this.$watch('_ageMin', () => { this.currentPage = 1; this.fetch(); });
                    this.$watch('_ageMax', () => { this.currentPage = 1; this.fetch(); });

                    this.fetch();
                },

                async fetch() {
                    this.loading = true;

                    const params = new URLSearchParams();
                    if (this.q)               params.set('q', this.q);
                    if (this.status)          params.set('status', this.status);
                    if (this.circleId)        params.set('circle_id', this.circleId);
                    if (this.centerId)        params.set('center_id', this.centerId);
                    if (this.educationalStage)params.set('educational_stage', this.educationalStage);
                    if (this.schoolGrade)     params.set('school_grade', this.schoolGrade);
                    if (this.decision)        params.set('decision', this.decision);
                    if (this._ageMin)         params.set('age_min', this._ageMin);
                    if (this._ageMax)         params.set('age_max', this._ageMax);
                    params.set('sort', this.sortField);
                    params.set('dir', this.sortAsc ? 'asc' : 'desc');
                    params.set('page', this.currentPage);

                    try {
                        const res = await fetch(`/students?${params}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        // ✅ التحقق من status code قبل parse
                        if (!res.ok) {
                            console.error('Server error:', res.status);
                            this.loading = false;
                            return;
                        }

                        const data = await res.json();
                        this.students = data.data;
                        this.meta = {
                            total:        data.total,
                            last_page:    data.last_page,
                            current_page: data.current_page,
                            per_page:     data.per_page,
                        };
                    } catch (e) {
                        console.error('fetch error', e);
                    } finally {
                        this.loading = false;
                    }
                },

                sortBy(field) {
                    if (this.sortField === field) {
                        this.sortAsc = !this.sortAsc;
                    } else {
                        this.sortField = field;
                        this.sortAsc   = true;
                    }
                },

                goToPage(page) {
                    if (page >= 1 && page <= this.meta.last_page) {
                        this.currentPage = page;
                        this.fetch();
                    }
                },

                get hasFilters() {
                    return [
                        this.q, this.status, this.circleId, this.educationalStage,
                        this.centerId, this.schoolGrade, this.decision,
                        this._ageMin, this._ageMax,
                    ].some(v => String(v).trim() !== '');
                },

                get pages() {
                    const total = this.meta.last_page;
                    const cur   = this.currentPage;
                    const delta = 2;
                    const range = [];

                    for (let i = Math.max(2, cur - delta); i <= Math.min(total - 1, cur + delta); i++) {
                        range.push(i);
                    }

                    const list = [1];
                    if (range[0] > 2) list.push('...');
                    list.push(...range);
                    if (range[range.length - 1] < total - 1) list.push('...');
                    if (total > 1) list.push(total);
                    return list;
                },

                resetFilters() {
                    Object.assign(this, {
                        q:               '',
                        status:          '',
                        circleId:        '',
                        educationalStage:'',
                        centerId:        '',
                        _ageMin:         '',
                        _ageMax:         '',
                        schoolGrade:     '',
                        decision:        '',
                        currentPage:     1,
                    });
                },
            };
        }
    </script>

    <div class="space-y-6" x-data="studentsIndex()">

        {{-- Header --}}
        <div class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="order-2 md:order-2 flex flex-wrap items-center gap-4 w-full md:w-auto">
                @can('create students')
                <a href="{{ route('students.create') }}"
                    class="w-full md:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    جديد
                </a>
                @endcan
            </div>

            <div class="order-1 md:order-1 text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">إدارة الطلاب</h1>
                <p class="text-emerald-100/80 text-sm font-medium"
                    x-text="loading
                        ? 'جاري التحميل...'
                        : (hasFilters
                            ? (meta.total + ' نتيجة')
                            : (meta.total + ' طالب مسجل في النظام'))">
                </p>
            </div>

            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        {{-- فلاتر --}}
        <div class="bg-white p-4 rounded-xl border border-gray-100 space-y-4">

            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative">
                    <input type="search" x-model="q"
                        placeholder="بحث بالاسم، الكود، الهاتف..."
                        class="w-full px-4 py-2.5 pr-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#10b981]/50 focus:border-emerald-500">
                    <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="button" x-show="hasFilters" @click="resetFilters()"
                    class="px-5 py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium rounded-lg transition-colors shrink-0">
                    إعادة تعيين
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
                    <select x-model="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <option value="">الكل</option>
                        <option value="مقيد">مقيد</option>
                        <option value="متوقف">متوقف</option>
                        <option value="مسافر">مسافر</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الحلقة</label>
                    <select x-model="circleId" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <option value="">كل الحلقات</option>
                        <template x-for="circle in circles" :key="circle.id">
                            <option :value="String(circle.id)" x-text="circle.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">المرحلة الدراسية</label>
                    <select x-model="educationalStage" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <option value="">الكل</option>
                        <option value="تمهيدي">تمهيدي</option>
                        <option value="حضانة">حضانة</option>
                        <option value="ابتدائي">ابتدائي</option>
                        <option value="اعدادي">اعدادي</option>
                        <option value="ثانوي">ثانوي</option>
                        <option value="جامعي">جامعي</option>
                        <option value="خريج">خريج</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @if($centers->count() > 1)
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الفرع</label>
                    <select x-model="centerId" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <option value="">كل الفروع</option>
                        <template x-for="c in centers" :key="c.id">
                            <option :value="String(c.id)" x-text="c.name"></option>
                        </template>
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">العمر (من – إلى)</label>
                    <div class="flex items-center gap-2">
                        {{-- ✅ min/max في HTML + setter في Alpine يحمي من قيم خارج النطاق --}}
                        <input type="number" x-model="ageMin"
                            min="1" max="99" placeholder="من"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <span class="text-gray-400 shrink-0">–</span>
                        <input type="number" x-model="ageMax"
                            min="1" max="99" placeholder="إلى"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الصف الدراسي</label>
                    <select x-model="schoolGrade" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <option value="">كل الصفوف</option>
                        @foreach(['الأول','الثاني','الثالث','الرابع','الخامس','السادس','دراسات عليا','لا يوجد'] as $grade)
                        <option value="{{ $grade }}">{{ $grade }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ✅ فلتر قرار الإدارة — للأدوار الإدارية فقط --}}
            @if($canViewStudents)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">قرار الإدارة</label>
                    <select x-model="decision" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <option value="">الكل</option>
                        <option value="تحت الاختبار">تحت الاختبار</option>
                        <option value="مقبول">مقبول</option>
                        <option value="مرفوض">مرفوض</option>
                    </select>
                </div>
            </div>
            @endif

        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-x-auto">
            <table class="w-full text-right min-w-[900px]">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th @click="sortBy('name')"
                            class="py-4 px-6 font-medium rounded-tr-xl cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>اسم الطالب</span>
                                <span x-show="sortField === 'name'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th @click="sortBy('status')"
                            class="py-4 px-6 font-medium cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>الحالة</span>
                                <span x-show="sortField === 'status'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        {{-- ✅ circle_name الآن في allowedSorts --}}
                        <th @click="sortBy('circle_name')"
                            class="py-4 px-6 font-medium cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>الحلقة</span>
                                <span x-show="sortField === 'circle_name'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th @click="sortBy('educational_stage')"
                            class="py-4 px-6 font-medium cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>المرحلة الدراسية</span>
                                <span x-show="sortField === 'educational_stage'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th @click="sortBy('age')"
                            class="py-4 px-6 font-medium cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>العمر</span>
                                <span x-show="sortField === 'age'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th class="py-4 px-6 font-medium rounded-tl-xl"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    {{-- Loading --}}
                    <tr x-show="loading">
                        <td colspan="6" class="py-12 text-center">
                            <div class="inline-flex items-center gap-2 text-gray-400">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                جاري التحميل...
                            </div>
                        </td>
                    </tr>

                    <template x-if="!loading">
                        <template x-for="student in students" :key="student.id">
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-4 px-6 font-medium text-gray-800" x-text="student.name"></td>
                                <td class="py-4 px-6">
                                    <span x-show="student.status === 'مقيد'"
                                        class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-md text-sm">مقيد</span>
                                    <span x-show="student.status === 'متوقف'"
                                        class="px-3 py-1 bg-orange-100 text-orange-700 rounded-md text-sm">موقوف</span>
                                    <span x-show="student.status === 'مسافر'"
                                        class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded-md text-sm">مسافر</span>
                                </td>
                                <td class="py-4 px-6 text-gray-600" x-text="student.circle_name || '—'"></td>
                                <td class="py-4 px-6 text-gray-600" x-text="student.educational_stage || '—'"></td>
                                <td class="py-4 px-6 text-gray-600" x-text="student.age ?? '—'"></td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-end gap-3">

                                        {{-- ✅ show_url مشروط من الـ backend --}}
                                        <a x-show="student.show_url"
                                            :href="student.show_url"
                                            class="text-green-400 hover:text-green-600 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        {{-- edit_url مشروط كما كان --}}
                                        <a x-show="student.edit_url"
                                            :href="student.edit_url"
                                            class="text-blue-500 hover:text-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <tr x-show="!loading && students.length === 0">
                        <td colspan="6" class="py-12 px-6 text-center text-gray-500">
                            <span x-show="hasFilters">لا توجد نتائج مطابقة لبحثك أو الفلاتر.</span>
                            <span x-show="!hasFilters">لا يوجد طلاب مسجلون حالياً.</span>
                        </td>
                    </tr>

                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 border-t border-gray-100"
                x-show="meta.last_page > 1">
                <div class="text-sm text-gray-500">
                    الصفحة <span class="font-medium text-gray-700" x-text="meta.current_page"></span>
                    من <span class="font-medium text-gray-700" x-text="meta.last_page"></span>
                    — إجمالي <span class="font-medium text-gray-700" x-text="meta.total"></span> طالب
                </div>
                <div class="flex items-center gap-1.5" dir="ltr">
                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                        :class="currentPage === 1
                            ? 'text-gray-300 border-gray-100 cursor-not-allowed'
                            : 'text-gray-600 border-gray-200 hover:bg-gray-50'"
                        class="px-3 py-1.5 rounded-lg border text-sm font-medium transition-colors">
                        ‹ السابق
                    </button>
                    <template x-for="(page, i) in pages" :key="i">
                        <span class="inline-flex items-center">
                            <span x-show="page === '...'" class="px-1.5 text-gray-400 select-none">...</span>
                            <button x-show="page !== '...'" @click="goToPage(page)"
                                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-w-[36px] text-center"
                                :class="currentPage === page
                                    ? 'bg-[#0a5c36] text-white shadow-sm'
                                    : 'text-gray-600 border border-gray-200 hover:bg-gray-50'"
                                x-text="page">
                            </button>
                        </span>
                    </template>
                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === meta.last_page"
                        :class="currentPage === meta.last_page
                            ? 'text-gray-300 border-gray-100 cursor-not-allowed'
                            : 'text-gray-600 border-gray-200 hover:bg-gray-50'"
                        class="px-3 py-1.5 rounded-lg border text-sm font-medium transition-colors">
                        التالي ›
                    </button>
                </div>
            </div>

        </div>

    </div>

</x-layouts.markaz-layout>