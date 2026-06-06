<x-layouts.markaz-layout>
    <div class="max-w-5xl mx-auto space-y-8" x-data="terminalApp()">

        <!-- Page Header -->
        <div>
            <h1 class="text-2xl font-bold text-[#0a5c36]">إعدادات النظام</h1>
            <p class="text-gray-500 text-sm">التحكم بمواعيد الحلقات وجدولة وإشعارات الغياب المتتالي</p>
        </div>

        @php
            $start = $settings['tracking_start'] ?? env('TRACKING_START_TIME', '14:00');
            $end   = $settings['tracking_end'] ?? env('TRACKING_END_TIME', '17:00');
            $notifyTime = $settings['notify_time'] ?? '17:30';
        @endphp

        {{-- ============================================================ --}}
        {{-- SECTION 1 + 2: Schedule Form                                --}}
        {{-- ============================================================ --}}
        <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Section 1: Session Hours --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800">مواعيد الحلقة</h2>
                        <p class="text-xs text-gray-500">تحديد وقت بدء وانتهاء الحصة اليومية</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">وقت البداية</label>
                        <select name="tracking_start"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white px-4 py-2.5 text-sm">
                            <option value="14:00" @selected($start === '14:00')>2:00 PM</option>
                            <option value="15:00" @selected($start === '15:00')>3:00 PM</option>
                            <option value="16:00" @selected($start === '16:00')>4:00 PM</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">وقت النهاية</label>
                        <select name="tracking_end"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white px-4 py-2.5 text-sm">
                            <option value="17:00" @selected($end === '17:00')>5:00 PM</option>
                            <option value="18:00" @selected($end === '18:00')>6:00 PM</option>
                            <option value="19:00" @selected($end === '19:00')>7:00 PM</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section 2: Notification Schedule --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800">جدولة الإشعارات التلقائية</h2>
                        <p class="text-xs text-gray-500">الوقت الذي يتم فيه إرسال إشعارات الغياب المتتالي يومياً</p>
                    </div>
                </div>
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-gray-700 mb-1">وقت الإرسال اليومي</label>
                    <input type="time" name="notify_time" value="{{ $notifyTime }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5 text-sm">
                    <p class="text-xs text-gray-400 mt-1">يُفضل أن يكون بعد وقت نهاية الحلقة بـ ٣٠ دقيقة</p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium text-sm shadow-sm">
                    حفظ الإعدادات
                </button>
            </div>
        </form>

        {{-- ============================================================ --}}
        {{-- SECTION 3: Interactive Terminal / Diagnostic Console         --}}
        {{-- ============================================================ --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800">⚡ الطرفية — سجل التشخيص</h2>
                    <p class="text-xs text-gray-500">نتائج فحص الغياب المتتالي في الوقت الفعلي</p>
                </div>
            </div>

            {{-- Terminal Window --}}
            <div class="bg-slate-900 rounded-xl shadow-inner overflow-hidden">
                {{-- Terminal Header --}}
                <div class="flex items-center gap-2 px-4 py-2 bg-slate-800 border-b border-slate-700">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-xs text-slate-400 mr-2 font-mono">markaz@notifier:~$</span>
                </div>

                {{-- Terminal Body --}}
                <div class="p-4 font-mono text-xs leading-relaxed whitespace-pre-wrap min-h-[280px] max-h-[400px] overflow-y-auto"
                     x-ref="terminal"
                     :class="{ 'opacity-50 pointer-events-none': loading }">
                    <template x-if="loading">
                        <div class="text-emerald-400/70 animate-pulse">
                            <span class="inline-block w-2 h-4 bg-emerald-400 ml-1 animate-pulse"></span>
                            جاري التشغيل...
                        </div>
                    </template>
                    <template x-if="!loading">
                        <div class="text-emerald-400" x-html="output"></div>
                    </template>
                </div>
            </div>

            {{-- Status Bar --}}
            <div class="flex items-center justify-between mt-3 text-xs text-gray-500 px-1" x-show="statsChecked">
                <span>
                    <span class="font-semibold text-gray-700" x-text="`${checked} طالب`"></span> تم فحصهم
                </span>
                <span>
                    <span class="font-semibold text-emerald-600" x-text="`${matched} تطابق`"></span>
                    —
                    <span class="font-semibold text-red-600" x-text="`${notified} إرسال`"></span>
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-3 mt-5">
                <button type="button" @click="runDryRun"
                    :disabled="loading"
                    class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-medium text-sm shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="loading ? 'جاري التشغيل...' : 'تشغيل التشخيص / معاينة المطابقة'"></span>
                </button>

                <button type="button" @click="confirmForceSend"
                    :disabled="loading"
                    class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-medium text-sm shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    إرسال القائمة الآن (Force Send)
                </button>

                <button type="button" @click="clearTerminal"
                    class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    مسح السجل
                </button>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function terminalApp() {
            return {
                output: `{!! $defaultOutput !!}`,
                loading: false,
                checked: 0,
                matched: 0,
                notified: 0,
                statsChecked: false,

                async runDryRun() {
                    this.loading = true;
                    this.output = '';
                    this.statsChecked = false;

                    try {
                        const res = await fetch('{{ route('admin.settings.dry-run-json') }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        const data = await res.json();
                        this.output = this.ansiToHtml(data.output);
                        this.parseStats(data.output);
                    } catch (e) {
                        this.output = `<span class="text-red-400">[ERROR] فشل الاتصال بالخادم: ${e.message}</span>`;
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => this.scrollDown());
                    }
                },

                async runForceSend() {
                    this.loading = true;
                    this.output = '';
                    this.statsChecked = false;

                    try {
                        const res = await fetch('{{ route('admin.settings.force-send-json') }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        const data = await res.json();
                        this.output = this.ansiToHtml(data.output);
                        this.checked = data.sent + data.skipped;
                        this.matched = data.sent;
                        this.notified = data.sent;
                        this.statsChecked = true;
                    } catch (e) {
                        this.output = `<span class="text-red-400">[ERROR] فشل الاتصال بالخادم: ${e.message}</span>`;
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => this.scrollDown());
                    }
                },

                confirmForceSend() {
                    if (!confirm('هل أنت متأكد من إرسال الإشعارات لجميع الطلاب المتطابقين الآن؟')) return;
                    this.runForceSend();
                },

                clearTerminal() {
                    this.output = '';
                    this.checked = 0;
                    this.matched = 0;
                    this.notified = 0;
                    this.statsChecked = false;
                },

                parseStats(text) {
                    const chk = text.match(/Checking (\d+)/);
                    const snd = text.match(/(?:Would send|Sent): (\d+)/);
                    this.checked = chk ? parseInt(chk[1]) : 0;
                    this.matched = snd ? parseInt(snd[1]) : 0;
                    this.notified = this.matched;
                    this.statsChecked = true;
                },

                ansiToHtml(text) {
                    return text
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/\[DRY-RUN\]/g, '<span class="text-cyan-400 font-bold">[DRY-RUN]</span>')
                        .replace(/\[ERROR\]/g, '<span class="text-red-400 font-bold">[ERROR]</span>')
                        .replace(/^Done\./gm, '<span class="text-green-400 font-bold">Done.</span>')
                        .replace(/^Checking/gm, '<span class="text-yellow-400">Checking</span>')
                        .replace(/Sent:/g, '<span class="text-green-400">Sent:</span>')
                        .replace(/Skipped:/g, '<span class="text-yellow-400">Skipped:</span>');
                },

                scrollDown() {
                    const el = this.$refs.terminal;
                    if (el) el.scrollTop = el.scrollHeight;
                }
            }
        }
    </script>
    @endpush
</x-layouts.markaz-layout>
