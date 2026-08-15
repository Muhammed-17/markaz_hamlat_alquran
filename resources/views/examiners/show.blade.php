<x-layouts.markaz-layout>
    <x-slot name="title">{{ $examiner->user?->name }}</x-slot>

    <div dir="rtl" class="bg-[#0b3d2c] rounded-3xl p-6 lg:p-8 text-white relative overflow-hidden shadow-xl mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
            <div>
                <h1 class="text-3xl font-black mb-2">{{ $examiner->user?->name ?? '—' }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-emerald-100/80 text-sm">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        {{ $examiner->phone ?: '—' }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ $examiner->competition_examiners_count }} مسابقة
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @can('edit examiners')
                <a href="{{ route('examiners.edit', $examiner) }}"
                    class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    تعديل المختبر
                </a>
                @endcan
                <a href="{{ route('examiners.index') }}"
                    class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    رجوع
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <label class="block text-xs font-bold text-gray-400 mb-1">البريد الإلكتروني</label>
                <p class="text-sm font-medium text-gray-800">{{ $examiner->user?->email ?? '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <label class="block text-xs font-bold text-gray-400 mb-1">رقم الهاتف الإضافي</label>
                <p class="text-sm font-medium text-gray-800">{{ $examiner->secondary_phone ?: '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 md:col-span-2">
                <label class="block text-xs font-bold text-gray-400 mb-1">العنوان</label>
                <p class="text-sm font-medium text-gray-800">{{ $examiner->address ?: '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 md:col-span-2">
                <label class="block text-xs font-bold text-gray-400 mb-1">ملاحظات</label>
                <p class="text-sm font-medium text-gray-800 leading-relaxed">{{ $examiner->notes ?: '—' }}</p>
            </div>
        </div>
    </div>
</x-layouts.markaz-layout>
