@csrf

@if($isEdit ?? false)
@method('PUT')
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">

    {{-- اسم الملف --}}
    <div class="space-y-2">

        <label for="name"
            class="block text-sm font-bold text-gray-700">
            اسم ملف التفسير
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $tafsirFile->name ?? '') }}"
            required
            maxlength="255"
            placeholder="مثال: تفسير ابن كثير"
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200
                   focus:bg-white focus:ring-2 focus:ring-emerald-100
                   focus:border-[#0a5c36] rounded-xl outline-none
                   transition-all">

        @error('name')
        <p class="text-sm text-red-600">
            {{ $message }}
        </p>
        @enderror

    </div>


    {{-- الوصف --}}
    <div class="space-y-2">

        <label for="description"
            class="block text-sm font-bold text-gray-700">
            الوصف
        </label>

        <textarea
            id="description"
            name="description"
            rows="5"
            placeholder="وصف مختصر لملف التفسير..."
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200
                   focus:bg-white focus:ring-2 focus:ring-emerald-100
                   focus:border-[#0a5c36] rounded-xl outline-none
                   transition-all resize-none">{{ old('description', $tafsirFile->description ?? '') }}</textarea>

        @error('description')
        <p class="text-sm text-red-600">
            {{ $message }}
        </p>
        @enderror

    </div>

</div>

{{-- Actions --}}
<div class="flex justify-end gap-3 mt-6">

    <a href="{{ route('tafsir-files.index') }}"
        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition">
        إلغاء
    </a>

    <button type="submit"
        class="px-7 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d]text-white rounded-xl font-bold transition shadow-sm">

        {{ ($isEdit ?? false) ? 'حفظ التعديلات' : 'حفظ الملف' }}

    </button>

</div>