<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full bg-blue-600 hover:bg-blue-700 text-orange-500 font-bold py-3 px-4  transition duration-200 flex items-center justify-center gap-2']) }}>
    {{ $slot }}
</button>
