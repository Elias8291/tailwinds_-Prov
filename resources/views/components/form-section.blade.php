@props([
    'title' => '',
    'icon' => null,
    'description' => null
])

<div class="space-y-6">
    <!-- Section Header -->
    <div class="border-b border-gray-200 pb-5">
        <div class="flex items-center gap-3">
            @if($icon)
                <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-[#9D2449]/10 text-[#9D2449]">
                    <i class="fas {{ $icon }} text-lg"></i>
                </div>
            @endif
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                @if($description)
                    <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Content -->
    <div class="space-y-6">
        {{ $slot }}
    </div>

    <!-- Section Footer - For buttons if needed -->
    @if(isset($footer))
        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div> 