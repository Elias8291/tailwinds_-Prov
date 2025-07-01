/**
 * Módulo de Gestión de Google Maps
 * Maneja la carga asíncrona y marcadores avanzados
 */
class GoogleMapsManager {
    constructor() {
        this.mapInstances = new Map();
        this.isLoaded = false;
        this.loadingPromise = null;
    }

    /**
     * Inicializar Google Maps con carga asíncrona
     */
    async init() {
        if (this.isLoaded) {
            return Promise.resolve();
        }

        if (this.loadingPromise) {
            return this.loadingPromise;
        }

        this.loadingPromise = this.loadGoogleMapsAPI();
        return this.loadingPromise;
    }

    /**
     * Cargar la API de Google Maps de forma asíncrona
     */
    loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            // Verificar si ya está cargado
            if (window.google && window.google.maps) {
                this.isLoaded = true;
                console.log('🗺️ Google Maps ya estaba cargado');
                resolve();
                return;
            }

            // Crear callback global único
            const callbackName = 'initGoogleMapsCallback_' + Date.now();
            window[callbackName] = () => {
                this.isLoaded = true;
                console.log('🗺️ Google Maps API cargada correctamente');
                delete window[callbackName]; // Limpiar callback
                resolve();
            };

            // Crear script con loading=async para mejor rendimiento
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${this.getApiKey()}&libraries=places&loading=async&callback=${callbackName}`;
            script.async = true;
            script.defer = true;
            script.onerror = () => {
                delete window[callbackName];
                reject(new Error('Error cargando Google Maps API'));
            };

            document.head.appendChild(script);
        });
    }

    /**
     * Obtener la API key (deberías moverla a una variable de entorno)
     */
    getApiKey() {
        // Por ahora usar la key existente, pero debería moverse a config
        return 'AIzaSyCUqfgNQIBvVa8EwkuRjKC7gJceDbA0FHZKs';
    }

    /**
     * Crear un mapa simple en un contenedor
     */
    async createSimpleMap(containerId, address, options = {}) {
        try {
            await this.init();

            const container = document.getElementById(containerId);
            if (!container) {
                throw new Error(`Container ${containerId} no encontrado`);
            }

            console.log(`🗺️ Inicializando mapa simple en: ${containerId} para: ${address}`);

            // Configuración por defecto
            const defaultOptions = {
                zoom: 15,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                ...options
            };

            // Geocodificar dirección
            const geocoder = new google.maps.Geocoder();
            const geocodeResult = await this.geocodeAddress(geocoder, address);
            
            if (!geocodeResult) {
                throw new Error('No se pudo geocodificar la dirección');
            }

            // Crear mapa
            const map = new google.maps.Map(container, {
                center: geocodeResult.geometry.location,
                ...defaultOptions
            });

            // Usar AdvancedMarkerElement en lugar de Marker deprecated
            if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
                const marker = new google.maps.marker.AdvancedMarkerElement({
                    map: map,
                    position: geocodeResult.geometry.location,
                    title: address
                });
            } else {
                // Fallback a Marker tradicional si AdvancedMarkerElement no está disponible
                const marker = new google.maps.Marker({
                    position: geocodeResult.geometry.location,
                    map: map,
                    title: address
                });
            }

            // Guardar instancia
            this.mapInstances.set(containerId, {
                map: map,
                address: address,
                location: geocodeResult.geometry.location
            });

            console.log(`✅ Mapa simple cargado correctamente para: ${address}`);
            return map;

        } catch (error) {
            console.error(`❌ Error creando mapa simple en ${containerId}:`, error);
            this.showMapError(containerId, 'Error cargando mapa: ' + error.message);
            throw error;
        }
    }

    /**
     * Geocodificar una dirección
     */
    geocodeAddress(geocoder, address) {
        return new Promise((resolve, reject) => {
            geocoder.geocode({ address: address }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    resolve(results[0]);
                } else {
                    console.error('Geocodificación falló:', status);
                    reject(new Error(`Geocodificación falló: ${status}`));
                }
            });
        });
    }

    /**
     * Mostrar error en el contenedor del mapa
     */
    showMapError(containerId, message) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-64 bg-gray-100 rounded-lg">
                    <div class="text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        <p class="mt-2 text-sm">${message}</p>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Obtener instancia de mapa
     */
    getMapInstance(containerId) {
        return this.mapInstances.get(containerId);
    }

    /**
     * Destruir mapa y liberar recursos
     */
    destroyMap(containerId) {
        const instance = this.mapInstances.get(containerId);
        if (instance) {
            // Limpiar listeners y otros recursos si es necesario
            this.mapInstances.delete(containerId);
            console.log(`🗺️ Mapa ${containerId} destruido`);
        }
    }

    /**
     * Construir dirección para geocodificación
     */
    buildAddress(data) {
        const parts = [];
        
        if (data.calle) parts.push(data.calle);
        if (data.numero_exterior) parts.push(data.numero_exterior);
        if (data.numero_interior) parts.push(`-${data.numero_interior}`);
        if (data.colonia) parts.push(data.colonia);
        if (data.municipio) parts.push(data.municipio);
        if (data.estado) parts.push(data.estado);
        if (data.codigo_postal) parts.push(data.codigo_postal);
        
        // Agregar país si no está presente
        if (!parts.join(', ').includes('México')) {
            parts.push('México');
        }
        
        return parts.join(', ');
    }
}

// Exportar para uso global
window.GoogleMapsManager = GoogleMapsManager; 