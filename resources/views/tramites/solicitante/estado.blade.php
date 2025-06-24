@extends('layouts.app')

@section('title', 'Estado del Trámite')

@section('content')
<!-- Beautiful Loading Modal -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full mx-auto overflow-hidden animate-modal-appear">
        <!-- Modal Header with Gradient -->
        <div class="bg-gradient-to-r from-[#9d2449] via-[#b83f65] to-[#8a203f] px-8 py-6">
            <div class="flex items-center justify-center space-x-3">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-alt text-white text-lg"></i>
                </div>
                <h3 class="text-2xl font-bold text-white">Estado del Trámite</h3>
            </div>
        </div>
        
        <!-- Modal Content -->
        <div class="px-8 py-12 text-center">
            <!-- Large Success Image -->
            <div class="relative mb-10">
                <div class="relative inline-block">
                    <img src="{{ asset('images/exito-elias.png') }}" 
                         alt="Trámite en Proceso" 
                         class="w-64 h-64 mx-auto object-contain drop-shadow-2xl animate-float">
                    
                    <!-- Animated Ring Around Image -->
                    <div class="absolute inset-0 rounded-full border-4 border-[#9d2449] opacity-30 animate-spin-slow"></div>
                    
                    <!-- Status Badge -->
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-2xl animate-pulse">
                        <i class="fas fa-eye text-white text-2xl"></i>
                    </div>
                    
                    <!-- Floating Particles -->
                    <div class="absolute top-8 -left-8 w-6 h-6 bg-[#9d2449] rounded-full opacity-40 animate-float-delayed"></div>
                    <div class="absolute top-16 -right-8 w-4 h-4 bg-blue-500 rounded-full opacity-50 animate-float-delayed-2"></div>
                    <div class="absolute -bottom-8 -left-6 w-5 h-5 bg-emerald-500 rounded-full opacity-45 animate-float-delayed-3"></div>
                </div>
            </div>
            
            <!-- Main Message -->
            <div class="mb-8">
                <h2 class="text-4xl font-bold text-gray-900 mb-4 animate-fade-in">
                    Trámite en Proceso de Revisión
                </h2>
                <div class="w-32 h-1 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mx-auto rounded-full mb-6"></div>
                <p class="text-xl text-gray-700 mb-4 animate-fade-in-delay">
                    Su solicitud ha sido recibida exitosamente
                </p>
                <p class="text-lg text-gray-600 animate-fade-in-delay-2">
                    Nuestro equipo especializado está revisando su documentación
                </p>
            </div>
            
            <!-- Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200 animate-slide-up">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-blue-900">Recibido</p>
                    <p class="text-xs text-blue-700">Documentos validados</p>
                </div>
                
                <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200 animate-slide-up" style="animation-delay: 0.2s">
                    <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-search text-white text-xl animate-pulse"></i>
                    </div>
                    <p class="text-sm font-semibold text-yellow-900">En Revisión</p>
                    <p class="text-xs text-yellow-700">Proceso actual</p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 animate-slide-up" style="animation-delay: 0.4s">
                    <div class="w-12 h-12 bg-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-bell text-white text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Notificación</p>
                    <p class="text-xs text-gray-600">Al completarse</p>
                </div>
            </div>
            
            <!-- Loading Animation -->
            <div class="flex justify-center items-center space-x-2 mb-6">
                <div class="w-3 h-3 bg-[#9d2449] rounded-full animate-bounce"></div>
                <div class="w-3 h-3 bg-[#b83f65] rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-3 h-3 bg-[#8a203f] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full max-w-md mx-auto">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Cargando panel de seguimiento</span>
                    <span class="font-semibold">75%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
                    <div class="bg-gradient-to-r from-[#9d2449] to-[#8a203f] h-3 rounded-full animate-progress-smooth shadow-sm"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div id="mainContent" class="min-h-screen py-8 opacity-0 transition-opacity duration-1000">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header with Image -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100 transform transition-all duration-700">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-col lg:flex-row items-center space-y-4 lg:space-y-0 lg:space-x-6">
                    <!-- Success Image -->
                    <div class="relative">
                        <img src="{{ asset('images/exito-elias.png') }}" 
                             alt="Éxito" 
                             class="w-20 h-20 lg:w-24 lg:h-24 object-contain rounded-xl shadow-lg">
                        <div class="absolute -top-2 -right-2 w-7 h-7 bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-file-alt text-white text-sm"></i>
                        </div>
                    </div>
                    
                    <div class="text-center lg:text-left">
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Estado del Trámite</h1>
                        <p class="text-lg text-gray-600 mb-1">Trámite ID: <span class="font-semibold text-[#9d2449]">{{ $tramite->id }}</span></p>
                        <p class="text-sm text-gray-500">Seguimiento en tiempo real de su proceso</p>
                    </div>
                </div>
                
                <a href="{{ route('tramites.solicitante.index') }}" 
                   class="flex items-center px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all duration-300 shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-3"></i>
                    <span class="font-medium">Volver al Panel</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Estado Actual - Sidebar Izquierdo -->
            <div class="xl:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-8">
                    <div class="text-center">
                        <div class="mx-auto mb-4 h-20 w-20 flex items-center justify-center rounded-2xl shadow-lg
                            {{ $tramite->getColorEstado() === 'green' ? 'bg-emerald-100' : '' }}
                        {{ $tramite->getColorEstado() === 'blue' ? 'bg-blue-100' : '' }}
                        {{ $tramite->getColorEstado() === 'red' ? 'bg-red-100' : '' }}
                            {{ $tramite->getColorEstado() === 'yellow' ? 'bg-amber-100' : '' }}">
                            <i class="fas text-3xl
                                {{ $tramite->estado === 'Aprobado' ? 'fa-check-circle text-emerald-600' : '' }}
                                {{ $tramite->estado === 'En Revision' ? 'fa-eye text-blue-600 animate-pulse' : '' }}
                            {{ $tramite->estado === 'Rechazado' ? 'fa-times-circle text-red-600' : '' }}
                                {{ $tramite->estado === 'Pendiente' ? 'fa-hourglass-half text-amber-600' : '' }}"></i>
                    </div>
                    
                        <h3 class="text-2xl font-bold mb-2
                            {{ $tramite->getColorEstado() === 'green' ? 'text-emerald-700' : '' }}
                            {{ $tramite->getColorEstado() === 'blue' ? 'text-blue-700' : '' }}
                            {{ $tramite->getColorEstado() === 'red' ? 'text-red-700' : '' }}
                            {{ $tramite->getColorEstado() === 'yellow' ? 'text-amber-700' : '' }}">
                            {{ $tramite->estado }}
                        </h3>
                        
                        @if($tramite->estado === 'En Revision')
                            <div class="mb-3 px-3 py-2 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-center space-x-2 text-blue-600">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></div>
                                    <span class="text-sm font-medium">Revisando...</span>
                                </div>
                            </div>
                        @endif
                        
                        <p class="text-gray-600 font-medium mb-3">{{ $tramite->tipo_tramite }}</p>
                        
                        <!-- Progreso Circular -->
                        <div class="relative w-24 h-24 mx-auto mb-4">
                            <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                                <circle cx="50" cy="50" r="45" fill="none" stroke="url(#gradient)" stroke-width="8" 
                                        stroke-linecap="round" stroke-dasharray="283" 
                                        stroke-dashoffset="{{ 283 - (283 * $tramite->getPorcentajeProgreso() / 100) }}"
                                        class="transition-all duration-1000"/>
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" style="stop-color:#9d2449"/>
                                        <stop offset="100%" style="stop-color:#8a203f"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-2xl font-bold text-[#9d2449]">{{ number_format($tramite->getPorcentajeProgreso(), 0) }}%</span>
                    </div>
                </div>
                        
                        <div class="text-sm text-gray-500 mb-4">
                            {{ $tramite->progreso_tramite }}/6 secciones completadas
                        </div>
                
                @if($tramite->estado === 'Rechazado' && $tramite->puedeSerEditado())
                    <button onclick="habilitarEdicion({{ $tramite->id }})" 
                                    class="w-full px-4 py-3 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-edit mr-2"></i>
                                <span class="font-medium">Editar Trámite</span>
                    </button>
                @elseif($tramite->estado === 'Aprobado')
                    <a href="{{ route('citas.agendar', $tramite->id) }}" 
                       class="w-full px-4 py-3 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105 animate-pulse text-center block">
                        <i class="fas fa-calendar-check mr-2"></i>
                        <span class="font-medium">Agendar Cita</span>
                    </a>
                @endif
            </div>
                </div>
            </div>
            
            <!-- Contenido Principal -->
            <div class="xl:col-span-3 space-y-8">
                <!-- Progreso del Trámite -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-center mb-8">
                        <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white mr-4">
                            <i class="fas fa-tasks text-lg"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Progreso Detallado</h2>
                    </div>
                    
                    <!-- Estado Especial para En Revision -->
                    @if($tramite->estado === 'En Revision')
                        <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-l-4 border-blue-500">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-check text-white text-lg"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-blue-900">Su trámite está siendo revisado</h3>
                                    <p class="text-blue-700">Nuestro equipo especializado está analizando su documentación</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-center space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <i class="fas fa-clock text-blue-500"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">Tiempo estimado</p>
                                        <p class="text-sm text-gray-600">3-5 días hábiles</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <i class="fas fa-bell text-blue-500"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">Notificaciones</p>
                                        <p class="text-sm text-gray-600">Le avisaremos por email</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-white rounded-lg shadow-sm">
                                    <i class="fas fa-shield-check text-blue-500"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">Seguridad</p>
                                        <p class="text-sm text-gray-600">Proceso certificado</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Barra de Progreso Principal -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-semibold text-gray-700">Progreso General</span>
                            <span class="text-xl font-bold text-[#9d2449]">{{ number_format($tramite->getPorcentajeProgreso(), 0) }}%</span>
                        </div>
                        <div class="relative w-full bg-gray-200 rounded-full h-4 shadow-inner">
                            <div class="absolute top-0 left-0 h-4 bg-gradient-to-r from-[#9d2449] via-[#b83f65] to-[#8a203f] rounded-full transition-all duration-1000 shadow-lg" 
                                 style="width: {{ $tramite->getPorcentajeProgreso() }}%">
                                <div class="absolute right-0 top-0 h-4 w-4 bg-white rounded-full shadow-lg border-2 border-[#9d2449] transform translate-x-1/2"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Secciones Grid -->
                @php
                    $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
                    $secciones = $tipoPersona === 'Moral' ? [
                            1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user-circle', 'descripcion' => 'Información básica del solicitante'],
                            2 => ['nombre' => 'Domicilio', 'icono' => 'fa-map-marker-alt', 'descripcion' => 'Dirección fiscal y comercial'],
                            3 => ['nombre' => 'Constitución', 'icono' => 'fa-building', 'descripcion' => 'Datos constitutivos de la empresa'],
                            4 => ['nombre' => 'Accionistas', 'icono' => 'fa-users', 'descripcion' => 'Información de socios y accionistas'],
                            5 => ['nombre' => 'Apoderado Legal', 'icono' => 'fa-user-tie', 'descripcion' => 'Representante legal autorizado'],
                            6 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload', 'descripcion' => 'Archivos y documentación requerida']
                        ] : [
                            1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user-circle', 'descripcion' => 'Información personal del solicitante'],
                            2 => ['nombre' => 'Domicilio', 'icono' => 'fa-map-marker-alt', 'descripcion' => 'Dirección de residencia'],
                            3 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload', 'descripcion' => 'Documentación personal requerida']
                    ];
                    
                    $progresoMaximo = $tipoPersona === 'Moral' ? 6 : 3;
                    $progresoMostrado = min($tramite->progreso_tramite, $progresoMaximo);
                @endphp
                
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($secciones as $numero => $seccion)
                    @php
                        $seccionRechazada = $tramite->seccionEstaRechazada($numero);
                        $seccionAprobada = $tramite->seccionEstaAprobada($numero);
                        $estadoRevision = $tramite->getEstadoSeccion($numero);
                        
                        if ($seccionRechazada) {
                            $bgColor = 'border-red-200 bg-red-50';
                            $iconBg = 'bg-red-100 text-red-600';
                                    $textColor = 'text-red-700';
                            $statusText = 'Rechazado - Requiere Corrección';
                                    $statusIcon = 'fas fa-exclamation-triangle text-red-500';
                        } elseif ($seccionAprobada) {
                                    $bgColor = 'border-emerald-200 bg-emerald-50';
                                    $iconBg = 'bg-emerald-100 text-emerald-600';
                                    $textColor = 'text-emerald-700';
                                    $statusText = 'Aprobado ✓';
                                    $statusIcon = 'fas fa-check-circle text-emerald-500';
                        } elseif ($progresoMostrado >= $numero) {
                            $bgColor = 'border-blue-200 bg-blue-50';
                            $iconBg = 'bg-blue-100 text-blue-600';
                                    $textColor = 'text-blue-700';
                            $statusText = 'En Revisión';
                                    $statusIcon = 'fas fa-eye text-blue-500 animate-pulse';
                        } else {
                            $bgColor = 'border-gray-200 bg-gray-50';
                                    $iconBg = 'bg-gray-100 text-gray-500';
                            $textColor = 'text-gray-600';
                            $statusText = 'Pendiente';
                            $statusIcon = 'fas fa-hourglass-half text-gray-400';
                        }
                    @endphp
                    
                            <div class="relative p-6 rounded-xl border-2 {{ $bgColor }} shadow-md hover:shadow-lg transition-all duration-300">
                                <!-- Status Badge -->
                                <div class="absolute top-4 right-4">
                            <i class="{{ $statusIcon }} text-lg"></i>
                        </div>
                        
                                <div class="flex items-start space-x-4">
                                    <div class="h-12 w-12 flex items-center justify-center rounded-xl {{ $iconBg }} shadow-sm">
                                        <i class="fas {{ $seccion['icono'] }} text-lg"></i>
                        </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-lg {{ $textColor }} mb-1">
                                {{ $seccion['nombre'] }}
                            </h4>
                                        <p class="text-sm text-gray-600 mb-3">{{ $seccion['descripcion'] }}</p>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $textColor }} bg-white bg-opacity-80">
                                {{ $statusText }}
                                        </span>
                            
                            @if($seccionRechazada && $estadoRevision && $estadoRevision->comentario)
                                            <div class="mt-4 p-3 bg-red-100 bg-opacity-80 rounded-lg">
                                                <p class="text-sm text-red-700 font-medium mb-2">
                                                    <i class="fas fa-comment-alt mr-2"></i>Comentario del revisor:
                                </p>
                                                <p class="text-sm text-red-800 italic mb-3">{{ $estadoRevision->comentario }}</p>
                                <button onclick="corregirSeccion({{ $tramite->id }}, {{ $numero }})" 
                                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors duration-200 shadow-md">
                                                    <i class="fas fa-edit mr-2"></i>Corregir Ahora
                                </button>
                                            </div>
                            @endif
                                    </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

                <!-- Información del Trámite -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white mr-4">
                            <i class="fas fa-info-circle text-lg"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Información del Trámite</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Fechas -->
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-4 flex items-center text-lg">
                                <i class="fas fa-calendar-alt mr-3 text-[#9d2449]"></i>
                                Fechas Importantes
                            </h3>
                            <div class="space-y-3">
                                <div class="flex flex-col space-y-1">
                                    <span class="text-sm text-gray-600">Fecha de inicio</span>
                                    <span class="font-semibold text-gray-900">{{ $tramite->fecha_inicio ? $tramite->fecha_inicio->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        @if($tramite->fecha_finalizacion)
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-sm text-gray-600">Fecha de finalización</span>
                                        <span class="font-semibold text-emerald-700">{{ $tramite->fecha_finalizacion->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        @if($tramite->fecha_revision)
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-sm text-gray-600">Fecha de revisión</span>
                                        <span class="font-semibold text-blue-700">{{ $tramite->fecha_revision->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                        <!-- Detalles -->
                <div>
                            <h3 class="font-semibold text-gray-900 mb-4 flex items-center text-lg">
                                <i class="fas fa-clipboard-list mr-3 text-[#9d2449]"></i>
                                Detalles del Trámite
                            </h3>
                            <div class="space-y-3">
                                <div class="flex flex-col space-y-1">
                                    <span class="text-sm text-gray-600">Tipo de trámite</span>
                                    <span class="font-semibold text-gray-900">{{ ucfirst($tramite->tipo_tramite) }}</span>
                        </div>
                                <div class="flex flex-col space-y-1">
                                    <span class="text-sm text-gray-600">Sección actual</span>
                                    <span class="font-semibold text-gray-900">{{ $tramite->getNombreSeccionActual() }}</span>
                        </div>
                        @if($tramite->revisor)
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-sm text-gray-600">Revisado por</span>
                                        <span class="font-semibold text-gray-900">{{ $tramite->revisor->nombre }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($tramite->observaciones)
                        <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-sticky-note mr-2 text-[#9d2449]"></i>
                                Observaciones
                            </h3>
                            <p class="text-gray-700 leading-relaxed">{{ $tramite->observaciones }}</p>
                </div>
            @endif
        </div>

        <!-- Información sobre qué sigue -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 border-2 border-blue-200 rounded-2xl p-8 shadow-lg">
                    <h3 class="font-bold text-blue-900 mb-6 text-xl flex items-center">
                        <i class="fas fa-lightbulb mr-3 text-yellow-500 text-2xl"></i>
                ¿Qué sigue?
            </h3>
                    <div class="text-blue-800 space-y-4">
                @if($tramite->estado === 'En Revision')
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-search text-blue-600 mt-1 text-lg"></i>
                                <p>Su trámite está siendo revisado por nuestro equipo especializado</p>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-bell text-blue-600 mt-1 text-lg"></i>
                                <p>Recibirá una notificación cuando la revisión esté completa</p>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-clock text-blue-600 mt-1 text-lg"></i>
                                <p>Tiempo estimado de revisión: 3-5 días hábiles</p>
                            </div>
                @elseif($tramite->estado === 'Rechazado')
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-exclamation-triangle text-red-600 mt-1 text-lg"></i>
                                <p>Su trámite requiere correcciones específicas</p>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-edit text-red-600 mt-1 text-lg"></i>
                                <p>Revise las observaciones y use "Editar Trámite" para corregir</p>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-redo text-red-600 mt-1 text-lg"></i>
                                <p>Una vez corregido, será enviado automáticamente para revisión</p>
                            </div>
                @elseif($tramite->estado === 'Aprobado')
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-trophy text-emerald-600 mt-1 text-lg"></i>
                                <p>¡Felicidades! Su trámite ha sido aprobado exitosamente</p>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-download text-emerald-600 mt-1 text-lg"></i>
                                <p>Puede descargar su certificado desde el panel principal</p>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-shield-alt text-emerald-600 mt-1 text-lg"></i>
                                <p>Conserve este documento para sus registros oficiales</p>
                            </div>
                @else
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-tasks text-amber-600 mt-1 text-lg"></i>
                                <p>Complete todas las secciones del formulario paso a paso</p>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-upload text-amber-600 mt-1 text-lg"></i>
                                <p>Suba todos los documentos requeridos en cada sección</p>
                            </div>
                            <div class="flex items-start space-x-4">
                                <i class="fas fa-paper-plane text-amber-600 mt-1 text-lg"></i>
                                <p>Envíe el trámite completo para iniciar la revisión</p>
                            </div>
                @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-delay {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes modal-appear {
    from { 
        opacity: 0; 
        transform: scale(0.9) translateY(-20px); 
    }
    to { 
        opacity: 1; 
        transform: scale(1) translateY(0); 
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

@keyframes float-delayed-2 {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-6px); }
}

@keyframes float-delayed-3 {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-12px); }
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes slide-up {
    from { 
        opacity: 0; 
        transform: translateY(20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

@keyframes progress-smooth {
    from { width: 0%; }
    to { width: 75%; }
}

.animate-modal-appear {
    animation: modal-appear 0.6s ease-out forwards;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 2.5s ease-in-out infinite;
    animation-delay: 0.5s;
}

.animate-float-delayed-2 {
    animation: float-delayed-2 3.5s ease-in-out infinite;
    animation-delay: 1s;
}

.animate-float-delayed-3 {
    animation: float-delayed-3 2.8s ease-in-out infinite;
    animation-delay: 1.5s;
}

.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}

.animate-slide-up {
    animation: slide-up 0.8s ease-out forwards;
    opacity: 0;
}

.animate-fade-in {
    animation: fade-in 1.2s ease-out forwards;
}

.animate-fade-in-delay {
    animation: fade-in-delay 1.2s ease-out 0.6s forwards;
    opacity: 0;
}

.animate-fade-in-delay-2 {
    animation: fade-in-delay 1.2s ease-out 1.2s forwards;
    opacity: 0;
}

.animate-progress-smooth {
    animation: progress-smooth 3s ease-out forwards;
}

/* Time slot hover and selection effects */
.time-slot {
    position: relative;
    overflow: hidden;
}

.time-slot::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(157, 36, 73, 0.1), transparent);
    transition: left 0.5s;
}

.time-slot:hover::before {
    left: 100%;
}

/* Enhanced modal backdrop */
#appointmentModal {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

/* Custom scrollbar for modal content */
.modal-content::-webkit-scrollbar {
    width: 6px;
}

.modal-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.modal-content::-webkit-scrollbar-thumb {
    background: #9d2449;
    border-radius: 3px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: #8a203f;
}
</style>

<script>
// Loading Overlay Animation
document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    const mainContent = document.getElementById('mainContent');
    
    // Show loading overlay for 3.5 seconds
    setTimeout(() => {
        loadingOverlay.style.opacity = '0';
        loadingOverlay.style.transform = 'scale(0.95)';
        loadingOverlay.style.transition = 'all 0.8s ease-out';
        
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
            mainContent.style.opacity = '1';
        }, 800);
    }, 3500);
});

async function habilitarEdicion(tramiteId) {
    if (!confirm('¿Está seguro de que desea habilitar la edición de este trámite?')) {
        return;
    }
    
    try {
        const response = await fetch(`/tramites-solicitante/habilitar-edicion/${tramiteId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                window.location.reload();
            }
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al habilitar la edición del trámite');
    }
}

async function corregirSeccion(tramiteId, seccionId) {
    if (!confirm('¿Desea ir a corregir esta sección?')) {
        return;
    }
    
    try {
        const response = await fetch(`/tramites-solicitante/corregir-seccion/${tramiteId}/${seccionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al habilitar la corrección de la sección');
    }
}


</script>
@endsection 