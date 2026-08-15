@php
$competition = $competition ?? null;
@endphp

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
        <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800">بيانات المسابقة</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2 md:col-span-2">
            <label for="name" class="block text-sm font-bold text-gray-700">اسم المسابقة <span class="text-red-500">*</span></label>
            <input id="name" type="text" name="name" autocomplete="off" value="{{ old('name', $competition->name ?? '') }}"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2 md:col-span-2">
            <label for="description" class="block text-sm font-bold text-gray-700">الوصف</label>
            <textarea id="description" name="description" rows="4"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">{{ old('description', $competition->description ?? '') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label for="status" class="block text-sm font-bold text-gray-700">الحالة <span class="text-red-500">*</span></label>
            <select id="status" name="status" autocomplete="off"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all appearance-none">
                <option value="draft" {{ old('status', $competition->status ?? 'draft') == 'draft' ? 'selected' : '' }}>مسودة</option>
                <option value="active" {{ old('status', $competition->status ?? '') == 'active' ? 'selected' : '' }}>نشطة</option>
                <option value="closed" {{ old('status', $competition->status ?? '') == 'closed' ? 'selected' : '' }}>مغلقة</option>
            </select>
            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
