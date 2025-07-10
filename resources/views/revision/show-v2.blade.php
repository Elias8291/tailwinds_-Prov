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
    foreach ($documentosSolicitante as $docSolicitante) {
        if ($docSolicitante->documento && $docSolicitante->documento->secciones) {
            foreach ($docSolicitante->documento->secciones as $seccion) {
                $clave = match($seccion->id) {
                    1 => 'datos_generales',
                    2 => 'domicilio',
                    3 => 'constitucion',
                    4 => 'accionistas',
                    5 => 'apoderado',
                    default => 'documentos'
                };
                
                if (!isset($documentosPorSeccion[$clave])) {
                    $documentosPorSeccion[$clave] = [];
                }
                
                $documentosPorSeccion[$clave][] = [
                    'id' => $docSolicitante->id,
                    'nombre' => $docSolicitante->documento->nombre,
                    'descripcion' => $docSolicitante->documento->descripcion ?? 'Documento requerido para el trámite.',
                    'estado' => $docSolicitante->estado,
                    'ruta_archivo' => $docSolicitante->ruta_archivo,
                    'observaciones' => $docSolicitante->observaciones,
                    'fecha_subida' => $docSolicitante->fecha_entrega,
                    'nombre_original' => 'document.pdf',
                    'comentario_revision' => $docSolicitante->observaciones,
                    'documento_cotejado' => $docSolicitante->documento_cotejado ?? false,
                ];
            }
        }
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
@endphp

<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 font-sans min-h-screen">
    <!-- Header -->
    <header class="bg-white rounded-3xl shadow-xl p-6 lg:p-8 mb-8 border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Revisión de Trámite #{{ $tramite->id }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Solicitante: {{ $tramite->solicitante->nombre_completo }}
                    <span class="mx-2">|</span>
                    RFC: {{ $tramite->solicitante->rfc }}
                </p>
            </div>
        </div>
    </header>

    <!-- Contenido Principal: Lista de Secciones -->
    <main class="space-y-12">
        @foreach($secciones as $seccion)
            <div id="seccion-{{$seccion['id']}}" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
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
                            <div class="p-6 lg:p-8 space-y-6">
                                <div class="flex items-center justify-between mb-5">
                                    <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-file-signature mr-4 text-3xl text-[#9d2449]"></i>
                                        <span>Expediente Documental</span>
                                    </h2>
                                </div>

                                @forelse($documentosSeccion as $documento)
                                    @php
                                        $estado = $documento['estado'] ?? 'Pendiente';
                                        $hasFile = !empty($documento['ruta_archivo']);
                                    @endphp
                                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 overflow-hidden border-l-4 
                                        @if($estado === 'Aprobado') border-green-500 
                                        @elseif($estado === 'Rechazado') border-red-500 
                                        @else border-gray-400 
                                        @endif">
                                        
                                        <div class="p-5 flex items-center gap-5">
                                            <!-- Icon -->
                                            <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center rounded-full
                                                @if($estado === 'Aprobado') bg-green-100 text-green-600
                                                @elseif($estado === 'Rechazado') bg-red-100 text-red-600
                                                @else bg-gray-100 text-gray-500
                                                @endif">
                                                <i class="fas fa-file-alt text-2xl"></i>
                                            </div>
                                            
                                            <!-- Document Info -->
                                            <div class="flex-grow min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="font-bold text-lg text-gray-800 truncate" title="{{ $documento['nombre'] }}">
                                                        {{ $documento['nombre'] }}
                                                    </p>
                                                    <span class="ml-4 px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider
                                                        @if($estado === 'Aprobado') bg-green-100 text-green-800
                                                        @elseif($estado === 'Rechazado') bg-red-100 text-red-800
                                                        @else bg-gray-200 text-gray-700
                                                        @endif">
                                                        {{ $estado }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    @if($documento['fecha_subida'])
                                                        Subido: {{ \Carbon\Carbon::parse($documento['fecha_subida'])->format('d/m/Y, h:i A') }}
                                                    @else
                                                        Sin entregar
                                                    @endif
                                                </p>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex-shrink-0 flex items-center gap-2">
                                                @if($hasFile)
                                                    <a href="{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => $documento['id']]) }}" 
                                                       target="_blank" 
                                                       class="h-10 w-10 inline-flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-200 hover:text-gray-800 transition-all" 
                                                       title="Ver Documento">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-16">
                                        <div class="mx-auto h-20 w-20 text-gray-400 mb-4 flex items-center justify-center bg-gray-100 rounded-full">
                                            <i class="fas fa-folder-open text-4xl"></i>
                                        </div>
                                        <h3 class="text-xl font-medium text-gray-900">Sin Documentos</h3>
                                        <p class="mt-2 text-md text-gray-500">No hay documentos asociados a esta sección.</p>
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
            </div>
        @endforeach
    </main>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush
