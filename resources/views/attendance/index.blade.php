<x-layouts.markaz-layout>
    <div class="space-y-6">
        <!-- Header Card -->
        <div
            class="bg-[#0a4d31] rounded-3xl p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">سجل المتابعة التاريخي</h1>
                <p class="text-emerald-100/80 text-sm font-medium">عرض ومراجعة كافة سجلات الحضور والغياب</p>
            </div>

            <div class="flex gap-4 z-10">
                <a href="{{ route('attendance.create') }}"
                    class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl text-white font-bold transition-all">
                    تسجيل الحضور
                </a>
                <a href="{{ route('attendance.report') }}"
                    class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl text-white font-bold transition-all">
                    عرض الإحصائيات
                </a>
            </div>

            <!-- Decorative background element -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white p-6 rounded-[30px] shadow-sm border border-gray-100 transition-all">
            <form action="{{ route('attendance.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 px-2">الحلقة</label>
                    <select name="circle_id"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold text-gray-700">
                        <option value="">كل الحلقات</option>
                        @foreach ($circles as $circle)
                            <option value="{{ $circle->id }}"
                                {{ $selectedCircleId == $circle->id ? 'selected' : '' }}>
                                {{ $circle->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 px-2">المسجل</label>
                    <select name="user_id"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold text-gray-700">
                        <option value="">كل المسجلين</option>
                        @foreach ($registrars as $registrar)
                            <option value="{{ $registrar->id }}"
                                {{ $selectedRegistrarId == $registrar->id ? 'selected' : '' }}>
                                {{ $registrar->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 px-2">من تاريخ</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold text-gray-700">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 px-2">إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold text-gray-700">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 px-2">ترتيب التاريخ</label>
                    <select name="sort_order"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold text-gray-700">
                        <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>الأحدث أولاً</option>
                        <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>الأقدم أولاً</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-[#0a5c36] text-white rounded-2xl p-4 font-black hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-emerald-900/10">
                        تحديث النتائج
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 border-b border-gray-100 font-black text-gray-400 text-sm uppercase">
                        <tr>
                            <th class="px-8 py-5 text-center">#</th>
                            <th class="px-8 py-5">تاريخ</th>
                            <th class="px-8 py-5">اسم الطالب</th>
                            <th class="px-8 py-5">الحلقة</th>
                            <th class="px-8 py-5">الحالة</th>
                            <th class="px-8 py-5">المسجل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($records as $record)
                            <tr class="hover:bg-emerald-50/30 transition-all group">
                                <td class="px-8 py-6 font-bold text-gray-500 text-center">
                                    {{ ($records->currentPage() - 1) * $records->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-8 py-6 font-bold text-gray-700">{{ $record->date }}</td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 group-hover:bg-white group-hover:text-emerald-500 shadow-sm transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $record->student->name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        class="px-4 py-2 bg-gray-100 rounded-xl text-gray-600 font-bold text-xs uppercase">{{ $record->student->circle->name ?? '-' }}</span>
                                </td>
                                <td class="px-8 py-6 font-black">
                                    @if ($record->status === 'present')
                                        <span class="text-emerald-500 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            حاضر
                                        </span>
                                    @elseif($record->status === 'absent')
                                        <span class="text-red-500 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                            غائب
                                        </span>
                                    @elseif($record->status === 'late')
                                        <span class="text-amber-500 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            متأخر
                                        </span>
                                    @else
                                        <span class="text-blue-500 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            بعذر
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-gray-500 font-medium">{{ $record->user->name ?? ($record->student->circle->mainTeacher->first()?->name ?? 'بواسطة المعلم') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center text-gray-400 font-medium">
                                    لا توجد سجلات مطابقة لهذه الفلاتر.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($records->hasPages())
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.markaz-layout>
