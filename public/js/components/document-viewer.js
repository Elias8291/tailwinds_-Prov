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
        
        console.log('📖 DocumentViewer inicializado');
    }

    /**
     * Muestra un documento en la sección especificada
     * @param {string} seccion - ID de la sección
     * @param {string} rutaArchivo - Ruta del archivo PDF
     * @param {string} nombreDocumento - Nombre del documento
     */
    showDocument(seccion, rutaArchivo, nombreDocumento) {
        console.log('📖 Abriendo documento:', nombreDocumento, 'en sección:', seccion);
        
        // Cerrar cualquier contenido abierto en otras secciones
        this.closeAllOtherSections(seccion);
        
        // Obtener el contenedor de la sección
        const contenedor = document.getElementById('contenido-' + seccion);
        if (!contenedor) {
            console.error('❌ Contenedor no encontrado para la sección:', seccion);
            return;
        }

        // Guardar el contenido original si no está guardado
        if (!this.originalContent.has(seccion)) {
            this.originalContent.set(seccion, contenedor.innerHTML);
            console.log('💾 Contenido original guardado para:', seccion);
        }

        // Actualizar estado
        this.currentDocument = {
            ruta: rutaArchivo,
            nombre: nombreDocumento,
            seccion: seccion
        };
        this.currentSection = seccion;
        this.openSections.add(seccion);

        // Crear el nuevo layout dividido
        this.createSplitLayout(contenedor, seccion, rutaArchivo, nombreDocumento);

        // Scroll automático a la sección
        setTimeout(() => {
            this.scrollToSection(seccion);
        }, 100);
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
        
        // Crear el header del documento
        const headerDocumento = this.createDocumentHeader(seccion, rutaArchivo, nombreDocumento);
        
        // Crear el iframe para el documento
        const iframe = this.createDocumentIframe(rutaArchivo);
        
        // Crear el layout dividido
        const layoutDividido = document.createElement('div');
        layoutDividido.className = 'grid grid-cols-1 lg:grid-cols-2 gap-4 min-h-[600px]';
        layoutDividido.setAttribute('data-viewer-layout', 'true');
        
        layoutDividido.innerHTML = `
            <div class="border-r border-gray-200 pr-4">
                ${originalContent}
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div id="visor-${seccion}" class="h-full flex flex-col">
                </div>
            </div>
        `;
        
        // Reemplazar el contenido
        contenedor.innerHTML = '';
        contenedor.appendChild(layoutDividido);
        
        // Agregar el header y el iframe al visor
        const visor = document.getElementById('visor-' + seccion);
        visor.appendChild(headerDocumento);
        visor.appendChild(iframe);
        
        console.log('✅ Layout dividido creado para sección:', seccion);
    }

    /**
     * Crea el header del documento con controles
     * @param {string} seccion - ID de la sección
     * @param {string} rutaArchivo - Ruta del archivo
     * @param {string} nombreDocumento - Nombre del documento
     * @returns {Element} Header del documento
     */
    createDocumentHeader(seccion, rutaArchivo, nombreDocumento) {
        const header = document.createElement('div');
        header.className = 'mb-3 p-3 bg-white rounded-lg border border-gray-200 shadow-sm flex-shrink-0';
        header.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center min-w-0 flex-1">
                    <i class="fas fa-file-pdf text-red-500 mr-2 flex-shrink-0"></i>
                    <span class="text-sm font-medium text-gray-700 truncate" title="${nombreDocumento}">
                        ${nombreDocumento}
                    </span>
                </div>
                <div class="flex items-center space-x-2 ml-3 flex-shrink-0">
                    <button onclick="documentViewer.openInNewTab('${rutaArchivo}')" 
                            class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded text-xs transition-colors whitespace-nowrap">
                        <i class="fas fa-external-link-alt mr-1"></i>Abrir
                    </button>
                    <button onclick="documentViewer.closeDocument('${seccion}')" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs transition-colors">
                        <i class="fas fa-times mr-1"></i>Cerrar
                    </button>
                </div>
            </div>
        `;
        return header;
    }

    /**
     * Crea el iframe para mostrar el documento
     * @param {string} rutaArchivo - Ruta del archivo
     * @returns {Element} Iframe del documento
     */
    createDocumentIframe(rutaArchivo) {
        const iframe = document.createElement('iframe');
        iframe.src = rutaArchivo;
        iframe.className = 'w-full flex-1 border border-gray-300 rounded min-h-0';
        iframe.style.minHeight = '500px';
        iframe.setAttribute('loading', 'lazy');
        
        // Agregar eventos de carga
        iframe.onload = () => {
            console.log('✅ Documento cargado correctamente');
        };
        
        iframe.onerror = () => {
            console.error('❌ Error al cargar documento');
            this.showDocumentError(iframe);
        };
        
        return iframe;
    }

    /**
     * Muestra error cuando no se puede cargar el documento
     * @param {Element} iframe - Iframe con error
     */
    showDocumentError(iframe) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'w-full flex-1 flex items-center justify-center bg-gray-100 rounded border border-gray-300';
        errorDiv.innerHTML = `
            <div class="text-center text-gray-500">
                <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                <p class="text-sm font-medium">Error al cargar el documento</p>
                <p class="text-xs mt-1">El archivo no está disponible o es inválido</p>
            </div>
        `;
        
        iframe.parentNode.replaceChild(errorDiv, iframe);
    }

    /**
     * Abre el documento en una nueva pestaña
     * @param {string} rutaArchivo - Ruta del archivo
     */
    openInNewTab(rutaArchivo) {
        window.open(rutaArchivo, '_blank');
        console.log('🔗 Documento abierto en nueva pestaña:', rutaArchivo);
    }

    /**
     * Cierra el documento en la sección especificada
     * @param {string} seccion - ID de la sección
     */
    closeDocument(seccion) {
        console.log('❌ Cerrando documento en sección:', seccion);
        
        const contenedor = document.getElementById('contenido-' + seccion);
        if (!contenedor) {
            console.error('❌ Contenedor no encontrado para la sección:', seccion);
            return;
        }

        // Restaurar contenido original
        const originalContent = this.originalContent.get(seccion);
        if (originalContent) {
            contenedor.innerHTML = originalContent;
            console.log('✅ Contenido original restaurado para:', seccion);
        }

        // Limpiar estado
        this.openSections.delete(seccion);
        
        if (this.currentSection === seccion) {
            this.currentDocument = null;
            this.currentSection = null;
        }
    }

    /**
     * Cierra todo el contenido abierto excepto la sección especificada
     * @param {string} seccionExcluida - Sección que no se debe cerrar
     */
    closeAllOtherSections(seccionExcluida = null) {
        console.log('🔄 Cerrando todas las secciones excepto:', seccionExcluida);
        
        const sectionsToClose = Array.from(this.openSections).filter(s => s !== seccionExcluida);
        
        sectionsToClose.forEach(seccion => {
            this.closeDocument(seccion);
        });
        
        // Limpiar mapas si están abiertos
        if (window.mapHandler) {
            window.mapHandler.cleanup();
        }
        
        console.log('✅ Secciones cerradas:', sectionsToClose);
    }

    /**
     * Cierra todo el contenido abierto
     */
    closeAll() {
        console.log('🗑️ Cerrando todo el contenido');
        this.closeAllOtherSections();
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
    selectDocumentForComparison(seccion, rutaArchivo, nombreDocumento) {
        const documentInfo = {
            seccion: seccion,
            ruta: rutaArchivo,
            nombre: nombreDocumento,
            id: `${seccion}-${Date.now()}`
        };

        // Si ya hay 2 documentos seleccionados, reemplazar el más antiguo
        if (this.selectedDocuments.length >= 2) {
            this.selectedDocuments.shift();
        }

        // Agregar el nuevo documento
        this.selectedDocuments.push(documentInfo);
        
        // Actualizar UI para mostrar selección
        this.updateComparisonUI();
        
        console.log('📋 Documento seleccionado para comparación:', nombreDocumento);
        console.log('📋 Documentos seleccionados:', this.selectedDocuments);

        // Si tenemos 2 documentos, mostrar opción de comparar
        if (this.selectedDocuments.length === 2) {
            this.showComparisonOption();
        }
    }

    /**
     * Actualiza la UI para mostrar documentos seleccionados
     */
    updateComparisonUI() {
        // Remover indicadores anteriores
        document.querySelectorAll('.documento-seleccionado').forEach(el => {
            el.classList.remove('documento-seleccionado');
        });

        // Crear panel de comparación si no existe
        let comparisonPanel = document.getElementById('comparison-panel');
        if (!comparisonPanel) {
            comparisonPanel = this.createComparisonPanel();
            document.body.appendChild(comparisonPanel);
        }

        // Actualizar contenido del panel
        this.updateComparisonPanel(comparisonPanel);
    }

    /**
     * Crea el panel de comparación
     */
    createComparisonPanel() {
        const panel = document.createElement('div');
        panel.id = 'comparison-panel';
        panel.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-2xl border border-gray-200 p-4 z-50 transition-all duration-300';
        panel.style.minWidth = '320px';
        return panel;
    }

    /**
     * Actualiza el contenido del panel de comparación
     */
    updateComparisonPanel(panel) {
        const selectedCount = this.selectedDocuments.length;
        
        let content = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-copy text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm">Comparar Documentos</h3>
                        <p class="text-xs text-gray-500">${selectedCount}/2 seleccionados</p>
                    </div>
                </div>
                <button onclick="documentViewer.clearComparison()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        `;

        if (selectedCount === 0) {
            content += `
                <div class="text-center text-gray-500">
                    <i class="fas fa-mouse-pointer text-2xl mb-2"></i>
                    <p class="text-xs">Shift + Clic en documentos para seleccionar</p>
                </div>
            `;
        } else {
            content += `<div class="space-y-2">`;
            
            this.selectedDocuments.forEach((doc, index) => {
                content += `
                    <div class="flex items-center bg-gray-50 rounded p-2">
                        <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs mr-2">
                            ${index + 1}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">${doc.nombre}</p>
                            <p class="text-xs text-gray-500">${doc.seccion}</p>
                        </div>
                        <button onclick="documentViewer.removeDocumentFromComparison(${index})" 
                                class="text-gray-400 hover:text-red-500 ml-2">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                `;
            });
            
            content += `</div>`;
            
            if (selectedCount === 2) {
                content += `
                    <button onclick="documentViewer.startComparison()" 
                            class="w-full mt-3 px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 text-sm font-medium">
                        <i class="fas fa-eye mr-2"></i>Comparar Documentos
                    </button>
                `;
            }
        }

        panel.innerHTML = content;
    }

    /**
     * Elimina un documento de la comparación
     */
    removeDocumentFromComparison(index) {
        this.selectedDocuments.splice(index, 1);
        this.updateComparisonUI();
    }

    /**
     * Limpia la selección de comparación
     */
    clearComparison() {
        this.selectedDocuments = [];
        const panel = document.getElementById('comparison-panel');
        if (panel) {
            panel.remove();
        }
        
        // Remover indicadores visuales
        document.querySelectorAll('.documento-seleccionado').forEach(el => {
            el.classList.remove('documento-seleccionado');
        });
    }

    /**
     * Inicia la comparación de documentos en pantalla completa
     */
    startComparison() {
        if (this.selectedDocuments.length !== 2) {
            console.warn('⚠️ Se necesitan exactamente 2 documentos para comparar');
            return;
        }

        this.comparisonMode = true;
        this.createFullscreenComparison();
    }

    /**
     * Crea el visor de comparación en pantalla completa
     */
    createFullscreenComparison() {
        // Cerrar todo contenido abierto
        this.closeAll();

        // Crear overlay de pantalla completa
        const overlay = document.createElement('div');
        overlay.id = 'fullscreen-comparison';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-95 z-[100] flex flex-col';
        
        // Header de comparación
        const header = document.createElement('div');
        header.className = 'bg-white border-b border-gray-200 p-4 flex items-center justify-between';
        header.innerHTML = `
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-copy text-white"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800">Comparación de Documentos</h2>
                    <p class="text-sm text-gray-600">Vista lado a lado para análisis detallado</p>
                </div>
            </div>
            <button onclick="documentViewer.closeComparison()" 
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-times mr-2"></i>Cerrar
            </button>
        `;

        // Contenedor de documentos
        const container = document.createElement('div');
        container.className = 'flex-1 flex';
        
        // Crear iframe para cada documento
        this.selectedDocuments.forEach((doc, index) => {
            const docContainer = document.createElement('div');
            docContainer.className = 'flex-1 flex flex-col border-r border-gray-600 last:border-r-0';
            
            // Header del documento
            const docHeader = document.createElement('div');
            docHeader.className = 'bg-gray-800 text-white p-3 flex items-center justify-between';
            docHeader.innerHTML = `
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-xs font-bold mr-3">
                        ${index + 1}
                    </div>
                    <div>
                        <h3 class="font-medium text-sm">${doc.nombre}</h3>
                        <p class="text-xs text-gray-400">${doc.seccion}</p>
                    </div>
                </div>
                <button onclick="window.open('${doc.ruta}', '_blank')" 
                        class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded text-xs">
                    <i class="fas fa-external-link-alt mr-1"></i>Abrir
                </button>
            `;
            
            // Iframe del documento
            const iframe = document.createElement('iframe');
            iframe.src = doc.ruta;
            iframe.className = 'flex-1 w-full border-0';
            iframe.style.minHeight = '500px';
            
            docContainer.appendChild(docHeader);
            docContainer.appendChild(iframe);
            container.appendChild(docContainer);
        });

        overlay.appendChild(header);
        overlay.appendChild(container);
        document.body.appendChild(overlay);

        console.log('👀 Comparación iniciada en pantalla completa');
    }

    /**
     * Cierra la comparación en pantalla completa
     */
    closeComparison() {
        const overlay = document.getElementById('fullscreen-comparison');
        if (overlay) {
            overlay.remove();
        }
        this.comparisonMode = false;
        console.log('❌ Comparación cerrada');
    }

    /**
     * Muestra la opción de comparación
     */
    showComparisonOption() {
        // Opcional: mostrar una notificación temporal
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-500';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="text-sm font-medium">¡2 documentos listos para comparar!</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
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
    window.documentViewer.closeAllOtherSections(seccionExcluida);
};

console.log('📖 DocumentViewer inicializado correctamente'); 