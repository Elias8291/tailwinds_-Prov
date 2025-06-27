@props([
    'active' => false,
    'href' => '#'
])

@php
$classes = ($active ?? false)
            ? 'bg-[#9d2449] text-white'
            : 'text-gray-600 hover:text-[#9d2449] hover:bg-gray-50';
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => 'flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 ' . $classes]) }}>
    {{ $slot }}
</a> 