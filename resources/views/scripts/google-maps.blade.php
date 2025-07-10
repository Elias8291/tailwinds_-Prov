// Configuración inicial
window.googleMapsConfig = {
    loaded: false,
    loading: false,
    callbacks: [],
    error: null
};

// Función para cargar el script
(function loadGoogleMaps() {
    if (window.googleMapsConfig.loading) return;
    window.googleMapsConfig.loading = true;

    const script = document.createElement('script');
    script.type = 'text/javascript';
    script.async = true;
    script.defer = true;
    script.src = '{!! $url !!}';

    // Manejar errores de carga
    script.onerror = function() {
        window.googleMapsConfig.error = 'Error al cargar Google Maps';
        window.googleMapsConfig.loading = false;
        console.error('Error al cargar Google Maps');
    };

    // Función de callback original
    const originalCallback = window.initGoogleMaps;
    window.initGoogleMaps = function() {
        window.googleMapsConfig.loaded = true;
        window.googleMapsConfig.loading = false;
        if (typeof originalCallback === 'function') {
            originalCallback();
        }
        // Ejecutar callbacks pendientes
        while (window.googleMapsConfig.callbacks.length) {
            const callback = window.googleMapsConfig.callbacks.shift();
            callback();
        }
    };

    document.head.appendChild(script);
})(); 