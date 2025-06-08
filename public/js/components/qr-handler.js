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
    }

    async initialize(QRReader, SATValidator, SATScraper) {
        try {
            console.log('Inicializando QRHandler...');
            
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
            
            console.log('QRHandler inicializado correctamente');
            return true;
        } catch (error) {
            console.error('Error al inicializar QRHandler:', error);
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
                console.log('Procesando archivo:', file.name);
                const result = await originalHandleFile.call(this, file);
                console.log('Resultado inicial de handleFile:', result);

                if (result && result.success) {
                    try {
                        const scrapedData = await self.qrReader.getLastScrapedData();
                        console.log('Datos obtenidos del scraper:', scrapedData);

                        if (scrapedData && scrapedData.details) {
                            self.lastScannedData = scrapedData;
                            console.log('Datos almacenados correctamente:', self.lastScannedData);

                            // Notificar a los listeners
                            if (self.onDataScanned) {
                                self.onDataScanned(self.lastScannedData);
                            }

                            return { success: true, data: self.lastScannedData };
                        }
                    } catch (error) {
                        console.error('Error al obtener datos del scraper:', error);
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
                console.error('Error en handleFile:', error);
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
                    console.error('No hay datos disponibles');
                    return false;
                }

                console.log('Generando contenido con datos:', self.lastScannedData);
                const content = self.scraper.generateModalContent(self.lastScannedData);

                return { success: true, content: content };
            } catch (error) {
                console.error('Error en showSatData:', error);
                return { success: false, error: error.message };
            }
        };

        this.qrReader.onValidQRFound = async function(url) {
            try {
                console.log('QR válido encontrado:', url);
                const scrapedData = await self.scraper.scrapeData(url);
                
                if (scrapedData && scrapedData.success && scrapedData.data) {
                    self.lastScannedData = scrapedData.data;
                    return { success: true, data: scrapedData.data };
                }
                
                return { success: false, error: scrapedData.error || 'Error al obtener datos del SAT' };
            } catch (error) {
                console.error('Error en onValidQRFound:', error);
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
            console.log('Ya hay un archivo en proceso');
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
            console.error('Error en handleFile:', error);
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
                console.error('No hay datos disponibles');
                return { success: false, error: 'No hay datos disponibles' };
            }

            console.log('Generando contenido con datos:', this.lastScannedData);
            const content = this.scraper.generateModalContent(this.lastScannedData);
            
            // Actualizar contenido del modal
            const satDataContent = document.getElementById('satDataContent');
            if (satDataContent) {
                satDataContent.innerHTML = content;
            }

            return { success: true, content: content };
        } catch (error) {
            console.error('Error en showSatData:', error);
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

export default QRHandler; 