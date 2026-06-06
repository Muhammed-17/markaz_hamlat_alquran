<x-layouts.markaz-layout>
    <!-- {{ dd($notifications) }} -->
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[#0a5c36]">الإشعارات</h1>
                <p class="text-gray-500 text-sm">جميع الإشعارات المرسلة إليك</p>
            </div>
            @if ($unreadCount > 0)
                <form action="{{ route('notifications.readAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 transition">
                        تحديد الكل كمقروء ({{ $unreadCount }})
                    </button>
                </form>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                @endphp
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 {{ $isUnread ? 'border-r-4 border-r-red-500 bg-red-50/50' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                @if ($isUnread)
                                    <span class="w-2 h-2 bg-red-500 rounded-full inline-block"></span>
                                @endif
                                <h4 class="font-bold text-gray-800 {{ $isUnread ? 'text-red-800' : '' }}">
                                    {{ $data['student_name'] ?? 'طالب' }}
                                </h4>
                                @if (!empty($data['circle_name']))
                                    <span class="text-xs text-gray-400">- {{ $data['circle_name'] }}</span>
                                @endif
                            </div>

                            <p class="text-gray-600 text-sm leading-relaxed">
                                {{ $data['message_ar'] ?? $data['message_en'] ?? '' }}
                            </p>

                            <p class="text-xs text-gray-400 mt-2">
                                {{ $notification->created_at->locale('ar')->isoFormat('dddd، D MMMM YYYY h:mm A') }}
                            </p>
                        </div>

                        @if ($isUnread)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 transition whitespace-nowrap">
                                    تحديد كمقروء
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="text-gray-400 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <p class="text-gray-500">لا توجد إشعارات حالياً</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </div>
</x-layouts.markaz-layout>
