@props([
    'type' => 'text',
    'label' => '',
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'helper' => null,
    'maxlength' => null,
    'pattern' => null,
    'inputmode' => null,
])

<div class="form-group mb-6">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-[#9D2449]">*</span>
        @endif
    </label>
    @endif

    <div class="relative">
        @if($type === 'textarea')
            <textarea
                id="{{ $name }}"
                name="{{ $name }}"
                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#9D2449]/20 focus:border-[#9D2449] transition-colors duration-200 ease-in-out
                       {{ $error ? 'border-red-300 focus:border-red-500 focus:ring-red-200' : '' }}
                       {{ $disabled ? 'bg-gray-50 cursor-not-allowed' : '' }}"
                placeholder="{{ $placeholder }}"
                {{ $disabled ? 'disabled' : '' }}
                {{ $required ? 'required' : '' }}
                {{ $maxlength ? 'maxlength='.$maxlength : '' }}
            >{{ $value }}</textarea>
        @elseif($type === 'select')
            <select
                id="{{ $name }}"
                name="{{ $name }}"
                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#9D2449]/20 focus:border-[#9D2449] transition-colors duration-200 ease-in-out
                       {{ $error ? 'border-red-300 focus:border-red-500 focus:ring-red-200' : '' }}
                       {{ $disabled ? 'bg-gray-50 cursor-not-allowed' : '' }}"
                {{ $disabled ? 'disabled' : '' }}
                {{ $required ? 'required' : '' }}
            >
                {{ $slot }}
            </select>
        @else
            <input
                type="{{ $type }}"
                id="{{ $name }}"
                name="{{ $name }}"
                value="{{ $value }}"
                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#9D2449]/20 focus:border-[#9D2449] transition-colors duration-200 ease-in-out
                       {{ $error ? 'border-red-300 focus:border-red-500 focus:ring-red-200' : '' }}
                       {{ $disabled ? 'bg-gray-50 cursor-not-allowed' : '' }}"
                placeholder="{{ $placeholder }}"
                {{ $disabled ? 'disabled' : '' }}
                {{ $required ? 'required' : '' }}
                {{ $maxlength ? 'maxlength='.$maxlength : '' }}
                {{ $pattern ? 'pattern='.$pattern : '' }}
                {{ $inputmode ? 'inputmode='.$inputmode : '' }}
            >
        @endif

        @if($error)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        @endif
    </div>

    @if($error)
        <p class="mt-2 text-sm text-red-600 transition-all duration-200 ease-in-out">
            {{ $error }}
        </p>
    @endif

    @if($helper && !$error)
        <p class="mt-2 text-sm text-gray-500">
            {{ $helper }}
        </p>
    @endif
</div> 