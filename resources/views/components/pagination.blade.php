@props([
    'items',
    'label' => 'resultados'
])

@if (method_exists($items, 'hasPages') && $items->hasPages())
<div class="px-6 py-4 border-t border-gray-100">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Información de resultados -->
        <div class="text-sm text-gray-600 order-2 sm:order-1">
            @if(method_exists($items, 'firstItem'))
            Mostrando
            <span class="font-semibold text-gray-900">{{ $items->firstItem() }}</span>
            a
            <span class="font-semibold text-gray-900">{{ $items->lastItem() }}</span>
            de
            <span class="font-semibold text-gray-900">{{ $items->total() }}</span>
            {{ $label }}
            @else
            <span class="font-semibold text-gray-900">{{ $items->count() }}</span>
            {{ $label }}
            @endif
        </div>

        <!-- Controles de paginación -->
        <div class="flex items-center gap-1 order-1 sm:order-2">
            {{-- Botón Previous --}}
            @if ($items->onFirstPage())
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 cursor-not-allowed rounded-lg">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Anterior
                </span>
            @else
                <a href="{{ $items->appends(request()->query())->previousPageUrl() }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#B4325E] hover:border-[#B4325E]/30 transition-all duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Anterior
                </a>
            @endif

            {{-- Números de página --}}
            <div class="hidden sm:flex items-center gap-1">
                {{-- Primera página --}}
                @if($items->currentPage() > 3)
                    <a href="{{ $items->appends(request()->query())->url(1) }}" 
                       class="inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#B4325E] hover:border-[#B4325E]/30 transition-all duration-200">
                        1
                    </a>
                    @if($items->currentPage() > 4)
                        <span class="inline-flex items-center justify-center w-10 h-10 text-gray-500">...</span>
                    @endif
                @endif

                {{-- Páginas alrededor de la actual --}}
                @foreach(range(max(1, $items->currentPage() - 2), min($items->lastPage(), $items->currentPage() + 2)) as $page)
                    @if($page == $items->currentPage())
                        <span class="inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] border border-[#B4325E] rounded-lg shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $items->appends(request()->query())->url($page) }}" 
                           class="inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#B4325E] hover:border-[#B4325E]/30 transition-all duration-200">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Última página --}}
                @if($items->currentPage() < $items->lastPage() - 2)
                    @if($items->currentPage() < $items->lastPage() - 3)
                        <span class="inline-flex items-center justify-center w-10 h-10 text-gray-500">...</span>
                    @endif
                    <a href="{{ $items->appends(request()->query())->url($items->lastPage()) }}" 
                       class="inline-flex items-center justify-center w-10 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#B4325E] hover:border-[#B4325E]/30 transition-all duration-200">
                        {{ $items->lastPage() }}
                    </a>
                @endif
            </div>

            {{-- Página actual en móvil --}}
            <div class="sm:hidden inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg">
                {{ $items->currentPage() }} de {{ $items->lastPage() }}
            </div>

            {{-- Botón Next --}}
            @if ($items->hasMorePages())
                <a href="{{ $items->appends(request()->query())->nextPageUrl() }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-[#B4325E] hover:border-[#B4325E]/30 transition-all duration-200">
                    Siguiente
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 cursor-not-allowed rounded-lg">
                    Siguiente
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </div>
    </div>
</div>
@endif 