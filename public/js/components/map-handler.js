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
            return true;
        } else {
            return false;
        }
    }

    /**
     * Carga dinámicamente la API de Google Maps si no está disponible
     */
    loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            if (typeof google !== 'undefined' && google.maps) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCUqfgNQ2Q4AVy8OTNMfogJceDbA0FHZKs&libraries=places';
            script.async = true;
            script.defer = true;
            
            script.onload = () => {
                resolve();
            };
            
            script.onerror = () => {
                reject('Error al cargar Google Maps API');
            };
            
            document.head.appendChild(script);
        });
    }

    /**
     * Inicializa un mapa en el contenedor especificado
     * @param {string} seccion - ID de la sección
     * @param {string} direccion - Dirección a mostrar en el mapa
     */
    initializeMap(seccion, direccion) {
        if (!this.checkGoogleMapsAPI()) {
            
            this.showMapError(seccion, 'Google Maps no está disponible');
            return;
        }

        const mapContainer = document.getElementById('mapa-' + seccion);
        if (!mapContainer) {
            
            return;
        }

        // Crear el layout del mapa con panel de información
        this.createMapLayout(mapContainer, seccion);

        // Configuración del mapa con estilo elegante
        const mapOptions = {
            zoom: 16,
            center: { lat: 19.4326, lng: -99.1332 }, // Ciudad de México por defecto
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: true,
            mapTypeControl: true,
            fullscreenControl: true,
            zoomControl: true,
            scaleControl: true,
            gestureHandling: 'cooperative',
            // Estilo elegante y profesional
            styles: [
                {
                    featureType: 'all',
                    elementType: 'geometry',
                    stylers: [{ saturation: -10 }, { lightness: 5 }]
                },
                {
                    featureType: 'water',
                    elementType: 'all',
                    stylers: [{ color: '#4A90E2' }, { lightness: 20 }]
                },
                {
                    featureType: 'road.highway',
                    elementType: 'geometry',
                    stylers: [{ color: '#E8E8E8' }, { weight: 0.8 }]
                },
                {
                    featureType: 'road.arterial',
                    elementType: 'geometry',
                    stylers: [{ color: '#F0F0F0' }, { weight: 0.6 }]
                },
                {
                    featureType: 'road.local',
                    elementType: 'geometry',
                    stylers: [{ color: '#FAFAFA' }, { weight: 0.4 }]
                },
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'simplified' }, { color: '#666666' }]
                },
                {
                    featureType: 'transit',
                    elementType: 'all',
                    stylers: [{ visibility: 'simplified' }, { saturation: -20 }]
                },
                {
                    featureType: 'landscape',
                    elementType: 'all',
                    stylers: [{ color: '#F8F8F8' }]
                }
            ]
        };

        // Crear el mapa en el contenedor específico
        const actualMapContainer = document.getElementById('actual-map-' + seccion);
        this.currentMap = new google.maps.Map(actualMapContainer, mapOptions);

        // Geocodificar la dirección si está disponible
        if (direccion && direccion !== 'Dirección no disponible') {
            this.geocodeAndMarkLocation(direccion, seccion);
        } else {
            
        }
    }

    /**
     * Crea el layout del mapa con panel de información lateral
     * @param {Element} container - Contenedor principal del mapa
     * @param {string} seccion - ID de la sección
     */
    createMapLayout(container, seccion) {
        container.innerHTML = `
            <div class="flex h-full bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Panel de información lateral -->
                <div class="w-80 bg-gradient-to-b from-gray-50 to-white border-r border-gray-200 flex flex-col">
                    <!-- Header del panel -->
                    <div class="p-4 border-b border-gray-200 bg-white">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-map-marked-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm">Análisis de Ubicación</h3>
                                <p class="text-xs text-gray-500">Domicilio empresarial</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de dirección -->
                    <div class="p-4 border-b border-gray-200">
                        <h4 class="font-medium text-gray-700 text-sm mb-2 flex items-center">
                            <i class="fas fa-building text-blue-500 mr-2 text-xs"></i>
                            Dirección Registrada
                        </h4>
                        <div id="direccion-info-${seccion}" class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                            <div class="animate-pulse">
                                <div class="h-3 bg-gray-300 rounded mb-2"></div>
                                <div class="h-3 bg-gray-300 rounded w-3/4"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Panel de calles cercanas -->
                    <div class="flex-1 p-4">
                        <h4 class="font-medium text-gray-700 text-sm mb-3 flex items-center">
                            <i class="fas fa-road text-green-500 mr-2 text-xs"></i>
                            Calles Cercanas
                        </h4>
                        <div id="calles-cercanas-${seccion}" class="space-y-2">
                            <div class="text-xs text-gray-500 italic">Cargando información...</div>
                        </div>
                    </div>
                    
                    <!-- Panel de coordenadas -->
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        <h4 class="font-medium text-gray-700 text-sm mb-2 flex items-center">
                            <i class="fas fa-crosshairs text-purple-500 mr-2 text-xs"></i>
                            Coordenadas
                        </h4>
                        <div id="coordenadas-${seccion}" class="text-xs text-gray-600 font-mono bg-white rounded px-2 py-1 border">
                            <div class="animate-pulse">
                                <div class="h-3 bg-gray-300 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contenedor del mapa -->
                <div class="flex-1 relative">
                    <div id="actual-map-${seccion}" class="w-full h-full"></div>
                    
                    <!-- Overlay de carga -->
                    <div id="map-loading-${seccion}" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center">
                        <div class="text-center">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mb-3"></div>
                            <p class="text-sm text-gray-600">Cargando mapa...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Geocodifica una dirección y coloca un marcador
     * @param {string} direccion - Dirección a geocodificar
     * @param {string} seccion - ID de la sección
     */
    geocodeAndMarkLocation(direccion, seccion) {
        if (!this.currentMap) {
            
            return;
        }

        const geocoder = new google.maps.Geocoder();
        
        // Actualizar información de dirección en el panel
        this.updateAddressInfo(seccion, direccion);
        
        // Agregar "México" al final para mejorar la precisión
        const direccionCompleta = direccion.includes('México') ? direccion : direccion + ', México';

        geocoder.geocode({ 
            address: direccionCompleta,
            region: 'MX' // Limitar a México
        }, (results, status) => {
            // Ocultar overlay de carga
            const loadingOverlay = document.getElementById('map-loading-' + seccion);
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }

            if (status === google.maps.GeocoderStatus.OK && results[0]) {
                const location = results[0].geometry.location;
                const lat = location.lat();
                const lng = location.lng();
                
                // Centrar el mapa en la ubicación
                this.currentMap.setCenter(location);
                this.currentMap.setZoom(17);

                // Actualizar coordenadas en el panel
                this.updateCoordinates(seccion, lat, lng);

                // Limpiar marcador anterior si existe
                if (this.currentMarker) {
                    this.currentMarker.setMap(null);
                }

                // Crear nuevo marcador elegante
                this.currentMarker = new google.maps.Marker({
                    position: location,
                    map: this.currentMap,
                    title: 'Domicilio de la empresa',
                    animation: google.maps.Animation.DROP,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: '#DC2626',
                        fillOpacity: 1,
                        strokeColor: '#FFFFFF',
                        strokeWeight: 3,
                        scale: 12
                    }
                });

                // Crear círculo de área de influencia
                const circle = new google.maps.Circle({
                    strokeColor: '#DC2626',
                    strokeOpacity: 0.4,
                    strokeWeight: 2,
                    fillColor: '#DC2626',
                    fillOpacity: 0.1,
                    map: this.currentMap,
                    center: location,
                    radius: 200 // 200 metros
                });

                // Crear ventana de información elegante
                const infoWindow = new google.maps.InfoWindow({
                    content: this.createElegantInfoWindow(direccion, results[0]),
                    maxWidth: 320,
                    pixelOffset: new google.maps.Size(0, -10)
                });

                // Eventos del marcador
                this.currentMarker.addListener('click', () => {
                    infoWindow.open(this.currentMap, this.currentMarker);
                });

                // Detectar calles cercanas
                this.detectNearbyStreets(location, seccion);

                
            } else {
                
                this.showGeocodeError(direccion);
                this.updateStreetsError(seccion);
            }
        });
    }

    /**
     * Actualiza la información de dirección en el panel lateral
     * @param {string} seccion - ID de la sección
     * @param {string} direccion - Dirección a mostrar
     */
    updateAddressInfo(seccion, direccion) {
        const addressContainer = document.getElementById('direccion-info-' + seccion);
        if (addressContainer) {
            addressContainer.innerHTML = `
                <div class="space-y-1">
                    <p class="text-sm font-medium text-gray-800">${direccion}</p>
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-shield-alt mr-1 text-green-500"></i>
                        <span>Dirección verificada</span>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Actualiza las coordenadas en el panel lateral
     * @param {string} seccion - ID de la sección
     * @param {number} lat - Latitud
     * @param {number} lng - Longitud
     */
    updateCoordinates(seccion, lat, lng) {
        const coordContainer = document.getElementById('coordenadas-' + seccion);
        if (coordContainer) {
            coordContainer.innerHTML = `
                <div class="space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Lat:</span>
                        <span class="text-gray-800">${lat.toFixed(6)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Lng:</span>
                        <span class="text-gray-800">${lng.toFixed(6)}</span>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Detecta las calles cercanas a una ubicación
     * @param {Object} location - Ubicación de Google Maps
     * @param {string} seccion - ID de la sección
     */
    detectNearbyStreets(location, seccion) {
        const service = new google.maps.places.PlacesService(this.currentMap);
        
        // Buscar calles y lugares cercanos
        const request = {
            location: location,
            radius: 300,
            type: ['route']
        };

        // También usar geocoding inverso para obtener componentes de dirección
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: location }, (results, status) => {
            if (status === 'OK' && results[0]) {
                this.processNearbyInfo(results, seccion);
            }
        });

        // Buscar lugares cercanos para contexto adicional
        const nearbyRequest = {
            location: location,
            radius: 200,
            type: ['establishment', 'point_of_interest']
        };

        service.nearbySearch(nearbyRequest, (results, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                this.updateNearbyPlaces(results.slice(0, 3), seccion);
            }
        });
    }

    /**
     * Procesa la información de ubicación cercana
     * @param {Array} results - Resultados de geocodificación
     * @param {string} seccion - ID de la sección
     */
    processNearbyInfo(results, seccion) {
        const streetsContainer = document.getElementById('calles-cercanas-' + seccion);
        if (!streetsContainer) return;

        const streets = [];
        const neighborhoods = [];
        
        // Procesar todos los resultados para extraer calles y colonias
        results.forEach(result => {
            result.address_components.forEach(component => {
                if (component.types.includes('route')) {
                    const streetName = component.long_name;
                    if (!streets.includes(streetName) && streetName.length > 3) {
                        streets.push(streetName);
                    }
                }
                if (component.types.includes('sublocality') || component.types.includes('neighborhood')) {
                    const neighborhood = component.long_name;
                    if (!neighborhoods.includes(neighborhood)) {
                        neighborhoods.push(neighborhood);
                    }
                }
            });
        });

        // Actualizar el contenedor
        let content = '';
        
        if (streets.length > 0) {
            content += `
                <div class="mb-3">
                    <h5 class="text-xs font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-road text-blue-500 mr-1"></i>
                        Calles Principales
                    </h5>
                    <div class="space-y-1">
                        ${streets.slice(0, 5).map(street => `
                            <div class="bg-white rounded border px-2 py-1 text-xs text-gray-700 flex items-center">
                                <i class="fas fa-minus text-gray-400 mr-2"></i>
                                ${street}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        if (neighborhoods.length > 0) {
            content += `
                <div class="mb-3">
                    <h5 class="text-xs font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-map-signs text-green-500 mr-1"></i>
                        Zona
                    </h5>
                    <div class="bg-green-50 border border-green-200 rounded px-2 py-1 text-xs text-green-700">
                        ${neighborhoods[0]}
                    </div>
                </div>
            `;
        }

        streetsContainer.innerHTML = content || '<div class="text-xs text-gray-500 italic">No se encontraron calles cercanas</div>';
    }

    /**
     * Actualiza información de lugares cercanos
     * @param {Array} places - Lugares cercanos
     * @param {string} seccion - ID de la sección
     */
    updateNearbyPlaces(places, seccion) {
        const streetsContainer = document.getElementById('calles-cercanas-' + seccion);
        if (!streetsContainer || places.length === 0) return;

        let nearbyContent = `
            <div class="mt-4 pt-3 border-t border-gray-200">
                <h5 class="text-xs font-medium text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-map-marker text-purple-500 mr-1"></i>
                    Referencias Cercanas
                </h5>
                <div class="space-y-1">
                    ${places.map(place => `
                        <div class="bg-purple-50 border border-purple-200 rounded px-2 py-1 text-xs text-purple-700 flex items-center">
                            <i class="fas fa-circle text-purple-400 mr-2" style="font-size: 6px;"></i>
                            <span class="truncate">${place.name}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        streetsContainer.innerHTML += nearbyContent;
    }

    /**
     * Actualiza el panel cuando hay error al detectar calles
     * @param {string} seccion - ID de la sección
     */
    updateStreetsError(seccion) {
        const streetsContainer = document.getElementById('calles-cercanas-' + seccion);
        if (streetsContainer) {
            streetsContainer.innerHTML = `
                <div class="text-xs text-amber-600 italic flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    No se pudo cargar información de calles
                </div>
            `;
        }
    }

    /**
     * Crea ventana de información elegante
     * @param {string} direccion - Dirección original
     * @param {Object} result - Resultado de geocodificación
     * @returns {string} HTML del contenido
     */
    createElegantInfoWindow(direccion, result) {
        const formattedAddress = result.formatted_address || direccion;
        
        return `
            <div class="p-4 max-w-sm bg-white rounded-lg shadow-lg">
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-building text-white text-sm"></i>
                    </div>
                    <div>
                        <h6 class="font-semibold text-gray-800 text-sm">Domicilio Empresarial</h6>
                        <p class="text-xs text-gray-500">Ubicación verificada</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                    <p class="text-sm text-gray-700 leading-relaxed">${direccion}</p>
                </div>
                
                ${formattedAddress !== direccion ? `
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">
                            <i class="fas fa-map-pin mr-1"></i>
                            <strong>Dirección encontrada:</strong>
                        </p>
                        <p class="text-xs text-gray-600 italic">${formattedAddress}</p>
                    </div>
                ` : ''}
                
                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                    <div class="flex items-center text-xs text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span>Geocodificado</span>
                    </div>
                    <button onclick="mapHandler.openInGoogleMaps('${formattedAddress}')" 
                            class="text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white px-3 py-1.5 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 flex items-center">
                        <i class="fas fa-external-link-alt mr-1"></i>Abrir Maps
                    </button>
                </div>
            </div>
        `;
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
     * Inicializa un mapa simple para panel lateral (solo mapa + punto)
     * @param {string} containerId - ID del contenedor del mapa
     * @param {string} direccion - Dirección a mostrar en el mapa
     */
    initializeSimpleMap(containerId, direccion) {
        if (!this.checkGoogleMapsAPI()) {
            this.showSimpleMapError(containerId, 'Google Maps no está disponible');
            return;
        }

        const mapContainer = document.getElementById(containerId);
        if (!mapContainer) {
            console.error('Contenedor del mapa no encontrado:', containerId);
            return;
        }

        // Configuración del mapa simple y elegante
        const mapOptions = {
            zoom: 16,
            center: { lat: 19.4326, lng: -99.1332 }, // Ciudad de México por defecto
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: false,
            fullscreenControl: false,
            zoomControl: true,
            scaleControl: false,
            gestureHandling: 'cooperative',
            // Estilo minimalista y limpio
            styles: [
                {
                    featureType: 'all',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }] // Ocultar todas las etiquetas
                },
                {
                    featureType: 'road',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }] // Ocultar nombres de calles
                },
                {
                    featureType: 'poi',
                    elementType: 'all',
                    stylers: [{ visibility: 'off' }] // Ocultar puntos de interés
                },
                {
                    featureType: 'transit',
                    elementType: 'all',
                    stylers: [{ visibility: 'off' }] // Ocultar transporte público
                },
                {
                    featureType: 'water',
                    elementType: 'all',
                    stylers: [{ color: '#4A90E2' }, { lightness: 20 }]
                },
                {
                    featureType: 'road',
                    elementType: 'geometry',
                    stylers: [{ color: '#F5F5F5' }, { weight: 0.5 }]
                },
                {
                    featureType: 'landscape',
                    elementType: 'all',
                    stylers: [{ color: '#FAFAFA' }]
                }
            ]
        };

        // Crear el mapa simple
        const simpleMap = new google.maps.Map(mapContainer, mapOptions);

        // Geocodificar la dirección si está disponible
        if (direccion && direccion !== 'Dirección no disponible') {
            this.geocodeSimpleLocation(direccion, simpleMap);
        } else {
            console.warn('Dirección no disponible para el mapa simple');
        }
    }

    /**
     * Geocodifica una dirección y coloca un marcador simple
     * @param {string} direccion - Dirección a geocodificar
     * @param {Object} map - Instancia del mapa de Google Maps
     */
    geocodeSimpleLocation(direccion, map) {
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
                map.setCenter(location);
                map.setZoom(16);

                // Crear marcador simple y elegante
                const marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: 'Ubicación del domicilio',
                    animation: google.maps.Animation.DROP,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: '#DC2626',
                        fillOpacity: 1,
                        strokeColor: '#FFFFFF',
                        strokeWeight: 3,
                        scale: 10
                    }
                });

                // Crear ventana de información simple
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div class="p-3 max-w-xs">
                            <div class="flex items-center mb-2">
                                <div class="w-6 h-6 bg-red-600 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h6 class="font-medium text-gray-800 text-sm">Domicilio</h6>
                            </div>
                            <p class="text-sm text-gray-600">${direccion}</p>
                        </div>
                    `,
                    maxWidth: 280
                });

                // Evento del marcador
                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });

                console.log('Mapa simple cargado correctamente para:', direccion);
            } else {
                console.error('Error al geocodificar dirección simple:', status, direccion);
                this.showSimpleGeocodeError(map, direccion);
            }
        });
    }

    /**
     * Muestra error de geocodificación en el mapa simple
     * @param {Object} map - Instancia del mapa
     * @param {string} direccion - Dirección que falló
     */
    showSimpleGeocodeError(map, direccion) {
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="p-3 max-w-xs">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <h6 class="font-medium text-gray-800 text-sm">Ubicación no encontrada</h6>
                    </div>
                    <p class="text-xs text-gray-600">${direccion}</p>
                </div>
            `,
            position: map.getCenter()
        });

        infoWindow.open(map);
    }

    /**
     * Muestra error cuando Google Maps no está disponible en mapa simple
     * @param {string} containerId - ID del contenedor
     * @param {string} message - Mensaje de error
     */
    showSimpleMapError(containerId, message) {
        const mapContainer = document.getElementById(containerId);
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="flex items-center justify-center h-full bg-gradient-to-br from-red-50 to-orange-50 rounded-lg">
                    <div class="text-center p-4">
                        <svg class="w-12 h-12 mx-auto mb-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-700 mb-1">${message}</p>
                        <p class="text-xs text-gray-500">Intente recargar la página</p>
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

// Función global para mapa simple
window.inicializarMapaSimple = function(containerId, direccion) {
    window.mapHandler.initializeSimpleMap(containerId, direccion);
};

// Función global para obtener dirección
window.obtenerDireccionCompleta = function() {
    return MapHandler.getDireccionFromForm();
};

window.addEventListener('error', function(e) {
    if (e.message && e.message.includes('google')) {
        // Silenciar errores de Google Maps para evitar interferir con otros scripts
        e.preventDefault();
        return false;
    }
});

 

