@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-[#0d4636] focus:ring focus:ring-blue-200 transition duration-200 outline-none']) }}>
