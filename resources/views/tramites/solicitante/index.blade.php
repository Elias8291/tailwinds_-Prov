@extends('layouts.app')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<!-- Modal para mostrar datos del SAT -->
<div id="satDataModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] overflow-hidden">
        <!-- Modal header -->
        <div class="px-5 py-3 bg-gradient-to-br from-primary to-primary-dark border-b border-primary/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-1.5 bg-white/10 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Datos del SAT</h3>
                </div>
                <button onclick="closeSatModal()" class="text-white/80 hover:text-white transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Contenido de carga durante validación -->
        <div id="satValidationLoading" class="hidden p-6">
            <div class="flex flex-col items-center justify-center space-y-6 py-12">
                <!-- Spinner animado -->
                <div class="relative">
                    <div class="w-20 h-20 border-4 border-[#9d2449]/20 border-t-[#9d2449] rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-8 h-8 text-[#9d2449] animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Texto de validación -->
                <div class="text-center max-w-md">
                    <h3 class="text-xl font-semibold text-[#9d2449] mb-3">Validando Constancia Fiscal</h3>
                    <p class="text-gray-600 mb-6">
                        Estamos verificando que el RFC coincida con su cuenta y validando los datos con el SAT...
                    </p>
                    
                    <!-- Barra de progreso visual -->
                    <div class="w-full max-w-sm mx-auto mb-6">
                        <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] h-full rounded-full animate-loading-progress"></div>
                        </div>
                    </div>
                    
                    <!-- Pasos del proceso -->
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-center justify-center space-x-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span>Escaneando código QR de la constancia...</span>
                        </div>
                        <div class="flex items-center justify-center space-x-3">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                            <span>Validando RFC con su cuenta...</span>
                        </div>
                        <div class="flex items-center justify-center space-x-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                            <span>Extrayendo datos fiscales del SAT...</span>
                        </div>
                        <div class="flex items-center justify-center space-x-3">
                            <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse" style="animation-delay: 1.5s;"></div>
                            <span>Verificando información tributaria...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenido con datos del SAT (se muestra después de la validación) -->
        <div id="satDataContainer" class="hidden">
            <!-- Modal body -->
            <div class="p-5 overflow-y-auto" style="max-height: calc(90vh - 120px);">
                <div id="satDataContent" class="space-y-4">
                    <!-- Los datos del SAT se insertarán aquí -->
                </div>
            </div>
            <!-- Modal footer -->
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
                <div class="flex justify-end">
                    <button onclick="closeSatModal()" 
                            class="inline-flex items-center px-3 py-1.5 bg-white text-gray-700 hover:bg-gray-50 font-medium rounded-lg border border-gray-300 transition-colors duration-200 text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="min-h-screen bg-gray-50/30 font-montserrat py-8">
    <!-- Contenedor principal -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if(isset($tramite) && isset($paso_actual))
            <!-- Vista de pasos del trámite -->
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <!-- Header Section -->
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-[#9d2449]">
                                    {{ ucfirst($tramite->tipo_tramite) }} - Paso {{ $paso_actual }} de {{ $total_pasos }}
                                </h2>
                                <p class="text-gray-600 text-sm">
                                    RFC: {{ $solicitante->rfc }} | {{ $solicitante->tipo_persona === 'Física' ? 'Persona Física' : 'Persona Moral' }}
                                </p>
                            </div>
                            <button onclick="volverATramites()" class="inline-flex items-center text-gray-500 hover:text-[#9d2449]">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Volver
                            </button>
                        </div>
                        
                        <!-- Indicador de progreso -->
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Progreso del trámite</span>
                                <span class="text-sm text-gray-600">{{ round(($paso_actual / $total_pasos) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ ($paso_actual / $total_pasos) * 100 }}%"></div>
                            </div>
                            
                            <!-- Indicadores de pasos compactos -->
                            <div class="flex justify-between mt-3">
                                @for($i = 1; $i <= $total_pasos; $i++)
                                    <div class="flex flex-col items-center">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium
                                            {{ $i <= $paso_actual ? 'bg-[#9d2449] text-white' : 'bg-gray-300 text-gray-600' }}
                                            {{ $i == $paso_actual ? 'ring-2 ring-[#9d2449]/30' : '' }}">
                                            {{ $i }}
                                        </div>
                                        <span class="text-xs mt-1 text-center max-w-16
                                            {{ $i == $paso_actual ? 'text-[#9d2449] font-medium' : 'text-gray-500' }}">
                                            @php
                                                $nombres_pasos = [
                                                    1 => 'Datos',
                                                    2 => 'Domicilio',
                                                    3 => $solicitante->tipo_persona === 'Física' ? 'Docs' : 'Constitución',
                                                    4 => 'Accionistas',
                                                    5 => 'Apoderado',
                                                    6 => 'Documentos'
                                                ];
                                            @endphp
                                            {{ $nombres_pasos[$i] ?? 'Paso ' . $i }}
                                        </span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Mensajes de estado -->
                    @if(session('success'))
                        <div class="mx-5 mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mx-5 mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mx-5 mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                        </div>
                    @endif

                    <!-- Contenido del paso -->
                    <div class="p-6 space-y-6" 
                         x-data="{ 
                            currentStep: {{ $paso_actual }}, 
                            totalSteps: {{ $total_pasos }},
                            tipoPersona: '{{ $solicitante->tipo_persona }}',
                            tramiteId: {{ $tramite->id ?? 'null' }}
                         }"
                         @next-step="currentStep++"
                         @prev-step="currentStep--">
                         
                        <!-- Sección 1: Datos Generales -->
                        <div x-show="currentStep === 1" x-cloak>
                            @include('components.formularios.seccion-datos-generales', [
                                'title' => 'Datos Generales',
                                'datosTramite' => $datosTramite ?? [],
                                'datosSolicitante' => $datosSolicitante ?? [],
                                'codigoPostalDomicilio' => $codigoPostalDomicilio ?? (isset($datosDomicilio['codigo_postal']) ? $datosDomicilio['codigo_postal'] : null),
                                'datosDomicilio' => $datosDomicilio ?? [],
                                'mostrar_navegacion' => false
                            ])
                        </div>
                        
                        <!-- Sección 2: Domicilio -->
                        <div x-show="currentStep === 2" x-cloak>
                            @include('components.formularios.seccion-domicilio', [
                                'title' => 'Datos de Domicilio',
                                'datosDomicilio' => isset($datosDomicilio) && !empty($datosDomicilio) ? $datosDomicilio : (isset($tramite) ? ['tramite_id' => $tramite->id] : []),
                                'datosSolicitante' => $datosSolicitante ?? [],
                                'tramite' => $tramite ?? null,
                                'codigoPostalDomicilio' => $codigoPostalDomicilio ?? (isset($datosDomicilio['codigo_postal']) ? $datosDomicilio['codigo_postal'] : null),
                                'mostrar_navegacion' => false
                            ])
                        </div>
                        
                        <!-- Sección 3: Constitución (Solo Persona Moral) -->
                        <div x-show="currentStep === 3 && tipoPersona === 'Moral'" x-cloak>
                            @include('components.formularios.seccion-constitucion', [
                                'title' => 'Datos de Constitución',
                                'mostrar_navegacion' => false
                            ])
                        </div>
                        
                        <!-- Sección 3: Documentos (Solo Persona Física) -->
                        <div x-show="currentStep === 3 && tipoPersona === 'Física'" x-cloak @previous-step="currentStep--">
                            @include('components.formularios.seccion-documentos', [
                                'title' => 'Documentos Requeridos',
                                'tramite' => $tramite ?? null,
                                'mostrar_navegacion' => false
                            ])
                        </div>
                        
                        <!-- Sección 4: Accionistas (Solo Persona Moral) -->
                        <div x-show="currentStep === 4 && tipoPersona === 'Moral'" x-cloak>
                            @include('components.formularios.seccion-accionistas', [
                                'title' => 'Accionistas',
                                'mostrar_navegacion' => false
                            ])
                        </div>
                        
                        <!-- Sección 5: Apoderado Legal (Solo Persona Moral) -->
                        <div x-show="currentStep === 5 && tipoPersona === 'Moral'" x-cloak @next-step="currentStep++" @previous-step="currentStep--">
                            @include('components.formularios.seccion-apoderado', [
                                'title' => 'Apoderado Legal',
                                'tramite' => $tramite ?? null,
                                'datosApoderado' => isset($datosApoderado) ? $datosApoderado : [],
                                'mostrar_navegacion' => false
                            ])
                        </div>
                        
                        <!-- Sección 6: Documentos (Solo Persona Moral) -->
                        <div x-show="currentStep === 6 && tipoPersona === 'Moral'" x-cloak @previous-step="currentStep--">
                            @include('components.formularios.seccion-documentos', [
                                'title' => 'Documentos Requeridos',
                                'tramite' => $tramite ?? null,
                                'mostrar_navegacion' => false
                            ])
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="p-6 pt-0">
                        <div class="flex justify-between pt-4">
                            @if($puede_regresar ?? false)
                                <button type="button" 
                                        onclick="navegarAnterior()"
                                        class="inline-flex items-center bg-gray-600 text-white px-6 py-2 rounded-xl shadow-lg hover:bg-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-gray-600/20">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Anterior
                                </button>
                            @else
                                <div></div>
                            @endif
                            
                            @if($paso_actual < $total_pasos)
                                <button type="button" 
                                        onclick="navegarSiguiente()"
                                        class="inline-flex items-center bg-[#9d2449] text-white px-6 py-2 rounded-xl shadow-lg hover:bg-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-[#9d2449]/20">
                                    Siguiente
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            @else
                                <!-- Último paso - Botón Finalizar -->
                                <button type="button" 
                                        onclick="finalizarTramite()"
                                        class="inline-flex items-center bg-green-600 text-white px-6 py-2 rounded-xl shadow-lg hover:bg-green-700 transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-green-600/20">
                                    <i class="fas fa-check mr-2"></i>
                                    Finalizar Trámite
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="px-6 pb-6">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-blue-900 mb-1">Información importante</h4>
                                    <p class="text-sm text-blue-700">
                                        Al presionar "Siguiente", sus datos se guardan automáticamente y avanza al siguiente paso. 
                                        Puede regresar en cualquier momento para revisar o modificar la información.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Vista original de selección de trámites -->
            <!-- Header elegante -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-[#9d2449] to-[#7a1d37] rounded-2xl mb-4 shadow-lg">
                    <i class="fas fa-clipboard-list text-2xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent mb-2">
                    Mis Trámites
                </h1>
                <p class="text-gray-600 max-w-lg mx-auto">
                    Selecciona el trámite que necesitas realizar
                </p>
            </div>

        <!-- Tarjetas compactas y elegantes -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            
            <!-- Tarjeta de Inscripción -->
            <div class="relative bg-gradient-to-br from-[#9d2449]/10 to-[#9d2449]/5 rounded-2xl shadow-lg border border-[#9d2449]/20 overflow-hidden transition-all duration-200 hover:shadow-xl hover:scale-102 {{ $tipoTramite['inscripcion'] ? '' : 'opacity-50 pointer-events-none' }}">
                <!-- Gradiente decorativo -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#9d2449] to-[#b8396b]"></div>
                
                <!-- Contenido compacto -->
                <div class="p-5">
                    <!-- Header con icono -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-[#9d2449] to-[#b8396b] rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-user-plus text-white text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-bold text-[#9d2449]">Inscripción</h3>
                                <p class="text-xs text-gray-600">Nuevo registro</p>
                            </div>
                        </div>
                        @if($tipoTramite['inscripcion'])
                            <div class="w-3 h-3 bg-[#9d2449] rounded-full shadow-lg"></div>
                        @else
                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                        @endif
                    </div>

                    <!-- Descripción compacta -->
                    <p class="text-gray-700 text-sm mb-4">
                        @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'inscripcion')
                            <span class="text-[#9d2449] font-medium">En progreso:</span> Paso {{ $tramiteEnProgreso->progreso_tramite ?? 1 }} de {{ $tramiteEnProgreso->solicitante->tipo_persona === 'Física' ? 3 : 6 }}
                            @if(!empty($datosDomicilio['codigo_postal']))
                                <br><span class="text-xs text-gray-500">CP: {{ $datosDomicilio['codigo_postal'] }} {{ $datosDomicilio['estado'] ?? '' }}</span>
                            @endif
                        @else
                            @if($infoProveedor)
                                @if($infoProveedor['ya_vencido'])
                                    <span class="text-red-600 font-medium">Proveedor vencido</span> - Nueva inscripción requerida
                                @else
                                    Ya es proveedor activo ({{ $infoProveedor['pv'] }})
                                @endif
                            @else
                                Primera inscripción al Padrón de Proveedores del Estado
                                <br><span class="text-xs text-blue-600">⚡ Incluye validación de constancia fiscal</span>
                            @endif
                        @endif
                    </p>

                    <!-- Botón Ver Datos del SAT (oculto inicialmente) -->
                    <div class="mb-3">
                        <button type="button" 
                                id="verDatosBtn"
                                onclick="showSatModal()"
                                style="display: none;"
                                class="inline-flex items-center text-xs bg-white hover:bg-blue-50 text-blue-600 font-medium py-1.5 px-3 rounded-lg transition-all duration-300 shadow-sm hover:shadow border border-blue-200 hover:border-blue-300">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver Datos del SAT
                        </button>
                        
                        <!-- Botón para simular validación RFC (solo para demostración) -->
                        <button type="button" 
                                onclick="simularValidacionRFC('{{ $infoProveedor['rfc'] ?? 'DEMO123456789' }}', 'verDatosBtn')"
                                class="inline-flex items-center text-xs bg-green-50 hover:bg-green-100 text-green-700 font-medium py-1.5 px-3 rounded-lg transition-all duration-300 shadow-sm hover:shadow border border-green-200 hover:border-green-300 ml-2">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Validar RFC
                        </button>
                    </div>

                    <!-- Botón elegante -->
                    @if($tipoTramite['inscripcion'])
                        <form action="{{ route('tramites.solicitante.iniciar-inscripcion') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-gradient-to-r from-[#9d2449] to-[#b8396b] text-white py-2.5 rounded-xl font-medium text-sm shadow-lg hover:shadow-xl transition-all duration-200">
                                @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'inscripcion')
                                    Continuar
                                    <i class="fas fa-play ml-2"></i>
                                @else
                                    Comenzar
                                    <i class="fas fa-arrow-right ml-2"></i>
                                @endif
                            </button>
                        </form>
                    @else
                        <div class="w-full bg-gray-200 text-gray-500 py-2.5 rounded-xl font-medium text-sm text-center">
                            No disponible
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tarjeta de Renovación -->
            <div class="relative bg-gradient-to-br from-[#9d2449]/15 to-[#9d2449]/8 rounded-2xl shadow-lg border border-[#9d2449]/25 overflow-hidden transition-all duration-200 hover:shadow-xl hover:scale-102 {{ $tipoTramite['renovacion'] ? '' : 'opacity-50 pointer-events-none' }}">
                <!-- Gradiente decorativo -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#c1437a] to-[#9d2449]"></div>
                
                <!-- Contenido compacto -->
                <div class="p-5">
                    <!-- Header con icono -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-[#c1437a] to-[#9d2449] rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-sync-alt text-white text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-bold text-[#9d2449]">Renovación</h3>
                                <p class="text-xs text-gray-600">
                                    @if($infoProveedor && $infoProveedor['proximo_a_vencer'])
                                        @if($infoProveedor['tiempo_restante']['urgente'])
                                            <span class="{{ $infoProveedor['tiempo_restante']['clase_css'] }} font-medium">¡URGENTE!</span>
                                        @else
                                            Próximo a vencer
                                        @endif
                                    @else
                                        Próximo a vencer
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($tipoTramite['renovacion'])
                            <div class="w-3 h-3 bg-[#c1437a] rounded-full shadow-lg"></div>
                        @else
                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                        @endif
                    </div>

                    <!-- Descripción compacta -->
                    <p class="text-gray-700 text-sm mb-4">
                        @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'renovacion')
                            <span class="text-[#c1437a] font-medium">En progreso:</span> Paso {{ $tramiteEnProgreso->progreso_tramite ?? 1 }} de {{ $tramiteEnProgreso->solicitante->tipo_persona === 'Física' ? 3 : 6 }}
                            @if(!empty($datosDomicilio['codigo_postal']))
                                <br><span class="text-xs text-gray-500">CP: {{ $datosDomicilio['codigo_postal'] }} {{ $datosDomicilio['estado'] ?? '' }}</span>
                            @endif
                        @else
                            @if($infoProveedor && $infoProveedor['proximo_a_vencer'])
                                <span class="font-medium {{ $infoProveedor['tiempo_restante']['clase_css'] }}">{{ $infoProveedor['pv'] }}</span> 
                                @if($infoProveedor['ya_vencido'])
                                    <span class="{{ $infoProveedor['tiempo_restante']['clase_css'] }}">{{ $infoProveedor['tiempo_restante']['texto'] }}</span>
                                @else
                                    vence en <span class="{{ $infoProveedor['tiempo_restante']['clase_css'] }}">{{ $infoProveedor['tiempo_restante']['texto'] }}</span>
                                @endif
                                <br><span class="text-xs text-gray-500">Vencimiento: {{ $infoProveedor['fecha_vencimiento']->format('d/m/Y H:i') }}</span>
                            @else
                                Renueva tu registro antes del vencimiento (7 días)
                                <br><span class="text-xs text-blue-600">⚡ Incluye validación de constancia fiscal</span>
                            @endif
                        @endif
                    </p>

                    <!-- Botón Ver Datos del SAT (oculto inicialmente) -->
                    <div class="mb-3">
                        <button type="button" 
                                id="verDatosBtnRenovacion"
                                onclick="showSatModal()"
                                style="display: none;"
                                class="inline-flex items-center text-xs bg-white hover:bg-blue-50 text-blue-600 font-medium py-1.5 px-3 rounded-lg transition-all duration-300 shadow-sm hover:shadow border border-blue-200 hover:border-blue-300">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver Datos del SAT
                        </button>
                        
                        <!-- Botón para simular validación RFC (solo para demostración) -->
                        <button type="button" 
                                onclick="simularValidacionRFC('{{ $infoProveedor['rfc'] ?? 'DEMO123456789' }}', 'verDatosBtnRenovacion')"
                                class="inline-flex items-center text-xs bg-green-50 hover:bg-green-100 text-green-700 font-medium py-1.5 px-3 rounded-lg transition-all duration-300 shadow-sm hover:shadow border border-green-200 hover:border-green-300 ml-2">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Validar RFC
                        </button>
                    </div>

                    <!-- Botón elegante -->
                    @if($tipoTramite['renovacion'])
                        <form action="{{ route('tramites.solicitante.iniciar-renovacion') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-gradient-to-r from-[#c1437a] to-[#9d2449] text-white py-2.5 rounded-xl font-medium text-sm shadow-lg hover:shadow-xl transition-all duration-200">
                                @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'renovacion')
                                    Continuar
                                    <i class="fas fa-play ml-2"></i>
                                @else
                                    Renovar
                                    <i class="fas fa-arrow-right ml-2"></i>
                                @endif
                            </button>
                        </form>
                    @else
                        <div class="w-full bg-gray-200 text-gray-500 py-2.5 rounded-xl font-medium text-sm text-center">
                            No disponible
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tarjeta de Actualización -->
            <div class="relative bg-gradient-to-br from-[#9d2449]/12 to-[#9d2449]/6 rounded-2xl shadow-lg border border-[#9d2449]/30 overflow-hidden transition-all duration-200 hover:shadow-xl hover:scale-102 {{ $tipoTramite['actualizacion'] ? '' : 'opacity-50 pointer-events-none' }}">
                <!-- Gradiente decorativo -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#7a1d37] to-[#9d2449]"></div>
                
                <!-- Contenido compacto -->
                <div class="p-5">
                    <!-- Header con icono -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-[#7a1d37] to-[#9d2449] rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-edit text-white text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-bold text-[#9d2449]">Actualización</h3>
                                <p class="text-xs text-gray-600">Modificar datos</p>
                            </div>
                        </div>
                        @if($tipoTramite['actualizacion'])
                            <div class="w-3 h-3 bg-[#7a1d37] rounded-full shadow-lg"></div>
                        @else
                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                        @endif
                    </div>

                    <!-- Descripción compacta -->
                    <p class="text-gray-700 text-sm mb-4">
                        @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'actualizacion')
                            <span class="text-[#7a1d37] font-medium">En progreso:</span> Paso {{ $tramiteEnProgreso->progreso_tramite ?? 1 }} de {{ $tramiteEnProgreso->solicitante->tipo_persona === 'Física' ? 3 : 6 }}
                            @if(!empty($datosDomicilio['codigo_postal']))
                                <br><span class="text-xs text-gray-500">CP: {{ $datosDomicilio['codigo_postal'] }} {{ $datosDomicilio['estado'] ?? '' }}</span>
                            @endif
                        @else
                            @if($infoProveedor)
                                <span class="text-green-600 font-medium">{{ $infoProveedor['pv'] }}</span> - Proveedor activo
                                <br><span class="text-xs {{ $infoProveedor['tiempo_restante']['clase_css'] }}">
                                    Vence en {{ $infoProveedor['tiempo_restante']['texto'] }}
                                </span>
                                <br><span class="text-xs text-gray-500">Vigente hasta: {{ $infoProveedor['fecha_vencimiento']->format('d/m/Y H:i') }}</span>
                            @else
                                Actualiza información, servicios y documentos
                                <br><span class="text-xs text-blue-600">⚡ Incluye validación de constancia fiscal</span>
                            @endif
                        @endif
                    </p>

                    <!-- Botón Ver Datos del SAT (oculto inicialmente) -->
                    <div class="mb-3">
                        <button type="button" 
                                id="verDatosBtnActualizacion"
                                onclick="showSatModal()"
                                style="display: none;"
                                class="inline-flex items-center text-xs bg-white hover:bg-blue-50 text-blue-600 font-medium py-1.5 px-3 rounded-lg transition-all duration-300 shadow-sm hover:shadow border border-blue-200 hover:border-blue-300">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver Datos del SAT
                        </button>
                        
                        <!-- Botón para simular validación RFC (solo para demostración) -->
                        <button type="button" 
                                onclick="simularValidacionRFC('{{ $infoProveedor['rfc'] ?? 'DEMO123456789' }}', 'verDatosBtnActualizacion')"
                                class="inline-flex items-center text-xs bg-green-50 hover:bg-green-100 text-green-700 font-medium py-1.5 px-3 rounded-lg transition-all duration-300 shadow-sm hover:shadow border border-green-200 hover:border-green-300 ml-2">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Validar RFC
                        </button>
                    </div>

                    <!-- Botón elegante -->
                    @if($tipoTramite['actualizacion'])
                        <form action="{{ route('tramites.solicitante.iniciar-actualizacion') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-gradient-to-r from-[#7a1d37] to-[#9d2449] text-white py-2.5 rounded-xl font-medium text-sm shadow-lg hover:shadow-xl transition-all duration-200">
                                @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'actualizacion')
                                    Continuar
                                    <i class="fas fa-play ml-2"></i>
                                @else
                                    Actualizar
                                    <i class="fas fa-arrow-right ml-2"></i>
                                @endif
                            </button>
                        </form>
                    @else
                        <div class="w-full bg-gray-200 text-gray-500 py-2.5 rounded-xl font-medium text-sm text-center">
                            No disponible
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
[x-cloak] { display: none !important; }

@keyframes loading-progress {
    0% { width: 20%; }
    50% { width: 75%; }
    100% { width: 95%; }
}

.animate-loading-progress {
    animation: loading-progress 2s ease-in-out infinite alternate;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.5s ease-out;
}

.primary {
    --tw-bg-opacity: 1;
    background-color: rgb(157 36 73 / var(--tw-bg-opacity));
}

.primary-dark {
    --tw-bg-opacity: 1;
    background-color: rgb(122 29 55 / var(--tw-bg-opacity));
}
</style>
@endpush

@push('scripts')
<!-- Scripts necesarios para SAT -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js"></script>
<script src="/js/scrapers/sat-scraper.js"></script>
<script src="/js/validators/sat-validator.js"></script>
<script src="/js/components/qr-reader.js"></script>
<script src="/js/components/qr-handler.js"></script>

<script>
    // Función para volver a la vista de trámites
    function volverATramites() {
        window.location.href = '{{ route("tramites.solicitante.index") }}';
    }

    @if(isset($tramite) && isset($paso_actual))
    // Funciones de navegación globales para integración con componentes
    window.navegarSiguiente = function() {
        // Usar SOLO Alpine.js - navegación SPA sin recargar página
        const alpineContainer = document.querySelector('[x-data]');
        
        if (alpineContainer) {
            try {
                // Intentar acceder al componente Alpine y aumentar currentStep
                if (typeof Alpine !== 'undefined') {
                    const alpineData = Alpine.$data(alpineContainer);
                    if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                        if (alpineData.currentStep < alpineData.totalSteps) {
                            alpineData.currentStep++;
                            return;
                        } else {
                            return;
                        }
                    }
                }
                
                // Fallback: usar event dispatch para comunicarse con Alpine
                alpineContainer.dispatchEvent(new CustomEvent('next-step'));
                return;
                
            } catch (error) {
                // Error silencioso
            }
        }
    };

    window.navegarAnterior = function() {
        const alpineContainer = document.querySelector('[x-data]');
        
        if (alpineContainer) {
            try {
                if (typeof Alpine !== 'undefined') {
                    const alpineData = Alpine.$data(alpineContainer);
                    if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                        alpineData.currentStep--;
                        return;
                    }
                }
            } catch (error) {
                // Error silencioso
            }
        }
    };

    function finalizarTramite() {
        if (confirm('¿Está seguro de que desea finalizar el trámite? Una vez finalizado, no podrá realizar más cambios.')) {
            window.location.href = '{{ route("tramites.solicitante.index") }}';
        }
    }
    @endif

    // Variables globales para el SAT
    let satDataExtracted = null;
    let rfcValidated = false;
    
    // Función para mostrar el modal con datos del SAT
    window.showSatModal = function() {
        if (!satDataExtracted) {
            alert('No hay datos del SAT disponibles para mostrar');
            return;
        }

        const modal = document.getElementById('satDataModal');
        const loadingDiv = document.getElementById('satValidationLoading');
        const dataContainer = document.getElementById('satDataContainer');
        
        if (modal && loadingDiv && dataContainer) {
            // Mostrar modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Mostrar loading y ocultar contenido
            loadingDiv.classList.remove('hidden');
            dataContainer.classList.add('hidden');
            
            // Simular proceso de validación con diferentes etapas
            setTimeout(() => {
                // Después de 2 segundos, generar contenido del modal
                const content = generateSatModalContent(satDataExtracted);
                const satDataContent = document.getElementById('satDataContent');
                if (satDataContent) {
                    satDataContent.innerHTML = content;
                }
                
                // Ocultar loading y mostrar datos con animación
                loadingDiv.classList.add('hidden');
                dataContainer.classList.remove('hidden');
                dataContainer.classList.add('animate-fadeInUp');
                
                // Remover clase de animación después de completarla
                setTimeout(() => {
                    dataContainer.classList.remove('animate-fadeInUp');
                }, 500);
            }, 2000);
        }
    };

    // Función para cerrar el modal
    window.closeSatModal = function() {
        const modal = document.getElementById('satDataModal');
        const loadingDiv = document.getElementById('satValidationLoading');
        const dataContainer = document.getElementById('satDataContainer');
        
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
            
            // Resetear estado del modal para próxima vez
            if (loadingDiv) loadingDiv.classList.add('hidden');
            if (dataContainer) dataContainer.classList.add('hidden');
        }
    };

    // Función para generar contenido del modal usando SATScraper
    function generateSatModalContent(data) {
        // Usar SATScraper si está disponible, sino usar fallback
        if (typeof SATScraper !== 'undefined' && SATScraper.generateModalContent) {
            return SATScraper.generateModalContent(data);
        }
        
        // Fallback si SATScraper no está disponible
        if (!data || !data.sections) {
            return '<p class="text-gray-500">No hay datos para mostrar.</p>';
        }

        let html = '';
        
        data.sections.forEach(section => {
            html += `
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">
                        ${section.title}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            `;
            
            section.fields.forEach(field => {
                const isStatus = section.title === 'Estado de los Datos';
                const valueClass = isStatus && field.value.includes('⚠️') 
                    ? 'text-yellow-600' 
                    : isStatus && field.value.includes('✅') 
                        ? 'text-green-600' 
                        : 'text-gray-700';
                
                html += `
                        <div class="bg-gray-50 p-3 rounded-lg">
                        <dt class="text-sm font-medium text-gray-600 mb-1">${field.label}:</dt>
                        <dd class="text-sm ${valueClass} break-words">${field.value}</dd>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });

        return html;
    }

    // Función para cargar datos del SAT desde el backend
    window.cargarDatosSAT = async function() {
        // Por ahora, los datos del SAT se procesan dinámicamente con la simulación
        // Esta función se mantiene para compatibilidad futura
        console.log('Función cargarDatosSAT ejecutada - datos se procesan dinámicamente');
        return false;
    };

    // Función para simular la validación del RFC y activar botón "Ver Datos"
    window.simularValidacionRFC = function(rfc, verBtnId = 'verDatosBtn') {
        // Mostrar loading mientras se "valida"
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="w-3.5 h-3.5 mr-1.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>Validando...';
        btn.disabled = true;
        
        setTimeout(() => {
            // Restaurar botón
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            // Simular datos del SAT (esto normalmente vendría del QRHandler)
            satDataExtracted = {
                sections: [
                    {
                        title: 'Estado de los Datos',
                        fields: [
                            {
                                label: 'Conexión con SAT',
                                value: '✅ Datos obtenidos exitosamente del SAT'
                            },
                            {
                                label: 'Estado de Validación',
                                value: '✅ RFC validado correctamente'
                            }
                        ]
                    },
                    {
                        title: 'Datos Fiscales',
                        fields: [
                            {
                                label: 'RFC',
                                value: rfc
                            },
                            {
                                label: 'Nombre/Razón Social',
                                value: '{{ auth()->user()->nombre ?? "Contribuyente Validado" }}'
                            },
                            {
                                label: 'Tipo de Persona',
                                value: '{{ $solicitante->tipo_persona ?? "Física" }}'
                            },
                            {
                                label: 'Situación del Contribuyente',
                                value: 'Activo'
                            },
                            {
                                label: 'Fecha de Validación',
                                value: new Date().toLocaleDateString('es-MX', { 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })
                            }
                        ]
                    },
                    {
                        title: 'Datos de Domicilio',
                        fields: [
                            {
                                label: 'Código Postal',
                                value: '{{ $datosDomicilio["codigo_postal"] ?? "68000" }}'
                            },
                            {
                                label: 'Estado',
                                value: '{{ $datosDomicilio["estado"] ?? "Oaxaca" }}'
                            },
                            {
                                label: 'Municipio',
                                value: '{{ $datosDomicilio["municipio"] ?? "Oaxaca de Juárez" }}'
                            },
                            {
                                label: 'Colonia',
                                value: '{{ $datosDomicilio["colonia"] ?? "Centro" }}'
                            }
                        ]
                    },
                    {
                        title: 'Información del Trámite',
                        fields: [
                            {
                                label: 'Proceso',
                                value: 'Validación de Constancia Fiscal'
                            },
                            {
                                label: 'Sistema',
                                value: 'Padrón de Proveedores - Gobierno de Oaxaca'
                            },
                            {
                                label: 'Válida para',
                                value: 'Inscripción, Renovación y Actualización'
                            }
                        ]
                    }
                ]
            };
            
            rfcValidated = true;
            
            // Mostrar botón "Ver Datos"
            const verDatosBtn = document.getElementById(verBtnId);
            if (verDatosBtn) {
                verDatosBtn.style.display = 'inline-flex';
            }
            
            // Mostrar mensaje de éxito
            alert('🎉 RFC validado exitosamente con el SAT.\n\nAhora puede ver los datos extraídos de su constancia fiscal.');
        }, 2000); // 2 segundos para simular validación
    };

    // Cerrar modal al hacer clic fuera de él
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('satDataModal');
        const modalContent = modal?.querySelector('.bg-white');
        if (modal && event.target === modal && modalContent && !modalContent.contains(event.target)) {
            closeSatModal();
        }
    });

    // Carga instantánea - sin retrasos
    document.addEventListener('DOMContentLoaded', function() {
        // Las tarjetas cargan inmediatamente sin animación de retraso
        
        // Configurar PDF.js si está disponible
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js';
        }
        
        console.log('Vista de trámites cargada correctamente');
    });
</script>
@endpush
@endsection 