<x-layouts.markaz-layout>
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-[#0a5c36] text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </span>
                    إنشاء متابعة أسبوعية فردية
                </h2>
                <p class="text-gray-500 mt-1 text-sm">أدخل بيانات خطة الطالب الأسبوعية</p>
            </div>
            <a href="{{ route('student-weekly-followups.index-individual') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                قائمة المتابعات
            </a>
        </div>

        @include('student_weekly_followups.form_individual', [
            'mode' => 'create',
            'studentWeeklyFollowup' => null,
        ])
    </div>

    @push('scripts')
    <script>
        // Prevent accidental navigation with unsaved changes
        let formChanged = false;
        const form = document.getElementById('followup-form');

        form?.addEventListener('change', () => formChanged = true);
        form?.addEventListener('input', () => formChanged = true);

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        form?.addEventListener('submit', () => formChanged = false);
    </script>
    @endpush
</x-layouts.markaz-layout>