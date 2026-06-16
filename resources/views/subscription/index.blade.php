<x-layouts.markaz-layout>
    <div class="space-y-6">

        {{-- ─── Header ────────────────────────────────────────────── --}}
        <div class="bg-[#0b3d2c] rounded-3xl p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="text-right w-full md:w-auto z-10 text-wrap">
                <h1 class="text-3xl font-black mb-2">إدارة اشتراكات الطلاب</h1>
                <p class="text-emerald-100/80 text-sm font-medium">متابعة التحصيل المالي والإحصائيات</p>
            </div>
            <div class="z-10">
                <a href="{{ route('subscriptions.create') }}"
                    class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 rounded-xl text-white font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-900/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    جديد
                </a>
            </div>
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        {{-- ─── Filters ─────────────────────────────────────────────── --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('subscriptions.index') }}" method="GET" class="flex flex-wrap items-end gap-4">

                <div class="flex flex-col gap-1 min-w-55 flex-1">
                    <label class="text-[10px] font-bold text-gray-400 mr-2 uppercase">بحث باسم الطالب</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="اكتب اسم الطالب..."
                        class="bg-gray-50 border-none rounded-xl px-4 py-2.5 text-gray-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                </div>

                <div class="flex flex-col gap-1 min-w-45">
                    <label class="text-[10px] font-bold text-gray-400 mr-2 uppercase">الحلقة</label>
                    <select name="circle_id"
                        class="bg-gray-50 border-none rounded-xl px-4 py-2.5 text-gray-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                        @role(['admin','manager','supervisor'])
                        <option value="">كل الحلقات</option>
                        @endrole
                        @foreach ($circles as $circle)
                        <option value="{{ $circle->id }}" {{ $selectedCircleId == $circle->id ? 'selected' : '' }}>
                            {{ $circle->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1 min-w-37.5">
                    <label class="text-[10px] font-bold text-gray-400 mr-2 uppercase">حالة السداد</label>
                    <select name="status"
                        class="bg-gray-50 border-none rounded-xl px-4 py-2.5 text-gray-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                        <option value="">كل الحالات</option>
                        <option value="مدفوع" {{ $selectedStatus == 'مدفوع' ? 'selected' : '' }}>مدفوع</option>
                        <option value="معفي"  {{ $selectedStatus == 'معفي'  ? 'selected' : '' }}>معفي</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1 min-w-40">
                    <label class="text-[10px] font-bold text-gray-400 mr-2 uppercase">الشهر</label>
                    <input type="month" name="month" value="{{ $selectedMonth }}"
                        class="bg-gray-50 border-none rounded-xl px-4 py-2.5 text-gray-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#0a5c36] hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-all flex items-center gap-2 shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        بحث
                    </button>

                    @if($search || $selectedCircleId || $selectedStatus)
                    <a href="{{ route('subscriptions.index') }}"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                        إلغاء
                    </a>
                    @endif
                </div>

            </form>
        </div>

        {{-- ─── Quick Stats ──────────────────────────────────────────── --}}
        @can('view subscriptions chart')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- التحصيل الفعلي في الشهر المختار --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-emerald-100 transition-all">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 transition-transform group-hover:scale-110">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-bold">
                        تحصيل شهر {{ Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}
                        <span class="text-amber-500">(حسب تاريخ الدفع الفعلي)</span>
                    </p>
                    <h3 class="text-2xl font-black text-gray-800">
                        {{ number_format($monthlyCollected ?? 0, 2) }}
                        <span class="text-sm font-medium text-gray-400">ج.م</span>
                    </h3>
                    {{-- إيرادات شهر الاستحقاق للمقارنة --}}
                    @php $dueRevenue = $statusStats->where('status','مدفوع')->first()?->total_amount ?? 0; @endphp
                    @if($dueRevenue != ($monthlyCollected ?? 0))
                    <p class="text-[15px] text-gray-400 mt-0.5">
                        إيرادات هذا الشهر فقط: {{ number_format($dueRevenue, 2) }} ج.م
                    </p>
                    @endif
                </div>
            </div>

            {{-- نسبة السداد --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-blue-100 transition-all">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 transition-transform group-hover:scale-110">
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

            {{-- المبالغ غير المحصلة --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-amber-100 transition-all">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 transition-transform group-hover:scale-110">
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

        {{-- ─── Charts ───────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- الإيرادات الشهرية (حسب paid_at) --}}
            <div class="lg:col-span-2 bg-white p-8 rounded-[40px] shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8 border-b border-gray-50 pb-4">
                    <div>
                        <h3 class="text-[#0a5c36] font-black text-xl">نمو الإيرادات الشهري</h3>
                        <p class="text-gray-400 text-xs mt-1">حسب تاريخ الدفع الفعلي — آخر 6 أشهر</p>
                    </div>
                    <span class="text-gray-400 text-sm font-bold">آخر 6 أشهر</span>
                </div>
                <div class="relative h-87.5">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- توزيع الحالات (حسب شهر الاستحقاق) --}}
            <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100">
                <div class="mb-8 border-b border-gray-50 pb-4">
                    <h3 class="text-[#0a5c36] font-black text-xl">حالة الاشتراكات</h3>
                    <p class="text-gray-400 text-xs mt-1">حسب شهر الاستحقاق</p>
                </div>
                <div class="relative h-75">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-8 space-y-3">
                    @foreach ($statusStats as $stat)
                    <div class="flex justify-between items-center text-sm font-bold">
                        <span class="text-gray-500">
                            @if(in_array(strtolower($stat->status), ['paid', 'مدفوع']))
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
        @endcan

        {{-- ─── جدول الاشتراكات ──────────────────────────────────────── --}}
        <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">

            {{-- Title --}}
            <div class="p-8 border-b border-gray-50 flex justify-between items-center flex-wrap gap-3">
                <div>
                    <h3 class="text-[#0a5c36] font-black text-xl">
                        سجل الاشتراكات — {{ Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}
                    </h3>
                    <p class="text-gray-400 text-xs mt-1">
                        يشمل اشتراكات الشهر + كل ما دُفع فعلياً في هذا الشهر (مرتّب بآخر دفع أولاً)
                    </p>
                </div>
                <span class="px-3 py-1 bg-gray-50 text-gray-400 text-[10px] font-bold rounded-lg border border-gray-100">
                    {{ $selectedCircleId ? $circles->firstWhere('id', $selectedCircleId)?->name : 'جميع الحلقات' }}
                </span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 font-black text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-8 py-5">الطالب</th>
                            <th class="px-8 py-5">شهر الاستحقاق</th>
                            <th class="px-8 py-5">المبلغ</th>
                            <th class="px-8 py-5">حالة السداد</th>
                            <th class="px-8 py-5">تاريخ الدفع</th>
                            <th class="px-8 py-5">المسؤول</th>
                            <th class="px-8 py-5">طريقة الدفع</th>
                            <th class="px-8 py-5">الملاحظات</th>
                            <th class="px-8 py-5">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentSubscriptions as $sub)
                        <tr class="hover:bg-emerald-50/30 transition-all">

                            <td class="px-8 py-5 font-bold text-gray-800">{{ $sub->student->name }}</td>

                            {{-- شهر الاستحقاق — يوضح لو الدفع كان متأخراً --}}
                            <td class="px-8 py-5 text-sm font-medium text-gray-600">
                                {{ Carbon\Carbon::parse($sub->month)->translatedFormat('F Y') }}
                                @if($sub->paid_at && Carbon\Carbon::parse($sub->month)->format('Y-m') !== Carbon\Carbon::parse($sub->paid_at)->format('Y-m'))
                                    <span class="mr-1 px-2 py-0.5 bg-amber-50 text-amber-600 rounded-md text-[10px] font-bold">
                                        دُفع متأخراً
                                    </span>
                                @endif
                            </td>

                            <td class="px-8 py-5 font-black text-emerald-600">
                                {{ number_format($sub->amount, 2) }} ج.م
                            </td>

                            <td class="px-8 py-5">
                                @if($sub->status == 'مدفوع')
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold">مدفوع</span>
                                @elseif($sub->status == 'غير مدفوع')
                                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs font-bold">غير مدفوع</span>
                                @elseif($sub->status == 'معفي')
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">معفي</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-50 text-gray-500 rounded-lg text-xs font-bold">{{ $sub->status }}</span>
                                @endif
                            </td>

                            {{-- تاريخ الدفع الفعلي --}}
                            <td class="px-8 py-5 text-gray-500 text-sm font-medium">
                                @if($sub->paid_at)
                                    {{ $sub->paid_at->format('Y/m/d') }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="px-8 py-5 text-gray-600 text-sm">
                                {{ $sub->collectedBy?->name ?? '—' }}
                            </td>

                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">
                                    {{ $sub->payment_method ?? '—' }}
                                </span>
                            </td>

                            <td class="px-8 py-5">
                                @if($sub->notes)
                                <div class="relative max-w-45" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center gap-1.5 group w-full text-right">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0 group-hover:text-emerald-500 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="truncate text-gray-500 text-sm group-hover:text-gray-700 transition-colors">
                                            {{ $sub->notes }}
                                        </span>
                                    </button>
                                    <div x-cloak x-show="open" @click.outside="open = false"
                                        x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="absolute z-50 bottom-full right-0 mb-2 p-3 bg-gray-900 text-white text-sm rounded-xl shadow-xl max-w-65 wrap-break-word">
                                        <p class="leading-relaxed">{{ $sub->notes }}</p>
                                        <div class="absolute bottom-0 right-4 w-2 h-2 bg-gray-900 transform translate-y-1 rotate-45"></div>
                                    </div>
                                </div>
                                @else
                                <span class="text-gray-300">&mdash;</span>
                                @endif
                            </td>

                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2">
                                    @can('update', $sub)
                                    <a href="{{ route('subscriptions.edit', $sub->id) }}"
                                        class="p-2 bg-blue-50 hover:bg-blue-500 text-blue-600 hover:text-white rounded-xl transition-all"
                                        title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @endcan

                                    @can('delete', $sub)
                                    <button type="button"
                                        onclick="confirmDelete({{ $sub->id }}, {{ Js::from($sub->student->name) }})"
                                        class="p-2 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white rounded-xl transition-all"
                                        title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $sub->id }}"
                                        action="{{ route('subscriptions.destroy', $sub->id) }}"
                                        method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endcan
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-8 py-10 text-center text-gray-400 font-medium">
                                لا توجد اشتراكات مسجلة حالياً
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-gray-50">
                {{ $recentSubscriptions->links() }}
            </div>

        </div>
    </div>

    {{-- ─── Scripts ──────────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ─── Revenue Chart (حسب paid_at الفعلي) ──────────────────────
        const revenueData = @json($monthlyRevenue);
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: revenueData.map(d => d.month_label),
                datasets: [{
                    label: 'الإيرادات المستلمة فعلياً',
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
                        grid: { borderDash: [5, 5] },
                        ticks: { font: { family: 'Cairo' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Cairo' } }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });

        // ─── Status Chart (حسب شهر الاستحقاق) ────────────────────────
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
                        labels: { font: { family: 'Cairo', weight: 'bold' } }
                    }
                }
            }
        });

        // ─── Delete Confirmation ──────────────────────────────────────
        function confirmDelete(subId, studentName) {
            Swal.fire({
                title: 'حذف اشتراك: ' + studentName,
                text: 'سيتم حذف هذا الاشتراك نهائياً. لن تتمكن من التراجع!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl font-bold',
                    confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
                    cancelButton: 'rounded-xl px-6 py-2.5 text-sm',
                }
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + subId).submit();
                }
            });
        }

        // ─── Flash Messages ───────────────────────────────────────────
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح',
            text: @json(session('success')),
            confirmButtonColor: '#0a5c36',
            confirmButtonText: 'حسناً',
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-3xl font-bold',
                confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
            }
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: @json(session('error')),
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'حسناً',
            customClass: {
                popup: 'rounded-3xl font-bold',
                confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
            }
        });
        @endif
    </script>
</x-layouts.markaz-layout>