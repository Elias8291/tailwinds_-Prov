/**
 * MapHandler - Gestor de mapas para formularios
 * Versión sin console.log para mejor performance
 */
class MapHandler {
    constructor() {
        this.maps = {};
        this.markers = {};
        this.isGoogleMapsLoaded = false;
        this.init();
    }

    /**
     * Inicializar el MapHandler
     */
    init() {
        this.checkGoogleMapsAPI();
    }

    /**
     * Verificar si Google Maps API está disponible
     */
    checkGoogleMapsAPI() {
        if (typeof google !== 'undefined' && google.maps) {
            this.isGoogleMapsLoaded = true;
        } else {
            // Intentar cargar dinámicamente si no está disponible
            this.loadGoogleMapsAPI();
        }
    }

    /**
     * Cargar Google Maps API dinámicamente
     */
    loadGoogleMapsAPI() {
        if (this.isGoogleMapsLoaded) return;

        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCUqfgNQ2Q4AVy8OTNMfogJceDbA0FHZKs&libraries=places';
        script.async = true;
        script.defer = true;
        script.onload = () => {
            this.isGoogleMapsLoaded = true;
        };
        script.onerror = () => {
            // Error loading API
        };
        document.head.appendChild(script);
    }

    /**
     * Inicializar mapa para una sección específica
     */
    initializeMap(seccion, direccion) {
        if (!this.isGoogleMapsLoaded) {
            this.showMapError(seccion, 'Google Maps no está disponible');
            return false;
        }
        
        const mapContainer = document.getElementById('mapa-' + seccion);
        if (!mapContainer) {
            return false;
        }

        // Configuración del mapa
        const mapOptions = {
            zoom: 15,
            center: { lat: 19.4326, lng: -99.1332 }, // CDMX por defecto
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: true,
            fullscreenControl: true,
            zoomControl: true
        };

        // Crear el mapa
        this.maps[seccion] = new google.maps.Map(mapContainer, mapOptions);

        // Inicializar servicios
        this.initializeServices(seccion);

        // Si hay dirección, geocodificar
        if (direccion && direccion !== 'Dirección no disponible') {
            this.geocodeAddress(direccion, (results, status) => {
                if (status === 'OK' && results[0]) {
                    const location = results[0].geometry.location;
                    this.maps[seccion].setCenter(location);
                    this.addMarker(seccion, location, direccion);
                }
            });
        }

        return true;
    }

    /**
     * Inicializar servicios de Google Maps
     */
    initializeServices(seccion) {
        if (!this.maps[seccion]) return;

        // Servicio de geocodificación
        this.geocoder = new google.maps.Geocoder();

        // Servicio de Places
        if (google.maps.places) {
            this.placesService = new google.maps.places.PlacesService(this.maps[seccion]);
        }

        // Listener para clicks en el mapa
        google.maps.event.addListener(this.maps[seccion], 'click', (event) => {
            this.updateAddressFromMap(event.latLng, seccion);
        });
    }

    /**
     * Geocodificar una dirección
     */
    geocodeAddress(direccion, callback) {
        if (!direccion || direccion.trim() === '') {
            return;
        }

        if (!this.geocoder) {
            this.geocoder = new google.maps.Geocoder();
        }

        this.geocoder.geocode({ address: direccion }, (results, status) => {
            if (status === 'OK' && results && results.length > 0) {
                if (callback) callback(results, status);
            } else {
                if (callback) callback(null, status);
            }
        });
    }

    /**
     * Agregar marcador al mapa
     */
    addMarker(seccion, location, title = '') {
        if (!this.maps[seccion]) {
            return;
        }

        // Remover marcador anterior si existe
        if (this.markers[seccion]) {
            this.markers[seccion].setMap(null);
        }

        // Crear nuevo marcador
        this.markers[seccion] = new google.maps.Marker({
            position: location,
            map: this.maps[seccion],
            title: title,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        // Listener para cuando se arrastra el marcador
        google.maps.event.addListener(this.markers[seccion], 'dragend', (event) => {
            this.updateAddressFromMap(event.latLng, seccion);
        });
    }

    /**
     * Actualizar dirección desde el mapa
     */
    updateAddressFromMap(position, seccion) {
        if (!this.maps[seccion]) {
            return;
        }

        // Geocodificación inversa
        if (!this.geocoder) {
            this.geocoder = new google.maps.Geocoder();
        }

        this.geocoder.geocode({ location: position }, (results, status) => {
            if (status === 'OK' && results && results.length > 0) {
                const address = results[0];
                
                // Actualizar marcador
                this.addMarker(seccion, position, address.formatted_address);
                
                // Actualizar campos del formulario si existe función global
                if (typeof window.actualizarCamposDireccion === 'function') {
                    window.actualizarCamposDireccion(address);
                }
            }
        });
    }

    /**
     * Mostrar error en el contenedor del mapa
     */
    showMapError(seccion, mensaje) {
        const mapContainer = document.getElementById('mapa-' + seccion);
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="flex items-center justify-center h-full bg-gray-100 rounded-lg">
                    <div class="text-center text-gray-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-2"></i>
                        <p>${mensaje}</p>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Limpiar recursos del mapa
     */
    cleanup() {
        Object.keys(this.maps).forEach(seccion => {
            if (this.maps[seccion]) {
                google.maps.event.clearInstanceListeners(this.maps[seccion]);
                delete this.maps[seccion];
            }
            if (this.markers[seccion]) {
                this.markers[seccion].setMap(null);
                delete this.markers[seccion];
            }
        });
    }

    /**
     * Obtener dirección desde formulario
     */
    static getDireccionFromForm() {
        const campos = [
            document.getElementById('calle')?.value || '',
            document.getElementById('numero_exterior')?.value || '',
            document.getElementById('numero_interior')?.value || '',
            document.getElementById('colonia')?.value || '',
            document.getElementById('codigo_postal')?.value || '',
            document.getElementById('municipio')?.value || '',
            document.getElementById('estado')?.value || ''
        ];
        
        return campos.filter(campo => campo.trim() !== '').join(', ');
    }
}

// Inicializar MapHandler global
window.mapHandler = new MapHandler();

// Función global para obtener dirección completa
window.obtenerDireccionCompleta = function() {
    return MapHandler.getDireccionFromForm();
};

// Manejo de errores de Google Maps
window.addEventListener('error', function(e) {
    if (e.message && e.message.includes('google')) {
        // Silenciar errores de Google Maps para evitar interferir con otros scripts
        e.preventDefault();
        return false;
    }
}); 