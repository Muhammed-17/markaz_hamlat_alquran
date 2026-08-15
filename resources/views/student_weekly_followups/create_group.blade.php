<x-layouts.markaz-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-[#0a5c36] text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </span>
                    إنشاء متابعة أسبوعية جماعية
                </h2>
                <p class="text-gray-500 mt-1 text-sm">أدخل بيانات الخطة مرة واحدة وسيتم إنشاء سجل لكل طالب في الحلقة</p>
            </div>
            <button type="button" onclick="window.history.back()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 0h18" />
                </svg>
                رجوع
            </button>
        </div>

        @include('student_weekly_followups.form_group', [
        'mode' => 'create',
        'batchId' => null,
        'plan_data' => [],
        'students_data' => [],
        'activities_data' => [],
        'educational_lesson' => $educationalLesson ?? null,
        ])
    </div>

    @push('scripts')
    <script>
        // Auto-load students when circle changes
        document.getElementById('circle-select')?.addEventListener('change', function() {
            const circleId = this.value;
            if (!circleId) return;

            const url = new URL(window.location.href);
            url.searchParams.set('circle_id', circleId);
            window.location.href = url.toString();
        });
    </script>
    @endpush
</x-layouts.markaz-layout>