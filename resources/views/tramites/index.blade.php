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
        <!-- Área de procesamiento de documentos -->
        <div id="uploadArea" class="transition-all duration-300 ease-in-out">
            <div class="mt-6">
                <label for="document" class="block text-sm md:text-base font-medium text-gray-700 mb-3">
                    <span class="block md:inline">Constancia de Situación Fiscal</span>
                    <span class="text-sm text-gray-500 block md:inline md:ml-1">(PDF o Imagen)</span>
                </label>
                <div class="relative">
                    <input type="file" id="document" name="document" accept=".pdf,.png,.jpg,.jpeg" required
                           class="hidden">
                    <label for="document" class="group flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-[#B4325E]/20 hover:border-[#B4325E] rounded-xl transition-all duration-300 cursor-pointer bg-[#B4325E]/5 hover:bg-[#B4325E]/10">
                        <div class="flex flex-col md:flex-row items-center space-y-3 md:space-y-0 md:space-x-4 px-4">
                            <!-- Icono -->
                            <div class="transform group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-[#B4325E]/70 group-hover:text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <!-- Texto -->
                            <div class="text-center md:text-left">
                                <p class="text-[#B4325E]/70 group-hover:text-[#B4325E] font-medium mb-1 transition-colors duration-300">
                                    Haga clic para seleccionar archivo
                                </p>
                                <p class="text-sm text-gray-500" id="fileName">
                                    PDF o Imagen con QR (Máximo 5MB)
                                </p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Área de previsualización -->
            <div id="previewArea" class="hidden">
                <div class="hidden">
                    <div id="qrResult"></div>
                    <canvas id="pdfCanvas"></canvas>
                </div>
            </div>

            <!-- Botón Ver Datos del SAT -->
            <div class="mt-4">
                <button type="button" 
                        id="verDatosBtn"
                        onclick="qrReader.showSatData()"
                        class="hidden inline-flex items-center text-sm bg-white hover:bg-[#B4325E]/5 text-[#B4325E] font-medium py-2 px-3 rounded-lg transition-all duration-300 shadow-sm hover:shadow border border-[#B4325E]/20 hover:border-[#B4325E]/40">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver Datos del SAT
                </button>
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

        <!-- Modal para mostrar datos del SAT -->
        <div id="satDataModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4 overflow-y-auto">
            <div class="relative w-full max-w-2xl mx-auto">
                <!-- Modal content -->
                <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <!-- Modal header -->
                    <div class="px-6 py-4 bg-gradient-to-br from-[#B4325E] to-[#93264B] border-b border-[#B4325E]/10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-white/10 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-white">
                                    Datos del SAT
                                </h3>
                            </div>
                            <button onclick="closeSatModal()" class="text-white/80 hover:text-white transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal body -->
                    <div class="p-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <div id="satDataContent" class="space-y-6">
                            <!-- Los datos del SAT se insertarán aquí -->
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                        <div class="flex justify-end space-x-3">
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
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js"></script>

<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js';

function showLoading(show = true) {
    const loadingIndicator = document.getElementById('loading-indicator');
    if (loadingIndicator) {
        loadingIndicator.classList.toggle('hidden', !show);
    }
}

function showError(message) {
    showLoading(false);
    alert(message);
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

        // Procesar el archivo
        if (!window.qrReader) {
            console.error('QRReader no inicializado');
            throw new Error('El lector QR no está inicializado');
        }

        console.log('QRReader encontrado, procesando archivo...');
        const result = await window.qrReader.handleFile(file);
        console.log('Datos extraídos del QR:', result);
        
        if (result && result.success) {
            console.log('Procesamiento exitoso, datos obtenidos:', result.data);
            const verDatosBtn = document.getElementById('verDatosBtn');
            if (verDatosBtn) {
                verDatosBtn.classList.remove('hidden');
                console.log('Botón "Ver Datos" mostrado');
            }
            // Mostrar datos automáticamente
            console.log('Intentando mostrar modal con datos...');
            window.qrReader.showSatData();
        } else {
            console.error('Fallo en el procesamiento:', result);
            throw new Error('No se pudieron extraer los datos del documento');
        }

    } catch (error) {
        console.error('Error detallado:', error);
        showError(error.message || 'Error al procesar el documento');
        // Resetear el input file
        event.target.value = '';
        const fileName = document.getElementById('fileName');
        if (fileName) {
            fileName.textContent = 'PDF o Imagen con QR (Máximo 5MB)';
        }
        // Ocultar botón de ver datos
        const verDatosBtn = document.getElementById('verDatosBtn');
        if (verDatosBtn) {
            verDatosBtn.classList.add('hidden');
        }
    } finally {
        showLoading(false);
    }
});

function showSatModal() {
    const modal = document.getElementById('satDataModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // Prevenir scroll del body
        document.body.style.overflow = 'hidden';
    }
}

function closeSatModal() {
    const modal = document.getElementById('satDataModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        // Restaurar scroll del body
        document.body.style.overflow = '';
    }
}
</script>

<script type="module">
    import QRReader from '/js/components/qr-reader.js';
    import SATValidator from '/js/validators/sat-validator.js';
    import SATScraper from '/js/scrapers/sat-scraper.js';
    
    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        try {
            console.log('Inicializando QRReader...');
            // Inicializar QRReader
            window.qrReader = new QRReader(SATValidator, SATScraper);
            console.log('QRReader inicializado correctamente');

            // Asegurarse de que el modal esté configurado correctamente
            const modal = document.getElementById('satDataModal');
            if (!modal) {
                console.error('Modal no encontrado en el DOM');
            }

            // Exponer funciones de modal globalmente
            window.showSatModal = function() {
                console.log('Mostrando modal SAT');
                const modal = document.getElementById('satDataModal');
                if (modal) {
                    modal.style.display = 'flex';
                    modal.classList.remove('hidden');
                } else {
                    console.error('Modal no encontrado al intentar mostrarlo');
                }
            };

            window.closeSatModal = function() {
                console.log('Cerrando modal SAT');
                const modal = document.getElementById('satDataModal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }
            };

            // Sobrescribir la función showSatData del QRReader
            const originalShowSatData = window.qrReader.showSatData;
            window.qrReader.showSatData = function() {
                console.log('Llamada a showSatData');
                try {
                    const result = originalShowSatData.call(this);
                    console.log('Resultado de showSatData:', result);
                    window.showSatModal();
                    return result;
                } catch (error) {
                    console.error('Error en showSatData:', error);
                    throw error;
                }
            };
        } catch (error) {
            console.error('Error durante la inicialización:', error);
        }
    });
</script>

@endsection 