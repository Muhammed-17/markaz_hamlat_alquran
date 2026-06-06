<x-layouts.markaz-layout>
    <div class="max-w-3xl mx-auto py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-[#0a5c36]">إضافة معلم جديد</h1>
                <p class="text-gray-500 mt-2">تعيين مستخدم كمعلم أو مشرف في المركز</p>
            </div>
            <a href="{{ route('teachers.index') }}"
                class="flex items-center gap-2 text-gray-500 hover:text-[#0a5c36] transition-colors font-bold">
                <span>العودة للقائمة</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
        </div>

        <form action="{{ route('teachers.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8"
                x-data="{ role: '{{ old('role', 'teacher') }}' }">

                <!-- Title -->
                <div class="flex items-center gap-3 mb-8 border-b border-gray-50 pb-4">
                    <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">البيانات الأساسية</h2>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">اسم المعلم</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none">
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="'البريد الالكتروني'" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                        :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4" x-data="{ show: false }">
                    <x-input-label for="password" :value="'كلمة المرور'" />
                    <div class="relative">
                        <x-text-input id="password" class="block mt-1 w-full pl-10"
                            x-bind:type="show ? 'text' : 'password'"
                            name="password" required />
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 left-0 flex items-center px-3 text-gray-400">
                            👁
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Role Select -->
                <div class="mt-6">
                    <label for="role" class="block text-sm font-bold text-gray-700 mb-2">نوع المستخدم</label>
                    <select name="role" id="role" x-model="role"
                        class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none">
                        <option value="manager">مدير</option>
                        <option value="supervisor">مشرف</option>
                        <option value="teacher">معلم</option>
                    </select>
                    @error('role')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 mt-12">
                <a href="{{ route('teachers.index') }}"
                    class="px-8 py-3 rounded-2xl text-gray-500 hover:bg-gray-100 font-bold">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-12 py-3 bg-[#0a5c36] text-white rounded-2xl font-black">
                    حفظ البيانات
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>