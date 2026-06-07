{{-- resources/views/circles/form.blade.php --}}
@php
    $isEdit = isset($circle) && $circle->exists;
    $canManageAll = auth()->user()->hasPermissionTo('view all circles');
@endphp

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
        <div class="p-2 bg-emerald-50 rounded-xl text-[#0a5c36]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800">بيانات الحلقة</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- اسم الحلقة --}}
        @if($canManageAll)
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">اسم الحلقة <span class="text-red-500">*</span></label>
                <input type="text" name="name"
                    value="{{ old('name', $circle->name ?? '') }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- أقصى عدد --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">أقصى عدد للطلاب</label>
                <input type="number" name="max_students"
                    value="{{ old('max_students', $circle->max_students ?? 20) }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
                @error('max_students') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- نوع الحلقة --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">نوع الحلقة <span class="text-red-500">*</span></label>
                <select name="type"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all appearance-none">
                    <option value="group"      {{ old('type', $circle->type ?? '') == 'group'      ? 'selected' : '' }}>جماعية</option>
                    <option value="individual" {{ old('type', $circle->type ?? '') == 'individual' ? 'selected' : '' }}>فردية</option>
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- مستوى الحلقة --}}
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700">مستوى الحلقة <span class="text-red-500">*</span></label>
                <select name="level"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all appearance-none">
                    <option value="build"      {{ old('level', $circle->level ?? '') == 'build'      ? 'selected' : '' }}>بناء</option>
                    <option value="mastery"    {{ old('level', $circle->level ?? '') == 'mastery'    ? 'selected' : '' }}>إتقان</option>
                    <option value="creativity" {{ old('level', $circle->level ?? '') == 'creativity' ? 'selected' : '' }}>إبداع</option>
                </select>
                @error('level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        @else
            {{-- مدير الفرع — حقول مخفية --}}
            @if($isEdit)
                <input type="hidden" name="name"         value="{{ $circle->name }}">
                <input type="hidden" name="type"         value="{{ $circle->type }}">
                <input type="hidden" name="level"        value="{{ $circle->level }}">
                <input type="hidden" name="max_students" value="{{ $circle->max_students }}">
                <div class="md:col-span-2 bg-emerald-50 p-4 rounded-2xl text-emerald-800 font-bold border border-emerald-100">
                    تعديل الحلقة: {{ $circle->name }}
                </div>
            @else
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">اسم الحلقة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">نوع الحلقة <span class="text-red-500">*</span></label>
                    <select name="type"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all appearance-none">
                        <option value="group">جماعية</option>
                        <option value="individual">فردية</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">مستوى الحلقة <span class="text-red-500">*</span></label>
                    <select name="level"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all appearance-none">
                        <option value="build">بناء</option>
                        <option value="mastery">إتقان</option>
                        <option value="creativity">إبداع</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">أقصى عدد للطلاب</label>
                    <input type="number" name="max_students" value="{{ old('max_students', 20) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
                </div>
            @endif
        @endif

        {{-- المعلم الأساسي --}}
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">المعلم الأساسي</label>
            <select name="teacher_id"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all appearance-none">
                <option value="">-- اختر المعلم --</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}"
                        {{ old('teacher_id', $circle->mainTeacher->first()?->id ?? '') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
            @error('teacher_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- المعلم المساعد --}}
        <div class="space-y-2">
            <label class="block text-sm font-bold text-gray-700">المعلم المساعد</label>
            <select name="assistant_teacher_id"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all appearance-none">
                <option value="">-- اختر المعلم المساعد --</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}"
                        {{ old('assistant_teacher_id', $circle->assistantTeacher->first()?->id ?? '') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
            @error('assistant_teacher_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- المشرف --}}
        @if($canManageAll)
            <div class="md:col-span-2 space-y-2">
                <label class="block text-sm font-bold text-gray-700">المشرف</label>
                <select name="supervisor_id"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all appearance-none">
                    <option value="">-- اختر المشرف --</option>
                    @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}"
                            {{ old('supervisor_id', $circle->supervisor?->id ?? '') == $supervisor->id ? 'selected' : '' }}>
                            {{ $supervisor->name }}
                        </option>
                    @endforeach
                </select>
                @error('supervisor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- ملاحظات --}}
            <div class="md:col-span-2 space-y-2">
                <label class="block text-sm font-bold text-gray-700">ملاحظات</label>
                <textarea name="notes" rows="4"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">{{ old('notes', $circle->notes ?? '') }}</textarea>
                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        @else
            <input type="hidden" name="supervisor_id" value="{{ $circle->supervisor?->id ?? '' }}">
            <input type="hidden" name="notes" value="{{ $circle->notes ?? '' }}">
        @endif

    </div>
</div>