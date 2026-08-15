<x-layouts.markaz-layout>
    <x-slot name="title">ملاحظة جديدة</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center"><i class="fas fa-plus-circle text-amber-500"></i></div>
                    إضافة ملاحظة سلوكية
                </h2>
            </div>
            <form action="{{ route('behavioral-notes.store') }}" method="POST" class="p-6">
                @csrf
                @include('behavioral_notes.form')
            </form>
        </div>
    </div>
</x-layouts.markaz-layout>