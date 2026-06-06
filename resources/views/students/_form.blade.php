<div x-show="activeTab === 2" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6" x-cloak>
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
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="applicant" value="الأم" x-model="formData.applicant" class="rounded-full text-[#0a5c36] focus:ring-[#0a5c36]"> <span>الأم</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="applicant" value="الأب" x-model="formData.applicant" class="rounded-full text-[#0a5c36] focus:ring-[#0a5c36]"> <span>الأب</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="applicant" value="الطالب" x-model="formData.applicant" class="rounded-full text-[#0a5c36] focus:ring-[#0a5c36]"> <span>الطالب نفسه</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="applicant" value="أخرى" x-model="formData.applicant" class="rounded-full text-[#0a5c36] focus:ring-[#0a5c36]"> <span>أخرى</span></label>
        </div>
        @error('applicant')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
        <input type="text" name="applicant_other" x-model="formData.applicant_other" x-show="formData.applicant === 'أخرى'" placeholder="يرجى تحديد مقدم الطلب..." class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">اسم الطالب (رباعيًّا) <span class="text-red-500">*</span></label>
            <input type="text" name="name" x-model="formData.name" placeholder="الاسم الرباعي كاملًا" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all" required>
            @error('name')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">كود الطالب (F)</label>
            <input type="text" name="student_code" x-model="formData.student_code" placeholder="يتم توليده تلقائيًا" disabled class="w-full p-3 bg-gray-100 border border-gray-200 rounded-2xl text-sm font-medium text-gray-400 cursor-not-allowed">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">النوع <span class="text-red-500">*</span></label>
            <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="gender" value="ذكر" x-model="formData.gender" class="text-[#0a5c36] focus:ring-[#0a5c36]" required> <span>ذكر</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="gender" value="أنثى" x-model="formData.gender" class="text-[#0a5c36] focus:ring-[#0a5c36]" required> <span>أنثى</span></label>
            </div>
            @error('gender')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">تاريخ الميلاد</label>
            <input type="date" name="date_of_birth" x-model="formData.date_of_birth" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
            @error('date_of_birth')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">العنوان التفصيلي (المركز - القرية - الشارع) <span class="text-red-500">*</span></label>
        <input type="text" name="address" x-model="formData.address" placeholder="مثال: المنصورة - قرية النصر - شارع الجمهورية" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all" required>
        @error('address')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
    </div>

    <div class="border-t border-gray-100 my-6"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">رقم الواتساب للمتابعة</label>
            <input type="tel" name="whatsapp_number" x-model="formData.whatsapp_number" placeholder="01xxxxxxxxx" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
            @error('whatsapp_number')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">صاحب رقم الواتساب</label>
            <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="whatsapp_owner" value="الأم" x-model="formData.whatsapp_owner"> <span>الأم</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="whatsapp_owner" value="الأب" x-model="formData.whatsapp_owner"> <span>الأب</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="whatsapp_owner" value="الطالب" x-model="formData.whatsapp_owner"> <span>الطالب</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="whatsapp_owner" value="أخرى" x-model="formData.whatsapp_owner"> <span>أخرى</span></label>
            </div>
            @error('whatsapp_owner')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
            @enderror
            <input type="text" name="whatsapp_owner_other" x-model="formData.whatsapp_owner_other" x-show="formData.whatsapp_owner === 'أخرى'" placeholder="يرجى التحديد..." class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">رقم اتصال إضافي</label>
        <input type="tel" name="second_phone" x-model="formData.second_phone" placeholder="01xxxxxxxxx" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
        @error('second_phone')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex justify-between items-center pt-6 border-t border-gray-50 mt-8">
        <button type="button" @click="prevStep()" class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm">
            <span>→ السابق</span>
        </button>
        <button type="button" @click="nextStep()" class="flex items-center gap-2 px-6 py-3 bg-[#0a5c36] hover:bg-[#074427] text-white font-bold rounded-2xl shadow-md hover:shadow-lg transition-all text-sm">
            <span>التالي ←</span>
        </button>
    </div>
</div>

<div x-show="activeTab === 3" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6" x-cloak>
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
            <select name="educational_stage" x-model="formData.educational_stage" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none" required>
                <option value="">-- اختر المرحلة --</option>
                <option value="تمهيدي">تمهيدي</option>
                <option value="حضانة">حضانة</option>
                <option value="ابتدائي">ابتدائي</option>
                <option value="اعدادي">اعدادي</option>
                <option value="ثانوي">ثانوي</option>
                <option value="جامعي">جامعي</option>
                <option value="خريج">خريج</option>
            </select>
            @error('educational_stage')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
            @enderror
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">نوع التعليم <span class="text-red-500">*</span></label>
            <select name="education_type" x-model="formData.education_type" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none" required>
                <option value="">-- اختر النوع --</option>
                <option value="غير محدد">غير محدد</option>
                <option value="أزهري">أزهري</option>
                <option value="عام (تربية وتعليم)">عام (تربية وتعليم)</option>
            </select>
            @error('education_type')
                <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">الصف الدراسي <span class="text-red-500">*</span></label>
        <select name="school_grade" x-model="formData.school_grade" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none" required>
            <option value="">-- اختر الصف --</option>
            <option value="لا يوجد">لا يوجد</option>
            <option value="الأول">الأول</option>
            <option value="الثاني">الثاني</option>
            <option value="الثالث">الثالث</option>
            <option value="الرابع">الرابع</option>
            <option value="الخامس">الخامس</option>
            <option value="السادس">السادس</option>
            <option value="دراسات عليا">دراسات عليا</option>
        </select>
        @error('school_grade')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">المؤسسة التعليمية (الحضانة / المدرسة / المعهد / الكلية) <span class="text-red-500">*</span></label>
        <input type="text" name="previous_school" x-model="formData.previous_school" placeholder="اسم المؤسسة التعليمية بالكامل" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all" required>
        @error('previous_school')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex justify-between items-center pt-6 border-t border-gray-50 mt-8">
        <button type="button" @click="prevStep()" class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm">
            <span>→ السابق</span>
        </button>
        <button type="button" @click="nextStep()" class="flex items-center gap-2 px-6 py-3 bg-[#0a5c36] hover:bg-[#074427] text-white font-bold rounded-2xl shadow-md hover:shadow-lg transition-all text-sm">
            <span>التالي ←</span>
        </button>
    </div>
</div>

<div x-show="activeTab === 4" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6" x-cloak>
    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
        <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">💚</div>
        <div>
            <h2 class="text-xl font-black text-[#0a5c36]">بيانات الرعاية الطلابية</h2>
            <p class="text-xs text-gray-400 mt-1">الحالة الصحية، السلوكية، والسمات الشخصية</p>
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">الحالة الصحية للطالب <span class="text-red-500">*</span></label>
        <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="health_status" value="طبيعية" x-model="formData.health_status" required> <span>طبيعية (الحمد لله)</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="health_status" value="أخرى" x-model="formData.health_status" required> <span>أخرى</span></label>
        </div>
        @error('health_status')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
        <input type="text" name="health_status_other" x-model="formData.health_status_other" x-show="formData.health_status === 'أخرى'" placeholder="يرجى توضيح الحالة الصحية..." class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">صعوبات التعلم <span class="text-red-500">*</span></label>
        <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="learning_difficulties" value="لا يوجد" x-model="formData.learning_difficulties" required> <span>لا يوجد (الحمد لله)</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="learning_difficulties" value="أخرى" x-model="formData.learning_difficulties" required> <span>أخرى</span></label>
        </div>
        @error('learning_difficulties')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
        <input type="text" name="learning_difficulties_other" x-model="formData.learning_difficulties_other" x-show="formData.learning_difficulties === 'أخرى'" placeholder="يرجى توضيح صعوبات التعلم..." class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">السمات الشخصية <span class="text-red-500">*</span></label>
        <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="personal_traits" value="لا يوجد" x-model="formData.personal_traits" required> <span>لا يوجد</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="personal_traits" value="أخرى" x-model="formData.personal_traits" required> <span>أخرى</span></label>
        </div>
        @error('personal_traits')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
        <input type="text" name="personal_traits_other" x-model="formData.personal_traits_other" x-show="formData.personal_traits === 'أخرى'" placeholder="يرجى تحديد السمات البارزة (عنيد، خجول...)" class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">الهواية المفضلة <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="checkbox" name="hobbies[]" value="كرة القدم" x-model="formData.hobbies" class="rounded text-[#0a5c36] focus:ring-[#0a5c36]"> <span>كرة القدم</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="checkbox" name="hobbies[]" value="الكاراتيه" x-model="formData.hobbies" class="rounded text-[#0a5c36] focus:ring-[#0a5c36]"> <span>الكاراتيه</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="checkbox" name="hobbies[]" value="الرسم" x-model="formData.hobbies" class="rounded text-[#0a5c36] focus:ring-[#0a5c36]"> <span>الرسم</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="checkbox" name="hobbies[]" value="البرمجة والألعاب الإلكترونية" x-model="formData.hobbies" class="rounded text-[#0a5c36] focus:ring-[#0a5c36]"> <span>البرمجة والألعاب</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="checkbox" name="hobbies[]" value="الأشغال اليدوية" x-model="formData.hobbies" class="rounded text-[#0a5c36] focus:ring-[#0a5c36]"> <span>الأشغال اليدوية</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="checkbox" name="hobbies[]" value="القراءة والإطلاع" x-model="formData.hobbies" class="rounded text-[#0a5c36] focus:ring-[#0a5c36]"> <span>القراءة</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="checkbox" name="hobbies[]" value="أخرى" x-model="formData.hobbies" class="rounded text-[#0a5c36] focus:ring-[#0a5c36]"> <span>أخرى</span></label>
        </div>
        <input type="text" name="hobby_other" x-model="formData.hobby_other" x-show="formData.hobbies && formData.hobbies.includes('أخرى')" placeholder="يرجى ذكر الهواية..." class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">حالة خروج الطالب من المركز <span class="text-red-500">*</span></label>
        <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="student_exit_status" value="بمفرده" x-model="formData.student_exit_status" required> <span>بمفرده</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="student_exit_status" value="مع ولي الأمر أو أحد الأقارب" x-model="formData.student_exit_status" required> <span>مع ولي الأمر أو أحد الأقارب</span></label>
        </div>
        @error('student_exit_status')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex justify-between items-center pt-6 border-t border-gray-50 mt-8">
        <button type="button" @click="prevStep()" class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm">
            <span>→ السابق</span>
        </button>
        <button type="button" @click="nextStep()" class="flex items-center gap-2 px-6 py-3 bg-[#0a5c36] hover:bg-[#074427] text-white font-bold rounded-2xl shadow-md hover:shadow-lg transition-all text-sm">
            <span>التالي ←</span>
        </button>
    </div>
</div>

<div x-show="activeTab === 5" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6" x-cloak>
    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
        <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">🎤</div>
        <div>
            <h2 class="text-xl font-black text-[#0a5c36]">مستوى الالتحاق وتقييم التلاوة</h2>
            <p class="text-xs text-gray-400 mt-1">تحديد مستوى الطالب الأولي وتوجيهه للمسار الصحيح</p>
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">مستوى القراءة من المصحف <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="quran_reading_level" value="مبتدئ" x-model="formData.quran_reading_level" required> <span>مبتدئ (لا يقرأ)</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="quran_reading_level" value="مقبول" x-model="formData.quran_reading_level" required> <span>مقبول (يقرأ ببطء)</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="quran_reading_level" value="متمكن" x-model="formData.quran_reading_level" required> <span>متمكن (بدون أحكام)</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="quran_reading_level" value="متقن" x-model="formData.quran_reading_level" required> <span>متقن (توجد أحكام تجويد)</span></label>
        </div>
        @error('quran_reading_level')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">مستوى الالتحاق بالمركز <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <label class="flex flex-col p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-[#0a5c36] transition-all" :class="centerEntryLevel === 'construction' ? 'border-[#0a5c36] bg-emerald-50/30' : ''">
                <div class="flex items-center gap-2 font-bold text-[#0a5c36]">
                    <input type="radio" name="center_entry_level" value="construction" x-model="centerEntryLevel" required class="text-[#0a5c36] focus:ring-[#0a5c36]">
                    <span>🌱 مستوى البناء</span>
                </div>
                <span class="text-xs text-gray-400 mt-2 mr-5">حلقات الحفظ والتأسيس المنتظمة</span>
            </label>
            <label class="flex flex-col p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-[#0a5c36] transition-all" :class="centerEntryLevel === 'itqan' ? 'border-[#0a5c36] bg-emerald-50/30' : ''">
                <div class="flex items-center gap-2 font-bold text-amber-600">
                    <input type="radio" name="center_entry_level" value="itqan" x-model="centerEntryLevel" required class="text-amber-600 focus:ring-amber-500">
                    <span>⭐ مستوى الإتقان</span>
                </div>
                <span class="text-xs text-gray-400 mt-2 mr-5">حلقات التثبيت، الخاتمين، وضبط المراجعة</span>
            </label>
            <label class="flex flex-col p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-[#0a5c36] transition-all" :class="centerEntryLevel === 'ibda' ? 'border-[#0a5c36] bg-emerald-50/30' : ''">
                <div class="flex items-center gap-2 font-bold text-indigo-600">
                    <input type="radio" name="center_entry_level" value="ibda" x-model="centerEntryLevel" required class="text-indigo-600 focus:ring-indigo-500">
                    <span>🏆 مستوى الإبداع</span>
                </div>
                <span class="text-xs text-gray-400 mt-2 mr-5">مجالس الإجازة، السند، والقراءات</span>
            </label>
        </div>
        @error('center_entry_level')
            <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex justify-between items-center pt-6 border-t border-gray-50 mt-8">
        <button type="button" @click="prevStep()" class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm">
            <span>→ السابق</span>
        </button>
        <button type="button" @click="nextStep()" class="flex items-center gap-2 px-6 py-3 bg-[#0a5c36] hover:bg-[#074427] text-white font-bold rounded-2xl shadow-md hover:shadow-lg transition-all text-sm">
            <span>التالي ←</span>
        </button>
    </div>
</div>

<div x-show="activeTab === 6 && centerEntryLevel === 'construction'" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6" x-cloak>
    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
        <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">🌱</div>
        <div>
            <h2 class="text-xl font-black text-[#0a5c36]">مستوى البناء</h2>
            <p class="text-xs text-gray-400 mt-1">تسكين الطالب في الحلقات وخطة الحفظ</p>
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">سورة الالتحاق الحالية <span class="text-red-500">*</span></label>
        <select name="current_surah" id="currentSurah" x-model="formData.current_surah" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
            <option value="">-- اختر السورة --</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">النظام المتبع <span class="text-red-500">*</span></label>
            <div class="flex gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="enrollment_type" value="فردي" x-model="formData.enrollment_type"> <span>فردي</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="enrollment_type" value="جماعي" x-model="formData.enrollment_type"> <span>جماعي</span></label>
            </div>
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">اسم الحلقة <span class="text-red-500">*</span></label>
            <select name="circle_name" x-model="formData.circle_name" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                <option value="">-- اختر الحلقة --</option>
                <option value="تمهيدي">تمهيدي</option>
                <option value="الأرقم بن أبي الأرقم">الأرقم بن أبي الأرقم</option>
                <option value="معاوية بن أبي سفيان">معاوية بن أبي سفيان</option>
                <option value="عبدالرحمن بن عوف">عبدالرحمن بن عوف</option>
                <option value="أبي بن كعب">أبي بن كعب</option>
                <option value="عثمان بن عفان">عثمان بن عفan</option>
                <option value="معاذ بن جبل">معاذ بن جبل</option>
                <option value="زيد بن ثابت">زيد بن ثابت</option>
                <option value="الزبير بن العوام">الزبير بن العوام</option>
                <option value="حليمه مسعود">حليمه مسعود</option>
                <option value="سعد الغامدي">سعد الغامدي</option>
                <option value="حفصة بنت عمر بن الخطاب">حفصة بنت عمر بن الخطاب</option>
                <option value="أم سلمة">أم سلمة</option>
                <option value="عائشة بنت أبي بكر الصديق">عائشة بنت أبي بكر الصديق</option>
                <option value="خديجة بنت خويلد">خديجة بنت خويلد</option>
                <option value="عبدالله بن مسعود">عبدالله بن مسعود</option>
                <option value="عمر بن الخطاب">عمر بن الخطاب</option>
                <option value="أبوبكر الصديق">أبوبكر الصديق</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">خطة الحفظ الجديد <span class="text-red-500">*</span></label>
            <select name="new_memorization_plan" x-model="formData.new_memorization_plan" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                <option value="">-- اختر مقدار الحفظ --</option>
                <option value="ترديد">ترديد</option>
                <option value="ثلاثة سطور">ثلاثة سطور</option>
                <option value="خمسة سطور">خمسة سطور</option>
                <option value="نصف وجه">نصف وجه</option>
                <option value="عشرة سطور">عشرة سطور</option>
                <option value="وجه واحد">وجه واحد</option>
                <option value="وجه ونصف">وجه ونصف</option>
                <option value="وجهان">وجهان</option>
                <option value="ربع واحد">ربع واحد</option>
            </select>
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">مستوى الحفظ بعد الاختبار <span class="text-red-500">*</span></label>
            <select name="behavior_level" x-model="formData.behavior_level" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all appearance-none">
                <option value="">-- اختر التقييم --</option>
                <option value="ممتاز">ممتاز</option>
                <option value="جيد">جيد</option>
                <option value="تثبيت">تثبيت</option>
                <option value="تأسيس">تأسيس</option>
                <option value="إعادة حفظ">إعادة حفظ</option>
            </select>
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">خطة الحفظ القديم (المراجعة) <span class="text-red-500">*</span></label>
        <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="old_memorization_plan" value="منتهي" x-model="formData.old_memorization_plan"> <span>منتهي</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="old_memorization_plan" value="فئة الماهر" x-model="formData.old_memorization_plan"> <span>فئة الماهر</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="old_memorization_plan" value="فئة المرتل" x-model="formData.old_memorization_plan"> <span>فئة المرتل</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="old_memorization_plan" value="ترديد" x-model="formData.old_memorization_plan"> <span>ترديد</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="old_memorization_plan" value="أخرى" x-model="formData.old_memorization_plan"> <span>أخرى</span></label>
        </div>
        <input type="text" name="old_memorization_plan_other" x-model="formData.old_memorization_plan_other" x-show="formData.old_memorization_plan === 'أخرى'" placeholder="يرجى توضيح الخطة الإضافية..." class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
    </div>

    <div class="flex justify-between items-center pt-6 border-t border-gray-50 mt-8">
        <button type="button" @click="prevStep()" class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm">
            <span>→ السابق</span>
        </button>
        <button type="button" @click="nextStep()" class="flex items-center gap-2 px-6 py-3 bg-[#0a5c36] hover:bg-[#074427] text-white font-bold rounded-2xl shadow-md hover:shadow-lg transition-all text-sm">
            <span>التالي ←</span>
        </button>
    </div>
</div>

<div x-show="activeTab === 7 && centerEntryLevel === 'itqan'" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6" x-cloak>
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
            <input type="text" name="previous_school" x-model="formData.previous_school" placeholder="اسم المسجد، المركز، أو الشيخ السابق" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">عدد الختمات السابقة <span class="text-red-500">*</span></label>
            <input type="text" name="memorization_level" x-model="formData.memorization_level" placeholder="مثال: ختمة واحدة أو أكثر" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">مقدار المراجعة الحالي (الورد اليومي) <span class="text-red-500">*</span></label>
        <input type="text" name="academic_notes" x-model="formData.academic_notes" placeholder="مثال: جزء يوميًّا، حزب، نصف جزء..." class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">تقييم مستوى الحفظ من المتقدم (1-10) <span class="text-red-500">*</span></label>
        <div class="flex items-center gap-1 bg-gray-50 p-4 rounded-2xl border border-gray-100 dir-ltr justify-end">
            <template x-for="i in 10">
                <button type="button" @click="formData.self_rating = (11 - i)" class="text-2xl transition-colors" :class="(formData.self_rating >= (11 - i)) ? 'text-amber-400' : 'text-gray-300'">★</button>
            </template>
            <input type="hidden" name="self_rating" :value="formData.self_rating">
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">متون التجويد المحفوظة بإتقان <span class="text-red-500">*</span></label>
        <div class="flex flex-wrap gap-4 p-3 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="tajweed_matn" value="لا يوجد" x-model="formData.tajweed_matn"> <span>لا يوجد</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="tajweed_matn" value="التحفة" x-model="formData.tajweed_matn"> <span>تحفة الأطفال</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="tajweed_matn" value="الجزرية" x-model="formData.tajweed_matn"> <span>المقدمة الجزرية</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="tajweed_matn" value="أخرى" x-model="formData.tajweed_matn"> <span>أخرى</span></label>
        </div>
        <input type="text" name="tajweed_matn_other" x-model="formData.tajweed_matn_other" x-show="formData.tajweed_matn === 'أخرى'" placeholder="يرجى كتابة اسم المتن..." class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">المسار المرغوب فيه <span class="text-red-500">*</span></label>
            <div class="flex flex-col gap-2 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="desired_path" value="تثبيت الحفظ" x-model="formData.desired_path"> <span>تثبيت الحفظ وتجويده</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="desired_path" value="تصحيح التلاوة" x-model="formData.desired_path"> <span>تصحيح التلاوة والنطق</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="desired_path" value="الإجازة والسند" x-model="formData.desired_path"> <span>الإجازة والسند المتصل</span></label>
            </div>
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">الوقت المناسب للمجلس <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="preferred_time" value="صباحًا" x-model="formData.preferred_time"> <span>صباحًا</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="preferred_time" value="ظهرًا" x-model="formData.preferred_time"> <span>ظهرًا</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="preferred_time" value="عصرًا" x-model="formData.preferred_time"> <span>عصرًا</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="preferred_time" value="ليلًا" x-model="formData.preferred_time"> <span>ليلًا</span></label>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer col-span-2"><input type="radio" name="preferred_time" value="أون لاين" x-model="formData.preferred_time"> <span>أون لاين (عبر الإنترنت)</span></label>
            </div>
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">الشيخ / المشرف المفضل للمجلس <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 gap-3 p-3 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="supervisor_name" value="بدون تحديد" x-model="formData.supervisor_name"> <span>بدون تحديد (حسب المتاح)</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="supervisor_name" value="ش عبدالبديع عثمان" x-model="formData.supervisor_name"> <span>ش عبدالبديع عثمان</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="supervisor_name" value="ش سعد الشعراوي" x-model="formData.supervisor_name"> <span>ش سعد الشعراوي</span></label>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 cursor-pointer"><input type="radio" name="supervisor_name" value="ش محمد الطيب" x-model="formData.supervisor_name"> <span>ش محمد الطيب</span></label>
        </div>
    </div>

    <div class="flex justify-between items-center pt-6 border-t border-gray-50 mt-8">
        <button type="button" @click="prevStep()" class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm">
            <span>→ السابق</span>
        </button>
        <button type="button" @click="nextStep()" class="flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl shadow-md hover:shadow-lg transition-all text-sm">
            <span>التالي ←</span>
        </button>
    </div>
</div>

<div x-show="activeTab === 8 && centerEntryLevel === 'ibda'" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6" x-cloak>
    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl text-xl">🏆</div>
        <div>
            <h2 class="text-xl font-black text-indigo-600">مستوى الإبداع</h2>
            <p class="text-xs text-gray-400 mt-1">بيانات الروايات والأسانيد التي حصل عليها الطالب</p>
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">الإجازات والأسانيد التي حصل عليها سابقًا <span class="text-red-500">*</span></label>
        <textarea name="description" x-model="formData.description" placeholder="يرجى ذكر الإجازات، اسم الشيخ المجيز، والمتن المكتوب بالتفصيل..." class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all min-h-[100px]"></textarea>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">المسار والرواية المراد دراستها حاليًا <span class="text-red-500">*</span></label>
        <input type="text" name="desired_path" x-model="formData.desired_path" placeholder="مثال: رواية ورش عن نافع، القراءات العشر..." class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-bold text-gray-700">الوقت المناسب للمجلس <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 p-3 bg-gray-50 rounded-2xl border border-gray-100">
            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer"><input type="radio" name="preferred_time" value="صباحًا" x-model="formData.preferred_time"> <span>صباحًا</span></label>
            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer"><input type="radio" name="preferred_time" value="ظهرًا" x-model="formData.preferred_time"> <span>ظهرًا</span></label>
            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer"><input type="radio" name="preferred_time" value="عصرًا" x-model="formData.preferred_time"> <span>عصرًا</span></label>
            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer"><input type="radio" name="preferred_time" value="ليلًا" x-model="formData.preferred_time"> <span>ليلًا</span></label>
            <label class="flex items-center gap-1 text-xs font-bold text-gray-600 cursor-pointer"><input type="radio" name="preferred_time" value="أون لاين" x-model="formData.preferred_time"> <span>عن بُعد</span></label>
        </div>
    </div>

    <div class="flex justify-between items-center pt-6 border-t border-gray-50 mt-8">
        <button type="button" @click="prevStep()" class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm">
            <span>→ السابق</span>
        </button>
        <button type="button" @click="nextStep()" class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-md hover:shadow-lg transition-all text-sm">
            <span>التالي ←</span>
        </button>
    </div>
</div>

<div x-show="activeTab === 9" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6" x-cloak>
    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
        <div class="p-3 bg-emerald-50 text-[#0a5c36] rounded-2xl text-xl">📝</div>
        <div>
            <h2 class="text-xl font-black text-[#0a5c36]">التوصيات والملاحظات</h2>
            <p class="text-xs text-gray-400 mt-1">توصيات المشرف والبيانات الإدارية والمالية للمركز</p>
        </div>
    </div>

    <div class="bg-emerald-50/50 rounded-2xl p-4 text-center border border-emerald-100/60 my-4">
        <div class="font-serif font-bold text-[#0a5c36] text-lg">« وَلَقَدْ يَسَّرْنَا الْقُرْآنَ لِلذِّكْرِ فَهَلْ مِن مُّدَّكِرٍ »</div>
        <div class="text-[10px] text-emerald-700/70 font-bold mt-1">سورة القمر - آية ١٧</div>
    </div>

    <div class="space-y-2">
        <div class="flex justify-between items-center mb-1">
            <label class="block text-sm font-bold text-gray-700">توصيات وملاحظات المشرف الفنية</label>
            <button type="button" @click="startDictation('notes')" class="text-xs font-bold flex items-center gap-1 px-3 py-1 bg-emerald-50 rounded-full text-[#0a5c36] hover:bg-emerald-100 transition-all" :class="isRecording === 'notes' ? 'animate-pulse bg-red-50 text-red-600 border border-red-200' : ''">
                <span x-text="isRecording === 'notes' ? '🔴 جاري الاستماع...' : '🎤 إملاء صوتی'"></span>
            </button>
        </div>
        <textarea name="notes" x-model="formData.notes" placeholder="أدخل أي ملاحظات فنية حول مخارج الحروف، التجويد، أو سلوك الطالب..." class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all min-h-[110px]"></textarea>
    </div>

    <div class="bg-amber-50/20 border-2 border-amber-100 rounded-2xl p-6 space-y-6">
        <div class="flex items-center gap-2">
            <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
            <h3 class="text-sm font-black text-amber-800">البيانات المالية والمتابعة الإدارية</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-600">اسم المشرف مسجل البيانات <span class="text-red-500">*</span></label>
                <select name="supervisor_id" x-model="formData.supervisor_id" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none appearance-none" required>
                    <option value="">-- اختر اسم المشرف --</option>
                    <option value="محمد الطيب">محمد الطيب</option>
                    <option value="محمد الشحات">محمد الشحات</option>
                    <option value="سعد الشعراوي">سعد الشعراوي</option>
                    <option value="عبدالبديع عثمان">عبدالبديع عثمان</option>
                    <option value="أخرى">أخرى</option>
                </select>
                <input type="text" name="supervisor_other" x-model="formData.supervisor_other" x-show="formData.supervisor_id === 'أخرى'" placeholder="اكتب اسم المشرف..." class="w-full mt-2 p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none focus:border-[#0a5c36] focus:ring-1 focus:ring-[#0a5c36] transition-all">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-600">تاريخ الالتحاق الفعلي</label>
                <input type="date" name="join_date" x-model="formData.join_date" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-600">قرار الإدارة المبدئي (G)</label>
                <select name="status" x-model="formData.status" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium appearance-none">
                    <option value="pending">قيد الانتظار والمراجعة</option>
                    <option value="active">مقبول منتظم</option>
                    <option value="rejected">مرفوض</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-600">رسوم الاشتراك المقررة (I)</label>
                <input type="text" name="subscription_fees" x-model="formData.subscription_fees" placeholder="مثال: 150" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-600">المستلم المالي (J)</label>
                <select name="receiver_name" x-model="formData.receiver_name" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium appearance-none">
                    <option value="">-- اختر مستلم الرسوم --</option>
                    <option value="محمد الطيب">محمد الطيب</option>
                    <option value="محمد الشحات">محمد الشحات</option>
                    <option value="سعد الشعراوي">سعد الشعراوي</option>
                    <option value="عبد البديع عثمان">عبد البديع عثمان</option>
                </select>
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-600">الأدوات والكتب المستلمة (K)</label>
                <select name="received_tools" x-model="formData.received_tools" class="w-full p-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium appearance-none">
                    <option value="">-- اختر نوع العهدة --</option>
                    <option value="المصحف فقط">المصحف فقط</option>
                    <option value="المتابعة فقط">دفتر المتابعة فقط</option>
                    <option value="المصحف والمتابعة">المصحف ودفتر المتابعة معًا</option>
                </select>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center pt-6 border-t border-gray-50 mt-8">
        <button type="button" @click="prevStep()" class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all text-sm">
            <span>→ السابق</span>
        </button>
        <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-[#0a5c36] hover:bg-[#074427] text-emerald-50 font-black rounded-2xl shadow-md hover:shadow-lg transition-all text-sm">
            <span>حفظ البيانات وإرسال النموذج ✓</span>
        </button>
    </div>
</div>