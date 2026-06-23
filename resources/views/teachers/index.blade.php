{{-- 1. نضع كود الـ PHP والـ Mapping في أعلى الملف تماماً --}}
@php
$teachersList = $teachers->map(fn($t) => [
'id' => $t->id,
'name' => $t->name,
'email' => $t->user->email ?? '',
'center' => $t->center?->name ?? '',
'status' => $t->user->status ?? 'inactive',
'is_online' => $t->user->is_online ?? false,
'roles' => $t->user->roles->map(fn($r) => [
'name' => $r->name,
'display_name' => $r->display_name ?? $r->name,
])->toArray(),
'show_url' => auth()->user()->can('view', $t) ? route('teachers.show', $t) : null,
'edit_url' => auth()->user()->can('update', $t) ? route('teachers.edit', $t) : null,
'delete_url' => auth()->user()->can('delete', $t) ? route('teachers.destroy', $t) : null,
'toggle_url' => auth()->user()->can('toggle', $t) ? route('teachers.toggle', $t) : null,
]);

// توحيد الألوان برمجياً في مكان واحد لسهولة التعديل مستقبلاً
$roleColors = [
'teacher' => 'bg-red-50 text-red-700 border border-red-200',
'supervisor' => 'bg-blue-50 text-blue-700 border border-blue-200',
'manager' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
'admin' => 'bg-purple-50 text-purple-700 border border-purple-200',
'general_manager' => 'bg-purple-50 text-purple-700 border border-purple-200',
];
@endphp

<x-layouts.markaz-layout>

    <script>
        function teachersIndex() {
            return {
                teachers: @json($teachersList),
                roleColors: @json($roleColors), // تمرير الألوان من الـ PHP مباشرة لـ Alpine
                q: '',
                centerId: '',
                role: '',
                status: '',
                currentPage: 1,
                perPage: 20,
                sortField: 'name',
                sortAsc: true,

                sortBy(field) {
                    this.sortField === field ?
                        this.sortAsc = !this.sortAsc :
                        (this.sortField = field, this.sortAsc = true);
                    this.currentPage = 1;
                },

                goToPage(page) {
                    if (page >= 1 && page <= this.totalPages) this.currentPage = page;
                },

                get hasFilters() {
                    return this.q.trim() !== '' || this.centerId !== '' || this.role !== '' || this.status !== '';
                },

                get filteredTeachers() {
                    const term = this.q.trim().toLowerCase();

                    let result = this.teachers.filter(t => {
                        if (this.centerId && t.center !== this.centerId) return false;
                        if (this.status && t.status !== this.status) return false;
                        if (this.role && !t.roles.some(r => r.name === this.role)) return false;
                        if (!term) return true;
                        return [t.name, t.email, t.center].join(' ').toLowerCase().includes(term);
                    });

                    result.sort((a, b) => {
                        let vA = a[this.sortField];
                        let vB = b[this.sortField];

                        if (this.sortField === 'role') {
                            vA = a.roles[0]?.display_name ?? '';
                            vB = b.roles[0]?.display_name ?? '';
                        }

                        vA = vA ?? '';
                        vB = vB ?? '';

                        if (typeof vA === 'string') {
                            return this.sortAsc ?
                                vA.localeCompare(vB, 'ar', {
                                    sensitivity: 'base'
                                }) :
                                vB.localeCompare(vA, 'ar', {
                                    sensitivity: 'base'
                                });
                        }

                        if (typeof vA === 'boolean') {
                            return this.sortAsc ? (vA === vB ? 0 : vA ? -1 : 1) : (vA === vB ? 0 : vA ? 1 : -1);
                        }

                        return this.sortAsc ? (vA < vB ? -1 : 1) : (vA > vB ? -1 : 1);
                    });

                    return result;
                },

                get totalCount() {
                    return this.teachers.length;
                },
                get visibleCount() {
                    return this.filteredTeachers.length;
                },
                get totalPages() {
                    return Math.ceil(this.filteredTeachers.length / this.perPage) || 1;
                },

                get paginatedTeachers() {
                    if (this.currentPage > this.totalPages) this.currentPage = 1;
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredTeachers.slice(start, start + this.perPage);
                },

                get pages() {
                    const total = this.totalPages,
                        cur = this.currentPage,
                        delta = 2;
                    const range = [];
                    for (let i = Math.max(2, cur - delta); i <= Math.min(total - 1, cur + delta); i++) range.push(i);
                    const list = [1];
                    if (range[0] > 2) list.push('...');
                    list.push(...range);
                    if (range[range.length - 1] < total - 1) list.push('...');
                    if (total > 1) list.push(total);
                    return list;
                },

                resetFilters() {
                    this.q = '';
                    this.centerId = '';
                    this.role = '';
                    this.status = '';
                    this.currentPage = 1;
                },

                confirmDelete(url, name) {
                    Swal.fire({
                        title: 'حذف معلم: ' + name,
                        text: 'سيتم حذف المعلم وحسابه من النظام. لن تتمكن من التراجع!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-3xl font-bold',
                            confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
                            cancelButton: 'rounded-xl px-6 py-2.5 text-sm',
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = url;
                            form.innerHTML = `
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                },

                async toggleStatus(url) {
                    const result = await Swal.fire({
                        title: 'تغيير حالة الحساب؟',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0a5c36',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'نعم، تغيير',
                        cancelButtonText: 'إلغاء',
                        customClass: {
                            popup: 'rounded-3xl font-bold',
                            confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
                            cancelButton: 'rounded-xl px-6 py-2.5 text-sm',
                        }
                    });

                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = url;

                        // الحماية من انهيار التوكن باستدعائه مباشرة من البليد الآمن
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = "{{ csrf_token() }}";

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PATCH';

                        form.appendChild(tokenInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                },
            };
        }
    </script>

    <div class="space-y-6" x-data="teachersIndex()">

        {{-- Header --}}
        <div class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">إدارة المعلمين</h1>
                <p class="text-emerald-100/80 text-sm font-medium"
                    x-text="hasFilters ? (visibleCount + ' نتيجة من ' + totalCount) : (totalCount + ' معلم مسجل في النظام')">
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                @can('create teachers')
                <a href="{{ route('teachers.create') }}"
                    class="w-full md:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة معلم جديد
                </a>
                @endcan
            </div>
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <div class="flex flex-wrap gap-3 items-end">

                {{-- بحث --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-gray-500 mb-1">بحث بالاسم أو البريد</label>
                    <div class="relative">
                        <input type="search" x-model.debounce.200ms="q"
                            placeholder="ابحث عن معلم..."
                            class="w-full p-2.5 pr-9 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all">
                        <svg class="w-4 h-4 absolute right-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                        </svg>
                    </div>
                </div>

                {{-- فلتر الفرع --}}
                @can('view all teachers')
                <div class="min-w-[180px] flex-1 sm:flex-none">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الفرع</label>
                    <select x-model="centerId"
                        class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none bg-no-repeat bg-[left_10px_center]">
                        <option value="">-- كل الفروع --</option>
                        @foreach($centers as $center)
                        <option value="{{ $center->name }}">{{ $center->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endcan

                {{-- فلتر الدور --}}
                @if(auth()->user()->can('view all teachers') || auth()->user()->hasRole('manager'))
                <div class="min-w-[180px] flex-1 sm:flex-none">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الدور</label>
                    <select x-model="role"
                        class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                        <option value="">-- كل الأدوار --</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->display_name ?? $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endcan

                {{-- فلتر الحالة --}}
                <div class="min-w-[150px] flex-1 sm:flex-none">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الحالة الحسابية</label>
                    <select x-model="status"
                        class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                        <option value="">-- كل الحالات --</option>
                        <option value="active">نشط</option>
                        <option value="inactive">موقوف</option>
                    </select>
                </div>

                {{-- إعادة تعيين --}}
                <button type="button" x-show="hasFilters" @click="resetFilters()"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl text-sm transition-all">
                    مسح
                </button>

            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-x-auto">
            <table class="w-full text-right min-w-[950px]">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th @click="sortBy('name')" class="py-4 px-6 font-medium rounded-tr-xl cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>اسم المعلم</span>
                                <span x-show="sortField === 'name'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th @click="sortBy('email')" class="py-4 px-6 font-medium cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>البريد الإلكتروني</span>
                                <span x-show="sortField === 'email'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th @click="sortBy('center')" class="py-4 px-6 font-medium cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>الفرع</span>
                                <span x-show="sortField === 'center'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th @click="sortBy('role')" class="py-4 px-6 font-medium cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>الأدوار</span>
                                <span x-show="sortField === 'role'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th @click="sortBy('is_online')" class="py-4 px-6 font-medium cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>الاتصال</span>
                                <span x-show="sortField === 'is_online'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th @click="sortBy('status')" class="py-4 px-6 font-medium cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <div class="flex items-center gap-1">
                                <span>الحالة</span>
                                <span x-show="sortField === 'status'" x-text="sortAsc ? '↑' : '↓'"></span>
                            </div>
                        </th>
                        <th class="py-4 px-6 font-medium rounded-tl-xl"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <template x-for="teacher in paginatedTeachers" :key="teacher.id">
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-4 px-6 font-medium text-gray-800" x-text="teacher.name"></td>
                            <td class="py-4 px-6 text-gray-600 text-sm" x-text="teacher.email || '—'"></td>
                            <td class="py-4 px-6 text-sm">
                                <template x-if="teacher.center">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold" x-text="teacher.center"></span>
                                </template>
                                <template x-if="!teacher.center">
                                    <span class="text-gray-400">—</span>
                                </template>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-if="teacher.roles.length === 0">
                                        <span class="text-gray-400 text-xs">—</span>
                                    </template>
                                    <template x-for="role in teacher.roles" :key="role.name">
                                        <div class="px-2.5 py-1 rounded-md text-xs font-bold inline-flex items-center gap-1.5 transition-all shadow-sm"
                                            :class="roleColors[role.name] ?? 'bg-gray-50 text-gray-600 border border-gray-200'">
                                            <span x-text="role.display_name"></span>
                                        </div>
                                    </template>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full animate-pulse" :class="teacher.is_online ? 'bg-emerald-500' : 'bg-gray-300'"></span>
                                    <span class="font-bold text-xs" :class="teacher.is_online ? 'text-emerald-600' : 'text-gray-400'" x-text="teacher.is_online ? 'متصل الآن' : 'غير متصل'"></span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <template x-if="teacher.toggle_url">
                                    <button type="button" @click="toggleStatus(teacher.toggle_url)"
                                        class="px-3 py-1 rounded-full text-xs font-bold transition-all"
                                        :class="teacher.status === 'active' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                        x-text="teacher.status === 'active' ? 'نشط' : 'موقوف'">
                                    </button>
                                </template>
                                <template x-if="!teacher.toggle_url">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold"
                                        :class="teacher.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                                        x-text="teacher.status === 'active' ? 'نشط' : 'موقوف'">
                                    </span>
                                </template>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-end gap-3">
                                    <template x-if="teacher.show_url">
                                        <a :href="teacher.show_url" class="text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 p-1.5 rounded-lg transition" title="عرض التفاصيل">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </template>
                                    <template x-if="teacher.edit_url">
                                        <a :href="teacher.edit_url" class="text-blue-500 hover:text-blue-700 hover:bg-blue-50 p-1.5 rounded-lg transition" title="تعديل">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    </template>
                                    <template x-if="teacher.delete_url">
                                        <button type="button" @click="confirmDelete(teacher.delete_url, teacher.name)" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-lg transition" title="حذف">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredTeachers.length === 0">
                        <td colspan="7" class="py-12 text-center text-gray-400 font-medium">
                            <span x-show="hasFilters">لا توجد نتائج مطابقة للفلاتر المحددة.</span>
                            <span x-show="!hasFilters">لا يوجد معلمون مسجلون حالياً.</span>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 border-t border-gray-100" x-show="filteredTeachers.length > perPage">
                <div class="text-sm text-gray-500">
                    الصفحة <span class="font-medium text-gray-700" x-text="currentPage"></span> من <span class="font-medium text-gray-700" x-text="totalPages"></span> — عرض
                    <span class="font-medium text-gray-700" x-text="Math.min((currentPage-1)*perPage+1, filteredTeachers.length)"></span>–<span class="font-medium text-gray-700" x-text="Math.min(currentPage*perPage, filteredTeachers.length)"></span> من <span class="font-medium text-gray-700" x-text="filteredTeachers.length"></span>
                </div>
                <div class="flex items-center gap-1.5" dir="ltr">
                    <button @click="goToPage(currentPage-1)" :disabled="currentPage===1" :class="currentPage===1?'text-gray-300 border-gray-100 cursor-not-allowed':'text-gray-600 border-gray-200 hover:bg-gray-50'" class="px-3 py-1.5 rounded-lg border text-sm font-medium transition-colors">‹ السابق</button>
                    <template x-for="(page,i) in pages" :key="i">
                        <span class="inline-flex items-center">
                            <span x-show="page==='...'" class="px-1.5 text-gray-400 select-none">...</span>
                            <button x-show="page!=='...'" @click="goToPage(page)" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all min-w-[36px] text-center" :class="currentPage===page?'bg-[#0a5c36] text-white shadow-sm':'text-gray-600 border border-gray-200 hover:bg-gray-50'" x-text="page"></button>
                        </span>
                    </template>
                    <button @click="goToPage(currentPage+1)" :disabled="currentPage===totalPages" :class="currentPage===totalPages?'text-gray-300 border-gray-100 cursor-not-allowed':'text-gray-600 border-gray-200 hover:bg-gray-50'" class="px-3 py-1.5 rounded-lg border text-sm font-medium transition-colors">التالي ›</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert Scripts --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح',
                text: "{{ session('success') }}",
                confirmButtonColor: '#0a5c36',
                confirmButtonText: 'حسناً',
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-3xl font-bold',
                    confirmButton: 'rounded-xl px-6 py-2.5 text-sm'
                }
            });
        });
        @endif

        @if(session('error'))
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: "{{ session('error') }}",
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'حسناً',
                customClass: {
                    popup: 'rounded-3xl font-bold',
                    confirmButton: 'rounded-xl px-6 py-2.5 text-sm'
                }
            });
        });
        @endif
    </script>
    @endpush
</x-layouts.markaz-layout>