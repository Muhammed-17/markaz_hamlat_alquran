@props(['name', 'label' => ''])

<div>
    @if ($label)
        <x-input-label :for="$name" :value="$label" />
    @endif

    <select name="{{ $name }}" id="{{ $name }}"
        {{ $attributes->merge([
            'class' =>'w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0a5c36] outline-none transition-all ' .
                ($errors->has($name) ? 'border-red-500 focus:ring-red-500' : ''),
        ]) }}>
        {{ $slot }}
    </select>

    <x-input-error :messages="$errors->get($name)" />
</div>
