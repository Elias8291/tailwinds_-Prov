@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Notificaciones -->
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

        <!-- Encabezado Compacto -->
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
                            
        <!-- Contenedor Principal con Tabs Mejorados -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80" x-data="{ activeTab: 'rfc' }">
            <!-- Tabs Compactos y Modernos -->
            <div class="p-3">
                <div class="bg-gray-100/80 p-1 rounded-lg flex gap-1">
                    <button @click="activeTab = 'rfc'" 
                            :class="activeTab === 'rfc' ? 'tab-active text-white' : 'tab-inactive text-gray-600'"
                            class="tab-button flex-1 px-4 py-2 text-sm font-medium rounded-md flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                        <span class="font-semibold">Consulta RFC</span>
                                    </button>
                    <button @click="activeTab = 'constancia'" 
                            :class="activeTab === 'constancia' ? 'tab-active text-white' : 'tab-inactive text-gray-600'"
                            class="tab-button flex-1 px-4 py-2 text-sm font-medium rounded-md flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                        <span class="font-semibold">Cargar Constancia</span>
                                    </button>
                                </div>
                            </div>

            <!-- Contenido -->
            <div class="p-4 pt-2">
                <!-- Búsqueda RFC -->
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
                                <button class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-[#9d2449] to-[#7a1d37] hover:from-[#7a1d37] hover:to-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95 gap-1.5">
                                    <span>Consultar RFC</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carga Constancia -->
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
                            <!-- Drop Zone -->
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

        <!-- Incluir el componente de servicios -->
        @include('components.servicios-disponibles')
    </div>
</div>

<!-- Elementos necesarios para QR (ocultos) -->
<div class="hidden">
    <div id="qrResult"></div>
    <canvas id="pdfCanvas"></canvas>
    <div id="qr-file-name"></div>
    <div id="qr-file-status"></div>
    <div id="qr-processing-status"></div>
    <div id="qr-error-message"></div>
</div>

<!-- Indicador de carga -->
<div id="loading-indicator" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-4 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-8 w-8 border-4 border-[#9d2449] border-t-transparent"></div>
        <span class="text-gray-700 font-medium">Procesando documento...</span>
    </div>
</div>

<!-- Modal para mostrar datos del SAT -->
<div id="satDataModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden">
        <!-- Modal header -->
        <div class="px-6 py-4 bg-gradient-to-br from-[#9d2449] to-[#7a1d37] border-b border-[#9d2449]/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white/10 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Datos del SAT</h3>
                </div>
                <button onclick="closeSatModal()" class="text-white/80 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Modal body -->
        <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 150px);">
            <div id="satDataContent" class="space-y-6">
                <!-- Los datos del SAT se insertarán aquí -->
            </div>
        </div>
        <!-- Modal footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            <div class="flex justify-end">
                <button onclick="closeSatModal()" 
                        class="inline-flex items-center px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 font-medium rounded-lg border border-gray-300 transition-colors duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js"></script>
<script type="module">
    import QRHandler from '/js/components/qr-handler.js';
    import QRReader from '/js/components/qr-reader.js';
    import SATValidator from '/js/validators/sat-validator.js';
    import SATScraper from '/js/scrapers/sat-scraper.js';
    
    // Configuración específica para index.blade.php
    const config = {
        fileNameElement: 'qr-file-name',
        previewAreaElement: 'qr-preview',
        uploadAreaElement: 'qr-upload-area',
        verDatosBtnElement: 'verDatosBtn'
    };

    let qrHandler = null;
    let isProcessing = false;
    let lastScannedData = null;

    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            console.log('Inicializando QRHandler...');
            
            // Crear e inicializar QRHandler con configuración
            qrHandler = new QRHandler(config);
            await qrHandler.initialize(QRReader, SATValidator, SATScraper);

            // Configurar callbacks
            qrHandler.setOnDataScanned((data) => {
                console.log('Datos escaneados:', data);
                lastScannedData = data;
                isProcessing = false;
                showLoading(false);
                showSatModal();
            });

            qrHandler.setOnError((error) => {
                console.error('Error en QRHandler:', error);
                isProcessing = false;
                showLoading(false);
                showError(error);
                resetUpload();
            });

            console.log('QRHandler inicializado correctamente');

        } catch (error) {
            console.error('Error durante la inicialización:', error);
            showError('Error al inicializar el lector QR: ' + error.message);
        }
    });

    // Función para mostrar el modal con datos del SAT
    window.showSatModal = function() {
        const modal = document.getElementById('satDataModal');
        if (modal && lastScannedData) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            const satDataContent = document.getElementById('satDataContent');
            if (satDataContent) {
                const content = generateSatDataHtml(lastScannedData);
                satDataContent.innerHTML = content;
            }
        }
    };

    // Función para generar el HTML de los datos del SAT
    function generateSatDataHtml(data) {
        if (!data || !data.details) return '<p>No hay datos disponibles</p>';

        const details = data.details;
        return `
            <div class="space-y-6">
                <!-- Sección de información principal -->
                <div class="bg-white rounded-xl p-6 border border-[#B4325E]/10 shadow-sm">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                ${details.tipoPersona === 'Moral' ? 
                                    (details.razonSocial || 'Razón Social No Disponible') : 
                                    (details.nombreCompleto || 'Nombre No Disponible')}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                    RFC: ${details.rfc || 'No disponible'}
                                </span>
                                ${details.curp ? `
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                        CURP: ${details.curp}
                                    </span>
                                ` : ''}
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                    Persona ${details.tipoPersona || 'No especificada'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de dirección -->
                ${details.nombreVialidad || details.colonia || details.cp ? `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h4 class="text-lg font-semibold text-gray-800">
                                Dirección
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2">
                                ${details.nombreVialidad ? `
                                    <p class="text-sm text-gray-900">
                                        ${details.nombreVialidad}
                                        ${details.numeroExterior ? ` #${details.numeroExterior}` : ''}
                                        ${details.numeroInterior ? ` Int. ${details.numeroInterior}` : ''}
                                    </p>
                                ` : ''}
                                ${details.colonia ? `<p class="text-sm text-gray-600">Col. ${details.colonia}</p>` : ''}
                                ${details.cp ? `<p class="text-sm text-gray-600">CP ${details.cp}</p>` : ''}
                            </div>
                        </div>
                    </div>
                ` : ''}

                <!-- Secciones adicionales -->
                ${data.sections && data.sections.length > 0 ? data.sections.map(section => `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h4 class="text-lg font-semibold text-gray-800">
                                ${section.title || 'Información Adicional'}
                            </h4>
                        </div>
                        <div class="divide-y divide-gray-100">
                            ${section.fields.map(field => `
                                <div class="px-6 py-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center">
                                        <div class="sm:w-1/3">
                                            <span class="text-sm font-medium text-gray-500">${field.label || ''}</span>
                                        </div>
                                        <div class="sm:w-2/3 mt-1 sm:mt-0">
                                            <span class="text-sm text-gray-900">${field.value || ''}</span>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `).join('') : ''}
            </div>
        `;
    }

    // Función para cerrar el modal
    window.closeSatModal = function() {
        const modal = document.getElementById('satDataModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // Cerrar modal al hacer clic fuera de él
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('satDataModal');
        const modalContent = modal?.querySelector('.bg-white');
        if (modal && event.target === modal && modalContent && !modalContent.contains(event.target)) {
            closeSatModal();
        }
    });

    // Evento change del input file
    document.getElementById('documentInput')?.addEventListener('change', async (event) => {
        const file = event.target.files[0];
        if (!file || isProcessing) return;

        try {
            isProcessing = true;
            showLoading(true);

            // Validar el tipo de archivo
            const isPDF = file.type === 'application/pdf';
            const isImage = file.type.startsWith('image/');
            
            if (!isPDF && !isImage) {
                throw new Error('El archivo debe ser un PDF o una imagen (JPG, PNG).');
            }

            // Validar el tamaño del archivo (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                throw new Error('El archivo no debe exceder los 5MB.');
            }

            console.log('Iniciando procesamiento del archivo:', file.name);

            // Verificar que QRHandler está inicializado
            if (!qrHandler) {
                throw new Error('El lector QR no está inicializado. Por favor, recarga la página.');
            }

            // Actualizar elementos de UI
            const fileNameElement = document.getElementById('qr-file-name');
            const fileStatusElement = document.getElementById('qr-file-status');
            const processingStatusElement = document.getElementById('qr-processing-status');
            
            if (fileNameElement) fileNameElement.textContent = file.name;
            if (fileStatusElement) fileStatusElement.textContent = 'Archivo seleccionado';
            if (processingStatusElement) processingStatusElement.textContent = 'Procesando...';

            await qrHandler.handleFile(file);

        } catch (error) {
            console.error('Error detallado:', error);
            showError(error.message || 'Error al procesar el documento');
            resetUpload();
        } finally {
            isProcessing = false;
            showLoading(false);
        }
    });

    // Función para mostrar/ocultar el indicador de carga
    function showLoading(show = true) {
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.style.display = show ? 'flex' : 'none';
        }
    }

    // Función para mostrar errores
    function showError(message) {
        const errorElement = document.getElementById('qr-error-message');
        if (errorElement) {
            errorElement.textContent = message;
        }

        // Crear notificación de error
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm';
        notification.innerHTML = `
            <div class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-800">${message}</p>
                </div>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => notification.remove(), 3000);
    }

    // Función para resetear el estado
    function resetUpload() {
        const fileInput = document.getElementById('documentInput');
        if (fileInput) {
            fileInput.value = '';
        }

        const elements = {
            'qr-file-name': '',
            'qr-file-status': '',
            'qr-processing-status': '',
            'qr-error-message': ''
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });

        isProcessing = false;
    }
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
</style>

@endsection 