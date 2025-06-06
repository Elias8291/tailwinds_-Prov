class QRReader {
    constructor(validator = null, scraper = null) {
        this.html5QrcodeScanner = new Html5Qrcode("qrResult");
        this.canvas = document.getElementById('pdfCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.validator = validator;
        this.scraper = scraper;
        this.satData = null;
    }

    async handleFile(file) {
        const fileName = file.name;
        document.getElementById('fileName').textContent = fileName;
        const previewArea = document.getElementById('previewArea');
        const qrResult = document.getElementById('qrResult');
        previewArea.classList.remove('hidden');

        try {
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
        
        // Ocultar área de previsualización
        const previewArea = document.getElementById('previewArea');
        if (previewArea) {
            previewArea.classList.add('hidden');
        }
        
        // Resetear el texto del archivo
        const fileName = document.getElementById('fileName');
        if (fileName) {
            fileName.textContent = 'PDF o Imagen con QR (Máximo 5MB)';
        }
        this.satData = null;
    }

    async onValidQRFound(qrCode) {
        try {
            // Eliminar completamente el área de subida de PDF
            const uploadArea = document.getElementById('uploadArea');
            if (uploadArea) {
                uploadArea.remove();
            }
            
            // Eliminar el área de previsualización/información del QR
            const previewArea = document.getElementById('previewArea');
            if (previewArea) {
                previewArea.remove();
            }
            
            // Si hay un scraper, obtener los datos
            if (this.scraper) {
                this.satData = await this.scraper.scrapeData(qrCode);
                if (!this.satData) {
                    throw new Error('No se pudieron obtener los datos del SAT');
                }
                
                // Preparar el contenido del modal pero NO mostrarlo
                const modalContent = this.scraper.generateModalContent(this.satData);
                const satDataContent = document.getElementById('satDataContent');
                if (satDataContent) {
                    satDataContent.innerHTML = modalContent;
                }
                
                // Mostrar el botón de "Ver Datos"
                const verDatosBtn = document.getElementById('verDatosBtn');
                if (verDatosBtn) {
                    verDatosBtn.classList.remove('hidden');
                }
            }

            // Mostrar formulario de registro
            const registrationForm = document.getElementById('registrationForm');
            if (registrationForm) {
                registrationForm.classList.remove('hidden');
            }
            
            // Guardar URL del QR
            const qrUrlInput = document.getElementById('qrUrl');
            if (qrUrlInput) {
                qrUrlInput.value = qrCode;
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

export default QRReader; 