<x-layouts.markaz-layout>
    <div class="space-y-6" x-data="{
        allPresent() {
            $dispatch('mark-all-present');
        }
    }">
        <!-- Header Card -->
        <div
            class="bg-[#0a4d31] rounded-3xl p-8 text-white relative overflow-visible flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            {{-- class="bg-[#0a4d31] rounded-3xl p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6"> --}}
            <div class="text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">تسجيل الحضور والغياب</h1>
                <p class="text-emerald-100/80 text-sm font-medium">متابعة دقيقة لحلقات مركز حملة القرآن</p>
                <div class="flex gap-3 mt-4">
                    <a href="{{ route('attendance.index') }}"
                        class="text-xs font-bold px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        الإحصائيات
                    </a>
                    <a href="{{ route('attendance.report') }}"
                        class="text-xs font-bold px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        التقرير العام
                    </a>
                </div>
            </div>

            <!-- Filters Form -->
            <form action="{{ route('attendance.create') }}" method="GET" id="filterForm"
                class="flex flex-col md:flex-row gap-4 w-full md:w-auto z-10">
                <!-- Date Picker -->
                <div class="relative group">
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                        class="bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl px-5 py-3 text-white focus:outline-none focus:ring-4 focus:ring-emerald-400/30 transition-all font-bold group-hover:border-emerald-400/50">
                </div>

                <!-- Circle Selection -->
                {{-- <div class="relative group">
                    <select name="circle_id" onchange="this.form.submit()"
                        class="bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl px-5 py-3 pr-10 text-white focus:outline-none focus:ring-4 focus:ring-emerald-400/30 transition-all font-bold appearance-none group-hover:border-emerald-400/50 min-w-[200px]">
                        <option value="">اختر الحلقة...</option>
                        @foreach ($circles as $circle)
                            <option value="{{ $circle->id }}" {{ $selectedCircleId == $circle->id ? 'selected' : '' }}
                                class="text-gray-800">
                                {{ $circle->name }} ({{ $circle->level }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute left-4 inset-y-0 flex items-center pointer-events-none text-emerald-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                </div> --}}
                <!-- مكوّن اختيار الحلقة -->
                <div x-data="{ open: false, selected: '{{ $circles->firstWhere('id', $selectedCircleId)->name ?? 'اختر الحلقة...' }}' }" class="relative w-full md:w-auto z-50">

                    <!-- زر القائمة -->
                    <button @click="open = !open" type="button"
                        class="flex items-center justify-between w-full bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl px-5 py-3 text-white focus:outline-none focus:ring-4 focus:ring-emerald-400/30 transition-all font-bold group-hover:border-emerald-400/50 min-w-[220px]">
                        <span x-text="selected"></span>
                        <svg class="w-5 h-5 text-emerald-300 transition-transform duration-300"
                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <!-- القائمة المنسدلة -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2" @click.away="open = false"
                        class="absolute right-0 mt-2 w-full bg-white text-gray-800 rounded-2xl shadow-2xl overflow-hidden border border-gray-100 origin-top transition-all z-50">

                        @foreach ($circles as $circle)
                            <a href="{{ route('attendance.create', ['circle_id' => $circle->id, 'date' => $date]) }}"
                                @click="selected = '{{ $circle->name }} ({{ $circle->level }})'; open = false;"
                                class="flex items-center justify-between px-5 py-3 hover:bg-emerald-50 transition text-right">
                                <span>{{ $circle->name }} ({{ $circle->level }})</span>

                                @if ($selectedCircleId == $circle->id)
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

            </form>

            <!-- Decorative background element -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        @if ($selectedCircleId)
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Info Sidebar -->
                    <div class="space-y-6 lg:order-2">
                        <div
                            class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center">
                            <div
                                class="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center text-[#0a5c36] mb-4">
                                <span class="text-3xl font-black">{{ count($students) }}</span>
                            </div>
                            <h3 class="text-gray-800 font-bold">إجمالي الطلاب</h3>
                            <p class="text-gray-400 text-xs mt-1">المقيدين في هذه الحلقة</p>
                        </div>

                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 sticky top-6">
                            <h3 class="text-[#0a5c36] font-bold mb-6 text-right pb-3 border-b border-gray-50">إجراءات
                                سريعة</h3>
                            <div class="space-y-3">
                                <button type="button" @click="allPresent()"
                                    class="w-full flex items-center justify-between p-4 rounded-2xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-all font-bold">
                                    <span>تحضير الكل حاضر</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                                <button type="submit"
                                    class="w-full mt-4 flex items-center justify-center gap-2 p-5 rounded-2xl bg-[#0a5c36] text-white shadow-lg shadow-emerald-900/10 hover:scale-[1.02] active:scale-95 transition-all font-black text-lg">
                                    <span>حفظ السجل</span>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Students Main List -->
                    <div class="lg:col-span-3 space-y-4 lg:order-1">
                        @forelse($students as $index => $student)
                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row items-center gap-6 transition-all hover:border-emerald-100 group"
                                x-data="{ status: '{{ $attendanceData[$student->id]->status ?? 'not_recorded' }}' }" @mark-all-present.window="status = 'present'">

                                <!-- Student ID & Avatar -->
                                <div class="flex items-center gap-4 flex-1 w-full">
                                    <div
                                        class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-emerald-50 group-hover:text-emerald-500 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="text-right">
                                        <h4 class="text-lg font-bold text-gray-800">{{ $student->name }}</h4>
                                        <p class="text-gray-400 text-xs">رقم الطالب: #{{ $student->id }}</p>
                                    </div>
                                </div>

                                <!-- Status Controls -->
                                <div class="flex flex-wrap justify-center gap-2">
                                    <input type="hidden" name="attendance[{{ $index }}][student_id]"
                                        value="{{ $student->id }}">
                                    <input type="hidden" name="attendance[{{ $index }}][status]"
                                        :value="status">

                                    <!-- Present -->
                                    <button type="button" @click="status = 'present'"
                                        :class="status === 'present' ? 'bg-emerald-500 text-white border-emerald-500' :
                                            'bg-white text-gray-400 border-gray-100 hover:border-emerald-200'"
                                        class="flex items-center gap-2 px-4 py-2 rounded-xl border-2 transition-all font-bold text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>حاضر</span>
                                    </button>

                                    <!-- Late -->
                                    <button type="button" @click="status = 'late'"
                                        :class="status === 'late' ? 'bg-amber-500 text-white border-amber-500' :
                                            'bg-white text-gray-400 border-gray-100 hover:border-amber-200'"
                                        class="flex items-center gap-2 px-4 py-2 rounded-xl border-2 transition-all font-bold text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>متأخر</span>
                                    </button>

                                    <!-- Absent -->
                                    <button type="button" @click="status = 'absent'"
                                        :class="status === 'absent' ? 'bg-red-500 text-white border-red-500' :
                                            'bg-white text-gray-400 border-gray-100 hover:border-red-200'"
                                        class="flex items-center gap-2 px-4 py-2 rounded-xl border-2 transition-all font-bold text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>غائب</span>
                                    </button>

                                    <!-- Excused -->
                                    <button type="button" @click="status = 'excused'"
                                        :class="status === 'excused' ? 'bg-blue-500 text-white border-blue-500' :
                                            'bg-white text-gray-400 border-gray-100 hover:border-blue-200'"
                                        class="flex items-center gap-2 px-4 py-2 rounded-xl border-2 transition-all font-bold text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>بعذر</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-3xl p-20 text-center border border-dashed border-gray-200">
                                <div
                                    class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-xl font-bold text-gray-800 mb-2">لا يوجد طلاب في هذه الحلقة</h4>
                                <p class="text-gray-400">يرجى إضافة طلاب للحلقة أولاً للتمكن من تسجيل حضورهم</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </form>
        @else
            <div class="bg-white rounded-3xl p-20 text-center border border-dashed border-gray-200 shadow-sm">
                <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-2">يرجى اختيار الحلقة لعرض سجل الحضور</h4>
                <p class="text-gray-400">يمكنك اختيار الحلقة والتاريخ من الشريط العلوي</p>
            </div>
        @endif
    </div>
</x-layouts.markaz-layout>
