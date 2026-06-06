<x-layouts.markaz-layout>
    <div class="space-y-6">
        <!-- Header Card -->
        <div
            class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">
            <div class="order-2 md:order-1 w-full md:w-auto z-10">
                <form action="{{ route('subscriptions.late_and_unpaid') }}" method="GET"
                    class="flex flex-wrap items-end gap-3">
                    <div class="w-full md:w-48">
                        <label class="block text-xs font-bold text-emerald-100/80 mb-1 px-1">تصفية حسب الحلقة</label>
                        <select name="circle_id"
                            class="w-full bg-white/10 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:bg-white/20 focus:border-emerald-400 focus:ring-0 transition-colors cursor-pointer appearance-none font-bold">
                            <option value="all" class="text-gray-800 bg-white">جميع الحلقات</option>
                            @foreach ($circles as $circle)
                                <option value="{{ $circle->id }}"
                                    {{ $selectedCircleId == $circle->id ? 'selected' : '' }}
                                    class="text-gray-800 bg-white">
                                    {{ $circle->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        عرض
                    </button>
                    <a href="{{ route('subscriptions.index') }}"
                        class="px-6 py-2.5 bg-white/10 hover:bg-white/20 border border-white/10 text-white font-bold rounded-xl transition-all flex items-center justify-center">
                        عودة
                    </a>
                </form>
            </div>

            <div class="order-1 md:order-2 text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">الطلاب المتعثرين</h1>
                <p class="text-emerald-100/80 text-sm font-medium">قائمة بالطلاب المتأخرين عن سداد الاشتراكات</p>
            </div>

            <!-- Decorative Element -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-50 text-gray-500 text-sm">
                    <tr>
                        <th class="py-4 px-6 font-medium">اسم الطالب</th>
                        <th class="py-4 px-6 font-medium">الحلقة</th>
                        <th class="py-4 px-6 font-medium">عدد الأشهر المتأخرة</th>
                        <th class="py-4 px-6 font-medium">الأشهر</th>
                        <th class="py-4 px-6 font-medium">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-4 px-6 font-bold text-gray-800">{{ $student->name }}</td>
                            <td class="py-4 px-6 text-gray-600">{{ $student->circle?->name ?? '-' }}</td>
                            <td class="py-4 px-6">
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-bold">
                                    {{ $student->unpaid_months_count }} أشهر
                                </span>
                            </td>
                            <td class="py-4 px-6 text-gray-500 text-sm max-w-sm">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($student->unpaid_months_list as $month)
                                        <span class="bg-gray-100 border border-gray-200 px-2 py-0.5 rounded text-xs">
                                            {{ $month }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ route('subscriptions.create') }}?student_id={{ $student->id }}&circle_id={{ $student->circle_id }}&month={{ \Carbon\Carbon::now()->format('Y-m') }}"
                                    class="text-[#10b981] hover:text-[#059669] font-medium text-sm border border-[#10b981] px-3 py-1 rounded-lg hover:bg-emerald-50 transition">
                                    تسجيل سداد
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>جميع الطلاب ملتزمون بالسداد!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.markaz-layout>
