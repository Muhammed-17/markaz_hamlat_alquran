<x-layouts.markaz-layout>
    <div class="max-w-4xl mx-auto"
        x-data="{
            unreadCount: {{ $unreadCount }},
            markAsRead(id) {
                const card = document.getElementById('notification-' + id);
                const btn = document.getElementById('mark-btn-' + id);
                if (!card) return;
                btn.disabled = true;
                btn.textContent = 'جاري...';
                fetch('{{ route('guardian.notifications.read', '') }}/' + id, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        card.classList.remove('border-r-red-500', 'bg-amber-50/50');
                        card.classList.add('border-r-transparent');
                        const badge = card.querySelector('.unread-badge');
                        if (badge) badge.remove();
                        const markBtn = card.querySelector('.mark-btn');
                        if (markBtn) markBtn.remove();
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.textContent = 'تحديد كمقروء';
                });
            },
            markAllAsRead() {
                const form = document.getElementById('mark-all-form');
                if (form) form.submit();
            }
        }">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[#0a5c36]">الإشعارات</h1>
                <p class="text-gray-500 text-sm">جميع الإشعارات المرسلة إليك</p>
            </div>
            @if ($unreadCount > 0)
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-400" x-show="unreadCount > 0" x-cloak>
                        غير مقروء: <span class="font-bold text-red-500" x-text="unreadCount"></span>
                    </span>
                    <form id="mark-all-form" action="{{ route('guardian.notifications.readAll') }}" method="POST">
                        @csrf
                        <button type="button" @click="markAllAsRead()"
                            class="px-4 py-2 text-sm font-bold text-white bg-[#0a5c36] hover:bg-[#084b2c] rounded-xl transition">
                            تحديد الكل كمقروء
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Flash Message -->
        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-2 text-sm font-medium">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Notification List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                @endphp
                <div id="notification-{{ $notification->id }}"
                    class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 transition-all duration-300
                        {{ $isUnread ? 'border-r-4 border-r-red-500 bg-amber-50/50' : 'border-r-4 border-r-transparent' }}">

                    <div class="p-5">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="w-10 h-10 rounded-xl {{ $isUnread ? 'bg-red-100 text-red-500' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    @if ($isUnread)
                                        <span class="unread-badge w-2 h-2 bg-red-500 rounded-full inline-block shrink-0"></span>
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

                                <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $notification->created_at->locale('ar')->diffForHumans() }}
                                </p>
                            </div>

                            <!-- Action -->
                            <div class="shrink-0">
                                @if ($isUnread)
                                    <button id="mark-btn-{{ $notification->id }}"
                                        @click="markAsRead('{{ $notification->id }}')"
                                        class="mark-btn px-3 py-1.5 text-xs font-bold text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 rounded-lg transition-all whitespace-nowrap">
                                        تحديد كمقروء
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 text-center py-16 px-8">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-[2rem] bg-emerald-50 flex items-center justify-center">
                        <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">لا توجد إشعارات جديدة حالياً</h3>
                    <p class="text-gray-400 text-sm">سيتم إشعارك عند تسجيل غياب متتالٍ لأحد أبنائك</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layouts.markaz-layout>
