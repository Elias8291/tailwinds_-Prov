class QRHandler {
    constructor() {
        this.lastScannedData = null;
        this.onDataScanned = null;
        this.onError = null;
        this.qrReader = null;
        this.validator = null;
        this.scraper = null;
    }

    async initialize(QRReader, SATValidator, SATScraper) {
        try {
            console.log('Inicializando QRHandler...');
            this.qrReader = new QRReader(SATValidator, SATScraper);
            this.validator = SATValidator;
            this.scraper = SATScraper;

            // Sobrescribir métodos del QRReader
            this._overrideMethods();
            
            console.log('QRHandler inicializado correctamente');
            return true;
        } catch (error) {
            console.error('Error al inicializar QRHandler:', error);
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
        if (!this.qrReader) {
            throw new Error('QRHandler no ha sido inicializado');
        }
        return await this.qrReader.handleFile(file);
    }

    showSatData() {
        if (!this.qrReader) {
            throw new Error('QRHandler no ha sido inicializado');
        }
        return this.qrReader.showSatData();
    }

    getLastScannedData() {
        return this.lastScannedData;
    }

    reset() {
        this.lastScannedData = null;
    }
}

export default QRHandler; 