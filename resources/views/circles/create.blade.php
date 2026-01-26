<x-layouts.markaz-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">إضافة حلقة جديدة</h1>
            <p class="text-gray-600">قم بتعبئة البيانات أدناه لإنشاء حلقة تعليمية جديدة في المركز.</p>
        </div>

        <form action="{{ route('circles.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Circle Name -->
                <div class="col-span-2 md:col-span-1">
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">اسم الحلقة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0a5c36] focus:border-transparent outline-none transition"
                        placeholder="أدخل اسم الحلقة">
                    @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Max Students -->
                <div class="col-span-2 md:col-span-1">
                    <label for="max_students" class="block text-sm font-bold text-gray-700 mb-2">أقصى عدد للطلاب</label>
                    <input type="number" name="max_students" id="max_students" value="{{ old('max_students', 20) }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0a5c36] focus:border-transparent outline-none transition"
                        placeholder="مثلاً: 20">
                    @error('max_students') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Type -->
                <div class="col-span-2 md:col-span-1">
                    <label for="type" class="block text-sm font-bold text-gray-700 mb-2">نوع الحلقة <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0a5c36] focus:border-transparent outline-none transition appearance-none bg-no-repeat bg-[length:1em] bg-[left_1rem_center]"
                        style="background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 24 24%22 stroke=%22currentColor%22><path stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19 9l-7 7-7-7%22/></svg>')">
                        <option value="Group" {{ old('type') == 'Group' ? 'selected' : '' }}>جماعية</option>
                        <option value="Individual" {{ old('type') == 'Individual' ? 'selected' : '' }}>فردية</option>
                    </select>
                    @error('type') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Level -->
                <div class="col-span-2 md:col-span-1">
                    <label for="level" class="block text-sm font-bold text-gray-700 mb-2">مستوى الحلقة <span class="text-red-500">*</span></label>
                    <select name="level" id="level" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0a5c36] focus:border-transparent outline-none transition appearance-none bg-no-repeat bg-[length:1em] bg-[left_1rem_center]"
                        style="background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 24 24%22 stroke=%22currentColor%22><path stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19 9l-7 7-7-7%22/></svg>')">
                        <option value="Foundation" {{ old('level') == 'Foundation' ? 'selected' : '' }}>بناء</option>
                        <option value="Advanced" {{ old('level') == 'Advanced' ? 'selected' : '' }}>إتقان</option>
                        <option value="Creative" {{ old('level') == 'Creative' ? 'selected' : '' }}>إبداع</option>
                    </select>
                    @error('level') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Notes -->
                <div class="col-span-2">
                    <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">ملاحظات إضافية</label>
                    <textarea name="notes" id="notes" rows="4"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0a5c36] focus:border-transparent outline-none transition resize-none"
                        placeholder="أدخل أي ملاحظات تتعلق بالحلقة">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Is Active -->
                <div class="col-span-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:-translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="mr-3 text-sm font-bold text-gray-700">تنشيط الحلقة</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">إذا تم إلغاء التنشيط، لن تظهر الحلقة في قوائم الاختيار الحالية.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-6">
                <a href="{{ route('circles.index') }}" class="px-6 py-2 text-gray-600 hover:text-gray-800 transition font-medium">إلغاء</a>
                <button type="submit" class="px-8 py-2 bg-[#0a5c36] hover:bg-[#084a2c] text-white font-bold rounded-lg shadow-md transition-all duration-200">
                    حفظ الحلقة
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>
