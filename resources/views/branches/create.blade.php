<x-layouts.markaz-layout>
    <div dir="rtl" class="max-w-3xl mx-auto">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-black text-gray-800">إضافة مقر جديد</h1>
            <a href="{{ route('branches.index') }}"
                class="text-sm font-bold text-gray-500 hover:text-gray-700 transition">
                → رجوع للفروع
            </a>
        </div>

        <form action="{{ route('branches.store') }}" method="POST" class="space-y-6">
            @csrf

            @include('branches.form')

            <div class="flex justify-end gap-3">
                <a href="{{ route('branches.index') }}"
                    class="px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold rounded-2xl transition-all">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-[#0a5c36] hover:bg-[#08492a] text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-emerald-500/20 active:scale-95">
                    حفظ المقر
                </button>
            </div>
        </form>

    </div>
</x-layouts.markaz-layout>