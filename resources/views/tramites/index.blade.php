@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm" x-data="{ success: false, error: false }">
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="bg-white rounded-xl shadow-lg border-l-4 border-[#9d2449] p-4 mb-2">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-800">{{ session('success') }}</p>
                </div>
            </div>
            @endif
            @if(session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-800">{{ session('error') }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg p-4 mb-5 border border-gray-200/80">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-2.5 shadow-md">
                    <svg class="w-6 h-6 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                        Trámites Disponibles
                    </h1>
                    <p class="text-xs text-gray-500">Seleccione su método de consulta</p>
                </div>
            </div>
        </div>
                            
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80 mb-5" x-data="{ activeTab: 'rfc' }">
            <div class="p-3">
                <div class="bg-gray-100/80 p-1 rounded-lg flex gap-1">
                    <button @click="activeTab = 'rfc'; clearSearchFields()" 
                            :class="activeTab === 'rfc' ? 'tab-active text-white' : 'tab-inactive text-gray-600'"
                            class="tab-button flex-1 px-4 py-2 text-sm font-medium rounded-md flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="font-semibold">Consulta RFC</span>
                    </button>
                    <button @click="activeTab = 'constancia'; clearSearchFields()" 
                            :class="activeTab === 'constancia' ? 'tab-active text-white' : 'tab-inactive text-gray-600'"
                            class="tab-button flex-1 px-4 py-2 text-sm font-medium rounded-md flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span class="font-semibold">Cargar Constancia</span>
                    </button>
                </div>
            </div>

            <div class="p-4 pt-2">
                <div x-show="activeTab === 'rfc'" 
                     class="space-y-3">
                    <div class="bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-xl border border-[#9d2449]/10 p-3">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-1.5 shadow-md">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Consulta por RFC</h2>
                        </div>

                        <div class="space-y-2">
                            <div class="relative">
                                <input type="text" 
                                       id="rfcSearchInput"
                                       placeholder="XAXX010101000" 
                                       class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 transition-all duration-300 text-gray-700 text-sm font-mono bg-white/90 backdrop-blur-sm">
                            </div>

                            <div class="flex justify-end">
                                <button id="buscarRFCBtn" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-[#9d2449] to-[#7a1d37] hover:from-[#7a1d37] hover:to-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95 gap-1.5">
                                    <span>Consultar RFC</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'constancia'" 
                     class="space-y-3"
                     x-data="{ fileName: '', isDragging: false }">
                    <div class="bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-xl border border-[#9d2449]/10 p-3">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-1.5 shadow-md">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Cargar Constancia</h2>
                        </div>

                        <div class="space-y-3">
                            <div :class="isDragging ? 'border-[#9d2449] bg-[#9d2449]/5' : 'border-gray-300 hover:border-[#9d2449]'"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave="isDragging = false"
                                 @drop.prevent="isDragging = false"
                                 class="border-2 border-dashed transition-all duration-300 rounded-xl p-4 text-center relative bg-white/50 hover:bg-white/80 cursor-pointer group">
                                <input type="file" 
                                       id="documentInput" 
                                       name="document" 
                                       accept=".pdf,.png,.jpg,.jpeg" 
                                       @change="fileName = $event.target.files[0]?.name || ''"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <div class="space-y-2">
                                    <div class="bg-[#9d2449]/10 w-10 h-10 rounded-lg flex items-center justify-center mx-auto group-hover:bg-[#9d2449]/20 transition-colors duration-300">
                                        <svg class="w-5 h-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-[#9d2449]" x-text="fileName ? 'Archivo seleccionado: ' + fileName : 'Arrastra tu constancia aquí'"></p>
                                        <p class="text-xs text-gray-500" x-text="fileName ? '' : 'o haz clic para seleccionar'"></p>
                                        <p class="text-xs text-gray-400 mt-1">PDF, PNG, JPG • Máx. 5MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="resumenProveedor" class="hidden bg-white/95 backdrop-blur-sm rounded-xl border border-[#9d2449]/20 shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-[#9d2449]/8 to-[#7a1d37]/8 p-3 border-b border-[#9d2449]/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-1.5 shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                            Información del Proveedor
                        </h3>
                    </div>
                    <div id="estadoProveedor" class="px-2 py-1 rounded-full text-xs font-semibold"></div>
                </div>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-lg p-3 border border-gray-200/50">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                            </svg>
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">RFC</span>
                        </div>
                        <p id="proveedorRFC" class="text-sm font-mono font-bold text-gray-800">-</p>
                    </div>
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-lg p-3 border border-gray-200/50">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">Tipo</span>
                        </div>
                        <p id="proveedorTipo" class="text-sm font-semibold text-gray-800">-</p>
                    </div>
                    <div class="md:col-span-2 bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-lg p-3 border border-[#9d2449]/10">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-xs font-medium text-[#9d2449] uppercase tracking-wide">Nombre/Razón Social</span>
                        </div>
                        <p id="proveedorNombre" class="text-sm font-semibold text-gray-800 leading-relaxed">-</p>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200/50">
                    <div class="flex flex-wrap gap-2 justify-end">
                        <button id="verHistorialBtn" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-[#9d2449] bg-white border border-[#9d2449]/30 hover:bg-[#9d2449]/5 transition-all duration-300 gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ver Historial
                        </button>
                        <button id="verDatosSatBtn" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-gradient-to-r from-[#9d2449] to-[#7a1d37] hover:from-[#7a1d37] hover:to-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver datos SAT
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="serviciosDisponiblesContainer"></div>
    </div>
</div>

<div class="hidden">
    <div id="qrResult"></div>
    <canvas id="pdfCanvas"></canvas>
    <div id="qr-file-name"></div>
    <div id="qr-file-status"></div>
    <div id="qr-processing-status"></div>
    <div id="qr-error-message"></div>
</div>

<div id="verSatModalBtn" class="fixed bottom-6 right-6 hidden">
    <button type="button"
            class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#9d2449] to-[#7a1d37] text-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        <span class="font-medium">Ver Datos SAT</span>
    </button>
</div>

@include('components.loading-indicator')
@include('components.modals.sat-data-modal')
@include('components.modals.historial-proveedor-modal')
@include('components.resumen-proveedor')

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js"></script>
<script type="module">
    import { SATHandler } from '/js/modules/sat-handler.js';
    import { HistorialHandler } from '/js/modules/historial-handler.js';
    
    const config = {
        fileNameElement: 'qr-file-name',
        previewAreaElement: 'qr-preview',
        uploadAreaElement: 'qr-upload-area',
        verDatosBtnElement: 'verDatosBtn'
    };

    window.satHandler = null;
    window.historialHandler = null;

    window.clearSearchFields = function() {
        // Limpiar campo de RFC
        document.getElementById('rfcSearchInput').value = '';
        // Limpiar archivo seleccionado
        document.getElementById('documentInput').value = '';
        // Ocultar resumen del proveedor
        const resumenDiv = document.getElementById('resumenProveedor');
        if (resumenDiv) {
            resumenDiv.classList.add('hidden');
        }
        // Limpiar el historial si está visible
        const historialModal = document.getElementById('historialProveedorModal');
        if (historialModal && historialModal.style.display !== 'none') {
            window.closeHistorialModal();
        }
    };

    window.actualizarResumenProveedor = (data, fromSAT = false) => {
        const resumenDiv = document.getElementById('resumenProveedor');
        const estadoDiv = document.getElementById('estadoProveedor');
        
        if (resumenDiv && data) {
            resumenDiv.classList.remove('hidden');
            resumenDiv.classList.add('fade-in');
            
            document.getElementById('proveedorRFC').textContent = data.rfc || '-';
            document.getElementById('proveedorTipo').textContent = data.tipoPersona || '-';
            document.getElementById('proveedorNombre').textContent = 
                data.tipoPersona === 'Moral' ? 
                    (data.razonSocial || '-') : 
                    ((data.nombre || '') + ' ' + (data.apellido_paterno || '') + ' ' + (data.apellido_materno || '')).trim() || '-';
            
            if (estadoDiv) {
                const estado = data.estado || (fromSAT ? 'Activo' : 'Inactivo');
                estadoDiv.textContent = estado;
                
                if (estado.toLowerCase() === 'activo') {
                    estadoDiv.className = 'px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800';
                } else {
                    estadoDiv.className = 'px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800';
                }
            }
        }
    };

    // Renderizar servicios disponibles
    function renderServiciosDisponibles(tramites, rfc = null) {
        const container = document.getElementById('serviciosDisponiblesContainer');
        if (!container) return;
        let html = `
        <div class="mt-6 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80">
            <div class="p-5">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-2.5 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                            Servicios Disponibles
                        </h2>
                        <p class="text-sm text-gray-500">Seleccione el tipo de trámite que desea realizar</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        `;
        let hayTramite = false;
        tramites.forEach(tramite => {
            if (!tramite.disponible) return;
            hayTramite = true;
            if (tramite.tipo === 'inscripcion') {
                html += `
                <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative p-6">
                        <div class="mb-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                            Inscripción
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            Registre su primera vez al padrón de contribuyentes con todos los requisitos necesarios.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                                Nuevo registro
                            </span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
                `;
            }
            if (tramite.tipo === 'renovacion') {
                html += `
                <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative p-6">
                        <div class="mb-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                            Renovación
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            Renueve su registro existente para mantener vigente su situación fiscal.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                                Renovar registro
                            </span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
                `;
            }
            if (tramite.tipo === 'actualizacion') {
                html += `
                <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative p-6">
                        <div class="mb-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                            Actualización
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            Modifique sus datos personales, fiscales o de actividad económica.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                                Modificar datos
                            </span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
                `;
            }
        });
        html += '</div>';
        if (!hayTramite) {
            html += `<div class='text-center text-gray-500 py-8'>No hay trámites disponibles para este RFC.</div>`;
        }
        html += '</div></div>';
        container.innerHTML = html;
    }

    // Buscar trámites disponibles por RFC
    async function fetchAndRenderServicios(rfc) {
        if (!rfc) return renderServiciosDisponibles([], null);
        try {
            const resp = await fetch(`/api/rfc-search/${encodeURIComponent(rfc)}`);
            const data = await resp.json();
            if (data && data.tramites) {
                renderServiciosDisponibles(data.tramites, rfc);
            } else {
                renderServiciosDisponibles([], rfc);
            }
        } catch (e) {
            renderServiciosDisponibles([], rfc);
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            window.satHandler = new SATHandler(config);
            window.historialHandler = new HistorialHandler();
            
            await window.satHandler.initialize();

            const rfcSearchInput = document.getElementById('rfcSearchInput');
            const buscarRFCBtn = document.getElementById('buscarRFCBtn');
            
            if (rfcSearchInput && buscarRFCBtn) {
                buscarRFCBtn.addEventListener('click', async () => {
                    const rfc = rfcSearchInput.value.trim();
                    if (rfc) {
                        try {
                            const response = await fetch(`/api/proveedor/historial?rfc=${encodeURIComponent(rfc)}`);
                            const data = await response.json();
                            if (data.success && data.proveedor) {
                                window.actualizarResumenProveedor(data.proveedor, false);
                            }
                        } catch (error) {
                            console.error('Error al buscar proveedor:', error);
                        }
                        // Buscar y renderizar servicios disponibles
                        fetchAndRenderServicios(rfc);
                    }
                });

                rfcSearchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        buscarRFCBtn.click();
                    }
                });
            }

            document.getElementById('documentInput')?.addEventListener('change', async (event) => {
                const file = event.target.files[0];
                if (file) {
                    await window.satHandler.handleFile(file);
                    window.actualizarResumenProveedor(window.satHandler.lastScannedData?.details, true);
                    // Si se detecta RFC en el PDF, buscar y renderizar servicios disponibles
                    const rfc = window.satHandler.lastScannedData?.details?.rfc;
                    if (rfc) fetchAndRenderServicios(rfc);
                }
            });

            // Configurar botones de acciones
            document.getElementById('verHistorialBtn')?.addEventListener('click', () => {
                const rfc = document.getElementById('proveedorRFC')?.textContent;
                if (rfc && rfc !== '-') {
                    window.historialHandler.buscarHistorial(rfc);
                }
            });

            document.getElementById('verDatosSatBtn')?.addEventListener('click', () => {
                window.satHandler?.showModal();
            });

            // Configurar cierre del modal SAT
            window.closeSatModal = () => {
                window.satHandler?.closeModal();
            };

            document.getElementById('satDataModal')?.addEventListener('click', (event) => {
                const modal = event.currentTarget;
                const modalContent = modal.querySelector('.bg-white');
                if (event.target === modal && !modalContent.contains(event.target)) {
                    window.closeSatModal();
                }
            });

            // Al cargar la página, limpiar servicios
            fetchAndRenderServicios(null);

        } catch (error) {
            console.error('Error durante la inicialización:', error);
        }
    });
</script>

<style>
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tab-button {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .tab-active {
        background: linear-gradient(135deg, #9d2449 0%, #7a1d37 100%);
        box-shadow: 0 4px 12px rgba(157, 36, 73, 0.2);
    }
    .tab-inactive {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
    }
    .tab-inactive:hover {
        background: rgba(157, 36, 73, 0.05);
        transform: translateY(-1px);
    }
    .content-transition {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #resumenProveedor {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateY(0);
    }
    #resumenProveedor.hidden {
        opacity: 0;
        transform: translateY(-10px);
        pointer-events: none;
    }
    #resumenProveedor .bg-gradient-to-r {
        transition: all 0.2s ease-in-out;
    }
    #resumenProveedor .bg-gradient-to-r:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>

@endsection