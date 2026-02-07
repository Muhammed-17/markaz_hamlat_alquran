<x-layouts.markaz-layout>
    <div class="space-y-6">
        <!-- Header Card -->
        <div
            class="bg-[#0b3d2c] rounded-3xl p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="text-right w-full md:w-auto z-10 text-wrap">
                <h1 class="text-3xl font-black mb-2">إدارة اشتراكات الطلاب</h1>
                <p class="text-emerald-100/80 text-sm font-medium">متابعة التحصيل المالي والإحصائيات</p>
            </div>

            <!-- Filters -->
            <form action="{{ route('subscriptions.index') }}" method="GET"
                class="flex flex-wrap gap-4 z-10 w-full md:justify-end">
                <div class="flex flex-col gap-1 min-w-[150px]">
                    <label class="text-[10px] font-bold text-emerald-200/60 mr-2 uppercase">عرض الحلقة</label>
                    <select name="circle_id" onchange="this.form.submit()"
                        class="bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                        @role(['admin', 'supervisor'])
                            <option value="" class="text-gray-800">كل الحلقات</option>
                        @endrole
                        @foreach ($circles as $circle)
                            <option value="{{ $circle->id }}" class="text-gray-800"
                                {{ $selectedCircleId == $circle->id ? 'selected' : '' }}>
                                {{ $circle->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1 min-w-[150px]">
                    <label class="text-[10px] font-bold text-emerald-200/60 mr-2 uppercase">الشهر</label>
                    <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()"
                        class="bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                </div>

                <div class="flex items-end">
                    <a href="{{ route('subscriptions.create') }}"
                        class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 rounded-xl text-white font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-900/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        جديد
                    </a>
                </div>
            </form>

            <!-- Decorative background element -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-emerald-100 transition-all">
                <div
                    class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-bold">تحصيل شهر
                        {{ Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}</p>
                    <h3 class="text-2xl font-black text-gray-800">
                        {{ number_format($statusStats->where('status', 'مدفوع')->first()?->total_amount ?? 0, 2) }}
                        <span class="text-sm font-medium text-gray-400">ج.م</span>
                    </h3>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-blue-100 transition-all">
                <div
                    class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-bold">نسبة السداد</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $paymentRate }}%</h3>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-amber-100 transition-all">
                <div
                    class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-bold">إجمالي المبالغ المستحقة (غير محصلة)</p>
                    <h3 class="text-2xl font-black text-gray-800">
                        {{ number_format($unpaidAmount ?? 0, 2) }}
                        <span class="text-sm font-medium text-gray-400">ج.م</span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Chart: Monthly Revenue -->
            <div class="lg:col-span-2 bg-white p-8 rounded-[40px] shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8 border-b border-gray-50 pb-4">
                    <h3 class="text-[#0a5c36] font-black text-xl">نمو الإيرادات الشهري</h3>
                    <span class="text-gray-400 text-sm font-bold">آخر 6 أشهر</span>
                </div>
                <div class="relative h-[350px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Side Chart: Status Distribution -->
            <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100">
                <h3 class="text-[#0a5c36] font-black text-xl mb-8 border-b border-gray-50 pb-4">حالة الاشتراكات</h3>
                <div class="relative h-[300px]">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-8 space-y-3">
                    @foreach ($statusStats as $stat)
                        <div class="flex justify-between items-center text-sm font-bold">
                            <span class="text-gray-500">
                                @if (in_array(strtolower($stat->status), ['paid', 'مدفوع']))
                                    مدفوع
                                @elseif(in_array(strtolower($stat->status), ['pending', 'غير مدفوع']))
                                    بانتظار السداد
                                @else
                                    {{ $stat->status }}
                                @endif
                            </span>
                            <span class="text-gray-800">{{ $stat->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-[#0a5c36] font-black text-xl">سجل الاشتراكات
                    ({{ Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }})</h3>
                <div class="flex gap-2">
                    <span
                        class="px-3 py-1 bg-gray-50 text-gray-400 text-[10px] font-bold rounded-lg border border-gray-100">
                        {{ $selectedCircleId ? $circles->firstWhere('id', $selectedCircleId)->name : 'جميع الحلقات' }}
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 font-black text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-8 py-5">الطالب</th>
                            <th class="px-8 py-5">الحلقة</th>
                            <th class="px-8 py-5">المبلغ</th>
                            <th class="px-8 py-5">التاريخ</th>
                            <th class="px-8 py-5">المسؤول</th>
                            <th class="px-8 py-5">طريقة الدفع</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentSubscriptions as $sub)
                            <tr class="hover:bg-emerald-50/30 transition-all">
                                <td class="px-8 py-5 font-bold text-gray-800">{{ $sub->student->name }}</td>
                                <td class="px-8 py-5">
                                    <span
                                        class="px-3 py-1 bg-gray-100 rounded-lg text-gray-600 text-[10px] font-black uppercase">{{ $sub->circle->name }}</span>
                                </td>
                                <td class="px-8 py-5 font-black text-emerald-600">{{ number_format($sub->amount, 2) }}
                                    ج.م</td>
                                <td class="px-8 py-5 text-gray-500 text-sm font-medium">
                                    {{ $sub->paid_at?->format('Y/m/d') }}</td>
                                <td class="px-8 py-5 text-gray-600 text-sm">
                                    {{ $sub->collectedBy?->name ?? '—' }}
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">
                                        {{ $sub->payment_method === 'cash' ? 'نقدي' : ($sub->payment_method === 'transfer' ? 'تحويل بنكي' : 'أخرى') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-10 text-center text-gray-400 font-medium">لا توجد
                                    اشتراكات مسجلة حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart (Bar)
        const revenueData = @json($monthlyRevenue);
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: revenueData.map(d => d.month_label),
                datasets: [{
                    label: 'الإيرادات المستلمة',
                    data: revenueData.map(d => d.total),
                    backgroundColor: '#10b981',
                    borderRadius: 12,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
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
                        display: false
                    }
                }
            }
        });

        // Status Chart (Doughnut)
        const statusData = @json($statusStats);
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusData.map(d => {
                    if (['paid', 'مدفوع'].includes(d.status.toLowerCase())) return 'مدفوع';
                    if (['pending', 'غير مدفوع'].includes(d.status.toLowerCase())) return 'بانتظار السداد';
                    return d.status;
                }),
                datasets: [{
                    data: statusData.map(d => d.count),
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    cutout: '80%'
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
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-layouts.markaz-layout>
