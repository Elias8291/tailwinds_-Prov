@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary
    'disabled' => false,
])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'px-6 py-3 rounded-lg font-semibold text-sm transition-all duration-200 ease-in-out shadow-sm
                  ' . ($variant === 'primary' 
                    ? 'bg-gradient-to-r from-[#9D2449] to-[#6A2E2E] text-white hover:from-[#8A1F3F] hover:to-[#5A2626] focus:ring-2 focus:ring-[#9D2449]/20' 
                    : 'bg-gradient-to-r from-[#2D5446] to-[#1F3A30] text-white hover:from-[#264A3D] hover:to-[#1A3229] focus:ring-2 focus:ring-[#2D5446]/20')
                  . ($disabled ? ' opacity-50 cursor-not-allowed' : ' hover:transform hover:-translate-y-0.5')
    ]) }}
    {{ $disabled ? 'disabled' : '' }}
>
    {{ $slot }}
</button> 