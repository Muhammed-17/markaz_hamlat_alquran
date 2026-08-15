<x-layouts.markaz-layout>
    <x-slot name="title">تعديل درجة السؤال</x-slot>

    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('admin.competition-results.index') }}" class="hover:text-[#0a5c36]">النتائج</a>
        <span>/</span>
        <span>تعديل السؤال</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="{ answered: {{ old('answered', $answer->answered) ? 'true' : 'false' }} }">
        <div class="mb-6 pb-6 border-b border-gray-50">
            <h2 class="text-lg font-bold text-gray-800 mb-1">{{ $answer->competitionQuestion?->name }}</h2>
            <p class="text-xs text-gray-400">نوع السؤال: {{ $answer->competitionQuestion?->type }}</p>
        </div>

        @if($answer->competitionQuestion?->content)
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-700 leading-relaxed">{{ $answer->competitionQuestion->content }}</p>
            </div>
        @endif

        @if($answer->competitionQuestion?->notes)
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-6">
                <p class="text-xs font-bold text-amber-700 mb-1">ملاحظات السؤال</p>
                <p class="text-sm text-gray-700">{{ $answer->competitionQuestion->notes }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.competition-answers.update', $answer) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">هل أجاب؟</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="answered" value="1" x-model="answered" :value="true"
                            {{ old('answered', $answer->answered) ? 'checked' : '' }}
                            class="w-4 h-4 text-[#0a5c36] focus:ring-emerald-200">
                        <span class="text-sm font-medium text-gray-700">نعم</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="answered" value="0" x-model="answered" :value="false"
                            {{ !old('answered', $answer->answered) ? 'checked' : '' }}
                            class="w-4 h-4 text-red-500 focus:ring-red-200">
                        <span class="text-sm font-medium text-gray-700">لا</span>
                    </label>
                </div>
            </div>

            <div x-show="answered" x-cloak class="space-y-5">
                <div>
                    <label for="score" class="block text-sm font-bold text-gray-700 mb-1">الدرجة</label>
                    <input type="number" step="0.5" min="0" max="100" id="score" name="score"
                        value="{{ old('score', $answer->score) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
                    @error('score') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="memorization_mistakes" class="block text-sm font-bold text-gray-700 mb-1">أخطاء الحفظ</label>
                    <textarea id="memorization_mistakes" name="memorization_mistakes" rows="2"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">{{ old('memorization_mistakes', $answer->memorization_mistakes) }}</textarea>
                </div>

                <div>
                    <label for="tashkeel_mistakes" class="block text-sm font-bold text-gray-700 mb-1">أخطاء التشكيل</label>
                    <textarea id="tashkeel_mistakes" name="tashkeel_mistakes" rows="2"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">{{ old('tashkeel_mistakes', $answer->tashkeel_mistakes) }}</textarea>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-bold text-gray-700 mb-1">ملاحظات</label>
                <textarea id="notes" name="notes" rows="2"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">{{ old('notes', $answer->notes) }}</textarea>
            </div>

            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs font-bold text-gray-400 mb-1">تم التقييم بواسطة</p>
                <p class="text-sm font-medium text-gray-800">
                    @if($answer->competition_examiner_id)
                        {{ $answer->competitionExaminer?->user?->name }} (مختبر)
                    @elseif($answer->user_id)
                        {{ $answer->user?->name }} (إشراف/إدارة)
                    @else
                        —
                    @endif
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
                <a href="{{ route('admin.competition-results.show', $answer->competition_participant_id) }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition-all">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-xl text-sm transition-all">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>
