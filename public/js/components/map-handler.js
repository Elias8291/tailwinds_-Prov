/**
 * Componente para manejar mapas de Google Maps
 * Gestiona la visualización de ubicaciones en las revisiones de trámites
 */

class MapHandler {
    constructor() {
        this.currentMap = null;
        this.currentMarker = null;
        this.isGoogleMapsLoaded = false;
        this.checkGoogleMapsAPI();
    }

    /**
     * Verifica si la API de Google Maps está cargada
     */
    checkGoogleMapsAPI() {
        if (typeof google !== 'undefined' && google.maps) {
            this.isGoogleMapsLoaded = true;
            console.log('✅ Google Maps API está disponible');
        } else {
            console.warn('⚠️ Google Maps API no está cargada');
            // Intentar cargar dinámicamente si no está disponible
            this.loadGoogleMapsAPI();
        }
    }

    /**
     * Carga dinámicamente la API de Google Maps si no está disponible
     */
    loadGoogleMapsAPI() {
        if (this.isGoogleMapsLoaded) return;

        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCUqfgNQ2Q4AVy8OTNMfogJceDbA0FHZKs&libraries=places';
        script.async = true;
        script.defer = true;
        script.onload = () => {
            this.isGoogleMapsLoaded = true;
            console.log('✅ Google Maps API cargada dinámicamente');
        };
        script.onerror = () => {
            console.error('❌ Error al cargar Google Maps API');
        };
        document.head.appendChild(script);
    }

    /**
     * Inicializa un mapa en el contenedor especificado
     * @param {string} seccion - ID de la sección
     * @param {string} direccion - Dirección a mostrar en el mapa
     */
    initializeMap(seccion, direccion) {
        if (!this.isGoogleMapsLoaded) {
            console.error('❌ Google Maps API no está disponible');
            this.showMapError(seccion, 'Google Maps no está disponible');
            return;
        }

        const mapContainer = document.getElementById('mapa-' + seccion);
        if (!mapContainer) {
            console.error('❌ Contenedor de mapa no encontrado:', 'mapa-' + seccion);
            return;
        }

        // Configuración del mapa
        const mapOptions = {
            zoom: 15,
            center: { lat: 19.4326, lng: -99.1332 }, // Ciudad de México por defecto
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: true,
            mapTypeControl: true,
            fullscreenControl: true,
            zoomControl: true,
            scaleControl: true
        };

        // Crear el mapa
        this.currentMap = new google.maps.Map(mapContainer, mapOptions);

        // Geocodificar la dirección si está disponible
        if (direccion && direccion !== 'Dirección no disponible') {
            this.geocodeAndMarkLocation(direccion);
        } else {
            console.warn('⚠️ No hay dirección válida para geocodificar');
        }
    }

    /**
     * Geocodifica una dirección y coloca un marcador
     * @param {string} direccion - Dirección a geocodificar
     */
    geocodeAndMarkLocation(direccion) {
        if (!this.currentMap) {
            console.error('❌ No hay mapa inicializado');
            return;
        }

        const geocoder = new google.maps.Geocoder();
        
        // Agregar "México" al final para mejorar la precisión
        const direccionCompleta = direccion.includes('México') ? direccion : direccion + ', México';

        geocoder.geocode({ 
            address: direccionCompleta,
            region: 'MX' // Limitar a México
        }, (results, status) => {
            if (status === google.maps.GeocoderStatus.OK && results[0]) {
                const location = results[0].geometry.location;
                
                // Centrar el mapa en la ubicación
                this.currentMap.setCenter(location);
                this.currentMap.setZoom(16);

                // Limpiar marcador anterior si existe
                if (this.currentMarker) {
                    this.currentMarker.setMap(null);
                }

                // Crear nuevo marcador
                this.currentMarker = new google.maps.Marker({
                    position: location,
                    map: this.currentMap,
                    title: 'Domicilio de la empresa',
                    animation: google.maps.Animation.DROP,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });

                // Crear ventana de información
                const infoWindow = new google.maps.InfoWindow({
                    content: this.createInfoWindowContent(direccion, results[0]),
                    maxWidth: 300
                });

                // Eventos del marcador
                this.currentMarker.addListener('click', () => {
                    infoWindow.open(this.currentMap, this.currentMarker);
                });

                // Mostrar info automáticamente
                setTimeout(() => {
                    infoWindow.open(this.currentMap, this.currentMarker);
                }, 500);

                console.log('✅ Ubicación geocodificada correctamente:', direccion);
            } else {
                console.warn('⚠️ No se pudo geocodificar la dirección:', direccion, 'Status:', status);
                this.showGeocodeError(direccion);
            }
        });
    }

    /**
     * Crea el contenido de la ventana de información
     * @param {string} direccion - Dirección original
     * @param {Object} result - Resultado de geocodificación
     * @returns {string} HTML del contenido
     */
    createInfoWindowContent(direccion, result) {
        const formattedAddress = result.formatted_address || direccion;
        
        return `
            <div class="p-3 max-w-sm">
                <div class="flex items-center mb-2">
                    <i class="fas fa-building text-blue-600 mr-2"></i>
                    <h6 class="font-semibold text-gray-800">Domicilio de la empresa</h6>
                </div>
                <p class="text-sm text-gray-600 mb-2">${direccion}</p>
                ${formattedAddress !== direccion ? `
                    <p class="text-xs text-gray-500 border-t pt-2">
                        <strong>Dirección encontrada:</strong><br>
                        ${formattedAddress}
                    </p>
                ` : ''}
                <div class="flex items-center mt-2 pt-2 border-t">
                    <button onclick="mapHandler.openInGoogleMaps('${formattedAddress}')" 
                            class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition-colors">
                        <i class="fas fa-external-link-alt mr-1"></i>Abrir en Google Maps
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Abre la ubicación en Google Maps en una nueva pestaña
     * @param {string} direccion - Dirección a abrir
     */
    openInGoogleMaps(direccion) {
        const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(direccion)}`;
        window.open(url, '_blank');
    }

    /**
     * Muestra un error cuando no se puede geocodificar
     * @param {string} direccion - Dirección que falló
     */
    showGeocodeError(direccion) {
        if (!this.currentMap) return;

        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="p-3 max-w-sm">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>
                        <h6 class="font-semibold text-gray-800">Ubicación no encontrada</h6>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">No se pudo localizar la dirección:</p>
                    <p class="text-sm font-medium text-gray-700">${direccion}</p>
                    <div class="mt-3 pt-2 border-t">
                        <button onclick="mapHandler.searchManually('${direccion}')" 
                                class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-1"></i>Buscar manualmente
                        </button>
                    </div>
                </div>
            `,
            position: this.currentMap.getCenter()
        });

        infoWindow.open(this.currentMap);
    }

    /**
     * Abre búsqueda manual en Google Maps
     * @param {string} direccion - Dirección a buscar
     */
    searchManually(direccion) {
        this.openInGoogleMaps(direccion);
    }

    /**
     * Muestra error cuando Google Maps no está disponible
     * @param {string} seccion - ID de la sección
     * @param {string} message - Mensaje de error
     */
    showMapError(seccion, message) {
        const mapContainer = document.getElementById('mapa-' + seccion);
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="flex items-center justify-center h-full bg-gray-100 rounded-lg">
                    <div class="text-center text-gray-500">
                        <i class="fas fa-map-marked-alt text-4xl mb-3"></i>
                        <p class="text-sm font-medium">${message}</p>
                        <p class="text-xs mt-1">Intente recargar la página</p>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Limpia el mapa actual
     */
    cleanup() {
        if (this.currentMarker) {
            this.currentMarker.setMap(null);
            this.currentMarker = null;
        }
        this.currentMap = null;
    }

    /**
     * Obtiene la dirección completa desde el formulario de domicilio
     * @returns {string} Dirección formateada
     */
    static getDireccionFromForm() {
        try {
            // Buscar elementos de domicilio en el formulario
            const domicilioContainer = document.getElementById('contenido-domicilio');
            if (!domicilioContainer) {
                return 'Dirección no disponible';
            }

            // Obtener datos de los campos readonly o divs de información
            const campos = domicilioContainer.querySelectorAll('.bg-gray-50, input[readonly]');
            const datos = {};

            // Mapear campos comunes
            campos.forEach(campo => {
                const texto = campo.textContent || campo.value || '';
                const textoLimpio = texto.trim();
                
                if (textoLimpio && textoLimpio !== 'No especificado' && textoLimpio !== '') {
                    // Intentar identificar el tipo de campo por contexto
                    if (textoLimpio.match(/^\d{5}$/)) {
                        datos.cp = textoLimpio;
                    } else if (textoLimpio.match(/^[A-Z]{2,}$/)) {
                        if (!datos.estado) datos.estado = textoLimpio;
                        else if (!datos.municipio) datos.municipio = textoLimpio;
                    } else if (textoLimpio.length > 10 && !datos.calle) {
                        datos.calle = textoLimpio;
                    } else if (textoLimpio.match(/^\d+[A-Z]?$/)) {
                        datos.numero = textoLimpio;
                    } else if (!datos.colonia) {
                        datos.colonia = textoLimpio;
                    }
                }
            });

            // Construir dirección
            let direccion = '';
            if (datos.calle) direccion += datos.calle;
            if (datos.numero) direccion += ' ' + datos.numero;
            if (datos.colonia) direccion += ', ' + datos.colonia;
            if (datos.municipio) direccion += ', ' + datos.municipio;
            if (datos.estado) direccion += ', ' + datos.estado;
            if (datos.cp) direccion += ' ' + datos.cp;

            return direccion || 'Dirección no disponible';
        } catch (error) {
            console.error('Error al obtener dirección:', error);
            return 'Dirección no disponible';
        }
    }
}

// Crear instancia global
window.mapHandler = new MapHandler();

// Función global para compatibilidad
window.inicializarMapa = function(seccion, direccion) {
    window.mapHandler.initializeMap(seccion, direccion);
};

// Función global para obtener dirección
window.obtenerDireccionCompleta = function() {
    return MapHandler.getDireccionFromForm();
};

console.log('🗺️ MapHandler inicializado correctamente'); 