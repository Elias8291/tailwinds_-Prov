/**
 * Componente para manejar la visualización de documentos PDF
 * Controla que solo se muestre un documento a la vez por sección
 */

class DocumentViewer {
    constructor() {
        this.currentDocument = null;
        this.currentSection = null;
        this.openSections = new Set(); // Track sections with open content
        this.originalContent = new Map(); // Store original content for each section
        this.selectedDocuments = []; // For comparison mode
        this.comparisonMode = false;
        
        this.init();
    }

    init() {
        // DocumentViewer inicializado
    }

    /**
     * Muestra un documento en la sección especificada
     * @param {string} seccion - ID de la sección
     * @param {string} rutaArchivo - Ruta del archivo PDF
     * @param {string} nombreDocumento - Nombre del documento
     */
    showDocument(seccion, rutaArchivo, nombreDocumento) {
        // Documento abierto
        
        // Verificar si es la misma sección
        if (this.currentDocument === seccion) {
            return; // Ya está abierto
        }

        // Cerrar otras secciones primero
        this.closeOtherSections(seccion);

        const contenedor = document.getElementById('contenido-' + seccion);
        if (!contenedor) {
            return;
        }

        // Guardar contenido original si no existe
        if (!this.originalContent.has(seccion)) {
            this.originalContent.set(seccion, contenedor.innerHTML);
        }

        // Crear el layout dividido
        this.createSplitLayout(contenedor, seccion, rutaArchivo, nombreDocumento);
        
        // Marcar como documento actual
        this.currentDocument = seccion;
    }

    /**
     * Crea el layout dividido para mostrar formulario y documento
     * @param {Element} contenedor - Contenedor de la sección
     * @param {string} seccion - ID de la sección
     * @param {string} rutaArchivo - Ruta del archivo
     * @param {string} nombreDocumento - Nombre del documento
     */
    createSplitLayout(contenedor, seccion, rutaArchivo, nombreDocumento) {
        const originalContent = this.originalContent.get(seccion);
        
        const splitLayout = document.createElement('div');
        splitLayout.className = 'grid grid-cols-1 xl:grid-cols-2 gap-6';
        splitLayout.setAttribute('data-document-layout', 'true');
        
        splitLayout.innerHTML = `
            <div class="space-y-6">
                ${originalContent}
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-6 border border-gray-100 sticky top-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-file-pdf text-red-500 mr-2 text-xl"></i>
                        <span class="text-lg font-semibold text-gray-700 truncate">${nombreDocumento}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="documentViewer.openInNewTab('${rutaArchivo}')" 
                                class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg text-sm transition-colors tooltip" 
                                data-tooltip="Abrir en nueva pestaña">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                        <button onclick="documentViewer.selectForComparison('${nombreDocumento}', '${rutaArchivo}')" 
                                class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg text-sm transition-colors tooltip" 
                                data-tooltip="Seleccionar para comparar">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button onclick="documentViewer.closeDocument('${seccion}')" 
                                class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-sm transition-colors">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
                <div id="documento-viewer-${seccion}" class="w-full h-96 border border-gray-300 rounded-xl overflow-hidden bg-white">
                    <iframe src="${rutaArchivo}#toolbar=0&navpanes=0&scrollbar=0" 
                            class="w-full h-full" 
                            frameborder="0"
                            onload="documentViewer.onDocumentLoad()"
                            onerror="documentViewer.onDocumentError()">
                    </iframe>
                </div>
                
                <!-- Controles adicionales -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Documentos seleccionados: ${this.selectedDocuments.length}</span>
                        ${this.selectedDocuments.length >= 2 ? `
                            <button onclick="documentViewer.compareDocuments()" 
                                    class="bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded-lg text-sm transition-colors">
                                <i class="fas fa-columns mr-1"></i>Comparar
                            </button>
                        ` : ''}
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="documentViewer.downloadDocument('${rutaArchivo}', '${nombreDocumento}')" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg text-sm transition-colors">
                            <i class="fas fa-download mr-1"></i>Descargar
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        contenedor.innerHTML = '';
        contenedor.appendChild(splitLayout);
    }

    /**
     * Callback cuando el documento se carga correctamente
     */
    onDocumentLoad() {
        // Documento cargado correctamente
    }

    /**
     * Callback cuando hay error al cargar el documento
     */
    onDocumentError() {
        // Error al cargar documento
    }

    /**
     * Abre el documento en una nueva pestaña
     * @param {string} rutaArchivo - Ruta del archivo
     */
    openInNewTab(rutaArchivo) {
        window.open(rutaArchivo, '_blank');
    }

    /**
     * Cierra el documento en la sección especificada
     * @param {string} seccion - ID de la sección
     */
    closeDocument(seccion) {
        const contenedor = document.getElementById('contenido-' + seccion);
        if (!contenedor) {
            return;
        }

        // Restaurar contenido original
        const originalContent = this.originalContent.get(seccion);
        if (originalContent) {
            contenedor.innerHTML = originalContent;
        }

        // Limpiar referencias
        if (this.currentDocument === seccion) {
            this.currentDocument = null;
        }
    }

    /**
     * Cierra todo el contenido abierto excepto la sección especificada
     * @param {string} seccionExcluida - Sección que no se debe cerrar
     */
    closeOtherSections(seccionExcluida = null) {
        const sectionsToClose = Array.from(this.openSections).filter(s => s !== seccionExcluida);
        
        sectionsToClose.forEach(seccion => {
            this.closeDocument(seccion);
        });
        
        // Limpiar mapas si están abiertos
        if (window.mapHandler) {
            window.mapHandler.cleanup();
        }
    }

    /**
     * Cierra todo el contenido abierto
     */
    closeAll() {
        this.closeOtherSections();
    }

    /**
     * Hace scroll a la sección especificada
     * @param {string} seccion - ID de la sección
     */
    scrollToSection(seccion) {
        const mapeoSecciones = {
            'datos_generales': '01',
            'domicilio': '02',
            'constitucion': '03',
            'accionistas': '04',
            'apoderado': '05',
            'documentos': '06'
        };
        
        const numeroSeccion = mapeoSecciones[seccion];
        if (numeroSeccion) {
            const elementos = document.querySelectorAll('h2');
            for (let elemento of elementos) {
                if (elemento.textContent.includes(numeroSeccion)) {
                    elemento.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    break;
                }
            }
        }
    }

    /**
     * Obtiene información del documento actual
     * @returns {Object|null} Información del documento actual
     */
    getCurrentDocument() {
        return this.currentDocument;
    }

    /**
     * Verifica si hay contenido abierto en alguna sección
     * @returns {boolean} True si hay contenido abierto
     */
    hasOpenContent() {
        return this.openSections.size > 0;
    }

    /**
     * Obtiene las secciones con contenido abierto
     * @returns {Array} Array de secciones abiertas
     */
    getOpenSections() {
        return Array.from(this.openSections);
    }

    /**
     * Selecciona un documento para comparación
     * @param {string} seccion - ID de la sección
     * @param {string} rutaArchivo - Ruta del archivo PDF
     * @param {string} nombreDocumento - Nombre del documento
     */
    selectForComparison(nombreDocumento, rutaArchivo) {
        const documento = { nombre: nombreDocumento, ruta: rutaArchivo };
        
        // Verificar si ya está seleccionado
        const yaSeleccionado = this.selectedDocuments.some(doc => doc.ruta === rutaArchivo);
        
        if (yaSeleccionado) {
            // Remover de la selección
            this.selectedDocuments = this.selectedDocuments.filter(doc => doc.ruta !== rutaArchivo);
        } else {
            // Agregar a la selección (máximo 2)
            if (this.selectedDocuments.length < 2) {
                this.selectedDocuments.push(documento);
            } else {
                // Reemplazar el primero
                this.selectedDocuments.shift();
                this.selectedDocuments.push(documento);
            }
        }
        
        // Actualizar UI si es necesario
        this.updateComparisonUI();
    }

    /**
     * Actualiza la UI para mostrar documentos seleccionados
     */
    updateComparisonUI() {
        // Actualizar botones y contadores en la interfaz
        const contadores = document.querySelectorAll('[data-selected-count]');
        contadores.forEach(contador => {
            contador.textContent = this.selectedDocuments.length;
        });
        
        // Mostrar/ocultar botones de comparación
        const botonesComparar = document.querySelectorAll('[data-compare-button]');
        botonesComparar.forEach(boton => {
            if (this.selectedDocuments.length >= 2) {
                boton.style.display = 'inline-flex';
            } else {
                boton.style.display = 'none';
            }
        });
    }

    /**
     * Comparar documentos seleccionados
     */
    compareDocuments() {
        if (this.selectedDocuments.length < 2) {
            return;
        }

        // Crear ventana de comparación
        this.createComparisonWindow();
    }

    /**
     * Crear ventana de comparación
     */
    createComparisonWindow() {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl w-11/12 h-5/6 max-w-7xl overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800">Comparación de Documentos</h3>
                    <button onclick="documentViewer.closeComparison()" 
                            class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-4 p-4 h-full">
                    <div class="flex flex-col">
                        <h4 class="font-semibold text-gray-700 mb-2">${this.selectedDocuments[0].nombre}</h4>
                        <iframe src="${this.selectedDocuments[0].ruta}#toolbar=0&navpanes=0" 
                                class="w-full flex-1 border border-gray-300 rounded-lg" 
                                frameborder="0">
                        </iframe>
                    </div>
                    <div class="flex flex-col">
                        <h4 class="font-semibold text-gray-700 mb-2">${this.selectedDocuments[1].nombre}</h4>
                        <iframe src="${this.selectedDocuments[1].ruta}#toolbar=0&navpanes=0" 
                                class="w-full flex-1 border border-gray-300 rounded-lg" 
                                frameborder="0">
                        </iframe>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        this.comparisonModal = modal;
    }

    /**
     * Cierra la comparación en pantalla completa
     */
    closeComparison() {
        if (this.comparisonModal) {
            this.comparisonModal.remove();
            this.comparisonModal = null;
        }
    }

    /**
     * Descargar documento
     */
    downloadDocument(rutaArchivo, nombreDocumento) {
        const link = document.createElement('a');
        link.href = rutaArchivo;
        link.download = nombreDocumento;
        link.click();
    }

    /**
     * Búsqueda en documentos (funcionalidad futura)
     */
    searchInDocuments(query) {
        // Implementar búsqueda en documentos
    }

    /**
     * Zoom en documento (funcionalidad futura)
     */
    zoomDocument(seccion, level) {
        // Implementar zoom
    }
}

// Crear instancia global
window.documentViewer = new DocumentViewer();

// Función global para compatibilidad
window.mostrarDocumento = function(seccion, rutaArchivo, nombreDocumento) {
    window.documentViewer.showDocument(seccion, rutaArchivo, nombreDocumento);
};

// Función global para cerrar documento
window.cerrarDocumento = function(seccion) {
    window.documentViewer.closeDocument(seccion);
};

// Función global para cerrar todo
window.cerrarTodoContenidoAbierto = function(seccionExcluida = null) {
    window.documentViewer.closeOtherSections(seccionExcluida);
};

 
