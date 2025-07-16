@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .gradient-text {
        background: linear-gradient(135deg, #9d2449 0%, #be185d 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
        transform: translateY(-4px);
    }

    .icon-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .highlight-action {
        background: linear-gradient(135deg, rgba(157, 36, 73, 0.02) 0%, rgba(190, 24, 93, 0.02) 100%);
        border-left: 4px solid transparent !important;
        position: relative;
        overflow: hidden;
    }

    .highlight-action::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(157, 36, 73, 0.03) 0%, rgba(190, 24, 93, 0.03) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .highlight-action:hover::before {
        opacity: 1;
    }

    .highlight-action:hover {
        border-left-color: #9d2449 !important;
        transform: translateX(2px);
    }

    .glow-button {
        position: relative;
        overflow: hidden;
    }

    .glow-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .glow-button:hover::before {
        left: 100%;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .float-animation {
        animation: float 6s ease-in-out infinite;
    }

    .shadow-3xl {
        box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
    }

    .text-shadow {
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endpush

<div class="min-h-screen py-6 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-lg overflow-hidden max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="p-6 border-b border-gray-100/30">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="mb-6 lg:mb-0">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 px-4 py-2 rounded-full text-sm font-semibold text-gray-600 flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                <span id="currentTime" class="font-mono"></span>
                            </div>
                            <div class="h-6 w-px bg-gray-200"></div>
                            <div class="text-sm text-gray-500 font-medium flex items-center">
                                <i class="fas fa-calendar-day mr-2"></i>
                                <span id="currentDate"></span>
                            </div>
                            @if(auth()->user()->hasRole('Proveedor'))
                            <div class="h-6 w-px bg-gray-200"></div>
                            <div class="bg-gradient-to-br from-emerald-100 to-emerald-200 px-4 py-2 rounded-full text-sm font-semibold text-emerald-700 flex items-center">
                                <i class="fas fa-certificate mr-2"></i>
                                <span>Proveedor Oficial</span>
                            </div>

                            @endif
                        </div>
                        <h1 id="greeting" class="text-3xl font-bold gradient-text mb-2">
                            Buenos días, {{ auth()->check() ? auth()->user()->name : 'Invitado' }}
                        </h1>
                        @if(auth()->user()->hasRole('Proveedor'))
                        <p class="text-gray-600 text-base font-medium">
                            <span class="text-emerald-600 font-semibold">¡Proveedor Oficial!</span> - 
                            Bienvenido al <span class="text-[#9d2449] font-semibold">Padrón de Proveedores del Estado de Oaxaca</span>
                        </p>
                        @else
                        <p class="text-gray-600 text-base font-medium">
                            Bienvenido al <span class="text-[#9d2449] font-semibold">Padrón de Proveedores del Estado de Oaxaca</span>
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                @can('dashboard.ver-estadisticas')
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Usuarios -->
                    <div class="bg-white rounded-2xl p-6 card-hover shadow-lg border border-gray-100/50 relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-indigo-300"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800">{{ $totalUsuarios ?? 0 }}</p>
                                <span class="inline-block px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-full mt-1">
                                    Total registrados
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Usuarios del Sistema</h3>
                        <p class="text-sm text-gray-500">Gestión completa de usuarios</p>
                        <div class="mt-4 pt-4 border-t border-gray-100/50">
                            <a href="{{ route('users.index') }}" class="group inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-semibold transition-colors">
                                <span>Administrar usuarios</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Trámites -->
                    <div class="bg-white rounded-2xl p-6 card-hover shadow-lg border border-gray-100/50 relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-500 to-amber-300"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-amber-500 to-amber-600 w-12 h-12 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-alt text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800">{{ $tramitesPendientes ?? 0 }}</p>
                                <span class="inline-block px-3 py-1 text-xs font-medium text-amber-600 bg-amber-50 rounded-full mt-1">
                                    Pendientes de revisión
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Trámites en Proceso</h3>
                        <p class="text-sm text-gray-500">Solicitudes por aprobar</p>
                        <div class="mt-4 pt-4 border-t border-gray-100/50">
                            <a class="group inline-flex items-center text-amber-600 hover:text-amber-800 text-sm font-semibold transition-colors">
                                <span>Revisar trámites</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Proveedores -->
                    <div class="bg-white rounded-2xl p-6 card-hover shadow-lg border border-gray-100/50 relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-emerald-300"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center">
                                <i class="fas fa-building text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800"></p>
                                <span class="inline-block px-3 py-1 text-xs font-medium text-emerald-600 bg-emerald-50 rounded-full mt-1">
                                    Activos en el padrón
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Proveedores Registrados</h3>
                        <p class="text-sm text-gray-500">Base de datos completa</p>
                        <div class="mt-4 pt-4 border-t border-gray-100/50">
                            <a  class="group inline-flex items-center text-emerald-600 hover:text-emerald-800 text-sm font-semibold transition-colors">
                                <span>Gestionar proveedores</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Citas -->
                    <div class="bg-white rounded-2xl p-6 card-hover shadow-lg border border-gray-100/50 relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-rose-500 to-rose-300"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-rose-500 to-rose-600 w-12 h-12 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-check text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800">{{ $totalCitas ?? 0 }}</p>
                                <span class="inline-block px-3 py-1 text-xs font-medium text-rose-600 bg-rose-50 rounded-full mt-1">
                                    {{ $citasHoy ?? 0 }} programadas hoy
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Sistema de Citas</h3>
                        <p class="text-sm text-gray-500">Agenda digitalizada</p>
                        <div class="mt-4 pt-4 border-t border-gray-100/50">
                            <a class="group inline-flex items-center text-rose-600 hover:text-rose-800 text-sm font-semibold transition-colors">
                                <span>Ver calendario</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endcan

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Quick Actions Section -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100/50 overflow-hidden">
                            <div class="p-6 border-b border-gray-100/50">
                                <h3 class="text-xl font-bold gradient-text flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#9d2449] to-[#be185d] rounded-lg flex items-center justify-center mr-3 icon-pulse">
                                        <i class="fas fa-bolt text-white"></i>
                                    </div>
                                    Acciones Rápidas
                                </h3>
                                <p class="text-gray-500 text-sm mt-2">Herramientas principales del sistema</p>
                            </div>
                            <div class="divide-y divide-gray-100/50">
                                <!-- Mis Trámites -->
                                <a class="group block p-6 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform">
                                            <i class="fas fa-file-lines text-white text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-base font-bold text-gray-800 group-hover:gradient-text transition-colors">Mis Trámites</h4>
                                            <p class="text-sm text-gray-500 mt-1">Gestiona y da seguimiento a tus solicitudes</p>
                                            <div class="flex items-center mt-2">
                                                <span class="text-xs text-blue-600 font-medium">Ver detalles</span>
                                                <i class="fas fa-chevron-right text-xs text-blue-600 ml-1 group-hover:translate-x-1 transition-transform"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <!-- Estado del Proveedor (Solo para Solicitantes, versión simplificada) -->
                                @if(!auth()->user()->hasRole('Proveedor'))
                                @can('mi-estado-proveedor.ver')
                                <a class="group block p-6 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform">
                                            <i class="fas fa-chart-line text-white text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-base font-bold text-gray-800 group-hover:gradient-text transition-colors">Estado del Proveedor</h4>
                                            <p class="text-sm text-gray-500 mt-1">Consulta tu estatus y certificaciones</p>
                                            <div class="flex items-center mt-2">
                                                <span class="text-xs text-green-600 font-medium">Verificar estado</span>
                                                <i class="fas fa-chevron-right text-xs text-green-600 ml-1 group-hover:translate-x-1 transition-transform"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endcan
                                @endif

                                <!-- Citas -->
                                <a  class="group block p-6 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform">
                                            <i class="fas fa-calendar-check text-white text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-base font-bold text-gray-800 group-hover:gradient-text transition-colors">Agendar Cita</h4>
                                            <p class="text-sm text-gray-500 mt-1">Programa tu visita para entrega de documentos</p>
                                            <div class="flex items-center mt-2">
                                                <span class="text-xs text-purple-600 font-medium">Programar cita</span>
                                                <i class="fas fa-chevron-right text-xs text-purple-600 ml-1 group-hover:translate-x-1 transition-transform"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Assistant Section with Action Button -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-lg h-full flex flex-col justify-between overflow-hidden relative" style="border: none;">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-transparent"></div>
                            


                            <!-- Content Section with Image and Button -->
                            <div class="flex-1 flex items-center relative z-10 p-6" style="border: none;">
                                <div class="flex items-center w-full">
                                    <!-- Left Side - Call to Action -->
                                    <div class="flex-1 pr-6">
                                        @if(auth()->user()->hasRole('Proveedor'))
                                        <!-- PROVEEDOR: Estado y Renovación -->
                                        <div class="space-y-4 -mt-8">
                                            <div class="text-center lg:text-left">
                                                <div class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-emerald-100 to-green-100 rounded-full text-xs font-semibold text-emerald-700 mb-3">
                                                    <i class="fas fa-certificate text-emerald-600 mr-1.5 text-xs"></i>
                                                    ¡Proveedor Activo!
                                                </div>
                                                <h4 class="text-2xl lg:text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-2">
                                                    ¡Bienvenido, 
                                                    <span class="bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">Proveedor Oficial</span>!
                                                </h4>
                                                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                                    Gestiona tu estatus en el 
                                                    <span class="font-semibold text-emerald-600">Padrón de Proveedores</span>
                                                </p>
                                            </div>
                                            
                                            @can('mi-estado-proveedor.ver')
                                            <div class="relative">
                                                <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-green-500 rounded-2xl blur opacity-20 transition duration-300"></div>
                                                
                                                <a 
                                                   class="glow-button relative group inline-flex items-center justify-center w-full lg:w-auto px-6 py-3.5 bg-gradient-to-r from-emerald-500 via-green-500 to-emerald-500 text-white font-bold rounded-xl hover:from-green-500 hover:via-emerald-500 hover:to-green-500 transition-all duration-300 transform hover:scale-102 shadow-xl hover:shadow-2xl">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="relative">
                                                            <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                                                                <i class="fas fa-chart-line text-white text-sm"></i>
                                                            </div>
                                                            <div class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-yellow-400 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-check text-yellow-800 text-xs"></i>
                                                            </div>
                                                        </div>
                                                        <div class="text-left">
                                                            <div class="text-lg font-bold">Mi Estado de Proveedor</div>
                                                            <div class="text-xs text-emerald-100 font-medium">Consultar padrón vigente</div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center">
                                                                <i class="fas fa-arrow-right text-white text-sm group-hover:translate-x-0.5 transition-transform duration-300"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            @endcan

                                            @if(auth()->check() && (auth()->user()->can('tramites-solicitante.renovacion') || auth()->user()->can('tramites-solicitante.actualizacion')))
                                            <div class="flex justify-center lg:justify-start">
                                                <a  
                                                   class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-emerald-50 to-green-50 text-emerald-700 font-semibold rounded-lg hover:from-emerald-100 hover:to-green-100 transition-all duration-300 shadow-sm hover:shadow-md text-sm">
                                                    <i class="fas fa-sync-alt text-emerald-600 mr-2 text-xs"></i>
                                                    <span>Renovar/Actualizar</span>
                                                </a>
                                            </div>
                                            @endif


                                        </div>
                                        
                                        @else
                                        <!-- SOLICITANTE: Iniciar Trámite -->
                                        <div class="space-y-8 -mt-6">
                                            <!-- Header Section -->
                                            <div class="text-center lg:text-left space-y-4">
                                                <!-- Badge mejorado -->
                                                <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-50 via-orange-50 to-red-50 rounded-full shadow-sm">
                                                    <div class="w-5 h-5 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mr-2">
                                                        <i class="fas fa-star text-white text-xs"></i>
                                                    </div>
                                                    <span class="text-sm font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                                                        ¡Únete al Padrón Oficial!
                                                    </span>
                                                </div>
                                                
                                                <!-- Título principal mejorado -->
                                                <div class="space-y-2">
                                                    <h4 class="text-3xl lg:text-4xl font-black leading-tight">
                                                        <span class="bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 bg-clip-text text-transparent">
                                                            Conviértete en
                                                        </span>
                                                        <br>
                                                        <span class="bg-gradient-to-r from-[#9d2449] via-[#be185d] to-[#9d2449] bg-clip-text text-transparent">
                                                            Proveedor Oficial
                                                        </span>
                                                    </h4>
                                                    
                                                    <!-- Descripción mejorada -->
                                                    <div class="max-w-md mx-auto lg:mx-0">
                                                        <p class="text-gray-600 text-base leading-relaxed">
                                                            Forma parte del exclusivo
                                                        </p>
                                                        <p class="font-bold text-lg bg-gradient-to-r from-[#9d2449] to-[#be185d] bg-clip-text text-transparent">
                                                            Padrón de Proveedores
                                                        </p>
                                                        <p class="text-gray-500 text-sm mt-2">
                                                            del Estado de Oaxaca
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Botón de acción más pequeño y bonito -->
                                            @can('tramites-solicitante.inscripcion')
                                            <div class="flex justify-center lg:justify-start">
                                                <div class="relative group">
                                                    <!-- Efecto de resplandor animado -->
                                                    <div class="absolute -inset-1 bg-gradient-to-r from-[#9d2449] via-pink-500 to-[#be185d] rounded-xl blur opacity-20 group-hover:opacity-40 transition-all duration-500 animate-pulse"></div>
                                                    
                                                    <a 
                                                       class="relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#9d2449] via-[#be185d] to-[#9d2449] text-white font-bold rounded-xl hover:from-[#be185d] hover:via-pink-500 hover:to-[#9d2449] transition-all duration-500 transform hover:scale-110 hover:rotate-1 shadow-lg hover:shadow-xl overflow-hidden">
                                                        
                                                        <!-- Efecto shimmer -->
                                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                                        
                                                        <div class="relative flex items-center space-x-3">
                                                            <!-- Icono con animaciones -->
                                                            <div class="relative">
                                                                <div class="w-8 h-8 bg-white/25 rounded-lg flex items-center justify-center backdrop-blur-sm group-hover:rotate-12 transition-transform duration-300">
                                                                    <i class="fas fa-rocket text-white text-sm group-hover:scale-110 transition-transform duration-300"></i>
                                                                </div>
                                                                <!-- Estrella brillante -->
                                                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-r from-yellow-300 to-yellow-500 rounded-full flex items-center justify-center shadow-sm">
                                                                    <i class="fas fa-star text-yellow-800 text-xs animate-spin group-hover:animate-pulse"></i>
                                                                </div>
                                                                <!-- Partículas brillantes -->
                                                                <div class="absolute -top-2 -left-2 w-2 h-2 bg-white rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping transition-all duration-300"></div>
                                                                <div class="absolute -bottom-1 -right-2 w-1.5 h-1.5 bg-pink-300 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-bounce transition-all duration-500 delay-100"></div>
                                                            </div>
                                                            
                                                            <!-- Texto del botón compacto -->
                                                            <div class="text-left">
                                                                <div class="text-base font-black tracking-wide group-hover:text-pink-100 transition-colors duration-300">Iniciar Mi Trámite</div>
                                                                <div class="text-xs text-pink-100 font-medium opacity-90 group-hover:opacity-100 transition-opacity duration-300">¡Tu futuro comienza aquí!</div>
                                                            </div>
                                                            
                                                            <!-- Flecha curiosa -->
                                                            <div class="ml-2">
                                                                <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                                                                    <i class="fas fa-arrow-right text-white text-sm group-hover:translate-x-1 group-hover:scale-125 transition-all duration-300"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            @endcan


                                        </div>
                                        @endif
                                    </div>

                                    <!-- Right Side - Assistant Image -->
                                    <div class="hidden lg:flex flex-1 justify-center items-end">
                                <img src="{{ asset('images/mujer_bienvenida.png') }}" 
                                     alt="Asistente Virtual" 
                                     class="w-auto h-[350px] object-contain drop-shadow-xl">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateDateTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = hours % 12 || 12;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;

            document.getElementById('currentTime').textContent = `${formattedHours}:${formattedMinutes} ${ampm}`;

            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            document.getElementById('currentDate').textContent = now.toLocaleDateString('es-ES', options);

            const greeting = document.getElementById('greeting');
            const userName = '{{ auth()->check() ? auth()->user()->name : 'Invitado' }}';

            if (hours < 12) {
                greeting.textContent = `Buenos días, ${userName}`;
            } else if (hours >= 12 && hours < 19) {
                greeting.textContent = `Buenas tardes, ${userName}`;
            } else {
                greeting.textContent = `Buenas noches, ${userName}`;
            }
        }

        updateDateTime();
        setInterval(updateDateTime, 60000);

        const cards = document.querySelectorAll('.card-hover');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endsection