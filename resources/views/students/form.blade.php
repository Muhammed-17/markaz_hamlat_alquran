@php
$isEdit = isset($student) && $student->exists;
$construction = $construction ?? $student->constructionDetail ?? null;
$itqan = $itqan ?? $student->itqanDetail ?? null;
$ibda = $ibda ?? $student->ibdaDetail ?? null;

$regMonth = now()->month;
$isNextYearReg = ($regMonth >= 7 && $regMonth <= 9);

    $gradePromotion=[ 'الأول'=> 'الثاني',
    'الثاني' => 'الثالث',
    'الثالث' => 'الرابع',
    'الرابع' => 'الخامس',
    'الخامس' => 'السادس',
    'السادس' => 'لا يوجد',
    ];

    $savedGrade = $student->school_grade ?? '';
    $suggestedGrade = ($isNextYearReg && !$isEdit && isset($gradePromotion[$savedGrade]))
    ? $gradePromotion[$savedGrade]
    : $savedGrade;
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

        <!-- ----------------------------------------- بيانات المشرف والالتحاق ------------------------------------------- -->
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
                    <label class="block text-sm font-bold text-gray-700">المشرف المقيم / مسجل البيانات <span
                            class="text-red-500">*</span></label>
                    <select name="supervisor_id" data-field="supervisor_id" required
                        class="w-full p-3 border rounded-2xl text-sm font-medium focus:outline-none focus:ring-1 transition-all">
                        <option value="" @selected(old('supervisor_id', $student->supervisor_id ?? '') == '')>-- اختر المشرف --</option>
                        @foreach ($supervisors ?? [] as $supervisor)
                        @php
                        $roleLabel = '';
                        if (isset($supervisor->role)) {
                        $roleLabel = $supervisor->role === 'admin' ? 'المدير' : 'مشرف';
                        } elseif ($supervisor->user && $supervisor->user->roles->count() > 0) {
                        $roleName = $supervisor->user->roles->first()->name;
                        $roleLabel = ($roleName === 'admin' || $roleName === 'supervisor') ? 'المدير' : 'مشرف';
                        } else {
                        $roleLabel = 'مشرف';
                        }
                        @endphp
                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $student->supervisor_id ?? '') == $supervisor->id ? 'selected' : '' }}>
                            {{ $supervisor->name }} ({{ $roleLabel }})
                        </option>
                        @endforeach
                    </select>
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

        <!-- --------------------------------------------------- البيانات الأساسية -------------------------------------- -->
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
                    <label class="block text-sm font-bold text-gray-700">مقدم طلب التسجيل <span
                            class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                            <input type="radio" name="applicant" value="الأم" data-field="applicant"
                                @checked(old('applicant', $student->applicant ?? '') == 'الأم')
                            class="rounded-full text-[#0a5c36] focus:ring-[#0a5c36]">
                            <span>الأم</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                            <input type="radio" name="applicant" value="الأب" data-field="applicant"
                                @checked(old('applicant', $student->applicant ?? '') == 'الأب')
                            class="rounded-full text-[#0a5c36] focus:ring-[#0a5c36]">
                            <span>الأب</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                            <input type="radio" name="applicant" value="الطالب" data-field="applicant"
                                @checked(old('applicant', $student->applicant ?? '') == 'الطالب')
                            class="rounded-full text-[#0a5c36] focus:ring-[#0a5c36]">
                            <span>الطالب نفسه</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                            <input type="radio" name="applicant" value="أخرى" data-field="applicant"
                                @checked(old('applicant', $student->applicant ?? '') == 'أخرى')
                            class="rounded-full text-[#0a5c36] focus:ring-[#0a5c36]">
                            <span>أخرى</span>
                        </label>
                    </div>
                    @error('applicant')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                    {{-- يظهر عند اختيار أخرى --}}
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
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                            required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">اسم الطالب (رباعيًّا) <span
                                class="text-red-500">*</span></label>
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
                        <label class="block text-sm font-bold text-gray-700">النوع <span
                                class="text-red-500">*</span></label>
                        <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="gender" value="ذكر" data-field="gender"
                                    @checked(old('gender', $student->gender ?? '') == 'ذكر')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]" required>
                                <span>ذكر</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="gender" value="أنثى" data-field="gender"
                                    @checked(old('gender', $student->gender ?? '') == 'أنثى')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]" required>
                                <span>أنثى</span>
                            </label>
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
                        <label class="block text-sm font-bold text-gray-700">العنوان التفصيلي (المركز - القرية - الشارع)
                            <span class="text-red-500">*</span></label>
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
                        <label class="block text-sm font-bold text-gray-700">المركز فرع <span
                                class="text-red-500">*</span></label>
                        <select name="center" data-field="center"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                            required>
                            <option value="" @selected(old('center', $student->center ?? '') == '')>-- اختر المركز --</option>
                            @foreach($centers ?? [] as $center)
                            <option value="{{ $center->name }}" @selected(old('center', $student->center ?? '') == $center->name)>{{ $center->name }}</option>
                            @endforeach
                        </select>
                        @error('center')
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
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        @error('whatsapp_number')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                        {{-- مؤشر نتيجة البحث --}}
                        <div id="guardianSearchResult" class="hidden mt-1 text-xs font-semibold px-2 py-1 rounded-xl"></div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">رقم اتصال إضافي</label>
                        <input type="tel" name="second_phone" data-field="second_phone"
                            value="{{ old('second_phone', $student->second_phone ?? '') }}"
                            placeholder="01xxxxxxxxx"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        @error('second_phone')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">صاحب رقم الواتساب</label>
                        <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="whatsapp_owner" value="الأم" data-field="whatsapp_owner"
                                    @checked(old('whatsapp_owner', $student->whatsapp_owner ?? '') == 'الأم')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                <span>الأم</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="whatsapp_owner" value="الأب" data-field="whatsapp_owner"
                                    @checked(old('whatsapp_owner', $student->whatsapp_owner ?? '') == 'الأب')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                <span>الأب</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="whatsapp_owner" value="الطالب" data-field="whatsapp_owner"
                                    @checked(old('whatsapp_owner', $student->whatsapp_owner ?? '') == 'الطالب')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                <span>الطالب</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="whatsapp_owner" value="أخرى" data-field="whatsapp_owner"
                                    @checked(old('whatsapp_owner', $student->whatsapp_owner ?? '') == 'أخرى')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                <span>أخرى</span>
                            </label>
                        </div>
                        @error('whatsapp_owner')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                        {{-- يظهر عند اختيار أخرى --}}
                        <input type="text" name="whatsapp_owner_other" data-field="whatsapp_owner_other"
                            value="{{ old('whatsapp_owner_other', $student->whatsapp_owner_other ?? '') }}"
                            data-show-when="whatsapp_owner=أخرى"
                            placeholder="يرجى التحديد..."
                            class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36]"
                            style="display:none;">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">صاحب الرقم الإضافي</label>
                        <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="additional_contact_owner" value="الأم" data-field="additional_contact_owner"
                                    @checked(old('additional_contact_owner', $student->additional_contact_owner ?? '') == 'الأم')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                <span>الأم</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="additional_contact_owner" value="الأب" data-field="additional_contact_owner"
                                    @checked(old('additional_contact_owner', $student->additional_contact_owner ?? '') == 'الأب')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                <span>الأب</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="additional_contact_owner" value="الطالب" data-field="additional_contact_owner"
                                    @checked(old('additional_contact_owner', $student->additional_contact_owner ?? '') == 'الطالب')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                <span>الطالب</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="additional_contact_owner" value="أخرى" data-field="additional_contact_owner"
                                    @checked(old('additional_contact_owner', $student->additional_contact_owner ?? '') == 'أخرى')
                                class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                <span>أخرى</span>
                            </label>
                        </div>
                        @error('additional_contact_owner')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                        {{-- يظهر عند اختيار أخرى --}}
                        <input type="text" name="additional_contact_owner_other" data-field="additional_contact_owner_other"
                            value="{{ old('additional_contact_owner_other', $student->additional_contact_owner_other ?? '') }}"
                            data-show-when="additional_contact_owner=أخرى"
                            placeholder="يرجى التحديد..."
                            class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36]"
                            style="display:none;">
                    </div>
                </div>

                {{-- ====== تعديل 1 و 2: حقل ولي الأمر مع البحث + خيار أخرى ====== --}}
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">تحديد ولي الأمر <span
                            class="text-red-500">*</span></label>
                    <select name="guardian_id" id="guardianSelect" data-field="guardian_id" required
                        class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        <option value="new" @selected(old('guardian_id', $student->guardian_id ?? '') == 'new')>+ إضافة ولي أمر جديد</option>
                        <option value="other" @selected(old('guardian_id', $student->guardian_id ?? '') == 'other')>✏️ أخرى (إدخال يدوي)</option>
                        @foreach($guardians as $guardian)
                        <option value="{{ $guardian->id }}" @selected(old('guardian_id', $student->guardian_id ?? '') == $guardian->id)>{{ $guardian->name }} ({{ $guardian->email }})</option>
                        @endforeach
                    </select>
                    @error('guardian_id')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror

                    {{-- تعديل 2: حقل الإدخال اليدوي عند اختيار "أخرى" --}}
                    <input type="text" name="guardian_name_manual" data-field="guardian_name_manual"
                        value="{{ old('guardian_name_manual', $student->guardian_name_manual ?? '') }}"
                        data-show-when="guardian_id=other"
                        placeholder="اكتب اسم ولي الأمر يدوياً..."
                        class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                        style="display:none;">
                </div>

                {{-- حقول إنشاء حساب جديد — تظهر عند اختيار "new" --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2" data-show-when="guardian_id=new" style="display:none;">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-bold text-gray-700">اسم المستخدم / البريد الإلكتروني <span
                                    class="text-red-500">*</span></label>
                        </div>
                        <div class="relative">
                            <input type="text" name="parent_email" id="parentEmailInput" data-field="parent_email"
                                value="{{ old('parent_email') }}"
                                placeholder="سيتم ملؤه تلقائياً برقم الواتساب"
                                class="w-full p-3 bg-gray-50/80 border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all ltr"
                                data-required-when="guardian_id=new">
                            <div class="absolute left-3 top-3.5 text-gray-300 pointer-events-none">✉️</div>
                        </div>
                        @error('parent_email')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-bold text-gray-700">كلمة المرور للحساب <span
                                    class="text-red-500">*</span></label>
                        </div>
                        <div class="relative">
                            <div class="absolute right-3 top-3.5 text-gray-400 pointer-events-none text-xs">🔒</div>
                            <input type="password" id="passwordInput" name="password" data-field="password"
                                value="{{ old('password') }}"
                                placeholder="سيتم ملؤها تلقائياً برقم الواتساب"
                                class="w-full p-3 pr-10 pl-10 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all ltr"
                                data-required-when="guardian_id=new">
                            <button type="button" data-action="togglePassword"
                                class="absolute left-3 top-2.5 p-1 text-gray-400 hover:text-[#0a5c36] focus:outline-none transition-colors text-sm">
                                <span id="passwordShowIcon">👁️</span>
                                <span id="passwordHideIcon" style="display:none;">🙈</span>
                            </button>
                        </div>
                        @error('password')
                        <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        <!-- --------------------------------------------------- البيانات الدراسية -------------------------------------- -->
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
                    <label class="block text-sm font-bold text-gray-700">المرحلة الدراسية <span
                            class="text-red-500">*</span></label>
                    <select name="educational_stage" data-field="educational_stage"
                        class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none"
                        required>
                        <option value="" @selected(old('educational_stage', $student->educational_stage ?? '') == '')>-- اختر المرحلة --</option>
                        <option value="تمهيدي" @selected(old('educational_stage', $student->educational_stage ?? '') == 'تمهيدي')>تمهيدي</option>
                        <option value="حضانة" @selected(old('educational_stage', $student->educational_stage ?? '') == 'حضانة')>حضانة</option>
                        <option value="ابتدائي" @selected(old('educational_stage', $student->educational_stage ?? '') == 'ابتدائي')>ابتدائي</option>
                        <option value="اعدادي" @selected(old('educational_stage', $student->educational_stage ?? '') == 'اعدادي')>اعدادي</option>
                        <option value="ثانوي" @selected(old('educational_stage', $student->educational_stage ?? '') == 'ثانوي')>ثانوي</option>
                        <option value="جامعي" @selected(old('educational_stage', $student->educational_stage ?? '') == 'جامعي')>جامعي</option>
                        <option value="خريج" @selected(old('educational_stage', $student->educational_stage ?? '') == 'خريج')>خريج</option>
                    </select>
                    @error('educational_stage')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">نوع التعليم <span
                            class="text-red-500">*</span></label>
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
                    <label class="block text-sm font-bold text-gray-700">الصف الدراسي <span
                            class="text-red-500">*</span></label>
                    {{-- ====== تعديل 3: الصف المقترح بمراعاة السنة المصرية ====== --}}
                    @if($isNextYearReg && !$isEdit && isset($gradePromotion[$savedGrade]))
                    <div class="mb-1 text-xs text-amber-600 font-semibold bg-amber-50 px-3 py-1.5 rounded-xl border border-amber-100">
                        ⚠️ موسم تسجيل — الصف المقترح للعام الجديد: {{ $gradePromotion[$savedGrade] }}
                    </div>
                    @endif
                    <select name="school_grade" data-field="school_grade"
                        class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none"
                        required>
                        <option value="" @selected(old('school_grade', $suggestedGrade)=='' )>-- اختر الصف --</option>
                        <option value="لا يوجد" @selected(old('school_grade', $suggestedGrade)=='لا يوجد' )>لا يوجد</option>
                        <option value="الأول" @selected(old('school_grade', $suggestedGrade)=='الأول' )>الأول</option>
                        <option value="الثاني" @selected(old('school_grade', $suggestedGrade)=='الثاني' )>الثاني</option>
                        <option value="الثالث" @selected(old('school_grade', $suggestedGrade)=='الثالث' )>الثالث</option>
                        <option value="الرابع" @selected(old('school_grade', $suggestedGrade)=='الرابع' )>الرابع</option>
                        <option value="الخامس" @selected(old('school_grade', $suggestedGrade)=='الخامس' )>الخامس</option>
                        <option value="السادس" @selected(old('school_grade', $suggestedGrade)=='السادس' )>السادس</option>
                        <option value="دراسات عليا" @selected(old('school_grade', $suggestedGrade)=='دراسات عليا' )>دراسات عليا</option>
                    </select>
                    @error('school_grade')
                    <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">المؤسسة التعليمية (الحضانة / المدرسة / المعهد /
                        الكلية) <span class="text-red-500">*</span></label>
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

        <!-- --------------------------------------------------- الحالة الصحية للطالب -------------------------------------- -->
        <div id="step-4" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">💚</div>
                <div>
                    <h2 class="text-xl font-black text-[#0a5c36]">بيانات الرعاية الطلابية</h2>
                    <p class="text-xs text-gray-400 mt-1">الحالة الصحية، السلوكية، والسمات الشخصية</p>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">الحالة الصحية للطالب <span
                        class="text-red-500">*</span></label>
                <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="health_status" value="طبيعية" data-field="health_status"
                            @checked(old('health_status', $student->health_status ?? '') == 'طبيعية') required>
                        <span>طبيعية (الحمد لله)</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="health_status" value="أخرى" data-field="health_status"
                            @checked(old('health_status', $student->health_status ?? '') == 'أخرى') required>
                        <span>أخرى</span>
                    </label>
                </div>
                @error('health_status')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                @enderror
                {{-- يظهر عند اختيار أخرى --}}
                <input type="text" name="health_status_other" data-field="health_status_other"
                    value="{{ old('health_status_other', $student->health_status_other ?? '') }}"
                    data-show-when="health_status=أخرى"
                    placeholder="يرجى توضيح الحالة الصحية..."
                    class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                    style="display:none;">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">صعوبات التعلم <span
                        class="text-red-500">*</span></label>
                <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="learning_difficulties" value="لا يوجد" data-field="learning_difficulties"
                            @checked(old('learning_difficulties', $student->learning_difficulties ?? '') == 'لا يوجد') required>
                        <span>لا يوجد (الحمد لله)</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="learning_difficulties" value="أخرى" data-field="learning_difficulties"
                            @checked(old('learning_difficulties', $student->learning_difficulties ?? '') == 'أخرى') required>
                        <span>أخرى</span>
                    </label>
                </div>
                @error('learning_difficulties')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                @enderror
                {{-- يظهر عند اختيار أخرى --}}
                <input type="text" name="learning_difficulties_other" data-field="learning_difficulties_other"
                    value="{{ old('learning_difficulties_other', $student->learning_difficulties_other ?? '') }}"
                    data-show-when="learning_difficulties=أخرى"
                    placeholder="يرجى توضيح صعوبات التعلم..."
                    class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                    style="display:none;">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">السمات الشخصية <span
                        class="text-red-500">*</span></label>
                <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="personal_traits" value="لا يوجد" data-field="personal_traits"
                            @checked(old('personal_traits', $student->personal_traits ?? '') == 'لا يوجد') required>
                        <span>لا يوجد</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="personal_traits" value="أخرى" data-field="personal_traits"
                            @checked(old('personal_traits', $student->personal_traits ?? '') == 'أخرى') required>
                        <span>أخرى</span>
                    </label>
                </div>
                @error('personal_traits')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                @enderror
                {{-- يظهر عند اختيار أخرى --}}
                <input type="text" name="personal_traits_other" data-field="personal_traits_other"
                    value="{{ old('personal_traits_other', $student->personal_traits_other ?? '') }}"
                    data-show-when="personal_traits=أخرى"
                    placeholder="يرجى تحديد السمات البارزة (عنيد، خجول...)"
                    class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                    style="display:none;">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">الهواية المفضلة <span
                        class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    @php
                    $savedHobbies = old('hobbies', $student->hobbies ?? []);
                    if (is_string($savedHobbies)) $savedHobbies = json_decode($savedHobbies, true) ?? [];
                    @endphp
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="checkbox" name="hobbies[]" value="كرة القدم" data-field="hobbies"
                            @checked(in_array('كرة القدم', $savedHobbies))
                            class="rounded text-[#0a5c36] focus:ring-[#0a5c36]">
                        <span>كرة القدم</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="checkbox" name="hobbies[]" value="الكاراتيه" data-field="hobbies"
                            @checked(in_array('الكاراتيه', $savedHobbies))
                            class="rounded text-[#0a5c36] focus:ring-[#0a5c36]">
                        <span>الكاراتيه</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="checkbox" name="hobbies[]" value="الرسم" data-field="hobbies"
                            @checked(in_array('الرسم', $savedHobbies))
                            class="rounded text-[#0a5c36] focus:ring-[#0a5c36]">
                        <span>الرسم</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="checkbox" name="hobbies[]" value="البرمجة والألعاب الإلكترونية" data-field="hobbies"
                            @checked(in_array('البرمجة والألعاب الإلكترونية', $savedHobbies))
                            class="rounded text-[#0a5c36] focus:ring-[#0a5c36]">
                        <span>البرمجة والألعاب</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="checkbox" name="hobbies[]" value="الأشغال اليدوية" data-field="hobbies"
                            @checked(in_array('الأشغال اليدوية', $savedHobbies))
                            class="rounded text-[#0a5c36] focus:ring-[#0a5c36]">
                        <span>الأشغال اليدوية</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="checkbox" name="hobbies[]" value="القراءة والإطلاع" data-field="hobbies"
                            @checked(in_array('القراءة والإطلاع', $savedHobbies))
                            class="rounded text-[#0a5c36] focus:ring-[#0a5c36]">
                        <span>القراءة</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="checkbox" name="hobbies[]" value="أخرى" id="hobbyOtherCheckbox" data-field="hobbies"
                            @checked(in_array('أخرى', $savedHobbies))
                            class="rounded text-[#0a5c36] focus:ring-[#0a5c36]">
                        <span>أخرى</span>
                    </label>
                </div>
                {{-- يظهر عند تحديد checkbox أخرى --}}
                <input type="text" name="hobby_other" data-field="hobby_other"
                    value="{{ old('hobby_other', $student->hobby_other ?? '') }}"
                    id="hobbyOtherInput"
                    placeholder="يرجى ذكر الهواية الإضافية..."
                    class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                    style="display:none;">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">حالة خروج الطالب من المركز <span
                        class="text-red-500">*</span></label>
                <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="student_exit_status" value="بمفرده" data-field="student_exit_status"
                            @checked(old('student_exit_status', $student->student_exit_status ?? '') == 'بمفرده') required>
                        <span>بمفرده</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                        <input type="radio" name="student_exit_status" value="مع ولي الأمر أو أحد الأقارب" data-field="student_exit_status"
                            @checked(old('student_exit_status', $student->student_exit_status ?? '') == 'مع ولي الأمر أو أحد الأقارب') required>
                        <span>مع ولي الأمر أو أحد الأقارب</span>
                    </label>
                </div>
                @error('student_exit_status')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
                @enderror
                {{-- يظهر عند اختيار "مع ولي الأمر أو أحد الأقارب" --}}
                <input type="text" name="exit_details" data-field="exit_details"
                    value="{{ old('exit_details', $student->exit_details ?? '') }}"
                    data-show-when="student_exit_status=مع ولي الأمر أو أحد الأقارب"
                    placeholder="يرجى توضيح مع من ستخرج..."
                    class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                    style="display:none;">
            </div>

            <div x-data="{ selectedLevel: '{{ old('center_entry_level', $student->center_entry_level ?? 'construction') }}' }">

                <!-- --------------------------------------------------- تقييم التلاوة وتحديد مستوى الالتحاق -------------------------------------- -->
                <div id="step-5" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                            <div class="p-3 bg-[#e8f5ed] text-[#0a5c36] rounded-2xl text-xl">🎤</div>
                            <div>
                                <h2 class="text-xl font-black text-gray-800">تقييم التلاوة وتحديد مستوى الالتحاق</h2>
                                <p class="text-xs text-gray-400 mt-1">تحديد المسار الفني والتعليمي للطالب بناء على تقييم الشيخ المختبر</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">مستوى القراءة من المصحف <span
                                    class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <label class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-[#0a5c36]/50 transition-all text-sm font-semibold text-gray-600">
                                    <input type="radio" name="reading" value="مبتدئ" data-field="reading"
                                        @checked(old('reading', $student->reading ?? '') == 'مبتدئ')
                                    class="text-[#0a5c36] focus:ring-[#0a5c36]" required>
                                    <span>مبتدئ <span class="text-xs text-gray-400 font-normal">(لا يقرأ)</span></span>
                                </label>
                                <label class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-[#0a5c36]/50 transition-all text-sm font-semibold text-gray-600">
                                    <input type="radio" name="reading" value="مقبول" data-field="reading"
                                        @checked(old('reading', $student->reading ?? '') == 'مقبول')
                                    class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                    <span>مقبول <span class="text-xs text-gray-400 font-normal">(يقرأ ببطء)</span></span>
                                </label>
                                <label class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-[#0a5c36]/50 transition-all text-sm font-semibold text-gray-600">
                                    <input type="radio" name="reading" value="متمكن" data-field="reading"
                                        @checked(old('reading', $student->reading ?? '') == 'متمكن')
                                    class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                    <span>متمكن <span class="text-xs text-gray-400 font-normal">(بدون أحكام)</span></span>
                                </label>
                                <label class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-[#0a5c36]/50 transition-all text-sm font-semibold text-gray-600">
                                    <input type="radio" name="reading" value="متقن" data-field="reading"
                                        @checked(old('reading', $student->reading ?? '') == 'متقن')
                                    class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                    <span>متقن <span class="text-xs text-gray-400 font-normal">(توجد أحكام)</span></span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4 pt-2">
                            <label class="block text-sm font-bold text-gray-700">اختر مستوى تحضير أو التحاق الطالب بعد الاختبار
                                <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label id="card-construction"
                                    class="flex flex-col p-4 border rounded-2xl cursor-pointer hover:border-[#0a5c36] transition-all border-gray-100 bg-gray-50">
                                    <div class="flex items-center gap-2 font-bold text-[#0a5c36]">
                                        <input type="radio" name="center_entry_level" value="construction"
                                            x-model="selectedLevel"
                                            data-field="center_entry_level" required
                                            class="text-[#0a5c36] focus:ring-[#0a5c36]">
                                        <span>🌱 مستوى البناء</span>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-2 mr-5">الحلقات التأسيسية وحفظ الأجزاء المنتظمة</span>
                                </label>

                                <label id="card-itqan"
                                    class="flex flex-col p-4 border rounded-2xl cursor-pointer hover:border-[#7a6020] transition-all border-gray-100 bg-gray-50">
                                    <div class="flex items-center gap-2 font-bold text-[#7a6020]">
                                        <input type="radio" name="center_entry_level" value="mastery"
                                            x-model="selectedLevel"
                                            data-field="center_entry_level"
                                            class="text-[#b8973a] focus:ring-[#b8973a]">
                                        <span>⭐ مستوى الإتقان</span>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-2 mr-5">حلقات التثبيت، المراجعة المكثفة والخاتمين</span>
                                </label>

                                <label id="card-ibda"
                                    class="flex flex-col p-4 border rounded-2xl cursor-pointer hover:border-indigo-600 transition-all border-gray-100 bg-gray-50">
                                    <div class="flex items-center gap-2 font-bold text-indigo-800">
                                        <input type="radio" name="center_entry_level" value="creativity"
                                            x-model="selectedLevel"
                                            data-field="center_entry_level"
                                            class="text-indigo-600 focus:ring-indigo-500">
                                        <span>🏆 مستوى الإبداع</span>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-2 mr-5">مجالس الإجازات، القراءات والسند المتصل</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- --------------------------------------------------- مستوى البناء ------------------------------------- -->
                <div id="step-6" x-show="selectedLevel === 'construction'" x-transition class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                        <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">🌱</div>
                        <div>
                            <h2 class="text-xl font-black text-[#0a5c36]">مستوى البناء</h2>
                            <p class="text-xs text-gray-400 mt-1">تسكين الطالب في الحلقات وخطة الحفظ</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">سورة الالتحاق الحالية <span
                                class="text-red-500">*</span></label>
                        <select name="current_surah" id="currentSurah" data-field="current_surah"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">

                            <option value="" @selected(old('current_surah', $construction->current_surah ?? '') == '')>-- اختر السورة أو حالة الالتحاق --</option>

                            <option value="بداية" @selected(old('current_surah', $construction->current_surah ?? '') == 'بداية') class="font-bold text-[#0a5c36]">🟢 بداية (مبتدئ تماماً)</option>
                            <option value="خاتم" @selected(old('current_surah', $construction->current_surah ?? '') == 'خاتم') class="font-bold text-indigo-600">👑 خاتم (حفظ القرآن كاملاً)</option>

                            @foreach([
                            'الفاتحة', 'البقرة', 'آل عمران', 'النساء', 'المائدة', 'الأنعام', 'الأعراف', 'الأنفال', 'التوبة', 'يونس',
                            'هود', 'يوسف', 'الرعد', 'إبراهيم', 'الحجر', 'النحل', 'الإسراء', 'الكهف', 'مريم', 'طه', 'الأنبياء',
                            'الحج', 'المؤمنون', 'النور', 'الفرقان', 'الشعراء', 'النمل', 'القصص', 'العنكبوت', 'الروم', 'لقمان',
                            'السجدة', 'الأحزاب', 'سبأ', 'فاطر', 'يس', 'الصافات', 'ص', 'الزمر', 'غافر', 'فصلت', 'الشورى',
                            'الزخرف', 'الدخان', 'الجاثية', 'الأحقاف', 'محمد', 'الفتح', 'الحجرات', 'ق', 'الذاريات', 'الطور',
                            'النجم', 'القمر', 'الرحمن', 'الواقعة', 'الحديد', 'المعادلة', 'الحشر', 'الممتحنة', 'الصف', 'الجمعة',
                            'المنافقون', 'التغابن', 'الطلاق', 'التحريم', 'الملك', 'القلم', 'الحاقة', 'المعارج', 'نوح', 'الجن',
                            'المزمل', 'المدثر', 'القيامة', 'الإنسان', 'المرسلات', 'النبأ', 'النازعات', 'عبس', 'التكوير',
                            'الانفطار', 'المطففين', 'الانشقاق', 'البروج', 'الطارق', 'الأعلى', 'الغاشية', 'الفجر', 'البلد',
                            'الشمس', 'الليل', 'الضحى', 'الشرح', 'التين', 'العلق', 'القدر', 'البينة', 'الزلزلة', 'العاديات',
                            'القارعة', 'التكاثر', 'العصر', 'الهمزة', 'الفيل', 'قريش', 'الماعون', 'الكوثر', 'الكافرون',
                            'النصر', 'المسد', 'الإخلاص', 'الفلق', 'الناس'
                            ] as $index => $value)
                            <option value="{{ $value }}" @selected(old('current_surah', $construction->current_surah ?? '') == $value)>
                                {{ $index + 1 }}. {{ $value }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ====== تعديل 4: النظام المتبع مبني على الحلقة ====== --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">اسم الحلقة <span class="text-red-500">*</span></label>
                            <select name="group_name" data-field="group_name" id="circleSelect"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">

                                <option value="" data-type="" @selected(old('group_name', $construction->group_name ?? '') == '')>-- اختر الحلقة --</option>

                                @foreach($circles as $circle)
                                <option value="{{ $circle->name }}"
                                    data-type="{{ $circle->type ?? '' }}"
                                    @selected(old('group_name', $construction->group_name ?? '') == $circle->name)>
                                    {{ $circle->name }}
                                </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">النظام المتبع <span class="text-red-500">*</span></label>
                            <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100" id="studySystemWrapper">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="study_system" value="فردي" data-field="study_system"
                                        @checked(old('study_system', $construction->study_system ?? '') == 'فردي')>
                                    <span>فردي</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="study_system" value="جماعي" data-field="study_system"
                                        @checked(old('study_system', $construction->study_system ?? '') == 'جماعي')>
                                    <span>جماعي</span>
                                </label>
                            </div>
                            <p id="studySystemHint" class="text-xs text-gray-400 hidden">يُحدَّد تلقائياً من نوع الحلقة المختارة</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">خطة الحفظ الجديد <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="new_memorization_plan" data-field="new_memorization_plan"
                                value="{{ old('new_memorization_plan', $construction->new_memorization_plan ?? '') }}"
                                placeholder="مثال: حفظ 5 سطور"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">مستوى الحفظ بعد الاختبار <span
                                    class="text-red-500">*</span></label>
                            <select name="placement_evaluation" data-field="placement_evaluation" class="w-full h-12 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
                                <option value="" @selected(old('placement_evaluation', $construction->placement_evaluation ?? '') == '')>-- اختر التقييم --</option>
                                <option value="ممتاز" @selected(old('placement_evaluation', $construction->placement_evaluation ?? '') == 'ممتاز')>ممتاز</option>
                                <option value="جيد" @selected(old('placement_evaluation', $construction->placement_evaluation ?? '') == 'جيد')>جيد</option>
                                <option value="تثبيت" @selected(old('placement_evaluation', $construction->placement_evaluation ?? '') == 'تثبيت')>تثبيت</option>
                                <option value="تأسيس" @selected(old('placement_evaluation', $construction->placement_evaluation ?? '') == 'تأسيس')>تأسيس</option>
                                <option value="إعادة حفظ" @selected(old('placement_evaluation', $construction->placement_evaluation ?? '') == 'إعادة حفظ')>إعادة حفظ</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">خطة الحفظ القديم (المراجعة) <span
                                class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="old_memorization_plan" value="منتهي" data-field="old_memorization_plan"
                                    @checked(old('old_memorization_plan', $construction->old_memorization_plan ?? '') == 'منتهي')>
                                <span>منتهي</span>
                            </label>

                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="old_memorization_plan" value="فئة الماهر" data-field="old_memorization_plan"
                                    @checked(old('old_memorization_plan', $construction->old_memorization_plan ?? '') == 'فئة الماهر')>
                                <span>فئة الماهر</span>
                            </label>

                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="old_memorization_plan" value="فئة المرتل" data-field="old_memorization_plan"
                                    @checked(old('old_memorization_plan', $construction->old_memorization_plan ?? '') == 'فئة المرتل')>
                                <span>فئة المرتل</span>
                            </label>

                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="old_memorization_plan" value="ترديد" data-field="old_memorization_plan"
                                    @checked(old('old_memorization_plan', $construction->old_memorization_plan ?? '') == 'ترديد')>
                                <span>ترديد</span>
                            </label>

                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="old_memorization_plan" value="أخرى" data-field="old_memorization_plan"
                                    @checked(old('old_memorization_plan', $construction->old_memorization_plan ?? '') == 'أخرى')>
                                <span>أخرى</span>
                            </label>
                        </div>
                        {{-- يظهر عند اختيار أخرى --}}
                        <input type="text" name="old_memorization_plan_other" data-field="old_memorization_plan_other"
                            value="{{ old('old_memorization_plan_other', $construction->old_memorization_plan_other ?? '') }}"
                            data-show-when="old_memorization_plan=أخرى"
                            placeholder="يرجى توضيح الخطة الإضافية..."
                            class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all"
                            style="display:none;">
                    </div>

                </div>

                <!-- --------------------------------------------------- مستوى الإتقان -------------------------------------- -->
                <div id="step-7" x-show="selectedLevel === 'mastery'" x-transition class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
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
                            <input type="text" name="previous_memorization_side" data-field="previous_memorization_side"
                                value="{{ old('previous_memorization_side', $itqan->previous_memorization_side ?? '') }}"
                                placeholder="اسم المسجد، المركز، أو الشيخ السابق"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">عدد الختمات السابقة <span class="text-red-500">*</span></label>
                            <input type="text" name="previous_khatamat_count" data-field="previous_khatamat_count"
                                value="{{ old('previous_khatamat_count', $itqan->previous_khatamat_count ?? '') }}"
                                placeholder="مثال: ختمة واحدة أو أكثر"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">مقدار المراجعة الحالي (الورد اليومي) <span class="text-red-500">*</span></label>
                        <input type="text" name="current_review_amount" data-field="current_review_amount"
                            value="{{ old('current_review_amount', $itqan->current_review_amount ?? '') }}"
                            placeholder="مثال: جزء يوميًّا، حزب، نصف جزء..."
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">تقييم مستوى الحفظ من المتقدم (1-10) <span class="text-red-500">*</span></label>
                        <select name="self_evaluation"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all appearance-none">
                            <option value="">-- اختر التقييم --</option>
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @selected(old('self_evaluation', $itqan?->self_evaluation ?? 0) == $i)>{{ $i }}</option>
                                @endfor
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">متون التجويد المحفوظة بإتقان <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="tajweed_matn" value="لا يوجد" data-field="tajweed_matn"
                                    @checked(old('tajweed_matn', $itqan->memorized_texts ?? '') == 'لا يوجد')>
                                <span>لا يوجد</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="tajweed_matn" value="التحفة" data-field="tajweed_matn"
                                    @checked(old('tajweed_matn', $itqan->memorized_texts ?? '') == 'التحفة')>
                                <span>تحفة الأطفال</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="tajweed_matn" value="الجزرية" data-field="tajweed_matn"
                                    @checked(old('tajweed_matn', $itqan->memorized_texts ?? '') == 'الجزرية')>
                                <span>المقدمة الجزرية</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                <input type="radio" name="tajweed_matn" value="أخرى" data-field="tajweed_matn"
                                    @checked(old('tajweed_matn', $itqan->memorized_texts ?? '') == 'أخرى')>
                                <span>أخرى</span>
                            </label>
                        </div>
                        {{-- يظهر عند اختيار أخرى --}}
                        <input type="text" name="tajweed_matn_other" data-field="tajweed_matn_other"
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
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="desired_path" value="تثبيت الحفظ" data-field="desired_path"
                                        @checked(old('desired_path', $itqan->desired_path ?? '') == 'تثبيت الحفظ')>
                                    <span>تثبيت الحفظ وتجويده</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="desired_path" value="تصحيح التلاوة" data-field="desired_path"
                                        @checked(old('desired_path', $itqan->desired_path ?? '') == 'تصحيح التلاوة')>
                                    <span>تصحيح التلاوة والنطق</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="desired_path" value="الإجازة والسند" data-field="desired_path"
                                        @checked(old('desired_path', $itqan->desired_path ?? '') == 'الإجازة والسند')>
                                    <span>الإجازة والسند المتصل</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">الوقت المناسب للمجلس <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="preferred_time" value="صباحًا" data-field="preferred_time"
                                        @checked(old('preferred_time', $itqan->preferred_time ?? '') == 'صباحًا')>
                                    <span>صباحًا</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="preferred_time" value="ظهرًا" data-field="preferred_time"
                                        @checked(old('preferred_time', $itqan->preferred_time ?? '') == 'ظهرًا')>
                                    <span>ظهرًا</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="preferred_time" value="عصرًا" data-field="preferred_time"
                                        @checked(old('preferred_time', $itqan->preferred_time ?? '') == 'عصرًا')>
                                    <span>عصرًا</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer">
                                    <input type="radio" name="preferred_time" value="ليلًا" data-field="preferred_time"
                                        @checked(old('preferred_time', $itqan->preferred_time ?? '') == 'ليلًا')>
                                    <span>ليلًا</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer col-span-2">
                                    <input type="radio" name="preferred_time" value="أون لاين" data-field="preferred_time"
                                        @checked(old('preferred_time', $itqan->preferred_time ?? '') == 'أون لاين')>
                                    <span>أون لاين (عبر الإنترنت)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">المعلم المفضل للمجلس (مستوى الإتقان) <span class="text-red-500">*</span></label>
                        <select name="teacher_name" data-field="teacher_name"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all appearance-none">
                            <option value="" @selected(old('teacher_name', $itqan->teacher_name ?? '') == '')>-- اختر المعلم --</option>
                            <option value="بدون تحديد" @selected(old('teacher_name', $itqan->teacher_name ?? '') == 'بدون تحديد')>بدون تحديد (حسب المتاح)</option>
                            @foreach ($teachers ?? [] as $teacher)
                            <option value="{{ $teacher->name }}" @selected(old('teacher_name', $itqan->teacher_name ?? '') == $teacher->name)>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- --------------------------------------------------- مستوى الإبداع -------------------------------------- -->
                <div id="step-8" x-show="selectedLevel === 'creativity'" x-transition class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl text-xl">🏆</div>
                        <div>
                            <h2 class="text-xl font-black text-indigo-600">مستوى الإبداع</h2>
                            <p class="text-xs text-gray-400 mt-1">بيانات الروايات والأسانيد التي حصل عليها الطالب</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">الإجازات والأسانيد التي حصل عليها سابقًا <span class="text-red-500">*</span></label>
                        <textarea name="previous_licenses_and_chains" data-field="description"
                            placeholder="يرجى ذكر الإجازات، اسم الشيخ المجيز، والمتن المكتوب بالتفصيل..."
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all min-h-[100px]">{{ old('previous_licenses_and_chains', $ibda->previous_licenses_and_chains ?? '') }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">المسار والرواية المراد دراستها حاليًا <span class="text-red-500">*</span></label>
                        <input type="text" name="desired_narration_and_path" data-field="desired_narration_and_path"
                            value="{{ old('desired_narration_and_path', $ibda->desired_narration_and_path ?? '') }}"
                            placeholder="مثال: رواية ورش عن نافع، القراءات العشر..."
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">الوقت المناسب للمجلس <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer">
                                <input type="radio" name="preferred_time" value="صباحًا" data-field="preferred_time"
                                    @checked(old('preferred_time', $ibda->preferred_time ?? '') == 'صباحًا')>
                                <span>صباحًا</span>
                            </label>
                            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer">
                                <input type="radio" name="preferred_time" value="ظهرًا" data-field="preferred_time"
                                    @checked(old('preferred_time', $ibda->preferred_time ?? '') == 'ظهرًا')>
                                <span>ظهرًا</span>
                            </label>
                            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer">
                                <input type="radio" name="preferred_time" value="عصرًا" data-field="preferred_time"
                                    @checked(old('preferred_time', $ibda->preferred_time ?? '') == 'عصرًا')>
                                <span>عصرًا</span>
                            </label>
                            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer">
                                <input type="radio" name="preferred_time" value="ليلًا" data-field="preferred_time"
                                    @checked(old('preferred_time', $ibda->preferred_time ?? '') == 'ليلًا')>
                                <span>ليلًا</span>
                            </label>
                            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer">
                                <input type="radio" name="preferred_time" value="أون لاين" data-field="preferred_time"
                                    @checked(old('preferred_time', $ibda->preferred_time ?? '') == 'أون لاين')>
                                <span>عن بُعد</span>
                            </label>
                        </div>

                        <div class="space-y-2 mt-4">
                            <label class="block text-sm font-bold text-gray-700">المعلم المفضل للمجلس (مستوى الإبداع) <span class="text-red-500">*</span></label>
                            <select name="supervisor_name" data-field="supervisor_name"
                                class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all appearance-none">
                                <option value="">-- اختر المعلم --</option>
                                @foreach ($teachers ?? [] as $teacher)
                                <option value="{{ $teacher->name }}" @selected(old('supervisor_name', $ibda->supervisor_name ?? '') == $teacher->name)>{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ------------------------------- التوصيات النهائية والملاحظات الإدارية -------------------------------- -->
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
                        <input type="text" name="subscription_fees" data-field="subscription_fees"
                            value="{{ old('subscription_fees', $student->subscription_fees ?? '') }}"
                            placeholder="مثال: 150"
                            class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-gray-600">الأدوات والكتب المستلمة</label>
                        <select name="received_tools" data-field="received_tools"
                            class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium appearance-none">
                            <option value="" @selected(old('received_tools', $student->received_tools ?? '') == '')>-- اختر نوع العهدة --</option>
                            <option value="لم يأخذ شيء" @selected(old('received_tools', $student->received_tools ?? '') == 'لم يأخذ شيء')>لم يأخذ شيء</option>
                            <option value="المصحف فقط" @selected(old('received_tools', $student->received_tools ?? '') == 'المصحف فقط')>المصحف فقط</option>
                            <option value="المتابعة فقط" @selected(old('received_tools', $student->received_tools ?? '') == 'المتابعة فقط')>دفتر المتابعة فقط</option>
                            <option value="المصحف والمتابعة" @selected(old('received_tools', $student->received_tools ?? '') == 'المصحف والمتابعة')>المصحف ودفتر المتابعة معًا</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    @can('edit students')
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">حالة الطالب</label>
                        <select name="status" data-field="status"
                            class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm">
                            <option value="active" @selected(old('status', $student->status ?? 'active') == 'active')>مقيد</option>
                            <option value="inactive" @selected(old('status', $student->status ?? '') == 'inactive')>موقوف</option>
                            <option value="traveler" @selected(old('status', $student->status ?? '') == 'traveler')>مسافر</option>
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="status" value="{{ old('status', $student->status ?? 'active') }}">
                    @endcan

                    @can('manage student status')
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">قرار الإدارة</label>
                        <select name="decision" data-field="decision"
                            class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm">
                            <option value="pending" @selected(old('decision', $student->decision ?? 'pending') == 'pending')>تحت الاختبار</option>
                            <option value="accepted" @selected(old('decision', $student->decision ?? '') == 'accepted')>مقبول</option>
                            <option value="rejected" @selected(old('decision', $student->decision ?? '') == 'rejected')>مرفوض</option>
                        </select>
                    </div>
                    @else
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">قرار الإدارة</label>
                        <div class="w-full p-3 bg-gray-100 border border-gray-200 rounded-2xl text-sm text-gray-600 flex items-center gap-2">
                            @php $decision = old('decision', $student->decision ?? 'pending'); @endphp
                            @if($decision === 'accepted')
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> مقبول
                            @elseif($decision === 'rejected')
                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> مرفوض
                            @else
                            <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> تحت الاختبار
                            @endif
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
                    <textarea name="notes" data-field="notes" placeholder="اكتب التوصيات الخاصة بمخارج الحروف والتجويد..."
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm min-h-[100px]">{{ old('notes', $student->notes ?? '') }}</textarea>
                </div>

            </div>

            <div class="flex justify-end items-center pt-6 border-t border-gray-100 mt-8">
                <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-[#0a5c36] hover:bg-[#084d2d] text-white font-black rounded-2xl shadow-md transition-all text-sm">
                    حفظ البيانات وإرسال النموذج ✓
                </button>
            </div>

        </div>

        {{-- ============================================================
         JavaScript — كل المنطق في مكان واحد
         ============================================================ --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ================================================================
                // 1) data-show-when — يُظهر/يُخفي الحقل بناءً على قيمة حقل آخر
                //    يدعم: radio buttons + select
                //    الصيغة: data-show-when="fieldName=expectedValue"
                // ================================================================
                function initShowWhen(input) {
                    const condition = input.getAttribute('data-show-when');
                    if (!condition) return;

                    const eqIndex = condition.indexOf('=');
                    const fieldName = condition.substring(0, eqIndex);
                    const expected = condition.substring(eqIndex + 1);

                    function getVal() {
                        // radio
                        const checked = document.querySelector(`[name="${fieldName}"]:checked`);
                        if (checked) return checked.value;
                        // select
                        const sel = document.querySelector(`select[name="${fieldName}"]`);
                        if (sel) return sel.value;
                        return '';
                    }

                    function toggle() {
                        const show = getVal() === expected;
                        input.style.display = show ? (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA' ? 'block' : 'grid') : 'none';
                        // لا نمسح القيمة عند الإخفاء حتى لا نفقد البيانات عند التبديل السريع
                    }

                    // ربط الاستماع على جميع عناصر الحقل المرتبط
                    document.querySelectorAll(`[name="${fieldName}"]`).forEach(el => {
                        el.addEventListener('change', toggle);
                    });

                    // تشغيل فوري عند التحميل
                    toggle();
                }

                // تطبيق على كل الحقول التي تحمل data-show-when
                document.querySelectorAll('[data-show-when]').forEach(initShowWhen);

                // ================================================================
                // 2) الهواية "أخرى" — checkbox خاص
                // ================================================================
                const hobbyCheckbox = document.getElementById('hobbyOtherCheckbox');
                const hobbyOtherInput = document.getElementById('hobbyOtherInput');

                function toggleHobbyOther() {
                    if (!hobbyCheckbox || !hobbyOtherInput) return;
                    hobbyOtherInput.style.display = hobbyCheckbox.checked ? 'block' : 'none';
                }

                if (hobbyCheckbox) {
                    hobbyCheckbox.addEventListener('change', toggleHobbyOther);
                    toggleHobbyOther(); // تشغيل فوري
                }

                // ================================================================
                // 3) الحلقة → تحديد النظام المتبع تلقائياً
                // ================================================================
                const circleSelect = document.getElementById('circleSelect');
                const studySystemHint = document.getElementById('studySystemHint');

                function autoSelectSystem() {
                    if (!circleSelect) return;
                    const selectedOption = circleSelect.options[circleSelect.selectedIndex];
                    const systemType = selectedOption ? selectedOption.getAttribute('data-type') : '';

                    if (systemType === 'فردي' || systemType === 'جماعي') {
                        const radio = document.querySelector(`input[name="study_system"][value="${systemType}"]`);
                        if (radio) {
                            radio.checked = true;
                            if (studySystemHint) studySystemHint.classList.remove('hidden');
                        }
                    } else {
                        if (studySystemHint) studySystemHint.classList.add('hidden');
                    }
                }

                if (circleSelect) {
                    circleSelect.addEventListener('change', autoSelectSystem);
                    if (circleSelect.value !== '') autoSelectSystem();
                }

                // ================================================================
                // 4) البحث برقم الواتساب → تحديد ولي الأمر تلقائياً
                // ================================================================
                const whatsappInput = document.getElementById('whatsappInput');
                const guardianSelect = document.getElementById('guardianSelect');
                const emailInput = document.getElementById('parentEmailInput');
                const passwordInput = document.getElementById('passwordInput');
                const searchResult = document.getElementById('guardianSearchResult');

                function debounce(fn, ms) {
                    let timer;
                    return function(...args) {
                        clearTimeout(timer);
                        timer = setTimeout(() => fn.apply(this, args), ms);
                    };
                }

                function showResult(msg, type) {
                    if (!searchResult) return;
                    searchResult.textContent = msg;
                    searchResult.className = 'mt-1 text-xs font-semibold px-2 py-1 rounded-xl ' +
                        (type === 'found' ?
                            'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                            'bg-amber-50 text-amber-700 border border-amber-100');
                    searchResult.classList.remove('hidden');
                }

                if (whatsappInput && guardianSelect) {
                    whatsappInput.addEventListener('input', debounce(async function() {
                        const phone = this.value.trim();

                        if (phone.length < 10) {
                            if (searchResult) searchResult.classList.add('hidden');
                            return;
                        }

                        try {
                            const res = await fetch(`/api/users/find-by-phone?phone=${encodeURIComponent(phone)}`);
                            const data = await res.json();

                            if (data.found) {
                                // ولي الأمر موجود في النظام
                                guardianSelect.value = data.user_id;
                                // إخفاء حقول الحساب الجديد عبر إطلاق حدث change
                                guardianSelect.dispatchEvent(new Event('change'));
                                showResult('✅ تم العثور على ولي الأمر: ' + (data.name ?? ''), 'found');
                            } else {
                                // غير موجود → حساب جديد
                                guardianSelect.value = 'new';
                                guardianSelect.dispatchEvent(new Event('change'));

                                // تعبئة تلقائية بالرقم (فقط إذا لم يُعدِّل المستخدم الحقل يدوياً)
                                if (emailInput && !emailInput.dataset.userModified) {
                                    emailInput.value = phone;
                                }
                                if (passwordInput && !passwordInput.dataset.userModified) {
                                    passwordInput.value = phone;
                                }

                                showResult('⚠️ لم يُوجد ولي أمر بهذا الرقم — سيُنشأ حساب جديد', 'new');
                            }
                        } catch (e) {
                            // في حالة خطأ في الشبكة نتجاهل بصمت
                            console.warn('guardian search error', e);
                        }
                    }, 700));
                }

                // تتبع التعديل اليدوي على حقلي الحساب
                if (emailInput) {
                    emailInput.addEventListener('input', () => {
                        emailInput.dataset.userModified = '1';
                    });
                }
                if (passwordInput) {
                    passwordInput.addEventListener('input', () => {
                        passwordInput.dataset.userModified = '1';
                    });
                }

                // ================================================================
                // 5) زر إظهار/إخفاء كلمة المرور
                // ================================================================
                const togglePasswordBtn = document.querySelector('[data-action="togglePassword"]');
                const showIcon = document.getElementById('passwordShowIcon');
                const hideIcon = document.getElementById('passwordHideIcon');

                if (togglePasswordBtn && passwordInput) {
                    togglePasswordBtn.addEventListener('click', function() {
                        const isPassword = passwordInput.type === 'password';
                        passwordInput.type = isPassword ? 'text' : 'password';
                        if (showIcon) showIcon.style.display = isPassword ? 'none' : 'inline';
                        if (hideIcon) hideIcon.style.display = isPassword ? 'inline' : 'none';
                    });
                }

            });
        </script>