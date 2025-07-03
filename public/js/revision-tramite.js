/**
 * Sistema de Revisión de Trámites - Módulo Principal
 * Refactorizado: Limpio, organizado y usando más Laravel, menos JavaScript inline
 */

window.RevisionTramite = (function() {
    'use strict';
    
    // Estado global
    let state = {
        currentSection: 'general',
        tramiteData: {},
        revisionData: {},
        isInitialized: false
    };
    
    // Cache de elementos DOM
    const dom = {
        sectionTabs: null,
        sectionContents: null,
        documentModal: null,
        mapModal: null,
        mainContainer: null
    };
    
    /**
     * Módulo de Navegación entre Secciones
     */
    const Navigation = {
        init() {
            this.cacheDOMElements();
            this.bindEvents();
            this.showInitialSection();
        },
        
        cacheDOMElements() {
            dom.sectionTabs = document.querySelectorAll('.seccion-tab');
            dom.sectionContents = document.querySelectorAll('.section-content');
        },
        
        bindEvents() {
            dom.sectionTabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const seccion = tab.getAttribute('data-seccion');
                    this.switchToSection(seccion);
                });
            });
        },
        
        showInitialSection() {
            this.switchToSection('general');
        },
        
        switchToSection(seccion) {
            if (!seccion) return;
            
            // Actualizar estado
            state.currentSection = seccion;
            
            // Actualizar pestañas
            this.updateTabs(seccion);
            
            // Mostrar contenido
            this.showContent(seccion);
            
            // Actualizar header
            this.updateSectionHeader(seccion);
            
            // Ejecutar acciones específicas
            this.handleSectionSpecificActions(seccion);
        },
        
        updateTabs(activeSeccion) {
            dom.sectionTabs.forEach(tab => {
                const seccion = tab.getAttribute('data-seccion');
                const isActive = seccion === activeSeccion;
                
                // Remover clases activas
                tab.classList.remove('active', 'bg-blue-50', 'text-blue-700', 'border-blue-300');
                
                if (isActive) {
                    tab.classList.add('active', 'bg-blue-50', 'text-blue-700', 'border-blue-300');
                } else {
                    tab.classList.add('bg-gray-50', 'text-gray-700', 'border-gray-300');
                }
            });
        },
        
        showContent(seccion) {
            // Ocultar todos los contenidos
            dom.sectionContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Mostrar contenido activo
            const activeContent = document.getElementById(`contenido-${seccion}`);
            if (activeContent) {
                activeContent.classList.remove('hidden');
            }
        },
        
        updateSectionHeader(seccion) {
            const sectionInfo = {
                'general': {
                    title: 'Información General',
                    description: 'Resumen del trámite y datos del solicitante'
                },
                'datos-generales': {
                    title: 'Datos Generales',
                    description: 'Información básica y datos de contacto'
                },
                'domicilio': {
                    title: 'Domicilio',
                    description: 'Dirección y verificación geográfica'
                },
                'constitucion': {
                    title: 'Constitución',
                    description: 'Acta constitutiva y datos notariales'
                },
                'accionistas': {
                    title: 'Accionistas',
                    description: 'Información de socios y accionistas'
                },
                'apoderado': {
                    title: 'Apoderado Legal',
                    description: 'Datos del representante legal'
                },
                'documentos': {
                    title: 'Documentos',
                    description: 'Archivos y documentos adjuntos'
                },
                'decision-final': {
                    title: 'Decisión Final',
                    description: 'Proceso de aprobación final'
                }
            };
            
            const info = sectionInfo[seccion] || { title: 'Sección', description: 'Información de la sección' };
            
            const titleElement = document.getElementById('seccion-titulo');
            const descriptionElement = document.getElementById('seccion-descripcion');
            
            if (titleElement) titleElement.textContent = info.title;
            if (descriptionElement) descriptionElement.textContent = info.description;
        },
        
        handleSectionSpecificActions(seccion) {
            switch (seccion) {
                case 'domicilio':
                    Maps.loadSidebarMap();
                    break;
                case 'documentos':
                    Documents.refreshList();
                    break;
            }
        }
    };
    
    /**
     * Módulo de Gestión de Documentos
     */
    const Documents = {
        init() {
            this.cacheDOMElements();
            this.bindEvents();
        },
        
        cacheDOMElements() {
            dom.documentModal = document.getElementById('documento-modal');
        },
        
        bindEvents() {
            // Delegación de eventos para documentos
            document.addEventListener('click', (e) => {
                const documentItem = e.target.closest('.documento-item');
                if (documentItem) {
                    e.preventDefault();
                    this.openDocument(documentItem);
                }
            });
            
            // Cerrar modal con tecla Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && dom.documentModal && !dom.documentModal.classList.contains('hidden')) {
                    this.closeDocument();
                }
            });
        },
        
        openDocument(documentElement) {
            const documentData = this.extractDocumentData(documentElement);
            if (!documentData) return;
            
            this.updateDocumentModal(documentData);
            this.showDocumentModal();
            this.updateSidebarViewer(documentData);
        },
        
        extractDocumentData(element) {
            return {
                id: element.getAttribute('data-documento-id'),
                path: element.getAttribute('data-ruta'),
                name: element.querySelector('.text-gray-900')?.textContent?.trim() || 'Documento'
            };
        },
        
        updateDocumentModal(data) {
            const elements = {
                title: document.getElementById('documento-modal-title'),
                iframe: document.getElementById('documento-iframe'),
                name: document.getElementById('documento-nombre-revision')
            };
            
            if (elements.title) elements.title.textContent = data.name;
            if (elements.name) elements.name.textContent = data.name;
            if (elements.iframe && data.path) elements.iframe.src = data.path;
        },
        
        showDocumentModal() {
            if (dom.documentModal) {
                dom.documentModal.classList.remove('hidden');
                dom.documentModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        },
        
        closeDocument() {
            if (dom.documentModal) {
                dom.documentModal.classList.add('hidden');
                dom.documentModal.classList.remove('flex');
                document.body.style.overflow = '';
                
                // Limpiar iframe
                const iframe = document.getElementById('documento-iframe');
                if (iframe) iframe.src = '';
            }
            
            this.closeSidebarViewer();
        },
        
        updateSidebarViewer(data) {
            const elements = {
                viewer: document.getElementById('visor-documento'),
                iframe: document.getElementById('iframe-documento'),
                name: document.getElementById('documento-actual-nombre')
            };
            
            if (elements.viewer) elements.viewer.classList.remove('hidden');
            if (elements.iframe && data.path) elements.iframe.src = data.path;
            if (elements.name) elements.name.textContent = data.name;
        },
        
        closeSidebarViewer() {
            const viewer = document.getElementById('visor-documento');
            if (viewer) viewer.classList.add('hidden');
        },
        
        openFullScreen() {
            const iframe = document.getElementById('iframe-documento');
            if (iframe && iframe.src) {
                window.open(iframe.src, '_blank');
            }
        },
        
        refreshList() {
            console.log('📄 Refreshing documents for section:', state.currentSection);
        }
    };
    
    /**
     * Módulo de Gestión de Mapas
     */
    const Maps = {
        init() {
            this.cacheDOMElements();
            this.bindEvents();
        },
        
        cacheDOMElements() {
            dom.mapModal = document.getElementById('mapa-modal');
        },
        
        bindEvents() {
            // Eventos específicos de mapas pueden ir aquí
        },
        
        openFullScreen() {
            if (dom.mapModal) {
                dom.mapModal.classList.remove('hidden');
                dom.mapModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
            
            this.loadFullMap();
        },
        
        closeFullScreen() {
            if (dom.mapModal) {
                dom.mapModal.classList.add('hidden');
                dom.mapModal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        },
        
        loadSidebarMap() {
            const container = document.getElementById('mapa-panel-lateral');
            if (container && state.revisionData.domicilio) {
                console.log('🗺️ Loading sidebar map with data:', state.revisionData.domicilio);
                this.initializeMap(container, state.revisionData.domicilio);
            }
        },
        
        loadFullMap() {
            const container = document.getElementById('mapa-domicilio');
            if (container && state.revisionData.domicilio) {
                console.log('🗺️ Loading full map with data:', state.revisionData.domicilio);
                this.initializeMap(container, state.revisionData.domicilio);
            }
        },
        
        initializeMap(container, addressData) {
            // Placeholder para la inicialización del mapa
            // Aquí se integraría con Google Maps u otro proveedor
            console.log('🗺️ Map initialization placeholder for:', container.id);
        },
        
        changeView(viewType) {
            console.log('🗺️ Changing map view to:', viewType);
        }
    };
    
    /**
     * Módulo de Gestión de Revisiones
     */
    const Sections = {
        approve(seccion) {
            this.updateSectionStatus(seccion, 'aprobado');
        },
        
        reject(seccion) {
            this.updateSectionStatus(seccion, 'rechazado');
        },
        
        requestCorrection(seccion) {
            this.updateSectionStatus(seccion, 'correccion');
        },
        
        saveComment(seccion) {
            const comment = this.getComment(seccion);
            if (!comment) {
                alert('Por favor, ingrese un comentario antes de guardar.');
                return;
            }
            
            this.saveCommentOnly(seccion, comment);
        },
        
        getComment(seccion) {
            const textarea = document.getElementById(`comentarios-${seccion}`);
            return textarea ? textarea.value.trim() : '';
        },
        
        getPriority(seccion) {
            const select = document.getElementById(`prioridad-${seccion}`);
            return select ? select.value : 'media';
        },
        
        updateSectionStatus(seccion, estado) {
            const requestData = {
                seccion: seccion,
                estado: estado,
                comentario: this.getComment(seccion),
                prioridad: this.getPriority(seccion),
                tramite_id: state.tramiteData.id
            };
            
            this.sendRevisionUpdate(requestData);
        },
        
        saveCommentOnly(seccion, comment) {
            const requestData = {
                seccion: seccion,
                comentario: comment,
                tramite_id: state.tramiteData.id
            };
            
            this.sendCommentUpdate(requestData);
        },
        
        async sendRevisionUpdate(data) {
            try {
                console.log('📤 Sending revision update:', data);
                
                // Aquí iría la petición AJAX real
                /*
                const response = await fetch('/api/revision/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    this.showSuccessMessage(`Sección ${data.seccion} ${data.estado} exitosamente`);
                } else {
                    this.showErrorMessage(result.message || 'Error al actualizar la revisión');
                }
                */
                
                // Simulación por ahora
                this.showSuccessMessage(`Sección ${data.seccion} ${data.estado} exitosamente`);
                
            } catch (error) {
                console.error('❌ Error sending revision update:', error);
                this.showErrorMessage('Error de conexión. Por favor, intente nuevamente.');
            }
        },
        
        async sendCommentUpdate(data) {
            try {
                console.log('💬 Sending comment update:', data);
                
                // Simulación por ahora
                this.showSuccessMessage('Comentario guardado exitosamente');
                
            } catch (error) {
                console.error('❌ Error sending comment:', error);
                this.showErrorMessage('Error al guardar el comentario');
            }
        },
        
        showSuccessMessage(message) {
            // Implementar notificación de éxito
            console.log('✅', message);
        },
        
        showErrorMessage(message) {
            // Implementar notificación de error
            console.error('❌', message);
        }
    };
    
    /**
     * Módulo de Gestión de Modales
     */
    const Modals = {
        init() {
            this.bindGlobalEvents();
        },
        
        bindGlobalEvents() {
            // Cerrar modales con Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeAll();
                }
            });
            
            // Cerrar modales al hacer clic fuera
            document.addEventListener('click', (e) => {
                this.handleOutsideClick(e);
            });
        },
        
        handleOutsideClick(e) {
            // Modal de documentos
            if (dom.documentModal && e.target === dom.documentModal) {
                Documents.closeDocument();
            }
            
            // Modal de mapa
            if (dom.mapModal && e.target === dom.mapModal) {
                Maps.closeFullScreen();
            }
        },
        
        closeDocument() {
            Documents.closeDocument();
        },
        
        closeMap() {
            Maps.closeFullScreen();
        },
        
        closeAll() {
            this.closeDocument();
            this.closeMap();
        }
    };
    
    /**
     * Inicialización Principal
     */
    function init() {
        if (state.isInitialized) {
            console.warn('⚠️ RevisionTramite already initialized');
            return;
        }
        
        // Cargar datos globales
        state.tramiteData = window.tramiteData || {};
        state.revisionData = window.revisionData || {};
        
        // Cachear elementos principales
        dom.mainContainer = document.getElementById('main-container');
        
        // Inicializar módulos
        Navigation.init();
        Documents.init();
        Maps.init();
        Modals.init();
        
        // Marcar como inicializado
        state.isInitialized = true;
        
        console.log('✅ RevisionTramite initialized successfully');
        console.log('📊 Tramite data:', state.tramiteData);
        console.log('📋 Revision data:', state.revisionData);
    }
    
    // API Pública
    return {
        init,
        // Módulos públicos
        navigation: Navigation,
        documents: Documents,
        maps: Maps,
        sections: Sections,
        modals: Modals,
        // Estado (solo lectura)
        getCurrentSection: () => state.currentSection,
        getTramiteData: () => ({ ...state.tramiteData }),
        getRevisionData: () => ({ ...state.revisionData })
    };
    
})();

// Auto-inicialización
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.RevisionTramite.init);
} else {
    window.RevisionTramite.init();
} 