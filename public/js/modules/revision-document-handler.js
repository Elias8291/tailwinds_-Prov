/**
 * Módulo de Manejo de Documentos en Revisión
 * Gestiona modales, visualización y revisión de documentos
 */
class RevisionDocumentHandler {
    constructor(tramiteId, csrfToken) {
        this.tramiteId = tramiteId;
        this.csrfToken = csrfToken;
        this.documentoActualId = null;
        this.revisionesDocumentos = {};
        this.comentariosDocumentos = {};
        
        this.init();
    }

    init() {
        console.log('📄 Iniciando Document Handler...');
        this.setupEventListeners();
        this.setupModalHandlers();
    }

    setupEventListeners() {
        // Delegar eventos para elementos dinámicos
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-document-action]')) {
                const action = e.target.dataset.documentAction;
                const documentId = e.target.dataset.documentId;
                
                switch (action) {
                    case 'open-modal':
                        this.abrirModalDocumento(e.target.dataset.url, e.target.dataset.nombre, documentId);
                        break;
                    case 'open-panel':
                        this.mostrarDocumentoEnPanel(documentId, e.target.dataset.nombre);
                        break;
                    case 'close-viewer':
                        this.cerrarVisorDocumento();
                        break;
                    case 'view-full':
                        this.verDocumentoCompleto();
                        break;
                    case 'new-tab':
                        this.abrirDocumentoNuevaPestana();
                        break;
                }
            }
        });
    }

    setupModalHandlers() {
        // Configurar cierre de modal con escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.cerrarModalDocumento();
            }
        });
    }

    /**
     * Abrir modal de documento para revisión
     */
    abrirModalDocumento(url, nombre, documentoId = null, documentoData = null) {
        const modal = document.getElementById('documento-modal');
        const iframe = document.getElementById('documento-iframe');
        const titulo = document.getElementById('documento-modal-title');
        
        if (!modal || !iframe) {
            console.error('Modal de documento no encontrado');
            return;
        }

        // Configurar modal
        this.documentoActualId = documentoId;
        titulo.textContent = `📄 ${nombre}`;
        iframe.src = url;
        
        // Mostrar modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Actualizar panel de revisión
        this.actualizarPanelRevisionDocumento(nombre, documentoData);
        
        console.log(`📄 Modal abierto para documento: ${nombre}`);
    }

    /**
     * Cerrar modal de documento
     */
    cerrarModalDocumento() {
        const modal = document.getElementById('documento-modal');
        const iframe = document.getElementById('documento-iframe');
        
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        if (iframe) {
            iframe.src = '';
        }
        
        this.documentoActualId = null;
        this.resetearControlesRevision();
        
        console.log('📄 Modal de documento cerrado');
    }

    /**
     * Actualizar panel de revisión de documento
     */
    actualizarPanelRevisionDocumento(nombre, documentoData) {
        const nombreElement = document.getElementById('documento-nombre-revision');
        const versionElement = document.getElementById('documento-version');
        const estadoElement = document.getElementById('documento-estado-actual');
        const comentarioTextarea = document.getElementById('comentario-documento');
        
        if (nombreElement) nombreElement.textContent = nombre;
        if (versionElement) versionElement.textContent = documentoData?.version || 'v1';
        
        // Configurar estado
        if (estadoElement && documentoData?.estado) {
            this.actualizarEstadoDocumentoVisual(documentoData.estado);
        }
        
        // Cargar comentario existente
        if (comentarioTextarea && documentoData?.observaciones) {
            comentarioTextarea.value = documentoData.observaciones;
        }
        
        // Cargar historial si existe
        if (documentoData?.historial) {
            this.cargarHistorialDocumento(documentoData.historial);
        }
    }

    /**
     * Resetear controles de revisión
     */
    resetearControlesRevision() {
        // Limpiar comentario
        const comentario = document.getElementById('comentario-documento');
        if (comentario) comentario.value = '';
        
        // Resetear radio buttons
        const radioPendiente = document.querySelector('input[name="estado-documento"][value="pendiente"]');
        if (radioPendiente) radioPendiente.checked = true;
        
        // Resetear prioridad
        const selectPrioridad = document.getElementById('prioridad-documento');
        if (selectPrioridad) selectPrioridad.value = 'media';
        
        // Ocultar historial
        const historial = document.getElementById('historial-revisiones');
        if (historial) historial.classList.add('hidden');
    }

    /**
     * Aplicar revisión al documento
     */
    async aplicarRevisionDocumento(accion) {
        if (!this.documentoActualId) {
            window.revisionNotifications?.mostrarNotificacion('❌ Error: No hay documento seleccionado', 'error');
            return;
        }
        
        const comentario = document.getElementById('comentario-documento')?.value || '';
        const prioridad = document.getElementById('prioridad-documento')?.value || 'media';
        
        // Validar comentario para ciertas acciones
        if ((accion === 'rechazado' || accion === 'correccion') && !comentario.trim()) {
            window.revisionNotifications?.mostrarNotificacion('⚠️ Debe agregar un comentario para esta acción', 'warning');
            return;
        }
        
        const btnAccion = document.getElementById(`btn-${this.getButtonId(accion)}-documento`);
        if (btnAccion) {
            const textoOriginal = btnAccion.innerHTML;
            btnAccion.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-1"></div> Procesando...';
            btnAccion.disabled = true;
            
            try {
                // Simular llamada a API (aquí integrarías con tu backend)
                await this.enviarRevisionDocumento(accion, comentario, prioridad);
                
                // Actualizar estado local
                this.revisionesDocumentos[this.documentoActualId] = {
                    accion, comentario, prioridad,
                    fecha: new Date().toISOString()
                };
                
                // Actualizar interfaz
                this.actualizarEstadoDocumentoVisual(accion);
                this.agregarAlHistorial(accion, comentario, prioridad);
                
                const mensajes = {
                    'aprobado': '✅ Documento aprobado correctamente',
                    'rechazado': '❌ Documento rechazado',
                    'correccion': '📝 Corrección solicitada'
                };
                
                window.revisionNotifications?.mostrarNotificacion(
                    mensajes[accion], 
                    accion === 'aprobado' ? 'success' : 'warning'
                );
                
            } catch (error) {
                console.error('Error aplicando revisión:', error);
                window.revisionNotifications?.mostrarNotificacion('❌ Error al procesar la revisión', 'error');
            } finally {
                // Restaurar botón
                btnAccion.innerHTML = textoOriginal;
                btnAccion.disabled = false;
            }
        }
    }

    /**
     * Guardar solo comentario sin cambiar estado
     */
    async guardarComentarioDocumento() {
        if (!this.documentoActualId) {
            window.revisionNotifications?.mostrarNotificacion('❌ Error: No hay documento seleccionado', 'error');
            return;
        }
        
        const comentario = document.getElementById('comentario-documento')?.value || '';
        const prioridad = document.getElementById('prioridad-documento')?.value || 'media';
        
        if (!comentario.trim()) {
            window.revisionNotifications?.mostrarNotificacion('⚠️ Agregue un comentario antes de guardar', 'warning');
            return;
        }
        
        const btn = document.getElementById('btn-guardar-comentario-doc');
        if (btn) {
            const textoOriginal = btn.innerHTML;
            btn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-1"></div> Guardando...';
            btn.disabled = true;
            
            try {
                // Simular guardado
                await this.guardarComentario(comentario, prioridad);
                
                this.comentariosDocumentos[this.documentoActualId] = {
                    comentario, prioridad,
                    fecha: new Date().toISOString()
                };
                
                window.revisionNotifications?.mostrarNotificacion('💾 Comentario guardado correctamente', 'success');
                this.agregarAlHistorial('comentario', comentario, prioridad);
                
            } catch (error) {
                console.error('Error guardando comentario:', error);
                window.revisionNotifications?.mostrarNotificacion('❌ Error al guardar comentario', 'error');
            } finally {
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
            }
        }
    }

    /**
     * Mostrar documento en panel lateral
     */
    mostrarDocumentoEnPanel(documentoId, nombreDocumento) {
        const visorContainer = document.getElementById('visor-documento');
        const documentoIframe = document.getElementById('iframe-documento');
        const documentoTitulo = document.getElementById('documento-titulo');
        
        if (!visorContainer || !documentoIframe) {
            console.error('Contenedores del visor no encontrados');
            return;
        }

        const url = this.generateDocumentUrl(documentoId);
        
        // Configurar título
        if (documentoTitulo) {
            documentoTitulo.textContent = nombreDocumento;
        }
        
        // Configurar iframe
        documentoIframe.src = url;
        
        // Mostrar visor
        visorContainer.classList.remove('hidden');
        
        // Scroll al visor
        visorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        console.log(`📄 Documento cargado en panel: ${nombreDocumento}`);
    }

    /**
     * Cerrar visor de documento
     */
    cerrarVisorDocumento() {
        const visorContainer = document.getElementById('visor-documento');
        const documentoIframe = document.getElementById('iframe-documento');
        
        if (visorContainer) {
            visorContainer.classList.add('hidden');
        }
        
        if (documentoIframe) {
            documentoIframe.src = '';
        }
        
        console.log('📄 Visor de documento cerrado');
    }

    /**
     * Ver documento en modo completo
     */
    verDocumentoCompleto() {
        const iframe = document.getElementById('iframe-documento');
        if (iframe && iframe.src) {
            window.open(iframe.src, '_blank');
        }
    }

    /**
     * Abrir documento en nueva pestaña
     */
    abrirDocumentoNuevaPestana() {
        const iframe = document.getElementById('iframe-documento');
        if (iframe && iframe.src) {
            window.open(iframe.src, '_blank');
        }
    }

    /**
     * Actualizar estado visual del documento
     */
    actualizarEstadoDocumentoVisual(estado) {
        const estadoElement = document.getElementById('documento-estado-actual');
        if (!estadoElement) return;
        
        const estadosConfig = {
            'aprobado': { clases: 'bg-green-100 text-green-800', texto: '✅ Aprobado' },
            'rechazado': { clases: 'bg-red-100 text-red-800', texto: '❌ Rechazado' },
            'correccion': { clases: 'bg-yellow-100 text-yellow-800', texto: '📝 Requiere corrección' },
            'pendiente': { clases: 'bg-gray-100 text-gray-800', texto: '⏳ Pendiente' }
        };
        
        const config = estadosConfig[estado] || estadosConfig['pendiente'];
        estadoElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.clases}`;
        estadoElement.textContent = config.texto;
        
        // Actualizar radio button
        const radio = document.querySelector(`input[name="estado-documento"][value="${estado}"]`);
        if (radio) radio.checked = true;
    }

    /**
     * Agregar entrada al historial
     */
    agregarAlHistorial(accion, comentario, prioridad) {
        const historialContainer = document.getElementById('historial-revisiones');
        const listaHistorial = document.getElementById('lista-historial');
        
        if (!listaHistorial) return;
        
        // Mostrar historial
        if (historialContainer) {
            historialContainer.classList.remove('hidden');
        }
        
        const fecha = new Date().toLocaleString('es-ES', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
        
        const iconos = {
            'aprobado': '✅', 'rechazado': '❌',
            'correccion': '📝', 'comentario': '💬'
        };
        
        const colores = {
            'aprobado': 'text-green-600', 'rechazado': 'text-red-600',
            'correccion': 'text-yellow-600', 'comentario': 'text-blue-600'
        };
        
        const nuevaEntrada = document.createElement('div');
        nuevaEntrada.className = 'p-2 bg-gray-50 rounded border-l-2 border-gray-300';
        nuevaEntrada.innerHTML = `
            <div class="flex items-center justify-between mb-1">
                <span class="font-medium ${colores[accion] || 'text-gray-600'}">
                    ${iconos[accion] || '📄'} ${accion.charAt(0).toUpperCase() + accion.slice(1)}
                </span>
                <span class="text-gray-400">${fecha}</span>
            </div>
            ${comentario ? `<p class="text-gray-700">${comentario}</p>` : ''}
            <div class="flex items-center justify-between mt-1">
                <span class="text-xs text-gray-500">Prioridad: ${prioridad}</span>
                <span class="text-xs text-gray-500">Revisor</span>
            </div>
        `;
        
        listaHistorial.insertBefore(nuevaEntrada, listaHistorial.firstChild);
    }

    // Métodos auxiliares
    getButtonId(accion) {
        const mapping = {
            'aprobado': 'aprobar',
            'rechazado': 'rechazar',
            'correccion': 'correccion'
        };
        return mapping[accion] || accion;
    }

    generateDocumentUrl(documentoId) {
        return `/tramites-solicitante/ver-documento-revision/${this.tramiteId}/${documentoId}`;
    }

    // Métodos de API (para integrar con backend)
    async enviarRevisionDocumento(accion, comentario, prioridad) {
        // Simular delay de red
        return new Promise(resolve => setTimeout(resolve, 1500));
    }

    async guardarComentario(comentario, prioridad) {
        // Simular delay de red
        return new Promise(resolve => setTimeout(resolve, 1000));
    }

    cargarHistorialDocumento(historial) {
        // Implementar carga de historial desde datos
        console.log('Cargando historial:', historial);
    }

    // Exponer métodos globalmente
    exposeGlobalMethods() {
        window.abrirModalDocumento = this.abrirModalDocumento.bind(this);
        window.cerrarModalDocumento = this.cerrarModalDocumento.bind(this);
        window.aplicarRevisionDocumento = this.aplicarRevisionDocumento.bind(this);
        window.guardarComentarioDocumento = this.guardarComentarioDocumento.bind(this);
        window.mostrarDocumentoEnPanel = this.mostrarDocumentoEnPanel.bind(this);
        window.cerrarVisorDocumento = this.cerrarVisorDocumento.bind(this);
        window.verDocumentoCompleto = this.verDocumentoCompleto.bind(this);
        window.abrirDocumentoNuevaPestana = this.abrirDocumentoNuevaPestana.bind(this);
    }
}

// Exportar para uso en otros módulos
window.RevisionDocumentHandler = RevisionDocumentHandler; 