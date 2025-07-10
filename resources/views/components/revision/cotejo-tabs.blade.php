@props(['seccion' => '', 'tramite'])

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>

<!-- Desktop Split-Pane (md and up) -->
<div x-data="{
        leftWidth: 50,
        isDragging: false,
        documentVisible: true,
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
        }
    }" class="hidden md:flex w-full border border-gray-200 rounded-lg overflow-hidden bg-white">
    <!-- Left Panel -->
    <div class="relative transition-all duration-300"
         :style="{ width: documentVisible ? leftWidth + '%' : '100%' }">
        <div class="sticky top-0 bg-white/80 backdrop-blur-sm p-4 lg:p-6 border-b border-gray-200 z-10 flex justify-between items-center">
            <h3 class="text-base lg:text-lg font-bold text-gray-800">
                <i class="fas fa-file-invoice mr-2"></i> Datos del Formulario
            </h3>
            <button @click="documentVisible = !documentVisible"
                    class="text-sm font-medium text-[#9d2449] hover:text-[#7a1d3a] px-3 py-1 bg-rose-50 rounded-lg flex items-center">
                <span x-show="documentVisible" class="flex items-center"><i class="fas fa-eye-slash mr-2"></i>Ocultar Documentos</span>
                <span x-show="!documentVisible" class="flex items-center"><i class="fas fa-eye mr-2"></i>Mostrar Documentos</span>
            </button>
        </div>
        <div class="px-4 lg:px-6 pb-6 overflow-y-auto">
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
         class="w-1.5 flex-shrink-0 bg-gray-200 hover:bg-[#9d2449] cursor-col-resize transition-colors duration-200"></div>

    <!-- Right Panel -->
    <div x-show="documentVisible" x-transition class="flex-1 flex flex-col min-w-0"
         x-data="{ 
             activeDocument: @if($seccion === 'domicilio') null @else {{ !empty($documentos) ? json_encode($documentos[0]) : 'null' }} @endif,
             showMapInfo: false
         }">
        
        <!-- Document Selector: Horizontal Icon Bar -->
        <div class="flex-shrink-0 bg-white p-3 border-b border-gray-200">
            <div class="flex items-center gap-3 overflow-x-auto h-12 no-scrollbar px-1">
                
                <!-- Static Map Button -->
                @if ($seccion === 'domicilio')
                <button @click="showMapInfo = true"
                        class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-300 focus:outline-none transform hover:scale-110 bg-gray-100 text-gray-500 hover:bg-gray-200"
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
                                }">
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
                    <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-white to-gray-50 rounded-t-lg">
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
                    <div class="flex-grow relative">
                        <div id="google-map" class="w-full h-full rounded-b-lg"></div>
                    </div>
                </div>
            </template>
            
            <template x-if="activeDocument && activeDocument.ruta_archivo">
                <div class="w-full h-full bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col">
                    <!-- Viewer Header -->
                    <div class="p-3 border-b border-slate-100 flex justify-between items-center bg-white rounded-t-xl">
                        <h4 class="text-md font-medium text-slate-700 truncate pr-4" x-text="activeDocument.nombre"></h4>
                        <a :href="`{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => '0']) }}`.replace('/0', '/' + activeDocument.id)"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#9d2449] text-white text-xs font-medium rounded-md hover:bg-[#7a1d3a] transition-colors flex-shrink-0">
                            <i class="fas fa-external-link-alt text-sm"></i>
                            <span>Abrir</span>
                        </a>
                    </div>
                    <!-- Viewer Iframe -->
                    <div class="flex-grow relative">
                        <iframe :key="activeDocument.id" :src="`{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => '0']) }}`.replace('/0', '/' + activeDocument.id)" width="100%" height="100%" class="bg-white"></iframe>
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

<!-- Mobile Tabs (below md) -->
<div x-data="{ activeTab: 'form' }" class="md:hidden w-full border border-gray-200 rounded-lg overflow-hidden bg-white">
    <!-- Tabs -->
    <div class="border-b border-gray-200 bg-gray-50">
        <nav class="flex">
            @if($formulario)
            <button @click="activeTab='form'" :class="activeTab==='form' ? 'text-[#9d2449] border-b-2 border-[#9d2449]' : 'text-gray-600 hover:text-gray-800'" class="w-full py-3 text-sm font-medium flex items-center justify-center gap-2">
                <i class="fas fa-file-invoice"></i><span>Formulario</span>
            </button>
            @endif
            @if(!empty($documentos))
            <button @click="activeTab='doc'" :class="activeTab==='doc' ? 'text-[#9d2449] border-b-2 border-[#9d2449]' : 'text-gray-600 hover:text-gray-800'" class="w-full py-3 text-sm font-medium flex items-center justify-center gap-2">
                <i class="fas fa-folder-open"></i><span>Documentos</span>
            </button>
            @endif
        </nav>
    </div>
    <!-- Content -->
    <div>
        <!-- Formulario tab -->
        <div x-show="activeTab==='form'" class="p-4 bg-white">
            @if($formulario)
                <div class="prose max-w-none">
                    {!! $formulario !!}
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-3xl"></i>
                    <p class="mt-2">No hay datos de formulario.</p>
                </div>
            @endif
        </div>
        <!-- Documentos tab -->
        <div x-show="activeTab==='doc'" class="bg-gray-50" 
             x-data="{ 
                 activeDocument: {{ !empty($documentos) ? json_encode($documentos[0]) : 'null' }},
                 showMapInfo: false
             }">
            @if(!empty($documentos))
                <!-- Icon Selector -->
                <div class="p-2 border-b bg-white relative">
                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                        @if ($seccion === 'domicilio')
                        <button @click="showMapInfo = true; activeDocument = null"
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-300 focus:outline-none transform hover:scale-110 bg-gray-100 text-gray-500 hover:bg-gray-200"
                                title="Ver Geolocalización">
                            <i class="fas fa-map-location-dot text-xl"></i>
                        </button>
                        @endif

                        @foreach($documentos as $documento)
                            @php
                                $estado = $documento['estado'] ?? 'Pendiente';
                            @endphp
                            <button @click="activeDocument = {{ json_encode($documento) }}; showMapInfo = false"
                                    class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-300 focus:outline-none transform hover:scale-110
                                        @if($estado === 'Aprobado') bg-green-100 text-green-600
                                        @elseif($estado === 'Rechazado') bg-red-100 text-red-600
                                        @else bg-gray-100 text-gray-500 hover:bg-gray-200
                                        @endif">
                                @if($estado === 'Aprobado')
                                    <i class="fas fa-file-circle-check text-xl"></i>
                                @elseif($estado === 'Rechazado')
                                    <i class="fas fa-file-circle-xmark text-xl"></i>
                                @else
                                    <i class="fas fa-file-circle-question text-xl"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Document Content -->
                <div class="p-4">
                    <!-- Google Maps Viewer Mobile -->
                    <template x-if="showMapInfo">
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-white to-gray-50">
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
                            <div class="h-64">
                                <div id="google-map-mobile" class="w-full h-full"></div>
                            </div>
                        </div>
                    </template>

                    <template x-if="activeDocument">
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="p-3 border-b flex justify-between items-center">
                                <h4 class="font-medium text-gray-700" x-text="activeDocument.nombre"></h4>
                                <a :href="`{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => '0']) }}`.replace('/0', '/' + activeDocument.id)"
                                   target="_blank"
                                   class="px-3 py-1 bg-[#9d2449] text-white text-xs font-medium rounded-md hover:bg-[#7a1d3a]">
                                    Ver Documento
                                </a>
                            </div>
                            <div class="p-4" x-show="!activeDocument.ruta_archivo">
                                <p class="text-center text-gray-500">No hay archivo adjunto para este documento.</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="!activeDocument && !showMapInfo">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-hand-pointer text-3xl"></i>
                            <p class="mt-2">Seleccione un documento para visualizarlo.</p>
                        </div>
                    </template>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-folder-open text-3xl"></i>
                    <p class="mt-2">No hay documentos en esta sección.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Google Maps para Trámites -->
<script>
// Configurar dirección del trámite para Google Maps
window.tramiteAddress = `{{ $domicilioConcatenado }}`;
</script>
<script src="{{ asset('js/components/google-maps-tramite.js') }}" defer></script>