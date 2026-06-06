<x-layouts.markaz-layout>
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Section A: Create New Role --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <h2 class="text-xl font-bold text-[#0a5c36] mb-6">إضافة دور جديد</h2>

            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <div class="mb-6">
                    <label for="role-name" class="block text-sm font-bold text-gray-700 mb-2">اسم الدور</label>
                    <input id="role-name" type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-2xl border-gray-200 p-3 text-sm focus:outline-none focus:ring-1 transition-all"
                        placeholder="مثال: مدير، مشرف، معلم">
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-sm font-bold text-gray-700 mb-3">الصلاحيات</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    @foreach($permissions as $permission)
                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                            class="rounded border-gray-300 text-[#0a5c36] focus:ring-[#0a5c36]"
                            {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">{{ $permission->display_name ?? $permission->name }}</span>
                    </label>
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
                        صلاحيات دور: <span class="text-emerald-600">{{ $role->name }}</span>
                    </h3>
                    <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                        {{ $role->permissions->count() }} صلاحية نشطة
                    </span>
                </div>

                <form action="{{ route('admin.roles.permissions.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                        @foreach($permissions ?? [] as $permission)
                        @php
                            $isManageRoles = $permission->name === 'manage roles' || $permission->display_name === 'manage roles';
                            $isAdminRole   = $role->name === 'admin';
                        @endphp

                        @if($isAdminRole && $isManageRoles)
                            {{-- مخفية دائماً ومفعّلة لدور admin، لا يمكن إلغاؤها --}}
                            <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                        @else
                        <label class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-emerald-50/50 rounded-xl cursor-pointer transition-all select-none border border-transparent hover:border-emerald-100">
                            <input type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                @checked(in_array($permission->id, $role->permissions->pluck('id')->toArray()))
                            class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded-lg">
                            <span class="text-sm text-gray-700 font-medium">{{ $permission->display_name ?? $permission->name }}</span>
                        </label>
                        @endif
                        @endforeach
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-50">
                        <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d] text-white font-bold rounded-xl shadow-md transition-all text-xs">
                            ✓ حفظ وتحديث صلاحيات {{ $role->name }}
                        </button>
                    </div>
                </form>

            </div>
            @endforeach
        </div>

    </div>
</x-layouts.markaz-layout>