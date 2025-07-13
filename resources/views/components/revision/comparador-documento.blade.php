@props(['documento', 'formulario' => '', 'titulo', 'tramiteId'])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" 
    x-data="documentViewer('{{ $titulo }}', {{ isset($documento['id']) ? $documento['id'] : 'null' }})"
    x-init="init()">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">{{ $titulo }}</h3>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200" x-ref="container">
        <!-- Panel Izquierdo: Documento o Mapa -->
        <div class="h-[600px] relative">
            <div class="absolute inset-0 p-4">
                <div class="bg-gray-50 rounded-lg p-4 mb-4 flex items-center justify-between">
                    <div>
                        @if(strtolower($titulo) === 'domicilio y comprobante')
                            <span class="text-sm font-medium text-gray-700">Ubicación en Mapa</span>
                        @else
                            <span class="text-sm font-medium text-gray-700">Documento:</span>
                            <span class="ml-2 text-sm text-gray-600">{{ $documento['nombre'] ?? 'Sin nombre' }}</span>
                        @endif
                    </div>
                    @if(strtolower($titulo) === 'domicilio y comprobante')
                        <div class="flex items-center space-x-2">
                            <button type="button" 
                                    @click="toggleStreetView"
                                    class="px-3 py-1 bg-[#9d2449] text-white text-sm rounded-md hover:bg-[#8a203f] transition-colors">
                                <i class="fas fa-street-view mr-1"></i>
                                Street View
                            </button>
                            <button type="button" 
                                    @click="toggleMapType"
                                    class="px-3 py-1 bg-[#9d2449] text-white text-sm rounded-md hover:bg-[#8a203f] transition-colors">
                                <i class="fas fa-map mr-1"></i>
                                Vista
                            </button>
                            <button type="button" 
                                    @click="reloadAddress"
                                    class="px-3 py-1 bg-[#9d2449] text-white text-sm rounded-md hover:bg-[#8a203f] transition-colors">
                                <i class="fas fa-sync-alt mr-1"></i>
                                Recargar
                            </button>
                        </div>
                    @endif
                </div>
                <div class="h-[calc(100%-4rem)] rounded-lg border border-gray-200 overflow-hidden">
                    @if(strtolower($titulo) === 'domicilio y comprobante')
                        <div class="relative h-full">
                            <div x-ref="map" class="w-full h-full"></div>
                            <div x-ref="streetView" class="w-full h-full absolute top-0 left-0" style="display: none;"></div>
                            <div x-ref="mapLoader" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90">
                                <div class="flex items-center space-x-3">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#9d2449]"></div>
                                    <span class="text-gray-600">Cargando mapa...</span>
                                </div>
                            </div>
                            <div x-ref="mapError" class="absolute inset-0 flex items-center justify-center bg-white hidden">
                                <div class="text-center p-4">
                                    <i class="fas fa-exclamation-triangle text-[#9d2449] text-4xl mb-4"></i>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No se pudo cargar el mapa</h3>
                                    <p class="text-gray-600 mb-4">Es posible que un bloqueador de anuncios esté impidiendo cargar Google Maps.</p>
                                    <button @click="retryLoadMap" class="px-4 py-2 bg-[#9d2449] text-white rounded-md hover:bg-[#8a203f] transition-colors">
                                        <i class="fas fa-sync-alt mr-2"></i>Reintentar
                                    </button>
                                </div>
                            </div>
                            <div x-ref="locationInfo" class="absolute bottom-4 left-4 right-4 bg-white rounded-lg shadow-lg p-4 hidden">
                                <div x-ref="locationDetails" class="text-sm"></div>
                            </div>
                        </div>
                    @elseif(isset($documento['id']))
                        <iframe src="{{ route('revision.ver-documento', ['tramite' => $tramiteId, 'documento' => $documento['id']]) }}?inline=1" 
                                class="w-full h-full" 
                                sandbox="allow-same-origin allow-scripts allow-popups allow-forms"
                                loading="lazy"
                                title="Visor de documento {{ $documento['nombre'] ?? 'documento' }}"></iframe>
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
                <div class="h-[calc(100%-4rem)] overflow-y-auto rounded-lg border border-gray-200 p-4" x-ref="formulario">
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
                        @click="toggleSyncScroll"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]"
                        :class="{ 'bg-[#9d2449] text-white border-[#9d2449]': syncScroll }">
                    <i class="fas fa-link mr-2"></i>
                    Sincronizar scroll
                </button>
                <button type="button"
                        @click="toggleSideBySide"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]"
                        :class="{ 'bg-[#9d2449] text-white border-[#9d2449]': !sideBySide }">
                    <i class="fas fa-columns mr-2"></i>
                    Vista lado a lado
                </button>
            </div>
            @if(isset($documento['id']) && strtolower($titulo) !== 'domicilio y comprobante')
                <div class="flex items-center space-x-2">
                <button type="button"
                            @click="zoomIn"
                        class="p-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-search-plus"></i>
                </button>
                <button type="button"
                            @click="zoomOut"
                        class="p-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-search-minus"></i>
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    function documentViewer(titulo, documentoId) {
        return {
            syncScroll: true,
            sideBySide: true,
            zoom: 100,
            mapViewer: null,

            async init() {
                if (titulo.toLowerCase() === 'domicilio y comprobante') {
                    try {
                        this.mapViewer = new GoogleMapsViewer(
                            this.$refs.map,
                            this.$refs.streetView,
                            this.$refs.mapLoader,
                            this.$refs.locationInfo,
                            this.$refs.locationDetails
                        );
                        await this.mapViewer.initialize();
                    } catch (error) {
                        console.error('Error initializing map:', error);
                        this.$refs.mapLoader.style.display = 'none';
                        this.$refs.mapError.classList.remove('hidden');
                    }
                }

                this.initScrollSync();
            },

            retryLoadMap() {
                this.$refs.mapError.classList.add('hidden');
                this.$refs.mapLoader.style.display = 'flex';
                this.init();
            },

            reloadAddress() {
                if (this.mapViewer) {
                    this.mapViewer.loadAddressFromForm();
                }
            },

            // ... existing methods ...
        };
    }
</script>
<script src="{{ asset('js/components/google-maps-viewer.js') }}"></script>
@endpush 
@endonce 