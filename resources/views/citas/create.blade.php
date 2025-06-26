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

<div class="min-h-screen w-full overflow-x-hidden">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-lg mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <form action="{{ route('citas.store') }}" method="POST" class="divide-y divide-gray-100">
                    @csrf

                    <!-- Encabezado -->
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-xl p-3 shadow-lg mb-3">
                                <i class="fas fa-calendar-plus text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent mb-2">
                                Nueva Cita
                            </h2>
                            <p class="text-sm text-gray-600">Complete los datos para agendar una nueva cita</p>
                        </div>
                    </div>

                    <!-- Información de la Cita -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-6">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                                Información de la Cita
                            </h3>
                            <div class="w-32 h-0.5 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mt-2 rounded-full opacity-50"></div>
                        </div>
                        <div class="w-full max-w-lg mx-auto space-y-5">
                            <!-- Fecha y Hora (separados) -->
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="flex-1">
                                    <label for="fecha" class="block text-xs font-medium text-gray-500 mb-1">
                                        Fecha <span class="text-[#9d2449]">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                            <i class="fas fa-calendar-alt text-base"></i>
                                        </span>
                                        <input type="date" name="fecha" id="fecha" required
                                               class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('fecha') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                               min="{{ now()->format('Y-m-d') }}"
                                               value="{{ old('fecha', old('fecha_hora') ? explode('T', old('fecha_hora'))[0] : '') }}"
                                               placeholder="Ej: 2024-07-01">
                                    </div>
                                    @error('fecha')
                                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                                <div class="flex-1">
                                    <label for="hora" class="block text-xs font-medium text-gray-500 mb-1">
                                        Hora <span class="text-[#9d2449]">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                            <i class="fas fa-clock text-base"></i>
                                        </span>
                                        <input type="time" name="hora" id="hora" required
                                               class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('hora') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                               value="{{ old('hora', old('fecha_hora') ? explode('T', old('fecha_hora'))[1] ?? '' : '') }}"
                                               placeholder="Ej: 09:30">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 ml-1">Selecciona una hora disponible</p>
                                    @error('hora')
                                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                            <!-- Motivo -->
                            <div>
                                <label for="motivo" class="block text-xs font-medium text-gray-500 mb-1">
                                    Motivo de la Cita <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-clipboard-list text-base"></i>
                                    </span>
                                    <input type="text" 
                                           name="motivo" 
                                           id="motivo" 
                                           required
                                           class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('motivo') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                           placeholder="Ej: Revisión de documentos"
                                           value="{{ old('motivo') }}">
                                </div>
                                @error('motivo')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Notas -->
                            <div>
                                <label for="notas" class="block text-xs font-medium text-gray-500 mb-1">
                                    Notas Adicionales
                                </label>
                                <div class="relative">
                                    <span class="absolute top-3 left-0 flex items-start pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-sticky-note text-base"></i>
                                    </span>
                                    <textarea name="notas" 
                                              id="notas" 
                                              rows="3"
                                              class="w-full pl-10 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 resize-none @error('notas') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                              placeholder="Agregue cualquier información adicional relevante">{{ old('notas') }}</textarea>
                                </div>
                                @error('notas')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="p-4">
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('citas.index') }}" 
                               class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-300">
                                <i class="fas fa-times mr-2 text-gray-400 group-hover:text-gray-600"></i>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900">Cancelar</span>
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg">
                                <i class="fas fa-save mr-2 text-white/90 group-hover:text-white"></i>
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Guardar Cita</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
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