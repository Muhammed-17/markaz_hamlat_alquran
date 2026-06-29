@props([
'name',
'placeholder' => 'اختر...',
'searchPlaceholder' => 'بحث...',
'defaultOption' => null,
'defaultValue' => '',
'options' => '[]',
])

@php
$optionsArray = is_string($options) ? json_decode($options, true) ?? [] : $options;
$optionsJson = json_encode($optionsArray);
@endphp

<div x-data="{
    open: false,
    search: '',
    selectedVal: '{{ $defaultValue }}',
    selectedLabel: '{{ $placeholder }}',
    allOptions: {{ $optionsJson }},

    init() {
        this.updateLabel();
    },

    updateLabel() {
        if (this.selectedVal) {
            const found = this.allOptions.find(o => String(o.value) === String(this.selectedVal));
            this.selectedLabel = found ? found.label : '{{ $placeholder }}';
        } else {
            this.selectedLabel = '{{ $placeholder }}';
        }
    },

    get filtered() {
        if (!this.search) return this.allOptions;
        const q = this.search.toLowerCase().trim();
        return this.allOptions.filter(o => o.label.toLowerCase().includes(q));
    },
select(val, label) {
    this.selectedVal = val;
    this.selectedLabel = label || '{{ $placeholder }}';
    this.open = false;
    this.search = '';
    window.dispatchEvent(new CustomEvent('searchable-change', {
        detail: { name: '{{ $name }}', value: val }
    }));
    },

    clear() {
        this.select('', '{{ $placeholder }}');
    },

    updateOptions(newOptions) {
        this.allOptions = newOptions;
        const found = this.allOptions.find(o => String(o.value) === String(this.selectedVal));
        if (!found) {
            this.selectedVal = '';
            this.selectedLabel = '{{ $placeholder }}';
        }
    }
}"
    x-on:click.outside="open = false; search = ''"
    x-on:update-options.window="if ($event.detail.name === '{{ $name }}') updateOptions($event.detail.options)"
    class="relative w-full">

    {{-- Hidden input --}}
    <input type="hidden" name="{{ $name }}" :value="selectedVal">

    {{-- Trigger --}}
    <button type="button" @click="open = !open"
        class="w-full h-11 px-3 rounded-xl border border-gray-200 bg-white text-sm text-right flex items-center justify-between gap-2 hover:border-gray-300 focus:outline-none focus:border-[#0b3d2c] focus:ring-1 focus:ring-[#0b3d2c] transition-all">
        <span x-text="selectedLabel" class="truncate" :class="selectedVal ? 'text-gray-800' : 'text-gray-400'"></span>
        <div class="flex items-center gap-1 shrink-0">
            <span x-show="selectedVal" @click.stop="clear()" class="w-5 h-5 rounded-full bg-gray-100 hover:bg-red-100 text-gray-400 hover:text-red-500 flex items-center justify-center cursor-pointer transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </span>
            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
        x-transition
        class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden"
        style="display: none;">

        <div class="p-2 border-b border-gray-100">
            <input type="text"
                x-model="search"
                placeholder="{{ $searchPlaceholder }}"
                class="w-full h-9 px-3 text-sm rounded-lg border border-gray-200 focus:outline-none focus:border-[#0b3d2c] focus:ring-1 focus:ring-[#0b3d2c]">
        </div>

        <ul class="max-h-48 overflow-y-auto py-1">
            @if($defaultOption)
            <li>
                <button type="button" @click="select('', '{{ $defaultOption }}')"
                    class="w-full text-right px-3 py-2 text-sm"
                    :class="selectedVal === '' ? 'text-[#0b3d2c] font-semibold bg-emerald-50' : 'text-gray-700 hover:bg-gray-50'">
                    {{ $defaultOption }}
                </button>
            </li>
            @endif

            <template x-for="option in filtered" :key="option.value">
                <li>
                    <button type="button" @click="select(option.value, option.label)"
                        class="w-full text-right px-3 py-2 text-sm flex items-center gap-2"
                        :class="String(selectedVal) === String(option.value) ? 'text-[#0b3d2c] font-semibold bg-emerald-50' : 'text-gray-700 hover:bg-gray-50'">
                        <svg x-show="String(selectedVal) === String(option.value)" class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-text="option.label"></span>
                    </button>
                </li>
            </template>

            <li x-show="filtered.length === 0" class="px-3 py-4 text-sm text-gray-400 text-center">لا توجد نتائج</li>
        </ul>
    </div>
</div>