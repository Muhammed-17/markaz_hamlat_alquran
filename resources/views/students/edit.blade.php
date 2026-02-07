<x-layouts.markaz-layout>
    <div class="max-w-4xl mx-auto py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-[#0a5c36]">تعديل طالب</h1>
                <p class="text-gray-500 mt-2">تعديل بيانات طالب في حلقات مركز حملة القرآن</p>
            </div>
            <a href="{{ route('students.index') }}"
                class="flex items-center gap-2 text-gray-500 hover:text-[#0a5c36] transition-colors font-bold">
                <span>العودة للقائمة</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
        </div>

        <form action="{{ route('students.update', $student->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-8 border-b border-gray-50 pb-4">
                    <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">1. البيانات الشخصية</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">اسم الطالب
                            الرباعي</label>
                        <input type="text" name="name" id="name" value="{{ $student->name }}" required
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none"
                            placeholder="أدخل اسم الطالب الرباعي">
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">وصف أو نبذة عن
                            الطالب</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none resize-none"
                            placeholder="أدخل أي معلومات إضافية عن الطالب">{{ $student->description }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Age -->
                    <div>
                        <label for="age" class="block text-sm font-bold text-gray-700 mb-2">العمر</label>
                        <input type="number" name="age" id="age" value="{{ old('age', $student->age) }}"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none"
                            placeholder="مثلاً: 10">
                        @error('age')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-bold text-gray-700 mb-2">تاريخ
                            الميلاد</label>
                        <input type="date" name="date_of_birth" id="date_of_birth"
                            value="{{ old('date_of_birth', $student->date_of_birth) }}"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                        @error('date_of_birth')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-bold text-gray-700 mb-2">الجنس</label>
                        <select name="gender" id="gender" required
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                        </select>
                        @error('gender')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Education Level -->
                    <div>
                        <label for="education_level" class="block text-sm font-bold text-gray-700 mb-2">المستوى
                            الدراسي</label>
                        <select name="education_level" id="education_level" required
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="preschool" {{ old('education_level') == 'preschool' ? 'selected' : '' }}>حضانة</option>
                            <option value="primary" {{ old('education_level') == 'primary' ? 'selected' : '' }}>ابتدائية</option>
                            <option value="secondary" {{ old('education_level') == 'secondary' ? 'selected' : '' }}>إعدادية</option>
                            <option value="high_school" {{ old('education_level') == 'high_school' ? 'selected' : '' }}>ثانوية</option>
                            <option value="university" {{ old('education_level') == 'university' ? 'selected' : '' }}>جامعية</option>
                            <option value="other" {{ old('education_level') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('education_level')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">رقم التواصل
                            الأساسي</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $student->phone) }}"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none"
                            placeholder="01xxxxxxxxx">
                        @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Second Phone -->
                    <div>
                        <label for="second_phone" class="block text-sm font-bold text-gray-700 mb-2">رقم تواصل
                            إضافي</label>
                        <input type="text" name="second_phone" id="second_phone"
                            value="{{ old('second_phone', $student->second_phone) }}"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none"
                            placeholder="01xxxxxxxxx">
                        @error('second_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-bold text-gray-700 mb-2">العنوان</label>
                        <input type="text" name="address" id="address"
                            value="{{ old('address', $student->address) }}"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none"
                            placeholder="الحي، الشارع">
                    </div>
                </div>
            </div>

            <!-- Enrollment Information -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-8 border-b border-gray-50 pb-4">
                    <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">3. بيانات الالتحاق والحلقة</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Circle -->
                    <div>
                        <label for="circle_id" class="block text-sm font-bold text-gray-700 mb-2">الحلقة
                            المستهدفة</label>
                        <select name="circle_id" id="circle_id"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="">غير محدد (سيتم التحديد لاحقاً)</option>
                            @foreach ($circles as $circle)
                            <option value="{{ $circle->id }}"
                                {{ old('circle_id', $circle->id) == $circle->id ? 'selected' : '' }}>
                                {{ $circle->name }} ({{ $circle->level }})
                            </option>
                            @endforeach
                        </select>
                        @error('circle_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Enrollment Date -->
                    <div>
                        <label for="enrollment_date" class="block text-sm font-bold text-gray-700 mb-2">تاريخ
                            الالتحاق</label>
                        <input type="date" name="enrollment_date" id="enrollment_date"
                            value="{{ old('enrollment_date', $student->enrollment_date) }}"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-2">حالة الطالب
                            الحالية</label>
                        <select name="status" id="status" required
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>مقيد</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>متوقف</option>
                            <option value="traveler" {{ old('status') == 'traveler' ? 'selected' : '' }}>مسافر</option>
                        </select>
                        @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Surah -->
                    <div>
                        <label for="current_surah" class="block text-sm font-bold text-gray-700 mb-2">السورة
                            الحالية</label>
                        <input type="text" name="current_surah" id="current_surah"
                            value="{{ old('current_surah', $student->current_surah) }}"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none"
                            placeholder="مثلاً: سورة البقرة">
                        @error('current_surah')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subscription Plan -->
                    {{-- <div>
                        <label for="subscription_price_id" class="block text-sm font-bold text-gray-700 mb-2">باقة الاشتراك</label>
                        <select name="subscription_price_id" id="subscription_price_id" 
                                class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="">اختر الباقة (اختياري)...</option>
                            @foreach ($subscriptionPrices as $price)
                                <option value="{{ $price->id }}" {{ old('subscription_price_id',$student->subscription_price_id) == $price->id ? 'selected' : '' }}>
                    {{ $price->circle_level }} - {{ $price->amount }} ر.س
                    </option>
                    @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-2">تلقائياً حسب مستوى الحلقة المختارة</p>
                </div> --}}
            </div>
    </div>

    <!-- Parent Information -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center gap-3 mb-8 border-b border-gray-50 pb-4">
            <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">2. بيانات ولي الأمر</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <!-- Guardian -->
            <div class="md:col-span-2">
                <label for="guardian_id" class="block text-sm font-bold text-gray-700 mb-2">ولي الأمر
                    المسجل</label>
                <select name="guardian_id" id="guardian_id"
                    class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                    <option value="">اختر ولي الأمر...</option>
                    @foreach ($guardians as $guardian)
                    <option value="{{ $guardian->id }}"
                        {{ old('guardian_id', $guardian->id) == $guardian->id ? 'selected' : '' }}>
                        {{ $guardian->name }}
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-2 italic">يجب أن يكون ولي الأمر مسجلاً مسبقاً في النظام للربط.</p>
                @error('guardian_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div
        class="flex items-center justify-end gap-4 mt-12 bg-gray-50/50 p-6 rounded-3xl border border-dashed border-gray-200">
        <a href="{{ route('students.index') }}"
            class="px-8 py-3 rounded-2xl text-gray-500 hover:bg-gray-100 font-bold transition-all">
            إلغاء
        </a>
        <button type="submit"
            class="px-12 py-3 bg-[#0a5c36] text-white rounded-2xl font-black shadow-lg shadow-emerald-900/20 hover:scale-[1.02] active:scale-95 transition-all">
            حفظ بيانات الطالب
        </button>
    </div>
    </form>
    </div>
</x-layouts.markaz-layout>