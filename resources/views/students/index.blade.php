<x-layouts.markaz-layout>

    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-[#0a5c36]">إدارة الطلاب</h1>
                <p class="text-gray-500 mt-1">{{ count($students) }} طالب مسجل في النظام</p>
            </div>
            @role('admin')
                <a href="{{ route('students.create') }}"
                    class="bg-[#10b981] hover:bg-[#059669] text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <span>إضافة طالب جديد</span>
                </a>
            @endrole
        </div>

        <!-- Search -->
        <div class="bg-white p-4 rounded-xl border border-gray-100 flex gap-4">
            <input type="text" placeholder="بحث باسم الطالب أو المستوى..."
                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#10b981]/50">
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
                    @foreach ($students as $student)
                        <tr class="hover:bg-gray-50/50">

                            <td class="py-4 px-6 font-medium text-gray-800">
                                {{ $student->name }}
                            </td>

                            <td class="py-4 px-6">
                                @if ($student->status === 'active')
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-md text-sm">
                                        مقيد
                                    </span>
                                @elseif ($student->status === 'inactive')
                                    <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-md text-sm">
                                        موقوف
                                    </span>
                                @elseif ($student->status === 'traveler')
                                    <span class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded-md text-sm">
                                        مسافر
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-gray-600">
                                {{ $student->circle->name ?? '—' }}
                            </td>

                            <td class="py-4 px-6 text-gray-600">
                                @if ($student->education_level == 'preschool')
                                    <span
                                        class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-semibold">
                                        حضانة
                                    </span>
                                @elseif ($student->education_level == 'primary')
                                    <span
                                        class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">
                                        ابتدائية
                                    </span>
                                @elseif ($student->education_level == 'secondary')
                                    <span
                                        class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">
                                        إعدادية
                                    </span>
                                @elseif ($student->education_level == 'high_school')
                                    <span
                                        class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
                                        ثانوية
                                    </span>
                                @elseif ($student->education_level == 'university')
                                    <span
                                        class="px-3 py-1 bg-violet-100 text-violet-700 rounded-full text-xs font-semibold">
                                        جامعية
                                    </span>
                                @elseif ($student->education_level == 'other')
                                    <span
                                        class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold">
                                        أخرى
                                    </span>
                                @endif

                            </td>

                            <td class="py-4 px-6 text-gray-600">
                                {{ $student->age }}
                            </td>
                            
                            <!-- Actions -->
                            <td class="py-4 px-6 relative" x-data="{ open: false }">

                                <div class="flex items-center justify-end gap-3">
                                    {{-- button show details --}}
                                    <a href="{{ route('students.show', $student->id) }}"
                                        class="text-green-400 hover:text-green-600 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- button edit --}}
                                    @role('admin')
                                        <a href="{{ route('students.edit', $student) }}"
                                            class="text-blue-500 hover:text-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
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
