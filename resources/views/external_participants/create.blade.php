<x-layouts.markaz-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-[#0a5c36] mb-2">إضافة مشارك خارجي جديد</h1>
            <p class="text-gray-600">قم بتعبئة البيانات أدناه لإضافة مشارك خارجي جديد.</p>
        </div>

        <form action="{{ route('external-participants.store') }}" method="POST">
            @csrf
            @include('external_participants.form')
            <div class="flex justify-end gap-4 border-t pt-6 mt-6">
                <a href="{{ route('external-participants.index') }}"
                    class="px-6 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition-all font-bold">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-8 py-2.5 bg-[#0a5c36] hover:bg-[#084d2d] text-white rounded-xl font-bold transition-all shadow-md">
                    حفظ المشارك
                </button>
            </div>
        </form>
    </div>
</x-layouts.markaz-layout>
