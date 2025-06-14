class QRReader {
    constructor(validator = null, scraper = null, config = {}) {
        this.html5QrcodeScanner = new Html5Qrcode("qrResult");
        this.canvas = document.getElementById('pdfCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.validator = validator;
        this.scraper = scraper;
        this.satData = null;
        this.config = {
            fileNameElement: 'fileName',
            previewAreaElement: 'previewArea',
            uploadAreaElement: 'uploadArea',
            registrationFormElement: 'registrationForm',
            verDatosBtnElement: 'verDatosBtn',
            qrUrlElement: 'qrUrl',
            ...config
        };
    }

    async handleFile(file) {
        try {
            // Actualizar UI de manera segura
            this.updateUIElement(this.config.fileNameElement, file.name);
            this.showElement(this.config.previewAreaElement);

            const qrResult = document.getElementById('qrResult');
            if (!qrResult) {
                throw new Error('Elemento QR no encontrado');
            }

            if (file.type === 'application/pdf') {
                await this.processPDF(file, qrResult);
            } else {
                await this.processImage(file, qrResult);
            }
            return { success: true };
        } catch (error) {
            this.showError(error.message);
            this.resetUI();
            return { success: false, error: error.message };
        }
    }

    // Método de utilidad para actualizar elementos UI de manera segura
    updateUIElement(elementId, text) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = text;
        }
    }

    // Método para mostrar/ocultar elementos
    showElement(elementId, show = true) {
        const element = document.getElementById(elementId);
        if (element) {
            if (show) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        }
    }

    async processPDF(file, resultElement) {
        resultElement.textContent = 'Procesando PDF...';
        
        try {
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument(arrayBuffer).promise;
            
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                const page = await pdf.getPage(pageNum);
                const viewport = page.getViewport({ scale: 1.5 });
                
                this.canvas.width = viewport.width;
                this.canvas.height = viewport.height;

                await page.render({
                    canvasContext: this.ctx,
                    viewport: viewport
                }).promise;

                try {
                    const qrCode = await this.scanCanvasForQR(this.canvas);
                    if (await this.validateQR(qrCode)) {
                        await this.onValidQRFound(qrCode);
                        return;
                    }
                } catch (error) {
                    if (pageNum === pdf.numPages) {
                        throw new Error('No se encontró un código QR válido en el documento');
                    }
                    continue;
                }
            }
            throw new Error('No se encontró un código QR válido en el documento');
        } catch (error) {
            throw new Error('Error al procesar el PDF: ' + error.message);
        }
    }

    async processImage(file, resultElement) {
        try {
            const qrCode = await this.html5QrcodeScanner.scanFile(file, true);
            if (await this.validateQR(qrCode)) {
                await this.onValidQRFound(qrCode);
            } else {
                throw new Error(this.validator ? this.validator.getErrorMessage() : 'Código QR inválido');
            }
        } catch (error) {
            throw new Error('No se pudo detectar un código QR válido');
        }
    }

    async validateQR(qrCode) {
        if (this.validator) {
            return this.validator.validate(qrCode);
        }
        return true;
    }

    async scanCanvasForQR(canvas) {
        return new Promise((resolve, reject) => {
            const imageData = canvas.toDataURL('image/jpeg');
            const image = new Image();
            image.src = imageData;
            image.onload = async () => {
                try {
                    const result = await this.html5QrcodeScanner.scanFile(
                        this.dataURLtoFile(imageData, 'page.jpg'),
                        true
                    );
                    resolve(result);
                } catch (error) {
                    reject(error);
                }
            };
            image.onerror = reject;
        });
    }

    dataURLtoFile(dataurl, filename) {
        let arr = dataurl.split(','),
            mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]),
            n = bstr.length,
            u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, {type:mime});
    }

    showError(message) {
        const errorMessage = document.getElementById('errorMessage');
        if (errorMessage) {
            errorMessage.textContent = message;
            if (typeof window.showErrorModal === 'function') {
                window.showErrorModal();
            }
        }
    }

    resetUI() {
        // Limpiar el canvas
        if (this.ctx) {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }
        
        // Resetear elementos según configuración
        this.updateUIElement(this.config.fileNameElement, 'PDF o Imagen con QR (Máximo 5MB)');
        this.showElement(this.config.previewAreaElement, false);
        
        this.satData = null;
    }

    async onValidQRFound(qrCode) {
        try {
            // Ocultar áreas según configuración
            this.showElement(this.config.uploadAreaElement, false);
            this.showElement(this.config.previewAreaElement, false);
            
            // Si hay un scraper, obtener los datos
            if (this.scraper) {
                const response = await this.scraper.scrapeData(qrCode);
                if (!response || !response.success) {
                    throw new Error(response.error || 'No se pudieron obtener los datos del SAT');
                }
                
                this.satData = response.data;
                
                // Mostrar el botón de "Ver Datos" si existe
                this.showElement(this.config.verDatosBtnElement, true);
                
                // Mostrar formulario de registro si existe
                this.showElement(this.config.registrationFormElement, true);
                
                // Guardar URL del QR si el elemento existe
                const qrUrlInput = document.getElementById(this.config.qrUrlElement);
                if (qrUrlInput) {
                    qrUrlInput.value = qrCode;
                }

                return { success: true, data: this.satData };
            }
        } catch (error) {
            throw new Error('Error al procesar los datos: ' + error.message);
        }
    }

    showSatData() {
        if (this.satData && typeof window.showSatDataModal === 'function') {
            window.showSatDataModal();
        }
    }
}

// Hacer la clase disponible globalmente para navegadores legacy
if (typeof window !== 'undefined') {
    window.QRReader = QRReader;
} 