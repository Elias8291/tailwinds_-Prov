@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 rounded-md shadow-sm']) !!}> 