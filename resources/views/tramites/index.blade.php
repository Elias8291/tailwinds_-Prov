@extends('layouts.app')

@section('content')
<style>
    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-100%); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideOutUp {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-100%); }
    }
    .notification-slide-in {
        animation: slideInDown 0.5s ease-out forwards;
    }
    .notification-slide-out {
        animation: slideOutUp 0.5s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .card-animate {
        opacity: 0;
        animation: fadeIn 0.6s cubic-bezier(0.21, 0.6, 0.35, 1) forwards;
    }
    .card-animate:nth-child(1) { animation-delay: 0.1s; }
    .card-animate:nth-child(2) { animation-delay: 0.2s; }
    .card-animate:nth-child(3) { animation-delay: 0.3s; }

    .card-custom {
        background-color: rgba(255, 255, 255, 1);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
    }

    .card-custom:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    }

    .gradient-inscripcion,
    .gradient-renovacion,
    .gradient-actualizacion {
        background: linear-gradient(135deg, rgba(243, 247, 255, 0.8) 0%, rgba(255, 255, 255, 0.95) 100%);
        border-left: 4px solid #B4325E;
        backdrop-filter: blur(8px);
    }

    .card-border-inscripcion,
    .card-border-renovacion,
    .card-border-actualizacion {
        border: 1px solid rgba(180, 50, 94, 0.1);
    }

    .upload-area {
        border: 2px dashed rgba(180, 50, 94, 0.2);
        transition: all 0.3s ease;
    }
    .upload-area:hover {
        border-color: #B4325E;
        background-color: rgba(180, 50, 94, 0.05);
    }
</style>

<div class="min-h-screen py-6 bg-gray-50/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="card-custom rounded-2xl shadow-md p-6 md:p-8 mb-8 transform transition-all duration-300 border border-gray-100/50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6">
                <div class="flex items-center space-x-4 md:space-x-6">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-2xl p-4 shadow-lg">
                        <svg class="w-7 h-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Trámites Disponibles
                        </h2>
                        <p class="text-gray-500 mt-1 text-sm md:text-base">Seleccione el tipo de trámite que desea realizar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área de carga de Constancia (más compacta) -->
        <div class="mb-8">
            <div class="card-custom rounded-xl shadow-sm p-4 border border-gray-100/50">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="bg-gradient-to-br from-[#B4325E]/10 to-[#93264B]/10 rounded-lg p-2.5">
                        <svg class="w-5 h-5 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Constancia de Situación Fiscal</h3>
                        <p class="text-sm text-gray-500">Cargue su documento para validación</p>
                    </div>
                </div>

                <div id="uploadArea" class="transition-all duration-300 ease-in-out">
                    <div class="relative">
                        <input type="file" id="document" name="document" accept=".pdf,.png,.jpg,.jpeg" required class="hidden">
                        <label for="document" class="group flex items-center justify-center w-full h-20 border border-dashed border-[#B4325E]/20 hover:border-[#B4325E] rounded-lg transition-all duration-300 cursor-pointer bg-[#B4325E]/5 hover:bg-[#B4325E]/10">
                            <div class="flex items-center space-x-3 px-4">
                                <div class="transform group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-5 h-5 text-[#B4325E]/70 group-hover:text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-[#B4325E]/70 group-hover:text-[#B4325E] transition-colors duration-300">
                                        Seleccionar archivo
                                    </p>
                                    <p class="text-xs text-gray-500" id="fileName">
                                        PDF o Imagen con QR (Máx. 5MB)
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Área de previsualización -->
                    <div id="previewArea" class="hidden">
                        <div class="hidden">
                            <div id="qrResult"></div>
                            <canvas id="pdfCanvas"></canvas>
                        </div>
                    </div>

                    <!-- Contenedor de Datos SAT -->
                    <div id="satDataContainer" class="mt-4 hidden">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-gray-900">Datos del SAT</h3>
                                    <button onclick="showSatModal()" 
                                            class="inline-flex items-center text-xs bg-white hover:bg-[#B4325E]/5 text-[#B4325E] font-medium py-1.5 px-3 rounded-md transition-all duration-300 border border-[#B4325E]/20 hover:border-[#B4325E]/40">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver detalles
                                    </button>
                                </div>
                                <div id="satDataContent" class="space-y-4">
                                    <!-- Los datos del SAT se insertarán aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @foreach($tramites as $tramite)
            <div class="card-animate group">
                <div class="card-custom rounded-xl overflow-hidden transition-all duration-300 group-hover:shadow-lg group-hover:transform group-hover:scale-[1.02] card-border-{{ $tramite['tipo'] }} relative">
                    <div class="gradient-{{ $tramite['tipo'] }} p-5 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-[#B4325E]/5 rounded-full -mr-8 -mt-8 flex items-center justify-center">
                            @if($tramite['tipo'] === 'inscripcion')
                            <svg class="w-8 h-8 text-[#B4325E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            @elseif($tramite['tipo'] === 'renovacion')
                            <svg class="w-8 h-8 text-[#B4325E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            @else
                            <svg class="w-8 h-8 text-[#B4325E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            @endif
                        </div>
                        
                        <div class="relative">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">
                                {{ $tramite['nombre'] }}
                            </h3>
                            <div class="flex items-center">
                                <span class="text-xs font-medium bg-[#B4325E]/10 text-[#B4325E] rounded-lg px-2.5 py-0.5">
                                    @if($tramite['tipo'] === 'inscripcion')
                                        Nuevo registro
                                    @elseif($tramite['tipo'] === 'renovacion')
                                        Extensión
                                    @else
                                        Modificar datos
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-white">
                        <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-2">{{ $tramite['descripcion'] }}</p>
                        
                        <div class="flex justify-end">
                            <a href="{{ route('tramites.create', ['tipo' => $tramite['tipo']]) }}" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white shadow-sm hover:shadow-md transition-all duration-200 bg-[#B4325E] hover:bg-[#93264B]">
                                Iniciar
                                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Notificaciones -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm">
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 class="card-custom rounded-lg shadow-lg border-l-4 border-[#B4325E] p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Modal para mostrar datos completos del SAT -->
        <div id="satDataModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden">
                <!-- Modal header -->
                <div class="px-6 py-4 bg-gradient-to-br from-[#B4325E] to-[#93264B] border-b border-[#B4325E]/10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-white/10 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Datos Completos del SAT</h3>
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
                    <div id="satDataModalContent" class="space-y-6">
                        <!-- Los datos completos del SAT se insertarán aquí -->
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

        <!-- Loading indicator -->
        <div id="loading-indicator" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 shadow-xl">
                <div class="flex items-center space-x-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#B4325E]"></div>
                    <p class="text-gray-700 font-medium">Procesando documento...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js"></script>

<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js';

// Funciones de utilidad
function showLoading(show = true) {
    const loadingIndicator = document.getElementById('loading-indicator');
    if (loadingIndicator) {
        loadingIndicator.classList.toggle('hidden', !show);
    }
}

function showError(message) {
    showLoading(false);
    // Crear notificación de error
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm notification-slide-in';
    notification.innerHTML = `
        <div class="card-custom rounded-lg shadow-lg border-l-4 border-red-500 p-4 bg-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">
                        ${message}
                    </p>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(notification);

    // Remover la notificación después de 3 segundos
    setTimeout(() => {
        notification.classList.replace('notification-slide-in', 'notification-slide-out');
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}

function resetUpload() {
    const fileInput = document.getElementById('document');
    if (fileInput) {
        fileInput.value = '';
    }

    const fileName = document.getElementById('fileName');
    if (fileName) {
        fileName.textContent = 'PDF o Imagen con QR (Máximo 5MB)';
    }

    const satDataContainer = document.getElementById('satDataContainer');
    if (satDataContainer) {
        satDataContainer.classList.add('hidden');
    }

    if (window.qrHandler) {
        window.qrHandler.reset();
    }

    showLoading(false);
}

// Funciones para el modal
function showSatModal() {
    const modal = document.getElementById('satDataModal');
    const modalContent = document.getElementById('satDataModalContent');
    
    if (modal && modalContent && window.qrHandler) {
        const result = window.qrHandler.showSatData();
        if (result.success) {
            modalContent.innerHTML = result.content;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        } else {
            showError('Error al mostrar los datos: ' + result.error);
        }
    }
}

function closeSatModal() {
    const modal = document.getElementById('satDataModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Cerrar modal al hacer clic fuera de él
document.addEventListener('click', function(event) {
    const modal = document.getElementById('satDataModal');
    const modalContent = modal?.querySelector('.bg-white');
    if (modal && event.target === modal && modalContent && !modalContent.contains(event.target)) {
        closeSatModal();
    }
});
</script>

<script type="module">
    import QRHandler from '/js/components/qr-handler.js';
    import QRReader from '/js/components/qr-reader.js';
    import SATValidator from '/js/validators/sat-validator.js';
    import SATScraper from '/js/scrapers/sat-scraper.js';
    
    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            console.log('Inicializando QRHandler...');
            
            // Crear e inicializar QRHandler
            const qrHandler = new QRHandler();
            await qrHandler.initialize(QRReader, SATValidator, SATScraper);

            // Configurar callbacks
            qrHandler.setOnDataScanned((data) => {
                console.log('Datos escaneados:', data);
                const satDataContainer = document.getElementById('satDataContainer');
                const satDataContent = document.getElementById('satDataContent');
                
                if (satDataContainer && satDataContent) {
                    const result = qrHandler.showSatData();
                    if (result.success) {
                        // Mostrar resumen en el contenedor
                        const resumen = generarResumen(data);
                        satDataContent.innerHTML = resumen;
                        satDataContainer.classList.remove('hidden');
                        showLoading(false);
                    } else {
                        showError('Error al mostrar los datos: ' + result.error);
                    }
                }
            });

            qrHandler.setOnError((error) => {
                console.error('Error en QRHandler:', error);
                showError(error);
                resetUpload();
            });

            // Asignar a window para acceso global
            window.qrHandler = qrHandler;
            console.log('QRHandler inicializado correctamente');

        } catch (error) {
            console.error('Error durante la inicialización:', error);
            showError('Error al inicializar el lector QR');
        }
    });

    // Función para generar un resumen de los datos
    function generarResumen(data) {
        if (!data || !data.details) return '';
        
        const details = data.details;
        return `
            <div class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                            ${details.tipoPersona === 'Moral' ? 
                                (details.razonSocial || 'Razón Social No Disponible') : 
                                (details.nombreCompleto || 'Nombre No Disponible')}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                RFC: ${details.rfc || 'No disponible'}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                ${details.tipoPersona || 'Tipo no especificado'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Evento change del input file
    document.getElementById('document').addEventListener('change', async (event) => {
        const file = event.target.files[0];
        if (!file) return;

        try {
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

            // Actualizar la etiqueta con el nombre del archivo
            const fileName = document.getElementById('fileName');
            if (fileName) {
                fileName.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            }

            showLoading(true);
            console.log('Iniciando procesamiento del archivo:', file.name);

            // Procesar el archivo con QRHandler
            if (!window.qrHandler) {
                throw new Error('El lector QR no está inicializado');
            }

            await window.qrHandler.handleFile(file);

        } catch (error) {
            console.error('Error detallado:', error);
            showError(error.message || 'Error al procesar el documento');
            resetUpload();
        }
    });
</script>

@endsection 