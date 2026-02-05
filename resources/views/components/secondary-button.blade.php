@props(['disabled' => false])

<x-primary-button {{ $attributes->merge(['class' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700']) }}>
    {{ $slot }}
</x-primary-button>
