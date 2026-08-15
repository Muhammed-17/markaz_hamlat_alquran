<div x-data="{ status: '{{ old('status', $behavioralNote->status ?? 'pending') }}' }" class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <!-- الطالب -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الطالب <span class="text-red-500">*</span></label>
        <div :class="status !== 'pending' ? 'pointer-events-none opacity-60 bg-gray-50 rounded-lg' : ''">
            <x-searchable-select
                name="student_id"
                :options="collect($students ?? [])->map(fn($s) => ['value' => $s->id, 'label' => $s->name])->values()"
                :defaultValue="old('student_id', $behavioralNote->student_id ?? '')"
                placeholder="اختر الطالب"
                searchPlaceholder="ابحث عن طالب..." />
        </div>
        @error('student_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- الحلقة -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الحلقة <span class="text-red-500">*</span></label>
        @if(auth()->user()->hasRole('teacher'))
        <input type="text" value="{{ auth()->user()->teacher?->circle?->name }}" disabled
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-gray-100 text-gray-500 outline-none">
        <input type="hidden" name="circle_id" value="{{ auth()->user()->teacher?->circle_id }}">
        @else
        <div :class="status !== 'pending' ? 'pointer-events-none opacity-60 bg-gray-50 rounded-lg' : ''">
            <x-searchable-select
                name="circle_id"
                :options="collect($circles ?? [])->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->values()"
                :defaultValue="old('circle_id', $behavioralNote->circle_id ?? '')"
                placeholder="اختر الحلقة"
                searchPlaceholder="ابحث عن حلقة..." />
        </div>
        @endif
        @error('circle_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- المعلم -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">المعلم <span class="text-red-500">*</span></label>
        @if(auth()->user()->hasRole('teacher'))
        <input type="text" value="{{ auth()->user()->name }}" disabled
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-gray-100 text-gray-500 outline-none">
        <input type="hidden" name="teacher_id" value="{{ auth()->user()->teacher?->id }}">
        @else
        <div :class="status !== 'pending' ? 'pointer-events-none opacity-60 bg-gray-50 rounded-lg' : ''">
            <x-searchable-select
                name="teacher_id"
                :options="collect($teachers ?? [])->map(fn($t) => ['value' => $t->id, 'label' => $t->user->name ?? $t->name])->values()"
                :defaultValue="old('teacher_id', $behavioralNote->teacher_id ?? '')"
                placeholder="اختر المعلم"
                searchPlaceholder="ابحث عن معلم..." />
        </div>
        @endif
        @error('teacher_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- تاريخ ووقت الحادثة -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ ووقت الحادثة <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="incident_at"
            :readonly="status !== 'pending'"
            :class="status !== 'pending' ? 'bg-gray-50 text-gray-500 pointer-events-none' : ''"
            value="{{ old('incident_at', isset($behavioralNote->incident_at) ? $behavioralNote->incident_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0a5c36] outline-none @error('incident_at') border-red-300 @enderror" required>
        @error('incident_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <!-- وصف السلوك -->
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">وصف السلوك <span class="text-red-500">*</span></label>
        <textarea name="behavior" rows="4"
            :readonly="status !== 'pending'"
            :class="status !== 'pending' ? 'bg-gray-50 text-gray-500' : ''"
            placeholder="صف الحادثة أو الملاحظة السلوكية بالتفصيل..."
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0a5c36] outline-none @error('behavior') border-red-300 @enderror" required>{{ old('behavior', $behavioralNote->behavior ?? '') }}</textarea>
        @error('behavior')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <!-- قسم المتابعة والوضع الحالي (يظهر فقط عندما لا تكون الحالة "قيد الانتظار") -->
    <!-- قسم المتابعة والوضع الحالي (يختفي عند "قيد الانتظار" و "قيد المراجعة/التحقيق") -->
    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-100 pt-4 mt-2"
        x-show="status !== 'pending' && status !== 'under_review'"
        x-cloak>

        <!-- الوضع الحالي (يأخذ المساحة الأكبر) -->
        <div class="md:col-span-2">
            @if(isset($behavioralNote) && $behavioralNote->action_taken)
            <div class="bg-sky-50 border border-sky-100 rounded-lg p-3.5 mb-3">
                <span class="block text-xs font-semibold text-sky-700 mb-1">الإجراء المتخذ من المشرف</span>
                <p class="text-sm text-sky-900 whitespace-pre-line">{{ $behavioralNote->action_taken }}</p>
            </div>
            @endif

            <label class="block text-sm font-medium text-gray-700 mb-2">الوضع الحالي</label>
            <textarea name="current_status" rows="3" placeholder="اكتب وضع الطالب الحالي بعد الإجراء المتخذ..."
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0a5c36] outline-none transition @error('current_status') border-red-300 @enderror">{{ old('current_status', $behavioralNote->current_status ?? '') }}</textarea>
            @error('current_status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- تاريخ المتابعة -->
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ المتابعة</label>
            <input type="date" name="follow_up_at"
                value="{{ old('follow_up_at', isset($behavioralNote->follow_up_at) ? (is_string($behavioralNote->follow_up_at) ? $behavioralNote->follow_up_at : $behavioralNote->follow_up_at->format('Y-m-d')) : '') }}"
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0a5c36] outline-none transition @error('follow_up_at') border-red-300 @enderror">
            @error('follow_up_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

    </div>

</div>
<div class="flex justify-between items-center mt-8 pt-5 border-t border-gray-100">
    <a href="{{ route('behavioral-notes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-2"><i class="fas fa-arrow-right"></i>رجوع</a>
    <button type="submit" class="bg-[#0a5c36] hover:bg-[#0d7a48] text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"><i class="fas fa-save"></i>{{ isset($behavioralNote) ? 'حفظ التعديلات' : 'إضافة الملاحظة' }}</button>
</div>