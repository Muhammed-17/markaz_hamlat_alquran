<x-layouts.markaz-layout>

    <div class="max-w-3xl mx-auto py-6 px-4">

        {{-- Header --}}
        <div class="mb-6">

            <a href="{{ route('tafsir-files.index') }}"
               class="inline-flex items-center gap-2 text-sm
                      text-gray-500 hover:text-[#0a5c36] mb-4">
                ← العودة إلى ملفات التفسير
            </a>

            <h1 class="text-2xl font-bold text-gray-800">
                تعديل ملف التفسير
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                تعديل بيانات ملف التفسير.
            </p>

        </div>

        <form action="{{ route('tafsir-files.update', $tafsirFile) }}"
              method="POST">

            @include('tafsir_files.form', [
                'isEdit' => true,
                'tafsirFile' => $tafsirFile,
            ])

        </form>

    </div>

</x-layouts.markaz-layout>