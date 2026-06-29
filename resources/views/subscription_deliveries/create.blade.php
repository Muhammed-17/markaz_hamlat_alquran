@php
$subscriptionDelivery = $delivery ?? null;
$selectedCircle = old('circle_id', $subscriptionDelivery->circle_id ?? request('circle_id'));
$selectedTeacher = old('teacher_id', $subscriptionDelivery->teacher_id ?? request('teacher_id'));
$selectedSupervisor = old('supervisor_id', $subscriptionDelivery->supervisor_id ?? '');
$selectedMonth = old('month', isset($subscriptionDelivery) ? \Carbon\Carbon::parse($subscriptionDelivery->month)->format('Y-m') : (request('month') ?? now()->format('Y-m')));
$deliveredAmount = old('delivered_by_teacher', $subscriptionDelivery->delivered_by_teacher ?? '');
$expectedAmount = old('expected_from_teacher', $subscriptionDelivery->expected_from_teacher ?? '');
$notes = old('notes', $subscriptionDelivery->notes ?? '');

$user = Auth::user();
$isAdmin = $user->hasRole(['admin', 'general_manager']);
@endphp

<x-layouts.markaz-layout>
    <div class="space-y-6" x-data="{
    selectedTeacher: '{{ $selectedTeacher }}',
    selectedCircle: '{{ $selectedCircle }}',
    selectedSupervisor: '{{ $selectedSupervisor }}',
    selectedMonth: '{{ $selectedMonth }}',
    deliveredAmount: {{ $deliveredAmount ? $deliveredAmount : 'null' }},
    expectedAmount: {{ $expectedAmount ? $expectedAmount : 'null' }},
    notes: '{{ addslashes($notes) }}',
    isSubmitting: false,
    teachersData: {{ Js::from($teachersWithCircles) }},
    circlesData: {{ Js::from($circlesWithTeachers) }},
    adminCollected: {{ old('admin_collected_amount', $subscriptionDelivery->admin_collected_amount ?? 0) }},

    get currentTeacher() {
        return this.teachersData.find(t => t.id == this.selectedTeacher);
    },

    get teacherCircles() {
        return this.currentTeacher?.circles ?? [];
    },

    get currentCircle() {
        return this.teacherCircles.find(c => c.id == this.selectedCircle);
    },

    get circleSupervisors() {
        return this.currentCircle?.supervisors ?? [];
    },

    get circleTotalAmount() {
        return this.currentCircle?.circle_total ?? 0;
    },

    get autoExpected() {
        return Math.max(0, this.circleTotalAmount - (parseFloat(this.adminCollected) || 0));
    },

    get remaining() {
        if (!this.expectedAmount || !this.deliveredAmount) return 0;
        return this.expectedAmount - parseFloat(this.deliveredAmount || 0);
    },

    get selectedTeacherName() {
        return this.currentTeacher?.name?.replace(' (معلم رئيسي)', '').replace(' (معلم مساعد)', '') ?? '';
    },

onTeacherChange() {
    this.selectedCircle = '';
    this.selectedSupervisor = '';
    this.expectedAmount = null;
    this.$nextTick(() => {
        if (this.teacherCircles.length === 1) {
            this.selectedCircle = this.teacherCircles[0].id;
            this.onCircleChange();
        }
    });
},

onCircleChange() {
    this.$nextTick(() => {
        console.log('selectedCircle:', this.selectedCircle, typeof this.selectedCircle);
        console.log('teacherCircles:', this.teacherCircles);
        const circle = this.teacherCircles.find(c => c.id == this.selectedCircle);
        console.log('found circle:', circle);
        this.selectedSupervisor = circle?.default_supervisor_id ?? '';
        this.expectedAmount = circle?.expected_from_teacher ?? 0;
    });
},
}"
        `
        <!-- Header -->
        <div class="bg-[#0b3d2c] rounded-3xl p-8 text-white flex justify-between items-center shadow-xl">
            <div>
                <h1 class="text-3xl font-black mb-2">
                    {{ isset($subscriptionDelivery) ? 'تعديل تسليم الاشتراكات' : 'تسليم اشتراكات جديد' }}
                </h1>
                <p class="text-emerald-100/80 text-sm font-medium">
                    {{ isset($subscriptionDelivery) ? 'تعديل سجل تسليم اشتراكات للمشرف' : 'تسجيل تسليم اشتراكات الحلقة للمشرف' }}
                </p>
            </div>

            <a href="{{ route('subscription-deliveries.index') }}"
                class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl font-bold transition">
                العودة للمراجعة
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">
            <form action="{{ isset($subscriptionDelivery) ? route('subscription-deliveries.update', $subscriptionDelivery->id) : route('subscription-deliveries.store') }}"
                method="POST" @submit="isSubmitting = true" class="max-w-4xl mx-auto space-y-8">

                @csrf
                @if(isset($subscriptionDelivery))
                @method('PUT')
                @endif

                <!-- Selected Summary -->
                <div x-show="selectedCircle && selectedTeacher"
                    class="col-span-1 md:col-span-2 bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
                    <div class="bg-emerald-100 p-3 rounded-full text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-emerald-600 font-bold">
                            {{ isset($subscriptionDelivery) ? 'يتم تعديل التسليم:' : 'يتم تسجيل التسليم:' }}
                        </p>
                        <p class="text-lg font-black text-gray-800">
                            <span x-text="currentCircle?.name"></span>
                            <span class="text-gray-400 mx-2">•</span>
                            <span x-text="selectedTeacherName"></span>
                        </p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Teacher -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">المعلم المُسلِّم</label>
                        <select name="teacher_id" x-model="selectedTeacher" @change="onTeacherChange()"
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">
                            <option value="">اختر المعلم...</option>
                            <template x-for="teacher in teachersData" :key="teacher.id">
                                <option :value="teacher.id" x-text="teacher.name"></option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
                    </div>

                    <!-- Circle بناءً على المعلم -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">الحلقة</label>
                        <select name="circle_id" x-model="selectedCircle" @change="onCircleChange()"
                            :disabled="!selectedTeacher"
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold disabled:opacity-40 disabled:cursor-not-allowed">
                            <option value="">اختر الحلقة...</option>
                            <template x-for="circle in teacherCircles" :key="circle.id">
                                <option :value="circle.id" x-text="circle.name"></option>
                            </template>
                        </select>
                        <p x-show="selectedTeacher && teacherCircles.length === 0"
                            class="text-red-500 text-sm font-bold mt-2">
                            لا توجد حلقات مرتبطة بهذا المعلم
                        </p>
                        <x-input-error :messages="$errors->get('circle_id')" class="mt-2" />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Month -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">شهر الاشتراك</label>
                        <input type="month" name="month" x-model="selectedMonth" max="{{ now()->format('Y-m') }}"
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">
                        <x-input-error :messages="$errors->get('month')" class="mt-2" />
                    </div>

                    <!-- Supervisor -->
                    @if($isAdmin)
                    <!-- للأدمن: يظهر المشرفين الافتراضيين للحلقة + كل المشرفين -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">المشرف</label>
                        <select name="supervisor_id" x-model="selectedSupervisor"
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">
                            <option value="">اختر المشرف...</option>
                            <!-- أولاً: مشرفو الحلقة (معلّمون) -->
                            <optgroup label="مشرفو الحلقة" x-show="circleSupervisors.length > 0">
                                <template x-for="sup in circleSupervisors" :key="sup.id">
                                    <option :value="sup.id" x-text="sup.name + ' (مشرف الحلقة)'"></option>
                                </template>
                            </optgroup>
                            <!-- ثانياً: كل المشرفين -->
                            <optgroup label="جميع المشرفين">
                                @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">
                                    {{ $supervisor->name }}
                                </option>
                                @endforeach
                            </optgroup>
                        </select>
                        <x-input-error :messages="$errors->get('supervisor_id')" class="mt-2" />
                    </div>
                    @else
                    <!-- لغير الأدمن: يظهر اسم صاحب الحساب أو مشرف الحلقة الافتراضي -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">المشرف</label>
                        <div x-show="!selectedCircle" class="w-full bg-gray-100 border-none rounded-2xl p-4 text-gray-400 font-bold">
                            اختر الحلقة أولاً
                        </div>
                        <div x-show="selectedCircle && selectedSupervisor" class="w-full bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-emerald-800 font-bold flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="circleSupervisors.find(s => s.id == selectedSupervisor)?.name ?? '{{ $user->name }}'"></span>
                            <span class="text-xs bg-emerald-200 text-emerald-800 px-2 py-0.5 rounded-full">مشرف الحلقة</span>
                        </div>
                        <div x-show="selectedCircle && !selectedSupervisor" class="w-full bg-amber-50 border border-amber-200 rounded-2xl p-4 text-amber-700 font-bold flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>لا يوجد مشرف مرتبط بهذه الحلقة</span>
                        </div>
                        <input type="hidden" name="supervisor_id" x-model="selectedSupervisor">
                    </div>
                    @endif
                </div>

                <!-- إجمالي الحلقة + المتوقع -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 space-y-4">

                    <!-- المبلغ المتوقع (محسوب تلقائياً + قابل للتعديل) -->
                    <div class="flex justify-between items-center border-t border-amber-200 pt-4">
                        <div>
                            <p class="text-amber-700 text-sm font-bold">المبلغ المتوقع تسليمه</p>
                            <p class="text-xs text-amber-600">إجمالي الحلقة ناقص ما استلمه المدير</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" name="expected_from_teacher"
                                x-model="expectedAmount"
                                min="0" step="0.01"
                                class="w-36 bg-white border border-amber-300 rounded-xl px-3 py-2 text-center font-black text-amber-700 text-xl focus:ring-2 focus:ring-amber-500">
                            <span class="text-amber-600 font-bold text-sm">ريال</span>
                        </div>
                    </div>

                    <x-input-error :messages="$errors->get('expected_from_teacher')" class="mt-2" />
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Delivered Amount -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">المبلغ المُسلَّم فعليًا</label>
                        <div class="relative">
                            <input type="number" name="delivered_by_teacher" x-model="deliveredAmount"
                                min="0" step="0.01" required
                                class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-black text-emerald-600 text-xl">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">
                                ريال
                            </span>
                        </div>
                        <x-input-error :messages="$errors->get('delivered_by_teacher')" class="mt-2" />
                    </div>

                    <!-- Remaining Preview -->
                    <div x-show="expectedAmount !== null && deliveredAmount"
                        :class="remaining >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'"
                        class="border rounded-2xl p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold" :class="remaining >= 0 ? 'text-emerald-700' : 'text-red-700'">
                                <span x-text="remaining >= 0 ? 'المتبقي على المعلم' : 'فائض في التسليم'"></span>
                            </p>
                        </div>
                        <span class="text-xl font-black"
                            :class="remaining >= 0 ? 'text-emerald-700' : 'text-red-700'"
                            x-text="Math.abs(remaining).toFixed(2) + ' ريال'"></span>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3" x-model="notes"
                        placeholder="أي ملاحظات إضافية (مثل سبب وجود فرق في المبلغ)..."
                        class="w-full bg-gray-50 border-none rounded-3xl p-6 focus:ring-2 focus:ring-emerald-500">{{ $notes }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <!-- Submit -->
                <button type="submit" :disabled="isSubmitting || !selectedCircle || !selectedTeacher"
                    class="w-full bg-[#0a5c36] text-white rounded-4xl p-6 font-black text-xl hover:scale-[1.01] active:scale-95 transition-all shadow-2xl flex items-center justify-center gap-4 disabled:opacity-60">

                    <svg x-show="isSubmitting" class="animate-spin h-6 w-6" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                    </svg>

                    <span x-text="isSubmitting ? 'جاري الحفظ...' : '{{ isset($subscriptionDelivery) ? 'تحديث التسليم' : 'تأكيد وتسجيل التسليم' }}'"></span>
                </button>
            </form>
        </div>
    </div>
</x-layouts.markaz-layout>