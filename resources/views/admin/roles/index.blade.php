@php
$groupLabels = [
'system' => '⚙️ النظام العام',
'users' => '👥 المستخدمون والأدوار',
'centers' => '🏢 الفروع والمراكز',
'teachers' => '👨‍🏫 المعلمون',
'circles' => '📚 الحلقات',
'students' => '🎓 الطلاب',
'attendance' => '📋 الحضور والغياب',
'subscriptions' => '💳 الاشتراكات والأسعار',
];
@endphp
<x-layouts.markaz-layout>
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Section A: Create New Role --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-xl font-bold text-[#0a5c36] mb-6">إضافة دور جديد</h2>

            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            اسم الدور <span class="text-xs text-gray-400 font-normal">(بالإنجليزية للنظام)</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full rounded-2xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all ltr"
                            placeholder="مثال: teacher, supervisor, admin">
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            الاسم بالعربية <span class="text-xs text-gray-400 font-normal">(للعرض)</span>
                        </label>
                        <input type="text" name="display_name" value="{{ old('display_name') }}"
                            class="w-full rounded-2xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#0a5c36] focus:border-[#0a5c36] transition-all"
                            placeholder="مثال: معلم، مشرف، مدير">
                        @error('display_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- الصلاحيات مجمعة بالـ group --}}
                <p class="text-sm font-bold text-gray-700 mb-3">الصلاحيات</p>
                <div class="space-y-4 mb-6">
                    @foreach($permissions as $group => $groupPermissions)
                    <div class="border border-gray-100 rounded-2xl p-4">
                        <p class="text-xs font-black text-emerald-700 mb-3">
                            {{ $groupLabels[$group] ?? $group }}
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($groupPermissions as $permission)
                            <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    class="rounded border-gray-300 text-[#0a5c36] focus:ring-[#0a5c36]"
                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">
                                    {{ $permission->display_name ?? $permission->name }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="submit"
                    class="bg-[#0a5c36] hover:bg-[#084d2d] text-white font-bold rounded-2xl shadow-sm transition-all text-sm px-5 py-2.5">
                    حفظ الدور
                </button>
            </form>
        </div>

        {{-- Section B: Existing Roles --}}
        <div class="space-y-6 mt-8">
            @foreach($roles ?? [] as $role)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">

                <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-50">
                    <h3 class="text-base font-black text-gray-800">
                        صلاحيات دور:
                        <span class="text-emerald-600">{{ $role->display_name ?? $role->name }}</span>
                        @if($role->display_name && $role->display_name !== $role->name)
                        <span class="text-xs text-gray-400 font-normal">({{ $role->name }})</span>
                        @endif
                    </h3>
                    <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                        {{ $role->permissions->count() }} صلاحية نشطة
                    </span>
                </div>

                <form action="{{ route('admin.roles.permissions.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- الصلاحيات مجمعة بالـ group --}}
                    <div class="space-y-4 mb-6">
                        @foreach($permissions as $group => $groupPermissions)
                        <div class="border border-gray-100 rounded-2xl p-4">
                            <p class="text-xs font-black text-emerald-700 mb-3">
                                {{ $groupLabels[$group] ?? $group }}
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($groupPermissions as $permission)
                                @php
                                $isLocked = $permission->name === 'manage roles' && $role->name === 'admin';
                                @endphp

                                @if($isLocked)
                                <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                                @else
                                <label class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-emerald-50/50 rounded-xl cursor-pointer transition-all select-none border border-transparent hover:border-emerald-100">
                                    <input type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                        @checked(in_array($permission->id, $role->permissions->pluck('id')->toArray()))
                                    class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded-lg">
                                    <span class="text-sm text-gray-700 font-medium">
                                        {{ $permission->display_name ?? $permission->name }}
                                    </span>
                                </label>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                        <button type="submit"
                            class="flex items-center gap-2 px-6 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d] text-white font-bold rounded-xl shadow-md transition-all text-xs">
                            ✓ حفظ وتحديث صلاحيات {{ $role->display_name ?? $role->name }}
                        </button>

                        @if(in_array($role->name, ['admin','general_manager','manager', 'supervisor', 'teacher', 'guardian']))
                        <span class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-400 rounded-xl text-xs font-bold">
                            🔒 دور أساسي محمي
                        </span>
                        @else
                        <button type="button"
                            onclick="confirmDelete({{ $role->id }}, '{{ $role->display_name ?? $role->name }}')"
                            class="flex items-center gap-2 px-6 py-2.5 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 hover:border-red-500 font-bold rounded-xl transition-all text-xs">
                            🗑 حذف الدور
                        </button>
                        @endif
                    </div>
                </form>

                @if(!in_array($role->name, ['admin','general_manager','manager', 'supervisor', 'teacher', 'guardian']))
                <form id="delete-form-{{ $role->id }}"
                    action="{{ route('admin.roles.destroy', $role->id) }}"
                    method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                @endif

            </div>
            @endforeach
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(roleId, roleName) {
            Swal.fire({
                title: 'حذف دور: ' + roleName,
                text: 'سيتم حذف الدور وجميع صلاحياته. لن تتمكن من التراجع!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'نعم، احذف الدور',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl font-bold',
                    confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
                    cancelButton: 'rounded-xl px-6 py-2.5 text-sm',
                }
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + roleId).submit();
                }
            });
        }

        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح',
            text: @json(session('success')),
            confirmButtonColor: '#0a5c36',
            confirmButtonText: 'حسناً',
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-3xl font-bold',
                confirmButton: 'rounded-xl px-6 py-2.5 text-sm'
            }
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: @json(session('error')),
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'حسناً',
            customClass: {
                popup: 'rounded-3xl font-bold',
                confirmButton: 'rounded-xl px-6 py-2.5 text-sm'
            }
        });
        @endif
    </script>
    @endpush
</x-layouts.markaz-layout>