@extends('layouts.app')

@section('title', 'Revisión de Trámite')

@section('content')
<div class="min-h-screen">
    <!-- Modales -->
    <x-modals.document-viewer-modal />
    <x-modals.map-modal />
</div>

@push('scripts')
<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>

<!-- Módulos JS -->
<script type="module">
    import { DocumentReviewHandler } from '/js/modules/document-review-handler.js';
    import { MapHandler } from '/js/modules/map-handler.js';

    // Inicializar manejadores
    const documentReviewHandler = new DocumentReviewHandler();
    const mapHandler = new MapHandler();

    // Exponer funciones al ámbito global
    window.abrirModalDocumento = (url, info) => documentReviewHandler.abrirModalDocumento(url, info);
    window.cerrarModalDocumento = () => documentReviewHandler.cerrarModalDocumento();
    window.aplicarRevisionDocumento = (estado) => documentReviewHandler.aplicarRevisionDocumento(estado);
    window.guardarComentarioDocumento = () => documentReviewHandler.guardarComentarioDocumento();
    
    window.abrirModalMapa = (address) => mapHandler.abrirModalMapa(address);
    window.cerrarModalMapa = () => mapHandler.cerrarModalMapa();
    window.cambiarVistaMapaModal = (tipo) => mapHandler.cambiarVistaMapaModal(tipo);
    window.copiarDireccion = () => mapHandler.copiarDireccion();
</script>
@endpush
@endsection