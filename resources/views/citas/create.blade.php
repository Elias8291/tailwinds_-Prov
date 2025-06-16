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

<div class="min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                            <i class="fas fa-calendar-plus text-2xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-[#B4325E]">Nueva Cita</h2>
                            <p class="text-sm text-gray-500">Complete los datos para agendar una nueva cita</p>
                        </div>
                    </div>
                    <a href="{{ route('citas.index') }}" class="inline-flex items-center text-gray-500 hover:text-[#B4325E]">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <form action="{{ route('citas.store') }}" method="POST" class="p-6 space-y-6 bg-white">
                @csrf

                <!-- Fecha y Hora -->
                <div>
                    <label for="fecha_hora" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha y Hora <span class="text-[#B4325E]">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fas fa-clock"></i>
                        </div>
                        <input type="datetime-local" 
                               name="fecha_hora" 
                               id="fecha_hora" 
                               required
                               class="w-full pl-10 pr-4 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 focus:outline-none transition-all duration-300 hover:border-[#B4325E]/50 @error('fecha_hora') border-red-300 hover:border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               value="{{ old('fecha_hora') }}">
                    </div>
                    @error('fecha_hora')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Motivo -->
                <div>
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo de la Cita <span class="text-[#B4325E]">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <input type="text" 
                               name="motivo" 
                               id="motivo" 
                               required
                               class="w-full pl-10 pr-4 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 focus:outline-none transition-all duration-300 hover:border-[#B4325E]/50 @error('motivo') border-red-300 hover:border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
                               placeholder="Ej: Revisión de documentos"
                               value="{{ old('motivo') }}">
                    </div>
                    @error('motivo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notas -->
                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-2">
                        Notas Adicionales
                    </label>
                    <div class="relative">
                        <div class="absolute top-3 left-0 flex items-start pl-3 pointer-events-none text-gray-400">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                        <textarea name="notas" 
                                  id="notas" 
                                  rows="4"
                                  class="w-full pl-10 pr-4 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 focus:outline-none transition-all duration-300 hover:border-[#B4325E]/50 @error('notas') border-red-300 hover:border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
                                  placeholder="Agregue cualquier información adicional relevante">{{ old('notas') }}</textarea>
                    </div>
                    @error('notas')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('citas.index') }}" 
                       class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-xl hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300 shadow-md hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Cita
                    </button>
                </div>
            </form>
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
    
    // Verificar si el trámite ya está completado y redirigir
    verificarEstadoTramite();
});

// Función para verificar si el trámite ya está enviado y redirigir al estado
async function verificarEstadoTramite() {
    try {
        const response = await fetch('/tramites-solicitante/verificar-estado', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            
            // Si el trámite está en revisión o finalizado, redirigir al estado
            if (data.success && data.tramite) {
                const tipoPersona = data.tramite.solicitante?.tipo_persona || 'Física';
                const progresoRequerido = tipoPersona === 'Moral' ? 6 : 3;
                
                // Verificar si está completado según el tipo de persona
                const estaCompletado = data.tramite.progreso_tramite >= progresoRequerido;
                const estaEnEstadoFinal = ['En Revision', 'Aprobado', 'Rechazado'].includes(data.tramite.estado);
                
                if (estaCompletado || estaEnEstadoFinal) {
                    console.log('🔄 Trámite ya enviado, redirigiendo a estado...', {
                        tramite: data.tramite,
                        tipo_persona: tipoPersona,
                        progreso: data.tramite.progreso_tramite,
                        requerido: progresoRequerido,
                        estado: data.tramite.estado
                    });
                    window.location.href = `/tramites-solicitante/estado/${data.tramite.id}`;
                }
            }
        }
    } catch (error) {
        console.log('ℹ️ No se pudo verificar el estado del trámite (normal si no hay trámite activo)');
    }
}

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
async function finalizarTramite() {
    if (!confirm('¿Está seguro de que desea finalizar el trámite? Una vez finalizado, no podrá realizar más cambios.')) {
        return;
    }
    
    try {
        // Obtener tramite_id del componente de documentos
        const documentosContainer = document.querySelector('[x-data*="documentosData"]');
        let tramiteId = null;
        
        if (documentosContainer && typeof Alpine !== 'undefined') {
            const alpineData = Alpine.$data(documentosContainer);
            tramiteId = alpineData ? alpineData.tramiteId : null;
        }
        
        if (tramiteId) {
            // Llamar a la función finalizarTramite del componente de documentos
            if (documentosContainer && typeof Alpine !== 'undefined') {
                const alpineData = Alpine.$data(documentosContainer);
                if (alpineData && typeof alpineData.finalizarTramite === 'function') {
                    await alpineData.finalizarTramite();
                    return;
                }
            }
        }
        
        // Fallback: redirigir al índice
        window.location.href = '{{ route("tramites.solicitante.index") }}';
        
    } catch (error) {
        console.error('Error al finalizar trámite:', error);
        // Fallback en caso de error
        window.location.href = '{{ route("tramites.solicitante.index") }}';
    }
}
</script>
@endpush
@endif

@endsection 