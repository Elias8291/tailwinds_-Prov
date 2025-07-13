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
        const isMobile = window.innerWidth < 1024;
        return new google.maps.Circle({
            strokeColor: '#9d2449',
            strokeOpacity: 0.3,
            strokeWeight: 2,
            fillColor: '#9d2449',
            fillOpacity: 0.08,
            map,
            center,
            radius: isMobile ? 100 : 150, // Radio más pequeño en móvil
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

        // Elegir el contenedor de mapa correcto
        const isMobile = window.innerWidth < 1024; // lg breakpoint
        const mapContainerId = isMobile ? 'google-map-mobile' : 'google-map-desktop';
        const mapContainer = document.getElementById(mapContainerId);

        if (!mapContainer) {
            console.warn(`Contenedor de mapa no encontrado: #${mapContainerId}`);
            return;
        }

        // Configuración del mapa limpia y elegante
        const mapOptions = {
            zoom: isMobile ? 15 : 16, // Zoom más alejado en móvil
            center: defaultLocation,
            styles: mapStyles,
            mapTypeControl: false,
            zoomControl: true,
            zoomControlOptions: {
                position: isMobile ? google.maps.ControlPosition.TOP_RIGHT : google.maps.ControlPosition.RIGHT_BOTTOM
            },
            scaleControl: false,
            streetViewControl: !isMobile, // Ocultar en móvil para ahorrar espacio
            streetViewControlOptions: {
                position: google.maps.ControlPosition.RIGHT_BOTTOM
            },
            fullscreenControl: isMobile, // Mostrar solo en móvil
            fullscreenControlOptions: {
                position: google.maps.ControlPosition.TOP_LEFT
            },
            gestureHandling: isMobile ? 'greedy' : 'auto', // Mejor para móvil
            restriction: isMobile ? {
                latLngBounds: null,
                strictBounds: false
            } : undefined, // Sin restricciones en móvil para mejor UX
            maxZoom: 18,
            minZoom: 10
        };

        // Crear mapa
        const map = new google.maps.Map(mapContainer, mapOptions);

        if (!map) return;

        // Forzar redimensionamiento del mapa después de un breve delay - solo si es necesario
        setTimeout(() => {
            google.maps.event.trigger(map, 'resize');
            if (address.trim()) {
                // Re-centrar si hay una dirección específica
                const currentCenter = map.getCenter();
                if (currentCenter) {
                    map.setCenter(currentCenter);
                }
            }
        }, 500);

        // Solo observar cambios de tamaño si no es móvil para evitar redimensionamientos innecesarios
        if (!isMobile) {
            const resizeObserver = new ResizeObserver(() => {
                google.maps.event.trigger(map, 'resize');
            });
            resizeObserver.observe(mapContainer);
        }

        // Procesar dirección si existe
        if (address.trim()) {
            geocoder.geocode({ address }, (results, status) => {
                if (status === 'OK') {
                    const location = results[0].geometry.location;
                    const formattedAddress = results[0].formatted_address;
                    
                    map.setCenter(location);
                            
                    // Crear marcador y círculo
                    const { marker, infoWindow } = createMarker(map, location, formattedAddress);
                    const circle = createAreaCircle(map, location);
                            
                    // Mostrar información automáticamente
                    setTimeout(() => {
                        infoWindow.open(map, marker);
                    }, 1000);

                } else {
                    console.warn('No se pudo encontrar la dirección:', address);
                }
            });
        }
    };

    // Cargar Google Maps cuando sea necesario
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new MutationObserver((mutations, obs) => {
            const mapDesktop = document.getElementById('google-map-desktop');
            const mapMobile = document.getElementById('google-map-mobile');

            if (mapDesktop || mapMobile) {
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

        // Observer simplificado para cuando se muestren los mapas
        const mapVisibilityObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    const addedNodes = Array.from(mutation.addedNodes);
                    addedNodes.forEach((node) => {
                        if (node.nodeType === 1 && (node.id === 'google-map-mobile' || node.id === 'google-map-desktop')) {
                            setTimeout(() => {
                                if (!mapsInitialized) {
                                    window.initGoogleMapsCallback && window.initGoogleMapsCallback();
                                }
                            }, 200);
                        }
                    });
                }
            });
        });

        mapVisibilityObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
})(); 