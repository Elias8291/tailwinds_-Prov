@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Mensajes de éxito y error -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="font-medium mb-1">Por favor corrija los siguientes errores:</h4>
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Título del Trámite -->
        <div class="mb-6">
            <div class="bg-white rounded-2xl shadow-lg p-6 backdrop-blur-lg border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-3 shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent animate-shimmer"></div>
                        <svg class="w-6 h-6 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                            {{ isset($datosTramite['tipo_tramite']) ? ucfirst($datosTramite['tipo_tramite']) : 'Nuevo' }} Trámite
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Complete los datos generales para continuar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Solicitante (si existe) -->
        @if(isset($datosTramite['rfc']))
        <div class="mb-6 bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-lg border border-[#9d2449]/10 p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="bg-white/80 rounded-lg p-2">
                        <svg class="w-5 h-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500">Tipo de Persona</span>
                        <p class="text-sm font-semibold text-gray-800">{{ $datosTramite['tipo_persona'] ?? 'No especificado' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="bg-white/80 rounded-lg p-2">
                        <svg class="w-5 h-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500">RFC</span>
                        <p class="text-sm font-semibold text-gray-800 font-mono">{{ $datosTramite['rfc'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Formulario de Datos Generales -->
        <div x-data="{ 
            tipoPersona: '{{ $datosTramite['tipo_persona'] ?? '' }}',
            rfc: '{{ $datosTramite['rfc'] ?? '' }}',
            curp: '{{ $datosTramite['curp'] ?? '' }}',
            razonSocial: '{{ $datosTramite['nombre_completo'] ?? '' }}'
        }">
            @include('components.formularios.seccion-datos-generales', ['datosTramite' => $datosTramite])
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Estilos personalizados para formularios */
    .form-input,
    .form-select,
    .form-textarea {
        @apply w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base;
        @apply border border-gray-200 rounded-lg;
        @apply bg-white;
        @apply transition-all duration-200;
        @apply focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20;
        @apply placeholder-gray-400;
    }

    /* Contenedor de input con icono */
    .input-icon-container {
        @apply relative flex items-center;
    }

    .input-icon-container i {
        @apply absolute left-3 text-gray-400 pointer-events-none;
        @apply transition-colors duration-200;
    }

    .input-icon-container input,
    .input-icon-container select {
        @apply pl-9;
    }

    .input-icon-container:focus-within i {
        @apply text-[#9d2449];
    }

    /* Estilos para inputs */
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="number"],
    select,
    textarea {
        @apply form-input;
    }

    /* Estilos para labels */
    label {
        @apply text-sm font-medium text-gray-700;
        @apply mb-1 block;
    }

    /* Mejoras para móvil */
    @media (max-width: 640px) {
        input, select, textarea {
            font-size: 16px !important;
        }

        .form-section {
            @apply p-4 rounded-lg border border-gray-100;
            @apply bg-white shadow-sm;
        }
    }

    /* Animaciones */
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        @apply transform-gpu scale-[1.01];
    }

    /* Estilos para campos requeridos */
    .required::after {
        content: '*';
        @apply text-red-500 ml-1;
    }

    /* Estilos para mensajes de error */
    .error-message {
        @apply text-xs text-red-500 mt-1;
    }

    /* Estilos para grupos de campos */
    .form-group {
        @apply mb-4 last:mb-0;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }

    .animate-shimmer {
        animation: shimmer 3s infinite;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
@endsection 