/**
 * QRHandler - Manejador de códigos QR del SAT
 * Versión sin console.log para mejor performance
 */
class QRHandler {
    constructor(config = {}) {
        this.lastScannedData = null;
        this.onDataScanned = null;
        this.onError = null;
        this.qrReader = null;
        this.validator = null;
        this.scraper = null;
        this.scannedData = null;
        this.isProcessing = false;
        this.config = config;
        this.init();
    }

    init() {
        // QRHandler inicializado
        try {
            this.setupEventListeners();
        } catch (error) {
            // Error durante la inicialización
        }
    }

    async initialize(QRReader, SATValidator, SATScraper) {
        try {
            // Verificar elementos necesarios
            const requiredElements = ['qrResult', 'pdfCanvas'];
            const missingElements = requiredElements.filter(id => !document.getElementById(id));
            
            if (missingElements.length > 0) {
                throw new Error(`Elementos faltantes: ${missingElements.join(', ')}`);
            }

            this.qrReader = new QRReader(SATValidator, SATScraper, this.config);
            this.validator = SATValidator;
            this.scraper = SATScraper;

            // Sobrescribir métodos del QRReader
            this._overrideMethods();
            
            return true;
        } catch (error) {
            if (this.onError) {
                this.onError(error.message);
            }
            return false;
        }
    }

    _overrideMethods() {
        const self = this;
        const originalHandleFile = this.qrReader.handleFile;

        this.qrReader.handleFile = async function(file) {
            try {
                const result = await originalHandleFile.call(this, file);

                if (result && result.success) {
                    try {
                        const scrapedData = await self.qrReader.getLastScrapedData();

                        if (scrapedData && scrapedData.details) {
                            self.lastScannedData = scrapedData;

                            // Notificar a los listeners
                            if (self.onDataScanned) {
                                self.onDataScanned(self.lastScannedData);
                            }

                            return { success: true, data: self.lastScannedData };
                        }
                    } catch (error) {
                        if (self.onError) {
                            self.onError(error.message);
                        }
                        return { success: false, error: error.message };
                    }
                }

                const errorMsg = result.error || 'Error al procesar el archivo';
                if (self.onError) {
                    self.onError(errorMsg);
                }
                return { success: false, error: errorMsg };
            } catch (error) {
                if (self.onError) {
                    self.onError(error.message);
                }
                return { success: false, error: error.message };
            }
        };

        this.qrReader.getLastScrapedData = async function() {
            return self.lastScannedData || null;
        };

        this.qrReader.showSatData = function() {
            try {
                if (!self.lastScannedData) {
                    return false;
                }

                const content = self.scraper.generateModalContent(self.lastScannedData);

                return { success: true, content: content };
            } catch (error) {
                return { success: false, error: error.message };
            }
        };

        this.qrReader.onValidQRFound = async function(url) {
            try {
                const scrapedData = await self.scraper.scrapeData(url);
                
                if (scrapedData && scrapedData.success && scrapedData.data) {
                    self.lastScannedData = scrapedData.data;
                    return { success: true, data: scrapedData.data };
                }
                
                return { success: false, error: scrapedData.error || 'Error al obtener datos del SAT' };
            } catch (error) {
                return { success: false, error: error.message };
            }
        };
    }

    setOnDataScanned(callback) {
        this.onDataScanned = callback;
    }

    setOnError(callback) {
        this.onError = callback;
    }

    async handleFile(file) {
        if (this.isProcessing) {
            return;
        }

        if (!this.qrReader) {
            throw new Error('QRHandler no ha sido inicializado');
        }

        try {
            this.isProcessing = true;
            const result = await this.qrReader.handleFile(file);
            return result;
        } catch (error) {
            
            if (this.onError) {
                this.onError(error.message);
            }
            return { success: false, error: error.message };
        } finally {
            this.isProcessing = false;
        }
    }

    showSatData() {
        try {
            if (!this.lastScannedData) {
                
                return { success: false, error: 'No hay datos disponibles' };
            }

            
            const content = this.scraper.generateModalContent(this.lastScannedData);
            
            // Actualizar contenido del modal
            const satDataContent = document.getElementById('satDataContent');
            if (satDataContent) {
                satDataContent.innerHTML = content;
            }

            return { success: true, content: content };
        } catch (error) {
            
            return { success: false, error: error.message };
        }
    }

    getLastScannedData() {
        return this.lastScannedData;
    }

    reset() {
        this.lastScannedData = null;
    }

    getRfcFromData() {
        if (this.scannedData && this.scannedData.details) {
            return this.scannedData.details.rfc || null;
        }
        return null;
    }

    generateSatDataHtml(details) {
        return `
            <div class="space-y-6">
                <!-- Información General -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información General</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">RFC</p>
                            <p class="text-base font-medium text-gray-900">${details.rfc || 'No disponible'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tipo de Persona</p>
                            <p class="text-base font-medium text-gray-900">${details.tipoPersona || 'No especificado'}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">${details.tipoPersona === 'Moral' ? 'Razón Social' : 'Nombre Completo'}</p>
                            <p class="text-base font-medium text-gray-900">
                                ${details.tipoPersona === 'Moral' ? 
                                    (details.razonSocial || 'No disponible') : 
                                    (details.nombreCompleto || 'No disponible')}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Domicilio Fiscal -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Domicilio Fiscal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Dirección</p>
                            <p class="text-base font-medium text-gray-900">${details.direccion || 'No disponible'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Código Postal</p>
                            <p class="text-base font-medium text-gray-900">${details.codigoPostal || 'No disponible'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Entidad Federativa</p>
                            <p class="text-base font-medium text-gray-900">${details.entidadFederativa || 'No disponible'}</p>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Adicional</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Estatus</p>
                            <p class="text-base font-medium text-gray-900">${details.estatus || 'No disponible'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Fecha de Registro</p>
                            <p class="text-base font-medium text-gray-900">${details.fechaRegistro || 'No disponible'}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// Hacer la clase disponible globalmente para navegadores legacy
if (typeof window !== 'undefined') {
    window.QRHandler = QRHandler;
} 
