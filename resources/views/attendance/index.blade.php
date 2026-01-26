<x-layouts.markaz-layout>
    <div class="space-y-6">
        <!-- Header Card -->
        <div class="bg-[#0a4d31] rounded-2xl p-6 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-md gap-4">
            <!-- Mobile Menu Trigger -->
             <button @click="sidebarOpen = true" class="absolute top-4 right-4 md:hidden text-white/80 hover:text-white bg-white/10 p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
             </button>

            <!-- Titles (Right on Desktop) -->
            <div class="text-center md:text-right w-full md:w-auto order-1 md:order-1">
                <h1 class="text-2xl md:text-3xl font-bold mb-1">سجل الحضور والغياب</h1>
                <p class="text-white/80 text-sm">نظام المتابعة الذكي لطلاب الحلقات</p>
            </div>

            <!-- Date Picker (Left on Desktop) -->
            <div class="relative w-full md:w-auto flex justify-center md:justify-end order-2 md:order-2 mt-4 md:mt-0">
                 <div class="flex items-center bg-white/10 rounded-lg p-2 border border-white/20 backdrop-blur-sm">
                    <span class="text-white font-medium ml-3 px-2">01/22/2026</span>
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                 </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Controls Sidebar (Right Side in DOM/RTL) -->
            <div class="space-y-6">
                <!-- Display Options -->
                 <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-[#0a5c36] font-bold mb-4 text-right">خيارات العرض</h3>
                    <div class="space-y-3">
                         <button class="w-full flex items-center justify-between p-3 rounded-lg bg-[#00a884] text-white shadow-sm transition-all">
                             <span class="font-bold">تسجيل الحضور</span>
                             <div class="bg-white/20 p-1.5 rounded-md">
                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                             </div>
                         </button>
                         
                         <button class="w-full flex items-center justify-between p-3 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-all">
                             <span class="font-bold">التقارير</span>
                             <div class="p-1.5 ">
                                 <svg class="w-5 h-5 text-[#00a884]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                             </div>
                         </button>
                    </div>
                 </div>

                 <!-- Current Circle -->
                 <div class="bg-white p-5 rounded-2xl shadow-sm border-t-4 border-yellow-400">
                    <h3 class="text-gray-800 font-bold mb-4 text-right">الحلقة الحالية</h3>
                    <div class="relative">
                        <select class="w-full p-3 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:border-[#00a884] appearance-none bg-transparent font-bold">
                            <option selected>حلقة عمر بن الخطاب</option>
                            <option>حلقة أبي بكر الصديق</option>
                            <option>حلقة عثمان بن عفان</option>
                        </select>
                         <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                 </div>
            </div>

            <!-- Attendance List (Left Side in DOM/RTL) -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border-t-4 border-[#00a884] p-4 md:p-6 min-h-[500px]">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <!-- Title (Right) -->
                    <div class="text-right w-full md:w-auto">
                        <h2 class="text-[#0a5c36] text-xl font-bold">قائمة التحضير</h2>
                        <p class="text-[#00a884] text-sm">إجمالي الطلاب: {{ count($students) }}</p>
                    </div>

                    <!-- Actions (Left) -->
                    <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                             <input type="text" placeholder="بحث عن طالب..." class="w-full pl-8 pr-4 py-2 rounded-full border border-gray-200 focus:border-[#00a884] focus:ring focus:ring-[#00a884]/20 transition-all text-sm">
                             <svg class="w-4 h-4 text-[#00a884] absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <button class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-colors w-full md:w-auto text-center">
                            حفظ السجل
                        </button>
                    </div>
                </div>

                <!-- List Header (Desktop Only) -->
                <div class="hidden md:grid grid-cols-12 gap-4 border-b border-gray-100 pb-3 mb-4 text-sm font-bold text-[#0a5c36]">
                    <div class="col-span-3 text-center">الحالة الحالية</div>
                    <div class="col-span-5 text-center">تسجيل الحالة</div>
                    <div class="col-span-4 text-right pr-4">اسم الطالب</div>
                </div>

                <!-- Students List -->
                <div class="space-y-4">
                    @forelse($students as $student)
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-white hover:bg-gray-50 p-4 md:p-2 rounded-xl transition-colors border-b md:border-b-0 border-gray-50">
                        
                         <!-- Name (Mobile: Top, Desktop: Right) -->
                        <div class="md:col-span-4 text-right md:pr-4 font-bold text-gray-700 md:order-3 order-1">
                            {{ $student['name'] }}
                        </div>

                         <!-- Action Buttons (Mobile: Middle, Desktop: Center) -->
                        <div class="md:col-span-5 flex justify-center gap-3 md:order-2 order-2">
                             <!-- Absent (Red) -->
                            <button class="w-10 h-10 rounded-full border border-red-100 text-red-500 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all bg-white shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                             <!-- Excused (Blue) -->
                            <button class="w-10 h-10 rounded-full border border-blue-100 text-blue-500 hover:bg-blue-50 hover:border-blue-200 flex items-center justify-center transition-all bg-white shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                             <!-- Late (Orange) -->
                            <button class="w-10 h-10 rounded-full border border-orange-100 text-orange-500 hover:bg-orange-50 hover:border-orange-200 flex items-center justify-center transition-all bg-white shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                             <!-- Present (Green) -->
                            <button class="w-10 h-10 rounded-full border border-emerald-100 text-emerald-500 hover:bg-emerald-50 hover:border-emerald-200 flex items-center justify-center transition-all bg-white shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </div>
                        
                         <!-- Status Badge (Mobile: Bottom, Desktop: Left) -->
                        <div class="md:col-span-3 flex justify-center md:order-1 order-3">
                            <span class="px-4 py-1.5 rounded-full bg-gray-100 text-gray-500 text-xs font-bold shadow-sm w-full md:w-auto text-center">
                                لم يتم التسجيل
                            </span>
                        </div>

                    </div>
                    <!-- Separator for mobile optimization -->
                    <!-- <div class="border-b border-gray-50 md:hidden"></div> -->
                    @empty
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center py-20 text-gray-300 col-span-12">
                        <svg class="w-24 h-24 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <p class="text-lg">لا يوجد طلاب مطابقين للبحث</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.markaz-layout>
