<x-layouts.markaz-layout>
    <x-slot name="title">إدخال نتيجة يدوي — {{ $participant->participant_name }}</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('competitions.overview.index') }}" class="hover:text-[#0a5c36]">المسابقات</a>
        <span>/</span>
        <a href="{{ route('competitions.level-participants.index', $participant->competition_level_id) }}" class="hover:text-[#0a5c36]">المشاركون</a>
        <span>/</span>
        <span>{{ $participant->participant_name }}</span>
    </nav>

    <h1 class="text-xl font-black text-gray-800 mb-4">إدخال نتيجة يدوي — {{ $participant->participant_name }}</h1>

    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div class="text-sm text-amber-800 leading-relaxed">
            <p class="font-bold mb-1">تنبيه مهم قبل الحفظ</p>
            <p>
                هذه الدرجة ستُسجَّل كنتيجة نهائية مباشرة بدون المرور على أسئلة الاختبار.
                إذا قام أي مختبر أو أدمن لاحقًا بفتح شاشة الاختبار لهذا المشارك وتقييم أي سؤال
                ثم ضغط "اعتماد النتيجة"، فسيتم <strong>استبدال هذه القيمة تلقائيًا</strong>
                بمجموع درجات الأسئلة التي تم تقييمها، دون أي تنبيه إضافي في تلك اللحظة.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-md">
        <form method="POST" action="{{ route('competitions.participants.manual-result.store', $participant) }}" class="space-y-5">
            @csrf

            <div>
                <label for="total_score" class="block text-sm font-bold text-gray-700 mb-1">الدرجة النهائية</label>
                <input type="number" step="0.01" min="0" id="total_score" name="total_score"
                    value="{{ old('total_score', $participant->competitionResult?->total_score) }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all"
                    required>
                @error('total_score') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('competitions.level-participants.index', $participant->competition_level_id) }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition-all">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-xl text-sm transition-all">
                    حفظ النتيجة
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>