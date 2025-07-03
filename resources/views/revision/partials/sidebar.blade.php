{{-- Panel Lateral de Documentos y Mapa --}}
<aside class="w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden" id="documentos-container">
    {{-- Header del Panel --}}
    <div class="bg-gradient-to-r from-[#B4325E] to-[#93264B] p-4">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-medium text-white">Documentos</h3>
                <p class="text-sm text-white/80">y Verificación</p>
            </div>
        </div>
    </div>

    {{-- Lista de Documentos --}}
    <div class="p-4 border-b border-gray-200">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Documentos del Trámite</h4>
        <div id="lista-documentos" class="space-y-2 max-h-60 overflow-y-auto">
            @forelse($documentosPorSeccion as $seccion => $documentos)
                @if(count($documentos) > 0)
                    {{-- Separador de sección --}}
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider py-2 border-b">
                        {{ ucfirst(str_replace('-', ' ', $seccion)) }}
                    </div>
                    
                    @foreach($documentos as $documento)
                        <div class="documento-item cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all"
                             data-documento-id="{{ $documento['id'] ?? '' }}"
                             data-ruta="{{ $documento['ruta_archivo'] ?? '' }}"
                             onclick="RevisionTramite.documents.open(this)">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 line-clamp-2">
                                        {{ $documento['nombre'] ?? 'Documento sin nombre' }}
                                    </p>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($documento['estado'] ?? 'pendiente') }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            v{{ $documento['version_documento'] ?? '1' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            @empty
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No hay documentos</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Visor de Documentos Embebido --}}
    <div id="visor-documento" class="hidden border-b border-gray-200">
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-medium text-gray-900" id="documento-actual-nombre">Documento Seleccionado</h4>
                <div class="flex space-x-2">
                    <button id="btn-ver-completo" 
                            onclick="RevisionTramite.documents.openFullScreen()"
                            class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                            title="Ver en pantalla completa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </button>
                    <button onclick="RevisionTramite.documents.close()"
                            class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                            title="Cerrar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <iframe id="iframe-documento" class="w-full h-80 border border-gray-200 rounded" frameborder="0"></iframe>
        </div>
    </div>

    {{-- Mapa de Domicilio --}}
    <div class="p-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-900">Ubicación</h4>
            <button onclick="RevisionTramite.maps.openFullScreen()" 
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                Ver completo
            </button>
        </div>
        
        {{-- Información de dirección resumida --}}
        @if(!empty($datosDomicilio))
            <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                <p class="text-xs font-medium text-gray-500 mb-1">Dirección:</p>
                <p class="text-sm text-gray-900">
                    {{ ($datosDomicilio['calle'] ?? '') . ' ' . ($datosDomicilio['numero_exterior'] ?? '') }}
                    @if(!empty($datosDomicilio['numero_interior']))
                        Int. {{ $datosDomicilio['numero_interior'] }}
                    @endif
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    {{ ($datosDomicilio['colonia'] ?? '') . ', ' . ($datosDomicilio['municipio'] ?? '') }}
                </p>
                <p class="text-sm text-gray-600">
                    {{ ($datosDomicilio['estado'] ?? '') . ', CP ' . ($datosDomicilio['codigo_postal'] ?? '') }}
                </p>
            </div>
        @endif

        {{-- Contenedor del mapa --}}
        <div id="mapa-panel-lateral" class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center">
            <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-600 mx-auto"></div>
                <p class="text-sm text-gray-600 mt-2">Cargando mapa...</p>
            </div>
        </div>
    </div>
</aside> 