@php
$statusColors = [
'pending' => ['bg-amber-100', 'text-amber-700', 'قيد المراجعة'],
'confirmed' => ['bg-emerald-100', 'text-emerald-700', 'مُعتمَد'],
];

$circleId = request('circle_id');
$centerId = request('center_id');
@endphp

<x-layouts.markaz-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-[#0b3d2c] rounded-3xl p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6">
            <div class="text-right w-full md:w-auto z-10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-emerald-400/20 rounded-xl flex items-center justify-center border border-emerald-400/30">
                        <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-black">مراجعة تسليم الاشتراكات</h1>
                </div>
                <p class="text-emerald-100/60 text-sm font-medium pr-1">متابعة وتأكيد تسليمات المعلمين للمشرف والمدير</p>
            </div>

            <a href="{{ route('subscription-deliveries.create') }}" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-3.5 rounded-2xl font-bold transition-all shadow-lg shadow-emerald-500/30 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                جديد
            </a>

            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl"></div>
        </div>

        <!-- الفلاتر -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <form action="{{ route('subscription-deliveries.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-bold text-gray-500">تصفية:</span>

                <input type="month" name="month" value="{{ $month }}" max="{{ now()->format('Y-m') }}"
                    class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold">

                <select name="circle_id" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold min-w-[160px]">
                    <option value="">كل الحلقات</option>
                    @foreach($circles as $circle)
                    <option value="{{ $circle->id }}" {{ $circleId == $circle->id ? 'selected' : '' }}>
                        {{ $circle->name }}
                    </option>
                    @endforeach
                </select>

                <select name="center_id" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold min-w-[160px]">
                    <option value="">كل المراكز</option>
                    @foreach($centers as $center)
                    <option value="{{ $center->id }}" {{ $centerId == $center->id ? 'selected' : '' }}>
                        {{ $center->name }}
                    </option>
                    @endforeach
                </select>

                @if(request()->hasAny(['month', 'circle_id', 'center_id']))
                <a href="{{ route('subscription-deliveries.index') }}" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-colors">
                    إعادة ضبط
                </a>
                @endif
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs font-bold mb-1">إجمالي التسليمات</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['total_deliveries'] }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs font-bold mb-1">المتوقع</p>
                <p class="text-2xl font-black text-amber-600">{{ number_format($stats['total_expected'], 2) }}</p>
                <p class="text-xs text-gray-400">جنيه</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs font-bold mb-1">محصل المدير</p>
                <p class="text-2xl font-black text-blue-600">{{ number_format($stats['total_admin_collected'], 2) }}</p>
                <p class="text-xs text-gray-400">جنيه</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs font-bold mb-1">مسلم من المعلم</p>
                <p class="text-2xl font-black text-emerald-600">{{ number_format($stats['total_delivered'], 2) }}</p>
                <p class="text-xs text-gray-400">جنيه</p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-black text-gray-800 text-lg">قائمة التسليمات</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 font-black text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">الحلقة</th>
                            <th class="px-6 py-4">المعلم</th>
                            <th class="px-6 py-4">الشهر</th>
                            <th class="px-6 py-4">إجمالي الحلقة</th>
                            <th class="px-6 py-4">محصل المدير</th>
                            <th class="px-6 py-4">المتوقع</th>
                            <th class="px-6 py-4">مسلم</th>
                            <th class="px-6 py-4">المتبقي</th>
                            <th class="px-6 py-4">الحالة</th>
                            <th class="px-6 py-4 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($deliveries as $delivery)
                        @php
                        $remaining = $delivery->expected_from_teacher - $delivery->delivered_by_teacher;
                        $statusKey = $delivery->confirmed_by_admin ? 'confirmed' : 'pending';
                        $statusStyle = $statusColors[$statusKey];
                        @endphp
                        <tr class="hover:bg-emerald-50/30 transition-all">
                            <td class="px-6 py-4 font-bold text-gray-500">{{ $loop->iteration + ($deliveries->currentPage() - 1) * $deliveries->perPage() }}</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800">{{ $delivery->circle->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium">{{ $delivery->teacher->name }}</td>
                            <td class="px-6 py-4 text-gray-600 font-medium">{{ $delivery->month->format('Y-m') }}</td>
                            <td class="px-6 py-4 font-bold text-gray-700">{{ number_format($delivery->circle_total_amount, 2) }}</td>
                            <td class="px-6 py-4 font-bold text-blue-600">{{ number_format($delivery->admin_collected_amount, 2) }}</td>
                            <td class="px-6 py-4 font-bold text-amber-600">{{ number_format($delivery->expected_from_teacher, 2) }}</td>
                            <td class="px-6 py-4 font-bold text-emerald-600">{{ number_format($delivery->delivered_by_teacher, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="font-bold {{ $remaining > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ number_format($remaining, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 {{ $statusStyle[0] }} {{ $statusStyle[1] }} rounded-full text-xs font-bold">
                                    {{ $statusStyle[2] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- ✅ زر مراجعة المدير --}}
                                    @if(auth()->user()->hasRole(['admin', 'general_manager']))
                                    <a href="{{ route('subscription-deliveries.admin-review', $delivery) }}"
                                        class="w-9 h-9 flex items-center justify-center {{ $delivery->confirmed_by_admin ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600' : 'bg-blue-50 hover:bg-blue-100 text-blue-600' }} rounded-xl transition-colors"
                                        title="{{ $delivery->confirmed_by_admin ? 'عرض التفاصيل' : 'مراجعة المدير' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($delivery->confirmed_by_admin)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            @endif
                                        </svg>
                                    </a>
                                    @endif

                                    {{-- ✅ زر التعديل --}}
                                    @can('update', $delivery)
                                    <a href="{{ route('subscription-deliveries.edit', $delivery) }}"
                                        class="w-9 h-9 flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-xl transition-colors"
                                        title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @endcan

                                    {{-- ✅ زر الحذف --}}
                                    @can('delete', $delivery)
                                    <form action="{{ route('subscription-deliveries.destroy', $delivery) }}" method="POST" class="inline"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا التسليم؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-9 h-9 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-colors"
                                            title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan

                                    {{-- ✅ زر التفاصيل --}}
                                    <button onclick="toggleDetails({{ $delivery->id }})"
                                        class="w-9 h-9 flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl transition-colors"
                                        title="التفاصيل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Details Row -->
                        <tr id="details-{{ $delivery->id }}" class="hidden bg-gray-50/50">
                            <td colspan="11" class="px-6 py-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-400 font-bold text-xs mb-1">تاريخ التسليم</p>
                                        <p class="text-gray-700">{{ $delivery->delivered_at?->format('Y-m-d H:i') ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-400 font-bold text-xs mb-1">تاريخ الاعتماد</p>
                                        <p class="text-gray-700">{{ $delivery->confirmed_at?->format('Y-m-d H:i') ?? 'لم يتم الاعتماد بعد' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-400 font-bold text-xs mb-1">تم الاعتماد بواسطة</p>
                                        <p class="text-gray-700">{{ $delivery->admin?->name ?? '-' }}</p>
                                    </div>
                                    <div class="md:col-span-3">
                                        <p class="text-gray-400 font-bold text-xs mb-1">ملاحظات</p>
                                        <p class="text-gray-700">{{ $delivery->notes ?? 'لا توجد ملاحظات' }}</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <p class="text-gray-400 font-bold">لا توجد تسليمات لهذا الشهر</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($deliveries->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $deliveries->links() }}
            </div>
            @endif
        </div>
    </div>

    <script>
        function toggleDetails(deliveryId) {
            const row = document.getElementById(`details-${deliveryId}`);
            row.classList.toggle('hidden');
        }
    </script>
</x-layouts.markaz-layout>