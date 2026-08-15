@props([
    'name',
    'id' => null,
    'options' => [],
    'value' => null,
    'multiple' => false,
    'placeholder' => 'اختر أو اكتب قيمة جديدة...',
])

@php
    $selectId = $id ?? $name . '_' . uniqid();

    $normalizedOptions = collect($options)->map(function ($opt) {
        return is_array($opt) ? $opt : ['value' => $opt, 'label' => $opt];
    });

    // ✅ القيمة الحالية: نص واحد (single) أو مصفوفة (multiple)
    $selectedValues = $multiple
        ? collect($value ?? [])->filter()
        : collect([$value])->filter();

    // ✅ لو القيمة المخزنة (من قديم "أخرى" مثلاً) مش موجودة في القائمة الأصلية،
    // نضيفها كخيار تلقائيًا عشان تظهر محددة فورًا بدل ما تختفي
    foreach ($selectedValues as $val) {
        if (!$normalizedOptions->contains('value', $val)) {
            $normalizedOptions->push(['value' => $val, 'label' => $val]);
        }
    }
@endphp

<div wire:ignore.self x-data x-init="
    new TomSelect(document.getElementById('{{ $selectId }}'), {
        create: true,
        createOnBlur: true,
        persist: false,
        placeholder: @js($placeholder),
        maxItems: {{ $multiple ? 'null' : '1' }},
        render: {
            option_create: function(data, escape) {
                return '<div class=&quot;create p-2 text-emerald-700 font-bold bg-emerald-50 hover:bg-emerald-100 cursor-pointer rounded-lg&quot;>إضافة «' + escape(data.input) + '»...</div>';
            }
        }
    });
">
    <select
        id="{{ $selectId }}"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        {{ $multiple ? 'multiple' : '' }}
        {{ $attributes }}
        class="w-full">
        @if(!$multiple)
        <option value=""></option>
        @endif
        @foreach($normalizedOptions as $opt)
        <option value="{{ $opt['value'] }}" @selected($selectedValues->contains($opt['value']))>
            {{ $opt['label'] }}
        </option>
        @endforeach
    </select>
</div>