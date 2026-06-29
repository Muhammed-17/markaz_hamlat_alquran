<div class="space-y-6" x-data="formData()">
    <!-- Header -->
    <div class="bg-[#0b3d2c] rounded-3xl p-8 text-white flex justify-between items-center shadow-xl">
        <div>
            <h1 class="text-3xl font-black mb-2">تسجيل اشتراك جديد</h1>
            <p>إضافة سجل سداد مالي لطالب في المركز</p>
        </div>

        <a href="{{ route('subscriptions.index') }}"
            class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl font-bold transition">
            العودة للإحصائيات
        </a>

    </div>


    <!-- Form -->
    <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">

        <form action="{{ route('subscriptions.store') }}" method="POST" @submit="isSubmitting = true" class="max-w-4xl mx-auto space-y-8">
            @csrf
            <!-- Selected Student Summary -->
            <div x-show="selectedStudent || '{{ request('student_id') }}'"
                class="col-span-1 md:col-span-2 bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
                <div class="bg-emerald-100 p-3 rounded-full text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">
                        يتم تسجيل الاشتراك للطالب في حلقة:
                        <span class="font-bold text-emerald-700"
                            x-text="circles.find(c => c.id == selectedCircle)?.name"></span>
                    </p>
                    <p class="text-lg font-black text-gray-800"
                        x-text="students.find(s => s.id == selectedStudent)?.name"></p>
                </div>
            </div>
            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- ✅ حقل المعلم - في بداية الـ Form --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            @php
            $isAdmin = auth()->user()->hasRole(['admin', 'general_manager']);
            $currentUser = auth()->user();
            @endphp

            {{-- ✅ للأدمن: قائمة منسدلة بجميع المشرفين والمدراء والمعلمين --}}
            @if($isAdmin)
            <div>
                <label class="block text-gray-700 font-bold mb-2">المعلم / المسؤول</label>

                @php
                $teacherOptions = $teachers->map(fn($t) => [
                'value' => $t->id,
                'label' => $t->name . ($t->roles->isNotEmpty()
                ? ' (' . $t->roles->pluck('name')->map(fn($r) => ['supervisor'=>'مشرف','manager'=>'مدير','general_manager'=>'مدير عام','teacher'=>'معلم'][$r] ?? $r)->join('، ') . ')'
                : '')
                ])->values()->toArray();
                @endphp

                <x-searchable-select
                    name="teacher_id"
                    placeholder="اختر المعلم..."
                    search-placeholder="ابحث عن معلم..."
                    :options="json_encode($teacherOptions)"
                    :default-value="old('teacher_id', '')" />

                <p class="text-xs text-gray-400 mt-1">يتم تسجيل الاشتراك باسم المعلم المختار...</p>
                <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
            </div>
            @else
            {{-- ✅ لغير الأدمن: عرض اسم المستخدم الحالي فقط (hidden input) --}}
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold">المعلم / المسؤول</p>
                    <p class="font-black text-gray-800">{{ $currentUser->name }}</p>
                </div>
            </div>
            <input type="hidden" name="teacher_id" value="{{ $currentUser->id }}">
            @endif

            <div class="grid md:grid-cols-2 gap-8 col-span-1 md:col-span-2">

                {{-- Circle --}}
                <div>
                    <label class="block text-gray-700 font-bold mb-2">الحلقة</label>

                    @php
                    $circleOptions = $circles->map(fn($c) => [
                    'value' => $c->id,
                    'label' => $c->name,
                    ])->values()->toArray();
                    @endphp

                    {{-- Circle --}}
                    <x-searchable-select
                        name="circle_id"
                        placeholder="اختر الحلقة..."
                        search-placeholder="ابحث عن حلقة..."
                        :options="json_encode($circleOptions)"
                        default-value="" />
                    <x-input-error :messages="$errors->get('circle_id')" class="mt-2" />
                </div>


                {{-- Student --}}
                <div>
                    <label class="block text-gray-700 font-bold mb-2">الطالب</label>

                    <x-searchable-select
                        name="student_id"
                        placeholder="اختر الطالب..."
                        search-placeholder="ابحث عن طالب..."
                        options="[]"
                        default-value="" />

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

                    <input type="month" name="month" x-model="selectedMonth" class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">

                    <x-input-error :messages="$errors->get('month')" class="mt-2" />

                </div>

                {{-- ─── المبلغ (نسخة واحدة فقط) ─────────────────────── --}}
                <div>
                    <label class="block text-gray-700 font-bold mb-2">المبلغ</label>

                    <div class="relative">
                        {{--
                            عنصر واحد فقط مفعّل (enabled) في كل لحظة، فيُرسل اسم
                            "amount" مرة واحدة فقط مهما كانت حالة "status".
                            - لما الحالة ليست "معفي": الحقل الرقمي مفعّل ويرسل القيمة الحقيقية.
                            - لما الحالة "معفي": الحقل الرقمي مُعطّل (disabled لا يُرسل قيمته)،
                              ويتولى الحقل المخفي إرسال 0 بدلاً منه.
                        --}}
                        <input type="number" min="0" step="0.01" name="amount" x-model="amount"
                            :disabled="status === 'معفي'"
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-black text-emerald-600 text-xl disabled:opacity-50 disabled:cursor-not-allowed">

                        <input type="hidden" name="amount" value="0" :disabled="status !== 'معفي'">

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

                    <select name="payment_method" :disabled="status === 'معفي'"
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500 font-bold">

                        @php $paymentMethod = old('payment_method'); @endphp

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
                    class="w-full bg-gray-50 border-none rounded-3xl p-6 focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>

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

                <span x-text="isSubmitting ? 'جاري الحفظ...' : 'تأكيد وتسجيل الاشتراك'"></span>
            </button>

        </form>
    </div>

</div>