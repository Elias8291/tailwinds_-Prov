@extends('layouts.app')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
[x-cloak] { display: none !important; }
</style>
@endpush

<div class="min-h-screen bg-gray-100 font-montserrat py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        @if(isset($tramite))
                            <h2 class="text-2xl font-bold text-[#9d2449]">
                                {{ ucfirst($tramite->tipo_tramite) }} - Paso {{ $paso_actual ?? 1 }} de {{ $total_pasos ?? 3 }}
                            </h2>
                            <p class="text-gray-600 text-sm">
                                RFC: {{ $solicitante->rfc ?? '' }} | {{ ($solicitante->tipo_persona ?? '') === 'Física' ? 'Persona Física' : 'Persona Moral' }}
                                @if(!empty($codigoPostalDomicilio))
                                    | CP: {{ $codigoPostalDomicilio }}
                                @endif
                            </p>
                        @else
                            <h2 class="text-2xl font-bold text-[#9d2449]">Nueva Cita</h2>
                            <p class="text-gray-600 text-sm">Complete los datos para agendar una nueva cita</p>
                        @endif
                    </div>
                    @if(isset($tramite))
                        <a href="{{ route('tramites.solicitante.index') }}" class="inline-flex items-center text-gray-500 hover:text-[#9d2449]">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver
                        </a>
                    @else
                        <a href="{{ route('citas.index') }}" class="inline-flex items-center text-gray-500 hover:text-[#9d2449]">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver
                        </a>
                    @endif
                </div>
                
                <!-- Indicador de progreso (solo para trámites) -->
                @if(isset($tramite))
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Progreso del trámite</span>
                        <span class="text-sm text-gray-600">{{ round((($paso_actual ?? 1) / ($total_pasos ?? 3)) * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] h-2 rounded-full transition-all duration-300" 
                             style="width: {{ (($paso_actual ?? 1) / ($total_pasos ?? 3)) * 100 }}%"></div>
                    </div>
                    
                    <!-- Indicadores de pasos compactos -->
                    <div class="flex justify-between mt-3">
                        @for($i = 1; $i <= ($total_pasos ?? 3); $i++)
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium
                                    {{ $i <= ($paso_actual ?? 1) ? 'bg-[#9d2449] text-white' : 'bg-gray-300 text-gray-600' }}
                                    {{ $i == ($paso_actual ?? 1) ? 'ring-2 ring-[#9d2449]/30' : '' }}">
                                    {{ $i }}
                                </div>
                                <span class="text-xs mt-1 text-center max-w-16
                                    {{ $i == ($paso_actual ?? 1) ? 'text-[#9d2449] font-medium' : 'text-gray-500' }}">
                                    @php
                                        $nombres_pasos = [
                                            1 => 'Datos',
                                            2 => 'Domicilio',
                                            3 => (($solicitante->tipo_persona ?? '') === 'Física') ? 'Docs' : 'Constitución',
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
                @endif
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

            <!-- Información del Domicilio (si existe) -->
            @if(isset($tramite) && !empty($codigoPostalDomicilio))
                <div class="mx-5 mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 rounded-lg p-2">
                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Domicilio Registrado</p>
                            <p class="text-xs text-blue-600 mt-1">
                                <strong>Código Postal:</strong> {{ $codigoPostalDomicilio }}
                                @if(!empty($datosDomicilio['estado']))
                                    | {{ $datosDomicilio['estado'] }}
                                @endif
                                @if(!empty($datosDomicilio['municipio']))
                                    - {{ $datosDomicilio['municipio'] }}
                                @endif
                            </p>
                            @if(!empty($datosDomicilio['calle']))
                                <p class="text-xs text-blue-600">
                                    {{ $datosDomicilio['calle'] }}
                                    @if(!empty($datosDomicilio['numero_exterior']))
                                        #{{ $datosDomicilio['numero_exterior'] }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Section -->
            @if(isset($tramite))
                <!-- Formulario para trámites por pasos -->
                <div class="p-6 space-y-6" 
                     x-data="{ 
                        currentStep: {{ $paso_actual ?? 1 }}, 
                        totalSteps: {{ $total_pasos ?? 3 }},
                        tipoPersona: '{{ $solicitante->tipo_persona ?? '' }}',
                        
                        // Función para navegar al siguiente paso
                        nextStep() {
                            console.log('🔄 nextStep() llamada desde Alpine.js, paso actual:', this.currentStep);
                            if (this.currentStep < this.totalSteps) {
                                this.currentStep++;
                                console.log('✅ Navegado al paso:', this.currentStep);
                            } else {
                                console.log('🏁 Ya estás en el último paso');
                            }
                        },
                        
                        // Función para navegar al paso anterior
                        prevStep() {
                            console.log('🔄 prevStep() llamada desde Alpine.js, paso actual:', this.currentStep);
                            if (this.currentStep > 1) {
                                this.currentStep--;
                                console.log('✅ Navegado al paso anterior:', this.currentStep);
                            } else {
                                console.log('🏁 Ya estás en el primer paso');
                            }
                        }
                     }"
                     @next-step="nextStep()"
                     @prev-step="prevStep()">
                     

                     
                    <!-- Sección 1: Datos Generales -->
                    <div x-show="currentStep === 1" x-cloak>
                        @include('components.formularios.seccion-datos-generales', [
                            'title' => 'Datos Generales',
                            'datosTramite' => $datosTramite ?? [],
                            'datosSolicitante' => $datosSolicitante ?? [],
                            'codigoPostalDomicilio' => $codigoPostalDomicilio ?? null,
                            'datosDomicilio' => $datosDomicilio ?? [],
                            'mostrar_navegacion' => false
                        ])
                    </div>
                    
                    <!-- Sección 2: Domicilio -->
                    <div x-show="currentStep === 2" x-cloak>
                        @include('components.formularios.seccion-domicilio', [
                            'title' => 'Datos de Domicilio',
                            'datosDomicilio' => $datosDomicilio ?? [],
                            'datosSolicitante' => $datosSolicitante ?? [],
                            'tramite' => $tramite ?? null,
                            'codigoPostalDomicilio' => $codigoPostalDomicilio ?? null,
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
                    <div x-show="currentStep === 3 && tipoPersona === 'Física'" x-cloak @previous-step="prevStep()">
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
                    <div x-show="currentStep === 5 && tipoPersona === 'Moral'" x-cloak @next-step="nextStep()" @previous-step="prevStep()">
                        @include('components.formularios.seccion-apoderado', [
                            'title' => 'Apoderado Legal',
                            'tramite' => $tramite ?? null,
                            'datosApoderado' => isset($datosApoderado) ? $datosApoderado : [],
                            'mostrar_navegacion' => false
                        ])
                    </div>
                    
                    <!-- Sección 6: Documentos (Solo Persona Moral) -->
                    <div x-show="currentStep === 6 && tipoPersona === 'Moral'" x-cloak @previous-step="prevStep()">
                        @include('components.formularios.seccion-documentos', [
                            'title' => 'Documentos Requeridos',
                            'tramite' => $tramite ?? null,
                            'mostrar_navegacion' => false
                        ])
                    </div>
                </div>
            @else
                <!-- Formulario original para citas -->
                <form action="{{ route('citas.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Datos Generales y Domicilio -->
                    @include('components.formularios.seccion-datos-generales', [
                        'datosTramite' => [],
                        'datosSolicitante' => isset($solicitante) ? [
                            'rfc' => $solicitante->rfc,
                            'curp' => $solicitante->curp,
                            'tipo_persona' => $solicitante->tipo_persona,
                            'nombre_completo' => $solicitante->nombre_completo,
                            'razon_social' => $solicitante->razon_social,
                            'giro' => $solicitante->giro
                        ] : [],
                        'codigoPostalDomicilio' => $codigoPostalDomicilio ?? null,
                        'datosDomicilio' => $datosDomicilio ?? []
                    ])

                <!-- Información de Domicilio (Solo vista) -->
                @if(!empty($datosDomicilio['codigo_postal']))
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <div class="bg-blue-100 rounded-lg p-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Domicilio Registrado</p>
                            <p class="text-xs text-blue-600 mt-1">
                                CP {{ $datosDomicilio['codigo_postal'] }}
                                @if(!empty($datosDomicilio['estado']))
                                    - {{ $datosDomicilio['estado'] }}
                                @endif
                                @if(!empty($datosDomicilio['municipio']))
                                    - {{ $datosDomicilio['municipio'] }}
                                @endif
                                @if(!empty($datosDomicilio['colonia']))
                                    - {{ $datosDomicilio['colonia'] }}
                                @endif
                            </p>
                            @if(!empty($datosDomicilio['calle']))
                                <p class="text-xs text-blue-600">
                                    {{ $datosDomicilio['calle'] }}
                                    @if(!empty($datosDomicilio['numero_exterior']))
                                        #{{ $datosDomicilio['numero_exterior'] }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Fecha y Hora -->
                <div>
                    <label for="fecha_hora" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha y Hora <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="fecha_hora" id="fecha_hora" 
                           class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 @error('fecha_hora') border-red-500 @enderror"
                           value="{{ old('fecha_hora') }}"
                           min="{{ now()->format('Y-m-d\TH:i') }}">
                    @error('fecha_hora')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Motivo -->
                <div>
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo de la Cita <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="motivo" id="motivo" 
                           class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 @error('motivo') border-red-500 @enderror"
                           value="{{ old('motivo') }}"
                           placeholder="Ej: Revisión de documentos">
                    @error('motivo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notas -->
                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-1">
                        Notas Adicionales
                    </label>
                    <textarea name="notas" id="notas" rows="4" 
                              class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 @error('notas') border-red-500 @enderror"
                              placeholder="Agregue cualquier información adicional relevante">{{ old('notas') }}</textarea>
                    @error('notas')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center bg-[#9d2449] text-white px-6 py-2 rounded-xl shadow-lg hover:bg-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-[#9d2449]/20">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Agendar Cita
                        </button>
                    </div>
                </form>
            @endif


        </div>
    </div>
</div>

@if(isset($tramite))
@push('scripts')
<script>
// Asegurar que las funciones estén disponibles inmediatamente
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM cargado en create.blade.php');
    
    // Debug: verificar estado de Alpine.js
    if (typeof Alpine !== 'undefined') {
        console.log('✅ Alpine.js disponible en create.blade.php');
    } else {
        console.log('❌ Alpine.js NO disponible en create.blade.php');
    }
    
    // Debug: verificar contenedor Alpine.js
    const container = document.querySelector('[x-data*="currentStep"]');
    if (container) {
        console.log('✅ Contenedor Alpine.js encontrado en create.blade.php');
        console.log('📊 Contenedor:', container);
    } else {
        console.log('❌ Contenedor Alpine.js NO encontrado en create.blade.php');
    }
});

// Funciones de navegación globales para integración con componentes
window.navegarSiguiente = function() {
    console.log('🚀 navegarSiguiente() llamada desde create.blade.php');
    
    // Buscar contenedor Alpine.js específico
    const alpineContainer = document.querySelector('[x-data*="currentStep"]');
    
    if (alpineContainer) {
        console.log('📦 Contenedor Alpine.js encontrado');
        
        // Método 1: Usar nextStep() si está disponible
        try {
            if (typeof Alpine !== 'undefined') {
                const alpineData = Alpine.$data(alpineContainer);
                if (alpineData && typeof alpineData.nextStep === 'function') {
                    console.log('✅ Usando nextStep() de Alpine.js');
                    alpineData.nextStep();
                    return;
                }
            }
        } catch (error) {
            console.error('❌ Error usando nextStep():', error);
        }
        
        // Método 2: Disparar evento next-step
        try {
            console.log('📡 Disparando evento next-step');
            alpineContainer.dispatchEvent(new CustomEvent('next-step'));
            return;
        } catch (error) {
            console.error('❌ Error disparando evento:', error);
        }
        
        // Método 3: Manipular currentStep directamente
        try {
            if (typeof Alpine !== 'undefined') {
                const alpineData = Alpine.$data(alpineContainer);
                if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                    if (alpineData.currentStep < alpineData.totalSteps) {
                        alpineData.currentStep++;
                        console.log('✅ Navegado directamente al paso:', alpineData.currentStep);
                        return;
                    }
                }
            }
        } catch (error) {
            console.error('❌ Error manipulando currentStep:', error);
        }
    }
    
    console.error('❌ No se pudo navegar: contenedor Alpine.js no encontrado');
};

// Función para navegar al paso anterior usando Alpine.js
window.navegarAnterior = function() {
    console.log('🔙 navegarAnterior() llamada desde create.blade.php');
    
    const alpineContainer = document.querySelector('[x-data]');
    
    if (alpineContainer) {
        try {
            if (typeof Alpine !== 'undefined') {
                const alpineData = Alpine.$data(alpineContainer);
                if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                    alpineData.currentStep--;
                    console.log('✅ Navegado al paso anterior:', alpineData.currentStep);
                    return;
                }
            }
        } catch (error) {
            console.error('❌ Error al navegar al paso anterior:', error);
        }
    }
};

// Función para finalizar el trámite
function finalizarTramite() {
    if (confirm('¿Está seguro de que desea finalizar el trámite? Una vez finalizado, no podrá realizar más cambios.')) {
        // Enviar a finalizar usando una ruta específica
        window.location.href = '{{ route("tramites.solicitante.index") }}';
    }
}
</script>
@endpush
@endif

@endsection 