import QRHandler from '../components/qr-handler.js';
import QRReader from '../components/qr-reader.js';
import SATValidator from '../validators/sat-validator.js';
import SATScraper from '../scrapers/sat-scraper.js';

export class SATHandler {
    constructor(config = {}) {
        this.qrHandler = null;
        this.isProcessing = false;
        this.lastScannedData = null;
        this.config = {
            fileNameElement: 'qr-file-name',
            previewAreaElement: 'qr-preview',
            uploadAreaElement: 'qr-upload-area',
            verDatosBtnElement: 'verDatosBtn',
            ...config
        };
    }

    async initialize() {
        try {
            console.log('Inicializando SATHandler...');
            
            // Crear e inicializar QRHandler con configuración
            this.qrHandler = new QRHandler(this.config);
            const initialized = await this.qrHandler.initialize(QRReader, SATValidator, SATScraper);
            
            if (!initialized) {
                throw new Error('Error al inicializar QRHandler');
            }

            // Configurar callbacks
            this.qrHandler.setOnDataScanned((data) => {
                console.log('Datos escaneados:', data);
                this.lastScannedData = data;
                this.isProcessing = false;
                this.hideLoading();
                this.showModal();
            });

            this.qrHandler.setOnError((error) => {
                console.error('Error en QRHandler:', error);
                this.isProcessing = false;
                this.hideLoading();
                this.showError(error);
                this.resetUpload();
            });

            console.log('SATHandler inicializado correctamente');
            return true;
        } catch (error) {
            console.error('Error durante la inicialización:', error);
            this.showError('Error al inicializar: ' + error.message);
            return false;
        }
    }

    async handleFile(file) {
        if (!file || this.isProcessing) return;

        try {
            this.isProcessing = true;
            this.showLoading();

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
            await this.qrHandler.handleFile(file);

        } catch (error) {
            console.error('Error al procesar archivo:', error);
            this.showError(error.message || 'Error al procesar el documento');
            this.resetUpload();
        } finally {
            this.isProcessing = false;
            this.hideLoading();
        }
    }

    showModal() {
        if (this.lastScannedData) {
            const modal = document.getElementById('satDataModal');
            const satDataContent = document.getElementById('satDataContent');
            
            if (modal && satDataContent) {
                // Generar y mostrar el contenido
                const content = this.generateSatDataHtml(this.lastScannedData);
                satDataContent.innerHTML = content;
                
                // Mostrar el modal
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }
    }

    closeModal() {
        const modal = document.getElementById('satDataModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
            
            // Mostrar el botón flotante
            const verSatModalBtn = document.getElementById('verSatModalBtn');
            if (verSatModalBtn) {
                verSatModalBtn.classList.remove('hidden');
            }
        }
    }

    showLoading() {
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.style.display = 'flex';
        }
    }

    hideLoading() {
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
        }
    }

    showError(message) {
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

    resetUpload() {
        const fileInput = document.getElementById('documentInput');
        if (fileInput) {
            fileInput.value = '';
        }
    }

    generateSatDataHtml(data) {
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
} 