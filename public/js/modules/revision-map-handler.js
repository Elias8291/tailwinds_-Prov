/**
 * Módulo de Manejo de Mapas en Revisión
 * Gestiona Google Maps, modales de mapas y funcionalidades geográficas
 */
class RevisionMapHandler {
    constructor() {
        this.googleMapsLoaded = false;
        this.direccionActual = null;
        this.mapaModal = null;
        this.mapaPanel = null;
        
        this.init();
    }

    init() {
        console.log('🗺️ Iniciando Map Handler...');
        this.setupGoogleMapsLoader();
        this.setupEventListeners();
    }

    setupGoogleMapsLoader() {
        // Timeout para detectar si Google Maps no se carga
        setTimeout(() => {
            if (!this.googleMapsLoaded && !window.mapHandler) {
                console.warn('Google Maps no se cargó en 10 segundos, usando fallback');
                this.createMapFallback();
            }
        }, 10000);

        // Función global para callback de Google Maps
        window.initGoogleMaps = () => {
            this.googleMapsLoaded = true;
            console.log('Google Maps API cargada correctamente');
            
            // Cargar map-handler adicional si existe
            this.loadMapHandlerScript();
        };

        // También configurar la referencia en el contexto del módulo
        if (!window.initGoogleMaps) {
            window.initGoogleMaps = this.onGoogleMapsLoaded.bind(this);
        }

        // Detectar errores de Google Maps
        window.addEventListener('error', (e) => {
            if (e.filename && e.filename.includes('maps.googleapis.com')) {
                console.error('Error cargando Google Maps API');
                this.createMapFallback();
            }
        });
    }

    onGoogleMapsLoaded() {
        this.googleMapsLoaded = true;
        console.log('🗺️ Google Maps API cargada correctamente');
        
        // Cargar map-handler adicional si existe
        this.loadMapHandlerScript();
    }

    configurarDomicilio(domicilioData) {
        if (domicilioData) {
            this.domicilioData = domicilioData;
            console.log('🏠 Datos de domicilio configurados:', domicilioData);
        }
    }

    setupEventListeners() {
        // Delegación de eventos para acciones de mapa
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-map-action]')) {
                const action = e.target.dataset.mapAction;
                
                switch (action) {
                    case 'open-domicilio':
                        this.abrirMapaDomicilio();
                        break;
                    case 'close-modal':
                        this.cerrarModalMapa();
                        break;
                    case 'copy-address':
                        this.copiarDireccion();
                        break;
                    case 'open-google-maps':
                        this.abrirEnGoogleMaps();
                        break;
                    case 'change-view':
                        this.cambiarVistaMapaModal(e.target.dataset.viewType);
                        break;
                }
            }
        });

        // Cerrar modal con escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.cerrarModalMapa();
            }
        });
    }

    loadMapHandlerScript() {
        const existingScript = document.querySelector('script[src*="map-handler.js"]');
        if (existingScript) return;

        const script = document.createElement('script');
        script.src = window.assetUrl ? window.assetUrl('js/components/map-handler.js') : '/js/components/map-handler.js';
        script.onload = () => {
            console.log('Map handler adicional cargado correctamente');
        };
        script.onerror = () => {
            console.error('Error al cargar map-handler.js');
            this.createMapFallback();
        };
        document.head.appendChild(script);
    }

    createMapFallback() {
        window.mapHandler = {
            initializeMap: (seccion, direccion) => {
                const container = document.getElementById('mapa-' + seccion);
                if (container) {
                    container.innerHTML = this.generateMapFallbackHTML(direccion);
                }
            },
            cleanup: () => {
                console.log('Limpiando map handler fallback');
            }
        };
    }

    generateMapFallbackHTML(direccion) {
        return `
            <div class="flex items-center justify-center h-full bg-gradient-to-br from-red-50 to-orange-50 rounded-lg border border-red-200">
                <div class="text-center text-gray-700 p-8 max-w-md">
                    <svg class="w-20 h-20 mx-auto mb-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h4 class="text-xl font-bold text-gray-800 mb-3">🗺️ Google Maps no está disponible</h4>
                    <p class="text-sm text-gray-600 mb-4">Verifique su conexión a internet o intente recargar la página</p>
                    
                    <div class="bg-white border border-gray-300 rounded-lg p-4 mb-6">
                        <p class="text-xs text-gray-500 mb-2">📍 Dirección registrada:</p>
                        <p class="text-sm font-medium text-gray-800">${direccion}</p>
                    </div>
                    
                    <div class="space-y-3">
                        <button onclick="window.open('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent('${direccion}'), '_blank')" 
                                class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Abrir en Google Maps
                        </button>
                        
                        <button onclick="location.reload()" 
                                class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Recargar página
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Abrir modal de mapa de domicilio
     */
    abrirMapaDomicilio() {
        const modal = document.getElementById('mapa-modal');
        if (!modal) {
            console.error('Modal de mapa no encontrado');
            return;
        }

        // Obtener dirección
        this.direccionActual = this.obtenerDireccionDomicilio();
        
        if (!this.direccionActual) {
            window.revisionNotifications?.mostrarNotificacion('❌ No se pudo obtener la dirección del domicilio', 'error');
            return;
        }

        // Actualizar información en el modal
        this.actualizarInfoDireccionModal(this.direccionActual);

        // Mostrar modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Inicializar mapa después de que el modal sea visible
        setTimeout(() => {
            this.inicializarMapaModal();
        }, 300);

        console.log('🗺️ Modal de mapa abierto para:', this.direccionActual);
    }

    /**
     * Cerrar modal de mapa
     */
    cerrarModalMapa() {
        const modal = document.getElementById('mapa-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Limpiar mapa si existe
            this.limpiarMapaModal();
        }
        
        console.log('🗺️ Modal de mapa cerrado');
    }

    /**
     * Cambiar vista del mapa en modal
     */
    cambiarVistaMapaModal(tipoVista) {
        if (this.mapaModal && window.google && window.google.maps) {
            this.mapaModal.setMapTypeId(tipoVista);
            console.log(`🗺️ Vista de mapa cambiada a: ${tipoVista}`);
        }
    }

    /**
     * Copiar dirección al portapapeles
     */
    async copiarDireccion() {
        if (!this.direccionActual) {
            window.revisionNotifications?.mostrarNotificacion('❌ No hay dirección para copiar', 'error');
            return;
        }

        try {
            await navigator.clipboard.writeText(this.direccionActual);
            window.revisionNotifications?.mostrarNotificacion('📋 Dirección copiada al portapapeles', 'success');
        } catch (error) {
            // Fallback para navegadores sin soporte
            this.copiarDireccionFallback(this.direccionActual);
        }
    }

    /**
     * Abrir dirección en Google Maps
     */
    abrirEnGoogleMaps() {
        if (!this.direccionActual) {
            window.revisionNotifications?.mostrarNotificacion('❌ No hay dirección disponible', 'error');
            return;
        }

        const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(this.direccionActual)}`;
        window.open(url, '_blank');
        
        console.log('🗺️ Abriendo en Google Maps:', this.direccionActual);
    }

    /**
     * Mostrar mapa en panel lateral
     */
    async mostrarMapaEnPanel() {
        const container = document.getElementById('mapa-panel-lateral');
        if (!container) {
            console.error('Contenedor de mapa panel no encontrado');
            return;
        }

        try {
            container.classList.add('loading');
            
            const direccion = this.obtenerDireccionDomicilio();
            if (!direccion) {
                throw new Error('No se pudo obtener la dirección');
            }

            // Si Google Maps está disponible
            if (this.googleMapsLoaded && window.google && window.google.maps) {
                await this.crearMapaSimplePanel(container, direccion);
            } else {
                // Usar fallback
                this.crearMapaFallbackPanel(container, direccion);
            }

        } catch (error) {
            console.error('Error mostrando mapa en panel:', error);
            this.mostrarErrorMapaPanel(container, error.message);
        } finally {
            container.classList.remove('loading');
        }
    }

    /**
     * Obtener dirección del domicilio
     */
    obtenerDireccionDomicilio() {
        // Intentar obtener desde datos del backend
        const direccionBackend = this.obtenerDireccionDesdeDatosBackend();
        if (direccionBackend) {
            return direccionBackend;
        }

        // Fallback: obtener desde DOM
        return this.obtenerDireccionDesdeDOM();
    }

    obtenerDireccionDesdeDatosBackend() {
        // Usar datos del backend si están disponibles
        if (window.domicilioData) {
            return this.construirDireccionDesdeObjeto(window.domicilioData);
        }
        return null;
    }

    obtenerDireccionDesdeDOM() {
        const seccionDomicilio = document.querySelector('[data-seccion="domicilio"]');
        if (!seccionDomicilio) return null;

        const componentes = {
            calle: this.buscarValorPorEtiqueta(seccionDomicilio, 'Calle'),
            numero: this.buscarValorPorEtiqueta(seccionDomicilio, 'Número'),
            colonia: this.buscarValorPorEtiqueta(seccionDomicilio, 'Colonia'),
            municipio: this.buscarValorPorEtiqueta(seccionDomicilio, 'Municipio'),
            estado: this.buscarValorPorEtiqueta(seccionDomicilio, 'Estado'),
            cp: this.buscarValorPorEtiqueta(seccionDomicilio, 'Código Postal')
        };

        return this.construirDireccionDesdeComponentes(componentes);
    }

    construirDireccionDesdeObjeto(data) {
        const partes = [];
        
        if (data.calle) partes.push(data.calle);
        if (data.numero_exterior) partes.push(data.numero_exterior);
        if (data.colonia) partes.push(data.colonia);
        if (data.municipio) partes.push(data.municipio);
        if (data.estado) partes.push(data.estado);
        if (data.codigo_postal) partes.push(`CP ${data.codigo_postal}`);
        
        return partes.filter(p => p && p.trim()).join(', ');
    }

    construirDireccionDesdeComponentes(componentes) {
        const partes = [];
        
        if (componentes.calle) partes.push(componentes.calle);
        if (componentes.numero) partes.push(componentes.numero);
        if (componentes.colonia) partes.push(componentes.colonia);
        if (componentes.municipio) partes.push(componentes.municipio);
        if (componentes.estado) partes.push(componentes.estado);
        if (componentes.cp) partes.push(`CP ${componentes.cp}`);
        
        return partes.filter(p => p && p.trim()).join(', ');
    }

    buscarValorPorEtiqueta(contenedor, textoEtiqueta) {
        const labels = contenedor.querySelectorAll('label, .label, .font-semibold, .font-medium');
        
        for (const label of labels) {
            if (label.textContent.includes(textoEtiqueta)) {
                const input = label.nextElementSibling?.querySelector?.('input, select, textarea') || 
                             label.parentElement?.querySelector?.('input, select, textarea');
                
                if (input) {
                    return input.value || input.textContent?.trim();
                }
                
                const valueElement = label.nextElementSibling;
                if (valueElement && !valueElement.querySelector('input, select, textarea')) {
                    return valueElement.textContent?.trim();
                }
            }
        }
        
        return null;
    }

    // Métodos para mapa del modal
    async inicializarMapaModal() {
        const container = document.getElementById('mapa-modal-container');
        if (!container || !this.direccionActual) return;

        if (this.googleMapsLoaded && window.google && window.google.maps) {
            try {
                const { Map } = window.google.maps;
                const geocoder = new window.google.maps.Geocoder();
                
                // Geocodificar dirección
                const results = await this.geocodificarDireccion(geocoder, this.direccionActual);
                
                // Crear mapa
                this.mapaModal = new Map(container, {
                    zoom: 16,
                    center: results[0].geometry.location,
                    mapTypeId: 'roadmap'
                });
                
                // Agregar marcador
                new window.google.maps.Marker({
                    position: results[0].geometry.location,
                    map: this.mapaModal,
                    title: this.direccionActual
                });
                
            } catch (error) {
                console.error('Error inicializando mapa modal:', error);
                this.mostrarErrorMapaModal(container);
            }
        } else {
            this.mostrarFallbackMapaModal(container);
        }
    }

    async crearMapaSimplePanel(container, direccion) {
        if (!window.google || !window.google.maps) {
            throw new Error('Google Maps no disponible');
        }

        const { Map } = window.google.maps;
        const geocoder = new window.google.maps.Geocoder();
        
        try {
            const results = await this.geocodificarDireccion(geocoder, direccion);
            
            this.mapaPanel = new Map(container, {
                zoom: 15,
                center: results[0].geometry.location,
                mapTypeId: 'roadmap',
                gestureHandling: 'cooperative'
            });
            
            new window.google.maps.Marker({
                position: results[0].geometry.location,
                map: this.mapaPanel,
                title: direccion
            });
            
        } catch (error) {
            throw new Error(`Error geocodificando: ${error.message}`);
        }
    }

    crearMapaFallbackPanel(container, direccion) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                <div class="text-center p-6">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-800 mb-2">📍 Ubicación del Domicilio</h4>
                    <p class="text-sm text-gray-600 mb-4">${direccion}</p>
                    <button onclick="revisionMapHandler.abrirMapaDomicilio()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Ver en Mapa Completo
                    </button>
                </div>
            </div>
        `;
    }

    mostrarErrorMapaPanel(container, mensaje) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border border-red-200">
                <div class="text-center p-6">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-800 mb-2">🗺️ Error al cargar mapa</h4>
                    <p class="text-sm text-gray-600">${mensaje}</p>
                </div>
            </div>
        `;
    }

    // Métodos auxiliares
    actualizarInfoDireccionModal(direccion) {
        const elemento = document.getElementById('direccion-modal-completa');
        if (elemento) {
            elemento.textContent = direccion;
        }
    }

    limpiarMapaModal() {
        if (this.mapaModal) {
            this.mapaModal = null;
        }
    }

    mostrarErrorMapaModal(container) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full">
                <div class="text-center text-gray-600">
                    <p>Error al cargar el mapa</p>
                    <button onclick="location.reload()" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded">
                        Recargar
                    </button>
                </div>
            </div>
        `;
    }

    mostrarFallbackMapaModal(container) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full bg-gray-100">
                <div class="text-center p-8">
                    <h3 class="text-lg font-semibold mb-4">Mapa no disponible</h3>
                    <p class="text-gray-600 mb-4">${this.direccionActual}</p>
                    <button onclick="revisionMapHandler.abrirEnGoogleMaps()" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg">
                        Abrir en Google Maps
                    </button>
                </div>
            </div>
        `;
    }

    async geocodificarDireccion(geocoder, direccion) {
        return new Promise((resolve, reject) => {
            geocoder.geocode({ address: direccion + ', México' }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    resolve(results);
                } else {
                    reject(new Error(`Error de geocodificación: ${status}`));
                }
            });
        });
    }

    copiarDireccionFallback(texto) {
        const textArea = document.createElement('textarea');
        textArea.value = texto;
        document.body.appendChild(textArea);
        textArea.select();
        
        try {
            document.execCommand('copy');
            window.revisionNotifications?.mostrarNotificacion('📋 Dirección copiada al portapapeles', 'success');
        } catch (error) {
            window.revisionNotifications?.mostrarNotificacion('❌ No se pudo copiar la dirección', 'error');
        }
        
        document.body.removeChild(textArea);
    }

    // Exponer métodos globalmente
    exposeGlobalMethods() {
        window.abrirMapaDomicilio = this.abrirMapaDomicilio.bind(this);
        window.cerrarModalMapa = this.cerrarModalMapa.bind(this);
        window.cambiarVistaMapaModal = this.cambiarVistaMapaModal.bind(this);
        window.copiarDireccion = this.copiarDireccion.bind(this);
        window.abrirEnGoogleMaps = this.abrirEnGoogleMaps.bind(this);
    }
}

// Exportar para uso en otros módulos
window.RevisionMapHandler = RevisionMapHandler; 