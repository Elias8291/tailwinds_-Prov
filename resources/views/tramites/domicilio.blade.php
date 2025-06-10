@extends('layouts.app')

@section('title', 'Domicilio - Trámite')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8" x-data="tramiteData()">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Barra de progreso -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900">{{ ucfirst($datosDomicilio['tipo_tramite'] ?? 'inscripcion') }}</h1>
                <span class="text-sm text-gray-500">Paso 2 de 5</span>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] h-2 rounded-full transition-all duration-500" style="width: 40%"></div>
            </div>
            
            <div class="flex justify-between mt-2 text-xs text-gray-500">
                <span>Datos Generales</span>
                <span class="font-medium text-[#9d2449]">Domicilio</span>
                <span>Documentos</span>
                <span>Revisión</span>
                <span>Finalización</span>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulario de domicilio -->
        <form action="{{ route('tramites.guardar-domicilio') }}" method="POST" class="space-y-8">
            @csrf
            <input type="hidden" name="tramite_id" value="{{ $datosDomicilio['tramite_id'] }}">
            <input type="hidden" name="tipo_tramite" value="{{ $datosDomicilio['tipo_tramite'] }}">
            
            <x-formularios.seccion-domicilio 
                title="Domicilio"
                :datosDomicilio="$datosDomicilio" />

            <!-- Navegación -->
            <div class="flex justify-between pt-6 border-t border-gray-100">
                <!-- Botón Anterior -->
                <a href="{{ route('tramites.create', ['tipo_tramite' => $datosDomicilio['tipo_tramite'], 'tramite' => $datosDomicilio['tramite_id']]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl shadow-lg hover:bg-gray-300 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-gray-300/30 gap-2">
                    <i class="fas fa-arrow-left text-sm"></i>
                    <span>Anterior</span>
                </a>

                <!-- Botón Guardar y Continuar -->
                <button type="submit" 
                        onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Guardando...'; this.form.submit();"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#9d2449] to-[#7a1d37] text-white font-semibold rounded-xl shadow-lg hover:from-[#7a1d37] hover:to-[#9d2449] transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#9d2449]/30 gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save text-sm"></i>
                    <span>Guardar y Continuar</span>
                    <i class="fas fa-arrow-right text-sm"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function tramiteData() {
    return {
        tramiteId: {{ $datosDomicilio['tramite_id'] ?? 'null' }},
        tipoTramite: '{{ $datosDomicilio['tipo_tramite'] ?? 'inscripcion' }}'
    };
}
</script>
@endsection 