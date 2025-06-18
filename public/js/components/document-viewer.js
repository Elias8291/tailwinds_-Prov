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