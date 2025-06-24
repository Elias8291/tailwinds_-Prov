@extends('layouts.app')

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
                        'datosGenerales' => $datosGenerales ?? null,
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                Revisión
                            </h4>
                            
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Formulario de Aprobar -->
                                <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 1]) }}" class="flex-1">
                                    @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                  placeholder="Comentarios opcionales..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-4 py-3" 
                                                  rows="3">{{ ($revisionesExistentes[1] ?? [])['comentario'] ?? '' }}</textarea>
                                        <button type="submit" 
                                                class="w-full px-4 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-check mr-2"></i>Aprobar
                                        </button>
                                            </div>
                                </form>
                                
                                <!-- Formulario de Rechazar -->
                                <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 1]) }}" class="flex-1">
                                    @csrf
                                    <div class="space-y-3">
                                        <textarea name="comentario"
                                                  placeholder="Motivo del rechazo (requerido)..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-4 py-3" 
                                                  rows="3" required>{{ $estado === 'rechazado' ? (($revisionesExistentes[1] ?? [])['comentario'] ?? '') : '' }}</textarea>
                                        <button type="submit" 
                                                class="w-full px-4 py-3 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-times mr-2"></i>Rechazar
                                        </button>
                                    </div>
                                </form>
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
                        'datosDomicilio' => $domicilio ?? null,
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                                <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="flex flex-col lg:flex-row gap-4">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 2]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-4 py-3" 
                                                  rows="3">{{ ($revisionesExistentes[2] ?? [])['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-check mr-2"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 2]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-4 py-3" 
                                                  rows="3" required>{{ $estado === 'rechazado' ? (($revisionesExistentes[2] ?? [])['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-times mr-2"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
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
                        'datosConstitutivos' => $datosConstitutivos ?? null,
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="flex flex-col lg:flex-row gap-4">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 3]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-4 py-3" 
                                                  rows="3">{{ ($revisionesExistentes[3] ?? [])['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-check mr-2"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 3]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-4 py-3" 
                                                  rows="3" required>{{ $estado === 'rechazado' ? (($revisionesExistentes[3] ?? [])['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-times mr-2"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
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
                        'accionistas' => $accionistas ?? null,
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="flex flex-col lg:flex-row gap-4">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 4]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-4 py-3" 
                                                  rows="3">{{ ($revisionesExistentes[4] ?? [])['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-check mr-2"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 4]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-4 py-3" 
                                                  rows="3" required>{{ $estado === 'rechazado' ? (($revisionesExistentes[4] ?? [])['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-times mr-2"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
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
                        'datosApoderado' => $apoderado ?? null,
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="flex flex-col lg:flex-row gap-4">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 5]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-4 py-3" 
                                                  rows="3">{{ ($revisionesExistentes[5] ?? [])['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-check mr-2"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 5]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-4 py-3" 
                                                  rows="3" required>{{ $estado === 'rechazado' ? (($revisionesExistentes[5] ?? [])['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-times mr-2"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
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
                        'documentos' => $documentos ?? null,
                            'readonly' => true
                        ])
                        
                    <!-- Panel de revisión -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-clipboard-check text-[#9d2449] mr-3"></i>
                                    Revisión
                                </h4>
                                
                            <div class="flex flex-col lg:flex-row gap-4">
                                    <!-- Formulario de Aprobar -->
                                <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, $numSeccionDoc]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-4 py-3" 
                                                  rows="3">{{ ($revisionesExistentes[$numSeccionDoc] ?? [])['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-check mr-2"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, $numSeccionDoc]) }}" class="flex-1">
                                        @csrf
                                    <div class="space-y-3">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-4 py-3" 
                                                  rows="3" required>{{ $estado === 'rechazado' ? (($revisionesExistentes[$numSeccionDoc] ?? [])['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                class="w-full px-4 py-3 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center font-medium">
                                            <i class="fas fa-times mr-2"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
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
</style>
@endpush

@push('scripts')
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCgXSEgnOeCKaE80Zc6ouGxxcHK61vZAR8&libraries=places"></script>
<script src="{{ asset('js/components/document-viewer.js') }}"></script>
<script src="{{ asset('js/components/map-handler.js') }}"></script>
@endpush

<script>
// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de revisión inicializado');
    console.log('📖 DocumentViewer disponible:', typeof documentViewer !== 'undefined');
    console.log('🗺️ MapHandler disponible:', typeof mapHandler !== 'undefined');
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
        console.error('❌ Contenedor no encontrado para la sección:', seccion);
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
        if (window.mapHandler) {
            window.mapHandler.initializeMap(seccion, direccion);
        } else if (window.inicializarMapa) {
            window.inicializarMapa(seccion, direccion);
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
        if (modalRechazo && !modalRechazo.classList.contains('hidden')) {
            modalRechazo.classList.add('hidden');
        }
    }
});
</script> 
@endsection 