<x-layouts.markaz-layout>

    <div class="space-y-6" x-data="{
        selectedCircle: '',
        selectedStudent: '',
        selectedMonth: '{{ date('Y-m') }}',
        amount: 60,
        isSubmitting: false,
    
        students: {{ Js::from($students) }},
        circles: {{ Js::from($circles) }},
        prices: {{ Js::from($prices) }},
    
        get filteredStudents() {
    
            if (!this.selectedCircle) return [];
    
            let filtered = this.students.filter(
                s => s.circle_id == this.selectedCircle
            );
    
            if (!this.selectedMonth) return filtered;
    
            return filtered.filter(s => {
    
                const hasSub = s.subscriptions?.some(sub =>
                    sub.month?.startsWith(this.selectedMonth)
                );
    
                return !hasSub;
            });
        },
    
        updateDefaultAmount() {
    
            const student = this.students.find(
                s => s.id == this.selectedStudent
            );
    
            if (!student) {
                this.amount = 60;
                return;
            }
    
            const eduLevel =
                student.education_level?.toLowerCase() ?? '';
    
            const circleLevel =
                student.circle?.level?.toLowerCase() ?? '';
    
            const priceRule = this.prices.find(p =>
    
                (p.education_level?.toLowerCase() ?? '') === eduLevel &&
                (p.circle_level?.toLowerCase() ?? '') === circleLevel
            );
    
            this.amount = priceRule?.amount ?? 60;
        }
    }">

        <!-- Header -->
        <div class="bg-[#0b3d2c] rounded-3xl p-8 text-white flex justify-between items-center shadow-xl">

            <div>
                <h1 class="text-3xl font-black mb-2">تسجيل اشتراك جديد</h1>
                <p class="text-emerald-100/80 text-sm font-medium">
                    إضافة سجل سداد مالي لطالب في المركز
                </p>
            </div>

            <a href="{{ route('subscriptions.index') }}"
                class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl font-bold transition">
                العودة للإحصائيات
            </a>

        </div>


        <!-- Form -->
        <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">

            <form action="{{ route('subscriptions.store') }}" method="POST" @submit="isSubmitting = true"
                class="max-w-4xl mx-auto space-y-8">

                @csrf

                <div class="grid md:grid-cols-2 gap-8">

                    <!-- Circle -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">الحلقة</label>

                        <select name="circle_id" x-model="selectedCircle"
                            @change="selectedStudent=''; updateDefaultAmount()"
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">

                            <option value="">اختر الحلقة...</option>

                            @foreach ($circles as $circle)
                                <option value="{{ $circle->id }}">
                                    {{ $circle->name }} ({{ $circle->level }})
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
                                <option :value="student.id" x-text="student.name"></option>
                            </template>

                        </select>

                        <p x-show="selectedCircle && filteredStudents.length === 0"
                            class="text-red-500 text-sm font-bold mt-2">
                            كل الطلاب لديهم اشتراك هذا الشهر ✅
                        </p>

                        <x-input-error :messages="$errors->get('student_id')" class="mt-2" />

                    </div>


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
                                class="w-full bg-gray-50 border-none rounded-2xl p-4
focus:ring-2 focus:ring-emerald-500
font-black text-emerald-600 text-xl">

                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                ج.م
                            </span>

                        </div>

                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />

                    </div>


                    <!-- Payment -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">طريقة الدفع</label>

                        <select name="payment_method"
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">

                            <option value="cash">نقدي</option>
                            <option value="transfer">تحويل بنكي</option>
                            <option value="other">أخرى</option>

                        </select>

                    </div>


                    <!-- Status -->
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">حالة السداد</label>

                        <select name="status"
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">

                            <option value="مدفوع">مدفوع</option>
                            <option value="غير مدفوع">غير مدفوع</option>
                            <option value="ملغي">ملغي</option>

                        </select>

                    </div>

                </div>


                <!-- Notes -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">ملاحظات</label>

                    <textarea name="notes" rows="3"
                        class="w-full bg-gray-50 border-none rounded-3xl p-6 focus:ring-2 focus:ring-emerald-500"></textarea>

                </div>


                <!-- Submit -->
                <button type="submit" :disabled="isSubmitting"
                    class="w-full bg-[#0a5c36] text-white rounded-[2rem] p-6
font-black text-xl
hover:scale-[1.01]
active:scale-95
transition-all
shadow-2xl
flex items-center justify-center gap-4
disabled:opacity-60">

                    <!-- Spinner -->
                    <svg x-show="isSubmitting" class="animate-spin h-6 w-6" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                            fill="none" />
                    </svg>

                    <span x-text="isSubmitting ? 'جاري الحفظ...' : 'تأكيد وتسجيل الاشتراك'"></span>

                </button>

            </form>
        </div>

    </div>
</x-layouts.markaz-layout>
