@props(['disabled' => false])

<x-primary-button {{ $attributes->merge(['class' => 'bg-red-600 hover:bg-red-500 active:bg-red-700 focus:ring-red-500']) }}>
    {{ $slot }}
</x-primary-button>
