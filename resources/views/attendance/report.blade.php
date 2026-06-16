<x-layouts.markaz-layout>
    <div class="space-y-6">
        <!-- Header Card -->
        <div
            class="bg-[#0a4d31] rounded-3xl p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">إحصائيات الحضور والغياب</h1>
                <p class="text-emerald-100/80 text-sm font-medium">تحليل بياني لأداء المركز والحلقات</p>
            </div>

            <div class="flex gap-6 z-10">
                <a href="{{ route('attendance.index') }}"
                    class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl text-white font-bold transition-all flex items-center gap-2">
                    <svg class="w-5 h-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    سجل المتابعة
                </a>
                <a href="{{ route('attendance.create') }}"
                    class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl text-white font-bold transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    تسجيل الغياب
                </a>
            </div>

            <!-- Decorative background element -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Pie Chart: Status Distribution -->
            <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100">
                <h3 class="text-[#0a5c36] font-black text-xl mb-8 border-b border-gray-50 pb-4">توزيع الحالة (آخر 30
                    يوم)</h3>
                <div class="relative h-100">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Line Chart: Daily Presence -->
            <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100">
                <h3 class="text-[#0a5c36] font-black text-xl mb-8 border-b border-gray-50 pb-4">معدل الحضور (آخر 7 أيام)
                </h3>
                <div class="relative h-100">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const statusData = @json($stats);
        const dailyData = @json($dailyStats);

        // Status Distribution Chart
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusData.map(d => {
                    const translations = {
                        'present': 'حاضر',
                        'absent': 'غائب',
                        'late': 'متأخر',
                        'excused': 'بعذر'
                    };
                    return translations[d.status] || d.status;
                }),
                datasets: [{
                    data: statusData.map(d => d.count),
                    backgroundColor: [
                        '#ef4444', // red-500
                        '#3b82f6', // blue-500
                        '#f59e0b', // amber-500
                        '#10b981', // emerald-500
                    ],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            font: {
                                family: 'Cairo',
                                size: 14,
                                weight: 'bold'
                            },
                            padding: 20
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // Daily Presence Chart
        new Chart(document.getElementById('dailyChart'), {
            type: 'line',
            data: {
                labels: dailyData.map(d => d.date),
                datasets: [{
                    label: 'عدد الطلاب الحاضرين',
                    data: dailyData.map(d => d.count),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        rtl: true,
                        grid: {
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: {
                                family: 'Cairo'
                            }
                        }
                    },
                    x: {
                        rtl: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Cairo'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        rtl: true,
                        labels: {
                            font: {
                                family: 'Cairo',
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-layouts.markaz-layout>