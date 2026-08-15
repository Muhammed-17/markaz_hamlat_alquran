<x-layouts.markaz-layout>
    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">مراجعة الطلاب المستثناة من التحديث التلقائي</h2>
                <p class="text-sm text-gray-500 mt-1">
                    هؤلاء الطلاب لن يتم تحديث مرحلتهم/صفهم تلقائيًا — يحتاجون مراجعة يدوية دوريًا.
                </p>
            </div>
            <span class="text-sm font-semibold bg-amber-50 text-amber-700 px-3 py-1.5 rounded-full border border-amber-100">
                {{ $excluded->count() }} طالب
            </span>
        </div>

        @if($excluded->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center text-gray-400">
            لا يوجد طلاب مستثناة حاليًا ✓
        </div>
        @else
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 font-bold">الاسم</th>
                        <th class="px-4 py-3 font-bold">الكود</th>
                        <th class="px-4 py-3 font-bold">السن</th>
                        <th class="px-4 py-3 font-bold">المرحلة الحالية</th>
                        <th class="px-4 py-3 font-bold">الصف الحالي</th>
                        <th class="px-4 py-3 font-bold">سبب الاستثناء</th>
                        <th class="px-4 py-3 font-bold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($excluded as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $row->student->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $row->student->student_code }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $row->student->date_of_birth->age }}</td>
                        <td class="px-4 py-3">{{ $row->student->educational_stage ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $row->student->school_grade ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs bg-amber-50 text-amber-700 px-2 py-1 rounded-lg border border-amber-100">
                                {{ $row->reason }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('students.edit', $row->student->id) }}"
                                class="text-[#0a5c36] font-semibold hover:underline">
                                مراجعة →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-layouts.markaz-layout>