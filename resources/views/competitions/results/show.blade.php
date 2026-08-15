<x-layouts.markaz-layout>
    <x-slot name="title">اختبار — {{ $participant->participant_name }}</x-slot>

    <div dir="rtl" class="bg-[#0b3d2c] rounded-3xl p-6 text-white relative overflow-hidden shadow-xl mb-6">
        <div class="relative z-10">
            <h1 class="text-xl font-black mb-3">{{ $participant->participant_name }}</h1>
            <div class="flex items-center justify-between text-xs text-emerald-100/80 mb-2">
                <span>السؤال {{ $questionIndex }} من {{ $totalQuestions }}</span>
                <span>{{ $answeredCount }} / {{ $totalQuestions }} مُجاب</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-2">
                <div class="bg-emerald-400 h-2 rounded-full transition-all" style="width: {{ $totalQuestions > 0 ? ($questionIndex / $totalQuestions) * 100 : 0 }}%"></div>
            </div>
        </div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8"
        x-data="{
            answered: '{{ $existingAnswer ? ($existingAnswer->answered ? '1' : '0') : '1' }}',
        }">
        <div class="mb-6 pb-6 border-b border-gray-50">
            <h2 class="text-lg font-bold text-gray-800 mb-1">{{ $question->name }}</h2>
            <p class="text-xs text-gray-400">نوع السؤال: {{ $question->typeLabel() }}</p>
        </div>

        @if($question->memorizationFromSurah || $question->memorizationToSurah)
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <label class="block text-xs font-bold text-gray-400 mb-1">من السورة</label>
                <p class="text-sm font-medium text-gray-800">
                    {{ $question->memorizationFromSurah?->name_arabic }} — آية {{ $question->memorization_from_ayah }}
                </p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <label class="block text-xs font-bold text-gray-400 mb-1">إلى السورة</label>
                <p class="text-sm font-medium text-gray-800">
                    {{ $question->memorizationToSurah?->name_arabic }} — آية {{ $question->memorization_to_ayah }}
                </p>
            </div>
        </div>
        @endif

        @if($question->content)
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-6">
            <p class="text-sm text-gray-700 leading-relaxed">{{ $question->content }}</p>
        </div>
        @endif

        @if($question->notes)
        <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-6">
            <p class="text-xs font-bold text-amber-700 mb-1">ملاحظات السؤال</p>
            <p class="text-sm text-gray-700">{{ $question->notes }}</p>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.exam.store', $participant) }}" class="space-y-5">
            @csrf
            <input type="hidden" name="competition_question_id" value="{{ $question->id }}">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">هل أجاب؟ <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="answered" value="1" x-model="answered"
                            class="w-4 h-4 text-[#0a5c36] focus:ring-emerald-200">
                        <span class="text-sm font-medium text-gray-700">نعم</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="answered" value="0" x-model="answered"
                            class="w-4 h-4 text-red-500 focus:ring-red-200">
                        <span class="text-sm font-medium text-gray-700">لا</span>
                    </label>
                </div>
                @error('answered') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="answered === '1'" x-cloak class="space-y-5">
                <div>
                    <label for="memorization_mistakes" class="block text-sm font-bold text-gray-700 mb-1">عدد أخطاء الحفظ</label>
                    <input type="number" min="0" id="memorization_mistakes" name="memorization_mistakes"
                        value="{{ old('memorization_mistakes', $existingAnswer?->memorization_mistakes ?? 0) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
                    @error('memorization_mistakes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tashkeel_mistakes" class="block text-sm font-bold text-gray-700 mb-1">عدد أخطاء التشكيل</label>
                    <input type="number" min="0" id="tashkeel_mistakes" name="tashkeel_mistakes"
                        value="{{ old('tashkeel_mistakes', $existingAnswer?->tashkeel_mistakes ?? 0) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">
                    @error('tashkeel_mistakes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-bold text-gray-700 mb-1">ملاحظات</label>
                <textarea id="notes" name="notes" rows="2"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-[#0a5c36] rounded-2xl outline-none transition-all">{{ old('notes', $existingAnswer?->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                @if($previousQuestion)
                <a href="{{ route('admin.exam.show', $participant) }}?question={{ $previousQuestion->id }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition-all">
                    السابق
                </a>
                @else
                <a href="{{ route('admin.participants.index', $participant->competition_level_id) }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition-all">
                    السابق
                </a>
                @endif

                <div class="flex items-center gap-3">
                    <button type="submit" name="action" value="save"
                        class="px-5 py-2.5 bg-white border border-[#0a5c36] text-[#0a5c36] hover:bg-emerald-50 font-bold rounded-xl text-sm transition-all">
                        حفظ
                    </button>
                    <button type="submit" name="action" value="{{ $isLast ? 'finish' : 'next' }}"
                        class="px-6 py-2.5 bg-[#0a5c36] hover:bg-[#0d7a48] text-white font-bold rounded-xl text-sm transition-all">
                        {{ $isLast ? 'إنهاء الاختبار' : 'التالي' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>