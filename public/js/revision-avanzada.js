/**
 * Sistema de Revisión Comparativa de Trámites
 * Layout de 3 columnas: Secciones | Formulario | Documentos y Revisión
 */
class RevisionComparativa {
    constructor(tramiteId, csrfToken, documentosPorSeccion = {}) {
        this.tramiteId = tramiteId;
        this.csrfToken = csrfToken;
        this.documentosPorSeccion = documentosPorSeccion;
        this.estadoSecciones = {};
        this.seccionActiva = null;
        this.todasAprobadas = false;
        this.algunaRechazada = false;
        
        // Referencias DOM
        this.elementos = {
            seccionesContainer: document.getElementById('secciones-container'),
            contenidoFormulario: document.getElementById('contenido-formulario'),
            panelDocumentosRevision: document.getElementById('panel-documentos-revision'),
            barraProgreso: document.getElementById('barra-progreso'),
            estadisticasProgreso: document.getElementById('estadisticas-progreso'),
            btnAprobarTodo: document.getElementById('btn-aprobar-todo'),
            mensajeValidacion: document.getElementById('mensaje-validacion'),
            textoValidacion: document.getElementById('texto-validacion')
        };
        
        this.init();
    }

    async init() {
        console.log('🚀 Iniciando Sistema de Revisión Avanzada...');
        
        // Configurar eventos
        this.setupEventListeners();
        
        // Cargar estado inicial
        await this.cargarEstadoRevisiones();
        
        // Configurar modales
        this.setupModales();
        
        console.log('✅ Sistema de Revisión Avanzada iniciado correctamente');
    }

    setupEventListeners() {
        // Configurar botones principales
        this.elementos.btnAprobarTodo?.addEventListener('click', () => {
            this.abrirModal('modal-aprobar-todo');
        });
        
        document.getElementById('btn-rechazar-todo')?.addEventListener('click', () => {
            this.abrirModal('modal-rechazar-todo');
        });
        
        document.getElementById('btn-pausar')?.addEventListener('click', () => {
            this.abrirModal('modal-pausar');
        });
    }

    /**
     * Cargar estado actual de las revisiones
     */
    async cargarEstadoRevisiones() {
        try {
            this.mostrarCargando(true);
            
            const response = await fetch(`/revision/${this.tramiteId}/estado-revisiones`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.estadoSecciones = data.secciones;
                this.todasAprobadas = data.todas_aprobadas;
                this.algunaRechazada = data.alguna_rechazada;
                
                this.renderizarSecciones(data.secciones);
                this.actualizarProgreso(data);
                this.actualizarBotonAprobar();
                
                console.log('✅ Estado cargado correctamente');
            } else {
                throw new Error(data.message || 'Error desconocido');
            }
        } catch (error) {
            console.error('❌ Error al cargar estado de revisiones:', error);
            this.mostrarNotificacion('Error al cargar el estado de las revisiones', 'error');
        } finally {
            this.mostrarCargando(false);
        }
    }

    /**
     * Renderizar las cards de secciones
     */
    renderizarSecciones(secciones) {
        if (!this.elementos.seccionesContainer) return;
        
        this.elementos.seccionesContainer.innerHTML = '';
        
        Object.values(secciones).forEach(seccion => {
            const card = this.crearCardSeccion(seccion);
            this.elementos.seccionesContainer.appendChild(card);
        });
    }

    /**
     * Crear card individual de sección
     */
    crearCardSeccion(seccion) {
        const div = document.createElement('div');
        div.className = 'seccion-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer mb-3';
        div.dataset.seccionId = seccion.seccion_id;
        
        const configuracion = this.getConfiguracionSeccion(seccion.seccion_id);
        const estadoConfig = this.getConfiguracionEstado(seccion.estado);

        div.innerHTML = `
            <!-- Header del Card -->
            <div class="p-4" onclick="revision.mostrarSeccion(${seccion.seccion_id}, '${seccion.nombre}')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 ${configuracion.bgColor} rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="${configuracion.icono} ${configuracion.iconColor} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-900 text-sm truncate">${seccion.nombre}</h4>
                            <div class="flex items-center space-x-2 mt-1">
                                <div class="w-2 h-2 ${estadoConfig.color} rounded-full"></div>
                                <span class="text-xs text-gray-600">${estadoConfig.texto}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1">
                        ${seccion.estado === 'aprobado' ? '<i class="fas fa-check-circle text-green-500 text-sm"></i>' : ''}
                        ${seccion.estado === 'rechazado' ? '<i class="fas fa-times-circle text-red-500 text-sm"></i>' : ''}
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </div>
            </div>
            
            <!-- Panel de Revisión Rápida -->
            <div class="px-4 pb-4 space-y-3">
                <!-- Botones de Acción -->
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="revision.aprobarSeccion(${seccion.seccion_id})" 
                            class="px-2 py-1 ${seccion.estado === 'aprobado' ? 'bg-green-100 text-green-700 cursor-default' : 'bg-green-600 hover:bg-green-700 text-white'} text-xs font-medium rounded transition-colors flex items-center justify-center">
                        <i class="fas fa-check mr-1"></i>
                        ${seccion.estado === 'aprobado' ? 'Aprobado' : 'Aprobar'}
                    </button>
                    <button onclick="revision.rechazarSeccion(${seccion.seccion_id})" 
                            class="px-2 py-1 ${seccion.estado === 'rechazado' ? 'bg-red-100 text-red-700 cursor-default' : 'bg-red-600 hover:bg-red-700 text-white'} text-xs font-medium rounded transition-colors flex items-center justify-center">
                        <i class="fas fa-times mr-1"></i>
                        ${seccion.estado === 'rechazado' ? 'Rechazado' : 'Rechazar'}
                    </button>
                </div>
                
                <!-- Área de Comentarios Compacta -->
                <div class="space-y-2">
                    <textarea id="comentario-${seccion.seccion_id}" 
                              rows="2" 
                              class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                              placeholder="Observaciones...">${seccion.comentario || ''}</textarea>
                    <button onclick="revision.guardarComentario(${seccion.seccion_id})" 
                            class="w-full px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                        <i class="fas fa-save mr-1"></i>
                        Guardar
                    </button>
                </div>
                
                ${seccion.revisor && seccion.fecha_revision ? `
                    <div class="text-xs text-gray-500 bg-gray-50 rounded px-2 py-1">
                        <div class="flex justify-between items-center">
                            <span><i class="fas fa-user mr-1"></i>${seccion.revisor}</span>
                            <span><i class="fas fa-clock mr-1"></i>${seccion.fecha_revision.split(' ')[0]}</span>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
        
        return div;
    }

    /**
     * Configuración visual por sección
     */
    getConfiguracionSeccion(seccionId) {
        const configuraciones = {
            1: { icono: 'fas fa-user-circle', bgColor: 'bg-primary-100', iconColor: 'text-primary' },
            2: { icono: 'fas fa-map-marker-alt', bgColor: 'bg-green-100', iconColor: 'text-green-600' },
            3: { icono: 'fas fa-file-contract', bgColor: 'bg-amber-100', iconColor: 'text-amber-600' },
            4: { icono: 'fas fa-users', bgColor: 'bg-blue-100', iconColor: 'text-blue-600' },
            5: { icono: 'fas fa-user-shield', bgColor: 'bg-purple-100', iconColor: 'text-purple-600' },
            6: { icono: 'fas fa-file-upload', bgColor: 'bg-orange-100', iconColor: 'text-orange-600' }
        };
        
        return configuraciones[seccionId] || { icono: 'fas fa-file', bgColor: 'bg-gray-100', iconColor: 'text-gray-600' };
    }

    /**
     * Configuración visual por estado
     */
    getConfiguracionEstado(estado) {
        const configuraciones = {
            'aprobado': { color: 'bg-green-500', texto: 'Aprobado' },
            'rechazado': { color: 'bg-red-500', texto: 'Rechazado' },
            'pendiente': { color: 'bg-yellow-500', texto: 'Pendiente' }
        };
        
        return configuraciones[estado] || { color: 'bg-gray-500', texto: 'Sin estado' };
    }

    /**
     * Actualizar barra de progreso
     */
    actualizarProgreso(data) {
        if (!this.elementos.barraProgreso || !this.elementos.estadisticasProgreso) return;
        
        const porcentaje = (data.aprobadas / data.total_secciones) * 100;
        
        // Animación de la barra de progreso
        this.elementos.barraProgreso.style.width = `${porcentaje}%`;
        
        // Cambiar color según progreso
        this.elementos.barraProgreso.className = 'h-3 rounded-xl transition-all duration-700 shadow-lg relative overflow-hidden ';
        if (porcentaje === 100) {
            this.elementos.barraProgreso.className += 'bg-gradient-to-r from-green-500 to-emerald-600';
        } else if (porcentaje >= 75) {
            this.elementos.barraProgreso.className += 'bg-gradient-to-r from-primary via-primary-dark to-primary-light';
        } else if (porcentaje >= 50) {
            this.elementos.barraProgreso.className += 'bg-gradient-to-r from-amber-500 to-orange-600';
        } else {
            this.elementos.barraProgreso.className += 'bg-gradient-to-r from-red-500 to-pink-600';
        }
        
        // Actualizar estadísticas
        this.elementos.estadisticasProgreso.innerHTML = `
            <div class="flex items-center space-x-4">
                <span class="font-semibold text-gray-900">${data.aprobadas}/${data.total_secciones} secciones completadas</span>
                <div class="flex space-x-3 text-xs">
                    ${data.aprobadas > 0 ? `<span class="text-green-600 font-medium"><i class="fas fa-check mr-1"></i>${data.aprobadas} aprobadas</span>` : ''}
                    ${data.rechazadas > 0 ? `<span class="text-red-600 font-medium"><i class="fas fa-times mr-1"></i>${data.rechazadas} rechazadas</span>` : ''}
                    ${data.pendientes > 0 ? `<span class="text-yellow-600 font-medium"><i class="fas fa-clock mr-1"></i>${data.pendientes} pendientes</span>` : ''}
                </div>
            </div>
        `;
    }

    /**
     * Actualizar estado del botón aprobar todo
     */
    actualizarBotonAprobar() {
        if (!this.elementos.btnAprobarTodo) return;
        
        const puedeAprobar = this.todasAprobadas && !this.algunaRechazada;
        
        if (puedeAprobar) {
            this.elementos.btnAprobarTodo.disabled = false;
            this.elementos.btnAprobarTodo.className = 'inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors';
            this.elementos.mensajeValidacion?.classList.add('hidden');
        } else {
            this.elementos.btnAprobarTodo.disabled = true;
            this.elementos.btnAprobarTodo.className = 'inline-flex items-center px-6 py-2 bg-gray-300 cursor-not-allowed text-gray-600 font-medium rounded-lg';
            
            let mensaje = '';
            if (this.algunaRechazada) {
                mensaje = 'No se puede aprobar el trámite porque hay secciones rechazadas. Revise y corrija las observaciones.';
            } else if (!this.todasAprobadas) {
                mensaje = 'Complete la revisión de todas las secciones antes de aprobar el trámite completo.';
            }
            
            if (this.elementos.textoValidacion && this.elementos.mensajeValidacion) {
                this.elementos.textoValidacion.textContent = mensaje;
                this.elementos.mensajeValidacion.classList.remove('hidden');
            }
        }
    }

    /**
     * Aprobar sección específica
     */
    async aprobarSeccion(seccionId) {
        if (this.estadoSecciones[seccionId]?.estado === 'aprobado') {
            this.mostrarNotificacion('Esta sección ya está aprobada', 'info');
            return;
        }
        
        try {
            const response = await fetch(`/revision/${this.tramiteId}/seccion/${seccionId}/aprobar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                this.mostrarNotificacion('Sección aprobada correctamente', 'success');
                await this.cargarEstadoRevisiones();
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error('❌ Error al aprobar sección:', error);
            this.mostrarNotificacion('Error al aprobar la sección', 'error');
        }
    }

    /**
     * Rechazar sección específica
     */
    async rechazarSeccion(seccionId) {
        if (this.estadoSecciones[seccionId]?.estado === 'rechazado') {
            this.mostrarNotificacion('Esta sección ya está rechazada', 'info');
            return;
        }
        
        const comentarioElement = document.getElementById(`comentario-${seccionId}`);
        const comentario = comentarioElement?.value.trim();
        
        if (!comentario) {
            this.mostrarNotificacion('Debe agregar un comentario explicando el motivo del rechazo', 'warning');
            comentarioElement?.focus();
            return;
        }
        
        try {
            const response = await fetch(`/revision/${this.tramiteId}/seccion/${seccionId}/rechazar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comentario })
            });
            
            if (response.ok) {
                this.mostrarNotificacion('Sección rechazada correctamente', 'success');
                await this.cargarEstadoRevisiones();
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error('❌ Error al rechazar sección:', error);
            this.mostrarNotificacion('Error al rechazar la sección', 'error');
        }
    }

    /**
     * Guardar comentario de sección
     */
    async guardarComentario(seccionId) {
        const comentarioElement = document.getElementById(`comentario-${seccionId}`);
        const comentario = comentarioElement?.value.trim();
        
        if (!comentario) {
            this.mostrarNotificacion('Escriba un comentario antes de guardar', 'warning');
            comentarioElement?.focus();
            return;
        }
        
        try {
            const response = await fetch(`/revision/${this.tramiteId}/seccion/${seccionId}/comentario`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comentario })
            });
            
            if (response.ok) {
                this.mostrarNotificacion('Comentario guardado correctamente', 'success');
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error('❌ Error al guardar comentario:', error);
            this.mostrarNotificacion('Error al guardar el comentario', 'error');
        }
    }

    /**
     * Mostrar sección seleccionada
     */
    mostrarSeccion(seccionId, nombreSeccion) {
        // Actualizar sección activa
        this.seccionActiva = seccionId;
        
        // Actualizar visual de secciones
        document.querySelectorAll('.seccion-card').forEach(card => {
            card.classList.remove('active');
        });
        document.querySelector(`[data-seccion-id="${seccionId}"]`)?.classList.add('active');
        
        // Mostrar contenido del formulario
        this.mostrarContenidoFormulario(nombreSeccion);
        
        // Mostrar panel de documentos y revisión
        this.mostrarPanelDocumentos(seccionId, nombreSeccion);
    }

    /**
     * Mostrar contenido del formulario en la columna central
     */
    mostrarContenidoFormulario(nombreSeccion) {
        console.log('🔍 Mostrando contenido para sección:', nombreSeccion);
        
        // Mapear nombres de secciones (manejo tanto minúsculas como mayúsculas)
        const mapeoNombres = {
            'general': 'datos-generales',
            'General': 'datos-generales',
            'datos generales': 'datos-generales',
            'Datos Generales': 'datos-generales',
            'domicilio': 'domicilio',
            'Domicilio': 'domicilio',
            'constitución': 'constitucion',
            'Constitución': 'constitucion',
            'constitucion': 'constitucion',
            'Constitucion': 'constitucion',
            'accionistas': 'accionistas',
            'Accionistas': 'accionistas',
            'apoderado': 'apoderado',
            'Apoderado': 'apoderado',
            'apoderado legal': 'apoderado',
            'Apoderado Legal': 'apoderado',
            'documentos': 'documentos',
            'Documentos': 'documentos'
        };
        
        const nombreMapeado = mapeoNombres[nombreSeccion] || nombreSeccion.toLowerCase().replace(/\s+/g, '-');
        const contenidoId = `contenido-${nombreMapeado}`;
        
        console.log('📍 Buscando contenido con ID:', contenidoId);
        
        // Ocultar todos los contenidos
        const contenidos = document.querySelectorAll('[id^="contenido-"]');
        contenidos.forEach(c => c.classList.add('hidden'));
        
        // Mostrar el contenido específico
        const contenido = document.getElementById(contenidoId);
        if (contenido) {
            contenido.classList.remove('hidden');
            console.log('✅ Contenido mostrado exitosamente para:', nombreSeccion);
            
            // Scroll hacia arriba en el contenedor del formulario
            const contenedorFormulario = this.elementos.contenidoFormulario;
            if (contenedorFormulario) {
                contenedorFormulario.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } else {
            console.warn(`⚠️ No se encontró contenido para la sección: ${nombreSeccion} (ID: ${contenidoId})`);
            console.log('📝 Contenidos disponibles:', Array.from(document.querySelectorAll('[id^="contenido-"]')).map(el => el.id));
            
            // Mostrar contenido general por defecto
            const contenidoGeneral = document.getElementById('contenido-general');
            if (contenidoGeneral) {
                contenidoGeneral.classList.remove('hidden');
                console.log('📋 Mostrando contenido general por defecto');
            }
        }
    }

    /**
     * Mostrar panel de documentos en la columna derecha
     */
    mostrarPanelDocumentos(seccionId, nombreSeccion) {
        console.log('📄 Mostrando panel de documentos para:', nombreSeccion);
        
        // Ocultar todos los paneles
        const paneles = document.querySelectorAll('[id^="panel-"]');
        paneles.forEach(p => p.classList.add('hidden'));
        
        // Mapear nombres de secciones para documentos (mismo mapeo que contenido)
        const mapeoDocumentos = {
            'general': 'datos-generales',
            'General': 'datos-generales',
            'datos generales': 'datos-generales',
            'Datos Generales': 'datos-generales',
            'domicilio': 'domicilio',
            'Domicilio': 'domicilio',
            'constitución': 'constitucion',
            'Constitución': 'constitucion',
            'constitucion': 'constitucion',
            'Constitucion': 'constitucion',
            'accionistas': 'accionistas',
            'Accionistas': 'accionistas',
            'apoderado': 'apoderado',
            'Apoderado': 'apoderado',
            'apoderado legal': 'apoderado',
            'Apoderado Legal': 'apoderado',
            'documentos': 'documentos',
            'Documentos': 'documentos'
        };
        
        // Obtener documentos de esta sección
        const seccionKey = this.getSectorKey(nombreSeccion);
        const documentosSeccion = this.documentosPorSeccion[seccionKey] || [];
        
        // Usar el nombre mapeado para el panel
        const nombreMapeado = mapeoDocumentos[nombreSeccion] || nombreSeccion.toLowerCase().replace(/\s+/g, '-');
        const panelId = `panel-${nombreMapeado}`;
        let panel = document.getElementById(panelId);
        
        if (!panel) {
            // Crear panel dinámico
            panel = document.createElement('div');
            panel.id = panelId;
            panel.className = '';
            this.elementos.panelDocumentosRevision.appendChild(panel);
            console.log('🆕 Panel creado dinámicamente:', panelId);
        }
        
        // Renderizar contenido del panel
        panel.innerHTML = this.crearContenidoPanelDocumentos(seccionId, nombreSeccion, documentosSeccion);
        panel.classList.remove('hidden');
        
        console.log('✅ Panel de documentos mostrado:', panelId);
    }

    /**
     * Crear contenido del panel de documentos
     */
    crearContenidoPanelDocumentos(seccionId, nombreSeccion, documentos) {
        const configuracion = this.getConfiguracionSeccion(seccionId);
        
        return `
            <div class="p-4">
                <!-- Header del Panel -->
                <div class="mb-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-6 h-6 ${configuracion.bgColor} rounded flex items-center justify-center">
                            <i class="${configuracion.icono} ${configuracion.iconColor} text-xs"></i>
                        </div>
                        <h4 class="font-medium text-gray-900 text-sm">${nombreSeccion}</h4>
                    </div>
                    <p class="text-xs text-gray-600">Documentos relacionados con esta sección</p>
                </div>
                
                <!-- Lista de Documentos -->
                <div class="space-y-3 mb-6">
                    ${documentos.length > 0 ? 
                        documentos.map(doc => this.crearCardDocumento(doc)).join('') :
                        `<div class="text-center py-4">
                            <i class="fas fa-inbox text-gray-400 text-2xl mb-2"></i>
                            <p class="text-xs text-gray-500">No hay documentos específicos para esta sección</p>
                        </div>`
                    }
                </div>
                
                <!-- Panel de Estado de Revisión -->
                <div class="border-t border-gray-200 pt-4">
                    <h5 class="font-medium text-gray-900 text-sm mb-3 flex items-center">
                        <i class="fas fa-clipboard-check text-blue-500 mr-2"></i>
                        Estado de Revisión
                    </h5>
                    
                    <div class="space-y-3">
                        ${this.crearEstadoRevisionSeccion(seccionId)}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Crear card de documento individual
     */
    crearCardDocumento(documento) {
        const estadoColor = {
            'Aprobado': 'border-green-300 bg-green-50',
            'Rechazado': 'border-red-300 bg-red-50',
            'Pendiente': 'border-yellow-300 bg-yellow-50'
        };
        
        const estadoIcono = {
            'Aprobado': 'fas fa-check-circle text-green-500',
            'Rechazado': 'fas fa-times-circle text-red-500',
            'Pendiente': 'fas fa-clock text-yellow-500'
        };

        return `
            <div class="border rounded-lg p-3 ${estadoColor[documento.estado] || 'border-gray-200'}">
                <div class="flex items-start space-x-2">
                    <i class="fas fa-file-pdf text-red-500 text-sm mt-0.5"></i>
                    <div class="flex-1 min-w-0">
                        <h6 class="text-xs font-medium text-gray-900 truncate">${documento.nombre}</h6>
                        <div class="flex items-center space-x-1 mt-1">
                            <i class="${estadoIcono[documento.estado] || 'fas fa-file text-gray-400'} text-xs"></i>
                            <span class="text-xs text-gray-600">${documento.estado || 'Sin estado'}</span>
                        </div>
                        ${documento.observaciones ? `
                            <p class="text-xs text-gray-500 mt-1 truncate">${documento.observaciones}</p>
                        ` : ''}
                    </div>
                </div>
                
                ${documento.ruta_archivo ? `
                    <div class="mt-2">
                        <a href="${documento.ruta_archivo}" 
                           target="_blank"
                           class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye mr-1"></i>
                            Ver documento
                        </a>
                    </div>
                ` : ''}
            </div>
        `;
    }

    /**
     * Crear estado de revisión de sección
     */
    crearEstadoRevisionSeccion(seccionId) {
        const seccion = this.estadoSecciones[seccionId];
        if (!seccion) return '<p class="text-xs text-gray-500">Información no disponible</p>';
        
        const estadoConfig = this.getConfiguracionEstado(seccion.estado);
        
        return `
            <div class="bg-gray-50 rounded p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-700">Estado Actual</span>
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 ${estadoConfig.color} rounded-full"></div>
                        <span class="text-xs text-gray-600">${estadoConfig.texto}</span>
                    </div>
                </div>
                
                ${seccion.comentario ? `
                    <div class="mt-2">
                        <p class="text-xs text-gray-600">${seccion.comentario}</p>
                    </div>
                ` : ''}
                
                ${seccion.revisor && seccion.fecha_revision ? `
                    <div class="mt-2 pt-2 border-t border-gray-200">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Por: ${seccion.revisor}</span>
                            <span>${seccion.fecha_revision}</span>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    }

    /**
     * Obtener clave de sección para documentos
     */
    getSectorKey(nombreSeccion) {
        const mapeo = {
            'general': 'datos_generales',
            'General': 'datos_generales',
            'datos generales': 'datos_generales',
            'Datos Generales': 'datos_generales',
            'domicilio': 'domicilio', 
            'Domicilio': 'domicilio',
            'constitución': 'constitucion',
            'Constitución': 'constitucion',
            'constitucion': 'constitucion',
            'Constitucion': 'constitucion',
            'accionistas': 'accionistas',
            'Accionistas': 'accionistas',
            'apoderado': 'apoderado',
            'Apoderado': 'apoderado',
            'apoderado legal': 'apoderado',
            'Apoderado Legal': 'apoderado',
            'documentos': 'documentos',
            'Documentos': 'documentos'
        };
        
        return mapeo[nombreSeccion] || 'documentos';
    }

    /**
     * Setup de modales
     */
    setupModales() {
        // Botones para cerrar modales
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', () => this.cerrarModales());
        });
        
        // Cerrar modal al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                this.cerrarModales();
            }
        });
        
        // Cerrar modal con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.cerrarModales();
            }
        });
    }

    abrirModal(modalId) {
        document.getElementById(modalId)?.classList.remove('hidden');
    }

    cerrarModales() {
        document.querySelectorAll('[id^="modal-"]').forEach(modal => {
            modal.classList.add('hidden');
        });
    }

    /**
     * Mostrar indicador de carga
     */
    mostrarCargando(mostrar = true) {
        if (mostrar) {
            this.elementos.estadisticasProgreso.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Cargando...';
        }
    }

    /**
     * Sistema de notificaciones
     */
    mostrarNotificacion(mensaje, tipo = 'info', duracion = 4000) {
        // Crear elemento de notificación
        const notificacion = document.createElement('div');
        notificacion.className = `fixed top-4 right-4 z-50 max-w-sm p-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
        
        const colores = {
            'success': 'bg-green-600 text-white',
            'error': 'bg-red-600 text-white',
            'warning': 'bg-yellow-600 text-white',
            'info': 'bg-blue-600 text-white'
        };
        
        const iconos = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        };
        
        notificacion.className += ` ${colores[tipo] || colores.info}`;
        
        notificacion.innerHTML = `
            <div class="flex items-center">
                <i class="${iconos[tipo] || iconos.info} mr-2"></i>
                <span class="text-sm font-medium">${mensaje}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notificacion);
        
        // Animar entrada
        setTimeout(() => {
            notificacion.classList.remove('translate-x-full');
        }, 100);
        
        // Auto-cerrar
        setTimeout(() => {
            notificacion.classList.add('translate-x-full');
            setTimeout(() => notificacion.remove(), 300);
        }, duracion);
    }
}

// Exponer métodos globales para compatibilidad
window.revision = null;

document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos del trámite
    const tramiteId = window.tramiteId || document.querySelector('meta[name="tramite-id"]')?.getAttribute('content');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const documentosPorSeccion = window.documentosPorSeccion || {};
    
    console.log('🔧 Inicializando sistema de revisión...', {
        tramiteId,
        csrfToken: csrfToken ? 'Presente' : 'Ausente',
        documentosPorSeccion: Object.keys(documentosPorSeccion)
    });
    
    if (!tramiteId || !csrfToken) {
        console.error('❌ No se pudieron obtener los datos necesarios para inicializar el sistema de revisión', {
            tramiteId: tramiteId || 'No encontrado',
            csrfToken: csrfToken || 'No encontrado'
        });
        return;
    }
    
    // Inicializar sistema de revisión
    window.revision = new RevisionComparativa(tramiteId, csrfToken, documentosPorSeccion);
}); 