<x-layouts.markaz-layout>
    <x-slot name="title">تسجيل الإجراء المتخذ</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="w-10 h-10 bg-sky-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-sky-500"></i>
                    </div>
                    تسجيل الإجراء المتخذ
                </h2>
                <p class="text-gray-500 text-sm mt-1">مراجعة الملاحظة السلوكية وتسجيل الإجراء الذي تم اتخاذه</p>
            </div>

            <!-- عرض بيانات الحادثة (للقراءة فقط) -->
            <div class="p-6 bg-gray-50 border-b border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="block text-xs text-gray-500 mb-1">الطالب</span>
                        <span class="font-semibold text-gray-900">{{ $behavioralNote->student->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 mb-1">الحلقة</span>
                        <span class="font-semibold text-gray-900">{{ $behavioralNote->circle->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 mb-1">المعلم</span>
                        <span class="font-semibold text-gray-900">{{ $behavioralNote->teacher->user->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 mb-1">تاريخ الحادثة</span>
                        <span class="font-semibold text-gray-900">{{ $behavioralNote->incident_at?->format('Y-m-d H:i') ?? '-' }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-xs text-gray-500 mb-1">آخر حالة مسجّلة من المعلم</span>
                        <span class="font-semibold text-gray-900">{{ $behavioralNote->current_status ?: '-' }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <span class="block text-xs text-gray-500 mb-1">وصف السلوك</span>
                    <p class="text-sm text-gray-800 whitespace-pre-line">{{ $behavioralNote->behavior }}</p>
                </div>
            </div>

            <!-- فورم الإجراء -->
            <form action="{{ route('behavioral-notes.update-action', $behavioralNote) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            الإجراء المتخذ <span class="text-red-500">*</span>
                        </label>
                        <textarea name="action_taken" rows="4"
                            placeholder="اكتب الإجراء الذي تم اتخاذه بخصوص هذه الملاحظة..."
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0a5c36] outline-none @error('action_taken') border-red-300 @enderror"
                            required>{{ old('action_taken', $behavioralNote->action_taken ?? '') }}</textarea>
                        @error('action_taken')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            حالة الإجراء <span class="text-red-500">*</span>
                        </label>
                        <select name="status"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0a5c36] outline-none @error('status') border-red-300 @enderror"
                            required>
                            @php
                            $selectedStatus = old('status', $behavioralNote->status ?? \App\Models\BehavioralNote::STATUS_PENDING);
                            @endphp
                            @foreach(\App\Models\BehavioralNote::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ $selectedStatus == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ المتابعة القادمة </label>
                        <input type="date" name="follow_up_at"
                            value="{{ old('follow_up_at', isset($behavioralNote->follow_up_at) ? $behavioralNote->follow_up_at->format('Y-m-d') : '') }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0a5c36] outline-none @error('follow_up_at') border-red-300 @enderror">
                        @error('follow_up_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex justify-between items-center mt-8 pt-5 border-t border-gray-100">
                    <a href="{{ route('behavioral-notes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-2">
                        <i class="fas fa-arrow-right"></i>رجوع
                    </a>
                    <button type="submit"
                        class="bg-[#0a5c36] hover:bg-[#0d7a48] text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-save"></i>حفظ الإجراء
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.markaz-layout>