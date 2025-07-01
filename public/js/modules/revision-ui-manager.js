/**
 * Módulo de Gestión de Interfaz de Usuario
 * Maneja navegación, tabs, redimensionamiento y elementos UI
 */
class RevisionUIManager {
    constructor() {
        this.seccionActual = 'general';
        this.isResizing = false;
        
        this.init();
    }

    init() {
        console.log('🖥️ Iniciando UI Manager...');
        this.initResizeSystem();
        this.initTabNavigation();
        this.initScrollSincronizado();
        this.initModals();
        this.setupGlobalEventListeners();
    }

    setupGlobalEventListeners() {
        // Eventos globales de UI
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Responsive handling
        window.addEventListener('resize', () => {
            this.handleWindowResize();
        });
    }

    /**
     * Sistema de redimensionamiento de paneles
     */
    initResizeSystem() {
        const resizeHandle = document.getElementById('resize-handle');
        const documentosContainer = document.getElementById('documentos-container');
        const mainContainer = document.getElementById('main-container');
        
        if (!resizeHandle || !documentosContainer || !mainContainer) return;
        
        resizeHandle.addEventListener('mousedown', (e) => {
            this.isResizing = true;
            document.addEventListener('mousemove', this.handleMouseMove.bind(this));
            document.addEventListener('mouseup', this.handleMouseUp.bind(this));
            e.preventDefault();
        });
    }

    handleMouseMove(e) {
        if (!this.isResizing) return;
        
        const container = document.getElementById('main-container');
        const documentosContainer = document.getElementById('documentos-container');
        
        if (container && documentosContainer) {
            const containerRect = container.getBoundingClientRect();
            const newWidth = Math.max(300, Math.min(800, e.clientX - containerRect.left));
            const percentage = (newWidth / containerRect.width) * 100;
            
            documentosContainer.style.width = `${Math.min(60, Math.max(25, percentage))}%`;
        }
    }

    handleMouseUp() {
        this.isResizing = false;
        document.removeEventListener('mousemove', this.handleMouseMove);
        document.removeEventListener('mouseup', this.handleMouseUp);
    }

    /**
     * Navegación de tabs
     */
    initTabNavigation() {
        const tabs = document.querySelectorAll('.tab-seccion');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const seccion = tab.dataset.seccion;
                this.mostrarSeccion(seccion);
            });
        });
    }

    mostrarSeccion(seccion) {
        // Actualizar tab activo
        document.querySelectorAll('.tab-seccion').forEach(tab => {
            tab.classList.remove('active');
        });
        
        const tabActivo = document.querySelector(`[data-seccion="${seccion}"]`);
        if (tabActivo) {
            tabActivo.classList.add('active');
        }

        // Mostrar contenido de sección
        document.querySelectorAll('[data-seccion-content]').forEach(content => {
            content.classList.add('hidden');
        });
        
        const contenidoSeccion = document.querySelector(`[data-seccion-content="${seccion}"]`);
        if (contenidoSeccion) {
            contenidoSeccion.classList.remove('hidden');
        }

        this.seccionActual = seccion;
        this.actualizarPanelLateral(seccion);
        
        console.log(`🖥️ Sección activa: ${seccion}`);
    }

    actualizarPanelLateral(seccion) {
        // Cargar documentos de la sección
        if (window.revisionDocumentHandler) {
            this.cargarDocumentosSeccion(seccion);
        }

        // Actualizar mapa si es sección de domicilio
        if (seccion === 'domicilio' && window.revisionMapHandler) {
            window.revisionMapHandler.mostrarMapaEnPanel();
        }
    }

    /**
     * Sistema de scroll sincronizado
     */
    initScrollSincronizado() {
        const mainScroll = document.getElementById('main-content');
        const sidebarScroll = document.getElementById('sidebar-content');
        
        if (mainScroll && sidebarScroll) {
            let isMainScrolling = false;
            let isSidebarScrolling = false;
            
            mainScroll.addEventListener('scroll', () => {
                if (!isSidebarScrolling) {
                    isMainScrolling = true;
                    const scrollRatio = mainScroll.scrollTop / (mainScroll.scrollHeight - mainScroll.clientHeight);
                    sidebarScroll.scrollTop = scrollRatio * (sidebarScroll.scrollHeight - sidebarScroll.clientHeight);
                    setTimeout(() => { isMainScrolling = false; }, 100);
                }
            });
            
            sidebarScroll.addEventListener('scroll', () => {
                if (!isMainScrolling) {
                    isSidebarScrolling = true;
                    const scrollRatio = sidebarScroll.scrollTop / (sidebarScroll.scrollHeight - sidebarScroll.clientHeight);
                    mainScroll.scrollTop = scrollRatio * (mainScroll.scrollHeight - mainScroll.clientHeight);
                    setTimeout(() => { isSidebarScrolling = false; }, 100);
                }
            });
        }
    }

    /**
     * Inicializar modales
     */
    initModals() {
        // Configurar cierre de modales al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop')) {
                this.closeAllModals();
            }
        });
    }

    closeAllModals() {
        const modals = document.querySelectorAll('.modal, [id$="-modal"]');
        modals.forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
        
        document.body.style.overflow = 'auto';
    }

    /**
     * Cargar documentos de sección
     */
    async cargarDocumentosSeccion(seccion) {
        const listaContainer = document.getElementById('lista-documentos');
        if (!listaContainer) return;

        try {
            this.mostrarCargandoDocumentos(true);
            
            const documentos = window.documentosPorSeccion?.[seccion] || [];
            
            if (documentos.length > 0) {
                this.mostrarDocumentosSeccion(documentos);
            } else {
                this.mostrarSinDocumentos();
            }
            
        } catch (error) {
            console.error('Error cargando documentos:', error);
            this.mostrarErrorDocumentos('Error al cargar los documentos de la sección');
        } finally {
            this.mostrarCargandoDocumentos(false);
        }
    }

    mostrarDocumentosSeccion(documentos) {
        const container = document.getElementById('lista-documentos');
        if (!container) return;

        container.innerHTML = documentos.map(doc => this.createDocumentCard(doc)).join('');
    }

    createDocumentCard(documento) {
        const estadoConfig = this.getEstadoClases(documento.estado || 'pendiente');
        
        return `
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all cursor-pointer documento-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 text-sm">${documento.nombre}</h4>
                            <p class="text-xs text-gray-500">Versión ${documento.version || '1'}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoConfig.clases}">
                        ${estadoConfig.texto}
                    </span>
                </div>
                
                <div class="flex space-x-2">
                    <button onclick="revisionDocumentHandler.mostrarDocumentoEnPanel(${documento.id}, '${documento.nombre}')" 
                            class="flex-1 px-3 py-2 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                        👁️ Ver
                    </button>
                    <button onclick="revisionDocumentHandler.abrirModalDocumento('${this.generateDocumentUrl(documento.id)}', '${documento.nombre}', ${documento.id})" 
                            class="flex-1 px-3 py-2 text-xs bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                        📝 Revisar
                    </button>
                </div>
            </div>
        `;
    }

    mostrarSinDocumentos() {
        const container = document.getElementById('lista-documentos');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">No hay documentos</h3>
                    <p class="text-sm text-gray-500">Esta sección no tiene documentos para revisar.</p>
                </div>
            `;
        }
    }

    mostrarErrorDocumentos(mensaje) {
        const container = document.getElementById('lista-documentos');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">Error al cargar</h3>
                    <p class="text-sm text-gray-500">${mensaje}</p>
                    <button onclick="revisionUIManager.cargarDocumentosSeccion('${this.seccionActual}')" 
                            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Reintentar
                    </button>
                </div>
            `;
        }
    }

    mostrarCargandoDocumentos(mostrar) {
        const container = document.getElementById('lista-documentos');
        if (!container) return;

        if (mostrar) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-sm text-gray-500">Cargando documentos...</p>
                </div>
            `;
        }
    }

    /**
     * Ajustar altura de paneles
     */
    ajustarAlturaPanel() {
        const header = document.querySelector('.header');
        const panels = document.querySelectorAll('.panel-content');
        
        if (header && panels.length > 0) {
            const headerHeight = header.offsetHeight;
            const viewportHeight = window.innerHeight;
            const availableHeight = viewportHeight - headerHeight - 40; // 40px padding
            
            panels.forEach(panel => {
                panel.style.maxHeight = `${availableHeight}px`;
            });
        }
    }

    handleWindowResize() {
        this.ajustarAlturaPanel();
        
        // Ajustar layout en móviles
        if (window.innerWidth < 768) {
            this.adjustMobileLayout();
        }
    }

    adjustMobileLayout() {
        const documentosContainer = document.getElementById('documentos-container');
        if (documentosContainer) {
            documentosContainer.style.width = '100%';
        }
    }

    // Métodos auxiliares
    getEstadoClases(estado) {
        const estados = {
            'pendiente': { clases: 'bg-yellow-100 text-yellow-800', texto: '⏳ Pendiente' },
            'aprobado': { clases: 'bg-green-100 text-green-800', texto: '✅ Aprobado' },
            'rechazado': { clases: 'bg-red-100 text-red-800', texto: '❌ Rechazado' },
            'correccion': { clases: 'bg-orange-100 text-orange-800', texto: '📝 Corrección' }
        };
        return estados[estado] || estados['pendiente'];
    }

    generateDocumentUrl(documentoId) {
        return `/tramites-solicitante/ver-documento-revision/${window.tramiteId}/${documentoId}`;
    }

    // Toggle material de apoyo
    toggleMaterialApoyo() {
        const materialContainer = document.getElementById('material-apoyo');
        const toggleBtn = document.getElementById('toggle-material');
        
        if (materialContainer && toggleBtn) {
            const isHidden = materialContainer.classList.contains('hidden');
            
            if (isHidden) {
                materialContainer.classList.remove('hidden');
                toggleBtn.textContent = '🔼 Ocultar Material de Apoyo';
            } else {
                materialContainer.classList.add('hidden');
                toggleBtn.textContent = '🔽 Mostrar Material de Apoyo';
            }
        }
    }

    // Exponer métodos globalmente
    exposeGlobalMethods() {
        window.toggleMaterialApoyo = this.toggleMaterialApoyo.bind(this);
    }
}

// Exportar globalmente
window.RevisionUIManager = RevisionUIManager; 