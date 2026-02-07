@props(['value'])

<label dir="rtl" {{ $attributes->merge(['class' => 'block text-sm font-bold text-gray-700 mb-2']) }}>
    {{ $value ?? $slot }}
</label>
