@extends('layouts.app')

@section('content')
<div class="max-w-[1400px] mx-auto px-6 py-6 space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <i class="fas fa-home w-4 h-4 mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right w-3 h-3 text-gray-400 mx-1"></i>
                    <a href="{{ route('revision.index') }}" class="ml-1 text-sm font-medium text-gray-600 hover:text-gray-900 md:ml-2 transition-colors duration-200">Revisiones</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right w-3 h-3 text-gray-400 mx-1"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Trámite #{{ $tramite->id ?? '' }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Principal -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-file-check text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Proceso de Revisión</h1>
                    <p class="text-gray-600">Selecciona la etapa de revisión que deseas realizar</p>
                    <div class="mt-1 flex items-center space-x-3 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-hashtag mr-1"></i>
                            {{ $tramite->id }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-user mr-1"></i>
                            {{ $tramite->solicitante->rfc ?? 'N/A' }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-briefcase mr-1"></i>
                            {{ ucfirst($tramite->tipo_tramite) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i class="fas fa-clock"></i>
                <span>{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Selector de Tipo de Revisión -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revisión Digital -->
        <div class="group">
            <a href="{{ route('revision.digital', $tramite) }}" 
               class="block bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                
                <!-- Header con badge -->
                <div class="relative p-6 pb-4">
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-list-check mr-1"></i>
                            Paso 1
                        </span>
                    </div>

                    <div class="flex items-start space-x-4">
                        <!-- Icono -->
                        <div class="w-16 h-16 rounded-xl bg-blue-100 flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-300 flex-shrink-0">
                            <i class="fas fa-laptop text-2xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                        </div>

                        <!-- Contenido -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-700 transition-colors duration-300">
                                Revisión Digital
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                Comparación y verificación de la información entre los formularios completados y los documentos subidos.
                            </p>

                            <!-- Características compactas -->
                            <div class="space-y-2">
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-balance-scale w-4 h-4 text-green-500 mr-2"></i>
                                    <span>Comparación de formularios vs documentos</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-search w-4 h-4 text-blue-500 mr-2"></i>
                                    <span>Verificación de datos ingresados</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-check-circle w-4 h-4 text-orange-500 mr-2"></i>
                                    <span>Validación de completitud</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer del botón -->
                <div class="px-6 py-4 bg-gray-50 group-hover:bg-blue-50 transition-colors duration-300">
                    <div class="flex items-center justify-center text-blue-600 group-hover:text-blue-700 font-semibold">
                        <i class="fas fa-play mr-2"></i>
                        Iniciar Revisión Digital
                    </div>
                </div>
            </a>
        </div>

        <!-- Cotejo Presencial -->
        <div class="group">
            <a href="{{ route('revision.presencial', $tramite) }}" 
               class="block bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                
                <!-- Header con badge -->
                <div class="relative p-6 pb-4">
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 group-hover:bg-red-500 group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Obligatorio
                        </span>
                    </div>

                    <div class="flex items-start space-x-4">
                        <!-- Icono -->
                        <div class="w-16 h-16 rounded-xl bg-amber-100 flex items-center justify-center group-hover:bg-amber-500 transition-colors duration-300 flex-shrink-0">
                            <i class="fas fa-users text-2xl text-amber-600 group-hover:text-white transition-colors duration-300"></i>
                        </div>

                        <!-- Contenido -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-amber-700 transition-colors duration-300">
                                Cotejo Presencial
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                Verificación física obligatoria de documentos originales para confirmar su autenticidad. Requerido después de la revisión digital.
                            </p>

                            <!-- Características compactas -->
                            <div class="space-y-2">
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-shield-check w-4 h-4 text-green-500 mr-2"></i>
                                    <span>Verificación de documentos reales</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-handshake w-4 h-4 text-blue-500 mr-2"></i>
                                    <span>Cotejo presencial obligatorio</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-file-signature w-4 h-4 text-orange-500 mr-2"></i>
                                    <span>Confirmación de autenticidad</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer del botón -->
                <div class="px-6 py-4 bg-gray-50 group-hover:bg-amber-50 transition-colors duration-300">
                    <div class="flex items-center justify-center text-amber-600 group-hover:text-amber-700 font-semibold">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Programar Cotejo Presencial
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Información adicional compacta -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-4 border border-gray-100">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-1">Proceso de Revisión</h3>
                <p class="text-gray-600 text-sm">
                    <strong>1. Revisión Digital:</strong> Comparación de formularios contra documentos subidos para verificar consistencia de datos. 
                    <strong>2. Cotejo Presencial:</strong> Verificación física obligatoria de documentos originales para confirmar su autenticidad y completar el proceso.
                </p>
            </div>
        </div>
    </div>

    <!-- Proceso paso a paso -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-route text-white text-sm"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Flujo del Proceso</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="flex items-center space-x-2">
                <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                <span class="text-gray-700">Revisión Digital (Comparación)</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                <span class="text-gray-700">Cotejo Presencial (Obligatorio)</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                <span class="text-gray-700">Proceso Completado</span>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
@endsection