@extends('layouts.app')

@section('title', 'Revisión de Trámite')

@section('content')
<div class="min-h-screen">
    <!-- Modal para ver documentos -->
    <div id="documento-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="cerrarModalDocumento()"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-[#B4325E] to-[#93264B]">
                    <h3 class="text-xl font-semibold text-white" id="documento-modal-title">
                        Visualización de Documento
                    </h3>
                    <button type="button" class="text-white hover:text-gray-200 transition-colors" onclick="cerrarModalDocumento()">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-hidden">
                    <iframe id="documento-iframe" class="w-full h-[70vh]" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver mapa completo -->
    <div id="mapa-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" onclick="cerrarModalMapa()"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-7xl max-h-[95vh] overflow-hidden border-4 border-green-200">
                <!-- Header mejorado -->
                <div class="p-6 border-b bg-gradient-to-r from-green-600 via-teal-600 to-emerald-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">🗺️ Verificación Geográfica Completa</h3>
                                <p class="text-green-100 text-sm mt-1">Análisis detallado del domicilio y calles aledañas</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Controles del mapa -->
                            <div class="flex items-center space-x-2 bg-white bg-opacity-20 rounded-lg px-3 py-2">
                                <button onclick="cambiarVistaMapaModal('roadmap')" 
                                        class="px-2 py-1 bg-white bg-opacity-30 hover:bg-opacity-50 rounded text-white text-xs font-medium transition-all">
                                    🗺️ Mapa
                                </button>
                                <button onclick="cambiarVistaMapaModal('satellite')" 
                                        class="px-2 py-1 bg-white bg-opacity-30 hover:bg-opacity-50 rounded text-white text-xs font-medium transition-all">
                                    🛰️ Satélite
                                </button>
                                <button onclick="cambiarVistaMapaModal('hybrid')" 
                                        class="px-2 py-1 bg-white bg-opacity-30 hover:bg-opacity-50 rounded text-white text-xs font-medium transition-all">
                                    🔗 Híbrido
                                </button>
                            </div>
                            <button type="button" class="text-white hover:text-green-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" onclick="cerrarModalMapa()">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                </div>
            </div>
                
                <!-- Información de la dirección en header -->
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-green-50 border-b border-green-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">📍 Dirección Completa:</p>
                                <p id="direccion-modal-completa" class="text-lg font-semibold text-gray-900">Cargando dirección...</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="copiarDireccion()" 
                                    class="flex items-center space-x-2 px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-all text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span>Copiar</span>
                            </button>
                            <button onclick="abrirEnGoogleMaps()" 
                                    class="flex items-center space-x-2 px-3 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition-all text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                <span>Google Maps</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Contenedor del mapa principal -->
                <div class="flex-1 overflow-hidden" style="height: 75vh;">
                    <div id="mapa-domicilio" class="w-full h-full relative">
                        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-green-50 to-teal-50 z-10">
                            <div class="text-center">
                                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mb-4"></div>
                                <p class="text-lg font-medium text-gray-700">Cargando mapa detallado...</p>
                                <p class="text-sm text-gray-500 mt-2">Preparando vista de calles aledañas</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer con información adicional -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6 text-sm text-gray-600">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span>Ubicación exacta</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span>Calles principales</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span>Puntos de referencia</span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400">
                            🗺️ Powered by Google Maps
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Principal Mejorado -->
    <div class="max-w-[1800px] mx-auto px-8 py-6">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Información del Trámite -->
                <div class="flex items-center space-x-6">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                        <svg class="w-8 h-8 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Revisión de Trámite
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">Proceso de validación y verificación de documentos</p>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mt-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] rounded-full"></div>
                                <span class="font-medium">ID: <span class="text-[#B4325E] font-mono font-semibold">#{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}</span></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="font-medium">{{ $tramite->tipo_tramite ?? 'Inscripción' }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                <span class="font-medium">RFC: <span class="font-mono font-semibold text-gray-700">{{ $datosSolicitante['rfc'] ?? 'Sin RFC' }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Estado y Acciones -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="px-4 py-2 rounded-xl shadow-sm 
                        @if($tramite->estado === 'Aprobado') bg-green-100 text-green-800 border border-green-200
                        @elseif($tramite->estado === 'Rechazado') bg-red-100 text-red-800 border border-red-200
                        @elseif($tramite->estado === 'En Revision') bg-blue-100 text-blue-800 border border-blue-200
                        @else bg-yellow-100 text-yellow-800 border border-yellow-200 @endif">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full
                                @if($tramite->estado === 'Aprobado') bg-green-500
                                @elseif($tramite->estado === 'Rechazado') bg-red-500
                                @elseif($tramite->estado === 'En Revision') bg-blue-500
                                @else bg-yellow-500 @endif"></div>
                            <span class="text-sm font-medium">{{ $tramite->estado ?? 'Pendiente' }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('revision.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transform hover:scale-105 transition-all duration-300 hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación de Secciones Compacta -->
    <div class="max-w-[1800px] mx-auto px-8 py-4">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                    Secciones de Revisión
                </h3>
                <div class="text-sm text-gray-500">
                    Seleccione una sección para revisar
                </div>
            </div>
            <div class="flex flex-wrap gap-2" id="secciones-container">
                <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="general">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        <span>General</span>
                    </div>
                </button>
                <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="datos-generales">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        <span>Datos</span>
                    </div>
                </button>
                <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="domicilio">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        <span>Domicilio</span>
                    </div>
                </button>
                @if($tramite->solicitante && strtolower($tramite->solicitante->tipo_persona) === 'moral')
                    <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="constitucion">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            <span>Constitución</span>
                        </div>
                    </button>
                    <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="accionistas">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            <span>Accionistas</span>
                        </div>
                    </button>
                    <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="apoderado">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            <span>Apoderado</span>
                        </div>
                    </button>
                    <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="personal">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            <span>Personal</span>
                        </div>
                    </button>
                @endif
                <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="documentos">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        <span>Documentos</span>
                    </div>
                </button>
                <!-- Sección de Decisión Final -->
                @if(auth()->user()->can('revision-tramites.aprobar'))
                <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="decision-final">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                        <span>Decisión Final</span>
                    </div>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Contenido Principal con Sistema Redimensionable -->
    <div class="max-w-[1800px] mx-auto px-8 pb-6">
        <div class="flex gap-6" id="main-container">

                        <!-- Formulario de Datos (Redimensionable) -->
            <main class="flex-1" id="formulario-container">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Datos del Formulario</h3>
                            <p class="text-sm text-gray-500 mt-1">Información proporcionada por el solicitante</p>
                        </div>
                        <!-- Controles de Revisión por Sección -->
                        <div class="flex items-center space-x-3" id="controles-seccion">
                            <div class="text-sm text-gray-500">
                                Sección: <span id="seccion-actual" class="font-medium text-gray-900">General</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8" id="contenido-formulario">
                    <!-- Contenido General por Defecto -->
                    <div id="contenido-general">
                        <!-- Información del Trámite -->
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">

                            <!-- Información del Trámite -->
                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Columna Izquierda - Información del Solicitante -->
                                    <div class="space-y-6">
                                        <div class="border-l-4 border-[#B4325E] pl-4">
                                            <h6 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Información del Solicitante</h6>
                                        </div>
                                        
                                        <div class="space-y-4">
                                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 font-medium">Tipo de Persona</p>
                                                            <p class="font-semibold text-gray-900">{{ $datosSolicitante['tipo_persona'] ?? 'No definido' }}</p>
                                                        </div>
                                                    </div>
                                                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ ($datosSolicitante['tipo_persona'] ?? '') === 'Física' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                                        {{ ($datosSolicitante['tipo_persona'] ?? '') === 'Física' ? '👤 Física' : '🏢 Moral' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-xs text-gray-500 font-medium">RFC</p>
                                                        <p class="font-mono font-bold text-gray-900 text-lg">{{ $datosSolicitante['rfc'] ?? 'Sin RFC' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(($datosSolicitante['tipo_persona'] ?? '') === 'Física')
                                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                                                    <div class="flex items-center">
                                                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1">
                                                            <p class="text-xs text-gray-500 font-medium">Nombre Completo</p>
                                                            <p class="font-semibold text-gray-900">{{ $datosSolicitante['nombre_completo'] ?? 'Sin nombre' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if(!empty($datosSolicitante['curp']))
                                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                                                        <div class="flex items-center">
                                                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                                                </svg>
                                                            </div>
                                                            <div class="flex-1">
                                                                <p class="text-xs text-gray-500 font-medium">CURP</p>
                                                                <p class="font-mono font-semibold text-gray-900">{{ $datosSolicitante['curp'] }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                                                    <div class="flex items-center">
                                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1">
                                                            <p class="text-xs text-gray-500 font-medium">Razón Social</p>
                                                            <p class="font-semibold text-gray-900">{{ $datosSolicitante['razon_social'] ?? 'Sin razón social' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Columna Derecha - Estado del Trámite -->
                                    <div class="space-y-6">
                                        <div class="border-l-4 border-blue-500 pl-4">
                                            <h6 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Estado del Trámite</h6>
                                        </div>

                                        <div class="space-y-4">
                                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 font-medium">Estado Actual</p>
                                                            <p class="font-semibold text-gray-900">{{ $tramite->estado ?? 'Pendiente' }}</p>
                                                        </div>
                                                    </div>
                                                    <span class="px-3 py-1 text-xs font-medium rounded-full 
                                                        @if(($tramite->estado ?? '') === 'Aprobado') bg-green-100 text-green-800
                                                        @elseif(($tramite->estado ?? '') === 'Rechazado') bg-red-100 text-red-800
                                                        @elseif(($tramite->estado ?? '') === 'En Revision') bg-blue-100 text-blue-800
                                                        @else bg-yellow-100 text-yellow-800 @endif">
                                                        @if(($tramite->estado ?? '') === 'Aprobado') ✅ Aprobado
                                                        @elseif(($tramite->estado ?? '') === 'Rechazado') ❌ Rechazado
                                                        @elseif(($tramite->estado ?? '') === 'En Revision') 🔍 En Revisión
                                                        @else ⏳ Pendiente @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-xs text-gray-500 font-medium">Fecha de Inicio</p>
                                                        <p class="font-semibold text-gray-900">{{ $tramite->created_at ? $tramite->created_at->format('d/m/Y H:i') : 'No definida' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-xs text-gray-500 font-medium">Progreso del Trámite</p>
                                                        <div class="flex items-center space-x-3 mt-1">
                                                            <div class="flex-1 bg-gray-200 rounded-full h-3">
                                                                <div class="bg-gradient-to-r from-[#B4325E] to-[#93264B] h-3 rounded-full transition-all duration-300" 
                                                                     style="width: {{ $tramite->progreso_tramite ?? 0 }}%"></div>
                                                            </div>
                                                            <span class="text-sm font-bold text-gray-900">{{ $tramite->progreso_tramite ?? 0 }}%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer del Resumen -->
                                <div class="mt-8 pt-6 border-t border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                                <span>Información verificada</span>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                                <span>Sistema actualizado</span>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            ID del Trámite: <span class="font-mono font-semibold">#{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Contenidos Específicos -->
                    <div id="contenido-datos-generales" class="hidden">
                        @include('components.formularios.seccion-datos-generales', [
                            'title' => 'Datos Generales',
                            'datosTramite' => $datosTramite ?? [],
                            'datosSolicitante' => $datosSolicitante ?? [],
                            'readonly' => true
                        ])
                        
                        <!-- Controles de Revisión para Datos Generales -->
                        <div class="revision-controls mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200" data-seccion="datos-generales">
                            <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Revisión de Datos Generales
                            </h5>
                            <div class="flex items-start space-x-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios de Revisión</label>
                                    <textarea 
                                        class="comentario-seccion w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 text-sm"
                                        rows="3"
                                        placeholder="Agregue comentarios sobre esta sección..."
                                    ></textarea>
                                </div>
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-xs text-gray-500">Estado:</span>
                                        <span class="estado-seccion px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="btn-aprobar-seccion px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-all flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Aprobar
                                        </button>
                                        <button class="btn-rechazar-seccion px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-all flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Rechazar
                                        </button>
                                    </div>
                                    <button class="btn-guardar-comentario w-full px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-all">
                                        💾 Guardar Comentario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="contenido-domicilio" class="hidden">
                        @include('components.formularios.seccion-domicilio', [
                            'title' => 'Domicilio',
                            'datosDomicilio' => $datosDomicilio ?? [],
                            'readonly' => true
                        ])
                        
                        <!-- Controles de Revisión para Domicilio -->
                        <div class="revision-controls mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200" data-seccion="domicilio">
                            <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Revisión de Domicilio
                            </h5>
                            <div class="flex items-start space-x-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios de Revisión</label>
                                    <textarea 
                                        class="comentario-seccion w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 text-sm"
                                        rows="3"
                                        placeholder="Agregue comentarios sobre esta sección..."
                                    ></textarea>
                                    
                                    <!-- Información sobre el mapa en panel lateral -->
                                    <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                            <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            <span class="text-sm font-medium text-green-700">✅ Mapa cargado en Material de Apoyo</span>
                                            </div>
                                        <p class="text-xs text-green-600 mt-1">El mapa del domicilio se muestra automáticamente en el panel lateral</p>
                                    </div>
                                </div>
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-xs text-gray-500">Estado:</span>
                                        <span class="estado-seccion px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="btn-aprobar-seccion px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-all flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Aprobar
                                        </button>
                                        <button class="btn-rechazar-seccion px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-all flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Rechazar
                                        </button>
                                    </div>
                                    <button class="btn-guardar-comentario w-full px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-all">
                                        💾 Guardar Comentario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($tramite->solicitante && strtolower($tramite->solicitante->tipo_persona) === 'moral')
                        <div id="contenido-constitucion" class="hidden">
                            @include('components.formularios.seccion-constitucion', [
                                'title' => 'Constitución',
                                'datosConstitucion' => $datosConstitucion ?? [],
                                'readonly' => true
                            ])
                            
                            <!-- Controles de Revisión para Constitución -->
                            <div class="revision-controls mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200" data-seccion="constitucion">
                                <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                    </svg>
                                    Revisión de Constitución
                                </h5>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios de Revisión</label>
                                        <textarea 
                                            class="comentario-seccion w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 text-sm"
                                            rows="3"
                                            placeholder="Agregue comentarios sobre esta sección..."
                                        ></textarea>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-xs text-gray-500">Estado:</span>
                                            <span class="estado-seccion px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="btn-aprobar-seccion px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-all flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Aprobar
                                            </button>
                                            <button class="btn-rechazar-seccion px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-all flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Rechazar
                                            </button>
                                        </div>
                                        <button class="btn-guardar-comentario w-full px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-all">
                                            💾 Guardar Comentario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="contenido-accionistas" class="hidden">
                            @include('components.formularios.seccion-accionistas', [
                                'title' => 'Accionistas',
                                'accionistas' => $accionistas ?? [],
                                'readonly' => true
                            ])
                            
                            <!-- Controles de Revisión para Accionistas -->
                            <div class="revision-controls mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200" data-seccion="accionistas">
                                <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Revisión de Accionistas
                                </h5>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios de Revisión</label>
                                        <textarea 
                                            class="comentario-seccion w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 text-sm"
                                            rows="3"
                                            placeholder="Agregue comentarios sobre esta sección..."
                                        ></textarea>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-xs text-gray-500">Estado:</span>
                                            <span class="estado-seccion px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="btn-aprobar-seccion px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-all flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Aprobar
                                            </button>
                                            <button class="btn-rechazar-seccion px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-all flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Rechazar
                                            </button>
                                        </div>
                                        <button class="btn-guardar-comentario w-full px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-all">
                                            💾 Guardar Comentario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="contenido-apoderado" class="hidden">
                            @include('components.formularios.seccion-apoderado', [
                                'title' => 'Apoderado Legal',
                                'datosApoderado' => $datosApoderado ?? [],
                                'readonly' => true
                            ])
                            
                            <!-- Controles de Revisión para Apoderado -->
                            <div class="revision-controls mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200" data-seccion="apoderado">
                                <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Revisión de Apoderado Legal
                                </h5>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios de Revisión</label>
                                        <textarea 
                                            class="comentario-seccion w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 text-sm"
                                            rows="3"
                                            placeholder="Agregue comentarios sobre esta sección..."
                                        ></textarea>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-xs text-gray-500">Estado:</span>
                                            <span class="estado-seccion px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="btn-aprobar-seccion px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-all flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Aprobar
                                            </button>
                                            <button class="btn-rechazar-seccion px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-all flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Rechazar
                                            </button>
                                        </div>
                                        <button class="btn-guardar-comentario w-full px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-all">
                                            💾 Guardar Comentario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="contenido-personal" class="hidden">
                            @include('components.formularios.seccion-personal', [
                                'title' => 'Personal',
                                'datosPersonal' => $datosPersonal ?? [],
                                'readonly' => true
                            ])
                            
                            <!-- Controles de Revisión para Personal -->
                            <div class="revision-controls mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200" data-seccion="personal">
                                <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    Revisión de Personal
                                </h5>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios de Revisión</label>
                                        <textarea 
                                            class="comentario-seccion w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 text-sm"
                                            rows="3"
                                            placeholder="Agregue comentarios sobre esta sección..."
                                        ></textarea>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-xs text-gray-500">Estado:</span>
                                            <span class="estado-seccion px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="btn-aprobar-seccion px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-all flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Aprobar
                                            </button>
                                            <button class="btn-rechazar-seccion px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-all flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Rechazar
                                            </button>
                                        </div>
                                        <button class="btn-guardar-comentario w-full px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-all">
                                            💾 Guardar Comentario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div id="contenido-documentos" class="hidden">
                        @include('components.formularios.seccion-documentos', [
                            'title' => 'Documentos',
                            'documentos' => $documentos ?? [],
                            'tramite' => $tramite,
                            'readonly' => true
                        ])
                        
                        <!-- Controles de Revisión para Documentos -->
                        <div class="revision-controls mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200" data-seccion="documentos">
                            <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Revisión de Documentos
                            </h5>
                            <div class="flex items-start space-x-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios de Revisión</label>
                                    <textarea 
                                        class="comentario-seccion w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 text-sm"
                                        rows="3"
                                        placeholder="Agregue comentarios sobre esta sección..."
                                    ></textarea>
                                </div>
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-xs text-gray-500">Estado:</span>
                                        <span class="estado-seccion px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="btn-aprobar-seccion px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-all flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Aprobar
                                        </button>
                                        <button class="btn-rechazar-seccion px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-all flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Rechazar
                                        </button>
                                    </div>
                                    <button class="btn-guardar-comentario w-full px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-all">
                                        💾 Guardar Comentario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Nueva Sección de Decisión Final -->
                    @if(auth()->user()->can('revision-tramites.aprobar'))
                    <div id="contenido-decision-final" class="hidden">
                        <div class="space-y-6">
                            <div class="text-center py-8">
                                <div class="w-20 h-20 bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h4 class="text-2xl font-bold text-gray-900 mb-2">Decisión Final del Trámite</h4>
                                <p class="text-gray-600">Complete la revisión y tome una decisión sobre el trámite</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Aprobar Trámite -->
                                <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <h5 class="text-lg font-semibold text-green-900 mb-2">Aprobar</h5>
                                    <p class="text-sm text-green-700 mb-4">El trámite cumple con todos los requisitos</p>
                                    <button id="btn-aprobar-todo" 
                                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all">
                                        Aprobar Trámite
                                    </button>
                                </div>
                                
                                <!-- Solicitar Correcciones -->
                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </div>
                                    <h5 class="text-lg font-semibold text-yellow-900 mb-2">Correcciones</h5>
                                    <p class="text-sm text-yellow-700 mb-4">Solicitar modificaciones al solicitante</p>
                                    <button id="btn-solicitar-correcciones" 
                                            class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-all">
                                        Solicitar Correcciones
                                    </button>
                                </div>
                                
                                <!-- Rechazar Trámite -->
                                <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                    <h5 class="text-lg font-semibold text-red-900 mb-2">Rechazar</h5>
                                    <p class="text-sm text-red-700 mb-4">El trámite no cumple los requisitos</p>
                                    <button id="btn-rechazar-todo" 
                                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all">
                                        Rechazar Trámite
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Resumen de Revisiones por Sección -->
                            <div class="bg-gray-50 rounded-xl p-6">
                                <h5 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Revisiones</h5>
                                <div id="resumen-revisiones" class="space-y-3">
                                    <!-- Se llena dinámicamente con JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </main>

            <!-- Separador Redimensionable -->
            <div class="w-1 bg-gray-200 hover:bg-[#B4325E] cursor-col-resize transition-colors duration-200" id="resize-handle">
                <div class="w-full h-full flex items-center justify-center">
                    <div class="w-0.5 h-8 bg-gray-400 rounded"></div>
                </div>
            </div>

            <!-- Panel de Documentos y Revisión (Redimensionable) -->
            <aside class="bg-white rounded-xl shadow-lg border border-gray-100 sticky" style="width: 500px; min-width: 200px; max-width: 1200px; top: 20px; align-self: flex-start;" id="documentos-container">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-lg p-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                                    Material de Apoyo
                                </h3>
                                <p class="text-sm text-gray-500">Documentos de respaldo para la revisión</p>
                            </div>
                        </div>
                        <button onclick="toggleMaterialApoyo()" class="text-gray-400 hover:text-gray-600 transition-colors" title="Minimizar/Expandir">
                            <svg id="toggle-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-4 overflow-y-auto" style="min-height: 600px; max-height: 85vh;" id="panel-documentos-revision">

                    
                    <!-- Documentos de la sección -->
                    <div class="p-4">
                        <div id="documentos-general" class="text-center py-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-base font-medium text-gray-900 mb-2">Material de Apoyo</h4>
                            <p class="text-sm text-gray-500">Seleccione una sección para ver documentos de respaldo</p>
                            
                            <!-- Herramientas de Apoyo -->
                            <div class="mt-4 space-y-3">
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <p class="text-xs text-blue-600 font-medium">💡 Use los controles inferiores para revisar cada sección</p>
                                </div>
                                
                                <!-- Acceso rápido al mapa -->
                                <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                                            </svg>
                                            <span class="text-xs font-medium text-green-700">Verificación Geográfica</span>
                                        </div>
                                        <button 
                                            onclick="abrirMapaDomicilio()"
                                            class="px-2 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded transition-all flex items-center"
                                            title="Ver ubicación del domicilio en mapa"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Mapa
                                        </button>
                                    </div>
                                    <p class="text-xs text-green-600 mt-1">Verifique la ubicación del domicilio registrado</p>
                                </div>
                            </div>
                        </div>
                        
                        <div id="panel-datos-generales" class="hidden"></div>
                        <div id="panel-domicilio" class="hidden"></div>
                        <div id="panel-constitucion" class="hidden"></div>
                        <div id="panel-accionistas" class="hidden"></div>
                        <div id="panel-apoderado" class="hidden"></div>
                        <div id="panel-personal" class="hidden"></div>
                        <div id="panel-documentos" class="hidden"></div>
                        <div id="panel-decision-final" class="hidden">
                            <h4 class="text-base font-medium text-gray-900 mb-4">Resumen Final</h4>
                            <p class="text-sm text-gray-600 mb-4">Revise el estado de todas las secciones antes de tomar la decisión final.</p>
                            <div id="resumen-estados-secciones" class="space-y-2">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Modales -->
    @if(auth()->user()->can('revision-tramites.aprobar'))
    <!-- Modal Solicitar Correcciones -->
    <div id="modal-solicitar-correcciones" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl max-w-lg w-full p-8 shadow-xl">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-edit text-xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Solicitar Correcciones</h3>
                    <p class="text-sm text-gray-500 mt-2">Especifique las correcciones necesarias</p>
                </div>
                <form action="{{ route('revision.solicitar-correcciones', $tramite) }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Detalles de las correcciones <span class="text-red-500">*</span></label>
                        <textarea name="comentario" rows="5" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" 
                                  placeholder="Describa las correcciones necesarias..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" class="modal-close px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-lg">
                            Enviar Correcciones
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Aprobar -->
    <div id="modal-aprobar-todo" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl max-w-lg w-full p-8 shadow-xl">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-xl text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Aprobar Trámite</h3>
                    <p class="text-sm text-gray-500 mt-2">¿Confirmar la aprobación del trámite?</p>
                </div>
                <form action="{{ route('revision.aprobar', $tramite) }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentario (opcional)</label>
                        <textarea name="comentarios" rows="5" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                  placeholder="Comentario sobre la aprobación..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" class="modal-close px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-green-500 hover:bg-green-600 rounded-lg">
                            Aprobar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Rechazar -->
    <div id="modal-rechazar-todo" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl max-w-lg w-full p-8 shadow-xl">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times text-xl text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Rechazar Trámite</h3>
                    <p class="text-sm text-gray-500 mt-2">¿Confirmar el rechazo del trámite?</p>
                </div>
                <form action="{{ route('revision.rechazar', $tramite) }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Motivo del rechazo <span class="text-red-500">*</span></label>
                        <textarea name="comentario" rows="5" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                  placeholder="Explique el motivo del rechazo..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" class="modal-close px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg">
                            Rechazar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Meta Tags y JavaScript -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="tramite-id" content="{{ $tramite->id }}">
    
    <!-- Cargar Google Maps API -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCUqfgNQ2Q4AVy8OTNMfogJceDbA0FHZKs&libraries=places&callback=initGoogleMaps"></script>
    
    <!-- Incluir map-handler.js después de que Google Maps esté cargado -->
    <script>
        function initGoogleMaps() {
            // Marcar que Google Maps se cargó correctamente
            googleMapsLoaded = true;
            console.log('Google Maps API cargada correctamente');
            
            // Google Maps está cargado, ahora cargar map-handler
            const script = document.createElement('script');
            script.src = '{{ asset('js/components/map-handler.js') }}';
            script.onload = function() {
                console.log('Map handler cargado correctamente');
            };
            script.onerror = function() {
                console.error('Error al cargar map-handler.js');
                createMapFallback();
            };
            document.head.appendChild(script);
        }
        
        // Fallback si Google Maps no se carga
        let googleMapsLoaded = false;
        
        // Timeout para detectar si Google Maps no se carga en 10 segundos
        setTimeout(() => {
            if (!googleMapsLoaded && !window.mapHandler) {
                console.warn('Google Maps no se cargó en 10 segundos, usando fallback');
                createMapFallback();
            }
        }, 10000);
        
        function createMapFallback() {
            window.mapHandler = {
                initializeMap: function(seccion, direccion) {
                    const container = document.getElementById('mapa-' + seccion);
                    if (container) {
                        container.innerHTML = `
                            <div class="flex items-center justify-center h-full bg-gradient-to-br from-red-50 to-orange-50 rounded-lg border border-red-200">
                                <div class="text-center text-gray-700 p-8 max-w-md">
                                    <svg class="w-20 h-20 mx-auto mb-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <h4 class="text-xl font-bold text-gray-800 mb-3">🗺️ Google Maps no está disponible</h4>
                                    <p class="text-sm text-gray-600 mb-4">Verifique su conexión a internet o intente recargar la página</p>
                                    
                                    <div class="bg-white border border-gray-300 rounded-lg p-4 mb-6">
                                        <p class="text-xs text-gray-500 mb-2">📍 Dirección registrada:</p>
                                        <p class="text-sm font-medium text-gray-800">${direccion}</p>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <button onclick="window.open('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent('${direccion}'), '_blank')" 
                                                class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center font-medium">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Abrir en Google Maps
                                        </button>
                                        
                                        <button onclick="location.reload()" 
                                                class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Recargar página
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                },
                cleanup: function() {
                    console.log('Limpiando map handler fallback');
                }
            };
        }
        
        // Detectar errores específicos de Google Maps
        window.addEventListener('error', function(e) {
            if (e.filename && e.filename.includes('maps.googleapis.com')) {
                console.error('Error cargando Google Maps API');
                createMapFallback();
            }
        });
    </script>
    
    @push('scripts')
    <script>
        window.tramiteId = {{ $tramite->id }};
        window.documentosPorSeccion = @json($documentosPorSeccion ?? []);
        
        // Estado global de revisiones por sección
        window.revisionesSecciones = {};
        window.seccionActual = 'general';
        
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar sistema de redimensionamiento
            initResizeSystem();
            
            // Inicializar navegación de tabs
            initTabNavigation();
            
            // Inicializar controles de revisión
            initRevisionControls();
            
            // Inicializar modales
            initModals();
            
            // Inicializar scroll sincronizado
            initScrollSincronizado();
            
            console.log('Sistema de revisión avanzado cargado');
        });

        // Hacer funciones globales para uso en onclick
        window.abrirMapaDomicilio = abrirMapaDomicilio;
        window.cerrarModalMapa = cerrarModalMapa;
        window.obtenerDireccionDomicilio = obtenerDireccionDomicilio;
        window.mostrarDocumentoEnPanel = mostrarDocumentoEnPanel;
        window.cerrarVisorDocumento = cerrarVisorDocumento;
        window.verDocumentoCompleto = verDocumentoCompleto;
        window.abrirDocumentoNuevaPestana = abrirDocumentoNuevaPestana;
        window.cambiarVistaMapaModal = cambiarVistaMapaModal;
        window.copiarDireccion = copiarDireccion;
        window.abrirEnGoogleMaps = abrirEnGoogleMaps;
        
        // Sistema de redimensionamiento
        function initResizeSystem() {
            const resizeHandle = document.getElementById('resize-handle');
            const documentosContainer = document.getElementById('documentos-container');
            const mainContainer = document.getElementById('main-container');
            
            if (!resizeHandle || !documentosContainer || !mainContainer) return;
            
            let isResizing = false;
            
            resizeHandle.addEventListener('mousedown', (e) => {
                isResizing = true;
                document.addEventListener('mousemove', handleMouseMove);
                document.addEventListener('mouseup', () => {
                    isResizing = false;
                    document.removeEventListener('mousemove', handleMouseMove);
                });
            });
            
            function handleMouseMove(e) {
                if (!isResizing) return;
                
                const containerRect = mainContainer.getBoundingClientRect();
                const newWidth = containerRect.right - e.clientX - 24;
                
                const clampedWidth = Math.max(200, Math.min(1200, newWidth));
                documentosContainer.style.width = clampedWidth + 'px';
            }
        }
        
        // Navegación de tabs
        function initTabNavigation() {
            const seccionTabs = document.querySelectorAll('.seccion-tab');
            const contenidoFormulario = document.getElementById('contenido-formulario');
            const panelDocumentos = document.getElementById('panel-documentos-revision');
            const seccionActualSpan = document.getElementById('seccion-actual');
            const controlesRevision = document.getElementById('controles-revision-seccion');
            
            if (!contenidoFormulario || !panelDocumentos || !seccionActualSpan) return;
            
            seccionTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remover clases activas
                    seccionTabs.forEach(t => {
                        t.classList.remove('bg-gradient-to-r', 'from-[#B4325E]', 'to-[#93264B]', 'text-white', 'shadow-md');
                        t.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');
                    });
                    
                    // Agregar clase activa
                    this.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-200');
                    this.classList.add('bg-gradient-to-r', 'from-[#B4325E]', 'to-[#93264B]', 'text-white', 'shadow-md');
                    
                    // Actualizar sección actual
                    window.seccionActual = this.dataset.seccion;
                    seccionActualSpan.textContent = this.querySelector('span:last-child').textContent.trim();
                    
                    // Ocultar todos los contenidos
                    contenidoFormulario.querySelectorAll('div[id^="contenido-"]').forEach(div => div.classList.add('hidden'));
                    panelDocumentos.querySelectorAll('div[id^="panel-"]').forEach(div => div.classList.add('hidden'));
                    
                    // Mostrar contenido correspondiente
                    const contenido = document.getElementById(`contenido-${window.seccionActual}`);
                    const panel = document.getElementById(`panel-${window.seccionActual}`);
                    
                    if (contenido) contenido.classList.remove('hidden');
                    if (panel) panel.classList.remove('hidden');
                    
                    // Cargar estado de revisión de la sección
                    loadSeccionRevision(window.seccionActual);
                    
                    // Cargar documentos de la sección
                    cargarDocumentosSeccion(window.seccionActual);
                });
            });
            
            // Activar primer tab por defecto
            if (seccionTabs.length > 0) {
                seccionTabs[0].click();
            }
        }
        
        // Controles de revisión por sección
        function initRevisionControls() {
            // Usar delegación de eventos para los controles dinámicos
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-aprobar-seccion')) {
                    const seccion = e.target.closest('.revision-controls').dataset.seccion;
                    aprobarSeccion(seccion);
                } else if (e.target.closest('.btn-rechazar-seccion')) {
                    const seccion = e.target.closest('.revision-controls').dataset.seccion;
                    rechazarSeccion(seccion);
                } else if (e.target.closest('.btn-guardar-comentario')) {
                    const seccion = e.target.closest('.revision-controls').dataset.seccion;
                    guardarComentarioSeccion(seccion);
                }
            });
        }
        
        // Cargar estado de revisión de una sección
        function loadSeccionRevision(seccion) {
            const revisionControl = document.querySelector(`.revision-controls[data-seccion="${seccion}"]`);
            
            if (!revisionControl) return;
            
            const comentarioTextarea = revisionControl.querySelector('.comentario-seccion');
            const estadoSpan = revisionControl.querySelector('.estado-seccion');
            
            if (!comentarioTextarea || !estadoSpan) return;
            
            if (window.revisionesSecciones[seccion]) {
                const revision = window.revisionesSecciones[seccion];
                comentarioTextarea.value = revision.comentario || '';
                actualizarEstadoSeccion(estadoSpan, revision.estado || 'pendiente');
            } else {
                comentarioTextarea.value = '';
                actualizarEstadoSeccion(estadoSpan, 'pendiente');
            }
        }
        
        // Actualizar visualización del estado de sección
        function actualizarEstadoSeccion(estadoSpan, estado) {
            estadoSpan.className = 'px-2 py-1 text-xs font-medium rounded-full';
            
            switch(estado) {
                case 'aprobado':
                    estadoSpan.classList.add('bg-green-100', 'text-green-800');
                    estadoSpan.textContent = '✅ Aprobado';
                    break;
                case 'rechazado':
                    estadoSpan.classList.add('bg-red-100', 'text-red-800');
                    estadoSpan.textContent = '❌ Rechazado';
                    break;
                default:
                    estadoSpan.classList.add('bg-yellow-100', 'text-yellow-800');
                    estadoSpan.textContent = '⏳ Pendiente';
            }
            
            // Actualizar indicador en el tab
            actualizarIndicadorTab(window.seccionActual, estado);
        }
        
        // Actualizar indicador visual en el tab
        function actualizarIndicadorTab(seccion, estado) {
            const tab = document.querySelector(`[data-seccion="${seccion}"]`);
            if (tab) {
                const indicador = tab.querySelector('.w-2.h-2');
                if (indicador) {
                    indicador.className = 'w-2 h-2 rounded-full';
                    switch(estado) {
                        case 'aprobado':
                            indicador.classList.add('bg-green-500');
                            break;
                        case 'rechazado':
                            indicador.classList.add('bg-red-500');
                            break;
                        default:
                            indicador.classList.add('bg-yellow-500');
                    }
                }
            }
        }
        
        // Aprobar sección
        function aprobarSeccion(seccion) {
            const revisionControl = document.querySelector(`.revision-controls[data-seccion="${seccion}"]`);
            if (!revisionControl) return;
            
            const comentario = revisionControl.querySelector('.comentario-seccion').value;
            const estadoSpan = revisionControl.querySelector('.estado-seccion');
            
            window.revisionesSecciones[seccion] = { estado: 'aprobado', comentario: comentario };
            actualizarEstadoSeccion(estadoSpan, 'aprobado');
            mostrarNotificacion('✅ Sección aprobada correctamente', 'success');
        }
        
        // Rechazar sección
        function rechazarSeccion(seccion) {
            const revisionControl = document.querySelector(`.revision-controls[data-seccion="${seccion}"]`);
            if (!revisionControl) return;
            
            const comentario = revisionControl.querySelector('.comentario-seccion').value;
            const estadoSpan = revisionControl.querySelector('.estado-seccion');
            
            if (!comentario.trim()) {
                mostrarNotificacion('⚠️ Debe agregar un comentario para rechazar', 'warning');
                return;
            }
            
            window.revisionesSecciones[seccion] = { estado: 'rechazado', comentario: comentario };
            actualizarEstadoSeccion(estadoSpan, 'rechazado');
            mostrarNotificacion('❌ Sección rechazada', 'warning');
        }
        
        // Guardar solo comentario
        function guardarComentarioSeccion(seccion) {
            const revisionControl = document.querySelector(`.revision-controls[data-seccion="${seccion}"]`);
            if (!revisionControl) return;
            
            const comentario = revisionControl.querySelector('.comentario-seccion').value;
            
            if (!window.revisionesSecciones[seccion]) {
                window.revisionesSecciones[seccion] = {};
            }
            window.revisionesSecciones[seccion].comentario = comentario;
            mostrarNotificacion('💾 Comentario guardado', 'success');
        }

        // Función para toggle del panel de material de apoyo
        function toggleMaterialApoyo() {
            const panel = document.getElementById('panel-documentos-revision');
            const icon = document.getElementById('toggle-icon');
            
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>';
            } else {
                panel.style.display = 'none';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>';
            }
        }

        // Funciones para el modal de documentos (mantener para compatibilidad)
        function abrirModalDocumento(url, nombre) {
            const modal = document.getElementById('documento-modal');
            const iframe = document.getElementById('documento-iframe');
            const titulo = document.getElementById('documento-modal-title');
            
            // Construir URL correcta para el documento
            const documentoUrl = url ? `/documentos/${url.split('/').pop()}` : '#';
            
            iframe.src = documentoUrl;
            titulo.textContent = `📄 ${nombre}`;
            modal.classList.remove('hidden');
        }

        function cerrarModalDocumento() {
            const modal = document.getElementById('documento-modal');
            const iframe = document.getElementById('documento-iframe');
            
            iframe.src = '';
            modal.classList.add('hidden');
        }

        // Variables globales para el documento actual
        let documentoActualId = null;
        let documentoActualNombre = null;

        // Funciones para mostrar documentos en el panel lateral
        function mostrarDocumentoEnPanel(documentoId, nombreDocumento) {
            const listaDocumentos = document.getElementById('lista-documentos');
            const visorDocumento = document.getElementById('visor-documento');
            const iframeDocumento = document.getElementById('iframe-documento');
            const nombreDocumentoActual = document.getElementById('nombre-documento-actual');
            const btnCerrarVisor = document.getElementById('btn-cerrar-visor');
            
            if (!listaDocumentos || !visorDocumento || !iframeDocumento || !nombreDocumentoActual) {
                console.error('Elementos del visor de documentos no encontrados');
                return;
            }
            
            // Guardar información del documento actual
            documentoActualId = documentoId;
            documentoActualNombre = nombreDocumento;
            
            // Construir URL correcta para el documento
            const documentoUrl = `/documentos/${documentoId}`;
            
            // Ocultar lista y mostrar visor
            listaDocumentos.style.display = 'none';
            visorDocumento.classList.remove('hidden');
            if (btnCerrarVisor) btnCerrarVisor.style.display = 'block';
            
            // Cargar documento en iframe
            iframeDocumento.src = documentoUrl;
            nombreDocumentoActual.textContent = `📄 ${nombreDocumento}`;
            
            // Ajustar altura después de mostrar el visor
            setTimeout(() => {
                const ajustarEvent = new Event('resize');
                window.dispatchEvent(ajustarEvent);
            }, 100);
            
            // Mostrar notificación
            mostrarNotificacion(`📄 Cargando documento: ${nombreDocumento}`, 'success');
        }

        function cerrarVisorDocumento() {
            const listaDocumentos = document.getElementById('lista-documentos');
            const visorDocumento = document.getElementById('visor-documento');
            const iframeDocumento = document.getElementById('iframe-documento');
            const btnCerrarVisor = document.getElementById('btn-cerrar-visor');
            
            if (!listaDocumentos || !visorDocumento || !iframeDocumento) {
                console.error('Elementos del visor de documentos no encontrados');
                return;
            }
            
            // Mostrar lista y ocultar visor
            listaDocumentos.style.display = 'block';
            visorDocumento.classList.add('hidden');
            if (btnCerrarVisor) btnCerrarVisor.style.display = 'none';
            
            // Limpiar iframe y variables globales
            iframeDocumento.src = '';
            documentoActualId = null;
            documentoActualNombre = null;
            
            mostrarNotificacion('📋 Regresando a la lista de documentos', 'success');
        }

        // Función para ver documento en tamaño completo (modal)
        function verDocumentoCompleto() {
            if (!documentoActualId || !documentoActualNombre) {
                mostrarNotificacion('⚠️ No hay documento seleccionado', 'warning');
                return;
            }
            
            const modal = document.getElementById('documento-modal');
            const iframe = document.getElementById('documento-iframe');
            const titulo = document.getElementById('documento-modal-title');
            
            if (!modal || !iframe || !titulo) {
                mostrarNotificacion('❌ Error al abrir el modal', 'error');
                return;
            }
            
            // Construir URL correcta para el documento
            const documentoUrl = `/documentos/${documentoActualId}`;
            
            iframe.src = documentoUrl;
            titulo.textContent = `🔍 ${documentoActualNombre} - Vista Completa`;
            modal.classList.remove('hidden');
            
            mostrarNotificacion(`🔍 Abriendo vista completa: ${documentoActualNombre}`, 'success');
        }

        // Función para abrir documento en nueva pestaña
        function abrirDocumentoNuevaPestana() {
            if (!documentoActualId || !documentoActualNombre) {
                mostrarNotificacion('⚠️ No hay documento seleccionado', 'warning');
                return;
            }
            
            const documentoUrl = `/documentos/${documentoActualId}`;
            window.open(documentoUrl, '_blank');
            
            mostrarNotificacion(`🆕 Abriendo en nueva pestaña: ${documentoActualNombre}`, 'success');
        }

        // Funciones para el modal de mapa
        function abrirMapaDomicilio() {
            const modal = document.getElementById('mapa-modal');
            if (modal) {
                modal.classList.remove('hidden');
                
                // Obtener dirección del domicilio
                const direccion = obtenerDireccionDomicilio();
                
                // Actualizar dirección en el modal
                const direccionModalCompleta = document.getElementById('direccion-modal-completa');
                if (direccionModalCompleta) {
                    direccionModalCompleta.textContent = direccion;
                }
                
                // Mostrar loading inicial con el nuevo diseño
                const mapContainer = document.getElementById('mapa-domicilio');
                if (mapContainer) {
                    // El loading ya está en el HTML, solo remover cuando esté listo
                }
                
                // Inicializar mapa después de un pequeño delay para asegurar que el modal esté visible
                setTimeout(() => {
                    if (window.mapHandler) {
                        window.mapHandler.initializeMap('domicilio', direccion);
                        // Remover el loading overlay
                        setTimeout(() => {
                            const loadingOverlay = mapContainer.querySelector('.absolute.inset-0');
                            if (loadingOverlay) {
                                loadingOverlay.style.display = 'none';
                            }
                        }, 2000);
                    } else {
                        // Fallback mejorado si no hay map handler
                        const loadingOverlay = mapContainer.querySelector('.absolute.inset-0');
                        if (loadingOverlay) {
                            loadingOverlay.innerHTML = `
                                <div class="flex items-center justify-center h-full bg-gradient-to-br from-red-50 to-orange-50">
                                    <div class="text-center text-gray-700 p-8 max-w-md">
                                        <svg class="w-20 h-20 mx-auto mb-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <h4 class="text-2xl font-bold text-gray-800 mb-4">🗺️ Google Maps no disponible</h4>
                                        <p class="text-lg text-gray-600 mb-6">Verifique su conexión a internet o intente recargar la página</p>
                                        
                                        <div class="bg-white border border-gray-300 rounded-xl p-4 mb-6 shadow-sm">
                                            <p class="text-sm text-gray-500 mb-2">📍 Dirección registrada:</p>
                                            <p class="text-lg font-semibold text-gray-800">${direccion}</p>
                                        </div>
                                        
                                        <div class="space-y-3">
                                        <button onclick="window.open('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent('${direccion}'), '_blank')" 
                                                    class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all flex items-center justify-center font-semibold text-lg shadow-lg">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Abrir en Google Maps
                                        </button>
                                            
                                            <button onclick="location.reload()" 
                                                    class="w-full px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors flex items-center justify-center font-medium">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Recargar página
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                        mostrarNotificacion('⚠️ Google Maps no está disponible', 'warning');
                    }
                }, 500);
            }
        }

        // Funciones para los controles del modal de mapa
        function cambiarVistaMapaModal(tipoVista) {
            if (window.mapHandler && window.mapHandler.changeMapType) {
                window.mapHandler.changeMapType(tipoVista);
                mostrarNotificacion(`🗺️ Vista cambiada a: ${tipoVista}`, 'success');
            } else {
                mostrarNotificacion('⚠️ Control de vista no disponible', 'warning');
            }
        }

        function copiarDireccion() {
            const direccion = obtenerDireccionDomicilio();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(direccion).then(() => {
                    mostrarNotificacion('📋 Dirección copiada al portapapeles', 'success');
                }).catch(err => {
                    console.error('Error al copiar:', err);
                    mostrarNotificacion('❌ Error al copiar la dirección', 'error');
                });
            } else {
                // Fallback para navegadores más antiguos
                const textArea = document.createElement('textarea');
                textArea.value = direccion;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                mostrarNotificacion('📋 Dirección copiada', 'success');
            }
        }

        function abrirEnGoogleMaps() {
            const direccion = obtenerDireccionDomicilio();
            const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(direccion)}`;
            window.open(url, '_blank');
            mostrarNotificacion('🗺️ Abriendo Google Maps en nueva pestaña', 'success');
        }

        function cerrarModalMapa() {
            const modal = document.getElementById('mapa-modal');
            if (modal) {
                modal.classList.add('hidden');
                
                // Limpiar el mapa
                if (window.mapHandler) {
                    window.mapHandler.cleanup();
                }
            }
        }

        // Función para obtener la dirección del domicilio desde los datos del formulario
        function obtenerDireccionDomicilio() {
            try {
                // Intentar obtener la dirección desde los datos de domicilio
                const domicilioSection = document.getElementById('contenido-domicilio');
                if (!domicilioSection) {
                    return 'Dirección no disponible';
                }

                // Buscar campos de domicilio específicos
                const datos = {};
                
                // Buscar elementos con texto visible (divs readonly o spans con datos)
                const elementos = domicilioSection.querySelectorAll('.bg-gray-50, input[readonly], .text-gray-700');
                
                elementos.forEach(elemento => {
                    const texto = (elemento.textContent || elemento.value || '').trim();
                    
                    if (texto && texto !== 'No especificado' && texto !== '' && texto.length > 2) {
                        // Identificar el tipo de campo por contexto o etiquetas cercanas
                        const etiqueta = elemento.closest('.grid')?.querySelector('label')?.textContent?.toLowerCase() || '';
                        
                        if (etiqueta.includes('calle') || (texto.length > 10 && !datos.calle)) {
                            datos.calle = texto;
                        } else if (etiqueta.includes('número') || texto.match(/^\d+[A-Z]?$/)) {
                            datos.numero = texto;
                        } else if (etiqueta.includes('colonia') || etiqueta.includes('asentamiento')) {
                            datos.colonia = texto;
                        } else if (etiqueta.includes('municipio') || etiqueta.includes('delegación')) {
                            datos.municipio = texto;
                        } else if (etiqueta.includes('estado') || etiqueta.includes('entidad')) {
                            datos.estado = texto;
                        } else if (etiqueta.includes('postal') || texto.match(/^\d{5}$/)) {
                            datos.cp = texto;
                        }
                    }
                });

                // Construir dirección completa
                let direccion = '';
                if (datos.calle) direccion += datos.calle;
                if (datos.numero) direccion += ' ' + datos.numero;
                if (datos.colonia) direccion += ', ' + datos.colonia;
                if (datos.municipio) direccion += ', ' + datos.municipio;
                if (datos.estado) direccion += ', ' + datos.estado;
                if (datos.cp) direccion += ' ' + datos.cp;

                return direccion || 'Ciudad de México, México';
                
            } catch (error) {
                console.error('Error al obtener dirección:', error);
                return 'Ciudad de México, México';
            }
        }
        
        // Inicializar modales
        function initModals() {
            const modales = {
                'btn-solicitar-correcciones': 'modal-solicitar-correcciones',
                'btn-aprobar-todo': 'modal-aprobar-todo',
                'btn-rechazar-todo': 'modal-rechazar-todo'
            };

            Object.keys(modales).forEach(btnId => {
                const btn = document.getElementById(btnId);
                if (btn) {
                    btn.addEventListener('click', () => {
                        const modal = document.getElementById(modales[btnId]);
                        if (modal) modal.classList.remove('hidden');
                    });
                }
            });

            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => {
                    btn.closest('.fixed').classList.add('hidden');
                });
            });
        }

        // Inicializar scroll sincronizado
        function initScrollSincronizado() {
            const documentosContainer = document.getElementById('documentos-container');
            const panelDocumentos = document.getElementById('panel-documentos-revision');
            
            if (!documentosContainer || !panelDocumentos) return;
            
            // Función para ajustar altura dinámicamente
            function ajustarAlturaPanel() {
                const windowHeight = window.innerHeight;
                const containerTop = documentosContainer.getBoundingClientRect().top;
                const availableHeight = windowHeight - containerTop - 40; // 40px de margen inferior
                
                // Ajustar altura máxima del panel principal
                panelDocumentos.style.maxHeight = Math.max(400, availableHeight) + 'px';
                
                // Ajustar altura del visor de documentos si está visible
                const visorDocumento = document.getElementById('visor-documento');
                if (visorDocumento && !visorDocumento.classList.contains('hidden')) {
                    const visorContainer = visorDocumento.querySelector('.bg-white');
                    if (visorContainer) {
                        const maxVisorHeight = Math.max(500, availableHeight - 100);
                        visorContainer.style.height = Math.min(maxVisorHeight, windowHeight * 0.7) + 'px';
                    }
                }
            }
            
            // Ajustar al cargar
            ajustarAlturaPanel();
            
            // Ajustar en resize
            window.addEventListener('resize', ajustarAlturaPanel);
            
            // Ajustar en scroll
            let scrollTimeout;
            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(ajustarAlturaPanel, 10);
            });
            
            // Observer para cambios en el contenido del formulario
            const formularioContainer = document.getElementById('contenido-formulario');
            if (formularioContainer && window.ResizeObserver) {
                const resizeObserver = new ResizeObserver(() => {
                    setTimeout(ajustarAlturaPanel, 100);
                });
                resizeObserver.observe(formularioContainer);
            }
        }
        
        // Mostrar notificaciones
        function mostrarNotificacion(mensaje, tipo) {
            // Crear elemento de notificación
            const notificacion = document.createElement('div');
            notificacion.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
            
            switch(tipo) {
                case 'success':
                    notificacion.classList.add('bg-green-500', 'text-white');
                    break;
                case 'error':
                    notificacion.classList.add('bg-red-500', 'text-white');
                    break;
                case 'warning':
                    notificacion.classList.add('bg-yellow-500', 'text-white');
                    break;
                default:
                    notificacion.classList.add('bg-blue-500', 'text-white');
            }
            
            notificacion.innerHTML = `
                <div class="flex items-center">
                    <span>${mensaje}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notificacion);
            
            // Animar entrada
            setTimeout(() => {
                notificacion.classList.remove('translate-x-full');
            }, 100);
            
            // Remover después de 5 segundos
            setTimeout(() => {
                notificacion.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notificacion.parentElement) {
                        document.body.removeChild(notificacion);
                    }
                }, 300);
            }, 5000);
        }

        // Cargar documentos de una sección
        function cargarDocumentosSeccion(seccion) {
            const panelDocumentos = document.getElementById('panel-documentos-revision');
            
            if (!panelDocumentos) return;
            
            // Si es la sección de domicilio, mostrar mapa en lugar de documentos
            if (seccion === 'domicilio') {
                mostrarMapaEnPanel();
                return;
            }
            
            // Si es la sección de documentos, ocultar el panel de Material de Apoyo
            if (seccion === 'documentos') {
                ocultarMaterialApoyo();
                return;
            }
            
            const seccionId = getSeccionId(seccion);
            
            // Mostrar loader
            panelDocumentos.innerHTML = `
                <div class="flex items-center justify-center h-full py-8">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#B4325E] mx-auto mb-4"></div>
                        <p class="text-sm text-gray-500">Cargando documentos...</p>
                    </div>
                </div>
            `;
            
            fetch(`/revision/${window.tramiteId}/documentos-seccion?seccion_id=${seccionId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    mostrarDocumentosSeccion(data.documentos);
                } else {
                    mostrarErrorDocumentos(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarErrorDocumentos('Error al cargar los documentos');
            });
        }
        
        // Mostrar documentos en el panel con iconos clicables
        function mostrarDocumentosSeccion(documentos) {
            const panelDocumentos = document.getElementById('panel-documentos-revision');
            
            if (!documentos || documentos.length === 0) {
                panelDocumentos.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-medium text-gray-900 mb-2">Sin Material de Apoyo</h4>
                        <p class="text-sm text-gray-500">No hay documentos asociados a esta sección</p>
                    </div>
                `;
                return;
            }
            
            // Crear estructura con lista de documentos y área de vista
            panelDocumentos.innerHTML = `
                <div class="h-full flex flex-col">
                    <!-- Header con información -->
                    <div class="flex-shrink-0 mb-4 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-blue-700 font-medium">
                                    📋 Material de Apoyo (${documentos.length})
                                </p>
                                <p class="text-xs text-blue-600 mt-1">
                                    Haga clic en el documento para visualizarlo
                                </p>
                            </div>
                            <button onclick="cerrarVisorDocumento()" class="text-blue-600 hover:text-blue-800 text-xs" id="btn-cerrar-visor" style="display: none;">
                                📄 ← Lista
                            </button>
                        </div>
                    </div>
                    
                    <!-- Lista de documentos -->
                    <div id="lista-documentos" class="flex-1 space-y-2">
                        ${documentos.map(doc => `
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-all duration-200 hover:border-[#B4325E] cursor-pointer"
                                 onclick="mostrarDocumentoEnPanel('${doc.id}', '${doc.nombre}')">
                                <div class="p-3">
                                    <div class="flex items-center justify-between">
                                        <!-- Icono y nombre -->
                                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 hover:from-[#B4325E] hover:to-[#93264B] rounded-lg flex items-center justify-center transition-all duration-200 shadow-sm group">
                                                    <svg class="w-5 h-5 text-gray-500 group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">${doc.nombre}</h4>
                                                <div class="flex items-center justify-between mt-1">
                                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full ${getEstadoClases(doc.estado)}">
                                        ${formatearEstado(doc.estado)}
                                    </span>
                                    <span class="text-xs text-gray-400">v${doc.version || '1'}</span>
                                </div>
                                    </div>
                            </div>
                            
                                        <!-- Botón para abrir en nueva pestaña -->
                                        <div class="flex-shrink-0 ml-2">
                                <a href="/documentos/${doc.id}" target="_blank" 
                                               class="text-gray-400 hover:text-[#B4325E] transition-colors p-1" 
                                               title="Abrir en nueva pestaña"
                                               onclick="event.stopPropagation();">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        ${doc.observaciones ? `
                                        <div class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-600 border-l-2 border-gray-300">
                                            <span class="font-medium">💬</span> ${doc.observaciones}
                            </div>
                        ` : ''}
                    </div>
                </div>
                        `).join('')}
                    </div>
                    
                    <!-- Área de visualización del documento -->
                    <div id="visor-documento" class="hidden flex-1 mt-4">
                        <div class="bg-white rounded-lg border border-gray-200" style="height: auto; min-height: 400px; max-height: calc(100vh - 250px);">
                            <div class="p-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                        <div class="flex items-center justify-between">
                                    <h5 id="nombre-documento-actual" class="text-sm font-medium text-gray-900"></h5>
                                    <div class="flex items-center space-x-2">
                                        <!-- Botón para ver en tamaño completo -->
                                        <button id="btn-ver-completo" 
                                                onclick="verDocumentoCompleto()" 
                                                class="group text-gray-500 hover:text-[#B4325E] transition-all duration-200 transform hover:scale-110"
                                                title="Ver documento en tamaño completo">
                                            <div class="w-8 h-8 bg-gray-100 group-hover:bg-[#B4325E] rounded-lg flex items-center justify-center transition-all duration-200 shadow-sm group-hover:shadow-md">
                                                <svg class="w-4 h-4 group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4M20 8V4a1 1 0 00-1-1h-4m4 8v4a1 1 0 01-1 1h-4M4 16v4a1 1 0 001 1h4"/>
                                                </svg>
                            </div>
                            </button>
                                        
                                        <!-- Botón para nueva pestaña -->
                                        <button id="btn-nueva-pestana" 
                                                onclick="abrirDocumentoNuevaPestana()" 
                                                class="group text-gray-500 hover:text-blue-600 transition-all duration-200 transform hover:scale-110"
                                                title="Abrir en nueva pestaña">
                                            <div class="w-8 h-8 bg-gray-100 group-hover:bg-blue-600 rounded-lg flex items-center justify-center transition-all duration-200 shadow-sm group-hover:shadow-md">
                                                <svg class="w-4 h-4 group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                        </div>
                                        </button>
                    </div>
                                </div>
                            </div>
                            <div class="p-2" style="height: calc(100% - 60px);">
                                <iframe id="iframe-documento" class="w-full h-full rounded border-0" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Mostrar error al cargar documentos
        function mostrarErrorDocumentos(mensaje) {
            const panelDocumentos = document.getElementById('panel-documentos-revision');
            panelDocumentos.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 mb-2">Error</h4>
                    <p class="text-sm text-red-600">${mensaje}</p>
                </div>
            `;
        }

        // Mostrar mapa en el panel lateral (versión simple)
        function mostrarMapaEnPanel() {
            const panelDocumentos = document.getElementById('panel-documentos-revision');
            const direccion = obtenerDireccionDomicilio();
            
            panelDocumentos.innerHTML = `
                <div class="h-full flex flex-col">
                    <!-- Header del mapa simple -->
                    <div class="flex-shrink-0 mb-4 p-3 bg-gradient-to-r from-green-50 to-teal-50 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-green-700 font-medium">🗺️ Ubicación</p>
                                    <p class="text-xs text-green-600">Vista rápida</p>
                                </div>
                            </div>
                            <button onclick="abrirMapaDomicilio()" 
                                    class="text-green-600 hover:text-green-800 transition-all duration-200 transform hover:scale-110"
                                    title="Ver mapa completo con detalles">
                                <div class="w-7 h-7 bg-green-100 hover:bg-green-600 rounded-lg flex items-center justify-center transition-all duration-200">
                                    <svg class="w-3 h-3 hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4M20 8V4a1 1 0 00-1-1h-4m4 8v4a1 1 0 01-1 1h-4M4 16v4a1 1 0 001 1h4"/>
                                    </svg>
                                </div>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Dirección compacta -->
                    <div class="flex-shrink-0 mb-3 p-2 bg-white rounded border border-gray-200">
                        <p class="text-xs text-gray-500 mb-1">📍 Dirección:</p>
                        <p class="text-sm font-medium text-gray-900 leading-tight">${direccion}</p>
                    </div>
                    
                    <!-- Contenedor del mapa simple -->
                    <div class="flex-1 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-2 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <h5 class="text-sm font-medium text-gray-900">🗺️ Mapa</h5>
                                <div class="flex items-center space-x-1">
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                    <span class="text-xs text-gray-500">Ubicación</span>
                                </div>
                            </div>
                        </div>
                        <div id="mapa-panel-lateral" class="w-full" style="height: 350px; min-height: 300px;">
                            <div class="flex items-center justify-center h-full bg-gray-50">
                                <div class="text-center">
                                    <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-green-500 mb-3"></div>
                                    <p class="text-sm text-gray-600">Cargando mapa...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón para vista completa -->
                    <div class="flex-shrink-0 mt-3">
                        <button onclick="abrirMapaDomicilio()" 
                                class="w-full flex items-center justify-center space-x-2 px-3 py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:from-green-700 hover:to-teal-700 transition-all text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Ver Análisis Completo</span>
                        </button>
                    </div>
                </div>
            `;
            
            // Inicializar mapa simple en el panel después de un pequeño delay
            setTimeout(() => {
                if (window.mapHandler && window.mapHandler.initializeSimpleMap) {
                    window.mapHandler.initializeSimpleMap('mapa-panel-lateral', direccion);
                } else {
                    // Fallback si no hay map handler
                    crearMapaFallbackPanel(direccion);
                }
            }, 500);
        }

        // Función de fallback para mapa simple en panel lateral
        function crearMapaFallbackPanel(direccion) {
            const container = document.getElementById('mapa-panel-lateral');
            if (container) {
                container.innerHTML = `
                    <div class="flex items-center justify-center h-full bg-gradient-to-br from-red-50 to-orange-50">
                        <div class="text-center text-gray-700 p-4">
                            <svg class="w-12 h-12 mx-auto mb-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 616 0z"/>
                            </svg>
                            <h4 class="text-sm font-bold text-gray-800 mb-2">🗺️ Mapa no disponible</h4>
                            <p class="text-xs text-gray-600 mb-3">Verifique su conexión a internet</p>
                            
                            <div class="bg-white border border-gray-300 rounded-lg p-2 mb-3 text-left">
                                <p class="text-xs text-gray-500 mb-1">📍 Dirección:</p>
                                <p class="text-xs font-medium text-gray-800 leading-tight">${direccion}</p>
                            </div>
                            
                            <button onclick="abrirMapaDomicilio()" 
                                    class="w-full px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                🔍 Ver Análisis Completo
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        // Función para ocultar Material de Apoyo en la sección documentos
        function ocultarMaterialApoyo() {
            const panelDocumentos = document.getElementById('panel-documentos-revision');
            
            panelDocumentos.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center">
                    <div class="text-center py-12 px-6 max-w-md">
                        <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">📄 Sección de Documentos</h4>
                        <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                            En esta sección ya tiene acceso directo a todos los documentos del trámite en el formulario principal.
                        </p>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-blue-700 font-medium">Material de Apoyo no necesario</p>
                            </div>
                            <p class="text-xs text-blue-600 mt-2 ml-7">
                                Los documentos se muestran directamente en la sección principal para su revisión.
                            </p>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span>Documentos disponibles en formulario</span>
                            </div>
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <span>Revisión directa optimizada</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Función auxiliar para obtener clases de estado
        function getEstadoClases(estado) {
            const estados = {
                'Pendiente': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                'Recibido': 'bg-blue-100 text-blue-800 border border-blue-200',
                'Rechazado': 'bg-red-100 text-red-800 border border-red-200',
                'En Revision': 'bg-purple-100 text-purple-800 border border-purple-200',
                'Aprobado': 'bg-green-100 text-green-800 border border-green-200'
            };
            return estados[estado] || 'bg-gray-100 text-gray-800 border border-gray-200';
        }

        // Función auxiliar para formatear estado
        function formatearEstado(estado) {
            const iconos = {
                'Pendiente': '⏳',
                'Recibido': '📥',
                'Rechazado': '❌',
                'En Revision': '🔍',
                'Aprobado': '✅'
            };
            return `${iconos[estado] || '📄'} ${estado}`;
        }
        
        // Mapear nombres de sección a IDs
        function getSeccionId(seccion) {
            const mapeo = {
                'general': 1,
                'datos-generales': 1,
                'domicilio': 2,
                'constitucion': 3,
                'accionistas': 4,
                'apoderado': 5,
                'personal': 7,
                'documentos': 6
            };
            return mapeo[seccion] || 1;
        }
    </script>
    @endpush

    @push('styles')
    <style>
        /* Estilos para tabs */
        .seccion-tab {
            @apply bg-gray-100 text-gray-700 border-gray-200;
        }
        
        .seccion-tab:hover {
            @apply bg-gray-200 transform scale-105;
        }
        
        /* Estilos para el redimensionamiento */
        #resize-handle:hover {
            @apply bg-[#B4325E] shadow-md;
        }
        
        /* Scrollbar personalizado */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-track {
            @apply bg-gray-100 rounded-full;
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb {
            @apply bg-gray-300 rounded-full;
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            @apply bg-[#B4325E];
        }
        
        /* Animaciones suaves */
        .transition-all {
            transition: all 0.3s ease;
        }
        
        /* Efectos hover para botones */
        button:hover {
            transform: translateY(-1px);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        /* Contenedor principal sin altura fija */
        #main-container {
            min-height: auto;
        }
        
        /* Formulario que se expande con el contenido */
        #formulario-container {
            min-height: auto;
        }
        
        #contenido-formulario {
            min-height: auto;
        }
        
        /* Panel de documentos más compacto */
        #panel-documentos-revision {
            max-height: 85vh;
            min-height: 400px;
            transition: max-height 0.3s ease;
        }
        
        /* Contenedor sticky para documentos */
        #documentos-container {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow: hidden;
            min-width: 200px;
            max-width: 1200px;
        }
        
        /* Estilos para el visor de documentos en panel lateral */
        #visor-documento {
            min-height: 400px;
            height: auto;
            max-height: calc(100vh - 200px);
            overflow: hidden;
        }
        
        #iframe-documento {
            min-height: 350px;
            max-height: calc(100vh - 250px);
        }
        
        /* Estilos para los botones del visor */
        #btn-ver-completo, #btn-nueva-pestana {
            transition: all 0.2s ease;
        }
        
        #btn-ver-completo:hover, #btn-nueva-pestana:hover {
            transform: scale(1.1);
        }
        
        /* Mejoras para lista de documentos */
        #lista-documentos {
            max-height: 60vh;
            overflow-y: auto;
        }
        
        /* Hover effects para documentos */
        .cursor-pointer:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Mejoras para los controles de revisión */
        .revision-controls {
            margin-bottom: 2rem;
        }
        
        /* Asegurar que el modal del mapa sea responsivo */
        #mapa-modal .relative {
            margin: 1rem;
        }
        
        @media (max-width: 768px) {
            #mapa-modal .relative {
                margin: 0.5rem;
                max-width: calc(100vw - 1rem);
                max-height: calc(100vh - 1rem);
            }
        }
    </style>
    @endpush
</div>
@endsection