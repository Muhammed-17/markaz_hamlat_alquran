<x-layouts.markaz-layout>
    <div class="space-y-8">

        <!-- Header Card -->
        <div
            class="bg-[#0a4d31] rounded-[2.5rem] p-8 lg:p-10 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-2xl gap-6">
            <div class="text-right w-full z-10">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-emerald-400/20 rounded-2xl flex items-center justify-center border border-emerald-400/30">
                        <svg class="w-7 h-7 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-black">المنصة العامة</h1>
                </div>
                <p class="text-emerald-100/70 text-sm font-medium pr-1">تابع أبناءك من مكان واحد — الحضور، الغياب، والاشتراكات المالية</p>
            </div>

            <div class="hidden md:block z-10">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/10 text-center min-w-[160px]">
                    <span class="block text-3xl font-black text-white">{{ $activeChildrenCount }}</span>
                    <span class="text-emerald-200 text-xs font-bold">طالب مقيد</span>
                </div>
            </div>

            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -top-20 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-3xl font-black text-gray-900">{{ $totalChildrenCount }}</span>
                        <span class="text-sm text-gray-500 font-bold">إجمالي الأبناء</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-3xl font-black text-gray-900">{{ $latestAbsences->count() }}</span>
                        <span class="text-sm text-gray-500 font-bold">آخر أيام غياب</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-3xl font-black text-gray-900">{{ $unpaidMonthsTotal }}</span>
                        <span class="text-sm text-gray-500 font-bold">اشتراكات متأخرة (شهر)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Rate per Child -->
        @if ($attendanceStats->isNotEmpty())
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-8 border-b border-gray-50 pb-4">
                    <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">نسبة الحضور الشهرية</h2>
                </div>

                <div class="space-y-6">
                    @foreach ($attendanceStats as $stat)
                        <div class="flex items-center gap-4">
                            <div class="w-32 shrink-0">
                                <a href="{{ route('students.show', $stat['id']) }}" class="font-bold text-gray-700 hover:text-emerald-600 transition">{{ $stat['name'] }}</a>
                            </div>
                            <div class="flex-1">
                                <div class="relative w-full h-4 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-l from-emerald-500 to-teal-400 rounded-full transition-all duration-700"
                                        style="width: {{ $stat['rate'] }}%"></div>
                                </div>
                            </div>
                            <div class="w-16 text-left shrink-0">
                                <span class="font-black text-gray-800">{{ $stat['rate'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Two-column: Absences + Unpaid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Latest Absences -->
            <div class="bg-white rounded-[2.5rem] p-6 lg:p-8 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                    <div class="p-2 bg-amber-50 rounded-xl text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">آخر أيام الغياب</h3>
                </div>

                @if ($latestAbsences->isEmpty())
                    <div class="text-center py-10 text-gray-400 font-medium">
                        <svg class="w-12 h-12 mx-auto mb-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>لا يوجد أيام غياب مسجلة — الحمد لله</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($latestAbsences as $absence)
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-2xl border border-red-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center text-red-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-800">{{ $absence->student->name }}</span>
                                        <span class="text-xs text-gray-500 block">{{ $absence->date }}</span>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-red-600 bg-red-100 px-3 py-1 rounded-full">غائب</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Unpaid Subscriptions -->
            <div class="bg-white rounded-[2.5rem] p-6 lg:p-8 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                    <div class="p-2 bg-rose-50 rounded-xl text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">الاشتراكات غير المدفوعة</h3>
                </div>

                @if ($unpaidSubscriptions->isEmpty())
                    <div class="text-center py-10 text-gray-400 font-medium">
                        <svg class="w-12 h-12 mx-auto mb-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>جميع الاشتراكات مدفوعة بالكامل</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($unpaidSubscriptions as $sub)
                            <div class="flex items-center justify-between p-4 bg-rose-50 rounded-2xl border border-rose-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-800">{{ $sub->student->name }}</span>
                                        <span class="text-xs text-gray-500 block">
                                            {{ $sub->month->locale('ar')->isoFormat('MMMM YYYY') }} — {{ number_format($sub->amount, 2) }} ر.س
                                        </span>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-rose-600 bg-rose-100 px-3 py-1 rounded-full">غير مدفوع</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.markaz-layout>
