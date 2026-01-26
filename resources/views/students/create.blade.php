<x-layouts.markaz-layout>
    <div class="max-w-4xl mx-auto py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-[#0a5c36]">إضافة طالب جديد</h1>
                <p class="text-gray-500 mt-2">تسجيل طالب جديد في حلقات مركز حملة القرآن</p>
            </div>
            <a href="{{ route('students.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-[#0a5c36] transition-colors font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>

        <form action="{{ route('students.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Personal Information -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-8 border-b border-gray-50 pb-4">
                    <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">البيانات الشخصية</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">اسم الطالب الرباعي</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                               class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none" 
                               placeholder="أدخل اسم الطالب الرباعي">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Guardian -->
                    <div>
                        <label for="guardian_id" class="block text-sm font-bold text-gray-700 mb-2">ولي الأمر</label>
                        <select name="guardian_id" id="guardian_id" required 
                                class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="">اختر ولي الأمر...</option>
                            @foreach($guardians as $guardian)
                                <option value="{{ $guardian->id }}" {{ old('guardian_id') == $guardian->id ? 'selected' : '' }}>
                                    {{ $guardian->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('guardian_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Age -->
                    <div>
                        <label for="age" class="block text-sm font-bold text-gray-700 mb-2">العمر</label>
                        <input type="number" name="age" id="age" value="{{ old('age') }}" 
                               class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none" 
                               placeholder="10">
                        @error('age') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-bold text-gray-700 mb-2">الجنس</label>
                        <select name="gender" id="gender" required 
                                class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>ذكر</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>أنثى</option>
                        </select>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Education Level -->
                    <div>
                        <label for="education_level" class="block text-sm font-bold text-gray-700 mb-2">المستوى الدراسي</label>
                        <select name="education_level" id="education_level" required 
                                class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="Primary" {{ old('education_level') == 'Primary' ? 'selected' : '' }}>ابتدائي</option>
                            <option value="Secondary" {{ old('education_level') == 'Secondary' ? 'selected' : '' }}>متوسط</option>
                            <option value="High School" {{ old('education_level') == 'High School' ? 'selected' : '' }}>ثانوي</option>
                            <option value="University" {{ old('education_level') == 'University' ? 'selected' : '' }}>جامعي</option>
                            <option value="Other" {{ old('education_level') == 'Other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('education_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">رقم التواصل</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                               class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none" 
                               placeholder="05xxxxxxx">
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-bold text-gray-700 mb-2">العنوان</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}" 
                               class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none" 
                               placeholder="الحي، الشارع">
                    </div>
                </div>
            </div>

            <!-- Enrollment Information -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center gap-3 mb-8 border-b border-gray-50 pb-4">
                    <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">بيانات الالتحاق والحلقة</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Circle -->
                    <div>
                        <label for="circle_id" class="block text-sm font-bold text-gray-700 mb-2">الحلقة</label>
                        <select name="circle_id" id="circle_id" 
                                class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="">غير محدد</option>
                            @foreach($circles as $circle)
                                <option value="{{ $circle->id }}" {{ old('circle_id') == $circle->id ? 'selected' : '' }}>
                                    {{ $circle->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('circle_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Enrollment Date -->
                    <div>
                        <label for="enrollment_date" class="block text-sm font-bold text-gray-700 mb-2">تاريخ الالتحاق</label>
                        <input type="date" name="enrollment_date" id="enrollment_date" value="{{ old('enrollment_date', date('Y-m-d')) }}" 
                               class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-2">حالة الطالب</label>
                        <select name="status" id="status" required 
                                class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>نشط</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="Away" {{ old('status') == 'Away' ? 'selected' : '' }}>منقطع</option>
                        </select>
                    </div>

                    <!-- Subscription Plan (Optional UI based on User Request) -->
                    <div>
                        <label for="subscription_price_id" class="block text-sm font-bold text-gray-700 mb-2">باقة الاشتراك</label>
                        <select name="subscription_price_id" id="subscription_price_id" 
                                class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl transition-all outline-none">
                            <option value="">اختر الباقة (اختياري)...</option>
                            @foreach($subscriptionPrices as $price)
                                <option value="{{ $price->id }}">
                                    {{ $price->circle_level }} - {{ $price->amount }} ر.س
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-2">يمكن تحديد الباقة لاحقاً من قسم الاشتراكات</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 mt-12 bg-gray-50/50 p-6 rounded-3xl border border-dashed border-gray-200">
                <a href="{{ route('students.index') }}" class="px-8 py-3 rounded-2xl text-gray-500 hover:bg-gray-100 font-bold transition-all">
                    إلغاء
                </a>
                <button type="submit" class="px-12 py-3 bg-[#0a5c36] text-white rounded-2xl font-black shadow-lg shadow-emerald-900/20 hover:scale-[1.02] active:scale-95 transition-all">
                    حفظ بيانات الطالب
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>
