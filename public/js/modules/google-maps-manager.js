/**
 * Módulo de Gestión de Google Maps
 * Maneja la carga asíncrona y marcadores avanzados
 */
class GoogleMapsManager {
    constructor(apiKey) {
        this.apiKey = apiKey;
        this.maps = new Map();
    }

    async loadGoogleMapsScript() {
        if (window.google && window.google.maps) {
            return Promise.resolve();
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}`;
            script.async = true;
            script.defer = true;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    async initializeMap(elementId, lat, lng, options = {}) {
        try {
            await this.loadGoogleMapsScript();

            const defaultOptions = {
                zoom: 16,
                center: { lat, lng },
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                zoomControl: true,
                styles: [
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'off' }]
                    }
                ]
            };

            const mapOptions = { ...defaultOptions, ...options };
            const element = document.getElementById(elementId);
            
            if (!element) {
                throw new Error(`Element with id "${elementId}" not found`);
            }

            const map = new google.maps.Map(element, mapOptions);
            const marker = new google.maps.Marker({
                position: { lat, lng },
                map: map,
                animation: google.maps.Animation.DROP
            });

            this.maps.set(elementId, { map, marker });
            return { map, marker };
        } catch (error) {
            console.error('Error initializing map:', error);
            throw error;
        }
    }

    updateMarkerPosition(elementId, lat, lng) {
        const mapData = this.maps.get(elementId);
        if (!mapData) {
            throw new Error(`No map found for element "${elementId}"`);
        }

        const { map, marker } = mapData;
        const position = new google.maps.LatLng(lat, lng);
        marker.setPosition(position);
        map.panTo(position);
    }

    destroyMap(elementId) {
        const mapData = this.maps.get(elementId);
        if (mapData) {
            const { map, marker } = mapData;
            marker.setMap(null);
            map.setDiv(null);
            this.maps.delete(elementId);
        }
    }
}

// Exportar la clase para su uso
window.GoogleMapsManager = GoogleMapsManager; 