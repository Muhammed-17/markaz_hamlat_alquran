<x-layouts.markaz-layout>
    <x-slot name="title">اشتراكاتي</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" dir="rtl">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[#0a5c36] flex items-center gap-2">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    اشتراكاتي
                </h1>
                <p class="text-gray-500 text-sm mt-1">سجل الاشتراكات الشهرية لأبنائك</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                العودة للرئيسية
            </a>
        </div>

        {{-- Month Filter --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <form method="GET" action="{{ route('guardian.subscription.own') }}" class="flex items-end gap-4">
                <div class="flex-1 max-w-xs">
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">الشهر</label>
                    <input type="month" id="month" name="month" value="{{ $month }}"
                        class="w-full rounded-lg border-gray-300 focus:border-[#0a5c36] focus:ring-[#0a5c36] text-sm">
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-[#0a5c36] hover:bg-[#084a2c] text-white rounded-lg transition-colors text-sm font-medium">
                    عرض السجل
                </button>
                @if(request('month'))
                <a href="{{ route('guardian.subscription.own') }}"
                    class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition-colors text-sm">
                    إعادة تعيين
                </a>
                @endif
            </form>
        </div>

        {{-- Empty State --}}
        @if(empty($summary))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">لا يوجد طلاب مرتبطين بك</h3>
            <p class="text-gray-500 text-sm">لم يتم العثور على أي طلاب مسجلين تحت حسابك.</p>
        </div>
        @else
        {{-- Students Cards --}}
        <div class="space-y-4">
            @foreach($summary as $item)
            @php
            $student = $item['student'];
            $circle = $item['circle'];
            $currentSub = $item['current_subscription'];
            $isPaid = $currentSub && $currentSub->status === 'مدفوع';
            $isExempt = $currentSub && $currentSub->status === 'معفي';
            $statusLabel = $isPaid ? 'مدفوع' : ($isExempt ? 'معفي' : 'غير مدفوع');
            $statusClass = $isPaid ? 'bg-emerald-100 text-emerald-800' : ($isExempt ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800');
            @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ open: false }">
                {{-- Card Header --}}
                <div class="p-5 border-b border-gray-50">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#0a5c36]/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#0a5c36]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $student->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $circle?->name ?? '—' }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- Stats Grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-50">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">المبلغ المطلوب</p>
                            <p class="font-bold text-gray-900">
                                {{ $currentSub ? number_format($currentSub->amount, 2) : '—' }} ج.م
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">المبلغ المدفوع</p>
                            <p class="font-bold {{ $isPaid ? 'text-emerald-600' : 'text-gray-400' }}">
                                {{ $isPaid ? number_format($currentSub->amount, 2) : '0.00' }} ج.م
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">تاريخ التحصيل</p>
                            <p class="font-bold text-gray-900">
                                {{ $currentSub?->paid_at ? $currentSub->paid_at->format('Y-m-d') : '—' }}
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">المُحصِّل</p>
                            <p class="font-bold text-gray-900">
                                {{ $currentSub?->collectedBy?->name ?? '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Summary Badges --}}
                    <div class="flex items-center gap-3 mt-4 flex-wrap">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-medium">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            {{ $item['paid_count_6m'] }} شهر مدفوع (6 شهور)
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            {{ $item['unpaid_count_6m'] }} شهر غير مدفوع
                        </span>
                        @if($item['last_paid_at'])
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-50 text-gray-600 text-xs font-medium">
                            آخر دفع: {{ \Carbon\Carbon::parse($item['last_paid_at'])->format('Y-m-d') }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Collapsible History --}}
                <div class="border-t border-gray-100">
                    <button @click="open = !open"
                        class="w-full px-5 py-3 flex items-center justify-between text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            سجل آخر 6 أشهر
                        </span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="px-5 pb-5">
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">الشهر</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">الحالة</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">المبلغ</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">تاريخ التحصيل</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">المُحصِّل</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-700">سجّله</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($item['history'] as $sub)
                                    @php
                                    $subPaid = $sub->status === 'مدفوع';
                                    $subExempt = $sub->status === 'معفي';
                                    $subStatusClass = $subPaid ? 'bg-emerald-100 text-emerald-800' : ($subExempt ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800');
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-center text-gray-900 font-medium">
                                            {{ \Carbon\Carbon::parse($sub->month)->format('Y-m') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $subStatusClass }}">
                                                {{ $sub->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-900">
                                            {{ number_format($sub->amount, 2) }} ج.م
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600">
                                            {{ $sub->paid_at ? $sub->paid_at->format('Y-m-d') : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600">
                                            {{ $sub->collectedBy?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600">
                                            {{ $sub->teacher?->name ?? '—' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            لا يوجد سجل اشتراكات للأشهر الماضية
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</x-layouts.markaz-layout>