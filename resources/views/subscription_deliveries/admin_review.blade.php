@php
$user = Auth::user();
$isAdmin = $user->hasRole('admin');
$isReviewer = $user->hasRole(['supervisor', 'manager', 'general_manager']);

// تعريف الألوان والتسميات
$statusColors = [
'pending' => 'bg-gray-100 text-gray-600',
'delivered' => 'bg-blue-100 text-blue-700',
'confirmed' => 'bg-emerald-100 text-emerald-700',
'rejected' => 'bg-red-100 text-red-700',
];
$statusLabels = [
'pending' => 'معلق',
'delivered' => 'مسلم',
'confirmed' => 'مؤكد',
'rejected' => 'مرفوض',
];
@endphp

<x-layouts.markaz-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-[#0b3d2c] rounded-3xl p-8 text-white flex justify-between items-center shadow-xl">
            <div>
                <h1 class="text-3xl font-black mb-2">مراجعة التسليم</h1>
                <p class="text-emerald-100/80 text-sm font-medium">
                    {{ $delivery->circle->name }} •
                    {{ \Carbon\Carbon::parse($delivery->month)->format('Y-m') }}
                </p>
            </div>
            <a href="{{ route('subscription-deliveries.index') }}"
                class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl font-bold transition">
                العودة
            </a>
        </div>

        <!-- عرض الحالة بوضوح -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs font-bold mb-1">حالة التسليم الحالية</p>
                <span class="px-4 py-2 rounded-full text-sm font-bold {{ $statusColors[$delivery->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $statusLabels[$delivery->status] ?? $delivery->status }}
                </span>
            </div>
            <div class="text-left">
                <p class="text-gray-400 text-xs font-bold mb-1">تم التأكيد من المدير؟</p>
                @if($delivery->confirmed_by_admin)
                <span class="text-emerald-600 font-bold flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    نعم - {{ $delivery->confirmed_at?->format('Y-m-d H:i') ?? '' }}
                </span>
                @else
                <span class="text-gray-400 font-bold">لا</span>
                @endif
            </div>
        </div>

        <form action="{{ route('subscription-deliveries.admin-review-update', $delivery) }}"
            method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- بطاقات المعلومات -->
            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-gray-400 text-xs font-bold mb-1">المعلم المُسلِّم</p>
                    <p class="text-lg font-black text-gray-800">{{ $delivery->teacher?->name ?? '—' }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-gray-400 text-xs font-bold mb-1">المشرف</p>
                    <p class="text-lg font-black text-gray-800">{{ $delivery->supervisor?->name ?? '—' }}</p>
                </div>
                <!-- حقل admin_id - يظهر فقط للـ Admin -->
                @if($isAdmin)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-gray-700 font-bold mb-2">تعيين المراجع / المعتمد</label>
                    <select name="admin_id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-5 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold">
                        <option value="">اختر المراجع...</option>
                        @foreach ($reviewers as $reviewer)
                        <option value="{{ $reviewer->id }}" {{ old('admin_id', $delivery->admin_id) == $reviewer->id ? 'selected' : '' }}>
                            {{ $reviewer->name }}
                            @foreach($reviewer->roles as $role)
                            <span class="text-xs text-gray-400">({{ $role->name }})</span>
                            @endforeach
                        </option>
                        @endforeach
                    </select>
                    @error('admin_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @elseif($isReviewer)
                <!-- للمشرف/المدير - يظهر اسم صاحب الحساب فقط -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-emerald-800 font-bold">أنت تقوم بالمراجعة: {{ $user->name }}</p>
                        <p class="text-emerald-600 text-sm">سيتم تسجيلك كمراجع لهذا التسليم</p>
                    </div>
                    <input type="hidden" name="admin_id" value="{{ $user->id }}">
                </div>
                @endif
            </div>

            <!-- الأرقام -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 space-y-5">

                <!-- إجمالي الحلقة -->
                <div class="flex justify-between items-center pb-5 border-b border-gray-100">
                    <div>
                        <p class="font-black text-gray-700">إجمالي اشتراكات الحلقة</p>
                        <p class="text-xs text-gray-400">مجموع اشتراكات الطلاب المقيدين في هذا الشهر</p>
                    </div>
                    <div class="text-left">
                        <span class="text-2xl font-black text-gray-800 block">
                            {{ number_format($circleTotal, 2) }} جنيه
                        </span>
                        @if($circleTotal == 0)
                        <span class="text-xs text-amber-600 font-bold">⚠️ تأكد من وجود طلاب مقيدين واشتراكات مسجلة</span>
                        @endif
                    </div>
                    <input type="hidden" name="circle_total_amount" value="{{ $circleTotal }}">
                </div>

                <!-- المبلغ المسلم من المعلم -->
                <div class="flex justify-between items-center pb-5 border-b border-gray-100">
                    <div>
                        <p class="font-black text-gray-700">المبلغ المسلم من المعلم</p>
                        <p class="text-xs text-gray-400">ما سلّمه المعلم للمشرف</p>
                    </div>
                    <span class="text-2xl font-black text-emerald-600">
                        {{ number_format($delivery->delivered_by_teacher, 2) }} جنيه
                    </span>
                </div>

                <!-- المبلغ المتوقع -->
                <div class="flex justify-between items-center pb-5 border-b border-gray-100">
                    <div>
                        <p class="font-black text-gray-700">المبلغ المتوقع</p>
                        <p class="text-xs text-gray-400">ما كان متوقعاً تسليمه (إجمالي - محصل المدير)</p>
                    </div>
                    <span class="text-2xl font-black text-amber-600">
                        {{ number_format($delivery->expected_from_teacher, 2) }} جنيه
                    </span>
                </div>

                <!-- ما استلمه المدير مباشرة -->
                <div class="flex justify-between items-center" x-data="{ adminCollected: {{ $delivery->admin_collected_amount ?? 0 }} }">
                    <div>
                        <p class="font-black text-gray-700">ما استلمه المدير مباشرة</p>
                        <p class="text-xs text-gray-400">مبالغ استلمها المدير من الطلاب</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="admin_collected_amount"
                            x-model="adminCollected"
                            min="0" step="0.01"
                            value="{{ old('admin_collected_amount', $delivery->admin_collected_amount ?? 0) }}"
                            class="w-40 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-center font-black text-blue-600 text-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-gray-400 font-bold">جنيه</span>
                    </div>
                </div>
            </div>

            <!-- ملاحظات -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                <label class="block text-gray-700 font-bold mb-3">ملاحظات</label>
                <textarea name="notes" rows="3"
                    placeholder="أي ملاحظات إضافية..."
                    class="w-full bg-gray-50 border-none rounded-2xl p-5 focus:ring-2 focus:ring-emerald-500 resize-none">{{ old('notes', $delivery->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <!-- أزرار -->
            <div class="flex gap-4">
                <!-- حفظ فقط -->
                <button type="submit" name="confirm" value="0"
                    class="flex-1 py-5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-3xl font-black text-lg transition-all">
                    حفظ البيانات فقط
                </button>

                <!-- تأكيد -->
                @if($isAdmin || $isReviewer)
                @if(!$delivery->confirmed_by_admin)
                <button type="submit" name="confirm" value="1"
                    class="flex-1 py-5 bg-[#0a5c36] hover:scale-[1.01] active:scale-95 text-white rounded-3xl font-black text-lg transition-all shadow-2xl">
                    تأكيد واعتماد التسليم
                </button>
                @else
                <button type="button" disabled
                    class="flex-1 py-5 bg-emerald-100 text-emerald-700 rounded-3xl font-black text-lg cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    تم التأكيد مسبقاً
                </button>
                @endif
                @endif
            </div>

        </form>
    </div>
</x-layouts.markaz-layout>