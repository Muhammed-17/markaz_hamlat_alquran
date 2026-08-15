<x-layouts.markaz-layout>
    {{-- Header --}}
    <div class="bg-[#0b3d2c] rounded-3xl p-8 text-white mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-black">مراجعة التحصيل</h1>
                <p class="text-emerald-200 mt-1 text-sm">
                    الحلقة: <span class="font-bold text-white">{{ $collectionRound->circle?->name ?? '—' }}</span>
                    &nbsp;|&nbsp;
                    رقم التحصيل: <span class="font-bold text-white">{{ $collectionRound->round_number }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                {{-- ✅ تحسين 4: زر تصدير/طباعة --}}
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 bg-white/10 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-white/20 transition print:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    طباعة
                </button>
                <a href="{{ route('collection-rounds.index') }}"
                    class="inline-flex items-center gap-2 bg-white/10 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                    عودة للقائمة
                </a>
            </div>
        </div>
    </div>

    {{-- بطاقة الحالة --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-3 flex-wrap">
            @if($collectionRound->status === 'pending')
            <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-sm font-bold bg-amber-100 text-amber-700">
                معلّق
            </span>
            <span class="text-gray-500 text-sm">في انتظار مراجعة المدير والتأكيد</span>
            @elseif($collectionRound->status === 'confirmed')
            <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-sm font-bold bg-emerald-100 text-emerald-700">
                مؤكَّد
            </span>
            <span class="text-gray-500 text-sm">
                تم التأكيد بواسطة <span class="font-bold text-gray-700">{{ $collectionRound->confirmedBy?->name ?? '—' }}</span>
                بتاريخ {{ $collectionRound->confirmed_at?->format('Y-m-d H:i') ?? '—' }}
            </span>
            @endif
        </div>
    </div>

    {{-- تنبيه ملاحظة المدير السابقة (إن وجدت ولم تُعالج) --}}
    @if($collectionRound->manager_note)
    <div class="mb-6 p-5 rounded-2xl border {{ $collectionRound->manager_note_addressed ? 'bg-gray-50 border-gray-200' : 'bg-red-50 border-red-200' }}">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 {{ $collectionRound->manager_note_addressed ? 'text-gray-400' : 'text-red-500' }} flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1">
                <p class="text-sm font-bold {{ $collectionRound->manager_note_addressed ? 'text-gray-500' : 'text-red-700' }} mb-1">
                    ملاحظة مراجعة المدير {{ $collectionRound->manager_note_addressed ? '(تمت معالجتها)' : '' }}
                </p>
                <p class="text-sm {{ $collectionRound->manager_note_addressed ? 'text-gray-500' : 'text-red-800' }} whitespace-pre-line leading-relaxed">{{ $collectionRound->manager_note }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ✅ تحسين 3: تحذير المحصّلين غير المحددين --}}
    @php
    $undefinedCollectors = $collectionRound->items->filter(fn($item) => is_null($item->collected_by_snapshot));
    @endphp
    @if($undefinedCollectors->count() > 0)
    <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-2xl p-5">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1">
                <h4 class="font-bold text-red-800 mb-1">⚠️ تحذير: محصّلون غير محددين</h4>
                <p class="text-sm text-red-700">
                    يوجد <span class="font-bold">{{ $undefinedCollectors->count() }}</span> اشتراك/اشتراكات مسجّلة بدون محصّل محدد.
                    هذا قد يشير إلى خطأ في تسجيل البيانات.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- بطاقات المعلومات --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide">المشرف</p>
            <p class="text-lg font-bold text-gray-800">{{ $collectionRound->createdBy?->name ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide">الحلقة</p>
            <p class="text-lg font-bold text-gray-800">{{ $collectionRound->circle?->name ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide">الفرع</p>
            <p class="text-lg font-bold text-gray-800">{{ $collectionRound->center?->name ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide">الشهر</p>
            <p class="text-lg font-bold text-gray-800">{{ $collectionRound->period_month?->format('Y-m') ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide">رقم التحصيل</p>
            <p class="text-lg font-bold text-gray-800">{{ $collectionRound->round_number }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide">عدد الاشتراكات</p>
            <p class="text-lg font-bold text-gray-800">{{ $collectionRound->students_count }}</p>
        </div>
    </div>

    {{-- ✅ تحسين 2: إحصائيات سريعة --}}
    @php
    $items = $collectionRound->items;
    $totalAmount = $collectionRound->total_amount;
    $avgAmount = $items->count() > 0 ? $totalAmount / $items->count() : 0;
    $collectorsCount = $items->pluck('collected_by_snapshot')->unique()->filter()->count();
    $stdDev = 0;
    if ($items->count() > 1) {
    $mean = $avgAmount;
    $variance = $items->sum(fn($item) => pow($item->amount_at_collection - $mean, 2)) / $items->count();
    $stdDev = sqrt($variance);
    }
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 text-center">
            <p class="text-2xl font-black text-emerald-700">{{ number_format($totalAmount, 2) }}</p>
            <p class="text-xs text-emerald-600 mt-1 font-medium">إجمالي المبلغ (جنيه)</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-center">
            <p class="text-2xl font-black text-blue-700">{{ $items->count() }}</p>
            <p class="text-xs text-blue-600 mt-1 font-medium">عدد الاشتراكات</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5 text-center">
            <p class="text-2xl font-black text-purple-700">{{ number_format($avgAmount, 2) }}</p>
            <p class="text-xs text-purple-600 mt-1 font-medium">متوسط المبلغ (جنيه)</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 text-center">
            <p class="text-2xl font-black text-orange-700">{{ $collectorsCount }}</p>
            <p class="text-xs text-orange-600 mt-1 font-medium">عدد المحصّلين</p>
        </div>
    </div>

    {{-- ✅ تحسين 5: مقارنة مع التحصيلات السابقة --}}
    @php
    $previousRounds = \App\Models\CollectionRound::where('circle_id', $collectionRound->circle_id)
    ->where('period_month', $collectionRound->period_month)
    ->where('id', '!=', $collectionRound->id)
    ->orderBy('round_number')
    ->get();
    @endphp
    @if($previousRounds->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            التحصيلات السابقة لهذا الشهر
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-xs font-bold text-gray-500">الرقم</th>
                        <th class="px-4 py-2 text-xs font-bold text-gray-500">المبلغ</th>
                        <th class="px-4 py-2 text-xs font-bold text-gray-500">الطلاب</th>
                        <th class="px-4 py-2 text-xs font-bold text-gray-500">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($previousRounds as $pr)
                    <tr class="{{ $pr->id === $collectionRound->id ? 'bg-emerald-50' : '' }}">
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $pr->round_number }}</td>
                        <td class="px-4 py-2 text-sm font-bold text-gray-800">{{ number_format($pr->total_amount, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $pr->students_count }}</td>
                        <td class="px-4 py-2">
                            <span class="text-xs px-2 py-1 rounded-full {{ $pr->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $pr->status === 'confirmed' ? 'مؤكَّد' : 'معلّق' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ✅ تحسين 1: تفاصيل التحصيل — عرض مجمّع حسب المحصّل + تفاصيل فردية --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6" x-data="{ expandedCollectors: {} }">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
            <h2 class="text-lg font-bold text-gray-800">تفاصيل التحصيل</h2>
            <div class="flex gap-2">
                <button type="button" @click="expandedCollectors = {}"
                    class="text-xs text-gray-500 hover:text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
                    طي الكل
                </button>
                <button type="button" @click="
                    @foreach($items->groupBy('collected_by_snapshot') as $collectedBy => $group)
                        expandedCollectors[{{ $collectedBy ?? 'null' }}] = true;
                    @endforeach
                "
                    class="text-xs text-[#0b3d2c] hover:text-[#094a36] px-3 py-1.5 rounded-lg hover:bg-[#0b3d2c]/5 transition">
                    توسيع الكل
                </button>
            </div>
        </div>

        {{-- عرض مجمّع حسب المحصّل --}}
        @php
        $groupedItems = $items->groupBy('collected_by_snapshot');
        @endphp

        <div class="divide-y divide-gray-100">
            @forelse($groupedItems as $collectedBy => $group)
            @php
            $collector = $group->first()->collectedBySnapshot;
            $collectorName = $collector?->name ?? 'غير محدد';
            $collectorTotal = $group->sum('amount_at_collection');
            $collectorCount = $group->count();
            $isUndefined = is_null($collectedBy);
            @endphp
            <div class="group"
                x-data="{ collectorId: {{ $collectedBy ?? 'null' }} }">
                {{-- ترويسة المحصّل --}}
                <div @click="expandedCollectors[collectorId] = !expandedCollectors[collectorId]"
                    class="flex items-center gap-4 p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                    :class="expandedCollectors[collectorId] ? 'bg-gray-50' : ''"
                    role="button"
                    tabindex="0"
                    @keydown.enter.prevent="expandedCollectors[collectorId] = !expandedCollectors[collectorId]"
                    @keydown.space.prevent="expandedCollectors[collectorId] = !expandedCollectors[collectorId]">
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                        :class="expandedCollectors[collectorId] ? 'rotate-90' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-gray-900">{{ $collectorName }}</span>
                            @if($isUndefined)
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold">غير محدد</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500 mt-0.5">
                            {{ $collectorCount }} اشتراك
                        </div>
                    </div>

                    <div class="text-left flex-shrink-0">
                        <span class="text-lg font-bold {{ $isUndefined ? 'text-red-600' : 'text-[#0b3d2c]' }}">{{ number_format($collectorTotal, 2) }}</span>
                        <span class="text-xs text-gray-400">جنيه</span>
                    </div>
                </div>

                {{-- تفاصيل الاشتراكات (مُوسّعة) --}}
                <div x-show="expandedCollectors[collectorId]"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 max-h-0"
                    x-transition:enter-end="opacity-100 max-h-[2000px]"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 max-h-[2000px]"
                    x-transition:leave-end="opacity-0 max-h-0"
                    class="overflow-hidden border-t border-gray-100 bg-gray-50/50">
                    {{-- ✅ تحسين 8: جدول على الشاشات الكبيرة، بطاقات على الجوال --}}

                    {{-- عرض الجدول (md فأعلى) --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="bg-gray-100/50">
                                <tr>
                                    <th class="px-6 py-2 text-xs font-bold text-gray-500">#</th>
                                    <th class="px-6 py-2 text-xs font-bold text-gray-500">الطالب</th>
                                    <th class="px-6 py-2 text-xs font-bold text-gray-500">المبلغ (جنيه)</th>
                                    {{-- ✅ تحسين 6: كشف المبالغ الشاذة --}}
                                    <th class="px-6 py-2 text-xs font-bold text-gray-500">ملاحظة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($group as $index => $item)
                                @php
                                $amount = $item->amount_at_collection;
                                $isOutlier = $stdDev > 0 && abs($amount - $avgAmount) > (2 * $stdDev);
                                $isHigh = $amount > $avgAmount;
                                @endphp
                                <tr class="{{ $isOutlier ? 'bg-amber-50/50' : '' }}">
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-700 font-medium">{{ $item->subscription?->student?->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm font-bold {{ $isOutlier ? 'text-amber-700' : 'text-gray-700' }}">{{ number_format($amount, 2) }}</td>
                                    <td class="px-6 py-3">
                                        @if($isOutlier)
                                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $isHigh ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $isHigh ? '🔺 مبلغ مرتفع' : '🔻 مبلغ منخفض' }}
                                        </span>
                                        @else
                                        <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- عرض البطاقات (الجوال) --}}
                    <div class="md:hidden p-4 space-y-3">
                        @foreach($group as $index => $item)
                        @php
                        $amount = $item->amount_at_collection;
                        $isOutlier = $stdDev > 0 && abs($amount - $avgAmount) > (2 * $stdDev);
                        $isHigh = $amount > $avgAmount;
                        @endphp
                        <div class="bg-white rounded-xl p-4 border {{ $isOutlier ? 'border-amber-200' : 'border-gray-200' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-gray-800">{{ $item->subscription?->student?->name ?? '—' }}</span>
                                <span class="text-sm font-bold {{ $isOutlier ? 'text-amber-700' : 'text-gray-700' }}">{{ number_format($amount, 2) }} ج</span>
                            </div>
                            @if($isOutlier)
                            <span class="text-xs font-bold px-2 py-1 rounded-full {{ $isHigh ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $isHigh ? '🔺 مبلغ مرتفع' : '🔻 مبلغ منخفض' }}
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p>لا توجد عناصر مسجَّلة في هذا التحصيل</p>
            </div>
            @endforelse
        </div>

        {{-- الإجمالي --}}
        <div class="bg-gray-50 border-t border-gray-100 px-6 py-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-gray-700">الإجمالي الكلي</span>
                <span class="text-2xl font-black text-[#0b3d2c]">{{ number_format($collectionRound->total_amount, 2) }} جنيه</span>
            </div>
        </div>
    </div>

    {{-- ملاحظة المشرف --}}
    @if($collectionRound->supervisor_note)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-sm font-bold text-gray-700 mb-2">ملاحظة المشرف</h3>
        <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed">{{ $collectionRound->supervisor_note }}</p>
    </div>
    @endif

    {{-- منطقة الإجراءات (تظهر فقط للحالة pending) --}}
    @if($collectionRound->status === 'pending')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6"
        x-data="{ showNoteForm: false, showConfirmModal: false, confirmedBy: '{{ auth()->user()->hasRole('admin') ? '' : auth()->id() }}', confirmedbyerror: false }">

        {{-- العنوان مع خط فاصل --}}
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-[#0b3d2c]/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#0b3d2c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">إجراءات المدير</h3>
                <p class="text-xs text-gray-400 mt-0.5">اختر الإجراء المناسب لهذا التحصيل</p>
            </div>
        </div>

        {{-- صف الإجراءات الرئيسية --}}
        <div class="flex flex-col lg:flex-row gap-4" x-show="!showNoteForm">

            {{-- قسم تأكيد التحصيل --}}
            <div class="flex-1 bg-emerald-50/50 border border-emerald-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm font-bold text-emerald-800">تأكيد التحصيل</span>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
                    @if(auth()->user()->hasRole('admin'))
                    {{-- حقل اختيار المدير --}}
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-emerald-700 mb-1.5">تسجيل التأكيد باسم</label>
                        <div class="relative">
                            <select x-model="confirmedBy" @change="confirmedByError = false" required
                                :class="confirmedByError ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500'"
                                class="w-full appearance-none rounded-lg bg-white text-sm h-11 pl-10 pr-4 cursor-pointer">
                                <option value="">اختر مديرًا...</option>
                                @foreach($confirmers as $confirmer)
                                <option value="{{ $confirmer->id }}">{{ $confirmer->name }}</option>
                                @endforeach
                            </select>
                            <svg class="w-4 h-4 text-emerald-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        {{-- ✅ رسالة الخطأ --}}
                        <p x-show="confirmedByError" x-cloak x-transition
                            class="text-xs text-red-600 mt-1.5 font-medium">
                            من فضلك اختر مديرًا أولاً
                        </p>
                    </div>
                    @else
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-emerald-700 mb-1.5">سيُسجَّل التأكيد باسم</label>
                        <div class="h-11 flex items-center px-4 rounded-lg bg-white border border-emerald-200 text-sm font-bold text-gray-700">
                            {{ auth()->user()->name }}
                        </div>
                    </div>
                    @endif

                    {{-- زر التأكيد --}}
                    <button type="button"
                        @click="if({{ auth()->user()->hasRole('admin') ? 'true' : 'false' }} && !confirmedBy) { confirmedByError = true; return; } confirmedByError = false; showConfirmModal = true"
                        class="inline-flex items-center justify-center gap-2 bg-emerald-600 text-white px-6 py-3 rounded-lg font-bold text-sm hover:bg-emerald-700 active:bg-emerald-800 transition shadow-sm hover:shadow-md transform hover:-translate-y-0.5 active:translate-y-0 h-11 sm:h-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        تأكيد التحصيل
                    </button>
                </div>
            </div>

            {{-- فاصل بصري --}}
            <div class="hidden lg:flex items-center">
                <div class="w-px h-16 bg-gray-200"></div>
            </div>

            {{-- قسم ملاحظة المراجعة --}}
            <div class="flex-1 bg-amber-50/50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="text-sm font-bold text-amber-800">ملاحظة مراجعة</span>
                </div>
                <p class="text-xs text-amber-600 mb-3 leading-relaxed">
                    أضف ملاحظة للمشرف لمراجعة التحصيل قبل التأكيد النهائي.
                </p>
                <button type="button" @click="showNoteForm = true"
                    class="inline-flex items-center justify-center gap-2 bg-white text-amber-700 border-2 border-amber-300 px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-amber-50 active:bg-amber-100 transition w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة ملاحظة
                </button>
            </div>
        </div>

        {{-- زر إلغاء الملاحظة (يظهر فقط عند فتح النموذج) --}}
        <div x-show="showNoteForm" x-transition class="mb-4">
            <button type="button" @click="showNoteForm = false"
                class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                إلغاء وعودة للإجراءات
            </button>
        </div>

        {{-- ✅ Modal التأكيد المزدوج --}}
        <div x-show="showConfirmModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showConfirmModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95">

                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">تأكيد التحصيل</h3>
                    <p class="text-sm text-gray-500">يرجى مراجعة الملخص التالي قبل التأكيد النهائي</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">الحلقة:</span>
                        <span class="font-bold text-gray-800">{{ $collectionRound->circle?->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">الشهر:</span>
                        <span class="font-bold text-gray-800">{{ $collectionRound->period_month?->format('Y-m') ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">عدد الاشتراكات:</span>
                        <span class="font-bold text-gray-800">{{ $collectionRound->students_count }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">المبلغ الإجمالي:</span>
                        <span class="font-bold text-emerald-700 text-lg">{{ number_format($collectionRound->total_amount, 2) }} جنيه</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <form method="POST" action="{{ route('collection-rounds.confirm.update', $collectionRound->id) }}" class="flex-1" id="confirmRoundFormModal">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="confirmed_by" x-bind:value="confirmedBy">

                        @if(auth()->user()->hasRole('admin'))
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-600 mb-1">تسجيل التأكيد باسم</label>
                            <select x-model="confirmedBy" required
                                class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm h-11">
                                <option value="">اختر مديرًا...</option>
                                @foreach($confirmers as $confirmer)
                                <option value="{{ $confirmer->id }}">{{ $confirmer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <p class="text-sm text-gray-600 mb-4">سيُسجَّل التأكيد باسمك: <span class="font-bold">{{ auth()->user()->name }}</span></p>
                        @endif

                        <button type="button"
                            onclick="confirmRoundAmountAndSubmitForm({{ $collectionRound->total_amount }}, document.getElementById('confirmRoundFormModal'))"
                            class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-700 transition shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            نعم، تأكيد التحصيل
                        </button>
                    </form>
                    <button type="button" @click="showConfirmModal = false"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-200 transition">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>

        {{-- نموذج ملاحظة المدير (قابل للطي) --}}
        <div x-show="showNoteForm" x-transition class="mt-4">
            <form method="POST" action="{{ route('collection-rounds.manager-note', $collectionRound->id) }}" class="max-w-2xl">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <label for="manager_note" class="block text-sm font-bold text-gray-700 mb-1">ملاحظة المراجعة</label>
                    <textarea id="manager_note" name="manager_note" rows="3" required maxlength="1000"
                        class="w-full rounded-xl border-gray-200 focus:border-amber-400 focus:ring-amber-400 resize-none"
                        placeholder="اشرح المشكلة التي تستدعي مراجعة هذا التحصيل..."></textarea>
                    @error('manager_note')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-amber-700 transition">
                    إرسال الملاحظة
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6 text-center mb-6">
        <p class="text-gray-500 font-medium">
            هذا التحصيل
            @if($collectionRound->status === 'confirmed')
            <span class="text-emerald-700 font-bold">مؤكَّد</span>
            @endif
            ولا يمكن اتخاذ أي إجراء إضافي عليها.
        </p>
    </div>
    @endif
</x-layouts.markaz-layout>