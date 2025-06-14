@extends('layouts.app')

@section('content')
<!-- Modal para mensajes de éxito/error -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <div id="messageModalContent">
            <!-- El contenido se insertará dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal para mostrar datos del SAT -->
<div id="satDataModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-[#9d2449] to-[#7a1d37]">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">Datos de la Constancia Fiscal</h3>
                <button onclick="closeSatModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 140px);">
            <div id="satDataContent" class="space-y-6"></div>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            <div class="flex justify-end space-x-3">
                <button onclick="closeSatModal()" 
                        class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 font-medium rounded-lg border border-gray-300 transition-colors">
                    Cerrar
                </button>
                <button onclick="confirmarDatos()" 
                        class="px-4 py-2 bg-gradient-to-r from-[#9d2449] to-[#7a1d37] text-white font-medium rounded-lg hover:shadow-lg transition-all">
                    Confirmar y Continuar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="min-h-screen py-8">
    <div class="max-w-md mx-auto px-4">
        <!-- Header compacto -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-2xl mb-4 shadow-lg">
                <i class="fas fa-receipt text-xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                Constancia Fiscal
            </h1>
            <p class="text-sm text-gray-600">
                <span class="font-medium text-[#9d2449]">{{ ucfirst($tipoTramite) }}</span> • {{ $solicitante->rfc }}
            </p>
        </div>

        <!-- Formulario compacto -->
        <div class="space-y-4">
            <!-- Campos ocultos -->
            <input type="hidden" id="satRfc" name="sat_rfc">
            <input type="hidden" id="satNombre" name="sat_nombre">
            <input type="hidden" id="satTipoPersona" name="sat_tipo_persona">
            <input type="hidden" id="satCurp" name="sat_curp">

            <!-- Área de carga -->
            <div id="upload-area" class="mb-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Cargar Constancia</h3>
                        <p class="text-sm text-gray-500">Seleccione su constancia de situación fiscal</p>
                    </div>
                    
                    <div class="relative">
                        <input type="file" 
                               id="constancia_fiscal" 
                               name="constancia_fiscal"
                               accept=".pdf,.png,.jpg,.jpeg"
                               class="hidden">
                        
                        <label for="constancia_fiscal" 
                               class="group flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 hover:border-[#9d2449] rounded-xl transition-all cursor-pointer hover:bg-gray-50">
                            
                            <div class="flex flex-col items-center space-y-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-all">
                                    <i class="fas fa-cloud-upload-alt text-white text-lg"></i>
                                </div>
                                <div class="text-center">
                                    <h4 class="text-base font-medium text-gray-700 group-hover:text-[#9d2449] transition-colors">
                                        Seleccionar archivo
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        PDF, PNG, JPG (Máx. 10MB)
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Estado procesado -->
            <div id="file-processed" class="hidden mb-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-green-800">Constancia Validada</h4>
                                <p class="text-xs text-green-600" id="processed-file-name"></p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" 
                                    onclick="showSatModal()"
                                    class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs font-medium transition-colors">
                                Ver Datos
                            </button>
                            <button type="button" 
                                    onclick="resetUpload()"
                                    class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-xs font-medium transition-colors">
                                Cambiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div id="loading-indicator" class="hidden mb-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin w-6 h-6 border-3 border-blue-500 border-t-transparent rounded-full"></div>
                        <div>
                            <h4 class="font-medium text-blue-800">Procesando...</h4>
                            <p class="text-xs text-blue-600">Validando RFC de la constancia</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón continuar -->
            <div class="flex justify-center">
                <button type="button" 
                        id="continue-button"
                        onclick="continueToForm()"
                        disabled
                        class="inline-flex items-center justify-center w-full bg-gradient-to-r from-[#9d2449] to-[#7a1d37] text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Continuar al Formulario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Elementos necesarios para QRHandler (ocultos) -->
<div id="qrResult" class="hidden"></div>
<canvas id="pdfCanvas" class="hidden"></canvas>

<!-- Scripts -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js"></script>
<script src="/js/scrapers/sat-scraper.js"></script>
<script src="/js/validators/sat-validator.js"></script>
<script src="/js/components/qr-reader.js"></script>
<script src="/js/components/qr-handler.js"></script>

<script>
    // Configurar PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js';

    // Variables globales
    let documentProcessed = false;
    let satDataExtracted = null;
    let qrHandler = null;

    // Configuración específica para constancia-fiscal.blade.php
    const config = {
        fileNameElement: 'processed-file-name',
        previewAreaElement: 'file-processed',
        uploadAreaElement: 'upload-area',
        loadingElement: 'loading-indicator',
        qrUrlElement: 'satRfc'
    };

    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            console.log('Inicializando procesador de constancia fiscal...');
            
            // Verificar que las clases estén disponibles
            if (typeof QRHandler === 'undefined' || typeof QRReader === 'undefined' || 
                typeof SATValidator === 'undefined' || typeof SATScraper === 'undefined') {
                throw new Error('Las clases necesarias no están disponibles');
            }
            
            // Crear e inicializar QRHandler con configuración
            qrHandler = new QRHandler(config);
            const initialized = await qrHandler.initialize(QRReader, SATValidator, SATScraper);
            
            if (!initialized) {
                throw new Error('No se pudo inicializar el QRHandler');
            }

            // Configurar callbacks
            qrHandler.setOnDataScanned((data) => {
                console.log('Datos escaneados de la constancia:', data);
                
                // Validar RFC
                const rfcTramite = '{{ $solicitante->rfc }}';
                const rfcConstancia = data.details?.rfc || '';
                
                if (!rfcConstancia) {
                    showMessageModal('Error de validación', 'No se pudo extraer el RFC de la constancia', 'error');
                    resetUpload();
                    return;
                }
                
                if (rfcConstancia.toUpperCase() !== rfcTramite.toUpperCase()) {
                    showMessageModal('RFC no coincide', `El RFC de la constancia (${rfcConstancia}) no coincide con el RFC del trámite (${rfcTramite})`, 'error');
                    resetUpload();
                    return;
                }
                
                // Guardar datos extraídos
                satDataExtracted = data;
                documentProcessed = true;
                
                // Llenar campos ocultos con datos del SAT
                if (data.details) {
                    document.getElementById('satRfc').value = data.details.rfc || '';
                    document.getElementById('satNombre').value = data.details.razonSocial || data.details.nombreCompleto || '';
                    document.getElementById('satTipoPersona').value = data.details.tipoPersona || '';
                    document.getElementById('satCurp').value = data.details.curp || '';
                }
                
                // Ocultar área de carga y loading
                document.getElementById('upload-area').classList.add('hidden');
                document.getElementById('loading-indicator').classList.add('hidden');
                
                // Mostrar área de archivo procesado
                const processedArea = document.getElementById('file-processed');
                processedArea.classList.remove('hidden');
                
                // Habilitar botón de continuar
                const continueButton = document.getElementById('continue-button');
                continueButton.disabled = false;
                continueButton.classList.remove('opacity-50', 'cursor-not-allowed');
                
                // Mostrar modal de éxito
                showMessageModal('¡Constancia validada correctamente!', 'RFC verificado exitosamente', 'success');
            });

            qrHandler.setOnError((error) => {
                console.error('Error en QRHandler:', error);
                showMessageModal('Error al procesar', error, 'error');
                resetUpload();
            });

            // Asignar a window para acceso global
            window.qrHandler = qrHandler;
            console.log('Procesador inicializado correctamente');

        } catch (error) {
            console.error('Error durante la inicialización:', error);
            showMessageModal('Error de inicialización', 'No se pudo inicializar el procesador: ' + error.message, 'error');
        }
    });

    // Evento change del input file
    document.getElementById('constancia_fiscal').addEventListener('change', async (event) => {
        const file = event.target.files[0];
        if (!file) return;

        try {
            // Validar el tipo de archivo
            const isPDF = file.type === 'application/pdf';
            const isImage = file.type.startsWith('image/');
            
            if (!isPDF && !isImage) {
                throw new Error('El archivo debe ser un PDF o una imagen (JPG, PNG).');
            }

            // Validar el tamaño del archivo (10MB)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                throw new Error('El archivo no debe exceder los 10MB.');
            }

            // Actualizar nombre de archivo procesado
            const processedFileName = document.getElementById('processed-file-name');
            if (processedFileName) {
                processedFileName.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            }

            // Mostrar loading
            document.getElementById('upload-area').classList.add('hidden');
            document.getElementById('loading-indicator').classList.remove('hidden');
            
            console.log('Iniciando procesamiento del archivo:', file.name);

            // Procesar el archivo con QRHandler
            if (!qrHandler) {
                throw new Error('El procesador no está inicializado');
            }

            await qrHandler.handleFile(file);

        } catch (error) {
            console.error('Error detallado:', error);
            showMessageModal('Error al procesar', error.message || 'Error al procesar el documento', 'error');
            resetUpload();
        }
    });

    // Función para mostrar el modal con datos del SAT
    window.showSatModal = function() {
        if (!satDataExtracted) {
            showMessageModal('Sin datos', 'No hay datos disponibles para mostrar', 'error');
            return;
        }

        const modal = document.getElementById('satDataModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Generar contenido del modal usando SATScraper
            const content = SATScraper.generateModalContent(satDataExtracted);
            const satDataContent = document.getElementById('satDataContent');
            if (satDataContent) {
                satDataContent.innerHTML = content;
            }
        }
    };

    // Función para cerrar el modal
    window.closeSatModal = function() {
        const modal = document.getElementById('satDataModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // Función para confirmar datos y continuar
    window.confirmarDatos = function() {
        window.closeSatModal();
        continueToForm();
    };

    // Función para continuar al formulario
    window.continueToForm = function() {
        if (!documentProcessed || !satDataExtracted) {
            showMessageModal('Acción requerida', 'Debe procesar una constancia fiscal antes de continuar', 'error');
            return;
        }

        // Mostrar loading en el botón
        const continueButton = document.getElementById('continue-button');
        const originalText = continueButton.innerHTML;
        continueButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
        continueButton.disabled = true;

        // Extraer código postal de los datos del SAT
        let codigoPostalSat = '';
        try {
            if (satDataExtracted && satDataExtracted.domicilio) {
                // Buscar el código postal en diferentes posibles ubicaciones
                if (satDataExtracted.domicilio.codigoPostal) {
                    codigoPostalSat = satDataExtracted.domicilio.codigoPostal;
                } else if (satDataExtracted.domicilio.cp) {
                    codigoPostalSat = satDataExtracted.domicilio.cp;
                } else if (satDataExtracted.domicilio['Código Postal']) {
                    codigoPostalSat = satDataExtracted.domicilio['Código Postal'];
                }
                
                // Limpiar el código postal (solo números)
                codigoPostalSat = codigoPostalSat.toString().replace(/\D/g, '');
                console.log('Código postal extraído del SAT:', codigoPostalSat);
            }
        } catch (error) {
            console.error('Error al extraer código postal del SAT:', error);
        }

        // Crear formulario para enviar datos y continuar al create
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('tramites.solicitante.subir-constancia-fiscal') }}';
        
        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Tramite ID
        const tramiteId = document.createElement('input');
        tramiteId.type = 'hidden';
        tramiteId.name = 'tramite_id';
        tramiteId.value = '{{ $tramite->id }}';
        form.appendChild(tramiteId);
        
        // Tipo tramite
        const tipoTramite = document.createElement('input');
        tipoTramite.type = 'hidden';
        tipoTramite.name = 'tipo_tramite';
        tipoTramite.value = '{{ $tipoTramite }}';
        form.appendChild(tipoTramite);
        
        // Datos del SAT como JSON
        const satData = document.createElement('input');
        satData.type = 'hidden';
        satData.name = 'sat_data';
        satData.value = JSON.stringify(satDataExtracted);
        form.appendChild(satData);
        
        // Código postal del SAT
        if (codigoPostalSat) {
            const cpSat = document.createElement('input');
            cpSat.type = 'hidden';
            cpSat.name = 'codigo_postal_sat';
            cpSat.value = codigoPostalSat;
            form.appendChild(cpSat);
        }
        
        // Agregar al DOM y enviar
        document.body.appendChild(form);
        form.submit();
    };

    // Función para resetear la carga
    window.resetUpload = function() {
        // Resetear variables
        documentProcessed = false;
        satDataExtracted = null;
        
        // Limpiar input
        document.getElementById('constancia_fiscal').value = '';
        
        // Mostrar área de carga
        document.getElementById('upload-area').classList.remove('hidden');
        
        // Ocultar otras áreas
        document.getElementById('file-processed').classList.add('hidden');
        document.getElementById('loading-indicator').classList.add('hidden');
        
        // Deshabilitar botón
        const continueButton = document.getElementById('continue-button');
        continueButton.disabled = true;
        continueButton.classList.add('opacity-50', 'cursor-not-allowed');
        
        // Limpiar campos ocultos
        document.getElementById('satRfc').value = '';
        document.getElementById('satNombre').value = '';
        document.getElementById('satTipoPersona').value = '';
        document.getElementById('satCurp').value = '';
        
        if (qrHandler) {
            qrHandler.reset();
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
</script>

<script>
    // Configurar PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js';

    // Función para mostrar modal de mensajes
    function showMessageModal(title, message, type = 'success') {
        const modal = document.getElementById('messageModal');
        const content = document.getElementById('messageModalContent');
        
        const isSuccess = type === 'success';
        const bgColor = isSuccess ? 'from-green-500 to-emerald-500' : 'from-red-500 to-rose-500';
        const icon = isSuccess ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
        const iconBg = isSuccess ? 'bg-green-100' : 'bg-red-100';
        const iconColor = isSuccess ? 'text-green-600' : 'text-red-600';
        
        content.innerHTML = `
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 ${iconBg} rounded-full flex items-center justify-center">
                        <i class="${icon} ${iconColor} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                        <p class="text-sm text-gray-600 mt-1">${message}</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button onclick="closeMessageModal()" 
                            class="px-4 py-2 bg-gradient-to-r ${bgColor} text-white font-medium rounded-lg hover:shadow-lg transition-all">
                        Entendido
                    </button>
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Auto cerrar después de 4 segundos para mensajes de éxito
        if (isSuccess) {
            setTimeout(() => {
                closeMessageModal();
            }, 4000);
        }
    }

    // Función para cerrar modal de mensajes
    function closeMessageModal() {
        const modal = document.getElementById('messageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>



@endsection 