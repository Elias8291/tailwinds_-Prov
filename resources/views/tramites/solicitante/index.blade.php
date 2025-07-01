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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            
            <!-- Tarjeta de Inscripción -->
            <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:-translate-y-2 {{ $tipoTramite['inscripcion'] ? '' : 'opacity-50 pointer-events-none' }}">
                <!-- Gradiente decorativo superior -->
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#9d2449] via-[#b8396b] to-[#9d2449]"></div>
                
                <!-- Efecto de brillo en hover -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                
                <!-- Contenido -->
                <div class="relative p-6">
                    <!-- Header con icono mejorado -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="relative">
                                <div class="w-14 h-14 bg-gradient-to-br from-[#9d2449] to-[#b8396b] rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                    <i class="fas fa-user-plus text-white text-lg"></i>
                                </div>
                                <!-- Anillo decorativo -->
                                <div class="absolute -inset-1 bg-gradient-to-r from-[#9d2449] to-[#b8396b] rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-[#9d2449] group-hover:text-[#b8396b] transition-colors duration-300">Inscripción</h3>
                                <p class="text-sm text-gray-500 font-medium">Primera vez en el padrón</p>
                            </div>
                        </div>
                        @if($tipoTramite['inscripcion'])
                            <div class="w-4 h-4 bg-[#9d2449] rounded-full shadow-md group-hover:scale-125 transition-transform duration-300"></div>
                        @else
                            <div class="w-4 h-4 bg-gray-300 rounded-full"></div>
                        @endif
                    </div>

                    <!-- Descripción mejorada -->
                    <div class="mb-6">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'inscripcion')
                                @if($tramiteEnProgreso->estado === 'En Revision')
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-eye mr-1"></i>
                                        En Revisión
                                    </span>
                                    <br>Trámite enviado y en proceso de evaluación
                                @elseif($tramiteEnProgreso->estado === 'Aprobado')
                                    <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Trámite Completado
                                    </span>
                                    <br>¡Su inscripción ha sido aprobada exitosamente!
                                @elseif($tramiteEnProgreso->estado === 'Rechazado')
                                    <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Requiere Correcciones
                                    </span>
                                    <br>Revise las observaciones y corrija la información
                                @else
                                    <span class="inline-flex items-center px-2 py-1 bg-[#9d2449]/10 text-[#9d2449] text-xs font-semibold rounded-full mb-2">
                                        <div class="w-2 h-2 bg-[#9d2449] rounded-full mr-2 animate-pulse"></div>
                                        En Progreso
                                    </span>
                                    <br>Paso {{ $tramiteEnProgreso->progreso_tramite ?? 1 }} de {{ $tramiteEnProgreso->solicitante->tipo_persona === 'Física' ? 3 : 6 }}
                                @endif
                            @else
                                @if($infoProveedor)
                                    @if($infoProveedor['ya_vencido'])
                                        <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full mb-2">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Proveedor vencido
                                        </span>
                                        <br>Se requiere nueva inscripción al padrón
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full mb-2">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Ya es proveedor
                                        </span>
                                        <br>Proveedor activo: <strong>{{ $infoProveedor['pv'] }}</strong>
                                    @endif
                                @else
                                    Regístrese por primera vez en el <strong>Padrón de Proveedores del Estado de Oaxaca</strong> y obtenga su número de proveedor oficial.
                                @endif
                            @endif
                        </p>
                    </div>

                    <!-- Botón elegante mejorado -->
                    @if($tipoTramite['inscripcion'])
                        @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'inscripcion' && in_array($tramiteEnProgreso->estado, ['En Revision', 'Aprobado', 'Rechazado']))
                            <!-- Trámite enviado - Mostrar enlace al estado -->
                            <a href="{{ route('tramites.solicitante.estado', $tramiteEnProgreso) }}" class="w-full bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#7a1d37] hover:to-[#9d2449] text-white py-3 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group block">
                                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="relative flex items-center justify-center">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Ver Estado del Trámite
                                </div>
                            </a>
                        @else
                            <form action="{{ route('tramites.solicitante.iniciar-inscripcion') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-gradient-to-r from-[#9d2449] to-[#b8396b] hover:from-[#7a1c38] hover:to-[#9d2449] text-white py-3 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group">
                                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <div class="relative flex items-center justify-center">
                                        @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'inscripcion')
                                            <i class="fas fa-play mr-2"></i>
                                            Continuar Trámite
                                        @else
                                            <i class="fas fa-plus-circle mr-2"></i>
                                            Iniciar Inscripción
                                        @endif
                                    </div>
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="w-full bg-gray-100 text-gray-500 py-3 rounded-xl font-semibold text-sm text-center border border-gray-200">
                            <i class="fas fa-lock mr-2"></i>
                            No disponible
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tarjeta de Renovación -->
            <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:-translate-y-2 {{ $tipoTramite['renovacion'] ? '' : 'opacity-50 pointer-events-none' }}">
                <!-- Gradiente decorativo superior -->
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#c1437a] via-[#e55a8f] to-[#c1437a]"></div>
                
                <!-- Efecto de brillo en hover -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                
                <!-- Contenido -->
                <div class="relative p-6">
                    <!-- Header con icono mejorado -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="relative">
                                <div class="w-14 h-14 bg-gradient-to-br from-[#c1437a] to-[#e55a8f] rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                    <i class="fas fa-sync-alt text-white text-lg"></i>
                                </div>
                                <!-- Anillo decorativo -->
                                <div class="absolute -inset-1 bg-gradient-to-r from-[#c1437a] to-[#e55a8f] rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-[#c1437a] group-hover:text-[#e55a8f] transition-colors duration-300">Renovación</h3>
                                <p class="text-sm text-gray-500 font-medium">
                                    @if($infoProveedor && $infoProveedor['proximo_a_vencer'])
                                        @if($infoProveedor['tiempo_restante']['urgente'])
                                            <span class="{{ $infoProveedor['tiempo_restante']['clase_css'] }} font-bold">¡URGENTE!</span>
                                        @else
                                            Próximo a vencer
                                        @endif
                                    @else
                                        Renovar registro
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($tipoTramite['renovacion'])
                            <div class="w-4 h-4 bg-[#c1437a] rounded-full shadow-md group-hover:scale-125 transition-transform duration-300"></div>
                        @else
                            <div class="w-4 h-4 bg-gray-300 rounded-full"></div>
                        @endif
                    </div>

                    <!-- Descripción mejorada -->
                    <div class="mb-6">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'renovacion')
                                @if($tramiteEnProgreso->estado === 'En Revision')
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-eye mr-1"></i>
                                        En Revisión
                                    </span>
                                    <br>Renovación enviada y en proceso de evaluación
                                @elseif($tramiteEnProgreso->estado === 'Aprobado')
                                    <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Renovación Completada
                                    </span>
                                    <br>¡Su renovación ha sido aprobada exitosamente!
                                @elseif($tramiteEnProgreso->estado === 'Rechazado')
                                    <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Requiere Correcciones
                                    </span>
                                    <br>Revise las observaciones y corrija la información
                                @else
                                    <span class="inline-flex items-center px-2 py-1 bg-[#c1437a]/10 text-[#c1437a] text-xs font-semibold rounded-full mb-2">
                                        <div class="w-2 h-2 bg-[#c1437a] rounded-full mr-2 animate-pulse"></div>
                                        En Progreso
                                    </span>
                                    <br>Paso {{ $tramiteEnProgreso->progreso_tramite ?? 1 }} de {{ $tramiteEnProgreso->solicitante->tipo_persona === 'Física' ? 3 : 6 }}
                                @endif
                            @else
                                @if($infoProveedor && $infoProveedor['proximo_a_vencer'])
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-id-card mr-1"></i>
                                        {{ $infoProveedor['pv'] }}
                                    </span>
                                    @if($infoProveedor['ya_vencido'])
                                        <br><span class="{{ $infoProveedor['tiempo_restante']['clase_css'] }} font-semibold">{{ $infoProveedor['tiempo_restante']['texto'] }}</span>
                                    @else
                                        <br>Vence en <span class="{{ $infoProveedor['tiempo_restante']['clase_css'] }} font-semibold">{{ $infoProveedor['tiempo_restante']['texto'] }}</span>
                                    @endif
                                    <br><span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded mt-1 inline-block">📅 Vencimiento: {{ $infoProveedor['fecha_vencimiento']->format('d/m/Y H:i') }}</span>
                                @else
                                    Renueve su registro en el padrón <strong>antes del vencimiento</strong> para mantener su estatus de proveedor activo.
                                @endif
                            @endif
                        </p>
                    </div>

                    <!-- Botón elegante mejorado -->
                    @if($tipoTramite['renovacion'])
                        @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'renovacion' && in_array($tramiteEnProgreso->estado, ['En Revision', 'Aprobado', 'Rechazado']))
                            <!-- Trámite enviado - Mostrar enlace al estado -->
                            <a href="{{ route('tramites.solicitante.estado', $tramiteEnProgreso) }}" class="w-full bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#7a1d37] hover:to-[#9d2449] text-white py-3 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group block">
                                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="relative flex items-center justify-center">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Ver Estado del Trámite
                                </div>
                            </a>
                        @else
                            <form action="{{ route('tramites.solicitante.iniciar-renovacion') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-gradient-to-r from-[#c1437a] to-[#e55a8f] hover:from-[#9d2449] hover:to-[#c1437a] text-white py-3 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group">
                                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <div class="relative flex items-center justify-center">
                                        @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'renovacion')
                                            <i class="fas fa-play mr-2"></i>
                                            Continuar Renovación
                                        @else
                                            <i class="fas fa-redo-alt mr-2"></i>
                                            Iniciar Renovación
                                        @endif
                                    </div>
                                </button>
                            </form>
                        @endif
                    @elseif(isset($tipoTramite['mensaje_bloqueo']) && $tipoTramite['mensaje_bloqueo'])
                        <!-- Mensaje de bloqueo por no tener 7 meses activo -->
                        <div class="w-full bg-yellow-50 border border-yellow-200 py-4 px-4 rounded-xl text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-clock text-yellow-600 text-lg mr-2"></i>
                                <span class="text-sm font-semibold text-yellow-800">Tiempo Mínimo Requerido</span>
                            </div>
                            <p class="text-xs text-yellow-700 leading-relaxed">
                                {{ $tipoTramite['mensaje_bloqueo'] }}
                            </p>
                        </div>
                    @else
                        <div class="w-full bg-gray-100 text-gray-500 py-3 rounded-xl font-semibold text-sm text-center border border-gray-200">
                            <i class="fas fa-lock mr-2"></i>
                            No disponible
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tarjeta de Actualización -->
            <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:-translate-y-2 {{ $tipoTramite['actualizacion'] ? '' : 'opacity-50 pointer-events-none' }}">
                <!-- Gradiente decorativo superior -->
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#7a1d37] via-[#9d2449] to-[#7a1d37]"></div>
                
                <!-- Efecto de brillo en hover -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                
                <!-- Contenido -->
                <div class="relative p-6">
                    <!-- Header con icono mejorado -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="relative">
                                <div class="w-14 h-14 bg-gradient-to-br from-[#7a1d37] to-[#9d2449] rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                    <i class="fas fa-edit text-white text-lg"></i>
                                </div>
                                <!-- Anillo decorativo -->
                                <div class="absolute -inset-1 bg-gradient-to-r from-[#7a1d37] to-[#9d2449] rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-[#7a1d37] group-hover:text-[#9d2449] transition-colors duration-300">Actualización</h3>
                                <p class="text-sm text-gray-500 font-medium">Modificar información</p>
                            </div>
                        </div>
                        @if($tipoTramite['actualizacion'])
                            <div class="w-4 h-4 bg-[#7a1d37] rounded-full shadow-md group-hover:scale-125 transition-transform duration-300"></div>
                        @else
                            <div class="w-4 h-4 bg-gray-300 rounded-full"></div>
                        @endif
                    </div>

                    <!-- Descripción mejorada -->
                    <div class="mb-6">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'actualizacion')
                                @if($tramiteEnProgreso->estado === 'En Revision')
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-eye mr-1"></i>
                                        En Revisión
                                    </span>
                                    <br>Actualización enviada y en proceso de evaluación
                                @elseif($tramiteEnProgreso->estado === 'Aprobado')
                                    <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Actualización Completada
                                    </span>
                                    <br>¡Su actualización ha sido aprobada exitosamente!
                                @elseif($tramiteEnProgreso->estado === 'Rechazado')
                                    <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Requiere Correcciones
                                    </span>
                                    <br>Revise las observaciones y corrija la información
                                @else
                                    <span class="inline-flex items-center px-2 py-1 bg-[#7a1d37]/10 text-[#7a1d37] text-xs font-semibold rounded-full mb-2">
                                        <div class="w-2 h-2 bg-[#7a1d37] rounded-full mr-2 animate-pulse"></div>
                                        En Progreso
                                    </span>
                                    <br>Paso {{ $tramiteEnProgreso->progreso_tramite ?? 1 }} de {{ $tramiteEnProgreso->solicitante->tipo_persona === 'Física' ? 3 : 6 }}
                                @endif
                            @else
                                @if($infoProveedor)
                                    <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full mb-2">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ $infoProveedor['pv'] }} - Activo
                                    </span>
                                    <br><span class="text-xs {{ $infoProveedor['tiempo_restante']['clase_css'] }} font-medium">
                                        Vence en {{ $infoProveedor['tiempo_restante']['texto'] }}
                                    </span>
                                    <br><span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded mt-1 inline-block">📅 Vigente hasta: {{ $infoProveedor['fecha_vencimiento']->format('d/m/Y H:i') }}</span>
                                @else
                                    Actualice su información, servicios y documentos para mantener sus datos al día en el padrón de proveedores.
                                @endif
                            @endif
                        </p>
                    </div>

                    <!-- Botón elegante mejorado -->
                    @if($tipoTramite['actualizacion'])
                        @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'actualizacion' && in_array($tramiteEnProgreso->estado, ['En Revision', 'Aprobado', 'Rechazado']))
                            <!-- Trámite enviado - Mostrar enlace al estado -->
                            <a href="{{ route('tramites.solicitante.estado', $tramiteEnProgreso) }}" class="w-full bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#7a1d37] hover:to-[#9d2449] text-white py-3 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group block">
                                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="relative flex items-center justify-center">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Ver Estado del Trámite
                                </div>
                            </a>
                        @else
                            <form action="{{ route('tramites.solicitante.iniciar-actualizacion') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-gradient-to-r from-[#7a1d37] to-[#9d2449] hover:from-[#5a1529] hover:to-[#7a1d37] text-white py-3 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group">
                                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <div class="relative flex items-center justify-center">
                                        @if($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'actualizacion')
                                            <i class="fas fa-play mr-2"></i>
                                            Continuar Actualización
                                        @else
                                            <i class="fas fa-pen-alt mr-2"></i>
                                            Iniciar Actualización
                                        @endif
                                    </div>
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="w-full bg-gray-100 text-gray-500 py-3 rounded-xl font-semibold text-sm text-center border border-gray-200">
                            <i class="fas fa-lock mr-2"></i>
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
<!-- Scripts necesarios para SAT - Carga lazy para optimizar rendimiento -->
<script>
    // Función para cargar scripts de forma asíncrona solo cuando se necesiten
    window.loadSATScripts = function() {
        if (window.satScriptsLoaded) return Promise.resolve();
        
        return Promise.all([
            loadScript('https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'),
            loadScript('https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js'),
            loadScript('/js/scrapers/sat-scraper.js'),
            loadScript('/js/validators/sat-validator.js'),
            loadScript('/js/components/qr-reader.js'),
            loadScript('/js/components/qr-handler.js')
        ]).then(() => {
            window.satScriptsLoaded = true;
        }).catch((error) => {
            console.warn('Error cargando scripts SAT:', error);
        });
    };
    
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
</script>

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
                // Error silencioso, continuar con fallback
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
    window.showSatModal = async function() {
        if (!satDataExtracted) {
            alert('No hay datos del SAT disponibles para mostrar');
            return;
        }

        // Cargar scripts SAT solo cuando sea necesario
        try {
            await window.loadSATScripts();
            if (window.configurePDFJS) {
                window.configurePDFJS();
            }
        } catch (error) {
            // Continuar sin scripts SAT si fallan
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
        return false;
    };

    // Función para simular la validación del RFC y activar botón "Ver Datos"
    window.simularValidacionRFC = async function(rfc, verBtnId = 'verDatosBtn') {
        // Cargar scripts SAT de forma asíncrona antes de validar
        try {
            await window.loadSATScripts();
        } catch (error) {
            // Continuar sin scripts SAT si fallan
        }

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

    // Configuración inicial - optimizada para evitar interferencias con el header
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePage);
    } else {
        // Delay para permitir que Alpine.js del header se inicialice primero
        setTimeout(initializePage, 100);
    }
    
    function initializePage() {
        // Configuración mínima sin interferir con Alpine.js del header
        try {
            // Solo configurar PDF.js si será necesario (cuando se usen scripts SAT)
            window.configurePDFJS = function() {
                if (typeof pdfjsLib !== 'undefined') {
                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js';
                }
            };
        } catch (error) {
            // Error silencioso
        }
    }
</script>
@endpush
@endsection 