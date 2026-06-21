<x-layouts.markaz-layout>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">مرحباً، {{ Auth::user()->name }}</h1>
            <p class="text-gray-600">
                @if (Auth::user()->hasAnyRole(['admin', 'supervisor']))
                    مرحباً بك في لوحة التحكم الرئيسية. لديك الصلاحيات الكاملة لإدارة المركز.
                @elseif(Auth::user()->hasRole('teacher'))
                    مرحباً بك في لوحة المعلم. يمكنك متابعة حلقاتك وطلابك من هنا.
                @else
                    مرحباً بك في بوابة المركز.
                @endif
            </p>
        </div>
        <div
            class="bg-[#e0f2f1] text-[#00695c] px-4 py-2 rounded-lg font-medium flex items-center gap-2 self-start md:self-auto">
            <span>{{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        @if (Auth::user()->hasAnyRole(['admin', 'supervisor']))
            <!-- Card 1: Attendance (Admin) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-blue-500 relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-gray-600 font-medium">نسبة الحضور اليوم</h3>
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['attendance_rate'] ?? '0' }}%</span>
                </div>
                <p class="text-sm text-blue-500 font-medium mt-1">إحصائية تقديرية</p>
            </div>

            <!-- Card 2: Monthly Collection (Admin) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-emerald-500 relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-gray-600 font-medium">التحصيل هذا الشهر</h3>
                    <div class="bg-emerald-50 p-2 rounded-lg text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <span
                        class="text-3xl font-bold text-gray-800">{{ number_format($stats['monthly_revenue'] ?? 0) }}</span>
                    <span class="text-sm text-gray-500 mb-1">ر.س</span>
                </div>
                <p class="text-sm text-emerald-500 font-medium mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span>{{ $stats['revenue_growth'] ?? 0 }}% عن الشهر الماضي</span>
                </p>
            </div>

            <!-- Card 3: Circles (Admin) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-yellow-500 relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-gray-600 font-medium">عدد الحلقات</h3>
                    <div class="bg-yellow-50 p-2 rounded-lg text-yellow-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['circles_count'] ?? 0 }}</span>
                </div>
                <p class="text-sm text-gray-500 font-medium mt-1">حلقة نشطة</p>
            </div>

            <!-- Card 4: Students (Admin) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-teal-500 relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-gray-600 font-medium">الطلاب المسجلين</h3>
                    <div class="bg-teal-50 p-2 rounded-lg text-teal-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['students_count'] ?? 0 }}</span>
                </div>
                <p class="text-sm text-gray-500 font-medium mt-1">بإشراف {{ $stats['teachers_count'] ?? 0 }} معلم</p>
            </div>
        @elseif(Auth::user()->hasRole('teacher'))
            <!-- Teacher Stats -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-yellow-500 relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-gray-600 font-medium">حلقاتي</h3>
                    <div class="bg-yellow-50 p-2 rounded-lg text-yellow-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['my_circles_count'] ?? 0 }}</span>
                </div>
                <p class="text-sm text-gray-500 font-medium mt-1">حلقات مسندة إليك</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-teal-500 relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-gray-600 font-medium">طلابي</h3>
                    <div class="bg-teal-50 p-2 rounded-lg text-teal-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['my_students_count'] ?? 0 }}</span>
                </div>
                <p class="text-sm text-gray-500 font-medium mt-1">طالب في حلقاتك</p>
            </div>
        @elseif(Auth::user()->hasRole('guardian'))
            <!-- Guardian Stats -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-teal-500 relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-gray-600 font-medium">الأبناء</h3>
                    <div class="bg-teal-50 p-2 rounded-lg text-teal-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['my_children_count'] ?? 0 }}</span>
                </div>
                <p class="text-sm text-gray-500 font-medium mt-1">ابن مسجل</p>
            </div>
        @endif

    </div>

    @if (Auth::user()->hasAnyRole(['admin', 'supervisor']))
        <!-- Charts & Lists Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Chart: Student Growth Trend -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-700">معدل نمو الطلاب (آخر ٦ أشهر)</h3>
                    <span
                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-bold ltr:flex-row-reverse flex items-center gap-1">
                        <span dir="ltr">+{{ $stats['student_growth_percentage'] ?? 0 }}%</span>
                        <span>زيادة</span>
                    </span>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            <!-- Chart: Student Status Distribution -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-1">
                <h3 class="text-lg font-bold text-gray-700 mb-4">توزيع حالات الطلاب</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-8">
            <!-- Absent Students -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-red-500">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-700">الطلاب الأكثر غياباً</h3>
                    <a href="{{ route('attendance.sequential-absences') }}"
                        class="text-sm text-blue-600 hover:text-blue-800 transition">عرض المزيد</a>
                </div>
                <div class="space-y-3">
                    @forelse($absentStudents ?? [] as $student)
                        <div class="flex justify-between items-center border-b border-gray-50 pb-2 last:border-0 ">
                            <div>
                                <p class="font-medium text-gray-800">{{ $student->name }}</p>   
                                <p class="text-xs text-gray-500">{{ $student->circle?->name ?? 'بدون حلقة' }}</p>
                            </div>
                            <span
                                class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">{{ $student->absence_days }}
                                أيام</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">لا يوجد طلاب متغيبين بكثرة مؤخراً.</p>
                    @endforelse
                </div>
            </div>

            <!-- Unpaid Students -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-r-4 border-orange-500">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-700">طلاب عليهم متأخرات</h3>
                    <a href="{{ route('subscriptions.late_and_unpaid') }}"
                        class="text-sm text-blue-600 hover:text-blue-800 transition">عرض التقرير المفصل</a>
                </div>
                <div class="space-y-3">
                    @forelse($unpaidStudents ?? [] as $student)
                        <div class="flex justify-between items-center border-b border-gray-50 pb-2 last:border-0">
                            <div>
                                <p class="font-medium text-gray-800">{{ $student->name }}</p>
                                <p class="text-xs text-gray-500">{{ $student->circle?->name ?? 'بدون حلقة' }}</p>
                            </div>
                            <span class="bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded-full">
                                {{ $student->unpaid_months_count }} شهر متأخر
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">جميع الطلاب سددوا الاشتراكات بالكامل.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Chart Script -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const canvas = document.getElementById('growthChart');
                if (canvas) {
                    const ctx = canvas.getContext('2d');

                    // Create gradient
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(13, 148, 136, 0.2)'); // Teal
                    gradient.addColorStop(1, 'rgba(13, 148, 136, 0.0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($chartData['labels'] ?? []),
                            datasets: [{
                                label: 'إجمالي الطلاب',
                                data: @json($chartData['data'] ?? []),
                                backgroundColor: gradient,
                                borderColor: '#0d9488', // Teal-600
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4, // Smooth curve
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#0d9488',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    rtl: true,
                                    titleAlign: 'right',
                                    bodyAlign: 'right'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    grid: {
                                        borderDash: [2, 2]
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                        }
                    });
                }
                // Status Distribution Chart
                const statusCanvas = document.getElementById('statusChart');
                if (statusCanvas) {
                    const ctxStatus = statusCanvas.getContext('2d');
                    new Chart(ctxStatus, {
                        type: 'doughnut',
                        data: {
                            labels: @json($statusChartData['labels'] ?? []),
                            datasets: [{
                                data: @json($statusChartData['data'] ?? []),
                                backgroundColor: [
                                    'rgba(239, 68, 68, 0.8)', // Active - Green
                                    'rgba(16, 185, 129, 0.8)', // Inactive - Red
                                    'rgba(245, 158, 11, 0.8)', // Traveler - Orange
                                    'rgba(107, 114, 128, 0.8)' // Other - Gray
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            family: 'Cairo'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif


    <!-- Bottom Section - Placeholder for general use -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Notifications (1/3) -->
        <div class="lg:col-span-1 space-y-4">
            <h3 class="text-lg font-bold text-gray-700 mb-4">تنبيهات هامة</h3>

            <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-start gap-3">
                <div class="mt-1 text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-blue-700">مرحباً بك في النظام</h4>
                    <p class="text-sm text-blue-600 mt-1">نتمنى لك تجربة ممتعة ومفيدة في استخدام نظام المركز.</p>
                </div>
            </div>
        </div>

        <!-- Performance Overview (2/3) -->
        <div class="lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-700">الوصول السريع</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4">
                @if (Auth::user()->hasAnyRole(['admin', 'supervisor']))
                    <a href="{{ route('students.index') }}"
                        class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">الطلاب</a>
                    <a href="{{ route('circles.index') }}"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">الحلقات</a>
                    <a href="{{ route('attendance.index') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">الحضور</a>
                @endif
            </div>
        </div>
    </div>
</x-layouts.markaz-layout>
