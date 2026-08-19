<x-layouts.markaz-layout>
    <div class="max-w-6xl mx-auto space-y-6">

        <h2 class="text-xl font-bold text-gray-800">تعديل اختبار سورة (جماعي)</h2>
        <form action="{{ route('surah-tests.update', $surahTest) }}" method="POST" id="surah-test-form" class="space-y-6">
            @csrf
            @method('PUT')

            @include('surah_tests.form_group', ['mode' => 'edit'])

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('surah-tests.show', $surahTest) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    إلغاء
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#0a5c36] text-white font-semibold hover:bg-[#0d7a48] transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    حفظ التعديلات
                </button>
            </div>
        </form>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#surah-test-form');
            if (!form) return;

            function serializeForm(formEl) {
                const data = new FormData(formEl);
                const obj = {};
                for (const [key, value] of data.entries()) {
                    if (obj[key] !== undefined) {
                        obj[key] = Array.isArray(obj[key]) ? [...obj[key], value] : [obj[key], value];
                    } else {
                        obj[key] = value;
                    }
                }
                return JSON.stringify(obj);
            }

            const initialSnapshot = serializeForm(form);

            form.addEventListener('submit', function(e) {
                const currentSnapshot = serializeForm(form);
                if (currentSnapshot === initialSnapshot) {
                    e.preventDefault();
                    alert('لم تقم بأي تعديل على البيانات.');
                }
            });
        });
    </script>
</x-layouts.markaz-layout>