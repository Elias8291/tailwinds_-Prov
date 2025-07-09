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
    
    if ($tramite->solicitante && $tramite->solicitante->tipo_persona === 'Moral') {
        $datosConstitucion = $constitucionController->getIncorporationData($tramite);
        $datosAccionistas = $tramite->accionistas; // Usamos la relación directa del modelo
        $datosApoderado = $apoderadoController->getDatosApoderadoLegal($tramite);
    }

    // Obtener y mapear los documentos para el componente de revisión
    $documentosSolicitante = $tramite->documentosSolicitante()
        ->with('documento')
        ->get()
        ->map(function($doc) {
            return [
                'id' => $doc->id,
                'nombre' => $doc->documento->nombre,
                'descripcion' => $doc->documento->descripcion ?? 'Documento requerido para el trámite.',
                'estado' => $doc->estado,
                'ruta_archivo' => $doc->ruta_archivo, // Se mantiene la ruta para que el componente determine si existe
                'observaciones' => $doc->observaciones,
                'fecha_subida' => $doc->fecha_entrega,
                'nombre_original' => 'document.pdf', // Placeholder
                'comentario_revision' => $doc->observaciones,
                'documento_cotejado' => $doc->cotejado ?? false,
            ];
        })
        ->toArray();

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

<div x-data="revisionController" x-init="init()" x-cloak class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 font-sans min-h-screen">
    
    <!-- Header Integrado -->
    <header class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <x-revision.header 
                :tramiteId="$tramite->id"
                :tipoTramite="$tramite->tipo_tramite"
                :rfc="$tramite->solicitante->rfc ?? ''"
            />
            <button @click="showAgendarCitaModal = true"
                    class="w-full md:w-auto flex-shrink-0 inline-flex items-center justify-center px-4 py-2 bg-[#9d2449] text-white font-semibold rounded-lg shadow-md hover:bg-[#7a1d3a] transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-[#9d2449] focus:ring-offset-2">
                <i class="fas fa-calendar-alt mr-2"></i>
                Agendar Cita de Cotejo
            </button>
        </div>
        <!-- Barra de Progreso -->
        <div class="mt-6">
            <div class="flex justify-between items-center mb-1">
                <h3 class="text-sm font-bold text-gray-600">Progreso de la Revisión</h3>
                <span class="text-sm font-bold text-[#9d2449]" x-text="`${Math.round(progreso * 100)}%`"></span>
            </div>
            <div class="bg-gray-200 rounded-full h-2 w-full overflow-hidden">
                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-full rounded-full transition-all duration-500"
                     :style="`width: ${progreso * 100}%`"></div>
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
                        $documentoData = [];
                    @endphp
                    @switch($seccion['clave'])
                        @case('datos_generales')
                            @php
                                $formView = view('components.formularios.seccion-datos-generales', [
                                    'datosTramite' => $datosTramite,
                                    'datosSolicitante' => $tramite->solicitante->toArray(),
                                    'readonly' => true
                                ])->render();
                                $documentoData = collect($documentosPorSeccion['datos_generales'] ?? [])->first() ?? [];
                            @endphp
                            @break
                        @case('domicilio')
                            @php
                                $formView = view('components.formularios.seccion-domicilio', [
                                    'datosDomicilio' => $datosDomicilio,
                                    'readonly' => true
                                ])->render();
                                $documentoData = collect($documentosPorSeccion['domicilio'] ?? [])->first() ?? [];
                            @endphp
                            @break
                        @case('constitucion')
                            @if($tramite->solicitante->tipo_persona === 'Moral')
                                @php
                                    $formView = view('components.formularios.seccion-constitucion', [
                                        'datosConstitucion' => $datosConstitucion,
                                        'readonly' => true
                                    ])->render();
                                    $documentoData = collect($documentosPorSeccion['constitucion'] ?? [])->first() ?? [];
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
                                    $documentoData = collect($documentosPorSeccion['accionistas'] ?? [])->first() ?? [];
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
                                    $documentoData = collect($documentosPorSeccion['apoderado'] ?? [])->first() ?? [];
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
                                    
                                    @php
                                        $totalDocumentos = count($documentosSolicitante);
                                        $documentosAprobados = count(array_filter($documentosSolicitante, function($doc) {
                                            return $doc['estado'] === 'Aprobado';
                                        }));
                                        $todosAprobados = $totalDocumentos > 0 && $documentosAprobados === $totalDocumentos;
                                    @endphp

                                    <div class="flex items-center gap-4">
                                        <div class="text-sm">
                                            <span class="font-medium text-gray-600">Estado de revisión:</span>
                                            <span class="ml-2 px-3 py-1 rounded-full {{ $todosAprobados ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $documentosAprobados }}/{{ $totalDocumentos }} documentos aprobados
                                            </span>
                                        </div>
                                        <button 
                                            @click="$dispatch('aprobar-seccion')"
                                            :disabled="!{{ $todosAprobados ? 'true' : 'false' }}"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg font-semibold text-sm text-white bg-[#9d2449] hover:bg-[#8a203f] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#9d2449]"
                                        >
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Aprobar Sección
                                        </button>
                                    </div>
                                </div>

                                @if(!$todosAprobados)
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700">
                                                    Faltan {{ $totalDocumentos - $documentosAprobados }} documento(s) por aprobar. Debe aprobar todos los documentos antes de poder aprobar esta sección.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @forelse($documentosSolicitante as $documento)
                                    @php
                                        $estado = $documento['estado'] ?? 'Pendiente';
                                        $hasFile = !empty($documento['ruta_archivo']);
                                    @endphp
                                    <div 
                                        x-data="{
                                            open: false,
                                            estado: '{{ $estado }}',
                                            loading: false,
                                            comentario: '',
                                            aprobarDocumento(id) {
                                                this.loading = true;
                                                fetch(`/api/documentos/${id}/aprobar`, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                    },
                                                    body: JSON.stringify({ comentario: this.comentario })
                                                })
                                                .then(r => r.json())
                                                .then(data => {
                                                    this.estado = 'Aprobado';
                                                    this.open = false;
                                                    this.loading = false;
                                                })
                                                .catch(() => { this.loading = false; alert('Error al aprobar.'); });
                                            },
                                            rechazarDocumento(id) {
                                                this.loading = true;
                                                fetch(`/api/documentos/${id}/rechazar`, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                    },
                                                    body: JSON.stringify({ comentario: this.comentario })
                                                })
                                                .then(r => r.json())
                                                .then(data => {
                                                    this.estado = 'Rechazado';
                                                    this.open = false;
                                                    this.loading = false;
                                                })
                                                .catch(() => { this.loading = false; alert('Error al rechazar.'); });
                                            }
                                        }"
                                        class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 overflow-hidden border-l-4"
                                        :class="{
                                            'border-green-500': estado === 'Aprobado',
                                            'border-red-500': estado === 'Rechazado',
                                            'border-gray-400': estado === 'Pendiente'
                                        }"
                                    >
                                        
                                        <div class="p-5 flex items-center gap-5">
                                            <!-- Icon -->
                                            <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-600': estado === 'Aprobado',
                                                    'bg-red-100 text-red-600': estado === 'Rechazado',
                                                    'bg-gray-100 text-gray-500': estado === 'Pendiente'
                                                }">
                                                <i class="fas fa-file-alt text-2xl"></i>
                                            </div>
                                            
                                            <!-- Document Info -->
                                            <div class="flex-grow min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="font-bold text-lg text-gray-800 truncate" title="{{ $documento['nombre'] }}">{{ $documento['nombre'] }}</p>
                                                    <span class="ml-4 px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider"
                                                        :class="{
                                                            'bg-green-100 text-green-800': estado === 'Aprobado',
                                                            'bg-red-100 text-red-800': estado === 'Rechazado',
                                                            'bg-gray-200 text-gray-700': estado === 'Pendiente'
                                                        }" 
                                                        x-text="estado"></span>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    @if($documento['fecha_subida']) Subido: {{ \Carbon\Carbon::parse($documento['fecha_subida'])->format('d/m/Y, h:i A') }} @else Sin entregar @endif
                                                </p>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex-shrink-0 flex items-center gap-2">
                                                @if($hasFile)
                                                    <a href="{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => $documento['id']]) }}" target="_blank" class="h-10 w-10 inline-flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-200 hover:text-gray-800 transition-all" title="Ver Documento">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                <button @click="open = !open" class="h-10 w-10 inline-flex items-center justify-center rounded-full text-white bg-[#9d2449] hover:bg-[#831f3c] transition-all shadow-md hover:shadow-lg" title="Evaluar Documento">
                                                    <i class="fas fa-pen-nib"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Collapsible Evaluation Panel -->
                                        <div x-show="open" x-collapse class="bg-gray-100 border-t border-gray-200">
                                            <form @submit.prevent class="p-5 space-y-4">
                                                <div class="w-full">
                                                    <label for="comentario_doc_{{ $documento['id'] }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                        <i class="fas fa-comment-alt mr-2 text-[#9d2449]"></i>Análisis y Comentarios
                                                    </label>
                                                    <div class="relative">
                                                        <textarea 
                                                            :id="'comentario_doc_' + {{ $documento['id'] }}" 
                                                            name="comentario"
                                                            rows="5"
                                                            x-model="comentario"
                                                            class="form-textarea block w-full border-gray-200 rounded-lg placeholder-gray-400 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/50 transition duration-150 ease-in-out text-sm leading-relaxed whitespace-pre-wrap break-words resize-y min-h-[120px] px-4 py-3"
                                                            style="word-wrap: break-word; white-space: pre-wrap; letter-spacing: 0.3px;"
                                                            placeholder="Escriba sus observaciones aquí..."
                                                        >{{ trim($documento['observaciones']) }}</textarea>
                                                    </div>
                                                    <p class="mt-1 text-xs text-gray-500">Ingrese sus observaciones detalladas sobre el documento.</p>
                                                </div>
                                                
                                                @if(!empty($documento['observaciones']))
                                                <div class="text-sm text-gray-600 border-l-4 border-[#9d2449]/30 pl-4 py-3 bg-white rounded-r-lg shadow-sm">
                                                    <p class="font-semibold text-gray-800 mb-2 flex items-center">
                                                        <i class="fas fa-history mr-2 text-[#9d2449]"></i>Comentario Previo
                                                    </p>
                                                    <div class="italic whitespace-pre-wrap break-words overflow-auto max-h-32 bg-gray-50/80 p-4 rounded-lg text-gray-600 leading-relaxed" style="letter-spacing: 0.3px;">
                                                        {{ $documento['observaciones'] }}
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <div class="flex items-center justify-end gap-3 pt-2">
                                                    <button type="button" @click="rechazarDocumento({{ $documento['id'] }})" :disabled="loading" class="font-semibold py-2 px-5 rounded-lg text-red-600 bg-white border-2 border-red-200 hover:bg-red-50 hover:border-red-500 transition-all shadow-sm disabled:opacity-60">
                                                        <span x-show="!loading">Rechazar</span>
                                                        <span x-show="loading">Procesando...</span>
                                                    </button>
                                                    <button type="button" @click="aprobarDocumento({{ $documento['id'] }})" :disabled="loading" class="font-semibold py-2 px-5 rounded-lg text-white bg-green-600 hover:bg-green-700 transition-all shadow-md disabled:opacity-60">
                                                        <span x-show="!loading">Aprobar</span>
                                                        <span x-show="loading">Procesando...</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-16">
                                        <div class="mx-auto h-20 w-20 text-gray-400 mb-4 flex items-center justify-center bg-gray-100 rounded-full"><i class="fas fa-folder-open text-4xl"></i></div>
                                        <h3 class="text-xl font-medium text-gray-900">Expediente Vacío</h3>
                                        <p class="mt-2 text-md text-gray-500">No se han entregado documentos para este trámite.</p>
                                    </div>
                                @endforelse
                            </div>
                            @break
                    @endswitch
                    @if($formView)
                        @include('components.revision.cotejo-tabs', ['documento' => $documentoData, 'formulario' => $formView])
                    @endif
                </div>
                
                <div class="border-t border-gray-200 p-6 lg:p-8">
                    <x-revision.seccion-revision 
                        :seccionId="$seccion['id']"
                        :estado="$revisionesExistentes[$seccion['id']]['estado'] ?? null"
                        :comentario="$revisionesExistentes[$seccion['id']]['comentario'] ?? null"
                        :tramiteId="$tramite->id"
                    />
                </div>
            </div>
        @endforeach
    </main>

    <!-- Barra Flotante de Finalización -->
    <div x-show="!isLoading" x-transition class="sticky bottom-4 z-40 w-full mt-8">
        <div class="max-w-4xl mx-auto bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-bold text-gray-800" x-text="getBotonTexto()"></p>
                    <p class="text-sm text-gray-600" x-text="getDescripcionBoton()"></p>
                </div>
                <button @click="terminarRevisionDigital()" :disabled="!todasSeccionesRevisadas() || isLoading" :class="getBotonClass()"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-white transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] disabled:cursor-not-allowed">
                    <span x-text="getBotonTexto()"></span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
             <!-- Alertas -->
            <div x-show="error" class="mt-3 p-3 bg-red-100 border border-red-300 rounded-lg text-sm text-red-800" x-text="error"></div>
            <div x-show="success" class="mt-3 p-3 bg-green-100 border border-green-300 rounded-lg text-sm text-green-800" x-text="success"></div>
        </div>
    </div>
    
    <!-- Modal para agendar cita (sin cambios) -->
    <div x-show="showAgendarCitaModal" x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-60 flex items-center justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div @click.away="resetCitaForm()" class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 sm:p-8 m-4">
            <div class="flex items-start justify-between">
                 <div class="flex items-center space-x-4">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-[#9d2449] bg-opacity-10">
                        <i class="fas fa-calendar-check text-2xl text-[#9d2449]"></i>
                    </div>
                    <div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            @if($tramite->citaCotejo) Reagendar Cita @else Agendar Cita @endif
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Cotejo presencial de documentos.</p>
                    </div>
                </div>
                <button @click="resetCitaForm()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form @submit.prevent="agendarCita()" class="mt-6 space-y-4">
                <div>
                    <label for="fecha_cita" class="block text-sm font-medium text-gray-700">Fecha y Hora</label>
                    <input type="datetime-local" id="fecha_cita" x-model="fechaCita" required :min="minDateTime"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#9d2449] focus:ring-[#9d2449] sm:text-sm">
                </div>
                <div>
                    <label for="motivo_cita" class="block text-sm font-medium text-gray-700">Motivo</label>
                    <input type="text" id="motivo_cita" x-model="motivoCita" required readonly
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-[#9d2449] focus:ring-[#9d2449] sm:text-sm">
                </div>
                <div>
                    <label for="notas_cita" class="block text-sm font-medium text-gray-700">Notas adicionales</label>
                    <textarea x-model="notasCita" id="notas_cita" rows="3" placeholder="Información adicional..."
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#9d2449] focus:ring-[#9d2449] sm:text-sm"></textarea>
                </div>

                <div x-show="errorCita" class="p-3 bg-red-100 border border-red-300 rounded-lg text-sm text-red-800" x-text="errorCita"></div>
                <div x-show="successCita" class="p-3 bg-green-100 border border-green-300 rounded-lg text-sm text-green-800" x-text="successCita"></div>

                <div class="pt-4 flex flex-col sm:flex-row-reverse gap-3">
                    <button type="submit" :disabled="isLoadingCita"
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-[#9d2449] text-base font-medium text-white hover:bg-[#7a1d3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] disabled:opacity-50">
                        <i x-show="isLoadingCita" class="fas fa-spinner animate-spin -ml-1 mr-2 h-5 w-5"></i>
                        <span x-text="isLoadingCita ? 'Procesando...' : (@if($tramite->citaCotejo) 'Reagendar' @else 'Agendar' @endif)"></span>
                    </button>
                    <button type="button" @click="resetCitaForm()"
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-6 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contenedor para notificaciones flotantes -->
    <div id="notification-container" class="fixed top-5 right-5 z-50 space-y-3"></div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('revisionController', () => ({
        // Controller principal sin cambios en la logica
        progreso: 0,
        seccionesEstados: {},
        showAgendarCitaModal: false,
        isLoadingCita: false,
        fechaCita: '',
        motivoCita: 'Cotejo físico de documentos - Trámite completo',
        notasCita: '',
        minDateTime: '',
        errorCita: null,
        successCita: null,
        isLoading: false,
        error: null,
        success: null,
        tipoPersona: '{{ $tramite->solicitante->tipo_persona ?? 'Física' }}',
        
        init() {
            this.seccionesEstados = {
                1: '{{ $revisionesExistentes[1]['estado'] ?? 'pendiente' }}',
                2: '{{ $revisionesExistentes[2]['estado'] ?? 'pendiente' }}',
                @if($tramite->solicitante && $tramite->solicitante->tipo_persona === 'Moral')
                3: '{{ $revisionesExistentes[3]['estado'] ?? 'pendiente' }}',
                4: '{{ $revisionesExistentes[4]['estado'] ?? 'pendiente' }}',
                5: '{{ $revisionesExistentes[5]['estado'] ?? 'pendiente' }}',
                @endif
                6: '{{ $revisionesExistentes[6]['estado'] ?? 'pendiente' }}'
            };

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            tomorrow.setHours(8, 0, 0, 0);
            this.minDateTime = tomorrow.toISOString().slice(0, 16);
            
            this.updateProgress();
            this.$watch('seccionesEstados', () => this.updateProgress());

            window.addEventListener('revision-section-updated', (event) => {
                const { seccionId, estado } = event.detail;
                if (this.seccionesEstados.hasOwnProperty(seccionId)) {
                    this.seccionesEstados[seccionId] = estado;
                }
            });
        },
        updateProgress() {
            const completadas = this.seccionesCompletadas();
            const total = this.totalSeccionesRequeridas();
            this.progreso = total > 0 ? completadas / total : 0;
        },
        totalSeccionesRequeridas() { return this.tipoPersona === 'Moral' ? 6 : 3; },
        seccionesRequeridasIds() { return this.tipoPersona === 'Moral' ? [1, 2, 3, 4, 5, 6] : [1, 2, 6]; },
        seccionesCompletadas() { return this.seccionesRequeridasIds().filter(id => this.seccionesEstados[id] === 'aprobado' || this.seccionesEstados[id] === 'rechazado').length; },
        todasSeccionesRevisadas() { return this.seccionesCompletadas() === this.totalSeccionesRequeridas(); },
        todasSeccionesAprobadas() { return this.seccionesRequeridasIds().every(id => this.seccionesEstados[id] === 'aprobado'); },
        haySeccionesRechazadas() { return this.seccionesRequeridasIds().some(id => this.seccionesEstados[id] === 'rechazado'); },
        getBotonTexto() {
            if (this.isLoading) return 'Procesando...';
            if (!this.todasSeccionesRevisadas()) return 'Completar Revisión';
            if (this.todasSeccionesAprobadas()) return 'Agendar Cita Presencial';
            if (this.haySeccionesRechazadas()) return 'Enviar para Correcciones';
            return 'Terminar Revisión Digital';
        },
        getDescripcionBoton() {
            if (!this.todasSeccionesRevisadas()) return 'Aún faltan secciones por marcar como "aprobado" o "rechazado".';
            if (this.todasSeccionesAprobadas()) return '¡Todo listo! Se agendará una cita para el cotejo presencial.';
            if (this.haySeccionesRechazadas()) return 'Se notificará al solicitante para que corrija las secciones rechazadas.';
            return 'Finalizar el proceso de revisión digital.';
        },
        getBotonClass() {
            if (!this.todasSeccionesRevisadas()) return 'bg-gray-400 text-gray-700';
            if (this.todasSeccionesAprobadas()) return 'bg-green-600 hover:bg-green-700';
            if (this.haySeccionesRechazadas()) return 'bg-orange-600 hover:bg-orange-700';
            return 'bg-[#9d2449] hover:bg-[#7a1d3a]';
        },
        async terminarRevisionDigital() {
            if (this.isLoading || !this.todasSeccionesRevisadas()) return;
            this.isLoading = true; this.error = null; this.success = null;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const todasAprobadas = this.todasSeccionesAprobadas();
                const response = await fetch(`/revision/{{ $tramite->id }}/terminar-revision-digital`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                    body: JSON.stringify({
                        secciones_estados: this.seccionesEstados,
                        accion: todasAprobadas ? 'agendar_cita' : 'enviar_correcciones'
                    })
                });
                const data = await response.json();
                if (!response.ok) throw data;
                this.success = data.message || 'Operación completada con éxito.';
                setTimeout(() => {
                    if (data.redirect_url) window.location.href = data.redirect_url;
                    else window.location.reload();
                }, 2000);
            } catch (err) {
                this.error = err.message || 'Ocurrió un error al finalizar la revisión.';
                console.error(err);
            } finally { this.isLoading = false; }
        },
        resetCitaForm() {
            this.fechaCita = '';
            this.notasCita = '';
            this.errorCita = null;
            this.successCita = null;
            this.showAgendarCitaModal = false;
        },
        async agendarCita() {
            if (this.isLoadingCita) return;
            this.isLoadingCita = true; this.errorCita = null; this.successCita = null;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(`/revision/{{ $tramite->id }}/agendar-cita`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                    body: JSON.stringify({ fecha_hora: this.fechaCita, motivo: this.motivoCita, notas: this.notasCita, general: true })
                });
                const data = await response.json();
                if (!response.ok) throw data;
                this.successCita = data.message || 'Cita agendada correctamente.';
                setTimeout(() => this.resetCitaForm(), 2000);
            } catch (err) {
                this.errorCita = err.message || 'Error al agendar la cita.';
                console.error(err);
            } finally { this.isLoadingCita = false; }
        }
    }));
});

window.actualizarEstadoRevision = function(seccionId, nuevoEstado) {
    window.dispatchEvent(new CustomEvent('revision-section-updated', {
        detail: { seccionId: parseInt(seccionId), estado: nuevoEstado }
    }));
};

/**
 * Funciones para la revisión de documentos individuales
 */
async function aprobarDocumento(documentoSolicitanteId) {
    const comentario = document.getElementById(`comentario_doc_${documentoSolicitanteId}`)?.value || 'Documento aprobado';
    
    try {
        const response = await fetch(`/revision/{{ $tramite->id }}/documento/${documentoSolicitanteId}/aprobar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ comentario: comentario })
        });

        const data = await response.json();
        if (data.success) {
            actualizarEstadoDocumentoUI(documentoSolicitanteId, 'Aprobado');
            mostrarNotificacion('success', data.message);
        } else {
            mostrarNotificacion('error', data.message || 'No se pudo aprobar el documento.');
        }
    } catch (error) {
        mostrarNotificacion('error', 'Error de conexión.');
    }
}

async function rechazarDocumento(documentoSolicitanteId) {
    const comentario = document.getElementById(`comentario_doc_${documentoSolicitanteId}`)?.value;
    
    if (!comentario || comentario.trim().length < 10) {
        mostrarNotificacion('error', 'El comentario es obligatorio y debe tener al menos 10 caracteres para rechazar.');
        return;
    }
    
    try {
        const response = await fetch(`/revision/{{ $tramite->id }}/documento/${documentoSolicitanteId}/rechazar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ comentario: comentario })
        });

        const data = await response.json();
        if (data.success) {
            actualizarEstadoDocumentoUI(documentoSolicitanteId, 'Rechazado');
            mostrarNotificacion('success', data.message);
        } else {
            mostrarNotificacion('error', data.message || 'No se pudo rechazar el documento.');
        }
    } catch (error) {
        mostrarNotificacion('error', 'Error de conexión.');
    }
}

function actualizarEstadoDocumentoUI(documentoSolicitanteId, nuevoEstado) {
    const card = document.getElementById(`doc-card-${documentoSolicitanteId}`);
    if (card) {
        const alpineComponent = Alpine.$data(card);
        if (alpineComponent) {
            alpineComponent.estado = nuevoEstado;
            alpineComponent.open = false; // Cierra el panel de revisión
        }
    }
}

function mostrarNotificacion(tipo, mensaje) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const id = 'notif_' + Date.now();
    const icon = tipo === 'success' 
        ? '<i class="fas fa-check-circle"></i>' 
        : '<i class="fas fa-exclamation-triangle"></i>';
    
    const colors = tipo === 'success'
        ? 'bg-emerald-500 border-emerald-600'
        : 'bg-rose-500 border-rose-600';

    const notificacionEl = document.createElement('div');
    notificacionEl.id = id;
    notificacionEl.className = `flex items-center text-white text-sm font-medium px-4 py-3 rounded-lg shadow-xl transition-all duration-300 transform translate-x-full ${colors}`;
    notificacionEl.innerHTML = `<div class="mr-2">${icon}</div><div>${mensaje}</div>`;
    
    container.appendChild(notificacionEl);

    // Animar entrada
    setTimeout(() => {
        notificacionEl.classList.remove('translate-x-full');
    }, 10);

    // Remover después de 5 segundos
    setTimeout(() => {
        notificacionEl.classList.add('opacity-0', 'scale-90');
        setTimeout(() => notificacionEl.remove(), 300);
    }, 5000);
}
</script>
@endpush
