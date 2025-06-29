@extends('layouts.app')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8 bg-gray-50 min-h-screen">
    <!-- Título del Trámite de Revisión -->
    <div class="max-w-5xl mx-auto mb-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 backdrop-blur-lg border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <!-- Información del Trámite -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('revision.index') }}" 
                       class="flex items-center justify-center w-12 h-12 bg-gray-100 text-gray-600 rounded-xl hover:bg-[#B4325E] hover:text-white transition-all duration-300 hover:scale-105 hover:shadow-lg">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                        <i class="fas fa-file-search text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Revisión de Trámite #{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $tramite->solicitante->razon_social ?? $tramite->solicitante->nombre_completo ?? 'Sin información' }}
                        </p>
                    </div>
                </div>
                
                <!-- Estado y Acciones -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium shadow-sm {{ 
                        $tramite->estado == 'Pendiente' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 
                        ($tramite->estado == 'En Revision' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 
                        ($tramite->estado == 'Por Cotejar' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-gray-50 text-gray-700 border border-gray-200')) 
                    }}">
                        <i class="fas fa-clock mr-2"></i>
                        {{ $tramite->estado }}
                    </span>
                    
                    <div class="flex items-center space-x-3">
                        <form method="POST" action="{{ route('revision.aprobar-todo', $tramite->id) }}" style="display: inline;" 
                              onsubmit="return confirm('¿Está seguro de que desea aprobar todo el trámite?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl text-sm font-medium hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                                <i class="fas fa-check-double mr-2"></i>
                                Aprobar Todo
                            </button>
                        </form>
                        
                        <button type="button" onclick="document.getElementById('modalRechazarTodo').classList.remove('hidden')" 
                                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl text-sm font-medium hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                            <i class="fas fa-times mr-2"></i>
                            Rechazar Todo
                        </button>
                    </div>
                </div>
            </div>
        </div>
                        </div>

    <!-- Panel de Resumen de Revisión -->
    <div class="max-w-5xl mx-auto mt-4 mb-6">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 rounded-2xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-blue-600 mr-3"></i>
                Resumen de Revisión
            </h3>
            
            <div x-data="resumenRevision()">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div onclick="filtrarPorEstado('aprobado')" 
                         class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm summary-card tooltip" 
                         data-tooltip="Clic para ver secciones aprobadas">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Aprobadas</p>
                                <p class="text-2xl font-bold text-green-500" x-text="aprobadas">0</p>
                            </div>
                            <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                        </div>
                    </div>
                    
                    <div onclick="filtrarPorEstado('rechazado')" 
                         class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm summary-card tooltip" 
                         data-tooltip="Clic para ver secciones rechazadas">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Rechazadas</p>
                                <p class="text-2xl font-bold text-rose-500" x-text="rechazadas">0</p>
                            </div>
                            <i class="fas fa-times-circle text-rose-400 text-2xl"></i>
                        </div>
                    </div>
                    
                    <div onclick="irASiguientePendiente()" 
                         class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm summary-card tooltip" 
                         data-tooltip="Clic para ir a la siguiente sección pendiente">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Pendientes</p>
                                <p class="text-2xl font-bold text-amber-500" x-text="pendientes">0</p>
                            </div>
                            <i class="fas fa-clock text-amber-400 text-2xl"></i>
                        </div>
                    </div>
                    
                    <div onclick="mostrarDetalleProgreso()" 
                         class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm summary-card tooltip" 
                         data-tooltip="Clic para ver detalle del progreso">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Progreso</p>
                                <p class="text-2xl font-bold text-blue-500" x-text="progreso + '%'">0%</p>
                            </div>
                            <i class="fas fa-chart-bar text-blue-400 text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Barra de progreso visual -->
                <div class="w-full bg-gray-200 rounded-full h-3 mb-4 progress-bar">
                    <div class="bg-gradient-to-r from-blue-400 to-blue-500 h-3 rounded-full transition-all duration-500" 
                         :style="'width: ' + progreso + '%'"></div>
                </div>
            </div>
            
            <!-- Acciones rápidas -->
            <div class="flex flex-wrap gap-2">
                <button onclick="irASiguientePendiente()" 
                        class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium tooltip" 
                        data-tooltip="Navega automáticamente a la siguiente sección pendiente">
                    <i class="fas fa-arrow-right mr-2"></i>Ir a Siguiente Pendiente
                </button>
                <button id="toggleVerTodo" onclick="toggleExpandirTodo()" 
                        class="inline-flex items-center px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium tooltip" 
                        data-tooltip="Alterna entre ver todas las secciones o una por una">
                    <i id="iconVerTodo" class="fas fa-expand-arrows-alt mr-2"></i>
                    <span id="textVerTodo">Ver Todo</span>
                </button>
                <button onclick="mostrarResumenComentarios()" 
                        class="inline-flex items-center px-3 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors text-sm font-medium tooltip" 
                        data-tooltip="Muestra un resumen de todos los comentarios">
                    <i class="fas fa-comments mr-2"></i>Ver Comentarios
                </button>
            </div>
        </div>
                        </div>

    <!-- Form Container de Revisión -->
    <div class="max-w-5xl mx-auto mt-4 sm:mt-8 bg-white rounded-2xl shadow-xl p-3 sm:p-4 md:p-8 relative z-10"
         x-data="{ 
            currentStep: 1,
            totalSteps: 0,
            tipoPersona: '{{ $tramite->solicitante->tipo_persona ?? 'Física' }}',
            isPersonaFisica: '{{ $tramite->solicitante->tipo_persona ?? 'Física' }}' === 'Física',
            tramiteId: '{{ $tramite->id }}',
            steps: [],
            init() {
                this.totalSteps = this.isPersonaFisica ? 3 : 6;
                this.steps = this.isPersonaFisica ? 
                    [
                        {number: '01', label: 'Datos Generales', icon: 'fas fa-user-circle'},
                        {number: '02', label: 'Domicilio', icon: 'fas fa-map-marker-alt'},
                        {number: '03', label: 'Documentos', icon: 'fas fa-folder'}
                    ] : 
                    [
                        {number: '01', label: 'Datos Generales', icon: 'fas fa-user-circle'},
                        {number: '02', label: 'Domicilio', icon: 'fas fa-map-marker-alt'},
                        {number: '03', label: 'Constitución', icon: 'fas fa-building'},
                        {number: '04', label: 'Accionistas', icon: 'fas fa-users'},
                        {number: '05', label: 'Apoderado Legal', icon: 'fas fa-user-tie'},
                        {number: '06', label: 'Documentos', icon: 'fas fa-folder'}
                    ];
            },
            nextStep() {
                if (this.currentStep < this.totalSteps) {
                    this.currentStep++;
                }
            },
            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                }
            },
            goToStep(step) {
                if (step >= 1 && step <= this.totalSteps) {
                    this.currentStep = step;
                }
            }
         }">
         
        <!-- Progress Indicator -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white shadow-lg">
                <span class="text-xl font-bold" x-text="currentStep">1</span>
                <span class="text-sm">/</span>
                <span class="text-lg" x-text="totalSteps">3</span>
                        </div>
            <div class="mt-3 text-sm text-gray-600 font-medium" x-text="steps[currentStep - 1]?.label || ''">Datos Generales</div>
            <div class="mt-1 text-xs text-gray-500">
                Sección <span x-text="currentStep">1</span> de <span x-text="totalSteps">3</span>
                    </div>
                    </div>

        <!-- Desktop Progress Container -->
        <div class="hidden md:block mb-8">
            <div class="flex items-center justify-center space-x-4">
                <template x-for="(step, index) in steps" :key="step.number">
                    <div class="flex items-center">
                        <button @click="goToStep(index + 1)" 
                                class="relative flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300 transform hover:scale-110"
                                :class="currentStep === (index + 1) ? 'bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white shadow-lg' : 
                                        currentStep > (index + 1) ? 'bg-emerald-500 text-white shadow-md' : 'bg-gray-200 text-gray-500'">
                            <i :class="step.icon" class="text-sm"></i>
                        </button>
                        <div x-show="index < steps.length - 1" class="w-16 h-0.5 mx-2"
                             :class="currentStep > (index + 1) ? 'bg-emerald-500' : 'bg-gray-300'"></div>
                        </div>
                </template>
                    </div>
            <div class="flex items-center justify-center mt-3">
                <template x-for="(step, index) in steps" :key="step.number">
                    <div class="flex items-center">
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-600" x-text="step.number"></div>
                            <div class="text-xs text-gray-500 mt-1" x-text="step.label"></div>
                    </div>
                        <div x-show="index < steps.length - 1" class="w-16"></div>
                </div>
                </template>
            </div>
        </div>

        <!-- Contenido de las Secciones -->
        <div class="max-w-4xl mx-auto">
            <!-- 01. Datos Generales -->
            <div x-show="currentStep === 1" x-cloak class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-8 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-12 h-12 flex items-center justify-center text-base font-bold mr-4">01</span>
                        <div class="flex items-center">
                                <i class="fas fa-user-circle mr-3 text-2xl"></i>
                                <span>Datos Generales</span>
                            </div>
                                </h2>
                        
                        <div class="flex items-center space-x-4">
                            @php
                                $estado = ($revisionesExistentes[1] ?? [])['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-3 py-2 rounded-full border text-sm font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-2"></i>
                                <span>{{ ucfirst($estado) }}</span>
                                </div>
                        </div>
                    </div>
                </div>
                
                    <div class="p-8">
                        @include('components.formularios.seccion-datos-generales', [
                            'datosTramite' => $datosTramite ?? [],
                            'datosSolicitante' => $datosSolicitante ?? [],
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                Revisión
                            </h4>
                            
                            <div class="space-y-6">
                                <!-- Comentario existente (si hay) -->
                                @if(!empty(($revisionesExistentes[1] ?? [])['comentario']))
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-comment-dots text-blue-400 mt-1 mr-3"></i>
                                        <div>
                                            <h5 class="text-sm font-medium text-blue-800 mb-1">Comentario anterior:</h5>
                                            <p class="text-sm text-blue-700">{{ ($revisionesExistentes[1] ?? [])['comentario'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Etiquetas rápidas para problemas comunes -->
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <h5 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-tags text-gray-500 mr-2"></i>
                                        Problemas comunes (clic para agregar):
                                    </h5>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" onclick="agregarEtiqueta(1, 'Falta información requerida')" 
                                                class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs hover:bg-yellow-200 transition-all duration-200 tag-hover tooltip" 
                                                data-tooltip="Clic para agregar al comentario">
                                            📝 Información incompleta
                                        </button>
                                        <button type="button" onclick="agregarEtiqueta(1, 'Documentos no legibles')" 
                                                class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs hover:bg-red-200 transition-all duration-200 tag-hover tooltip" 
                                                data-tooltip="Clic para agregar al comentario">
                                            🔍 Documentos no legibles
                                        </button>
                                        <button type="button" onclick="agregarEtiqueta(1, 'Datos inconsistentes')" 
                                                class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs hover:bg-orange-200 transition-all duration-200 tag-hover tooltip" 
                                                data-tooltip="Clic para agregar al comentario">
                                            ⚠️ Datos inconsistentes
                                        </button>
                                        <button type="button" onclick="agregarEtiqueta(1, 'Requiere verificación adicional')" 
                                                class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs hover:bg-purple-200 transition-all duration-200 tag-hover tooltip" 
                                                data-tooltip="Clic para agregar al comentario">
                                            🔎 Verificación adicional
                                        </button>
                                            </div>
                                </div>

                                <!-- Campo de comentario con diseño moderno -->
                                <div class="py-3 px-4 bg-white rounded-lg border border-gray-200 shadow-sm relative">
                                    <label for="comentario_seccion_1" class="sr-only">Comentario de revisión</label>
                                    <textarea id="comentario_seccion_1" rows="4"
                                        class="px-0 w-full text-sm text-gray-700 border-0 focus:ring-0 focus:outline-none bg-white resize-none placeholder-gray-400"
                                        placeholder="💬 Escriba sus comentarios o motivo de la decisión... (Ctrl+Enter para enviar)"
                                        oninput="actualizarContadorSeccion(1, this.value.length)">{{ ($revisionesExistentes[1] ?? [])['comentario'] ?? '' }}</textarea>
                                    
                                    <!-- Contador de caracteres -->
                                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                        <span id="contador_seccion_1">{{ strlen(($revisionesExistentes[1] ?? [])['comentario'] ?? '') }}</span>/500
                                    </div>
                                </div>
                                
                                <!-- Botones de acción mejorados -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button onclick="enviarRevision(1, 'aprobar')" 
                                            class="flex-1 inline-flex items-center justify-center py-3 px-4 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-500 rounded-lg focus:ring-4 focus:ring-green-100 hover:from-green-500 hover:to-green-600 transition-all duration-150 shadow-sm hover:shadow-md tooltip" 
                                            data-tooltip="Aprobar esta sección (Ctrl+A)">
                                        <i class="fas fa-check mr-2"></i>✅ Aprobar Sección
                                        <span class="ml-2 text-xs opacity-75">(Ctrl+A)</span>
                                    </button>
                                    <button onclick="enviarRevision(1, 'rechazar')" 
                                            class="flex-1 inline-flex items-center justify-center py-3 px-4 text-sm font-medium text-white bg-gradient-to-r from-rose-400 to-rose-500 rounded-lg focus:ring-4 focus:ring-rose-100 hover:from-rose-500 hover:to-rose-600 transition-all duration-150 shadow-sm hover:shadow-md tooltip" 
                                            data-tooltip="Rechazar esta sección (Ctrl+R)">
                                        <i class="fas fa-times mr-2"></i>❌ Rechazar Sección
                                        <span class="ml-2 text-xs opacity-75">(Ctrl+R)</span>
                                        </button>
                                    </div>

                                <!-- Indicador de progreso de sección -->
                                <div class="flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-gray-100">
                                    <span>Sección 1 de {{ $tramite->solicitante->tipo_persona === 'Física' ? '3' : '6' }}</span>
                                    <span>⏱️ Tiempo promedio: 3-5 min</span>
                                </div>
                                                        </div>
                                                    </div>
                    </div>
                </div>
            </div>

            <!-- 02. Domicilio -->
            <div x-show="currentStep === 2" x-cloak class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-8 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-12 h-12 flex items-center justify-center text-base font-bold mr-4">02</span>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-3 text-2xl"></i>
                                <span>Domicilio</span>
                            </div>
                        </h2>
                        
                        <div class="flex items-center space-x-4">
                            @php
                                $estado = ($revisionesExistentes[2] ?? [])['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-3 py-2 rounded-full border text-sm font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-2"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                            
                                <button onclick="mostrarMapa('domicilio')"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg text-sm transition-colors flex items-center">
                                <i class="fas fa-map mr-2"></i>Ver Mapa
                                </button>
                        </div>
                    </div>
                </div>
                
                <div id="contenido-domicilio" class="p-8">
                        @include('components.formularios.seccion-domicilio', [
                            'datosDomicilio' => $datosDomicilio ?? [],
                            'datosSAT' => $datosSAT ?? [],
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                                <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="space-y-6">
                                <!-- Campo de comentario con diseño moderno -->
                                <div class="py-3 px-4 bg-white rounded-lg border border-gray-200 shadow-sm relative">
                                    <label for="comentario_seccion_2" class="sr-only">Comentario de revisión</label>
                                    <textarea id="comentario_seccion_2" rows="4"
                                        class="px-0 w-full text-sm text-gray-700 border-0 focus:ring-0 focus:outline-none bg-white resize-none placeholder-gray-400"
                                        placeholder="Escriba sus comentarios o motivo de la decisión...">{{ ($revisionesExistentes[2] ?? [])['comentario'] ?? '' }}</textarea>
                                        </div>
                                
                                <!-- Botones de acción -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button onclick="enviarRevision(2, 'aprobar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-500 rounded-lg focus:ring-4 focus:ring-green-100 hover:from-green-500 hover:to-green-600 transition-colors duration-150">
                                        <i class="fas fa-check mr-2"></i>Aprobar Sección
                                    </button>
                                    <button onclick="enviarRevision(2, 'rechazar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-rose-400 to-rose-500 rounded-lg focus:ring-4 focus:ring-rose-100 hover:from-rose-500 hover:to-rose-600 transition-colors duration-150">
                                        <i class="fas fa-times mr-2"></i>Rechazar Sección
                                            </button>
                                        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 03. Constitución (Solo Persona Moral) -->
            <div x-show="currentStep === 3 && !isPersonaFisica" x-cloak class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-8 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-12 h-12 flex items-center justify-center text-base font-bold mr-4">03</span>
                            <div class="flex items-center">
                                <i class="fas fa-building mr-3 text-2xl"></i>
                                <span>Constitución</span>
                            </div>
                        </h2>
                        
                        <div class="flex items-center space-x-4">
                            @php
                                $estado = ($revisionesExistentes[3] ?? [])['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-3 py-2 rounded-full border text-sm font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-2"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                        @include('components.formularios.seccion-constitucion', [
                            'datosConstitucion' => $datosConstitucion ?? [],
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="space-y-6">
                                <!-- Campo de comentario con diseño moderno -->
                                <div class="py-3 px-4 bg-white rounded-lg border border-gray-200 shadow-sm relative">
                                    <label for="comentario_seccion_3" class="sr-only">Comentario de revisión</label>
                                    <textarea id="comentario_seccion_3" rows="4"
                                        class="px-0 w-full text-sm text-gray-700 border-0 focus:ring-0 focus:outline-none bg-white resize-none placeholder-gray-400"
                                        placeholder="Escriba sus comentarios o motivo de la decisión...">{{ ($revisionesExistentes[3] ?? [])['comentario'] ?? '' }}</textarea>
                                        </div>
                                
                                <!-- Botones de acción -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button onclick="enviarRevision(3, 'aprobar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-500 rounded-lg focus:ring-4 focus:ring-green-100 hover:from-green-500 hover:to-green-600 transition-colors duration-150">
                                        <i class="fas fa-check mr-2"></i>Aprobar Sección
                                    </button>
                                    <button onclick="enviarRevision(3, 'rechazar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-rose-400 to-rose-500 rounded-lg focus:ring-4 focus:ring-rose-100 hover:from-rose-500 hover:to-rose-600 transition-colors duration-150">
                                        <i class="fas fa-times mr-2"></i>Rechazar Sección
                                            </button>
                                        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 04. Accionistas (Solo Persona Moral) -->
            <div x-show="currentStep === 4 && !isPersonaFisica" x-cloak class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-8 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-12 h-12 flex items-center justify-center text-base font-bold mr-4">04</span>
                            <div class="flex items-center">
                                <i class="fas fa-users mr-3 text-2xl"></i>
                                <span>Accionistas</span>
                            </div>
                        </h2>
                        
                        <div class="flex items-center space-x-4">
                            @php
                                $estado = ($revisionesExistentes[4] ?? [])['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-3 py-2 rounded-full border text-sm font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-2"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                        @include('components.formularios.seccion-accionistas', [
                            'datosAccionistas' => $datosAccionistas ?? [],
                            'accionistas' => $accionistas ?? [],
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="space-y-6">
                                <!-- Campo de comentario con diseño moderno -->
                                <div class="py-3 px-4 bg-white rounded-lg border border-gray-200 shadow-sm relative">
                                    <label for="comentario_seccion_4" class="sr-only">Comentario de revisión</label>
                                    <textarea id="comentario_seccion_4" rows="4"
                                        class="px-0 w-full text-sm text-gray-700 border-0 focus:ring-0 focus:outline-none bg-white resize-none placeholder-gray-400"
                                        placeholder="Escriba sus comentarios o motivo de la decisión...">{{ ($revisionesExistentes[4] ?? [])['comentario'] ?? '' }}</textarea>
                                        </div>
                                
                                <!-- Botones de acción -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button onclick="enviarRevision(4, 'aprobar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-500 rounded-lg focus:ring-4 focus:ring-green-100 hover:from-green-500 hover:to-green-600 transition-colors duration-150">
                                        <i class="fas fa-check mr-2"></i>Aprobar Sección
                                    </button>
                                    <button onclick="enviarRevision(4, 'rechazar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-rose-400 to-rose-500 rounded-lg focus:ring-4 focus:ring-rose-100 hover:from-rose-500 hover:to-rose-600 transition-colors duration-150">
                                        <i class="fas fa-times mr-2"></i>Rechazar Sección
                                            </button>
                                        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 05. Apoderado Legal (Solo Persona Moral) -->
            <div x-show="currentStep === 5 && !isPersonaFisica" x-cloak class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-8 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-12 h-12 flex items-center justify-center text-base font-bold mr-4">05</span>
                            <div class="flex items-center">
                                <i class="fas fa-user-tie mr-3 text-2xl"></i>
                                <span>Apoderado Legal</span>
                            </div>
                        </h2>
                        
                        <div class="flex items-center space-x-4">
                            @php
                                $estado = ($revisionesExistentes[5] ?? [])['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-3 py-2 rounded-full border text-sm font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-2"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                        @include('components.formularios.seccion-apoderado', [
                            'datosApoderado' => $datosApoderado ?? [],
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="space-y-6">
                                <!-- Campo de comentario con diseño moderno -->
                                <div class="py-3 px-4 bg-white rounded-lg border border-gray-200 shadow-sm relative">
                                    <label for="comentario_seccion_5" class="sr-only">Comentario de revisión</label>
                                    <textarea id="comentario_seccion_5" rows="4"
                                        class="px-0 w-full text-sm text-gray-700 border-0 focus:ring-0 focus:outline-none bg-white resize-none placeholder-gray-400"
                                        placeholder="Escriba sus comentarios o motivo de la decisión...">{{ ($revisionesExistentes[5] ?? [])['comentario'] ?? '' }}</textarea>
                                        </div>
                                
                                <!-- Botones de acción -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button onclick="enviarRevision(5, 'aprobar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-500 rounded-lg focus:ring-4 focus:ring-green-100 hover:from-green-500 hover:to-green-600 transition-colors duration-150">
                                        <i class="fas fa-check mr-2"></i>Aprobar Sección
                                    </button>
                                    <button onclick="enviarRevision(5, 'rechazar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-rose-400 to-rose-500 rounded-lg focus:ring-4 focus:ring-rose-100 hover:from-rose-500 hover:to-rose-600 transition-colors duration-150">
                                        <i class="fas fa-times mr-2"></i>Rechazar Sección
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Documentos - Para Persona Física en paso 3, para Moral en paso 6 -->
            <div x-show="(isPersonaFisica && currentStep === 3) || (!isPersonaFisica && currentStep === 6)" x-cloak class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-8 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-12 h-12 flex items-center justify-center text-base font-bold mr-4" x-text="isPersonaFisica ? '03' : '06'">03</span>
                            <div class="flex items-center">
                                <i class="fas fa-folder mr-3 text-2xl"></i>
                                <span>Documentos</span>
                            </div>
                        </h2>
                        
                        <div class="flex items-center space-x-4">
                            @php
                                $numSeccionDoc = $tramite->solicitante->tipo_persona === 'Física' ? 3 : 6;
                                $estado = ($revisionesExistentes[$numSeccionDoc] ?? [])['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-3 py-2 rounded-full border text-sm font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-2"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                        @include('components.formularios.seccion-documentos', [
                            'documentos' => $documentos ?? [],
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="space-y-6">
                                <!-- Campo de comentario con diseño moderno -->
                                <div class="py-3 px-4 bg-white rounded-lg border border-gray-200 shadow-sm relative">
                                    <label :for="'comentario_seccion_' + (isPersonaFisica ? 3 : 6)" class="sr-only">Comentario de revisión</label>
                                    <textarea :id="'comentario_seccion_' + (isPersonaFisica ? 3 : 6)" rows="4"
                                        class="px-0 w-full text-sm text-gray-700 border-0 focus:ring-0 focus:outline-none bg-white resize-none placeholder-gray-400"
                                        placeholder="Escriba sus comentarios o motivo de la decisión...">{{ ($revisionesExistentes[$numSeccionDoc] ?? [])['comentario'] ?? '' }}</textarea>
                                        </div>
                                
                                <!-- Botones de acción -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button @click="enviarRevision(isPersonaFisica ? 3 : 6, 'aprobar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-green-400 to-green-500 rounded-lg focus:ring-4 focus:ring-green-100 hover:from-green-500 hover:to-green-600 transition-colors duration-150">
                                        <i class="fas fa-check mr-2"></i>Aprobar Sección
                                    </button>
                                    <button @click="enviarRevision(isPersonaFisica ? 3 : 6, 'rechazar')" 
                                            class="flex-1 inline-flex items-center justify-center py-2.5 px-4 text-sm font-medium text-white bg-gradient-to-r from-rose-400 to-rose-500 rounded-lg focus:ring-4 focus:ring-rose-100 hover:from-rose-500 hover:to-rose-600 transition-colors duration-150">
                                        <i class="fas fa-times mr-2"></i>Rechazar Sección
                                            </button>
                                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Navegación entre Secciones -->
            <div class="flex flex-col sm:flex-row justify-between gap-4 mt-8">
                <button @click="prevStep()" 
                        :disabled="currentStep === 1"
                        :class="currentStep === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105'"
                        class="flex items-center px-6 py-3 bg-gray-500 text-white rounded-xl transition-all duration-300 font-medium">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Anterior
                        </button>
                
                <button @click="nextStep()" 
                        :disabled="currentStep === totalSteps"
                        :class="currentStep === totalSteps ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105'"
                        class="flex items-center px-6 py-3 bg-[#9d2449] text-white rounded-xl transition-all duration-300 font-medium">
                    Siguiente
                    <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                </div>
            </div>
    </div>
</div>

<!-- Modal Rechazar Todo -->
<div id="modalRechazarTodo" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50">
    <div class="h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-2xl">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    Rechazar Todo el Trámite
                </h3>
            </div>
            
            <form method="POST" action="{{ route('revision.rechazar-todo', $tramite->id) }}" class="p-6">
                @csrf
                <div class="mb-6">
                    <p class="text-gray-700 mb-4">
                        ¿Está seguro de que desea rechazar todo el trámite? Esta acción no se puede deshacer.
                    </p>
                    <label for="comentario_rechazo_todo" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo del rechazo (requerido):
                    </label>
                    <textarea name="comentario" 
                              id="comentario_rechazo_todo"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-200 focus:border-red-400" 
                              rows="4" 
                              placeholder="Escriba el motivo del rechazo..."
                              required></textarea>
                </div>
                
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('modalRechazarTodo').classList.add('hidden')"
                            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Rechazar Todo
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Botón flotante para atajos de teclado -->
<div class="fixed bottom-6 right-6 z-50">
    <button onclick="mostrarAtajos()" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110 tooltip" 
            data-tooltip="Ver atajos de teclado">
        <i class="fas fa-keyboard text-lg"></i>
    </button>
</div>

<!-- Modal de atajos de teclado -->
<div id="modalAtajos" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50">
    <div class="h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-keyboard mr-3"></i>
                        Atajos de Teclado
                    </h3>
                    <button onclick="cerrarModalAtajos()" class="text-white/80 hover:text-white">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">Aprobar sección actual</span>
                        <kbd class="px-2 py-1 bg-gray-200 rounded text-sm font-mono">Ctrl + A</kbd>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">Rechazar sección actual</span>
                        <kbd class="px-2 py-1 bg-gray-200 rounded text-sm font-mono">Ctrl + R</kbd>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">Enviar desde textarea</span>
                        <kbd class="px-2 py-1 bg-gray-200 rounded text-sm font-mono">Ctrl + Enter</kbd>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">Ir a siguiente pendiente</span>
                        <kbd class="px-2 py-1 bg-gray-200 rounded text-sm font-mono">→</kbd>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">Cerrar modal</span>
                        <kbd class="px-2 py-1 bg-gray-200 rounded text-sm font-mono">Esc</kbd>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 text-center">
                        💡 <strong>Tip:</strong> Usa las etiquetas predefinidas para comentarios más rápidos
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
.min-h-screen {
    background: #f9fafb;
}

.transition-colors {
    transition: background-color 0.2s ease, color 0.2s ease;
}

.hover\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Animaciones para Alpine.js */
[x-cloak] {
    display: none !important;
}

.animate-shimmer {
    animation: shimmer 3s linear infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Mejoras para los formularios */
.form-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

/* Estilos para botones hover mejorados */
.hover\:scale-105:hover {
    transform: scale(1.05);
}

.hover\:scale-110:hover {
    transform: scale(1.1);
}

/* Gradientes mejorados */
.bg-gradient-to-br {
    background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

/* Animaciones para las notificaciones */
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

.animate-slide-in {
    animation: slideIn 0.3s ease-out;
}

.animate-slide-out {
    animation: slideOut 0.3s ease-in;
}

/* Efectos hover mejorados para las etiquetas */
.tag-hover:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Indicador de carga para botones */
.btn-loading {
    position: relative;
    pointer-events: none;
}

.btn-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Estilos para el resumen de revisión */
.summary-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Mejoras para la barra de progreso */
.progress-bar {
    position: relative;
    overflow: hidden;
}

.progress-bar::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Estilos para campos activos */
.field-active {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

/* Tooltip para atajos */
.tooltip {
    position: relative;
}

.tooltip::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 1000;
}

.tooltip:hover::after {
    opacity: 1;
    visibility: visible;
}
</style>
@endpush

@push('scripts')
<script>
// Cargar Google Maps solo cuando sea necesario y con manejo de errores
window.loadGoogleMaps = function() {
    if (window.google && window.google.maps) {
        return Promise.resolve();
    }
    
    return new Promise((resolve, reject) => {
        window.initGoogleMaps = function() {
            resolve();
        };
        
        const script = document.createElement('script');
        script.async = true;
        script.defer = true;
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCgXSEgnOeCKaE80Zc6ouGxxcHK61vZAR8&libraries=places&callback=initGoogleMaps';
        script.onerror = function() {
            reject(new Error('Google Maps no se pudo cargar'));
        };
        
        document.head.appendChild(script);
    });
};
</script>
<script src="{{ asset('js/components/document-viewer.js') }}" onerror=""></script>
<script src="{{ asset('js/components/map-handler.js') }}" onerror=""></script>
@endpush

<script>
// Manejo global de errores para evitar que interfieran con la funcionalidad
window.addEventListener('error', function(e) {
    // Capturar errores relacionados con Google Maps o Alpine.js sin interrumpir
    if (e.message && (e.message.includes('google') || e.message.includes('Alpine'))) {
        e.preventDefault();
        return true;
    }
});

// Manejo de promesas rechazadas
window.addEventListener('unhandledrejection', function(e) {
    e.preventDefault();
});

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Sistema de revisión inicializado
    
    // Verificar Alpine.js disponibilidad
    if (!window.Alpine) {
        // Alpine.js no disponible
    }
});

// Variable global para controlar el mapa abierto
window.mapaAbierto = null;

// Función para mostrar mapa (solo uno a la vez)
function mostrarMapa(seccion) {
    // Si ya hay un mapa abierto, cerrarlo primero
    if (window.mapaAbierto && window.mapaAbierto !== seccion) {
        cerrarMapa(window.mapaAbierto);
    }
    
    // Si ya está abierto este mapa, no hacer nada
    if (window.mapaAbierto === seccion) {
        return;
    }
    
    // Obtener el contenedor de la sección
    const contenedor = document.getElementById('contenido-' + seccion);
    if (!contenedor) {
        return;
    }
    
    // Guardar contenido original
    if (!contenedor.dataset.originalContent) {
        contenedor.dataset.originalContent = contenedor.innerHTML;
    }
    
    // Obtener el contenido actual del formulario
    const formularioActual = contenedor.dataset.originalContent;
    
    // Obtener datos de domicilio para construir la dirección
    const direccion = window.obtenerDireccionCompleta ? window.obtenerDireccionCompleta() : 'Dirección no disponible';
    
    // Crear el nuevo layout dividido
    const layoutDividido = document.createElement('div');
    layoutDividido.className = 'grid grid-cols-1 lg:grid-cols-2 gap-6';
    layoutDividido.setAttribute('data-map-layout', 'true');
    
    layoutDividido.innerHTML = `
        <div class="space-y-6">
            ${formularioActual}
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-500 mr-2 text-xl"></i>
                    <span class="text-lg font-semibold text-gray-700">Ubicación en Mapa</span>
            </div>
                <button onclick="cerrarMapa('${seccion}')" 
                        class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-sm transition-colors">
                    <i class="fas fa-times mr-1"></i>Cerrar Mapa
                </button>
            </div>
            <div id="mapa-${seccion}" class="w-full h-96 border border-gray-300 rounded-xl"></div>
        </div>
    `;
    
    // Reemplazar el contenido
    contenedor.innerHTML = '';
    contenedor.appendChild(layoutDividido);
    
    // Marcar este mapa como abierto
    window.mapaAbierto = seccion;
    
    // Inicializar el mapa después de que el DOM esté listo
    setTimeout(() => {
        // Cargar Google Maps solo cuando sea necesario
        if (window.loadGoogleMaps) {
            window.loadGoogleMaps().then(() => {
        if (window.mapHandler) {
            window.mapHandler.initializeMap(seccion, direccion);
        } else if (window.inicializarMapa) {
            window.inicializarMapa(seccion, direccion);
                }
            }).catch((error) => {
                // Mostrar mensaje alternativo en el contenedor del mapa
                const mapaContainer = document.getElementById(`mapa-${seccion}`);
                if (mapaContainer) {
                    mapaContainer.innerHTML = `
                        <div class="flex items-center justify-center h-full bg-gray-100 rounded-lg">
                            <div class="text-center text-gray-500">
                                <i class="fas fa-map-marked-alt text-4xl mb-2"></i>
                                <p>Mapa no disponible temporalmente</p>
                                <p class="text-sm">Dirección: ${direccion}</p>
                            </div>
                        </div>
                    `;
                }
            });
        }
    }, 100);
}

// Función para cerrar mapa
function cerrarMapa(seccion) {
    const contenedor = document.getElementById('contenido-' + seccion);
    if (!contenedor) return;
    
    // Restaurar contenido original
    if (contenedor.dataset.originalContent) {
        contenedor.innerHTML = contenedor.dataset.originalContent;
    }
    
    // Limpiar el mapa
    if (window.mapHandler) {
        window.mapHandler.cleanup();
    }
    
    // Marcar que no hay mapa abierto
    window.mapaAbierto = null;
}

// Función para mostrar documento (si existe el sistema de documentos)
function mostrarDocumento(seccion, ruta, nombre) {
    if (window.documentViewer) {
        window.documentViewer.showDocument(seccion, ruta, nombre);
    }
}

// JavaScript mínimo para cerrar modales al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.id === 'modalRechazarTodo') {
        document.getElementById('modalRechazarTodo').classList.add('hidden');
    }
});

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modalRechazo = document.getElementById('modalRechazarTodo');
        const modalAtajos = document.getElementById('modalAtajos');
        
        if (modalRechazo && !modalRechazo.classList.contains('hidden')) {
            modalRechazo.classList.add('hidden');
        }
        
        if (modalAtajos && !modalAtajos.classList.contains('hidden')) {
            cerrarModalAtajos();
        }
    }
});

// Función para enviar revisión con un solo campo de comentario
function enviarRevision(seccion, accion) {
    // Obtener el comentario de la sección correspondiente
    const comentarioElement = document.getElementById('comentario_seccion_' + seccion);
    if (!comentarioElement) {
        // Campo de comentario no encontrado
        return;
    }
    
    const comentario = comentarioElement.value.trim();
    
    // Validar si es rechazar y no hay comentario
    if (accion === 'rechazar' && comentario === '') {
        mostrarNotificacion('Por favor, proporcione un motivo para el rechazo.', 'warning');
        comentarioElement.focus();
        return;
    }
    
    // Mostrar indicador de carga
    const boton = event.target;
    const textoOriginal = boton.innerHTML;
    boton.disabled = true;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
    
    // Crear formulario dinámico
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = accion === 'aprobar' 
        ? `{{ route('revision.seccion.aprobar', [$tramite->id, '__SECCION__']) }}`.replace('__SECCION__', seccion)
        : `{{ route('revision.seccion.rechazar', [$tramite->id, '__SECCION__']) }}`.replace('__SECCION__', seccion);
    
    // Token CSRF
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Campo de comentario
    const comentarioInput = document.createElement('input');
    comentarioInput.type = 'hidden';
    comentarioInput.name = 'comentario';
    comentarioInput.value = comentario;
    form.appendChild(comentarioInput);
    
    // Agregar al DOM y enviar
    document.body.appendChild(form);
    form.submit();
}

// Función para agregar etiquetas predefinidas al comentario
function agregarEtiqueta(seccion, etiqueta) {
    const comentarioElement = document.getElementById('comentario_seccion_' + seccion);
    if (!comentarioElement) return;
    
    const comentarioActual = comentarioElement.value.trim();
    const nuevoComentario = comentarioActual 
        ? comentarioActual + '\n• ' + etiqueta
        : '• ' + etiqueta;
    
    comentarioElement.value = nuevoComentario;
    comentarioElement.focus();
    
    // Actualizar contador de caracteres
    actualizarContador(comentarioElement);
    
    // Mostrar notificación
    mostrarNotificacion('Etiqueta agregada al comentario', 'success');
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full`;
    
    // Estilos según tipo
    const estilos = {
        success: 'bg-emerald-500',
        warning: 'bg-amber-500', 
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    notificacion.classList.add(estilos[tipo] || estilos.info);
    notificacion.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${tipo === 'success' ? 'check' : tipo === 'warning' ? 'exclamation-triangle' : tipo === 'error' ? 'times' : 'info'} mr-2"></i>
            <span>${mensaje}</span>
        </div>
    `;
    
    document.body.appendChild(notificacion);
    
    // Animar entrada
    setTimeout(() => notificacion.classList.remove('translate-x-full'), 100);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notificacion.classList.add('translate-x-full');
        setTimeout(() => notificacion.remove(), 300);
    }, 3000);
}

// Función para datos del resumen de revisión
function resumenRevision() {
    return {
        aprobadas: {{ collect($revisionesExistentes)->where('estado', 'aprobado')->count() }},
        rechazadas: {{ collect($revisionesExistentes)->where('estado', 'rechazado')->count() }},
        pendientes: {{ ($tramite->solicitante->tipo_persona === 'Física' ? 3 : 6) - collect($revisionesExistentes)->whereIn('estado', ['aprobado', 'rechazado'])->count() }},
        get progreso() {
            const total = {{ $tramite->solicitante->tipo_persona === 'Física' ? 3 : 6 }};
            const completadas = this.aprobadas + this.rechazadas;
            return Math.round((completadas / total) * 100);
        }
    }
}

// Función para ir a la siguiente sección pendiente
function irASiguientePendiente() {
    const secciones = {{ $tramite->solicitante->tipo_persona === 'Física' ? '[1,2,3]' : '[1,2,3,4,5,6]' }};
    const revisionesExistentes = @json($revisionesExistentes);
    
    for (let seccion of secciones) {
        const revision = revisionesExistentes[seccion];
        if (!revision || revision.estado === 'pendiente') {
            // Navegar a esa sección
            if (typeof window.Alpine !== 'undefined') {
                window.Alpine.data('currentStep', seccion);
            }
            // Scroll suave a la sección
            setTimeout(() => {
                const elemento = document.querySelector(`[x-show*="${seccion}"]`);
                if (elemento) {
                    elemento.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
            break;
        }
    }
}

// Función mejorada para hacer toggle del expandir todo
function toggleExpandirTodo() {
    const container = document.querySelector('[x-data*="currentStep"]');
    const toggleBtn = document.getElementById('toggleVerTodo');
    const icon = document.getElementById('iconVerTodo');
    const text = document.getElementById('textVerTodo');
    
    if (!container || !toggleBtn || !icon || !text) {
        // No se pudieron encontrar los elementos necesarios para el toggle
        return;
    }
    
    // Verificar si ya está expandido
    const isExpanded = container.dataset.expandido === 'true';
    
    if (isExpanded) {
        // Contraer - volver al modo paso a paso
        container.dataset.expandido = 'false';
        text.textContent = 'Ver Todo';
        icon.className = 'fas fa-expand-arrows-alt mr-2';
        toggleBtn.setAttribute('data-tooltip', 'Muestra todas las secciones de una vez');
        
        // Limpiar estilos forzados y restaurar Alpine.js
        const sections = container.querySelectorAll('[x-show*="currentStep"]');
        sections.forEach(section => {
            section.removeAttribute('style');
            section.style.cssText = '';
        });
        
        // Resetear al paso 1 de forma más simple
        setTimeout(() => {
            try {
                // Intentar usar Alpine.js de manera segura
                if (window.Alpine && container && container.__x) {
                    const alpineComponent = container.__x;
                    if (alpineComponent && alpineComponent.$data && typeof alpineComponent.$data.currentStep !== 'undefined') {
                        alpineComponent.$data.currentStep = 1;
                    }
                }
                
                // Fallback: disparar evento click en el primer paso del navegador
                const firstStepButton = container.querySelector('[\\@click*="goToStep(1)"]');
                if (firstStepButton) {
                    firstStepButton.click();
                }
            } catch (e) {
                // Usando fallback para resetear paso
                // Método alternativo: forzar mostrar solo la primera sección
                const firstSection = container.querySelector('[x-show*="currentStep === 1"]');
                if (firstSection) {
                    // Ocultar todas las secciones
                    const allSections = container.querySelectorAll('[x-show*="currentStep"]');
                    allSections.forEach(section => section.style.display = 'none');
                    // Mostrar solo la primera
                    firstSection.style.display = 'block';
                }
            }
        }, 100);
        
        mostrarNotificacion('Volviendo al modo paso a paso', 'info');
    } else {
        // Expandir - mostrar todas las secciones
        container.dataset.expandido = 'true';
        text.textContent = 'Modo Pasos';
        icon.className = 'fas fa-compress-arrows-alt mr-2';
        toggleBtn.setAttribute('data-tooltip', 'Volver al modo paso a paso');
        
        // Mostrar todas las secciones forzadamente
        const sections = container.querySelectorAll('[x-show*="currentStep"]');
        sections.forEach(section => {
            section.style.display = 'block';
            section.style.visibility = 'visible';
            section.removeAttribute('x-cloak');
            section.removeAttribute('hidden');
        });
        
        mostrarNotificacion('Mostrando todas las secciones', 'success');
    }
}

// Función para mostrar resumen de comentarios
function mostrarResumenComentarios() {
    const comentarios = @json($revisionesExistentes);
    let resumen = '📋 Resumen de Comentarios:\n\n';
    
    Object.entries(comentarios).forEach(([seccion, data]) => {
        if (data.comentario) {
            resumen += `Sección ${seccion}: ${data.comentario}\n\n`;
        }
    });
    
    if (resumen === '📋 Resumen de Comentarios:\n\n') {
        resumen = 'No hay comentarios registrados aún.';
    }
    
    alert(resumen);
}

// Función para actualizar contador de caracteres
function actualizarContador(elemento) {
    const contador = elemento.parentElement.querySelector('[x-data]');
    if (contador) {
        const span = contador.querySelector('span');
        if (span) {
            span.textContent = elemento.value.length;
        }
    }
}

// Función para actualizar contador de sección específica
function actualizarContadorSeccion(seccion, longitud) {
    const contador = document.getElementById(`contador_seccion_${seccion}`);
    if (contador) {
        contador.textContent = longitud;
    }
}

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Solo si no estamos en un input/textarea o si es Ctrl+algo
    if ((e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') || e.ctrlKey) {
        
        // Ctrl + A = Aprobar sección actual
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            const seccionActual = getCurrentSection();
            if (seccionActual) {
                enviarRevision(seccionActual, 'aprobar');
            }
        }
        
        // Ctrl + R = Rechazar sección actual
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            const seccionActual = getCurrentSection();
            if (seccionActual) {
                enviarRevision(seccionActual, 'rechazar');
            }
        }
        
        // Ctrl + Enter = Enviar desde textarea
        if (e.ctrlKey && e.key === 'Enter' && e.target.tagName === 'TEXTAREA') {
            e.preventDefault();
            const seccionActual = getCurrentSection();
            if (seccionActual) {
                enviarRevision(seccionActual, 'aprobar');
            }
        }
        
        // Flecha derecha = Siguiente sección
        if (e.key === 'ArrowRight' && !e.ctrlKey) {
            e.preventDefault();
            irASiguientePendiente();
        }
    }
});

// Función auxiliar para obtener la sección actual
function getCurrentSection() {
    // Esta función debería retornar la sección actualmente visible
    // Por simplicidad, asumimos sección 1
    return 1;
}

// Funciones para las cards clickeables
function filtrarPorEstado(estado) {
    const revisionesExistentes = @json($revisionesExistentes);
    const secciones = [];
    
    Object.entries(revisionesExistentes).forEach(([seccion, data]) => {
        if (data.estado === estado) {
            secciones.push(seccion);
        }
    });
    
    if (secciones.length > 0) {
        mostrarNotificacion(`Encontradas ${secciones.length} secciones ${estado}s: ${secciones.join(', ')}`, 'info');
    } else {
        mostrarNotificacion(`No hay secciones ${estado}s`, 'warning');
    }
}

function mostrarDetalleProgreso() {
    const revisionesExistentes = @json($revisionesExistentes);
    const total = {{ $tramite->solicitante->tipo_persona === 'Física' ? 3 : 6 }};
    
    let detalle = '📊 Detalle del Progreso de Revisión:\n\n';
    
    for (let i = 1; i <= total; i++) {
        const revision = revisionesExistentes[i];
        const estado = revision ? revision.estado : 'pendiente';
        const emoji = estado === 'aprobado' ? '✅' : estado === 'rechazado' ? '❌' : '⏳';
        
        const nombreSeccion = getNombreSeccion(i);
        detalle += `${emoji} Sección ${i}: ${nombreSeccion} - ${estado.toUpperCase()}\n`;
    }
    
    const completadas = Object.values(revisionesExistentes).filter(r => r.estado !== 'pendiente').length;
    const progreso = Math.round((completadas / total) * 100);
    
    detalle += `\n📈 Progreso general: ${progreso}% (${completadas}/${total} completadas)`;
    
    alert(detalle);
}

function getNombreSeccion(numero) {
    const isPersonaFisica = '{{ $tramite->solicitante->tipo_persona }}' === 'Física';
    
    if (isPersonaFisica) {
        const nombres = {
            1: 'Datos Generales',
            2: 'Domicilio', 
            3: 'Documentos'
        };
        return nombres[numero] || 'Desconocida';
    } else {
        const nombres = {
            1: 'Datos Generales',
            2: 'Domicilio',
            3: 'Constitución',
            4: 'Accionistas',
            5: 'Apoderado Legal',
            6: 'Documentos'
        };
        return nombres[numero] || 'Desconocida';
    }
}

// Funciones para el modal de atajos
function mostrarAtajos() {
    document.getElementById('modalAtajos').classList.remove('hidden');
}

function cerrarModalAtajos() {
    document.getElementById('modalAtajos').classList.add('hidden');
}

// Cerrar modal con clic fuera
document.addEventListener('click', function(e) {
    if (e.target.id === 'modalAtajos') {
        cerrarModalAtajos();
    }
});

// Funciones para manejar documentos individuales
function aprobarDocumento(documentoId) {
    const comentario = document.getElementById(`comentario_doc_${documentoId}`).value;
    const cotejoPresencial = document.getElementById(`cotejo_${documentoId}`).checked;
    
    // Crear formulario dinámico
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/revision/documento/${documentoId}/aprobar`;
    
    // Token CSRF
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Comentario
    const comentarioInput = document.createElement('input');
    comentarioInput.type = 'hidden';
    comentarioInput.name = 'comentario';
    comentarioInput.value = comentario;
    form.appendChild(comentarioInput);
    
    // Cotejo presencial
    const cotejoInput = document.createElement('input');
    cotejoInput.type = 'hidden';
    cotejoInput.name = 'cotejo_presencial';
    cotejoInput.value = cotejoPresencial ? '1' : '0';
    form.appendChild(cotejoInput);
    
    // Enviar formulario
    document.body.appendChild(form);
    form.submit();
    
    mostrarNotificacion(`Documento ${documentoId} aprobado${cotejoPresencial ? ' con cotejo presencial' : ''}`, 'success');
}

function rechazarDocumento(documentoId) {
    const comentario = document.getElementById(`comentario_doc_${documentoId}`).value;
    const cotejoPresencial = document.getElementById(`cotejo_${documentoId}`).checked;
    
    if (!comentario.trim()) {
        mostrarNotificacion('Debe agregar un comentario para rechazar el documento', 'error');
        return;
    }
    
    // Crear formulario dinámico
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/revision/documento/${documentoId}/rechazar`;
    
    // Token CSRF
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Comentario
    const comentarioInput = document.createElement('input');
    comentarioInput.type = 'hidden';
    comentarioInput.name = 'comentario';
    comentarioInput.value = comentario;
    form.appendChild(comentarioInput);
    
    // Cotejo presencial
    const cotejoInput = document.createElement('input');
    cotejoInput.type = 'hidden';
    cotejoInput.name = 'cotejo_presencial';
    cotejoInput.value = cotejoPresencial ? '1' : '0';
    form.appendChild(cotejoInput);
    
    // Enviar formulario
    document.body.appendChild(form);
    form.submit();
    
    mostrarNotificacion(`Documento ${documentoId} rechazado`, 'warning');
}

// Inicializar contadores de caracteres y eventos
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea[id^="comentario_seccion_"], textarea[id^="comentario_doc_"]');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            actualizarContador(this);
        });
        
        // Agregar clase de campo activo al hacer focus
        textarea.addEventListener('focus', function() {
            this.parentElement.classList.add('field-active');
        });
        
        textarea.addEventListener('blur', function() {
            this.parentElement.classList.remove('field-active');
        });
    });
    
    // Mostrar mensaje de bienvenida
    setTimeout(() => {
        mostrarNotificacion('Sistema de revisión mejorado cargado. Presiona el botón 🎹 para ver atajos de teclado.', 'info');
    }, 1000);
});
</script> 
@endsection 