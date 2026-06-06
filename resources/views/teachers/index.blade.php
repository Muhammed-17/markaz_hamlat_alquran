<x-layouts.markaz-layout>

    <div class="space-y-6">

        <!-- Header Card -->
        <div
            class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
            <!-- العنوان (على اليمين في الشاشات الكبيرة) -->
            <div class="text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">إدارة المعلمين</h1>
                <p class="text-emerald-100/80 text-sm font-medium">{{ count($teachers) }} معلم مسجل في النظام</p>
            </div>

            <!-- الزر (على اليسار في الشاشات الكبيرة) -->
            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                @role('admin')
                    <a href="{{ route('teachers.create') }}"
                        class="w-full md:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        جديد
                    </a>
                @endrole
            </div>

            <!-- Decorative Element -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-x-auto">
            <table class="w-full text-right min-w-[800px]">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="py-4 px-6 font-medium rounded-tr-xl">اسم المعلم</th>
                        <th class="py-4 px-6 font-medium">البريد الإلكتروني</th>
                        <th class="py-4 px-6 font-medium">الصلاحية</th>
                        <th class="py-4 px-6 font-medium">الحالة</th>
                        <th class="py-4 px-6 font-medium rounded-tl-xl"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($teachers as $teacher)
                        <tr class="hover:bg-gray-50/50">

                            <!-- Name -->
                            <td class="py-4 px-6 font-medium text-gray-800">
                                {{ $teacher->name }}
                            </td>

                            <!-- Email -->
                            <td class="py-4 px-6 text-gray-600">
                                {{ $teacher->user->email ?? '—' }}
                            </td>

                            <!-- Role -->
                            <td class="py-4 px-6 text-gray-600">
                                @if ($teacher->user->getRoleNames()->first() === 'teacher')
                                    <span
                                        class="bg-red-50 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">معلم</span>
                                @elseif ($teacher->user->getRoleNames()->first() === 'supervisor')
                                    <span
                                        class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">مشرف</span>
                                @else
                                    <span
                                        class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold">—</span>
                                @endif
                            </td>

                            <!-- Status Switch -->
                            <td class="py-4 px-6">
                                @role('admin')
                                    <form action="{{ route('teachers.toggle', $teacher) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <label class="inline-flex items-center cursor-pointer relative">
                                            <input type="checkbox" onchange="this.form.submit()" class="sr-only peer"
                                                {{ $teacher->user->status === 'active' ? 'checked' : '' }}>

                                            <!-- الخلفية -->
                                            <div
                                                class="w-11 h-6 bg-gray-200 rounded-full transition peer-checked:bg-emerald-500">
                                            </div>

                                            <!-- الدائرة -->
                                            <span
                                                class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></span>
                                        </label>

                                    </form>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $teacher->user->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $teacher->user->status === 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                @endrole
                            </td>


                            <!-- Actions -->
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-end gap-3">

                                    <!-- Edit -->
                                    @role('admin')
                                        <a href="{{ route('teachers.edit', $teacher) }}"
                                            class="text-blue-500 hover:text-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endrole
                                    {{-- button delete --}}
                                    @role('admin')
                                        <form method="POST" action="{{ route('teachers.destroy', $teacher) }}"
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
                                    @endrole

                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

</x-layouts.markaz-layout>
