@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50/30 font-montserrat py-8">
    <!-- Contenedor principal -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        @if($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'inscripcion')
                            <span class="text-[#9d2449] font-medium">En progreso:</span> Paso {{ $tramiteEnProgreso->paso_actual ?? 1 }} de {{ $tramiteEnProgreso->tipo_persona === 'Física' ? 3 : 6 }}
                        @else
                            Primera inscripción al Padrón de Proveedores del Estado
                        @endif
                    </p>

                    <!-- Botón elegante -->
                    @if($tipoTramite['inscripcion'])
                        <form action="{{ route('tramites.solicitante.iniciar-inscripcion') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-gradient-to-r from-[#9d2449] to-[#b8396b] text-white py-2.5 rounded-xl font-medium text-sm shadow-lg hover:shadow-xl transition-all duration-200">
                                @if($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'inscripcion')
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
                        @if($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'renovacion')
                            <span class="text-[#c1437a] font-medium">En progreso:</span> Paso {{ $tramiteEnProgreso->paso_actual ?? 1 }} de {{ $tramiteEnProgreso->tipo_persona === 'Física' ? 3 : 6 }}
                        @else
                            Renueva tu registro antes del vencimiento (7 días)
                        @endif
                    </p>

                    <!-- Botón elegante -->
                    @if($tipoTramite['renovacion'])
                        <form action="{{ route('tramites.solicitante.iniciar-renovacion') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-gradient-to-r from-[#c1437a] to-[#9d2449] text-white py-2.5 rounded-xl font-medium text-sm shadow-lg hover:shadow-xl transition-all duration-200">
                                @if($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'renovacion')
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
                        @if($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'actualizacion')
                            <span class="text-[#7a1d37] font-medium">En progreso:</span> Paso {{ $tramiteEnProgreso->paso_actual ?? 1 }} de {{ $tramiteEnProgreso->tipo_persona === 'Física' ? 3 : 6 }}
                        @else
                            Actualiza información, servicios y documentos
                        @endif
                    </p>

                    <!-- Botón elegante -->
                    @if($tipoTramite['actualizacion'])
                        <form action="{{ route('tramites.solicitante.iniciar-actualizacion') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-gradient-to-r from-[#7a1d37] to-[#9d2449] text-white py-2.5 rounded-xl font-medium text-sm shadow-lg hover:shadow-xl transition-all duration-200">
                                @if($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'actualizacion')
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
    </div>
</div>

@push('scripts')
<script>
    // Carga instantánea - sin retrasos
    document.addEventListener('DOMContentLoaded', function() {
        // Las tarjetas cargan inmediatamente sin animación de retraso
        console.log('Módulo de trámites cargado');
    });
</script>
@endpush
@endsection 