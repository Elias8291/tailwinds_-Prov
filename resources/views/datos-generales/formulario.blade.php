@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg p-4 mb-5 border border-gray-200/80">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-2.5 shadow-md">
                    <svg class="w-6 h-6 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                        Datos Generales - Trámite #{{ $tramite->id }}
                    </h1>
                    <p class="text-xs text-gray-500">{{ ucfirst($datosTramite['tipo_tramite']) }}</p>
                </div>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <!-- Formulario -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80">
            <div class="p-6">
                @include('components.formularios.seccion-datos-generales', [
                    'datosTramite' => $datosTramite,
                    'datosSolicitante' => $datosSolicitante
                ])
            </div>
        </div>

        <!-- Botones de navegación -->
        <div class="mt-6 flex justify-between">
                            <a href="{{ route('tramites.solicitante.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Trámites
            </a>

            @if($tramite->progreso_tramite >= 2)
            <a href="{{ route('tramites.create', ['tipo_tramite' => strtolower($tramite->tipo_tramite), 'tramite' => $tramite->id, 'step' => 2]) }}" 
               class="inline-flex items-center px-4 py-2 bg-[#9d2449] text-white rounded-lg hover:bg-[#7a1d37] transition-colors">
                Continuar con Domicilio
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7"/>
                </svg>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection 