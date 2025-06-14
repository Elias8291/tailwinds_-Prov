@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg p-4 mb-5 border border-gray-200/80">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-2.5 shadow-md">
                        <svg class="w-6 h-6 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                            Revisión - Datos Generales
                        </h1>
                        <p class="text-xs text-gray-500">Trámite #{{ $tramite->id }} - {{ ucfirst($datosTramite['tipo_tramite']) }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ 
                        $tramite->estado === 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                        ($tramite->estado === 'Aprobado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') 
                    }}">
                        {{ $tramite->estado }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Información del Solicitante -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80 mb-6">
            <div class="bg-gradient-to-r from-[#9d2449]/8 to-[#7a1d37]/8 p-4 border-b border-[#9d2449]/10">
                <h2 class="text-lg font-semibold text-gray-800">Información del Solicitante</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- RFC -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">RFC</label>
                        <p class="text-lg font-mono font-bold text-gray-800">{{ $datosTramite['rfc'] }}</p>
                    </div>

                    <!-- Tipo de Persona -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tipo de Persona</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $datosTramite['tipo_persona'] }}</p>
                    </div>

                    @if($datosTramite['tipo_persona'] === 'Física' && !empty($datosTramite['curp']))
                    <!-- CURP -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">CURP</label>
                        <p class="text-lg font-mono font-bold text-gray-800">{{ $datosTramite['curp'] }}</p>
                    </div>
                    @endif

                    <!-- Nombre Completo -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nombre Completo</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $datosTramite['nombre_completo'] ?: 'No especificado' }}</p>
                    </div>

                    @if($datosTramite['tipo_persona'] === 'Moral')
                    <!-- Razón Social -->
                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Razón Social</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $datosTramite['razon_social'] }}</p>
                    </div>
                    @endif

                    <!-- Objeto Social -->
                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Objeto Social</label>
                        <p class="text-gray-800 leading-relaxed">{{ $datosTramite['objeto_social'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Contacto -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80 mb-6">
            <div class="bg-gradient-to-r from-[#9d2449]/8 to-[#7a1d37]/8 p-4 border-b border-[#9d2449]/10">
                <h2 class="text-lg font-semibold text-gray-800">Datos de Contacto</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre de Contacto -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nombre de Contacto</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $datosTramite['contacto_nombre'] }}</p>
                    </div>

                    <!-- Cargo -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cargo</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $datosTramite['contacto_cargo'] }}</p>
                    </div>

                    <!-- Correo -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Correo Electrónico</label>
                        <p class="text-lg font-mono text-gray-800">{{ $datosTramite['contacto_correo'] }}</p>
                    </div>

                    <!-- Teléfono -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Teléfono</label>
                        <p class="text-lg font-mono text-gray-800">{{ $datosTramite['contacto_telefono'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sector y Actividades -->
        @if(!empty($datosTramite['sector_id']) || !empty($actividades))
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80 mb-6">
            <div class="bg-gradient-to-r from-[#9d2449]/8 to-[#7a1d37]/8 p-4 border-b border-[#9d2449]/10">
                <h2 class="text-lg font-semibold text-gray-800">Sector y Actividades</h2>
            </div>
            <div class="p-6">
                @if(!empty($datosTramite['sector_id']))
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Sector</label>
                    <div class="bg-gray-50 rounded-lg p-4">
                        @php
                            $sector = $sectores->find($datosTramite['sector_id']);
                        @endphp
                        <p class="text-lg font-semibold text-gray-800">{{ $sector ? $sector->nombre : 'Sector no encontrado' }}</p>
                    </div>
                </div>
                @endif

                @if(!empty($actividades) && count($actividades) > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Actividades Seleccionadas</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($actividades as $actividad)
                        <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-[#9d2449]">
                            <p class="text-sm font-medium text-gray-800">{{ $actividad->nombre }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Botones de acción -->
        <div class="flex justify-between items-center">
            <a href="{{ route('revision.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Revisión
            </a>

            <div class="flex space-x-3">
                <a href="{{ route('datos-generales.mostrar', $tramite->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-[#9d2449] text-white rounded-lg hover:bg-[#7a1d37] transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar Datos
                </a>

                <a href="{{ route('revision.show', $tramite->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Ver Trámite Completo
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 