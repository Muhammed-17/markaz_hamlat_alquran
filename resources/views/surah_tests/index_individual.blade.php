<x-layouts.markaz-layout>
    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">اختبارات السور — فردي</h2>
            <div class="flex gap-2">
                <a href="{{ route('surah-tests.create', ['type' => 'individual']) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#0a5c36] text-white text-sm font-semibold hover:bg-[#0d7a48] transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    اختبار فردي جديد
                </a>
                <a href="{{ route('surah-tests.index.group') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-[#0a5c36] text-[#0a5c36] text-sm font-semibold hover:bg-[#0a5c36]/5 transition-colors">
                    عرض الاختبارات الجماعية
                </a>
                <a href="{{ route('surah-tests.repeat-students') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    طلاب الإعادة
                </a>
            </div>
        </div>

        @include('surah_tests.index_table', [
        'testType' => 'individual',
        'filterAction' => route('surah-tests.index.individual'),
        ])
    </div>
</x-layouts.markaz-layout>