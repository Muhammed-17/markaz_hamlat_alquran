<x-layouts.markaz-layout>

    <div class="max-w-6xl mx-auto py-6 px-4">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    ملفات التفسير
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    إدارة ملفات التفسير المستخدمة في المسابقات
                </p>
            </div>

            <a href="{{ route('tafsir-files.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d] text-white rounded-xl font-bold transition-all shadow-sm">

                <span class="text-lg">+</span>
                إضافة ملف تفسير
            </a>

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
                                        onsubmit="return confirm('هل أنت متأكد من حذف ملف التفسير؟');">

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