@props([
    'name',
    'rows' => 3,
    'value' => '',
    'label' => '',
])

<div>
    <x-input-label for="$name" :value="$label" />
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge([
            'class' =>
            'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#0a5c36] ' .
            ($errors->has($name)? 'border-red-500 focus:ring-red-500 bg-red-50': 'border-gray-200')
            ]) 
        }}>{{ old($name, $value) }}</textarea>

    <x-input-error :messages="$errors->get($name)" />
</div>
