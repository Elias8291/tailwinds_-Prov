@extends('layouts.app')

@section('title', 'Revisión de Trámite')

@push('styles')
<link href="{{ asset('css/revision-tramite.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="min-h-screen">
    <!-- Modales -->
    @include('revision.partials.modals')
    
    <!-- Header Principal -->
    @include('revision.partials.header', [
        'tramite' => $tramite,
        'datosSolicitante' => $datosSolicitante
    ])
    
    <!-- Navegación de Secciones -->
    @include('revision.partials.navigation', [
        'tramite' => $tramite
    ])
    
    <!-- Contenido Principal -->
    <div class="max-w-[1800px] mx-auto px-8 pb-6">
        <div class="flex gap-6" id="main-container">
            <!-- Panel de Formularios -->
            <main class="flex-1" id="formulario-container">
                @include('revision.partials.content', [
                    'tramite' => $tramite,
                    'datosTramite' => $datosTramite,
                    'datosSolicitante' => $datosSolicitante,
                    'datosDomicilio' => $datosDomicilio,
                    'datosConstitucion' => isset($datosConstitucion) ? $datosConstitucion : null,
                    'accionistas' => isset($accionistas) ? $accionistas : [],
                    'datosAccionistas' => isset($datosAccionistas) ? $datosAccionistas : [],
                    'datosApoderado' => isset($datosApoderado) ? $datosApoderado : null,
                    'documentosPorSeccion' => isset($documentosPorSeccion) ? $documentosPorSeccion : []
                ])
            </main>
            
            <!-- Panel Lateral -->
            @include('revision.partials.sidebar', [
                'tramite' => $tramite,
                'documentosPorSeccion' => isset($documentosPorSeccion) ? $documentosPorSeccion : [],
                'datosDomicilio' => $datosDomicilio
            ])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/revision-tramite.js') }}"></script>
<script>
@php
    $tipoPersona = 'Física';
    if (isset($tramite->solicitante) && isset($tramite->solicitante->tipo_persona)) {
        $tipoPersona = $tramite->solicitante->tipo_persona;
    }
    
    $documentosJson = '[]';
    if (isset($documentosPorSeccion)) {
        $documentosJson = json_encode($documentosPorSeccion);
    }
    
    $domicilioJson = '[]';
    if (isset($datosDomicilio)) {
        $domicilioJson = json_encode($datosDomicilio);
    }
    
    $comentariosJson = '[]';
    if (isset($comentariosGenerales)) {
        $comentariosJson = json_encode($comentariosGenerales);
    }
@endphp

// Inicializar datos globales
window.tramiteData = {
    id: {{ $tramite->id }},
    tipo_tramite: "{{ $tramite->tipo_tramite }}",
    estado: "{{ $tramite->estado }}",
    tipo_persona: "{{ $tipoPersona }}"
};

window.revisionData = {
    documentos: {!! $documentosJson !!},
    domicilio: {!! $domicilioJson !!},
    comentarios: {!! $comentariosJson !!}
};

// Inicializar módulo de revisión
document.addEventListener('DOMContentLoaded', function() {
    window.RevisionTramite.init();
});
</script>
@endpush
