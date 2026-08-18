<x-layouts.markaz-layout>

    <div class="max-w-6xl mx-auto py-6 px-4">

        {{-- Header --}}
        <div dir="rtl"
            class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden flex flex-col md:flex-row justify-between items-center shadow-xl gap-6 mb-8">

            <div class="text-right w-full md:w-auto z-10">
                <h1 class="text-3xl font-black mb-2">ملفات التفسير</h1>
                <p class="text-emerald-100/80 text-sm font-medium">
                    @if($tafsirFiles->total())
                    {{ $tafsirFiles->total() }} ملف تفسير مسجل في النظام
                    @else
                    إدارة ملفات التفسير المستخدمة في المسابقات
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto z-10">
                <a href="{{ route('tafsir-files.create') }}"
                    class="w-full md:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة ملف تفسير
                </a>
            </div>

            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-right">

                    <thead class="bg-gray-50 border-b border-gray-100">

                        <tr>

                            <th class="px-5 py-4 text-sm font-bold text-gray-600">
                                #
                            </th>

                            <th class="px-5 py-4 text-sm font-bold text-gray-600">
                                اسم الملف
                            </th>

                            <th class="px-5 py-4 text-sm font-bold text-gray-600">
                                الوصف
                            </th>

                            <th class="px-5 py-4 text-sm font-bold text-gray-600">
                                تاريخ الإضافة
                            </th>

                            <th class="px-5 py-4 text-sm font-bold text-gray-600">
                                الإجراءات
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($tafsirFiles as $tafsirFile)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-5 py-4 text-sm text-gray-500">
                                {{ $tafsirFiles->firstItem() + $loop->index }}
                            </td>

                            <td class="px-5 py-4">

                                <div class="font-bold text-gray-800">
                                    {{ $tafsirFile->name }}
                                </div>

                            </td>

                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $tafsirFile->description ?: '—' }}
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-500">
                                {{ $tafsirFile->created_at?->format('Y-m-d') }}
                            </td>

                            <td class="px-5 py-4">

                                <div class="flex items-center gap-2">

                                    <a href="{{ route('tafsir-files.edit', $tafsirFile) }}"
                                        class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-sm font-bold">
                                        تعديل
                                    </a>

                                    <form action="{{ route('tafsir-files.destroy', $tafsirFile) }}"
                                        method="POST"
                                        onsubmit="confirmDelete(event, { name: '{{ e($tafsirFile->name) }}', type: 'ملف التفسير' })">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100  text-sm font-bold">
                                            حذف
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="5"
                                class="px-5 py-12 text-center text-gray-500">

                                <div class="text-4xl mb-3">
                                    📚
                                </div>

                                <p class="font-bold">
                                    لا توجد ملفات تفسير
                                </p>

                                <p class="text-sm mt-1">
                                    قم بإضافة أول ملف تفسير.
                                </p>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if($tafsirFiles->hasPages())

            <div class="px-5 py-4 border-t border-gray-100">
                {{ $tafsirFiles->links() }}
            </div>

            @endif

        </div>

    </div>

</x-layouts.markaz-layout>