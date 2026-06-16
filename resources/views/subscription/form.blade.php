<div class="space-y-6" x-data="{
    selectedCircle: '{{ old('circle_id', $subscription->circle_id ?? request('circle_id')) }}',
    selectedStudent: '{{ old('student_id', $subscription->student_id ?? request('student_id')) }}',
    selectedMonth: '{{ old('month', isset($subscription) ? \Carbon\Carbon::parse($subscription->month)->format('Y-m') : (request('month') ?? date('Y-m'))) }}',
    amount: {{ old('amount', $subscription->amount ?? 60) }},
    status: '{{ old('status', $subscription->status ?? 'مدفوع') }}',
    isSubmitting: false,

    students: {{ Js::from($students) }},
    circles: {{ Js::from($circles) }},
    prices: {{ Js::from($prices) }},

    init() {
        if (this.selectedCircle && this.selectedStudent && !{{ isset($subscription) ? 'true' : 'false' }}) {
            this.$nextTick(() => {
                this.updateDefaultAmount();
            });
        }
        this.$watch('status', () => this.applyStatusRules());
        this.applyStatusRules();
    },

    get filteredStudents() {
        if (!this.selectedCircle) return [];

        let filtered = this.students.filter(s => s.circle_id == this.selectedCircle);

        if (!this.selectedMonth) return filtered;

        return filtered.filter(s => {
            if (s.id == this.selectedStudent) return true;

            const hasSub = s.subscriptions && s.subscriptions.some(sub => sub.month && sub.month.startsWith(this.selectedMonth));
            return !hasSub;
        });
    },

    updateDefaultAmount() {
        const student = this.students.find(s => s.id == this.selectedStudent);

        if (student) {
            const eduStage = student.educational_stage ?? '';
            const circleLevel = student.circle?.level ?? '';

            const priceRule = this.prices.find(p =>
                p.education_stage == eduStage &&
                p.circle_level == circleLevel
            );

            this.amount = priceRule ? priceRule.amount : 60;
        } else {
            this.amount = 60;
        }

        this.applyStatusRules();
    },

    applyStatusRules() {
        if (this.status === 'معفي') {
            this.amount = 0;
        } else if (this.amount == 0) {
            this.updateDefaultAmount();
            }
    }
}">
    <!-- Header -->
    <div class="bg-[#0b3d2c] rounded-3xl p-8 text-white flex justify-between items-center shadow-xl">
        <div>
            <h1 class="text-3xl font-black mb-2">{{ isset($subscription) ? 'تعديل الاشتراك' : 'تسجيل اشتراك جديد' }}</h1>
            <p class="text-emerald-100/80 text-sm font-medium">
                {{ isset($subscription) ? 'تعديل سجل سداد مالي لطالب في المركز' : 'إضافة سجل سداد مالي لطالب في المركز' }}
            </p>
        </div>

        <a href="{{ route('subscriptions.index') }}"
            class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl font-bold transition">
            العودة للإحصائيات
        </a>

    </div>


    <!-- Form -->
    <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">

        <form action="{{ isset($subscription) ? route('subscriptions.update', $subscription->id) : route('subscriptions.store') }}"
            method="POST" @submit="isSubmitting = true" class="max-w-4xl mx-auto space-y-8">

            @csrf
            @if(isset($subscription))
            @method('PUT')
            @endif

            <!-- Selected Student Summary -->
            <div x-show="selectedStudent"
                class="col-span-1 md:col-span-2 bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
                <div class="bg-emerald-100 p-3 rounded-full text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-emerald-600 font-bold">
                        {{ isset($subscription) ? 'يتم تعديل الاشتراك للطالب:' : 'يتم تسجيل الاشتراك للطالب:' }}
                    </p>
                    <p class="text-lg font-black text-gray-800"
                        x-text="students.find(s => s.id == selectedStudent)?.name"></p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8 col-span-1 md:col-span-2">

                <!-- Circle -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">الحلقة</label>

                    <select name="circle_id" x-model="selectedCircle"
                        @change="selectedStudent=''; updateDefaultAmount()"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">

                        <option value="">اختر الحلقة...</option>

                        @foreach ($circles as $circle)
                        <option value="{{ $circle->id }}">
                            {{ $circle->name }}
                        </option>
                        @endforeach

                    </select>

                    <x-input-error :messages="$errors->get('circle_id')" class="mt-2" />
                </div>


                <!-- Student -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">الطالب</label>

                    <select name="student_id" x-model="selectedStudent" @change="updateDefaultAmount()"
                        :disabled="!selectedCircle"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4
focus:ring-2 focus:ring-emerald-500 font-bold
disabled:opacity-40 disabled:cursor-not-allowed">

                        <option value="">اختر الطالب...</option>

                        <template x-for="student in filteredStudents" :key="student.id">
                            <option :value="student.id" x-text="student.name"
                                :selected="student.id == selectedStudent"></option>
                        </template>

                    </select>

                    <p x-show="selectedCircle && filteredStudents.length === 0"
                        class="text-red-500 text-sm font-bold mt-2">
                        كل الطلاب لديهم اشتراك هذا الشهر ✅
                    </p>

                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />

                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Month -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">اشتراك شهر</label>

                    <input type="month" name="month" x-model="selectedMonth"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4
focus:ring-2 focus:ring-emerald-500 font-bold">

                    <x-input-error :messages="$errors->get('month')" class="mt-2" />

                </div>


                <!-- Amount -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">المبلغ</label>

                    <div class="relative">

                        <input type="number" min="0" step="0.01" name="amount" x-model="amount"
                            :disabled="status === 'معفي'"
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-black text-emerald-600 text-xl disabled:opacity-50 disabled:cursor-not-allowed">

                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            ج.م
                        </span>

                    </div>

                    <p x-show="status === 'معفي'" class="text-xs text-gray-400 mt-1">
                        الطالب معفي من الاشتراك — لا قيمة مطلوبة
                    </p>

                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />

                </div>


                <!-- Payment -->
                <div x-show="status !== 'معفي'">
                    <label class="block text-gray-700 font-bold mb-2">طريقة الدفع</label>

                    <select name="payment_method"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">

                        @php $paymentMethod = old('payment_method', $subscription->payment_method ?? null); @endphp

                        <option value="نقدي" {{ $paymentMethod == 'نقدي' ? 'selected' : '' }}>نقدي</option>
                        <option value="تحويل بنكي" {{ $paymentMethod == 'تحويل بنكي' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="أخرى" {{ $paymentMethod == 'أخرى' ? 'selected' : '' }}>أخرى</option>

                    </select>

                </div>

                <!-- Status -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">حالة السداد</label>

                    <select name="status" x-model="status"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">

                        <option value="مدفوع">مدفوع</option>
                        <option value="معفي">معفي</option>

                    </select>

                </div>
            </div>


            <!-- Notes -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">ملاحظات</label>

                <textarea name="notes" rows="3"
                    class="w-full bg-gray-50 border-none rounded-3xl p-6 focus:ring-2 focus:ring-emerald-500">{{ old('notes', $subscription->notes ?? '') }}</textarea>

            </div>


            <!-- Submit -->
            <button type="submit" :disabled="isSubmitting"
                class="w-full bg-[#0a5c36] text-white rounded-4xl p-6
font-black text-xl
hover:scale-[1.01]
active:scale-95
transition-all
shadow-2xl
flex items-center justify-center gap-4
disabled:opacity-60">

                <svg x-show="isSubmitting" class="animate-spin h-6 w-6" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                        fill="none" />
                </svg>

                <span x-text="isSubmitting ? 'جاري الحفظ...' : '{{ isset($subscription) ? 'تحديث الاشتراك' : 'تأكيد وتسجيل الاشتراك' }}'"></span>

            </button>

        </form>
    </div>

</div>