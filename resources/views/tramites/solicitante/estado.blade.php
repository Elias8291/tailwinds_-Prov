@extends('layouts.app')

@section('title', 'Estado del Trámite')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-md">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Estado del Trámite</h1>
                        <p class="text-gray-600">Trámite ID: {{ $tramite->id }}</p>
                    </div>
                </div>
                
                <a href="{{ route('tramites.solicitante.index') }}" 
                   class="flex items-center px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver
                </a>
            </div>
        </div>

        <!-- Estado Actual -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Estado Actual</h2>
            
            <div class="flex items-center justify-between p-6 rounded-xl border-2 
                {{ $tramite->getColorEstado() === 'green' ? 'border-green-200 bg-green-50' : '' }}
                {{ $tramite->getColorEstado() === 'blue' ? 'border-blue-200 bg-blue-50' : '' }}
                {{ $tramite->getColorEstado() === 'red' ? 'border-red-200 bg-red-50' : '' }}
                {{ $tramite->getColorEstado() === 'yellow' ? 'border-yellow-200 bg-yellow-50' : '' }}">
                
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 flex items-center justify-center rounded-full 
                        {{ $tramite->getColorEstado() === 'green' ? 'bg-green-100' : '' }}
                        {{ $tramite->getColorEstado() === 'blue' ? 'bg-blue-100' : '' }}
                        {{ $tramite->getColorEstado() === 'red' ? 'bg-red-100' : '' }}
                        {{ $tramite->getColorEstado() === 'yellow' ? 'bg-yellow-100' : '' }}">
                        <i class="fas 
                            {{ $tramite->estado === 'Aprobado' ? 'fa-check-circle text-green-600' : '' }}
                            {{ $tramite->estado === 'En Revision' ? 'fa-clock text-blue-600' : '' }}
                            {{ $tramite->estado === 'Rechazado' ? 'fa-times-circle text-red-600' : '' }}
                            {{ $tramite->estado === 'Pendiente' ? 'fa-hourglass-half text-yellow-600' : '' }}
                            text-2xl"></i>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-bold 
                            {{ $tramite->getColorEstado() === 'green' ? 'text-green-800' : '' }}
                            {{ $tramite->getColorEstado() === 'blue' ? 'text-blue-800' : '' }}
                            {{ $tramite->getColorEstado() === 'red' ? 'text-red-800' : '' }}
                            {{ $tramite->getColorEstado() === 'yellow' ? 'text-yellow-800' : '' }}">
                            {{ $tramite->estado }}
                        </h3>
                        <p class="text-gray-600">{{ $tramite->tipo_tramite }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Progreso: {{ $tramite->progreso_tramite }}/6 secciones ({{ number_format($tramite->getPorcentajeProgreso(), 0) }}%)
                        </p>
                    </div>
                </div>
                
                @if($tramite->estado === 'Rechazado' && $tramite->puedeSerEditado())
                    <button onclick="habilitarEdicion({{ $tramite->id }})" 
                            class="px-6 py-3 bg-[#9d2449] text-white rounded-lg hover:bg-[#8a203f] transition duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Trámite
                    </button>
                @endif
            </div>
        </div>

        <!-- Progreso del Trámite -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Progreso del Trámite</h2>
            
            <!-- Barra de Progreso -->
            <div class="mb-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Progreso</span>
                    <span>{{ number_format($tramite->getPorcentajeProgreso(), 0) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-[#9d2449] to-[#8a203f] h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $tramite->getPorcentajeProgreso() }}%"></div>
                </div>
            </div>
            
            <!-- Secciones -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
                    $secciones = $tipoPersona === 'Moral' ? [
                        1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user'],
                        2 => ['nombre' => 'Domicilio', 'icono' => 'fa-home'],
                        3 => ['nombre' => 'Constitución', 'icono' => 'fa-building'],
                        4 => ['nombre' => 'Accionistas', 'icono' => 'fa-users'],
                        5 => ['nombre' => 'Apoderado Legal', 'icono' => 'fa-user-tie'],
                        6 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload']
                    ] : [
                        1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user'],
                        2 => ['nombre' => 'Domicilio', 'icono' => 'fa-home'],
                        3 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload']
                    ];
                    
                    $progresoMaximo = $tipoPersona === 'Moral' ? 6 : 3;
                    $progresoMostrado = min($tramite->progreso_tramite, $progresoMaximo);
                @endphp
                
                @foreach($secciones as $numero => $seccion)
                    @php
                        $seccionRechazada = $tramite->seccionEstaRechazada($numero);
                        $seccionAprobada = $tramite->seccionEstaAprobada($numero);
                        $estadoRevision = $tramite->getEstadoSeccion($numero);
                        
                        if ($seccionRechazada) {
                            $bgColor = 'border-red-200 bg-red-50';
                            $iconBg = 'bg-red-100 text-red-600';
                            $textColor = 'text-red-800';
                            $statusText = 'Rechazado - Requiere Corrección';
                            $statusIcon = 'fas fa-times-circle text-red-500';
                        } elseif ($seccionAprobada) {
                            $bgColor = 'border-green-200 bg-green-50';
                            $iconBg = 'bg-green-100 text-green-600';
                            $textColor = 'text-green-800';
                            $statusText = 'Aprobado';
                            $statusIcon = 'fas fa-check-circle text-green-500';
                        } elseif ($progresoMostrado >= $numero) {
                            $bgColor = 'border-blue-200 bg-blue-50';
                            $iconBg = 'bg-blue-100 text-blue-600';
                            $textColor = 'text-blue-800';
                            $statusText = 'En Revisión';
                            $statusIcon = 'fas fa-clock text-blue-500';
                        } else {
                            $bgColor = 'border-gray-200 bg-gray-50';
                            $iconBg = 'bg-gray-100 text-gray-400';
                            $textColor = 'text-gray-600';
                            $statusText = 'Pendiente';
                            $statusIcon = 'fas fa-hourglass-half text-gray-400';
                        }
                    @endphp
                    
                    <div class="flex items-center p-4 rounded-lg border-2 {{ $bgColor }} relative">
                        <!-- Indicador de estado en la esquina superior derecha -->
                        <div class="absolute top-2 right-2">
                            <i class="{{ $statusIcon }} text-lg"></i>
                        </div>
                        
                        <div class="h-10 w-10 flex items-center justify-center rounded-full mr-3 {{ $iconBg }}">
                            <i class="fas {{ $seccion['icono'] }}"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium {{ $textColor }}">
                                {{ $seccion['nombre'] }}
                            </h4>
                            <p class="text-xs {{ $textColor }} mb-1">
                                {{ $statusText }}
                            </p>
                            
                            @if($seccionRechazada && $estadoRevision && $estadoRevision->comentario)
                                <p class="text-xs text-red-600 italic bg-red-100 p-1 rounded mt-1">
                                    {{ $estadoRevision->comentario }}
                                </p>
                                <button onclick="corregirSeccion({{ $tramite->id }}, {{ $numero }})" 
                                        class="mt-2 text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 transition">
                                    <i class="fas fa-edit mr-1"></i>Corregir
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Información del Trámite</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-900 mb-3">Fechas</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fecha de inicio:</span>
                            <span class="font-medium">{{ $tramite->fecha_inicio ? $tramite->fecha_inicio->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        @if($tramite->fecha_finalizacion)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fecha de finalización:</span>
                                <span class="font-medium">{{ $tramite->fecha_finalizacion->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        @if($tramite->fecha_revision)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fecha de revisión:</span>
                                <span class="font-medium">{{ $tramite->fecha_revision->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-900 mb-3">Detalles</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tipo de trámite:</span>
                            <span class="font-medium">{{ ucfirst($tramite->tipo_tramite) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sección actual:</span>
                            <span class="font-medium">{{ $tramite->getNombreSeccionActual() }}</span>
                        </div>
                        @if($tramite->revisor)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Revisado por:</span>
                                <span class="font-medium">{{ $tramite->revisor->nombre }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($tramite->observaciones)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-medium text-gray-900 mb-2">Observaciones</h3>
                    <p class="text-gray-700 text-sm">{{ $tramite->observaciones }}</p>
                </div>
            @endif
        </div>

        <!-- Información sobre qué sigue -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-medium text-blue-900 mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                ¿Qué sigue?
            </h3>
            <div class="text-blue-800 text-sm space-y-1">
                @if($tramite->estado === 'En Revision')
                    <p>• Su trámite está siendo revisado por nuestro equipo</p>
                    <p>• Recibirá una notificación cuando la revisión esté completa</p>
                    <p>• El tiempo de revisión es de 3-5 días hábiles</p>
                @elseif($tramite->estado === 'Rechazado')
                    <p>• Su trámite ha sido rechazado y requiere correcciones</p>
                    <p>• Revise las observaciones y haga clic en "Editar Trámite" para corregir</p>
                    <p>• Una vez corregido, será enviado nuevamente para revisión</p>
                @elseif($tramite->estado === 'Aprobado')
                    <p>• ¡Felicidades! Su trámite ha sido aprobado</p>
                    <p>• Puede descargar su certificado desde el panel principal</p>
                    <p>• Conserve este documento para sus registros</p>
                @else
                    <p>• Complete todas las secciones del formulario</p>
                    <p>• Suba todos los documentos requeridos</p>
                    <p>• Envíe el trámite para revisión</p>
                @endif
            </div>
        </div>
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