@extends('layouts.app')

@section('title', 'Estado del Trámite')

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
                <a href="{{ route('tramites.solicitante.index') }}" 
                   class="flex items-center px-5 py-3 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-all duration-200 border border-gray-200 hover:border-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span class="font-medium">Volver</span>
                </a>
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
                @elseif($tramite->estado === 'Aprobado')
                    <div class="flex justify-center pt-6 border-t border-gray-100">
                        <a href="{{ route('citas.agendar', $tramite->id) }}" 
                           class="group px-8 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:shadow-xl transition-all duration-300 transform hover:scale-105 inline-block font-semibold">
                            <i class="fas fa-calendar-check mr-3 group-hover:animate-bounce"></i>Agendar mi Cita
                        </a>
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
                            <i class="fas fa-calendar-check mr-3 text-emerald-600"></i>
                            <span class="text-gray-700 font-medium">Agenda tu cita para finalizar</span>
                        </div>
                        <div class="flex items-center p-3 bg-white bg-opacity-80 rounded-lg">
                            <i class="fas fa-download mr-3 text-emerald-600"></i>
                            <span class="text-gray-700 font-medium">Descarga tu certificado</span>
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
                    @endphp
                    
                    <div class="flex items-center p-5 rounded-xl border-2 {{ $bgColor }} transition-all duration-300 shadow-sm hover:shadow-md">
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
                        
                        @if($seccionRechazada)
                            <button onclick="corregirSeccion({{ $tramite->id }}, {{ $numero }})" 
                                    class="group px-5 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg ml-4">
                                <i class="fas fa-edit mr-2 group-hover:animate-pulse"></i>Corregir
                            </button>
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

<script>
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