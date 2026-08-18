<x-layouts.markaz-layout>
    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إنشاء اختبار سورة (جماعي)</h2>
            <a href="{{ route('surah-tests.create.individual') }}"
                class="text-sm text-[#0a5c36] hover:underline">تحويل إلى اختبار فردي</a>
        </div>

        <form action="{{ route('surah-tests.store') }}" method="POST" id="surah-test-form" class="space-y-6">
            @csrf
            <input type="hidden" name="test_type" value="group">

            @include('surah_tests.form_group', ['mode' => 'create'])

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
    <!-- JS: جلب طلاب الحلقة عبر Ajax واستنساخ الكارت من <template> بدلاً من تكرار الـ HTML -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <script>
        (function() {
            const circleSelect = document.getElementById('circle-select');
            const listEl = document.getElementById('group-results-list');
            const countEl = document.getElementById('group-students-count');
            const templateEl = document.getElementById('student-card-template');

            function emptyRow(message) {
                return `<div class="bg-white rounded-2xl border border-gray-100 p-10 text-center text-gray-400 text-sm">${message}</div>`;
            }

            function buildCard(student, index) {
                const fragment = templateEl.content.cloneNode(true);
                const root = fragment.firstElementChild;

                const setField = (fieldName, attr, value) => {
                    const el = root.querySelector(`[data-field="${fieldName}"]`);
                    if (!el) return;
                    if (attr === 'text') {
                        el.textContent = value;
                    } else if (attr === 'value') {
                        el.value = value;
                    }
                };

                // اسم الحقل يتحول من data-field إلى name[index][field] بنفس الأسماء الأصلية
                const nameFor = (field) => `results[${index}][${field}]`;

                const studentIdEl = root.querySelector('[data-field="student_id"]');
                studentIdEl.setAttribute('name', nameFor('student_id'));
                studentIdEl.value = student.id;

                setField('name', 'text', student.name);
                setField('id-label', 'text', `رقم الطالب: #${student.id}`);

                ['prompt_errors', 'tashkeel_errors', 'percentage', 'level', 'notes'].forEach((field) => {
                    const el = root.querySelector(`[data-field="${field}"]`);
                    if (el) el.setAttribute('name', nameFor(field));
                });

                return root;
            }

            function fetchStudents(circleId) {
                if (!circleId) {
                    renderGroupList([]);
                    return;
                }

                listEl.innerHTML = emptyRow('جاري التحميل...');

                fetch(`/circles/${circleId}/students`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => renderGroupList(data))
                    .catch(() => {
                        listEl.innerHTML = emptyRow('حدث خطأ أثناء تحميل الطلاب.').replace('text-gray-400', 'text-red-400');
                    });
            }

            function renderGroupList(students) {
                listEl.innerHTML = '';

                if (students.length === 0) {
                    listEl.innerHTML = emptyRow('اختر الحلقة لعرض الطلاب');
                    countEl.textContent = '(0 طالب)';
                    return;
                }

                students.forEach((student, index) => {
                    listEl.appendChild(buildCard(student, index));
                });

                countEl.textContent = `(${students.length} طالب)`;
            }

            circleSelect.addEventListener('change', function() {
                fetchStudents(this.value);
            });

            if (circleSelect.value) {
                fetchStudents(circleSelect.value);
            }
        })();
    </script>
</x-layouts.markaz-layout>