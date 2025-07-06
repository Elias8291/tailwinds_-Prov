@extends('layouts.app')

@section('title', 'Estado del Trámite')

@push('styles')
<style>
    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            max-height: 1000px;
            transform: translateY(0);
        }
    }

    .animate-slideDown {
        animation: slideDown 0.3s ease-out forwards;
    }

    /* Mejora del cursor para elementos clickeables */
    .cursor-pointer:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease;
    }

    /* Efecto hover para los documentos individuales */
    .documento-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    /* Smooth transition para chevron */
    #chevron-3, #chevron-6 {
        transition: transform 0.3s ease;
    }

    /* Estilos para notificaciones */
    .notificacion-estado {
        transform: translateX(100%);
        transition: transform 0.3s ease-out;
    }

    /* Animación de pulso para barra de progreso */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .progress-pulse {
        animation: pulse 1.5s infinite;
    }

    /* Mejoras para botones de subida */
    .btn-upload:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-upload:active {
        transform: translateY(0);
    }

    /* Efecto de escala para documentos actualizados */
    .documento-updated {
        animation: scaleUpdate 0.6s ease-out;
    }

    @keyframes scaleUpdate {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    /* Estilos para el indicador de actualización */
    #indicador-actualizacion {
        backdrop-filter: blur(10px);
        transform: translateX(-100%);
        animation: slideInLeft 0.3s ease-out forwards;
    }

    @keyframes slideInLeft {
        from {
            transform: translateX(-100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Animación para elementos que se actualizan */
    .elemento-actualizado {
        animation: highlight 0.8s ease-out;
    }

    @keyframes highlight {
        0% { background-color: rgba(59, 130, 246, 0.1); }
        50% { background-color: rgba(59, 130, 246, 0.2); }
        100% { background-color: transparent; }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 space-y-8">
        
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-5">
                    <div class="w-14 h-14 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-clipboard-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Estado del Trámite</h1>
                        <p class="text-gray-600 text-lg">{{ $tramite->tipo_tramite }} <span class="text-gray-400">•</span> <span class="font-medium text-[#9d2449]">ID: {{ $tramite->id }}</span></p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Botón de control de actualizaciones -->
                    <button id="btn-toggle-polling" 
                            onclick="togglePolling()"
                            class="flex items-center px-4 py-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200 border border-blue-200 hover:border-blue-300 text-sm">
                        <i id="polling-icon" class="fas fa-sync-alt mr-2"></i>
                        <span id="polling-text">Actualizando...</span>
                    </button>

                    <a href="{{ route('tramites.solicitante.index') }}" 
                       class="flex items-center px-5 py-3 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-all duration-200 border border-gray-200 hover:border-gray-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span class="font-medium">Volver</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Estado Principal -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center shadow-lg
                                {{ $tramite->estado === 'Aprobado' ? 'bg-emerald-50 border-2 border-emerald-200' : '' }}
                                {{ $tramite->estado === 'En Revision' ? 'bg-blue-50 border-2 border-blue-200' : '' }}
                                {{ $tramite->estado === 'Rechazado' ? 'bg-red-50 border-2 border-red-200' : '' }}
                                {{ $tramite->estado === 'Pendiente' ? 'bg-amber-50 border-2 border-amber-200' : '' }}">
                                <i class="text-3xl
                                    {{ $tramite->estado === 'Aprobado' ? 'fas fa-check-circle text-emerald-600' : '' }}
                                    {{ $tramite->estado === 'En Revision' ? 'fas fa-clock text-blue-600' : '' }}
                                    {{ $tramite->estado === 'Rechazado' ? 'fas fa-exclamation-circle text-red-600' : '' }}
                                    {{ $tramite->estado === 'Pendiente' ? 'fas fa-hourglass-half text-amber-600' : '' }}"></i>
                            </div>
                            @if($tramite->estado === 'En Revision')
                                <div class="absolute -top-1 -right-1 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold mb-2
                                {{ $tramite->estado === 'Aprobado' ? 'text-emerald-700' : '' }}
                                {{ $tramite->estado === 'En Revision' ? 'text-blue-700' : '' }}
                                {{ $tramite->estado === 'Rechazado' ? 'text-red-700' : '' }}
                                {{ $tramite->estado === 'Pendiente' ? 'text-amber-700' : '' }}">
                                {{ $tramite->estado }}
                            </h2>
                            <p class="text-gray-600 text-lg leading-relaxed">
                                @if($tramite->estado === 'Aprobado')
                                    🎉 Su trámite ha sido aprobado exitosamente
                                @elseif($tramite->estado === 'En Revision')
                                    👀 Estamos revisando su documentación cuidadosamente
                                @elseif($tramite->estado === 'Rechazado')
                                    ⚠️ Su trámite requiere algunas correcciones
                                @else
                                    📝 Complete su trámite para continuar con el proceso
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- Progreso Elegante -->
                    <div class="text-center">
                        <div class="relative w-24 h-24 mx-auto mb-3">
                            <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="6"/>
                                <circle cx="50" cy="50" r="40" fill="none" stroke="url(#progressGradient)" stroke-width="6" 
                                        stroke-linecap="round" stroke-dasharray="251" 
                                        stroke-dashoffset="{{ 251 - (251 * $tramite->getPorcentajeProgreso() / 100) }}"
                                        class="transition-all duration-1000 ease-out"/>
                                <defs>
                                    <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" style="stop-color:#9d2449"/>
                                        <stop offset="50%" style="stop-color:#b83f65"/>
                                        <stop offset="100%" style="stop-color:#7a1d3a"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <span class="text-xl font-bold text-[#9d2449]">{{ number_format($tramite->getPorcentajeProgreso(), 0) }}%</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">{{ $tramite->progreso_tramite }}/{{ $tramite->solicitante->tipo_persona === 'Moral' ? 6 : 3 }} secciones completadas</p>
                    </div>
                </div>

                <!-- Botones de Acción Elegantes -->
                @if($tramite->estado === 'Rechazado' && $tramite->puedeSerEditado())
                    <div class="flex justify-center pt-6 border-t border-gray-100">
                        <button onclick="habilitarEdicion({{ $tramite->id }})" 
                                class="group px-8 py-4 bg-gradient-to-r from-[#9d2449] to-[#7a1d3a] text-white rounded-xl hover:shadow-xl transition-all duration-300 transform hover:scale-105 font-semibold">
                            <i class="fas fa-edit mr-3 group-hover:animate-pulse"></i>Corregir mi Trámite
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detalles del Trámite -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Información General -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md mr-4">
                        <i class="fas fa-info-circle text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Información General</h3>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-gray-600 font-medium">Fecha de inicio:</span>
                        <span class="font-semibold text-gray-800">{{ $tramite->fecha_inicio ? $tramite->fecha_inicio->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    @if($tramite->fecha_finalizacion)
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-gray-600 font-medium">Finalizado:</span>
                        <span class="font-semibold text-emerald-600">{{ $tramite->fecha_finalizacion->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($tramite->revisor)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 font-medium">Revisor asignado:</span>
                        <span class="font-semibold text-gray-800">{{ $tramite->revisor->nombre }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Estado Especial -->
            @if($tramite->estado === 'En Revision')
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200 p-6 shadow-lg">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center shadow-md mr-4">
                        <i class="fas fa-eye text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900">En Revisión</h3>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-white bg-opacity-60 rounded-lg">
                        <i class="fas fa-clock mr-3 text-blue-600"></i>
                        <span class="text-blue-800 font-medium">Tiempo estimado: 3-5 días hábiles</span>
                    </div>
                    <div class="flex items-center p-3 bg-white bg-opacity-60 rounded-lg">
                        <i class="fas fa-bell mr-3 text-blue-600"></i>
                        <span class="text-blue-800 font-medium">Te notificaremos por email</span>
                    </div>
                    <div class="flex items-center p-3 bg-white bg-opacity-60 rounded-lg">
                        <i class="fas fa-shield-check mr-3 text-blue-600"></i>
                        <span class="text-blue-800 font-medium">Proceso seguro y confiable</span>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 p-6 shadow-lg">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center shadow-md mr-4">
                        <i class="fas fa-lightbulb text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">¿Qué sigue?</h3>
                </div>
                <div class="space-y-4">
                    @if($tramite->estado === 'Aprobado')
                        <div class="flex items-center p-3 bg-white bg-opacity-80 rounded-lg">
                            <i class="fas fa-check-circle mr-3 text-emerald-600"></i>
                            <span class="text-gray-700 font-medium">Su trámite ha sido aprobado exitosamente</span>
                        </div>
                        
                        @if($tramite->cita)
                            <div class="bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-calendar-check mr-3 text-emerald-600 text-lg"></i>
                                    <span class="text-gray-800 font-bold">Cita Programada</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-calendar-alt mr-2 text-emerald-600"></i>
                                        <span class="text-gray-700">
                                            <strong>Fecha:</strong> {{ $tramite->cita->fecha_hora->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-clock mr-2 text-emerald-600"></i>
                                        <span class="text-gray-700">
                                            <strong>Hora:</strong> {{ $tramite->cita->fecha_hora->format('H:i') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-calendar-day mr-2 text-emerald-600"></i>
                                        <span class="text-gray-700">
                                            <strong>Día:</strong> 
                                            @php
                                                $dias = [
                                                    'Monday' => 'Lunes',
                                                    'Tuesday' => 'Martes', 
                                                    'Wednesday' => 'Miércoles',
                                                    'Thursday' => 'Jueves',
                                                    'Friday' => 'Viernes',
                                                    'Saturday' => 'Sábado',
                                                    'Sunday' => 'Domingo'
                                                ];
                                                echo $dias[$tramite->cita->fecha_hora->format('l')] ?? $tramite->cita->fecha_hora->format('l');
                                            @endphp
                                        </span>
                                    </div>
                                    <div class="flex items-start text-sm pt-2 border-t border-emerald-200">
                                        <i class="fas fa-map-marker-alt mr-2 text-emerald-600 mt-1"></i>
                                        <span class="text-gray-700">
                                            <strong>Ubicación:</strong><br>
                                            Ciudad Administrativa de Oaxaca<br>
                                            Edificio 1, Módulo de Proveedores
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex items-center p-3 bg-white bg-opacity-80 rounded-lg">
                            <i class="fas fa-file-signature mr-3 text-emerald-600"></i>
                            <span class="text-gray-700 font-medium">Asiste a tu cita para finalizar el proceso</span>
                        </div>
                    @elseif($tramite->estado === 'Rechazado')
                        <div class="flex items-center p-3 bg-white bg-opacity-80 rounded-lg">
                            <i class="fas fa-edit mr-3 text-red-600"></i>
                            <span class="text-gray-700 font-medium">Revisa los comentarios del revisor</span>
                        </div>
                        <div class="flex items-center p-3 bg-white bg-opacity-80 rounded-lg">
                            <i class="fas fa-redo mr-3 text-red-600"></i>
                            <span class="text-gray-700 font-medium">Corrige y vuelve a enviar</span>
                        </div>
                    @else
                        <div class="flex items-center p-3 bg-white bg-opacity-80 rounded-lg">
                            <i class="fas fa-tasks mr-3 text-amber-600"></i>
                            <span class="text-gray-700 font-medium">Completa todas las secciones</span>
                        </div>
                        <div class="flex items-center p-3 bg-white bg-opacity-80 rounded-lg">
                            <i class="fas fa-upload mr-3 text-amber-600"></i>
                            <span class="text-gray-700 font-medium">Sube todos los documentos requeridos</span>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Progreso de Secciones -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md mr-4">
                    <i class="fas fa-list-check text-white text-lg"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Progreso de Secciones</h3>
            </div>
            
            @php
                $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
                $secciones = $tipoPersona === 'Moral' ? [
                    1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user-circle'],
                    2 => ['nombre' => 'Domicilio', 'icono' => 'fa-map-marker-alt'],
                    3 => ['nombre' => 'Constitución', 'icono' => 'fa-building'],
                    4 => ['nombre' => 'Accionistas', 'icono' => 'fa-users'],
                    5 => ['nombre' => 'Apoderado Legal', 'icono' => 'fa-user-tie'],
                    6 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload']
                ] : [
                    1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user-circle'],
                    2 => ['nombre' => 'Domicilio', 'icono' => 'fa-map-marker-alt'],
                    3 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload']
                ];
                
                $progresoMaximo = $tipoPersona === 'Moral' ? 6 : 3;
                $progresoMostrado = min($tramite->progreso_tramite, $progresoMaximo);
            @endphp
            
            <div class="space-y-5">
                @foreach($secciones as $numero => $seccion)
                    @php
                        $seccionRechazada = $tramite->seccionEstaRechazada($numero);
                        $seccionAprobada = $tramite->seccionEstaAprobada($numero);
                        $estadoRevision = $tramite->getEstadoSeccion($numero);
                        
                        // Lógica especial para la sección de documentos
                        if ($seccion['nombre'] === 'Documentos') {
                            $documentosIndividuales = $tramite->documentosSolicitante()->get();
                            $totalDocs = $documentosIndividuales->count();
                            $aprobados = $documentosIndividuales->where('estado', 'Aprobado')->count();
                            $rechazados = $documentosIndividuales->where('estado', 'Rechazado')->count();
                            $enRevision = $documentosIndividuales->whereIn('estado', ['En Revision', 'Pendiente'])->count();
                            
                            if ($totalDocs === 0) {
                                // Sin documentos
                                $bgColor = 'bg-gray-50 border-gray-200 hover:bg-gray-100';
                                $iconColor = 'text-gray-500';
                                $statusColor = 'text-gray-600';
                                $statusText = 'Sin documentos';
                                $statusIcon = 'fa-inbox';
                                $iconBg = 'bg-gray-100';
                            } elseif ($rechazados > 0) {
                                // Algunos documentos rechazados
                                $bgColor = 'bg-red-50 border-red-200 hover:bg-red-100';
                                $iconColor = 'text-red-600';
                                $statusColor = 'text-red-700';
                                $statusText = "$rechazados documento(s) rechazado(s)";
                                $statusIcon = 'fa-exclamation-circle';
                                $iconBg = 'bg-red-100';
                                $seccionRechazada = true; // Para mostrar botón de corrección
                            } elseif ($aprobados === $totalDocs) {
                                // Todos los documentos aprobados
                                $bgColor = 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100';
                                $iconColor = 'text-emerald-600';
                                $statusColor = 'text-emerald-700';
                                $statusText = "Todos los documentos aprobados ($aprobados/$totalDocs)";
                                $statusIcon = 'fa-check-circle';
                                $iconBg = 'bg-emerald-100';
                            } else {
                                // Documentos en revisión o mixto
                                $bgColor = 'bg-blue-50 border-blue-200 hover:bg-blue-100';
                                $iconColor = 'text-blue-600';
                                $statusColor = 'text-blue-700';
                                $statusText = "En revisión ($aprobados aprobados, $enRevision pendientes)";
                                $statusIcon = 'fa-clock';
                                $iconBg = 'bg-blue-100';
                            }
                        } else {
                            // Lógica original para otras secciones
                            if ($seccionRechazada) {
                                $bgColor = 'bg-red-50 border-red-200 hover:bg-red-100';
                                $iconColor = 'text-red-600';
                                $statusColor = 'text-red-700';
                                $statusText = 'Requiere corrección';
                                $statusIcon = 'fa-exclamation-circle';
                                $iconBg = 'bg-red-100';
                            } elseif ($seccionAprobada) {
                                $bgColor = 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100';
                                $iconColor = 'text-emerald-600';
                                $statusColor = 'text-emerald-700';
                                $statusText = 'Completado';
                                $statusIcon = 'fa-check-circle';
                                $iconBg = 'bg-emerald-100';
                            } elseif ($progresoMostrado >= $numero) {
                                $bgColor = 'bg-blue-50 border-blue-200 hover:bg-blue-100';
                                $iconColor = 'text-blue-600';
                                $statusColor = 'text-blue-700';
                                $statusText = 'En revisión';
                                $statusIcon = 'fa-clock';
                                $iconBg = 'bg-blue-100';
                            } else {
                                $bgColor = 'bg-gray-50 border-gray-200 hover:bg-gray-100';
                                $iconColor = 'text-gray-500';
                                $statusColor = 'text-gray-600';
                                $statusText = 'Pendiente';
                                $statusIcon = 'fa-circle';
                                $iconBg = 'bg-gray-100';
                            }
                        }
                    @endphp
                    
                    <div class="rounded-xl border-2 {{ $bgColor }} transition-all duration-300 shadow-sm hover:shadow-md overflow-hidden">
                        <div class="flex items-center p-5 cursor-pointer" 
                             @if($seccion['nombre'] === 'Documentos') onclick="toggleDocumentos({{ $numero }})" @endif>
                            <div class="flex items-center flex-1">
                                <div class="w-14 h-14 rounded-full {{ $iconBg }} shadow-sm flex items-center justify-center mr-5">
                                    <i class="fas {{ $seccion['icono'] }} {{ $iconColor }} text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 text-lg mb-1">{{ $seccion['nombre'] }}</h4>
                                    <p class="text-sm {{ $statusColor }} flex items-center font-medium">
                                        <i class="fas {{ $statusIcon }} mr-2"></i>
                                        {{ $statusText }}
                                    </p>
                                </div>
                            </div>
                            
                            @if($seccion['nombre'] === 'Documentos')
                                <div class="flex items-center mr-4">
                                    <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300" id="chevron-{{ $numero }}"></i>
                                </div>
                            @endif
                            
                            @if($seccionRechazada)
                                <button onclick="corregirSeccion({{ $tramite->id }}, {{ $numero }})" 
                                        class="group px-5 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg ml-4">
                                    <i class="fas fa-edit mr-2 group-hover:animate-pulse"></i>Corregir
                                </button>
                            @endif
                        </div>
                        
                        @if($seccion['nombre'] === 'Documentos')
                            <!-- Panel expandible de documentos -->
                            <div id="documentos-panel-{{ $numero }}" class="hidden border-t border-gray-200 bg-white bg-opacity-50">
                                <div class="p-6">
                                    <h5 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-file-check mr-2 text-blue-600"></i>
                                        Estado de Documentos Individuales
                                    </h5>
                                    
                                    @php
                                        $documentosIndividuales = $tramite->documentosSolicitante()->with('documento')->get();
                                    @endphp
                                    
                                    @if($documentosIndividuales->count() > 0)
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($documentosIndividuales as $documento)
                                                @php
                                                    $estadoDoc = $documento->estado ?? 'Pendiente';
                                                    if ($estadoDoc === 'Aprobado') {
                                                        $colorDoc = 'bg-emerald-50 border-emerald-200';
                                                        $iconoDoc = 'fa-check-circle text-emerald-600';
                                                        $textoDoc = 'text-emerald-700';
                                                    } elseif ($estadoDoc === 'Rechazado') {
                                                        $colorDoc = 'bg-red-50 border-red-200';
                                                        $iconoDoc = 'fa-times-circle text-red-600';
                                                        $textoDoc = 'text-red-700';
                                                    } elseif ($estadoDoc === 'En Revision' || $estadoDoc === 'Pendiente') {
                                                        $colorDoc = 'bg-blue-50 border-blue-200';
                                                        $iconoDoc = 'fa-clock text-blue-600';
                                                        $textoDoc = 'text-blue-700';
                                                    } else {
                                                        $colorDoc = 'bg-gray-50 border-gray-200';
                                                        $iconoDoc = 'fa-circle text-gray-600';
                                                        $textoDoc = 'text-gray-700';
                                                    }
                                                @endphp
                                                
                                                <div class="documento-card border-2 {{ $colorDoc }} rounded-lg p-4 transition-all duration-200" data-documento-id="{{ $documento->id }}">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <h6 class="font-semibold text-gray-900 text-sm truncate pr-2">
                                                            {{ $documento->documento->nombre ?? 'Documento sin nombre' }}
                                                        </h6>
                                                        <div class="flex items-center {{ $textoDoc }} text-xs font-medium">
                                                            <i class="fas {{ $iconoDoc }} mr-1"></i>
                                                            <span>{{ $estadoDoc === 'En Revision' ? 'En Revisión' : $estadoDoc }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($documento->documento->descripcion)
                                                        <p class="text-xs text-gray-600 mb-2">{{ $documento->documento->descripcion }}</p>
                                                    @endif
                                                    
                                                    @if($documento->observaciones && $estadoDoc === 'Rechazado')
                                                        <div class="mt-3 p-2 bg-red-100 rounded border-l-2 border-red-400">
                                                            <p class="text-xs text-red-800 font-medium">Observaciones:</p>
                                                            <p class="text-xs text-red-700 mt-1">{{ $documento->observaciones }}</p>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($documento->documento_cotejado)
                                                        <div class="mt-2 flex items-center text-xs text-emerald-600">
                                                            <i class="fas fa-check-double mr-1"></i>
                                                            <span>Cotejado físicamente</span>
                                                        </div>
                                                    @endif
                                                    
                                                     @if($documento->fecha_entrega)
                                                         <div class="mt-2 text-xs text-gray-500">
                                                             <i class="fas fa-calendar mr-1"></i>
                                                             Entregado: {{ $documento->fecha_entrega->format('d/m/Y') }}
                                                         </div>
                                                     @endif
                                                     
                                                     @if($estadoDoc === 'Rechazado')
                                                         <!-- Botón para subir nuevo documento -->
                                                         <div class="mt-3 border-t border-red-200 pt-3">
                                                             <input type="file" 
                                                                    id="file-{{ $documento->id }}" 
                                                                    accept=".pdf"
                                                                    class="hidden"
                                                                    onchange="handleFileChange({{ $documento->id }}, this)">
                                                             <button onclick="document.getElementById('file-{{ $documento->id }}').click()"
                                                                      class="btn-upload w-full flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                                                                      id="btn-subir-{{ $documento->id }}">
                                                                 <i class="fas fa-upload mr-2"></i>
                                                                 Subir Nuevo Documento
                                                             </button>
                                                             
                                                             <!-- Indicador de progreso -->
                                                             <div id="progress-{{ $documento->id }}" class="hidden mt-2">
                                                                 <div class="w-full bg-red-100 rounded-full h-2">
                                                                     <div class="bg-red-600 h-2 rounded-full transition-all duration-300" style="width: 0%" id="progress-bar-{{ $documento->id }}"></div>
                                                                 </div>
                                                                 <div class="text-xs text-red-600 mt-1 text-center" id="progress-text-{{ $documento->id }}">
                                                                     Subiendo documento...
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Resumen estadístico -->
                                        @php
                                            $totalDocs = $documentosIndividuales->count();
                                            $aprobados = $documentosIndividuales->where('estado', 'Aprobado')->count();
                                            $rechazados = $documentosIndividuales->where('estado', 'Rechazado')->count();
                                            $enRevision = $documentosIndividuales->whereIn('estado', ['En Revision', 'Pendiente'])->count();
                                        @endphp
                                        
                                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <h6 class="text-sm font-bold text-gray-800 mb-3">Resumen de Documentos</h6>
                                            <div class="grid grid-cols-3 gap-4 text-center">
                                                <div class="flex flex-col">
                                                    <span class="text-emerald-600 font-bold text-lg">{{ $aprobados }}</span>
                                                    <span class="text-xs text-gray-600">Aprobados</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-red-600 font-bold text-lg">{{ $rechazados }}</span>
                                                    <span class="text-xs text-gray-600">Rechazados</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-blue-600 font-bold text-lg">{{ $enRevision }}</span>
                                                    <span class="text-xs text-gray-600">En Revisión</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="fas fa-inbox text-gray-400 text-xl"></i>
                                            </div>
                                            <p class="text-gray-500 font-medium">No hay documentos adjuntos</p>
                                            <p class="text-gray-400 text-sm mt-1">Los documentos aparecerán aquí cuando sean subidos</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if($seccionRechazada && $estadoRevision && $estadoRevision->comentario)
                        <div class="ml-16 mr-6 p-5 bg-red-100 rounded-lg border-l-4 border-red-400 shadow-sm">
                            <div class="flex items-start">
                                <i class="fas fa-comment-dots text-red-600 mr-3 mt-1"></i>
                                <div>
                                    <p class="font-semibold text-red-800 mb-1">Comentario del revisor:</p>
                                    <p class="text-red-700 leading-relaxed">{{ $estadoRevision->comentario }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Observaciones -->
        @if($tramite->observaciones)
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200 p-6 shadow-lg">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center shadow-md mr-4">
                    <i class="fas fa-sticky-note text-white text-lg"></i>
                </div>
                <h3 class="text-xl font-bold text-amber-900">Observaciones Generales</h3>
            </div>
            <div class="bg-white bg-opacity-70 rounded-lg p-4">
                <p class="text-amber-800 leading-relaxed font-medium">{{ $tramite->observaciones }}</p>
            </div>
        </div>
        @endif

    </div>
</div>

<script src="{{ asset('js/modules/estado-tramite-handler.js') }}"></script>
<script>
// Inicializar el handler cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    initEstadoTramiteHandler({{ $tramite->id }});
});
</script>
@endsection 