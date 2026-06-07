<x-layouts.markaz-layout>

    <div class="space-y-6">

        <!-- Header Card -->
        <div class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
            <div class="text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">إدارة المعلمين</h1>
                <p class="text-emerald-100/80 text-sm font-medium">{{ count($teachers) }} معلم مسجل في النظام</p>
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
        {{-- Search & Filters --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <form method="GET" action="{{ route('teachers.index') }}"
                class="flex flex-wrap gap-3 items-end">

                {{-- البحث --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-gray-500 mb-1">بحث بالاسم</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="ابحث عن معلم..."
                            class="w-full p-2.5 pr-9 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all">
                        <svg class="w-4 h-4 absolute right-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                        </svg>
                    </div>
                </div>

                {{-- فلتر الفرع — بصلاحية خاصة --}}
                {{-- فلتر الفرع — بصلاحيتين معاً --}}
                @can('filter teachers by center')
                @can('view all teachers')
                <div class="min-w-[180px]">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الفرع</label>
                    <select name="center_id"
                        class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all appearance-none">
                        <option value="">-- كل الفروع --</option>
                        @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                            {{ $center->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endcan
                @endcan

                {{-- فلتر الدور — بصلاحية خاصة --}}
                @can('filter teachers by role')
                <div class="min-w-[180px]">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الدور</label>
                    <select name="role"
                        class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all appearance-none">
                        <option value="">-- كل الأدوار --</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->display_name ?? $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endcan

                {{-- أزرار --}}
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-5 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d] text-white font-bold rounded-xl text-sm transition-all">
                        بحث
                    </button>
                    @if(request()->hasAny(['search', 'center_id', 'role']))
                    <a href="{{ route('teachers.index') }}"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl text-sm transition-all">
                        مسح
                    </a>
                    @endif
                </div>

            </form>
        </div>
        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-x-auto">
            <table class="w-full text-right min-w-[900px]">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="py-4 px-6 font-medium rounded-tr-xl">اسم المعلم</th>
                        <th class="py-4 px-6 font-medium">البريد الإلكتروني</th>
                        <th class="py-4 px-6 font-medium">الفرع</th>
                        <th class="py-4 px-6 font-medium">الأدوار</th>
                        <th class="py-4 px-6 font-medium">الحالة</th>
                        <th class="py-4 px-6 font-medium rounded-tl-xl"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($teachers as $teacher)
                    <tr class="hover:bg-gray-50/50">

                        {{-- الاسم --}}
                        <td class="py-4 px-6 font-medium text-gray-800">
                            {{ $teacher->name }}
                        </td>

                        {{-- البريد --}}
                        <td class="py-4 px-6 text-gray-600 text-sm">
                            {{ $teacher->user->email ?? '—' }}
                        </td>

                        {{-- الفرع ✅ --}}
                        <td class="py-4 px-6 text-gray-600 text-sm">
                            @if($teacher->center)
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold">
                                {{ $teacher->center->name }}
                            </span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- الأدوار ✅ --}}
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-1">
                                @forelse($teacher->user->roles as $role)
                                @php
                                $colors = [
                                'admin' => 'bg-purple-50 text-purple-700',
                                'supervisor' => 'bg-blue-50 text-blue-700',
                                'teacher' => 'bg-red-50 text-red-700',
                                'guardian' => 'bg-amber-50 text-amber-700',
                                ];
                                $color = $colors[$role->name] ?? 'bg-gray-50 text-gray-700';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                    {{ $role->display_name ?? $role->name }}
                                </span>
                                @empty
                                <span class="text-gray-400 text-xs">—</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- الحالة --}}
                        <td class="py-4 px-6">
                            @can('toggle teacher status')
                            <form action="{{ route('teachers.toggle', $teacher) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <label class="inline-flex items-center cursor-pointer relative">
                                    <input type="checkbox" onchange="this.form.submit()" class="sr-only peer"
                                        {{ $teacher->user->status === 'active' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 rounded-full transition peer-checked:bg-emerald-500"></div>
                                    <span class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></span>
                                </label>
                            </form>
                            @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $teacher->user->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $teacher->user->status === 'active' ? 'نشط' : 'غير نشط' }}
                            </span>
                            @endcan
                        </td>

                        {{-- الإجراءات --}}
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-end gap-3">

                                {{-- تعديل --}}
                                @can('edit teachers')
                                <a href="{{ route('teachers.edit', $teacher) }}"
                                    class="text-blue-500 hover:text-blue-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan

                                {{-- حذف --}}
                                @can('delete teachers')
                                <button type="button"
                                    onclick="confirmDeleteTeacher({{ $teacher->id }}, '{{ $teacher->name }}')"
                                    class="text-red-400 hover:text-red-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                <form id="delete-teacher-{{ $teacher->id }}"
                                    action="{{ route('teachers.destroy', $teacher) }}"
                                    method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endcan

                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeleteTeacher(teacherId, teacherName) {
            Swal.fire({
                title: 'حذف معلم: ' + teacherName,
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
                    document.getElementById('delete-teacher-' + teacherId).submit();
                }
            });
        }

        @if(session('success'))
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
                confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
            }
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: "{{ session('error') }}",
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'حسناً',
            customClass: {
                popup: 'rounded-3xl font-bold',
                confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
            }
        });
        @endif
    </script>
    @endpush

</x-layouts.markaz-layout>