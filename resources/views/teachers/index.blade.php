@php
$roleColors = [
'teacher' => 'bg-red-50 text-red-700 border border-red-200',
'supervisor' => 'bg-blue-50 text-blue-700 border border-blue-200',
'manager' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
'admin' => 'bg-purple-50 text-purple-700 border border-purple-200',
'general_manager' => 'bg-purple-50 text-purple-700 border border-purple-200',
];
@endphp

<x-layouts.markaz-layout>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">إدارة المعلمين</h1>
                @if(auth()->user()->hasRole(['admin', 'general_manager']))
                <p class="text-emerald-100/80 text-sm font-medium">
                    @if(request()->anyFilled(['q', 'center_id', 'role', 'status']))
                    {{ $teachers->total() }} نتيجة
                    @else
                    {{ $teachers->total() }} معلم مسجل في النظام
                    @endif
                </p>
                @else
                <p class="text-emerald-100/80 text-sm font-medium">
                    قائمة المعلمين
                </p>
                @endif
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
            <form method="GET" action="{{ route('teachers.index') }}" class="flex flex-wrap gap-3 items-end">

                {{-- بحث --}}
                <div class="flex-1 min-w-50">
                    <label class="block text-xs font-bold text-gray-500 mb-1">بحث بالاسم أو البريد</label>
                    <div class="relative">
                        <input type="search" name="q" value="{{ request('q') }}"
                            placeholder="ابحث عن معلم..."
                            class="w-full p-2.5 pr-9 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all">
                        <svg class="w-4 h-4 absolute right-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                        </svg>
                    </div>
                </div>

                {{-- فلتر الفرع --}}
                @if(auth()->user()->hasRole(['admin', 'general_manager']))
                <div class="min-w-45 flex-1 sm:flex-none">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الفرع</label>
                    <select name="center_id"
                        class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                        <option value="">-- كل الفروع --</option>
                        @foreach($centers as $center)
                        <option value="{{ $center->name }}" @selected(request('center_id')===$center->name)>{{ $center->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- فلتر الدور --}}
                @if(auth()->user()->hasRole(['admin', 'general_manager', 'manager']))
                <div class="min-w-45 flex-1 sm:flex-none">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الدور</label>
                    <select name="role"
                        class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                        <option value="">-- كل الأدوار --</option>
                        @foreach($roles as $r)
                        @if(!in_array($r->name, ['admin', 'guardian']))
                        <option value="{{ $r->name }}" @selected(request('role')===$r->name)>{{ $r->display_name ?? $r->name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- فلتر الحالة --}}
                <div class="min-w-37.5 flex-1 sm:flex-none">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الحالة الحسابية</label>
                    <select name="status"
                        class="w-full p-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                        <option value="">-- كل الحالات --</option>
                        <option value="active" @selected(request('status')==='active' )>نشط</option>
                        <option value="inactive" @selected(request('status')==='inactive' )>موقوف</option>
                    </select>
                </div>

                {{-- بحث --}}
                <button type="submit"
                    class="px-5 py-2.5 bg-[#0a5c36] hover:bg-[#08492a] text-white font-bold rounded-xl text-sm transition-all">
                    بحث
                </button>

                {{-- إعادة تعيين --}}
                @if(request()->anyFilled(['q', 'center_id', 'role', 'status']))
                <a href="{{ route('teachers.index') }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl text-sm transition-all">
                    مسح
                </a>
                @endif

            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-x-auto">
            <table class="w-full text-right min-w-250">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="py-4 px-6 font-medium rounded-tr-xl">#</th>
                        <th class="py-4 px-6 font-medium">اسم المعلم</th>
                        <th class="py-4 px-6 font-medium">البريد الإلكتروني</th>
                        <th class="py-4 px-6 font-medium">الفرع</th>
                        <th class="py-4 px-6 font-medium">الأدوار</th>
                        <th class="py-4 px-6 font-medium">الاتصال</th>
                        <th class="py-4 px-6 font-medium">الحالة</th>
                        <th class="py-4 px-6 font-medium rounded-tl-xl"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($teachers as $teacher)
                    @php
                    $isOnline = $teacher->last_seen_at
                    ? \Carbon\Carbon::parse($teacher->last_seen_at)->gt(now()->subMinutes(5))
                    : false;
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-4 px-6 text-gray-400 text-sm font-medium">{{ $teachers->firstItem() + $loop->index }}</td>
                        <td class="py-4 px-6 font-medium text-gray-800">{{ $teacher->name }}</td>
                        <td class="py-4 px-6 text-gray-600 text-sm">{{ $teacher->user_email ?? '—' }}</td>
                        <td class="py-4 px-6 text-sm">
                            @if($teacher->center)
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold">{{ $teacher->center->name }}</span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($teacher->user->roles as $role)
                                <div class="px-2.5 py-1 rounded-md text-xs font-bold inline-flex items-center gap-1.5 transition-all shadow-sm {{ $roleColors[$role->name] ?? 'bg-gray-50 text-gray-600 border border-gray-200' }}">
                                    {{ $role->display_name ?? $role->name }}
                                </div>
                                @empty
                                <span class="text-gray-400 text-xs">—</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full animate-pulse {{ $isOnline ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                                <span class="font-bold text-xs {{ $isOnline ? 'text-emerald-600' : 'text-gray-400' }}">{{ $isOnline ? 'متصل الآن' : 'غير متصل' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            @can('toggle', $teacher)
                            <form method="POST" action="{{ route('teachers.toggle', $teacher) }}"
                                onsubmit="confirmToggleStatus(event, { name: '{{ e($teacher->name) }}', isActive: {{ $teacher->user_status === 'active' ? 'true' : 'false' }} })">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-3 py-1 rounded-full text-xs font-bold transition-all {{ $teacher->user_status === 'active' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $teacher->user_status === 'active' ? 'نشط' : 'موقوف' }}
                                </button>
                            </form>
                            @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $teacher->user_status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $teacher->user_status === 'active' ? 'نشط' : 'موقوف' }}
                            </span>
                            @endcan
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-end gap-3">
                                @can('view', $teacher)
                                <a href="{{ route('teachers.show', $teacher) }}" class="text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 p-1.5 rounded-lg transition" title="عرض التفاصيل">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @endcan
                                @can('update', $teacher)
                                <a href="{{ route('teachers.edit', $teacher) }}" class="text-blue-500 hover:text-blue-700 hover:bg-blue-50 p-1.5 rounded-lg transition" title="تعديل">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan
                                @can('delete', $teacher)
                                <form method="POST" action="{{ route('teachers.destroy', $teacher) }}"
                                    onsubmit="confirmDelete(event, { name: '{{ e($teacher->name) }}', type: 'المعلم' })"
                                    class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-lg transition">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="حذف">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-400 font-medium">
                            @if(request()->anyFilled(['q', 'center_id', 'role', 'status']))
                            لا توجد نتائج مطابقة للفلاتر المحددة.
                            @else
                            لا يوجد معلمون مسجلون حالياً.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- ─── الترقيم الموحّد ─── --}}
            <x-pagination :paginator="$teachers" />
        </div>
    </div>
    <script>
        function confirmToggleStatus(event, {
            name,
            isActive
        }) {
            event.preventDefault();
            const form = event.target;

            const actionText = isActive ? 'إيقاف' : 'تفعيل';
            const actionColor = isActive ? '#ef4444' : '#10b981';

            Swal.fire({
                title: `${actionText} حساب ${name}؟`,
                text: isActive ?
                    'سيتم إيقاف حساب هذا المعلم ولن يتمكن من الدخول للنظام.' :
                    'سيتم إعادة تفعيل حساب هذا المعلم.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: actionColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: actionText,
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

            return false;
        }
    </script>
</x-layouts.markaz-layout>