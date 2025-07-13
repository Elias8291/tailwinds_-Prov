@props(['seccion' => '', 'tramite'])

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    
    .mobile-viewer {
        height: auto !important;
        min-height: 300px !important;
        max-height: 400px !important;
    }
    
    /* Mejoras para móvil */
    @media (max-width: 640px) {
        .mobile-viewer {
            height: auto !important;
            min-height: 280px !important;
            max-height: 350px !important;
        }
    }
    
    @media (min-width: 641px) and (max-width: 768px) {
        .mobile-viewer {
            height: auto !important;
            min-height: 320px !important;
            max-height: 380px !important;
        }
    }
    
    @media (min-width: 769px) and (max-width: 1023px) {
        .mobile-viewer {
            height: auto !important;
            min-height: 350px !important;
            max-height: 420px !important;
        }
    }
    
    /* Mejoras para pantallas muy pequeñas */
    @media (max-width: 360px) {
        .mobile-viewer {
            height: auto !important;
            min-height: 250px !important;
            max-height: 300px !important;
        }
    }
    
    /* Asegurar que el contenedor del mapa ocupe todo el espacio disponible */
    #google-map-desktop {
        width: 100% !important;
        height: 100% !important;
        border-radius: 0 0 8px 8px;
    }
    
    /* Contenedor del mapa para desktop - tamaño fijo */
    .map-container-desktop {
        height: 400px !important;
        width: 100%;
        position: relative;
        overflow: hidden;
    }
</style>

<!-- Desktop Split-Pane (lg and up) -->
<div x-data="{
        leftWidth: 50,
        isDragging: false,
        documentVisible: false, // El estado ahora es controlado por el evento global
        activeDocument: @if($seccion === 'domicilio') null @else {{ !empty($documentos) ? json_encode($documentos[0]) : 'null' }} @endif,
        showMapInfo: false,
        startDrag(event) {
            this.isDragging = true;
            const moveHandler = (e) => {
                if (!this.isDragging) return;
                const containerRect = this.$el.getBoundingClientRect();
                let newLeftWidth = ((e.clientX - containerRect.left) / containerRect.width) * 100;
                if (newLeftWidth < 25) newLeftWidth = 25;
                if (newLeftWidth > 75) newLeftWidth = 75;
                this.leftWidth = newLeftWidth;
            };
            const stopHandler = () => {
                this.isDragging = false;
                window.removeEventListener('mousemove', moveHandler);
                window.removeEventListener('mouseup', stopHandler);
                window.removeEventListener('mouseleave', stopHandler);
            };
            window.addEventListener('mousemove', moveHandler);
            window.addEventListener('mouseup', stopHandler);
            window.addEventListener('mouseleave', stopHandler);
        },
        refreshIframe() {
            const currentDoc = this.activeDocument;
            if (currentDoc) {
                this.activeDocument = null;
                this.$nextTick(() => {
                    this.activeDocument = currentDoc;
                });
            }
        }
    }"
    x-init="
        $watch('documentVisible', isVisible => {
            if (isVisible) {
                setTimeout(() => { refreshIframe() }, 200);
            }
        });
        window.addEventListener('document-visibility-changed', (event) => {
            if (event.detail.seccion === '{{ $seccion }}') {
                documentVisible = event.detail.visible;
            }
        });
    "
    class="hidden lg:flex w-full bg-white">
    
    <!-- Left Panel -->
    <div class="relative transition-all duration-300 border-r border-gray-200"
         :style="{ width: documentVisible ? leftWidth + '%' : '100%' }">
        <!-- La barra de herramientas ha sido eliminada -->
        <div class="px-4 xl:px-6 py-6 overflow-y-auto">
            @if($formulario)
                <div class="prose max-w-none">
                    {!! $formulario !!}
                </div>
            @else
                <div class="text-center py-12 text-gray-500 h-full flex items-center justify-center">
                    <div>
                        <i class="fas fa-info-circle text-4xl text-gray-300"></i>
                        <p class="mt-4">No hay datos de formulario para esta sección.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Divider -->
    <div x-show="documentVisible" x-transition @mousedown.prevent="startDrag"
         class="w-1.5 flex-shrink-0 bg-gray-200 hover:bg-primary cursor-col-resize transition-colors duration-200"></div>

    <!-- Right Panel -->
    <div x-show="documentVisible" x-transition class="flex-1 flex flex-col min-w-0">
        
        <!-- Document Selector: Horizontal Icon Bar -->
        <div class="flex-shrink-0 bg-white p-3 border-b border-gray-200">
            <div class="flex items-center gap-3 overflow-x-auto h-12 no-scrollbar px-1">
                
                <!-- Static Map Button -->
                @if ($seccion === 'domicilio')
                <button @click="showMapInfo = true; activeDocument = null"
                        class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-300 focus:outline-none transform hover:scale-110 bg-gray-100 text-gray-500 hover:bg-gray-200"
                        :class="{ 'bg-[#9d2449] text-white': showMapInfo }"
                        title="Ver Geolocalización">
                    <i class="fas fa-map-location-dot text-xl"></i>
                </button>
                @endif
                
                @if(!empty($documentos))
                    @foreach($documentos as $documento)
                        @php
                            $estado = $documento['estado'] ?? 'Pendiente';
                            $documentoId = $documento['id'];
                        @endphp
                        <button @click="activeDocument = {{ json_encode($documento) }}; showMapInfo = false"
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-300 focus:outline-none transform hover:scale-110"
                                :class="{
                                    'bg-[#9d2449] text-white shadow-lg': activeDocument && activeDocument.id === {{ $documentoId }},
                                    'bg-green-100 text-green-600': !activeDocument || activeDocument.id !== {{ $documentoId }} && '{{$estado}}' === 'Aprobado',
                                    'bg-red-100 text-red-600': !activeDocument || activeDocument.id !== {{ $documentoId }} && '{{$estado}}' === 'Rechazado',
                                    'bg-gray-100 text-gray-500 hover:bg-gray-200': !activeDocument || activeDocument.id !== {{ $documentoId }} && !['Aprobado', 'Rechazado'].includes('{{$estado}}')
                                }"
                                title="{{ $documento['nombre'] }}">
                            @if($estado === 'Aprobado')
                                <i class="fas fa-file-circle-check text-xl"></i>
                            @elseif($estado === 'Rechazado')
                                <i class="fas fa-file-circle-xmark text-xl"></i>
                            @else
                                <i class="fas fa-file-circle-question text-xl"></i>
                            @endif
                        </button>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500 px-2">No hay documentos para esta sección.</p>
                @endif
            </div>
        </div>

        <!-- Document Viewer -->
        <div class="flex-grow bg-slate-50 p-4">
            <!-- Google Maps Viewer -->
            <template x-if="showMapInfo">
                <div class="h-full bg-white rounded-lg shadow-inner border flex flex-col">
                    <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-white to-gray-50 rounded-t-lg flex-shrink-0">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-[#9d2449] rounded-full flex items-center justify-center shadow-md">
                                <i class="fas fa-map-marker-alt text-white text-lg"></i>
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-lg font-semibold text-gray-800">
                                    Ubicación del Domicilio
                                </h4>
                                <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                                    {{ $domicilioConcatenado }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow relative overflow-hidden map-container-desktop">
                        <div id="google-map-desktop" class="w-full h-full"></div>
                    </div>
                </div>
            </template>
            
            <template x-if="activeDocument && activeDocument.ruta_archivo">
                <div class="w-full h-full bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col">
                    <!-- Viewer Header -->
                    <div class="p-3 border-b border-slate-100 flex justify-between items-center bg-white rounded-t-xl">
                        <h4 class="text-md font-medium text-slate-700 truncate pr-4" x-text="activeDocument.nombre"></h4>
                        <a :href="`{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => '0']) }}`.replace('/0', '/' + activeDocument.id) + '?inline=1'"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#9d2449] text-white text-xs font-medium rounded-md hover:bg-[#7a1d3a] transition-colors flex-shrink-0">
                            <i class="fas fa-external-link-alt text-sm"></i>
                            <span>Abrir</span>
                        </a>
                    </div>
                    <!-- Viewer Iframe -->
                    <div class="flex-grow relative">
                        <iframe :key="activeDocument.id" :src="`{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => '0']) }}`.replace('/0', '/' + activeDocument.id) + '?inline=1'" width="100%" height="100%" class="bg-white"></iframe>
                    </div>
                </div>
            </template>
            <template x-if="activeDocument && !activeDocument.ruta_archivo">
                <div class="h-full flex items-center justify-center text-center bg-white rounded-lg shadow-inner border">
                    <div>
                        <i class="fas fa-file-circle-xmark text-4xl text-gray-400"></i>
                        <p class="mt-4 text-gray-600">No hay un archivo adjunto para este documento.</p>
                        <p class="mt-1 text-sm text-gray-500" x-text="activeDocument.nombre"></p>
                    </div>
                </div>
            </template>
            <template x-if="!activeDocument && !showMapInfo">
                <div class="h-full flex items-center justify-center text-center bg-white rounded-lg shadow-inner border">
                    <div>
                        <i class="fas fa-hand-pointer text-4xl text-gray-400"></i>
                        <p class="mt-4 text-gray-600">Seleccione un documento para visualizarlo.</p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- Mobile and Tablet Tabs (below lg) -->
<div x-data="{ 
        activeTab: 'form',
        activeDocument: @if($seccion === 'domicilio') null @else {{ !empty($documentos) ? json_encode($documentos[0]) : 'null' }} @endif,
        showMapInfo: false
    }" class="lg:hidden w-full border border-gray-200 rounded-lg overflow-hidden bg-white">
    
    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 bg-gray-50">
        <nav class="flex">
            @if($formulario)
            <button @click="activeTab='form'" 
                    :class="activeTab==='form' ? 'text-[#9d2449] border-b-2 border-[#9d2449] bg-white' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'" 
                    class="flex-1 py-2 sm:py-3 px-2 text-xs sm:text-sm font-medium flex items-center justify-center gap-1 sm:gap-2 transition-colors">
                <i class="fas fa-file-invoice text-xs sm:text-sm"></i>
                <span class="hidden xs:inline">Formulario</span>
            </button>
            @endif
            
            @if(!empty($documentos) || $seccion === 'domicilio')
            <button @click="activeTab='doc'" 
                    :class="activeTab==='doc' ? 'text-[#9d2449] border-b-2 border-[#9d2449] bg-white' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'" 
                    class="flex-1 py-2 sm:py-3 px-2 text-xs sm:text-sm font-medium flex items-center justify-center gap-1 sm:gap-2 transition-colors">
                <i class="fas fa-folder-open text-xs sm:text-sm"></i>
                <span class="hidden xs:inline">Documentos</span>
            </button>
            @endif
        </nav>
    </div>
    
    <!-- Tab Content -->
    <div class="relative">
        <!-- Formulario Tab -->
        <div x-show="activeTab==='form'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="p-3 sm:p-4 bg-white min-h-[350px] sm:min-h-[400px]">
            @if($formulario)
                <div class="prose max-w-none prose-sm sm:prose-base">
                    {!! $formulario !!}
                </div>
            @else
                <div class="text-center py-8 sm:py-12 text-gray-500">
                    <i class="fas fa-info-circle text-xl sm:text-2xl lg:text-3xl text-gray-300"></i>
                    <p class="mt-4 text-sm sm:text-base">No hay datos de formulario para esta sección.</p>
                </div>
            @endif
        </div>
        
        <!-- Documentos Tab -->
        <div x-show="activeTab==='doc'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="bg-gray-50">
            
            <!-- Document Selector for Mobile -->
            <div class="bg-white border-b border-gray-200 p-2 sm:p-3">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-2">
                    <!-- Map Button para sección domicilio -->
                    @if ($seccion === 'domicilio')
                    <button @click="showMapInfo = true; activeDocument = null"
                            class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 flex flex-col items-center justify-center rounded-lg transition-all duration-300 focus:outline-none transform hover:scale-105 border-2"
                            :class="{ 
                                'bg-[#9d2449] text-white border-[#9d2449]': showMapInfo,
                                'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200': !showMapInfo
                            }"
                            title="Ver Geolocalización">
                        <i class="fas fa-map-location-dot text-sm sm:text-lg md:text-xl"></i>
                        <span class="text-xs mt-1 hidden md:block">Mapa</span>
                    </button>
                    @endif

                    <!-- Document Buttons -->
                    @if(!empty($documentos))
                        @foreach($documentos as $documento)
                            @php
                                $estado = $documento['estado'] ?? 'Pendiente';
                                $documentoId = $documento['id'];
                            @endphp
                            <button @click="activeDocument = {{ json_encode($documento) }}; showMapInfo = false"
                                    class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 flex flex-col items-center justify-center rounded-lg transition-all duration-300 focus:outline-none transform hover:scale-105 border-2"
                                    :class="{
                                        'bg-[#9d2449] text-white border-[#9d2449]': activeDocument && activeDocument.id === {{ $documentoId }},
                                        'bg-green-100 text-green-600 border-green-200': !activeDocument || activeDocument.id !== {{ $documentoId }} && '{{$estado}}' === 'Aprobado',
                                        'bg-red-100 text-red-600 border-red-200': !activeDocument || activeDocument.id !== {{ $documentoId }} && '{{$estado}}' === 'Rechazado',
                                        'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200': !activeDocument || activeDocument.id !== {{ $documentoId }} && !['Aprobado', 'Rechazado'].includes('{{$estado}}')
                                    }"
                                    title="{{ $documento['nombre'] }}">
                                @if($estado === 'Aprobado')
                                    <i class="fas fa-file-circle-check text-sm sm:text-lg md:text-xl"></i>
                                @elseif($estado === 'Rechazado')
                                    <i class="fas fa-file-circle-xmark text-sm sm:text-lg md:text-xl"></i>
                                @else
                                    <i class="fas fa-file-circle-question text-sm sm:text-lg md:text-xl"></i>
                                @endif
                                <span class="text-xs mt-1 hidden md:block truncate w-full text-center">Doc</span>
                            </button>
                        @endforeach
                    @endif
                </div>
                
                <!-- Document Name Display -->
                <div class="mt-2 px-2">
                    <template x-if="activeDocument">
                        <div class="flex items-center justify-between">
                            <p class="text-xs sm:text-sm font-medium text-gray-700 truncate" x-text="activeDocument.nombre"></p>
                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full"
                                  :class="{
                                      'bg-green-100 text-green-800': activeDocument.estado === 'Aprobado',
                                      'bg-red-100 text-red-800': activeDocument.estado === 'Rechazado',
                                      'bg-gray-100 text-gray-700': !['Aprobado', 'Rechazado'].includes(activeDocument.estado)
                                  }"
                                  x-text="activeDocument.estado || 'Pendiente'"></span>
                        </div>
                    </template>
                    <template x-if="showMapInfo">
                        <p class="text-xs sm:text-sm font-medium text-gray-700">📍 Ubicación del Domicilio</p>
                    </template>
                </div>
            </div>

            <!-- Document Content Area -->
            <div class="p-0 mobile-viewer flex flex-col">
                <!-- Google Maps Link for Mobile -->
                <template x-if="showMapInfo">
                    <div class="p-3 sm:p-4">
                        <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 pt-1">
                                    <i class="fas fa-map-marked-alt text-gray-400 text-2xl"></i>
                                </div>
                                <div class="ml-4 flex-grow">
                                    <h3 class="text-base font-semibold text-gray-800">Ver en Google Maps</h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Se abrirá una nueva pestaña para revisar la ubicación.
                                    </p>
                                    <div class="mt-3">
                                        <p class="text-xs font-mono bg-gray-100 p-2 rounded-md border border-gray-200 text-gray-700">{{ $domicilioConcatenado }}</p>
                                    </div>
                                    <div class="mt-4">
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($domicilioConcatenado) }}"
                                           target="_blank"
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                           <i class="fas fa-external-link-alt mr-2"></i>
                                            Abrir Mapa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Document Viewer Mobile -->
                <template x-if="activeDocument && activeDocument.ruta_archivo">
                    <div class="bg-white rounded-lg shadow-sm border h-full flex flex-col m-3 sm:m-4">
                        <div class="p-2 sm:p-3 border-b border-gray-100 flex justify-between items-center bg-white rounded-t-lg">
                            <h4 class="text-xs sm:text-sm font-medium text-gray-700 truncate pr-2" x-text="activeDocument.nombre"></h4>
                            <a :href="`{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => '0']) }}`.replace('/0', '/' + activeDocument.id) + '?inline=1'"
                               target="_blank"
                               class="inline-flex items-center gap-1 px-2 py-1 bg-[#9d2449] text-white text-xs font-medium rounded-md hover:bg-[#7a1d3a] transition-colors flex-shrink-0">
                                <i class="fas fa-external-link-alt text-xs"></i>
                                <span class="hidden xs:inline">Abrir</span>
                            </a>
                        </div>
                        <div class="flex-grow relative">
                            <iframe :key="activeDocument.id" 
                                    :src="`{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => '0']) }}`.replace('/0', '/' + activeDocument.id) + '?inline=1'" 
                                    width="100%" 
                                    height="100%" 
                                    class="bg-white rounded-b-lg"
                                    loading="lazy"></iframe>
                        </div>
                    </div>
                </template>
                
                <!-- No File State -->
                <template x-if="activeDocument && !activeDocument.ruta_archivo">
                    <div class="h-full flex items-center justify-center text-center bg-white rounded-lg shadow-inner border m-3 sm:m-4">
                        <div class="p-4 sm:p-6">
                            <i class="fas fa-file-circle-xmark text-2xl sm:text-3xl md:text-4xl text-gray-400"></i>
                            <p class="mt-4 text-sm sm:text-base text-gray-600">No hay un archivo adjunto para este documento.</p>
                            <p class="mt-1 text-xs sm:text-sm text-gray-500" x-text="activeDocument.nombre"></p>
                        </div>
                    </div>
                </template>
                
                <!-- Default State -->
                <template x-if="!activeDocument && !showMapInfo">
                    <div class="h-full flex items-center justify-center text-center bg-white rounded-lg shadow-inner border m-3 sm:m-4">
                        <div class="p-4 sm:p-6">
                            <i class="fas fa-hand-pointer text-2xl sm:text-3xl md:text-4xl text-gray-400"></i>
                            <p class="mt-4 text-sm sm:text-base text-gray-600">Seleccione un documento para visualizarlo.</p>
                        </div>
                    </div>
                </template>
                
                <!-- No Documents State -->
                @if(empty($documentos) && $seccion !== 'domicilio')
                <div class="h-full flex items-center justify-center text-center bg-white rounded-lg shadow-inner border m-3 sm:m-4">
                    <div class="p-4 sm:p-6">
                        <i class="fas fa-folder-open text-2xl sm:text-3xl md:text-4xl text-gray-400"></i>
                        <p class="mt-4 text-sm sm:text-base text-gray-600">No hay documentos en esta sección.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Google Maps para Trámites -->
<script>
// Configurar dirección del trámite para Google Maps
window.tramiteAddress = `{{ $domicilioConcatenado }}`;
</script>
<script src="{{ asset('js/components/google-maps-tramite.js') }}" defer></script>