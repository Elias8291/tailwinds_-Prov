@extends('layouts.app')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
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
                        <div x-show="currentStep === 3 && tipoPersona === 'Física'" x-cloak>
                            @include('components.formularios.seccion-documentos', [
                                'title' => 'Documentos Requeridos',
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
                        <div x-show="currentStep === 5 && tipoPersona === 'Moral'" x-cloak>
                            @include('components.formularios.seccion-apoderado', [
                                'title' => 'Apoderado Legal',
                                'mostrar_navegacion' => false
                            ])
                        </div>
                        
                        <!-- Sección 6: Documentos (Solo Persona Moral) -->
                        <div x-show="currentStep === 6 && tipoPersona === 'Moral'" x-cloak>
                            @include('components.formularios.seccion-documentos', [
                                'title' => 'Documentos Requeridos',
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
                            Primera inscripción al Padrón de Proveedores del Estado
                        @endif
                    </p>

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
                                <p class="text-xs text-gray-600">Próximo a vencer</p>
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
                            Renueva tu registro antes del vencimiento (7 días)
                        @endif
                    </p>

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
                            Actualiza información, servicios y documentos
                        @endif
                    </p>

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
</style>
@endpush

@push('scripts')
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

    // Carga instantánea - sin retrasos
    document.addEventListener('DOMContentLoaded', function() {
        // Las tarjetas cargan inmediatamente sin animación de retraso
    });
</script>
@endpush
@endsection 