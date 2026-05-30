@php
    $canEditStudents = auth()->user()->hasAnyRole(['admin', 'supervisor']);
    $studentsList = $students->map(fn ($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'status' => $s->status,
        'circle_id' => $s->circle_id,
        'circle_name' => $s->circle?->name ?? '',
        'education_level' => $s->education_level ?? '',
        'age' => $s->age,
        'student_code' => $s->student_code ?? '',
        'phone' => $s->phone ?? '',
        'whatsapp_number' => $s->whatsapp_number ?? '',
        'educational_stage' => $s->educational_stage ?? '',
        'show_url' => route('students.show', $s),
        'edit_url' => route('students.edit', $s),
    ]);
    $circlesList = $students
        ->pluck('circle')
        ->filter()
        ->unique('id')
        ->sortBy('name')
        ->values()
        ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]);
@endphp

<x-layouts.markaz-layout>

    <script>
        function studentsIndex() {
            return {
                students: @json($studentsList),
                circles: @json($circlesList),
                q: '',
                status: '',
                circleId: '',
                educationLevel: '',

                educationLabels: {
                    preschool: 'حضانة',
                    primary: 'ابتدائية',
                    secondary: 'إعدادية',
                    high_school: 'ثانوية',
                    university: 'جامعية',
                    other: 'أخرى',
                },

                statusLabels: {
                    active: 'مقيد',
                    inactive: 'موقوف',
                    traveler: 'مسافر',
                },

                get totalCount() {
                    return this.students.length;
                },

                get hasFilters() {
                    return this.q.trim() !== '' || this.status !== '' || this.circleId !== '' ||
                        this.educationLevel !== '';
                },

                get filteredStudents() {
                    const term = this.q.trim().toLowerCase();

                    return this.students.filter((student) => {
                        if (this.status && student.status !== this.status) {
                            return false;
                        }
                        if (this.circleId && String(student.circle_id) !== String(this.circleId)) {
                            return false;
                        }
                        if (this.educationLevel && student.education_level !== this.educationLevel) {
                            return false;
                        }
                        if (!term) {
                            return true;
                        }

                        const haystack = [
                            student.name,
                            student.student_code,
                            student.circle_name,
                            student.phone,
                            student.whatsapp_number,
                            student.educational_stage,
                            this.educationLabels[student.education_level] || '',
                            this.statusLabels[student.status] || '',
                        ].join(' ').toLowerCase();

                        return haystack.includes(term);
                    });
                },

                get visibleCount() {
                    return this.filteredStudents.length;
                },

                resetFilters() {
                    this.q = '';
                    this.status = '';
                    this.circleId = '';
                    this.educationLevel = '';
                },
            };
        }
    </script>

    <div class="space-y-6" x-data="studentsIndex()">

        <!-- Header Card -->
        <div
            class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
            <div class="order-2 md:order-2 flex flex-wrap items-center gap-4 w-full md:w-auto">
                @role(['admin', 'supervisor'])
                    <a href="{{ route('students.create') }}"
                        class="w-full md:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        جديد
                    </a>
                @endrole
            </div>

            <div class="order-1 md:order-1 text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">إدارة الطلاب</h1>
                <p class="text-emerald-100/80 text-sm font-medium"
                    x-text="hasFilters ? (visibleCount + ' نتيجة من ' + totalCount) : (totalCount + ' طالب مسجل في النظام')">
                    {{ $students->count() }} طالب مسجل في النظام
                </p>
            </div>

            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <!-- بحث وفلاتر (بدون refresh) -->
        <div class="bg-white p-4 rounded-xl border border-gray-100 space-y-4">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative">
                    <input type="search" x-model.debounce.200ms="q"
                        placeholder="بحث بالاسم، الكود، الحلقة، الهاتف، أو المرحلة..."
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
                    <label for="filter_status" class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
                    <select id="filter_status" x-model="status"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <option value="">الكل</option>
                        <option value="active">مقيد</option>
                        <option value="inactive">موقوف</option>
                        <option value="traveler">مسافر</option>
                    </select>
                </div>

                <div>
                    <label for="filter_circle" class="block text-xs font-bold text-gray-500 mb-1">الحلقة</label>
                    <select id="filter_circle" x-model="circleId"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <option value="">كل الحلقات</option>
                        <template x-for="circle in circles" :key="circle.id">
                            <option :value="String(circle.id)" x-text="circle.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label for="filter_education" class="block text-xs font-bold text-gray-500 mb-1">المرحلة
                        الدراسية</label>
                    <select id="filter_education" x-model="educationLevel"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#10b981]/50">
                        <option value="">الكل</option>
                        <option value="preschool">حضانة</option>
                        <option value="primary">ابتدائية</option>
                        <option value="secondary">إعدادية</option>
                        <option value="high_school">ثانوية</option>
                        <option value="university">جامعية</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-x-auto">
            <table class="w-full text-right min-w-[900px]">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="py-4 px-6 font-medium rounded-tr-xl">اسم الطالب</th>
                        <th class="py-4 px-6 font-medium">الحالة</th>
                        <th class="py-4 px-6 font-medium">الحلقة</th>
                        <th class="py-4 px-6 font-medium">المرحلة الدراسيية</th>
                        <th class="py-4 px-6 font-medium">العمر</th>
                        <th class="py-4 px-6 font-medium rounded-tl-xl"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <template x-for="student in filteredStudents" :key="student.id">
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-4 px-6 font-medium text-gray-800" x-text="student.name"></td>
                            <td class="py-4 px-6">
                                <span x-show="student.status === 'active'"
                                    class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-md text-sm">مقيد</span>
                                <span x-show="student.status === 'inactive'"
                                    class="px-3 py-1 bg-orange-100 text-orange-700 rounded-md text-sm">موقوف</span>
                                <span x-show="student.status === 'traveler'"
                                    class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded-md text-sm">مسافر</span>
                            </td>
                            <td class="py-4 px-6 text-gray-600" x-text="student.circle_name || '—'"></td>
                            <td class="py-4 px-6 text-gray-600">
                                <span x-show="student.education_level === 'preschool'"
                                    class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-semibold">حضانة</span>
                                <span x-show="student.education_level === 'primary'"
                                    class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">ابتدائية</span>
                                <span x-show="student.education_level === 'secondary'"
                                    class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">إعدادية</span>
                                <span x-show="student.education_level === 'high_school'"
                                    class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">ثانوية</span>
                                <span x-show="student.education_level === 'university'"
                                    class="px-3 py-1 bg-violet-100 text-violet-700 rounded-full text-xs font-semibold">جامعية</span>
                                <span x-show="student.education_level === 'other'"
                                    class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold">أخرى</span>
                            </td>
                            <td class="py-4 px-6 text-gray-600" x-text="student.age ?? '—'"></td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-end gap-3">
                                    <a :href="student.show_url"
                                        class="text-green-400 hover:text-green-600 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    @if ($canEditStudents)
                                        <a :href="student.edit_url"
                                            class="text-blue-500 hover:text-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filteredStudents.length === 0">
                        <td colspan="6" class="py-12 px-6 text-center text-gray-500">
                            <span x-show="hasFilters">لا توجد نتائج مطابقة لبحثك أو الفلاتر.</span>
                            <span x-show="!hasFilters">لا يوجد طلاب مسجلون حالياً.</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</x-layouts.markaz-layout>
