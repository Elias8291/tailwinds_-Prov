@props(['documento', 'formulario', 'titulo'])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">{{ $titulo }}</h3>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">
        <!-- Panel Izquierdo: Documento -->
        <div class="h-[600px] relative">
            <div class="absolute inset-0 p-4">
                <div class="bg-gray-50 rounded-lg p-4 mb-4 flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Documento:</span>
                        <span class="ml-2 text-sm text-gray-600">{{ $documento['nombre'] ?? 'Sin nombre' }}</span>
                    </div>
                    @if(isset($documento['ruta_archivo']) && $documento['ruta_archivo'])
                    <button type="button" 
                            onclick="window.open('{{ $documento['ruta_archivo'] }}', '_blank')"
                            class="px-3 py-1 bg-[#9d2449] text-white text-sm rounded-md hover:bg-[#8a203f] transition-colors">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        Abrir
                    </button>
                    @endif
                </div>
                <div class="h-[calc(100%-4rem)] rounded-lg border border-gray-200 overflow-hidden">
                    @if(isset($documento['ruta_archivo']) && $documento['ruta_archivo'])
                        <iframe src="{{ $documento['ruta_archivo'] }}" class="w-full h-full"></iframe>
                    @else
                        <div class="flex flex-col items-center justify-center h-full bg-gray-50 space-y-4">
                            <i class="fas fa-file-alt text-gray-400 text-4xl"></i>
                            <div class="text-center">
                                <p class="text-gray-500">No hay documento disponible para esta sección.</p>
                                @if(isset($documento['estado']))
                                <p class="text-sm text-gray-400 mt-2">Estado: {{ ucfirst($documento['estado']) }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Formulario -->
        <div class="h-[600px] relative">
            <div class="absolute inset-0 p-4">
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <span class="text-sm font-medium text-gray-700">Datos del formulario</span>
                </div>
                <div class="h-[calc(100%-4rem)] overflow-y-auto rounded-lg border border-gray-200 p-4">
                    {!! $formulario !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Controles de Comparación -->
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button type="button" 
                        onclick="toggleSyncScroll(this)"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]">
                    <i class="fas fa-link mr-2"></i>
                    Sincronizar scroll
                </button>
                <button type="button"
                        onclick="toggleSideBySide(this)"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]">
                    <i class="fas fa-columns mr-2"></i>
                    Vista lado a lado
                </button>
            </div>
            @if(isset($documento['ruta_archivo']) && $documento['ruta_archivo'])
            <div class="flex items-center space-x-2">
                <button type="button"
                        onclick="zoomIn()"
                        class="p-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-search-plus"></i>
                </button>
                <button type="button"
                        onclick="zoomOut()"
                        class="p-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-search-minus"></i>
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
let syncScroll = false;
let currentZoom = 100;

function toggleSyncScroll(button) {
    syncScroll = !syncScroll;
    button.classList.toggle('bg-[#9d2449]');
    button.classList.toggle('text-white');
    button.classList.toggle('border-[#9d2449]');
}

function toggleSideBySide(button) {
    const container = button.closest('.bg-white').querySelector('.grid');
    container.classList.toggle('lg:grid-cols-2');
    container.classList.toggle('lg:grid-cols-1');
    button.classList.toggle('bg-[#9d2449]');
    button.classList.toggle('text-white');
    button.classList.toggle('border-[#9d2449]');
}

function zoomIn() {
    currentZoom += 10;
    updateZoom();
}

function zoomOut() {
    currentZoom = Math.max(50, currentZoom - 10);
    updateZoom();
}

function updateZoom() {
    document.querySelectorAll('iframe').forEach(iframe => {
        iframe.style.transform = `scale(${currentZoom / 100})`;
        iframe.style.transformOrigin = 'top left';
    });
}

// Sincronización de scroll
document.addEventListener('DOMContentLoaded', () => {
    const containers = document.querySelectorAll('.overflow-y-auto');
    containers.forEach(container => {
        container.addEventListener('scroll', function() {
            if (!syncScroll) return;
            const scrollPercentage = this.scrollTop / (this.scrollHeight - this.clientHeight);
            containers.forEach(otherContainer => {
                if (otherContainer !== this) {
                    otherContainer.scrollTop = scrollPercentage * (otherContainer.scrollHeight - otherContainer.clientHeight);
                }
            });
        });
    });
});
</script>
@endpush 