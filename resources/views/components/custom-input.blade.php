@props([
'name',
'type' => 'text',
'value' => '',
'label' => '',
])
<div>
    <x-input-label for="{{ $name }}" value="{{ $label }}" />
    
    <input
    type="{{ $type }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    {{ $attributes->merge([
    'class' =>
    'w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0a5c36] ' .
    ($errors->has($name) ? 'border-red-500 focus:ring-red-500' : '')
    ]) }}>
    
    <x-input-error :messages="$errors->get($name)" />
</div>