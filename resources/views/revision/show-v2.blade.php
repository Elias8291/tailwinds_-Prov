@extends('layouts.app')

@section('content')
@php
    use App\Http\Controllers\Formularios\DatosGeneralesController;
    use App\Http\Controllers\Formularios\DomicilioController;
    use App\Http\Controllers\Formularios\ConstitucionController;
    use App\Http\Controllers\Formularios\AccionistasController;
    use App\Http\Controllers\Formularios\ApoderadoLegalController;
    use App\Http\Controllers\Formularios\DocumentosController;
    use Illuminate\Support\Facades\Crypt;
    use App\Models\SeccionRevision;

    // Instanciar controladores
    $datosGeneralesController = new DatosGeneralesController();
    $domicilioController = new DomicilioController();
    $constitucionController = new ConstitucionController();
    $apoderadoController = new ApoderadoLegalController();
    $documentosController = new DocumentosController();

    // Obtener datos de cada sección
    $datosTramite = $datosGeneralesController->obtenerDatos($tramite);
    $datosDomicilio = $domicilioController->obtenerDatos($tramite);
    
    // Generar domicilio concatenado para el mapa
    $domicilioConcatenado = '';
    if (!empty($datosDomicilio)) {
        $partes = [];
        if (!empty($datosDomicilio['calle'])) {
            $direccion = $datosDomicilio['calle'];
            if (!empty($datosDomicilio['numero_exterior'])) {
                $direccion .= ' No. ' . $datosDomicilio['numero_exterior'];
                if (!empty($datosDomicilio['numero_interior'])) {
                    $direccion .= ' Int. ' . $datosDomicilio['numero_interior'];
                }
            }
            $partes[] = $direccion;
        }
        if (!empty($datosDomicilio['colonia'])) {
            $partes[] = $datosDomicilio['colonia'];
        }
        if (!empty($datosDomicilio['municipio'])) {
            $partes[] = $datosDomicilio['municipio'];
        }
        if (!empty($datosDomicilio['estado'])) {
            $partes[] = $datosDomicilio['estado'];
        }
        if (!empty($datosDomicilio['codigo_postal'])) {
            $partes[] = 'C.P. ' . $datosDomicilio['codigo_postal'];
        }
        $domicilioConcatenado = implode(', ', $partes);
    }
    
    if ($tramite->solicitante && $tramite->solicitante->tipo_persona === 'Moral') {
        $datosConstitucion = $constitucionController->getIncorporationData($tramite);
        $datosAccionistas = $tramite->accionistas; // Usamos la relación directa del modelo
        $datosApoderado = $apoderadoController->getDatosApoderadoLegal($tramite);
    }

    // Obtener documentos por sección
    $documentosSolicitante = $tramite->documentosSolicitante()
        ->with(['documento.secciones'])
        ->get();

    $documentosPorSeccion = [];
    
    // Inicializar arrays para cada sección
    $documentosPorSeccion['datos_generales'] = [];
    $documentosPorSeccion['domicilio'] = [];
    $documentosPorSeccion['constitucion'] = [];
    $documentosPorSeccion['accionistas'] = [];
    $documentosPorSeccion['apoderado'] = [];
    $documentosPorSeccion['documentos'] = [];
    
    foreach ($documentosSolicitante as $docSolicitante) {
        $documentoData = [
            'id' => $docSolicitante->id,
            'nombre' => $docSolicitante->documento->nombre ?? 'Documento sin nombre',
            'descripcion' => $docSolicitante->documento->descripcion ?? 'Documento requerido para el trámite.',
            'estado' => $docSolicitante->estado,
            'ruta_archivo' => $docSolicitante->ruta_archivo,
            'observaciones' => $docSolicitante->observaciones,
            'fecha_subida' => $docSolicitante->fecha_entrega,
            'nombre_original' => 'document.pdf',
            'comentario_revision' => $docSolicitante->observaciones,
            'documento_cotejado' => $docSolicitante->documento_cotejado ?? false,
        ];
        
        // Verificar si el documento tiene secciones específicas
        $tieneSeccionEspecifica = false;
        
        if ($docSolicitante->documento && $docSolicitante->documento->secciones) {
            foreach ($docSolicitante->documento->secciones as $seccion) {
                $clave = match($seccion->id) {
                    1 => 'datos_generales',
                    2 => 'domicilio',
                    3 => 'constitucion',
                    4 => 'accionistas',
                    5 => 'apoderado',
                    default => null
                };
                
                if ($clave) {
                    $documentosPorSeccion[$clave][] = $documentoData;
                    $tieneSeccionEspecifica = true;
                }
            }
        }
        
        // SIEMPRE agregar todos los documentos a la sección 'documentos' para tener una vista completa
        // Esto permite ver todos los documentos del trámite en un solo lugar
        $documentosPorSeccion['documentos'][] = $documentoData;
    }

    $secciones = [
        ['id' => 1, 'nombre' => 'Datos Generales', 'clave' => 'datos_generales', 'icon' => 'fa-id-card-alt'],
        ['id' => 2, 'nombre' => 'Domicilio', 'clave' => 'domicilio', 'icon' => 'fa-map-marked-alt'],
    ];
    if ($tramite->solicitante && $tramite->solicitante->tipo_persona === 'Moral') {
        $secciones[] = ['id' => 3, 'nombre' => 'Constitución', 'clave' => 'constitucion', 'icon' => 'fa-gavel'];
        $secciones[] = ['id' => 4, 'nombre' => 'Accionistas', 'clave' => 'accionistas', 'icon' => 'fa-users'];
        $secciones[] = ['id' => 5, 'nombre' => 'Apoderado Legal', 'clave' => 'apoderado', 'icon' => 'fa-user-tie'];
    }
    $secciones[] = ['id' => 6, 'nombre' => 'Documentos', 'clave' => 'documentos', 'icon' => 'fa-file-alt'];

    // Obtener estado de revisión de cada sección
    $seccionesEstados = SeccionRevision::where('tramite_id', $tramite->id)
        ->pluck('estado', 'seccion_id')
        ->toArray();
    
    // Clases para el badge del tipo de trámite
    $tipoTramite = ucfirst(strtolower($tramite->tipo_tramite));
    $tipoTramiteClasses = match($tipoTramite) {
        'Inscripcion' => 'bg-amber-100 text-amber-800 border-amber-300',
        'Renovacion' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
        'Actualizacion' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        default => 'bg-gray-100 text-gray-800 border-gray-300',
    };
@endphp

<!-- Contenedor principal con fondo limpio -->
<div class="w-full min-h-screen font-sans">

    <!-- Contenido Principal con nuevo padding y encabezado -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Encabezado Elegante (inspirado en users/index.blade.php) -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-lg sm:rounded-xl p-2 sm:p-3 shadow-md flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12">
                        <i class="fas fa-file-signature text-white/90 text-lg sm:text-2xl"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-lg sm:text-2xl font-bold bg-gradient-to-r from-primary to-primary-dark bg-clip-text text-transparent flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                           <span class="truncate">Revisión de Trámite</span>
                           <span class="px-2 sm:px-2.5 py-1 text-xs sm:text-sm font-semibold rounded-full border {{ $tipoTramiteClasses }} self-start sm:self-auto">
                               {{ $tipoTramite }}
                           </span>
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Folio del Trámite: #{{ $tramite->id }}
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0 text-left sm:text-right">
                    <p class="font-bold text-gray-800 text-sm sm:text-base truncate">{{ $tramite->solicitante->nombre_completo }}</p>
                    <span class="text-xs sm:text-sm text-gray-500">
                        RFC: {{ $tramite->solicitante->rfc }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Navegación de secciones en móvil -->
        <nav class="lg:hidden mb-4 sm:mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-2 sm:p-3">
                <div class="flex overflow-x-auto space-x-2 sm:space-x-3 scrollbar-hide">
                    @foreach($secciones as $index => $seccion)
                        <a href="#seccion-{{$seccion['id']}}" 
                           class="flex-shrink-0 flex items-center space-x-2 px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 hover:text-primary hover:bg-primary-50 rounded-md transition-colors whitespace-nowrap">
                            <i class="fas {{$seccion['icon']}} text-xs sm:text-sm"></i>
                            <span class="hidden xs:inline sm:inline">{{$seccion['nombre']}}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>
        
        <!-- Lista de Secciones con Títulos Rediseñados -->
        <div class="space-y-4 sm:space-y-6 lg:space-y-8">
            @foreach($secciones as $seccion)
                @php
                    $estadoSeccion = strtolower($seccionesEstados[$seccion['id']] ?? 'pendiente');
                    $estadoBadgeClasses = match($estadoSeccion) {
                        'aprobado' => 'bg-green-100 text-green-800',
                        'rechazado' => 'bg-red-100 text-red-800',
                        default => 'bg-yellow-100 text-yellow-800',
                    };
                @endphp
                <section id="seccion-{{$seccion['id']}}" class="bg-white rounded-lg sm:rounded-xl lg:rounded-2xl shadow-sm sm:shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-200 overflow-hidden">
                    <!-- Encabezado de Sección Elegante y Unificado -->
                    <div class="px-3 py-3 sm:px-4 sm:py-4 lg:px-6 lg:py-5 border-b border-gray-200 bg-gray-50/50">
                        <div class="flex items-center justify-between gap-2 sm:gap-4">
                            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 flex items-center justify-center rounded-full bg-primary-100 text-primary">
                                    <i class="fas {{$seccion['icon']}} text-sm sm:text-lg"></i>
                                </div>
                                <h3 class="text-base sm:text-xl font-bold text-gray-800 tracking-tight flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 min-w-0">
                                    <span class="truncate">{{ $seccion['nombre'] }}</span>
                                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-semibold {{ $estadoBadgeClasses }} self-start sm:self-auto">
                                        {{ ucfirst($estadoSeccion) }}
                                    </span>
                                </h3>
                            </div>
                            
                            <!-- Botón para Mostrar/Ocultar Documentos (solo si aplica) -->
                            @if($seccion['clave'] !== 'documentos' && !empty($documentosPorSeccion[$seccion['clave']]))
                                <div x-data="documentVisibility('{{ $seccion['clave'] }}')">
                                    <button @click="toggle"
                                            class="text-xs sm:text-sm font-medium text-primary hover:text-primary-dark px-2 sm:px-3 py-1 sm:py-1.5 bg-primary-50 hover:bg-primary-100 rounded-md sm:rounded-lg flex items-center transition-colors">
                                        <i class="fas fa-eye text-xs sm:text-sm mr-1 sm:mr-2 transition-transform duration-300" :class="{ 'fa-eye-slash': visible }"></i>
                                        <span class="hidden xs:inline sm:inline" x-text="visible ? 'Ocultar Material de Apoyo' : 'Mostrar Material de Apoyo'"></span>
                                        <span class="xs:hidden sm:hidden" x-text="visible ? 'Ocultar' : 'Mostrar'"></span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="content-wrapper">
                        @php
                            $formView = null;
                            $documentosSeccion = $documentosPorSeccion[$seccion['clave']] ?? [];
                        @endphp

                        @switch($seccion['clave'])
                            @case('datos_generales')
                                @php
                                    $formView = view('components.formularios.seccion-datos-generales', [
                                        'datosTramite' => $datosTramite,
                                        'datosSolicitante' => $tramite->solicitante->toArray(),
                                        'readonly' => true
                                    ])->render();
                                @endphp
                                @break
                            @case('domicilio')
                                @php
                                    $formView = view('components.formularios.seccion-domicilio', [
                                        'datosDomicilio' => $datosDomicilio,
                                        'readonly' => true
                                    ])->render();
                                @endphp
                                @break
                            @case('constitucion')
                                @if($tramite->solicitante->tipo_persona === 'Moral')
                                    @php
                                        $formView = view('components.formularios.seccion-constitucion', [
                                            'datosConstitucion' => $datosConstitucion,
                                            'readonly' => true
                                        ])->render();
                                    @endphp
                                @endif
                                @break
                            @case('accionistas')
                                @if($tramite->solicitante->tipo_persona === 'Moral')
                                    @php
                                        $formView = view('components.formularios.seccion-accionistas', [
                                            'accionistas' => $datosAccionistas,
                                            'readonly' => true
                                        ])->render();
                                    @endphp
                                @endif
                                @break
                            @case('apoderado')
                                 @if($tramite->solicitante->tipo_persona === 'Moral')
                                    @php
                                        $formView = view('components.formularios.seccion-apoderado', [
                                            'datosApoderado' => $datosApoderado,
                                            'readonly' => true
                                        ])->render();
                                    @endphp
                                @endif
                                @break
                            @case('documentos')
                                <!-- El contenido de la sección de documentos ahora va aquí -->
                                <div class="p-3 sm:p-4 lg:p-6 space-y-3 sm:space-y-4">
                                    <!-- Ya no se necesita el título aquí, se muestra arriba -->
                                    @forelse($documentosSeccion as $index => $documento)
                                        @php
                                            $estado = $documento['estado'] ?? 'Pendiente';
                                            $hasFile = !empty($documento['ruta_archivo']);

                                            // Paleta de colores clara y elegante
                                            $cardClasses = match($estado) {
                                                'Aprobado' => 'bg-green-50/50 border-l-4 border-green-500',
                                                'Rechazado' => 'bg-red-50/60 border-l-4 border-red-500',
                                                default => 'bg-white border-l-4 border-slate-300',
                                            };
                                            $iconContainerClasses = match($estado) {
                                                'Aprobado' => 'bg-green-600 text-white',
                                                'Rechazado' => 'bg-red-600 text-white',
                                                default => 'bg-slate-500 text-white',
                                            };
                                            $badgeClasses = match($estado) {
                                                'Aprobado' => 'bg-green-100 text-green-800',
                                                'Rechazado' => 'bg-red-100 text-red-700',
                                                default => 'bg-slate-200 text-slate-600',
                                            };
                                        @endphp
                                        <div x-data="{ expanded: false }" 
                                             @close-panel.window="expanded = false"
                                             data-documento-id="{{ $documento['id'] }}" 
                                             class="rounded-lg sm:rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden {{ $cardClasses }}">
                                            <!-- Cabecera del Documento -->
                                            <div class="p-3 sm:p-4 flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-start gap-3 sm:gap-4">
                                                <div class="relative flex-shrink-0 h-10 w-10 sm:h-12 sm:w-12 flex items-center justify-center rounded-lg {{ $iconContainerClasses }}">
                                                    <i class="fas fa-file-alt text-lg sm:text-xl"></i>
                                                    @if ($estado === 'Aprobado')
                                                        <div class="absolute -top-1 -right-1 h-4 w-4 sm:h-5 sm:w-5 bg-white rounded-full flex items-center justify-center shadow">
                                                            <i class="fas fa-check-circle text-green-500 text-sm sm:text-lg"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="flex-grow min-w-0">
                                                    <div class="flex flex-col space-y-2 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between">
                                                        <p class="font-bold text-gray-800 text-sm sm:text-base leading-tight" title="{{ $documento['nombre'] }}">
                                                            {{ $documento['nombre'] }}
                                                        </p>
                                                        <span class="badge-estado px-2 sm:px-3 py-1 text-xs font-semibold rounded-full uppercase tracking-wider self-start sm:self-center {{ $badgeClasses }}">
                                                            {{ $estado }}
                                                        </span>
                                                    </div>
                                                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                                                        @if($documento['fecha_subida'])
                                                            Subido: {{ \Carbon\Carbon::parse($documento['fecha_subida'])->format('d/m/Y, h:i A') }}
                                                        @else
                                                            Sin entregar
                                                        @endif
                                                    </p>
                                                </div>

                                                <div class="flex-shrink-0 flex items-center gap-2 sm:gap-2">
                                                    @if($hasFile)
                                                        <a href="{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => $documento['id']]) }}?inline=1" 
                                                           target="_blank" 
                                                           class="h-8 w-8 sm:h-9 sm:w-9 inline-flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-200/70 hover:text-slate-800 transition-all" 
                                                           title="Ver Documento">
                                                            <i class="fas fa-eye text-sm"></i>
                                                        </a>
                                                    @endif
                                                    <button @click="expanded = !expanded"
                                                            class="h-8 sm:h-9 px-3 sm:px-4 inline-flex items-center justify-center rounded-full text-xs sm:text-sm font-semibold transition-all text-primary hover:bg-primary-50"
                                                            title="Revisar documento">
                                                        <i class="fas fa-pen-to-square mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                                        <span class="hidden xs:inline">Revisar</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Panel de revisión con funcionalidad -->
                                            <div x-show="expanded" x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                 x-transition:leave="transition ease-in duration-150"
                                                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                 class="border-t border-primary-100 bg-primary-50/50 p-3 sm:p-4" style="display: none;"
                                                 x-data="documentoRevision({{ $documento['id'] }}, {{ $tramite->id }})">

                                                <div class="space-y-3 sm:space-y-4">
                                                    <div>
                                                        <label for="comentario-{{$documento['id']}}" class="block text-sm font-semibold text-primary-dark mb-2">
                                                            <i class="fas fa-edit mr-1 text-primary"></i> Observaciones de Revisión
                                                        </label>
                                                        <textarea x-model="comentario" id="comentario-{{$documento['id']}}" name="comentario" rows="3"
                                                                  class="w-full p-2 sm:p-3 text-sm bg-white border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary transition duration-150 ease-in-out"
                                                                  placeholder="Añadir un comentario...">{{ $documento['observaciones'] ?? '' }}</textarea>
                                                    </div>

                                                    <div class="flex flex-col sm:flex-row justify-end items-stretch sm:items-center gap-2 sm:gap-3 pt-2">
                                                        <button @click="rechazarDocumento()" :disabled="loading"
                                                                class="w-full sm:w-auto inline-flex justify-center items-center px-4 sm:px-5 py-2 border border-primary text-sm font-bold rounded-full text-primary bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-150 disabled:opacity-50">
                                                            <i class="fas fa-times-circle mr-2"></i>
                                                            <span x-text="loading ? 'Procesando...' : 'Rechazar'"></span>
                                                        </button>
                                                        <button @click="aprobarDocumento()" :disabled="loading"
                                                                class="w-full sm:w-auto inline-flex justify-center items-center px-4 sm:px-5 py-2 border border-transparent text-sm font-bold rounded-full text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-dark transition-all duration-150 disabled:opacity-50">
                                                            <i class="fas fa-check-circle mr-2"></i>
                                                            <span x-text="loading ? 'Procesando...' : 'Aprobar'"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8 sm:py-12 lg:py-16">
                                            <div class="mx-auto h-12 w-12 sm:h-16 sm:w-16 lg:h-20 lg:w-20 text-gray-400 mb-4 flex items-center justify-center bg-gray-100 rounded-full">
                                                <i class="fas fa-folder-open text-xl sm:text-2xl lg:text-3xl"></i>
                                            </div>
                                            <h3 class="text-base sm:text-lg lg:text-xl font-medium text-gray-900">Sin Documentos</h3>
                                            <p class="mt-2 text-sm sm:text-base text-gray-500">No hay documentos asociados a esta sección.</p>
                                        </div>
                                    @endforelse
                                </div>
                                @break
                        @endswitch

                        @if($formView)
                            @include('components.revision.cotejo-tabs', [
                                'formulario' => $formView,
                                'documentos' => $documentosSeccion,
                                'tramite' => $tramite,
                                'seccion' => $seccion['clave'],
                                'domicilioConcatenado' => $domicilioConcatenado ?? ''
                            ])
                        @endif
                    </div>
                    
                    {{-- Panel de Revisión para cada sección, excepto la de Documentos --}}
                    @if($seccion['clave'] !== 'documentos')
                        <div class="border-t border-gray-200">
                             @include('components.revision.revision-panel', ['seccion' => $seccion, 'tramite' => $tramite])
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    </main>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    /* Mejoras adicionales para móvil */
    @media (max-width: 475px) {
        .mobile-title {
            font-size: 1rem;
            line-height: 1.25rem;
        }
        
        .mobile-badge {
            font-size: 0.625rem;
            padding: 0.25rem 0.5rem;
        }
        
        .mobile-section-title {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        
        .mobile-icon {
            width: 1.5rem;
            height: 1.5rem;
        }
        
        .mobile-button {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }
    }
    
    /* Mejoras para pantallas muy pequeñas */
    @media (max-width: 360px) {
        .mobile-card {
            padding: 0.75rem;
        }
        
        .mobile-nav {
            padding: 0.5rem;
        }
        
        .mobile-content {
            padding: 0.75rem;
        }
    }
    
    /* Transiciones suaves para cambios de tamaño */
    .responsive-transition {
        transition: all 0.3s ease-in-out;
    }
</style>
@endpush

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('documentVisibility', (seccionClave) => ({
            visible: false,
            seccionClave: seccionClave,
            toggle() {
                this.visible = !this.visible
                
                // Emitir un evento global para que cotejo-tabs reaccione
                window.dispatchEvent(new CustomEvent('document-visibility-changed', {
                    detail: {
                        visible: this.visible,
                        seccion: this.seccionClave
                    }
                }));
            }
        }))

        Alpine.data('documentoRevision', (documentoId, tramiteId) => ({
            comentario: '',
            loading: false,
            
            async aprobarDocumento() {
                this.loading = true;
                try {
                    const response = await fetch(`/revision/${tramiteId}/documento/${documentoId}/aprobar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            observaciones: this.comentario
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Actualizar la UI
                        this.actualizarEstadoDocumento(documentoId, 'Aprobado');
                        
                        // Actualizar el estado de la sección de documentos
                        this.actualizarEstadoSeccionDocumentos();
                        
                        // Mostrar notificación
                        this.mostrarNotificacion('Documento aprobado exitosamente', 'success');
                        
                        // Cerrar el panel
                        this.$dispatch('close-panel');
                    } else {
                        this.mostrarNotificacion(data.message || 'Error al aprobar el documento', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.mostrarNotificacion('Error de conexión', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async rechazarDocumento() {
                if (!this.comentario.trim()) {
                    this.mostrarNotificacion('Debe proporcionar un comentario para rechazar el documento', 'error');
                    return;
                }

                this.loading = true;
                try {
                    const response = await fetch(`/revision/${tramiteId}/documento/${documentoId}/rechazar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            observaciones: this.comentario
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Actualizar la UI
                        this.actualizarEstadoDocumento(documentoId, 'Rechazado');
                        
                        // Actualizar el estado de la sección de documentos
                        this.actualizarEstadoSeccionDocumentos();
                        
                        // Mostrar notificación
                        this.mostrarNotificacion('Documento rechazado exitosamente', 'success');
                        
                        // Cerrar el panel
                        this.$dispatch('close-panel');
                    } else {
                        this.mostrarNotificacion(data.message || 'Error al rechazar el documento', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.mostrarNotificacion('Error de conexión', 'error');
                } finally {
                    this.loading = false;
                }
            },

            actualizarEstadoDocumento(documentoId, nuevoEstado) {
                // Buscar el elemento del documento y actualizar su estado visual
                const documentoElement = document.querySelector(`[data-documento-id="${documentoId}"]`);
                if (documentoElement) {
                    const badge = documentoElement.querySelector('.badge-estado');
                    const iconContainer = documentoElement.querySelector('.h-12.w-12');
                    const icon = iconContainer ? iconContainer.querySelector('i') : null;
                    
                    // Actualizar badge
                    if (badge) {
                        badge.textContent = nuevoEstado;
                        // Remover todas las clases de color existentes
                        badge.className = badge.className.replace(/bg-\w+-\d+/g, '').replace(/text-\w+-\d+/g, '');
                        
                        // Agregar nuevas clases según el estado
                        const badgeClasses = nuevoEstado === 'Aprobado' ? 'bg-green-100 text-green-800' :
                                           nuevoEstado === 'Rechazado' ? 'bg-red-100 text-red-700' :
                                           'bg-slate-200 text-slate-600';
                        
                        badge.className += ` ${badgeClasses}`;
                    }
                    
                    // Actualizar icono del contenedor
                    if (iconContainer) {
                        // Remover clases de color existentes
                        iconContainer.className = iconContainer.className.replace(/bg-\w+-\d+/g, '').replace(/text-\w+/g, '');
                        
                        // Agregar nuevas clases según el estado
                        const iconClasses = nuevoEstado === 'Aprobado' ? 'bg-green-600 text-white' :
                                          nuevoEstado === 'Rechazado' ? 'bg-red-600 text-white' :
                                          'bg-slate-500 text-white';
                        
                        iconContainer.className += ` ${iconClasses}`;
                    }
                    
                    // Actualizar el ícono de check para documentos aprobados
                    if (nuevoEstado === 'Aprobado' && iconContainer) {
                        let checkIcon = iconContainer.querySelector('.fa-check-circle');
                        if (!checkIcon) {
                            checkIcon = document.createElement('div');
                            checkIcon.className = 'absolute -top-1 -right-1 h-5 w-5 bg-white rounded-full flex items-center justify-center shadow';
                            checkIcon.innerHTML = '<i class="fas fa-check-circle text-green-500 text-lg"></i>';
                            iconContainer.appendChild(checkIcon);
                        }
                    } else if (iconContainer) {
                        // Remover el ícono de check si no está aprobado
                        const checkIcon = iconContainer.querySelector('.absolute');
                        if (checkIcon) {
                            checkIcon.remove();
                        }
                    }
                    
                    // Actualizar clase del contenedor principal
                    const cardClasses = nuevoEstado === 'Aprobado' ? 'bg-green-50/50 border-l-4 border-green-500' :
                                       nuevoEstado === 'Rechazado' ? 'bg-red-50/60 border-l-4 border-red-500' :
                                       'bg-white border-l-4 border-slate-300';
                    
                    // Remover clases de fondo y borde existentes
                    documentoElement.className = documentoElement.className
                        .replace(/bg-green-50\/50|bg-red-50\/60|bg-white/g, '')
                        .replace(/border-l-4/g, '')
                        .replace(/border-green-500|border-red-500|border-slate-300/g, '');
                    
                    // Agregar nuevas clases
                    documentoElement.className += ` ${cardClasses}`;
                }
            },

            actualizarEstadoSeccionDocumentos() {
                // Obtener todos los documentos de la sección de documentos
                const seccionDocumentos = document.querySelector('#seccion-6');
                if (!seccionDocumentos) return;
                
                const badges = seccionDocumentos.querySelectorAll('.badge-estado');
                let aprobados = 0;
                let rechazados = 0;
                let pendientes = 0;
                
                badges.forEach(badge => {
                    const estado = badge.textContent.trim();
                    if (estado === 'Aprobado') aprobados++;
                    else if (estado === 'Rechazado') rechazados++;
                    else pendientes++;
                });
                
                // Determinar el estado general de la sección
                let estadoSeccion = 'Pendiente';
                if (rechazados > 0) {
                    estadoSeccion = 'Rechazado';
                } else if (pendientes === 0 && aprobados > 0) {
                    estadoSeccion = 'Aprobado';
                }
                
                // Actualizar el badge de la sección
                const badgeSeccion = seccionDocumentos.querySelector('h3 span');
                if (badgeSeccion) {
                    badgeSeccion.textContent = estadoSeccion;
                    
                    // Remover clases existentes
                    badgeSeccion.className = badgeSeccion.className.replace(/bg-\w+-\d+/g, '').replace(/text-\w+-\d+/g, '');
                    
                    // Agregar nuevas clases
                    const badgeClasses = estadoSeccion === 'Aprobado' ? 'bg-green-100 text-green-800' :
                                        estadoSeccion === 'Rechazado' ? 'bg-red-100 text-red-800' :
                                        'bg-yellow-100 text-yellow-800';
                    
                    badgeSeccion.className += ` ${badgeClasses}`;
                }
            },

            mostrarNotificacion(mensaje, tipo) {
                // Crear notificación temporal
                const notificacion = document.createElement('div');
                notificacion.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white ${
                    tipo === 'success' ? 'bg-green-500' : 'bg-red-500'
                }`;
                notificacion.textContent = mensaje;
                
                document.body.appendChild(notificacion);
                
                // Remover después de 3 segundos
                setTimeout(() => {
                    notificacion.remove();
                }, 3000);
            }
        }))
    })
</script>
