<x-layouts.markaz-layout>
    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إنشاء اختبار سورة (فردي)</h2>
            <a href="{{ route('surah-tests.create', ['type' => 'group']) }}"
                class="text-sm text-[#0a5c36] hover:underline">تحويل إلى اختبار جماعي</a>
        </div>

        <form action="{{ route('surah-tests.store') }}" method="POST" id="surah-test-form" class="space-y-6">
            @csrf
            <input type="hidden" name="test_type" value="individual">

            @include('surah_tests.form_individual', ['mode' => 'create'])

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('surah-tests.index.individual') }}"
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
                    حفظ الاختبار
                </button>
            </div>
        </form>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- JS خاص بالنموذج الفردي: جلب طلاب الحلقة + إظهار بطاقة النتيجة -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <script>
        (function() {
            const individualCard = document.getElementById('individual-result-card');
            const individualHiddenStudentId = document.getElementById('individual-result-student-id');

            function fetchStudents(circleId) {
                if (!circleId) {
                    updateStudentOptions([]);
                    return;
                }

                fetch(`/circles/${circleId}/students`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => updateStudentOptions(data))
                    .catch(() => updateStudentOptions([]));
            }

            function updateStudentOptions(students) {
                const options = students.map(s => ({
                    value: s.id,
                    label: s.name
                }));

                // ✅ إرسال حدث تحديث للـ x-searchable-select بتاع student_id
                // بدون preserveSelection عشان يمسح اختيار الطالب القديم
                // (لأنه غالبًا مش موجود جوه الحلقة الجديدة).
                window.dispatchEvent(new CustomEvent('update-options', {
                    detail: {
                        name: 'student_id',
                        options: options,
                        preserveSelection: false
                    }
                }));

                // ✅ مسح اختيار الطالب صراحة ومسح النتيجة المعروضة
                window.dispatchEvent(new CustomEvent('clear-selection', {
                    detail: {
                        name: 'student_id'
                    }
                }));

                individualHiddenStudentId.value = '';
                individualCard.style.display = 'none';
            }

            // ✅ الاستماع لتغيير أي x-searchable-select في الصفحة، والتصرف
            // حسب اسم الحقل (circle_id أو student_id).
            window.addEventListener('searchable-change', (e) => {
                if (e.detail.name === 'circle_id') {
                    fetchStudents(e.detail.value);
                }

                if (e.detail.name === 'student_id') {
                    individualHiddenStudentId.value = e.detail.value;
                    individualCard.style.display = e.detail.value ? 'block' : 'none';
                }
            });


            document.addEventListener('alpine:initialized', () => {
                const initialCircleId = document.querySelector('input[name="circle_id"]')?.value;
                if (initialCircleId) {
                    fetchStudents(initialCircleId);
                }
            });
        })();
    </script>
</x-layouts.markaz-layout>