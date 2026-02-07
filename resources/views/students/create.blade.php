<x-layouts.markaz-layout>
    <div class="max-w-4xl mx-auto py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-[#0a5c36]">إضافة طالب جديد</h1>
                <p class="text-gray-500 mt-2">تسجيل طالب جديد في حلقات مركز حملة القرآن</p>
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

        <form action="{{ route('students.store') }}" method="POST" novalidate class="space-y-8">
            @csrf

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
                        <x-custom-input name="name" label="اسم الطالب الرباعي" type="text"
                            placeholder="أدخل اسم الطالب الرباعي" />
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <x-custom-textarea name="description" label="وصف أو نبذة عن الطالب"
                            placeholder="أدخل نبذة عن الطالب" />
                    </div>

                    <!-- Age -->
                    <x-custom-input name="age" label="العمر" type="number" placeholder="أدخل عمر الطالب" />

                    <!-- Date of Birth -->
                    <x-custom-input name="date_of_birth" label="تاريخ الميلاد" type="date"
                        placeholder="أدخل تاريخ الميلاد" />

                    <!-- Gender -->
                    <x-custom-select name="gender" label="الجنس">
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    </x-custom-select>

                    <!-- Education Level -->
                    <x-custom-select name="education_level" label="المرحلة الدراسي">
                        <option value="preschool" {{ old('education_level') == 'preschool' ? 'selected' : '' }}>حضانة</option>
                        <option value="primary" {{ old('education_level') == 'primary' ? 'selected' : '' }}>ابتدائية</option>
                        <option value="secondary" {{ old('education_level') == 'secondary' ? 'selected' : '' }}>إعدادية</option>
                        <option value="high_school" {{ old('education_level') == 'high_school' ? 'selected' : '' }}>ثانوية</option>
                        <option value="university" {{ old('education_level') == 'university' ? 'selected' : '' }}>جامعية</option>
                        <option value="other" {{ old('education_level') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </x-custom-select>

                    <!-- Phone -->
                    <x-custom-input name="phone" label="رقم التواصل الأساسي" type="text"
                        placeholder="01xxxxxxxxx" />

                    <!-- Second Phone -->
                    <x-custom-input name="second_phone" label="رقم تواصل إضافي" type="text"
                        placeholder="01xxxxxxxxx" />

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <x-custom-textarea name="address" label="العنوان" placeholder="الحي، الشارع" />
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
                    <h2 class="text-xl font-bold text-gray-800">2. بيانات الالتحاق والحلقة</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Circle -->
                    <div>
                        <x-custom-select name="circle_id" label="الحلقة المستهدفة">
                            <option value="">غير محدد (سيتم التحديد لاحقاً)</option>
                            @foreach ($circles as $circle)
                                <option value="{{ $circle->id }}"
                                    {{ old('circle_id') == $circle->id ? 'selected' : '' }}>
                                    {{ $circle->name }} ({{ $circle->level }})
                                </option>
                            @endforeach
                        </x-custom-select>
                    </div>

                    <!-- Status -->
                    <x-custom-select name="status" label="حالة الطالب الحالية">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>مقيد</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>متوقف</option>
                        <option value="traveler" {{ old('status') == 'traveler' ? 'selected' : '' }}>مسافر</option>
                    </x-custom-select>

                    <!-- Enrollment Date -->
                    <x-custom-input name="enrollment_date" label="تاريخ الالتحاق" type="date" placeholder="" />

                    <!-- Current Surah -->
                    <x-custom-input name="current_surah" label="السورة الحالية" type="text" placeholder="ادخل اسم السورة" />

                    <!-- Subscription Plan -->
                    {{-- <div>
                        <label for="subscription_price_id" class="block text-sm font-bold text-gray-700 mb-2">باقة الاشتراك</label>
                        <select name="subscription_price_id" id="subscription_price_id" 
                                class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="">اختر الباقة (اختياري)...</option>
                            @foreach ($subscriptionPrices as $price)
                                <option value="{{ $price->id }}" {{ old('subscription_price_id') == $price->id ? 'selected' : '' }}>
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
                    <h2 class="text-xl font-bold text-gray-800">3. بيانات ولي الأمر</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Guardian -->
                    <x-custom-select name="guardian_id" label="ولي الأمر المسجل">
                        <option value="">اختر ولي الأمر...</option>
                        @foreach ($guardians as $guardian)
                            <option value="{{ $guardian->id }}"
                                {{ old('guardian_id') == $guardian->id ? 'selected' : '' }}>
                                {{ $guardian->name }}
                            </option>
                        @endforeach
                    </x-custom-select>
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
