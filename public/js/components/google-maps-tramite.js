// Google Maps para Trámites - Versión simplificada e intuitiva
(function() {
    'use strict';
    
    // Función para suprimir errores de Google Maps
    const suppressGoogleMapsErrors = () => {
        const originalFetch = window.fetch;
        window.fetch = (input, init) => {
            const url = typeof input === 'string' ? input.toString() : (input ? input.url : '');
            if (url && url.includes('csp_test=true')) {
                return Promise.resolve(new Response(null, { status: 204 }));
            }
            return originalFetch(input, init);
        };
    };

    suppressGoogleMapsErrors();

    let mapsInitialized = false;

    // Estilos elegantes para el mapa
    const mapStyles = [
        {
            featureType: "poi",
            elementType: "labels",
            stylers: [{ visibility: "off" }]
        },
        {
            featureType: "transit",
            stylers: [{ visibility: "off" }]
        },
        {
            featureType: "road",
            elementType: "geometry",
            stylers: [{ color: "#f0f0f0" }]
        },
        {
            featureType: "water",
            stylers: [{ color: "#c9d6e8" }]
        }
    ];

    // Crear marcador principal elegante
    const createMarker = (map, position, address) => {
        const marker = new google.maps.Marker({
            position,
            map,
            title: 'Domicilio del Trámite',
            animation: google.maps.Animation.DROP,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 12,
                fillColor: '#9d2449',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 3
            }
        });

        // Información del marcador
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="p-3 text-center">
                    <div class="text-lg font-semibold text-gray-800 mb-2">
                        <i class="fas fa-map-marker-alt text-[#9d2449] mr-2"></i>
                        Ubicación Confirmada
                    </div>
                    <div class="text-sm text-gray-600 max-w-xs">
                        ${address}
                    </div>
                </div>
            `
        });

        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });

        return { marker, infoWindow };
    };

    // Crear círculo de área suave
    const createAreaCircle = (map, center) => {
        return new google.maps.Circle({
            strokeColor: '#9d2449',
            strokeOpacity: 0.3,
            strokeWeight: 2,
            fillColor: '#9d2449',
            fillOpacity: 0.08,
            map,
            center,
            radius: 150,
            clickable: false
        });
    };

    // Callback principal de Google Maps
    window.initGoogleMapsCallback = () => {
        if (mapsInitialized) return;
        mapsInitialized = true;

        const geocoder = new google.maps.Geocoder();
        const address = window.tramiteAddress || '';
        const defaultLocation = { lat: 19.4326, lng: -99.1332 };

        // Configuración del mapa limpia y elegante
        const mapOptions = {
            zoom: 16,
            center: defaultLocation,
            styles: mapStyles,
            mapTypeControl: false,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_BOTTOM
            },
            scaleControl: false,
            streetViewControl: true,
            streetViewControlOptions: {
                position: google.maps.ControlPosition.RIGHT_BOTTOM
            },
            fullscreenControl: false,
            gestureHandling: 'auto'
        };

        // Crear mapas
        const map = document.getElementById('google-map') 
            ? new google.maps.Map(document.getElementById('google-map'), mapOptions) 
            : null;
            
        const mapMobile = document.getElementById('google-map-mobile')
            ? new google.maps.Map(document.getElementById('google-map-mobile'), mapOptions)
            : null;

        if (!map && !mapMobile) return;

        // Procesar dirección si existe
        if (address.trim()) {
            geocoder.geocode({ address }, (results, status) => {
                if (status === 'OK') {
                    const location = results[0].geometry.location;
                    const formattedAddress = results[0].formatted_address;
                    
                    [map, mapMobile].forEach(m => {
                        if (m) {
                            m.setCenter(location);
                            
                            // Crear marcador y círculo
                            const { marker, infoWindow } = createMarker(m, location, formattedAddress);
                            const circle = createAreaCircle(m, location);
                            
                            // Mostrar información automáticamente
                            setTimeout(() => {
                                infoWindow.open(m, marker);
                            }, 1000);
                        }
                    });
                } else {
                    console.warn('No se pudo encontrar la dirección:', address);
                }
            });
        }
    };

    // Cargar Google Maps cuando sea necesario
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new MutationObserver((mutations, obs) => {
            if (document.getElementById('google-map') || document.getElementById('google-map-mobile')) {
                if (!document.querySelector('script[src*="maps.googleapis.com"]')) {
                    const script = document.createElement('script');
                    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCUqfgNQ2Q4AVy8OTNMfogJceDbA0FHZKs&callback=initGoogleMapsCallback&loading=async';
                    script.async = true;
                    script.defer = true;
                    document.head.appendChild(script);
                }
                obs.disconnect();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
})(); 