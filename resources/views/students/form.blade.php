@php
$isEdit = isset($student) && $student->exists;
$construction = $construction ?? ($student->constructionDetail ?? null);
$itqan = $itqan ?? ($student->itqanDetail ?? null);
$ibda = $ibda ?? ($student->ibdaDetail ?? null);

$regMonth = now()->month;
$isNextYearReg = ($regMonth >= 7 && $regMonth <= 9);

    $gradePromotion=[ 'الأول'=> 'الثاني',
    'الثاني' => 'الثالث',
    'الثالث' => 'الرابع',
    'الرابع' => 'الخامس',
    'الخامس' => 'السادس',
    'السادس' => 'لا يوجد',
    ];

    $savedGrade = isset($student) ? ($student->school_grade ?? '') : '';
    $suggestedGrade = ($isNextYearReg && !$isEdit && isset($gradePromotion[$savedGrade]))
    ? $gradePromotion[$savedGrade]
    : $savedGrade;

    $guardianData = null;
    if (isset($student) && $student->guardian_id && $student->guardian) {
    $guardianData = [
    'id' => $student->guardian->id,
    'name' => $student->guardian->name,
    'mobile' => $student->guardian->mobile ?? '',
    'email' => $student->guardian->email ?? '',
    'is_active' => ($student->guardian->status ?? '') === 'active',
    ];
    }

    $guardianQueryName = old(
    'guardian_name',
    optional(\App\Models\User::find(old('guardian_id', isset($student) ? $student->guardian_id : '')))->name ?? ''
    );
    @endphp

    <div id="student-form" class="space-y-8">

        @if ($errors->any())
        <div class="bg-red-50 border border-red-100 rounded-2xl p-5 space-y-3">
            <div class="flex items-center justify-between gap-3">
                <p class="text-red-700 font-bold text-sm">تعذر حفظ النموذج. راجع الحقول المطلوبة ثم أعد المحاولة.</p>
            </div>
            <ul class="text-red-700 text-sm list-disc pr-5 space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- ───────────────── بيانات المشرف والالتحاق ───────────────── -->
        <div id="step-1" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                <div class="p-3 bg-[#e8f5ed] text-[#0a5c36] rounded-2xl text-xl">📋</div>
                <div>
                    <h2 class="text-xl font-black text-gray-800">بيانات المشرف والالتحاق</h2>
                    <p class="text-xs text-gray-400 mt-1">تحديد المشرف المسؤول ومستوى دخول الطالب المبدئي</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">
                        المشرف المقيم / مسجل البيانات <span class="text-red-500">*</span>
                    </label>

                    @if($lockedSupervisor ?? false)
                    <div class="w-full p-3 bg-gray-100 border border-gray-200 rounded-2xl text-sm font-medium text-gray-700 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                        {{ $lockedSupervisor->user?->name ?? $lockedSupervisor->name }}
                    </div>
                    <input type="hidden" name="supervisor_id" value="{{ $lockedSupervisor->id }}">
                    @else
                    <select name="supervisor_id" data-field="supervisor_id"
                        class="w-full p-3 border rounded-2xl text-sm font-medium focus:outline-none focus:ring-1 transition-all">
                        <option value="{{ old('supervisor_id', $student->supervisor_id ?? '') == '' ? 'selected' : '' }}">
                            -- اختر المشرف --
                        </option>
                        @foreach ($supervisors ?? [] as $supervisor)
                        @php
                        $roleName = $supervisor->user?->roles?->first()?->name ?? '';
                        $roleLabel = match($roleName) {
                        'admin' => 'المسؤول',
                        'general_manager' => 'المدير العام',
                        'manager' => 'مدير فرع',
                        'supervisor' => 'مشرف',
                        default => 'مشرف',
                        };
                        @endphp
                        <option value="{{ $supervisor->id }}"
                            {{ old('supervisor_id', $student->supervisor_id ?? '') == $supervisor->id ? 'selected' : '' }}>
                            {{ $supervisor->user?->name ?? $supervisor->name }} ({{ $roleLabel }})
                        </option>
                        @endforeach
                    </select>

                    @if(($supervisors ?? collect())->isEmpty())
                    <p class="text-xs text-amber-500 mt-1">لا يوجد مشرفون متاحون</p>
                    @endif
                    @endif

                    <span data-error-for="supervisor_id" class="hidden text-red-500 text-xs font-medium">هذا الحقل مطلوب</span>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">تاريخ المقابلة / التسجيل</label>
                    <input type="date" name="join_date" data-field="join_date"
                        value="{{ old('join_date', $student->join_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                        class="w-full p-3 border rounded-2xl text-sm font-medium focus:outline-none focus:ring-1 transition-all">
                    <span data-error-for="join_date" class="hidden text-red-500 text-xs font-medium">هذا الحقل مطلوب</span>
                </div>
            </div>
        </div>

        <!-- ───────────────── البيانات الأساسية ───────────────── -->
        <div id="step-2" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                    <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">👤</div>
                    <div>
                        <h2 class="text-xl font-black text-[#0a5c36]">البيانات الأساسية</h2>
                        <p class="text-xs text-gray-400 mt-1">معلومات الطالب الشخصية والاتصال</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">مقدم طلب التسجيل <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        @foreach(['الأم', 'الأب', 'الطالب', 'أخرى'] as $applicantOption)
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                            <input type="radio" name="applicant" value="{{ $applicantOption }}" data-field="applicant"
                                @checked(old('applicant', $student->applicant ?? '') == $applicantOption)
                            class="rounded-full text-[#0a5c36] focus:ring-[#0a5c36]">
                            <span>{{ $applicantOption === 'الطالب' ? 'الطالب نفسه' : $applicantOption }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('applicant')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                    <input type="text" name="applicant_other" data-field="applicant_other"
                        value="{{ old('applicant_other', $student->applicant_other ?? '') }}"
                        data-show-when="applicant=أخرى"
                        placeholder="يرجى تحديد مقدم الطلب..."
                        class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                        style="display:none;">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">كود الطالب</label>
                        <input type="text" name="student_code" data-field="student_code"
                            value="{{ old('student_code', $student->student_code ?? $generatedCode ?? '') }}"
                            placeholder="كود الطالب"
                            readonly
                            class="w-full p-3 bg-gray-100 border border-gray-200 rounded-2xl text-sm font-medium text-gray-500 cursor-not-allowed">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">اسم الطالب (رباعيًّا) <span class="text-red-500">*</span></label>
                        <input type="text" name="name" data-field="name" placeholder="الاسم الرباعي كاملًا"
                            value="{{ old('name', $student->name ?? '') }}"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                            required>
                        @error('name')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">النوع <span class="text-red-500">*</span></label>
                        <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            @foreach(['ذكر', 'أنثى'] as $genderOption)
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="gender" value="{{ $genderOption }}" data-field="gender"
                                    @checked(old('gender', $student->gender ?? '') == $genderOption)
                                class="text-[#0a5c36] focus:ring-[#0a5c36]" required>
                                <span>{{ $genderOption }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('gender')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">تاريخ الميلاد</label>
                        <input type="date" name="date_of_birth" data-field="date_of_birth"
                            value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d') ?? '') }}"
                            max="{{ now()->subMonths(30)->format('Y-m-d') }}"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        @error('date_of_birth')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">العنوان التفصيلي <span class="text-red-500">*</span></label>
                        <input type="text" name="address" data-field="address"
                            value="{{ old('address', $student->address ?? '') }}"
                            placeholder="مثال: الشرقية - ههيا - قرية صبيح"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                            required>
                        @error('address')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">المركز فرع <span class="text-red-500">*</span></label>
                        <select name="center_id" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                            @foreach($centers ?? [] as $center)
                            <option value="{{ $center->id }}"
                                @selected(old('center_id', $student->center_id ?? '') == $center->id)>
                                {{ $center->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('center_id')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-100 my-6"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">رقم الواتساب للمتابعة</label>
                        <input type="tel" name="whatsapp_number" data-field="whatsapp_number"
                            value="{{ old('whatsapp_number', $student->whatsapp_number ?? '') }}"
                            id="whatsappInput"
                            placeholder="01xxxxxxxxx"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        @error('whatsapp_number')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">رقم اتصال إضافي</label>
                        <input type="tel" name="second_phone" data-field="second_phone"
                            value="{{ old('second_phone', $student->second_phone ?? '') }}"
                            placeholder="01xxxxxxxxx"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        @error('second_phone')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach([
                    ['name' => 'whatsapp_owner', 'label' => 'صاحب رقم الواتساب', 'other_name' => 'whatsapp_owner_other'],
                    ['name' => 'additional_contact_owner', 'label' => 'صاحب الرقم الإضافي', 'other_name' => 'additional_contact_owner_other'],
                    ] as $ownerField)
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">{{ $ownerField['label'] }}</label>
                        <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            @foreach(['الأم', 'الأب', 'الطالب', 'أخرى'] as $ownerOption)
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="{{ $ownerField['name'] }}" value="{{ $ownerOption }}"
                                    data-field="{{ $ownerField['name'] }}"
                                    @checked(old($ownerField['name'], $student->{$ownerField['name']} ?? '') == $ownerOption)
                                class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                <span>{{ $ownerOption }}</span>
                            </label>
                            @endforeach
                        </div>
                        <input type="text" name="{{ $ownerField['other_name'] }}"
                            value="{{ old($ownerField['other_name'], $student->{$ownerField['other_name']} ?? '') }}"
                            data-show-when="{{ $ownerField['name'] }}=أخرى"
                            placeholder="يرجى التحديد..."
                            class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36]"
                            style="display:none;">
                    </div>
                    @endforeach
                </div>

                {{-- ───── ولي الأمر ───── --}}
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">
                        تحديد ولي الأمر <span class="text-red-500">*</span>
                    </label>

                    <div class="relative" x-data="guardianSearch()">

                        {{-- حقل البحث --}}
                        <div class="relative">
                            <input type="text" id="guardianSearchInput"
                                x-model="query"
                                @input.debounce.400ms="search()"
                                @focus="if(query.length >= 2) search()"
                                @click.outside="results = []"
                                placeholder="ابحث بالاسم، الهاتف، الإيميل، أو رقم الحساب..."
                                autocomplete="off"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">

                            {{-- مؤشر التحميل --}}
                            <div x-show="searching" class="absolute left-3 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- نتائج البحث --}}
                        <div x-show="results.length > 0"
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">
                            <template x-for="guardian in results" :key="guardian.id">
                                <div @click="select(guardian)"
                                    class="px-4 py-3 hover:bg-emerald-50 cursor-pointer flex justify-between items-center border-b border-gray-50 last:border-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono bg-gray-100 text-gray-500 px-2 py-0.5 rounded-lg"
                                            x-text="'#' + guardian.id"></span>
                                        <span class="font-bold text-gray-800 text-sm" x-text="guardian.name"></span>
                                        {{-- ✅ مؤشر حالة الحساب --}}
                                        <span x-show="guardian.is_active"
                                            class="text-xs bg-emerald-100 text-emerald-600 px-1.5 py-0.5 rounded-md font-medium">نشط</span>
                                        <span x-show="!guardian.is_active"
                                            class="text-xs bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded-md font-medium">غير نشط</span>
                                    </div>
                                    <div class="flex flex-col items-end gap-0.5">
                                        <span class="text-gray-500 text-xs font-medium"
                                            x-text="guardian.mobile ? '📱 ' + guardian.mobile : ''"></span>
                                        <span class="text-gray-400 text-xs"
                                            x-text="guardian.email ? '🔑 ' + guardian.email : ''"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- ✅ رسالة لا نتائج --}}
                        <div x-show="noResults && !searching && query.length >= 2"
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-2xl shadow-lg p-3 text-sm text-gray-500 text-center">
                            لم يُعثر على ولي أمر — يمكنك إضافة حساب جديد
                        </div>

                        {{-- ولي الأمر المختار --}}
                        <div x-show="selected"
                            class="mt-2 px-3 py-2 bg-emerald-50 border border-emerald-100 rounded-xl flex justify-between items-center">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-mono bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-lg"
                                    x-text="selected?.id ? '#' + selected.id : ''"></span>
                                <span class="text-emerald-700 text-sm font-bold"
                                    x-text="'✅ ' + (selected?.name ?? '')"></span>
                                {{-- ✅ تحذير إذا الحساب غير نشط --}}
                                <span x-show="selected?.is_active === false"
                                    class="text-xs bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded-md font-medium">
                                    ⚠️ غير نشط
                                </span>
                                <template x-if="selected?.mobile">
                                    <span class="text-emerald-500 text-xs font-medium"
                                        x-text="'· 📱 ' + selected.mobile"></span>
                                </template>
                                <template x-if="selected?.email && !selected?.mobile">
                                    <span class="text-emerald-400 text-xs"
                                        x-text="'· 🔑 ' + selected.email"></span>
                                </template>
                            </div>
                            <button type="button" @click="clear()"
                                class="text-gray-400 hover:text-red-500 text-xs mr-2">✕ تغيير</button>
                        </div>

                        {{-- أزرار الإضافة --}}
                        <div x-show="!selected" class="mt-2 flex gap-4">
                            <button type="button" @click="createNew()"
                                class="text-sm text-emerald-600 font-bold hover:underline">
                                + إضافة ولي أمر جديد
                            </button>
                            <button type="button" @click="skipGuardian()"
                                class="text-sm text-gray-500 font-bold hover:underline">
                                تسجيل بدون ولي أمر حاليًا
                            </button>
                        </div>
                    </div>

                    {{-- hidden input --}}
                    <input type="hidden" name="guardian_id" id="guardianIdInput"
                        value="{{ old('guardian_id', $student->guardian_id ?? '') }}">

                    @error('guardian_id')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror

                    {{-- حقول إنشاء حساب جديد --}}
                    <div id="newGuardianFields" class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2" style="display:none;">

                        {{-- ✅ تنبيه وجود حساب مطابق --}}
                        <div id="guardianExistsAlert" class="md:col-span-2 hidden">
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700 font-medium">
                                ⚠️ يوجد حساب مطابق لهذا الإيميل أو الهاتف —
                                <button type="button" onclick="useExistingGuardian()"
                                    class="underline font-bold">استخدامه بدلاً من إنشاء جديد</button>
                            </div>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700">
                                اسم ولي الأمر <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="guardian_name" id="guardianNameInput"
                                value="{{ old('guardian_name') }}"
                                placeholder="اسم ولي الأمر كاملًا"
                                class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">
                                البريد الإلكتروني <span class="text-red-500">*</span>
                            </label>
                            {{-- ✅ إضافة @blur لفحص وجود الحساب --}}
                            <input type="email" name="parent_email" id="parentEmailInput"
                                value="{{ old('parent_email') }}"
                                placeholder="example@email.com"
                                onblur="checkGuardianExists()"
                                class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                            {{-- ✅ نتيجة الفحص --}}
                            <div id="emailCheckResult" class="hidden text-xs font-medium mt-1"></div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">كلمة المرور</label>
                            {{-- ✅ بدون value لمنع ظهورها في الـ source --}}
                            <input type="password" name="password" id="passwordInput"
                                placeholder="اتركها فارغة للتوليد التلقائي"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ───────────────── البيانات الدراسية ───────────────── -->
        <div id="step-3" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">🎓</div>
                <div>
                    <h2 class="text-xl font-black text-[#0a5c36]">البيانات الدراسية</h2>
                    <p class="text-xs text-gray-400 mt-1">المرحلة والمؤسسة التعليمية الحالية</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">المرحلة الدراسية <span class="text-red-500">*</span></label>
                    <select name="educational_stage" data-field="educational_stage"
                        class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none"
                        required>
                        <option value="" @selected(old('educational_stage', $student->educational_stage ?? '') == '')>-- اختر المرحلة --</option>
                        @foreach(['تمهيدي', 'حضانة', 'ابتدائي', 'اعدادي', 'ثانوي', 'جامعي', 'خريج'] as $stage)
                        <option value="{{ $stage }}" @selected(old('educational_stage', $student->educational_stage ?? '') == $stage)>
                            {{ $stage }}
                        </option>
                        @endforeach
                    </select>
                    @error('educational_stage')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">نوع التعليم <span class="text-red-500">*</span></label>
                    <select name="education_type" data-field="education_type"
                        class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none"
                        required>
                        <option value="" @selected(in_array(old('education_type', $student->education_type ?? ''), ['', null, 'غير محدد']))>-- اختر النوع --</option>
                        <option value="أزهري" @selected(old('education_type', $student->education_type ?? '') == 'أزهري')>أزهري</option>
                        <option value="عام (تربية وتعليم)" @selected(old('education_type', $student->education_type ?? '') == 'عام (تربية وتعليم)')>عام (تربية وتعليم)</option>
                    </select>
                    @error('education_type')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">الصف الدراسي <span class="text-red-500">*</span></label>
                    @if($isNextYearReg && !$isEdit && isset($gradePromotion[$savedGrade]))
                    <div class="mb-1 text-xs text-amber-600 font-semibold bg-amber-50 px-3 py-1.5 rounded-xl border border-amber-100">
                        ⚠️ موسم تسجيل — الصف المقترح للعام الجديد: {{ $gradePromotion[$savedGrade] }}
                    </div>
                    @endif
                    <select name="school_grade" data-field="school_grade"
                        class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none"
                        required>
                        <option value="" @selected(old('school_grade', $suggestedGrade)=='' )>-- اختر الصف --</option>
                        @foreach(['لا يوجد', 'الأول', 'الثاني', 'الثالث', 'الرابع', 'الخامس', 'السادس', 'دراسات عليا'] as $grade)
                        <option value="{{ $grade }}" @selected(old('school_grade', $suggestedGrade)==$grade)>{{ $grade }}</option>
                        @endforeach
                    </select>
                    @error('school_grade')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">المؤسسة التعليمية <span class="text-red-500">*</span></label>
                    <input type="text" name="previous_school" data-field="previous_school"
                        value="{{ old('previous_school', $student->previous_school ?? '') }}"
                        placeholder="اسم المؤسسة التعليمية بالكامل"
                        class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                        required>
                    @error('previous_school')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- ───────────────── بيانات الرعاية الطلابية ───────────────── -->
        <div id="step-4" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">💚</div>
                <div>
                    <h2 class="text-xl font-black text-[#0a5c36]">بيانات الرعاية الطلابية</h2>
                    <p class="text-xs text-gray-400 mt-1">الحالة الصحية، السلوكية، والسمات الشخصية</p>
                </div>
            </div>

            @foreach([
            ['field' => 'health_status', 'label' => 'الحالة الصحية للطالب', 'options' => [['value' => 'طبيعية', 'label' => 'طبيعية (الحمد لله)'], ['value' => 'أخرى', 'label' => 'أخرى']], 'other_field' => 'health_status_other', 'other_placeholder' => 'يرجى توضيح الحالة الصحية...'],
            ['field' => 'learning_difficulties', 'label' => 'صعوبات التعلم', 'options' => [['value' => 'لا يوجد', 'label' => 'لا يوجد (الحمد لله)'], ['value' => 'أخرى', 'label' => 'أخرى']], 'other_field' => 'learning_difficulties_other', 'other_placeholder' => 'يرجى توضيح صعوبات التعلم...'],
            ['field' => 'personal_traits', 'label' => 'السمات الشخصية', 'options' => [['value' => 'لا يوجد', 'label' => 'لا يوجد'], ['value' => 'أخرى', 'label' => 'أخرى']], 'other_field' => 'personal_traits_other', 'other_placeholder' => 'يرجى تحديد السمات البارزة (عنيد، خجول...)'],
            ] as $radioGroup)
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">{{ $radioGroup['label'] }} <span class="text-red-500">*</span></label>
                <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    @foreach($radioGroup['options'] as $opt)
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="{{ $radioGroup['field'] }}" value="{{ $opt['value'] }}"
                            data-field="{{ $radioGroup['field'] }}"
                            @checked(old($radioGroup['field'], $student->{$radioGroup['field']} ?? '') == $opt['value'])
                        required>
                        <span>{{ $opt['label'] }}</span>
                    </label>
                    @endforeach
                </div>
                @error($radioGroup['field'])
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                @enderror
                <input type="text" name="{{ $radioGroup['other_field'] }}"
                    value="{{ old($radioGroup['other_field'], $student->{$radioGroup['other_field']} ?? '') }}"
                    data-show-when="{{ $radioGroup['field'] }}=أخرى"
                    placeholder="{{ $radioGroup['other_placeholder'] }}"
                    class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                    style="display:none;">
            </div>
            @endforeach

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">الهواية المفضلة <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    @php
                    $savedHobbies = old('hobbies', $student->hobbies ?? []);
                    if (is_string($savedHobbies)) $savedHobbies = json_decode($savedHobbies, true) ?? [];
                    $hobbiesList = ['كرة القدم', 'الكاراتيه', 'الرسم', 'البرمجة والألعاب الإلكترونية', 'الأشغال اليدوية', 'القراءة والإطلاع', 'أخرى'];
                    @endphp
                    @foreach($hobbiesList as $hobby)
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="checkbox" name="hobbies[]" value="{{ $hobby }}"
                            data-field="hobbies"
                            @if($hobby==='أخرى' ) id="hobbyOtherCheckbox" @endif
                            @checked(in_array($hobby, $savedHobbies))
                            class="rounded text-[#0a5c36] focus:ring-[#0a5c36]">
                        <span>{{ $hobby === 'البرمجة والألعاب الإلكترونية' ? 'البرمجة والألعاب' : ($hobby === 'القراءة والإطلاع' ? 'القراءة' : $hobby) }}</span>
                    </label>
                    @endforeach
                </div>
                <input type="text" name="hobby_other" data-field="hobby_other"
                    value="{{ old('hobby_other', $student->hobby_other ?? '') }}"
                    id="hobbyOtherInput"
                    placeholder="يرجى ذكر الهواية الإضافية..."
                    class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                    style="display:none;">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">حالة خروج الطالب من المركز <span class="text-red-500">*</span></label>
                <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    @foreach(['بمفرده', 'مع ولي الأمر أو أحد الأقارب'] as $exitOption)
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="student_exit_status" value="{{ $exitOption }}"
                            data-field="student_exit_status"
                            @checked(old('student_exit_status', $student->student_exit_status ?? '') == $exitOption)
                        required>
                        <span>{{ $exitOption }}</span>
                    </label>
                    @endforeach
                </div>
                @error('student_exit_status')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                @enderror
                <input type="text" name="exit_details" data-field="exit_details"
                    value="{{ old('exit_details', $student->exit_details ?? '') }}"
                    data-show-when="student_exit_status=مع ولي الأمر أو أحد الأقارب"
                    placeholder="يرجى توضيح مع من ستخرج..."
                    class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                    style="display:none;">
            </div>

            <div x-data="{ selectedLevel: '{{ old('center_entry_level', $student->center_entry_level ?? 'construction') }}' }">

                <!-- ───────────────── تقييم التلاوة ───────────────── -->
                <div id="step-5" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                        <div class="p-3 bg-[#e8f5ed] text-[#0a5c36] rounded-2xl text-xl">🎤</div>
                        <div>
                            <h2 class="text-xl font-black text-gray-800">تقييم التلاوة وتحديد مستوى الالتحاق</h2>
                            <p class="text-xs text-gray-400 mt-1">تحديد المسار الفني والتعليمي للطالب بناء على تقييم الشيخ المختبر</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">مستوى القراءة من المصحف <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            @foreach([
                            ['value' => 'مبتدئ', 'desc' => 'لا يقرأ'],
                            ['value' => 'مقبول', 'desc' => 'يقرأ ببطء'],
                            ['value' => 'متمكن', 'desc' => 'بدون أحكام'],
                            ['value' => 'متقن', 'desc' => 'توجد أحكام'],
                            ] as $readingOption)
                            <label class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-[#0a5c36]/50 transition-all text-sm font-semibold text-gray-600">
                                <input type="radio" name="reading" value="{{ $readingOption['value'] }}"
                                    data-field="reading"
                                    @checked(old('reading', $student->reading ?? '') == $readingOption['value'])
                                class="text-[#0a5c36] focus:ring-[#0a5c36]" required>
                                <span>{{ $readingOption['value'] }} <span class="text-xs text-gray-400 font-normal">({{ $readingOption['desc'] }})</span></span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4 pt-2">
                        <label class="block text-sm font-bold text-gray-700">
                            اختر مستوى تحضير أو التحاق الطالب بعد الاختبار <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex flex-col p-4 border rounded-2xl cursor-pointer hover:border-[#0a5c36] transition-all border-gray-100 bg-gray-50">
                                <div class="flex items-center gap-2 font-bold text-[#0a5c36]">
                                    <input type="radio" name="center_entry_level" value="construction"
                                        x-model="selectedLevel" data-field="center_entry_level" required
                                        class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                    <span>🌱 مستوى البناء</span>
                                </div>
                                <span class="text-xs text-gray-500 mt-2 mr-5">الحلقات التأسيسية وحفظ الأجزاء المنتظمة</span>
                            </label>
                            <label class="flex flex-col p-4 border rounded-2xl cursor-pointer hover:border-[#7a6020] transition-all border-gray-100 bg-gray-50">
                                <div class="flex items-center gap-2 font-bold text-[#7a6020]">
                                    <input type="radio" name="center_entry_level" value="mastery"
                                        x-model="selectedLevel" data-field="center_entry_level"
                                        class="text-[#b8973a] focus:ring-[#b8973a]">
                                    <span>⭐ مستوى الإتقان</span>
                                </div>
                                <span class="text-xs text-gray-500 mt-2 mr-5">حلقات التثبيت، المراجعة المكثفة والخاتمين</span>
                            </label>
                            <label class="flex flex-col p-4 border rounded-2xl cursor-pointer hover:border-indigo-600 transition-all border-gray-100 bg-gray-50">
                                <div class="flex items-center gap-2 font-bold text-indigo-800">
                                    <input type="radio" name="center_entry_level" value="creativity"
                                        x-model="selectedLevel" data-field="center_entry_level"
                                        class="text-indigo-600 focus:ring-indigo-500">
                                    <span>🏆 مستوى الإبداع</span>
                                </div>
                                <span class="text-xs text-gray-500 mt-2 mr-5">مجالس الإجازات، القراءات والسند المتصل</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ───────────────── مستوى البناء ───────────────── -->
                <div id="step-6" x-show="selectedLevel === 'construction'" x-transition
                    class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                        <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">🌱</div>
                        <div>
                            <h2 class="text-xl font-black text-[#0a5c36]">مستوى البناء</h2>
                            <p class="text-xs text-gray-400 mt-1">تسكين الطالب في الحلقات وخطة الحفظ</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">سورة الالتحاق الحالية <span class="text-red-500">*</span></label>
                        <select name="current_surah" id="currentSurah" data-field="current_surah"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                            <option value="" @selected(old('current_surah', $construction->current_surah ?? '') == '')>-- اختر السورة أو حالة الالتحاق --</option>
                            <option value="بداية" @selected(old('current_surah', $construction->current_surah ?? '') == 'بداية')>🟢 بداية (مبتدئ تماماً)</option>
                            <option value="خاتم" @selected(old('current_surah', $construction->current_surah ?? '') == 'خاتم')>👑 خاتم (حفظ القرآن كاملاً)</option>
                            @foreach(['الفاتحة','البقرة','آل عمران','النساء','المائدة','الأنعام','الأعراف','الأنفال','التوبة','يونس','هود','يوسف','الرعد','إبراهيم','الحجر','النحل','الإسراء','الكهف','مريم','طه','الأنبياء','الحج','المؤمنون','النور','الفرقان','الشعراء','النمل','القصص','العنكبوت','الروم','لقمان','السجدة','الأحزاب','سبأ','فاطر','يس','الصافات','ص','الزمر','غافر','فصلت','الشورى','الزخرف','الدخان','الجاثية','الأحقاف','محمد','الفتح','الحجرات','ق','الذاريات','الطور','النجم','القمر','الرحمن','الواقعة','الحديد','المعادلة','الحشر','الممتحنة','الصف','الجمعة','المنافقون','التغابن','الطلاق','التحريم','الملك','القلم','الحاقة','المعارج','نوح','الجن','المزمل','المدثر','القيامة','الإنسان','المرسلات','النبأ','النازعات','عبس','التكوير','الانفطار','المطففين','الانشقاق','البروج','الطارق','الأعلى','الغاشية','الفجر','البلد','الشمس','الليل','الضحى','الشرح','التين','العلق','القدر','البينة','الزلزلة','العاديات','القارعة','التكاثر','العصر','الهمزة','الفيل','قريش','الماعون','الكوثر','الكافرون','النصر','المسد','الإخلاص','الفلق','الناس'] as $idx => $surah)
                            <option value="{{ $surah }}" @selected(old('current_surah', $construction->current_surah ?? '') == $surah)>
                                {{ $idx + 1 }}. {{ $surah }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">اسم الحلقة <span class="text-red-500">*</span></label>
                            <select name="group_name" data-field="group_name" id="circleSelect"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                                <option value="" data-type="" @selected(old('group_name', $construction->group_name ?? '') == '')>-- اختر الحلقة --</option>
                                @foreach($circles as $circle)
                                <option value="{{ $circle->name }}" data-type="{{ $circle->type ?? '' }}"
                                    @selected(old('group_name', $construction->group_name ?? '') == $circle->name)>
                                    {{ $circle->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">النظام المتبع <span class="text-red-500">*</span></label>
                            <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100" id="studySystemWrapper">
                                @foreach(['فردي', 'جماعي'] as $system)
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="study_system" value="{{ $system }}" data-field="study_system"
                                        @checked(old('study_system', $construction->study_system ?? '') == $system)>
                                    <span>{{ $system }}</span>
                                </label>
                                @endforeach
                            </div>
                            <p id="studySystemHint" class="text-xs text-gray-400 hidden">يُحدَّد تلقائياً من نوع الحلقة المختارة</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">خطة الحفظ الجديد <span class="text-red-500">*</span></label>
                            <input type="text" name="new_memorization_plan" data-field="new_memorization_plan"
                                value="{{ old('new_memorization_plan', $construction->new_memorization_plan ?? '') }}"
                                placeholder="مثال: حفظ 5 سطور"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">مستوى الحفظ بعد الاختبار <span class="text-red-500">*</span></label>
                            <select name="placement_evaluation" data-field="placement_evaluation"
                                class="w-full h-12 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                                <option value="" @selected(old('placement_evaluation', $construction->placement_evaluation ?? '') == '')>-- اختر التقييم --</option>
                                @foreach(['ممتاز', 'جيد', 'تثبيت', 'تأسيس', 'إعادة حفظ'] as $eval)
                                <option value="{{ $eval }}" @selected(old('placement_evaluation', $construction->placement_evaluation ?? '') == $eval)>{{ $eval }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">خطة الحفظ القديم (المراجعة) <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            @foreach(['منتهي', 'فئة الماهر', 'فئة المرتل', 'ترديد', 'أخرى'] as $plan)
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="old_memorization_plan" value="{{ $plan }}" data-field="old_memorization_plan"
                                    @checked(old('old_memorization_plan', $construction->old_memorization_plan ?? '') == $plan)>
                                <span>{{ $plan }}</span>
                            </label>
                            @endforeach
                        </div>
                        <input type="text" name="old_memorization_plan_other" data-field="old_memorization_plan_other"
                            value="{{ old('old_memorization_plan_other', $construction->old_memorization_plan_other ?? '') }}"
                            data-show-when="old_memorization_plan=أخرى"
                            placeholder="يرجى توضيح الخطة الإضافية..."
                            class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                            style="display:none;">
                    </div>
                </div>

                <!-- ───────────────── مستوى الإتقان ───────────────── -->
                <div id="step-7" x-show="selectedLevel === 'mastery'" x-transition
                    class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl text-xl">⭐</div>
                        <div>
                            <h2 class="text-xl font-black text-amber-600">مستوى الإتقان</h2>
                            <p class="text-xs text-gray-400 mt-1">تفاصيل الحفظ والمراجعة للمتقدمين المتميزين</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">جهة الحفظ السابقة <span class="text-red-500">*</span></label>
                            <input type="text" name="previous_memorization_side"
                                value="{{ old('previous_memorization_side', $itqan->previous_memorization_side ?? '') }}"
                                placeholder="اسم المسجد، المركز، أو الشيخ السابق"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">عدد الختمات السابقة <span class="text-red-500">*</span></label>
                            <input type="text" name="previous_khatamat_count"
                                value="{{ old('previous_khatamat_count', $itqan->previous_khatamat_count ?? '') }}"
                                placeholder="مثال: ختمة واحدة أو أكثر"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">مقدار المراجعة الحالي <span class="text-red-500">*</span></label>
                        <input type="text" name="current_review_amount"
                            value="{{ old('current_review_amount', $itqan->current_review_amount ?? '') }}"
                            placeholder="مثال: جزء يوميًّا، حزب، نصف جزء..."
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">تقييم مستوى الحفظ (1-10) <span class="text-red-500">*</span></label>
                        <select name="self_evaluation"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all appearance-none">
                            <option value="">-- اختر التقييم --</option>
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @selected(old('self_evaluation', $itqan?->self_evaluation ?? 0) == $i)>{{ $i }}</option>
                                @endfor
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">متون التجويد المحفوظة <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            @foreach(['لا يوجد' => 'لا يوجد', 'التحفة' => 'تحفة الأطفال', 'الجزرية' => 'المقدمة الجزرية', 'أخرى' => 'أخرى'] as $val => $lbl)
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="tajweed_matn" value="{{ $val }}" data-field="tajweed_matn"
                                    @checked(old('tajweed_matn', $itqan->tajweed_matn ?? '') == $val)>
                                <span>{{ $lbl }}</span>
                            </label>
                            @endforeach
                        </div>
                        <input type="text" name="tajweed_matn_other"
                            value="{{ old('tajweed_matn_other', $itqan->tajweed_matn_other ?? '') }}"
                            data-show-when="tajweed_matn=أخرى"
                            placeholder="يرجى كتابة اسم المتن..."
                            class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all"
                            style="display:none;">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">المسار المرغوب فيه <span class="text-red-500">*</span></label>
                            <div class="flex flex-col gap-2 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                                @foreach(['تثبيت الحفظ' => 'تثبيت الحفظ وتجويده', 'تصحيح التلاوة' => 'تصحيح التلاوة والنطق', 'الإجازة والسند' => 'الإجازة والسند المتصل'] as $val => $lbl)
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="desired_path" value="{{ $val }}" data-field="desired_path"
                                        @checked(old('desired_path', $itqan->desired_path ?? '') == $val)>
                                    <span>{{ $lbl }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">الوقت المناسب للمجلس <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 rounded-2xl border border-gray-100"
                                x-bind:inert="selectedLevel !== 'mastery'">
                                @foreach(['صباحًا', 'ظهرًا', 'عصرًا', 'ليلًا', 'أون لاين'] as $time)
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer {{ $time === 'أون لاين' ? 'col-span-2' : '' }}">
                                    <input type="radio" name="preferred_time" value="{{ $time }}" data-field="preferred_time"
                                        @checked(old('preferred_time', $itqan->preferred_time ?? '') == $time)>
                                    <span>{{ $time === 'أون لاين' ? 'أون لاين (عبر الإنترنت)' : $time }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <select name="teacher_name" data-field="teacher_name"
                        x-bind:disabled="selectedLevel !== 'mastery'"
                        class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all appearance-none">
                        <option value="" @selected(old('teacher_name', $itqan->teacher_name ?? '') == '')>-- اختر المعلم --</option>
                        <option value="بدون تحديد" @selected(old('teacher_name', $itqan->teacher_name ?? '') == 'بدون تحديد')>بدون تحديد (حسب المتاح)</option>
                        @foreach ($teachers ?? [] as $teacherItem)
                        <option value="{{ $teacherItem->name }}" @selected(old('teacher_name', $itqan->teacher_name ?? '') == $teacherItem->name)>
                            {{ $teacherItem->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- ───────────────── مستوى الإبداع ───────────────── -->
                <div id="step-8" x-show="selectedLevel === 'creativity'" x-transition
                    class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl text-xl">🏆</div>
                        <div>
                            <h2 class="text-xl font-black text-indigo-600">مستوى الإبداع</h2>
                            <p class="text-xs text-gray-400 mt-1">بيانات الروايات والأسانيد التي حصل عليها الطالب</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">الإجازات والأسانيد السابقة <span class="text-red-500">*</span></label>
                        <textarea name="previous_licenses_and_chains"
                            placeholder="يرجى ذكر الإجازات، اسم الشيخ المجيز، والمتن..."
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all min-h-25">{{ old('previous_licenses_and_chains', $ibda->previous_licenses_and_chains ?? '') }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">المسار والرواية المراد دراستها <span class="text-red-500">*</span></label>
                        <input type="text" name="desired_narration_and_path"
                            value="{{ old('desired_narration_and_path', $ibda->desired_narration_and_path ?? '') }}"
                            placeholder="مثال: رواية ورش عن نافع، القراءات العشر..."
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">الوقت المناسب للمجلس <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 p-3 bg-gray-50 rounded-2xl border border-gray-100"
                            x-bind:inert="selectedLevel !== 'creativity'">
                            @foreach(['صباحًا', 'ظهرًا', 'عصرًا', 'ليلًا', 'أون لاين'] as $time)
                            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer">
                                <input type="radio" name="preferred_time" value="{{ $time }}" data-field="preferred_time"
                                    @checked(old('preferred_time', $ibda->preferred_time ?? '') == $time)>
                                <span>{{ $time === 'أون لاين' ? 'عن بُعد' : $time }}</span>
                            </label>
                            @endforeach
                        </div>

                        <select name="supervisor_name" data-field="supervisor_name"
                            x-bind:disabled="selectedLevel !== 'creativity'"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all appearance-none">
                            <option value="">-- اختر المعلم --</option>
                            @foreach ($teachers ?? [] as $teacherItem)
                            <option value="{{ $teacherItem->name }}" @selected(old('supervisor_name', $ibda->supervisor_name ?? '') == $teacherItem->name)>
                                {{ $teacherItem->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- ───────────────── التوصيات النهائية ───────────────── -->
            <div id="step-9" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                    <div class="p-3 bg-[#e8f5ed] text-[#0a5c36] rounded-2xl text-xl">📝</div>
                    <div>
                        <h2 class="text-xl font-black text-gray-800">التوصيات النهائية والملاحظات الإدارية</h2>
                        <p class="text-xs text-gray-400 mt-1">الاعتماد المالي وقرار الإدارة النهائي لتسجيل الطالب</p>
                    </div>
                </div>

                <div class="bg-[#e8f5ed]/50 rounded-2xl p-4 text-center border border-[#d4c98a] my-4">
                    <div class="font-serif font-bold text-[#0a5c36] text-lg">« وَلَقَدْ يَسَّرْنَا الْقُرْآنَ لِلذِّكْرِ فَهَلْ مِن مُّدَّكِرٍ »</div>
                    <div class="text-[10px] text-gray-400 mt-1">سورة القمر - آية ١٧</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">رسوم حجز المقعد</label>
                        <input type="text" name="subscription_fees"
                            value="{{ old('subscription_fees', $student->subscription_fees ?? '') }}"
                            placeholder="مثال: 150"
                            class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-gray-600">الأدوات والكتب المستلمة</label>
                        <select name="received_tools"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium appearance-none">
                            <option value="" @selected(old('received_tools', $student->received_tools ?? '') == '')>-- اختر نوع العهدة --</option>
                            @foreach(['لم يأخذ شيء' => 'لم يأخذ شيء', 'المصحف فقط' => 'المصحف فقط', 'المتابعة فقط' => 'دفتر المتابعة فقط', 'المصحف والمتابعة' => 'المصحف ودفتر المتابعة معًا'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('received_tools', $student->received_tools ?? '') == $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @can('edit students')
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">حالة الطالب</label>
                        <select name="status" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm">
                            @foreach(['مقيد', 'متوقف', 'مسافر'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected(old('status', $student->status ?? 'مقيد') == $statusOption)>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="status" value="{{ old('status', $student->status ?? 'مقيد') }}">
                    @endcan

                    @can('manage student status')
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">قرار الإدارة</label>
                        <select name="decision" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm">
                            @foreach(['تحت الاختبار', 'مقبول', 'مرفوض'] as $decisionOption)
                            <option value="{{ $decisionOption }}" @selected(old('decision', $student->decision ?? 'تحت الاختبار') == $decisionOption)>{{ $decisionOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">قرار الإدارة</label>
                        @php $decision = old('decision', $student->decision ?? 'تحت الاختبار'); @endphp
                        <div class="w-full p-3 bg-gray-100 border border-gray-200 rounded-2xl text-sm text-gray-600 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full inline-block
                            {{ $decision === 'مقبول' ? 'bg-emerald-500' : ($decision === 'مرفوض' ? 'bg-red-500' : 'bg-amber-400') }}">
                            </span>
                            {{ $decision }}
                            <span class="text-xs text-gray-400 mr-auto">(صلاحيات الإدارة فقط)</span>
                        </div>
                        <input type="hidden" name="decision" value="{{ $decision }}">
                    </div>
                    @endcan
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-bold text-gray-700">ملاحظات الشيخ المختبر / المشرف الفنية</label>
                        <button type="button" data-action="dictate" data-dictate-field="notes"
                            class="text-xs font-bold flex items-center gap-1 px-3 py-1 bg-[#e8f5ed] rounded-full text-[#0a5c36]">
                            <span id="dictateBtnLabel">🎤 إملاء صوتي</span>
                        </button>
                    </div>
                    <textarea name="notes" data-field="notes"
                        placeholder="اكتب التوصيات الخاصة بمخارج الحروف والتجويد..."
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm min-h-25">{{ old('notes', $student->notes ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end items-center pt-6 border-t border-gray-100 mt-8">
                <button type="submit"
                    class="flex items-center gap-2 px-8 py-3 bg-[#0a5c36] hover:bg-[#084d2d] text-white font-black rounded-2xl shadow-md transition-all text-sm">
                    حفظ البيانات وإرسال النموذج ✓
                </button>
            </div>

        </div>

        {{-- ============================================================
         JavaScript
    ============================================================ --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ── 1) data-show-when ──────────────────────────────────────
                function initShowWhen(input) {
                    const condition = input.getAttribute('data-show-when');
                    if (!condition) return;

                    const eqIndex = condition.indexOf('=');
                    const fieldName = condition.substring(0, eqIndex);
                    const expected = condition.substring(eqIndex + 1);

                    function getVal() {
                        const checked = document.querySelector(`[name="${fieldName}"]:checked`);
                        if (checked) return checked.value;
                        const sel = document.querySelector(`select[name="${fieldName}"]`);
                        if (sel) return sel.value;
                        return '';
                    }

                    function toggle() {
                        input.style.display = getVal() === expected ? 'block' : 'none';
                    }

                    document.querySelectorAll(`[name="${fieldName}"]`).forEach(el => {
                        el.addEventListener('change', toggle);
                    });

                    toggle();
                }

                document.querySelectorAll('[data-show-when]').forEach(initShowWhen);

                // ── 2) الهواية "أخرى" ─────────────────────────────────────
                const hobbyCheckbox = document.getElementById('hobbyOtherCheckbox');
                const hobbyOtherInput = document.getElementById('hobbyOtherInput');

                if (hobbyCheckbox) {
                    const toggleHobby = () => {
                        if (hobbyOtherInput)
                            hobbyOtherInput.style.display = hobbyCheckbox.checked ? 'block' : 'none';
                    };
                    hobbyCheckbox.addEventListener('change', toggleHobby);
                    toggleHobby();
                }

                // ── 3) الحلقة → النظام المتبع ─────────────────────────────
                const circleSelect = document.getElementById('circleSelect');
                const studySystemHint = document.getElementById('studySystemHint');

                if (circleSelect) {
                    const autoSelectSystem = () => {
                        const opt = circleSelect.options[circleSelect.selectedIndex];
                        const systemType = opt ? opt.getAttribute('data-type') : '';

                        if (systemType === 'فردي' || systemType === 'جماعي') {
                            const radio = document.querySelector(`input[name="study_system"][value="${systemType}"]`);
                            if (radio) {
                                radio.checked = true;
                                studySystemHint?.classList.remove('hidden');
                            }
                        } else {
                            studySystemHint?.classList.add('hidden');
                        }
                    };

                    circleSelect.addEventListener('change', autoSelectSystem);
                    if (circleSelect.value !== '') autoSelectSystem();
                }

                // ── 4) guardian_id قبل الإرسال ────────────────────────────
                const studentForm = document.querySelector('form');
                if (studentForm) {
                    studentForm.addEventListener('submit', function() {
                        const guardianInput = document.getElementById('guardianIdInput');
                        if (guardianInput && !guardianInput.value) {
                            guardianInput.value = 'none';
                        }
                    });
                }

                // ── 5) استعادة حالة guardian بعد validation error ─────────
                const guardianIdVal = document.getElementById('guardianIdInput')?.value;
                if (guardianIdVal === 'new') {
                    document.getElementById('newGuardianFields').style.display = 'grid';
                }

                // ── 6) ربط checkGuardianExists بحقل الواتساب ──────────────
                const whatsappInput = document.getElementById('whatsappInput');
                if (whatsappInput) {
                    let whatsappTimer;
                    whatsappInput.addEventListener('input', () => {
                        clearTimeout(whatsappTimer);
                        whatsappTimer = setTimeout(checkGuardianExists, 600);
                    });
                }
            });

            // ── Alpine: guardianSearch ─────────────────────────────────────
            function guardianSearch() {
                return {
                    query: '{{ addslashes($guardianQueryName) }}',
                    results: [],
                    selected: @json($guardianData ?? null),
                    searching: false,
                    noResults: false,

                    async search() {
                        if (this.query.length < 2) {
                            this.results = [];
                            this.noResults = false;
                            return;
                        }

                        this.searching = true;
                        this.noResults = false;

                        try {
                            // ✅ URL محدث
                            const res = await fetch(
                                `/guardians/search?q=${encodeURIComponent(this.query)}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                }
                            );

                            if (!res.ok) {
                                console.error('Guardian search failed:', res.status);
                                return;
                            }

                            this.results = await res.json();
                            this.noResults = this.results.length === 0;

                            if (this.noResults) {
                                document.getElementById('guardianIdInput').value = 'new';
                                document.getElementById('newGuardianFields').style.display = 'grid';
                            } else {
                                document.getElementById('newGuardianFields').style.display = 'none';
                            }
                        } catch (e) {
                            console.error('Guardian search error:', e);
                        } finally {
                            this.searching = false;
                        }
                    },

                    select(guardian) {
                        this.selected = guardian;
                        this.query = guardian.name;
                        this.results = [];
                        this.noResults = false;
                        document.getElementById('guardianIdInput').value = guardian.id;
                        document.getElementById('newGuardianFields').style.display = 'none';
                        document.getElementById('guardianExistsAlert')?.classList.add('hidden');
                    },

                    clear() {
                        this.selected = null;
                        this.query = '';
                        this.results = [];
                        this.noResults = false;
                        document.getElementById('guardianIdInput').value = '';
                        document.getElementById('newGuardianFields').style.display = 'none';
                        document.getElementById('guardianExistsAlert')?.classList.add('hidden');
                    },

                    createNew() {
                        this.selected = null;
                        document.getElementById('guardianIdInput').value = 'new';
                        document.getElementById('newGuardianFields').style.display = 'grid';
                    },

                    skipGuardian() {
                        this.selected = {
                            id: null,
                            name: 'بدون ولي أمر',
                            is_active: null
                        };
                        this.query = '';
                        this.results = [];
                        this.noResults = false;
                        document.getElementById('guardianIdInput').value = 'none';
                        document.getElementById('newGuardianFields').style.display = 'none';
                    },
                };
            }

            // ── checkGuardianExists ────────────────────────────────────────
            let _existingGuardianFromCheck = null;

            async function checkGuardianExists() {
                const email = document.getElementById('parentEmailInput')?.value?.trim() ?? '';
                const mobile = document.getElementById('whatsappInput')?.value?.trim() ?? '';

                if (!email && !mobile) return;

                const params = new URLSearchParams();
                if (email) params.set('email', email);
                if (mobile) params.set('mobile', mobile);

                try {
                    const res = await fetch(`/guardians/check?${params}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    const alertEl = document.getElementById('guardianExistsAlert');
                    const emailResult = document.getElementById('emailCheckResult');

                    if (data.exists) {
                        _existingGuardianFromCheck = data;
                        alertEl?.classList.remove('hidden');
                        if (emailResult) {
                            emailResult.className = 'text-xs font-medium mt-1 text-amber-600';
                            emailResult.textContent = `⚠️ حساب موجود: ${data.name} (#${data.id})`;
                            emailResult.classList.remove('hidden');
                        }
                    } else {
                        _existingGuardianFromCheck = null;
                        alertEl?.classList.add('hidden');
                        if (emailResult) {
                            emailResult.className = 'text-xs font-medium mt-1 text-emerald-600';
                            emailResult.textContent = '✓ متاح — سيُنشأ حساب جديد';
                            emailResult.classList.remove('hidden');
                        }
                    }
                } catch (e) {
                    console.error('Guardian check error:', e);
                }
            }

            // ── useExistingGuardian ────────────────────────────────────────
            function useExistingGuardian() {
                if (!_existingGuardianFromCheck) return;

                document.getElementById('guardianIdInput').value = _existingGuardianFromCheck.id;
                document.getElementById('newGuardianFields').style.display = 'none';
                document.getElementById('guardianExistsAlert')?.classList.add('hidden');
                document.getElementById('emailCheckResult')?.classList.add('hidden');

                _existingGuardianFromCheck = null;
            }
        </script>