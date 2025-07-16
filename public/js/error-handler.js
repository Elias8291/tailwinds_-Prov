// ===== ERROR HANDLER GLOBAL =====
// Manejo de errores JavaScript y prevención de conflictos

(function() {
    'use strict';
    
    // Prevenir que errores de PrelineUI rompan la funcionalidad
    window.addEventListener('error', function(event) {
        // Filtrar errores específicos de PrelineUI que no afectan la funcionalidad
        const prelineErrorPatterns = [
            'evtTarget.closest is not a function',
            'Cannot read property \'closest\' of',
            'Cannot read properties of null (reading \'closest\')',
            'Cannot read properties of undefined (reading \'closest\')'
        ];
        
        const isPrelineError = prelineErrorPatterns.some(pattern => 
            event.message && event.message.includes(pattern)
        );
        
        if (isPrelineError) {
            event.preventDefault();
            return false;
        }
        
        // Para otros errores, solo logear sin interrumpir
        if (event.error) {
            console.error('Error JavaScript capturado:', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                error: event.error
            });
        }
    });
    
    // Manejar errores de promesas no capturadas
    window.addEventListener('unhandledrejection', function(event) {
        console.warn('Promesa rechazada no manejada:', event.reason);
        
        // Prevenir que errores de red o AJAX rompan la app
        if (event.reason && (
            event.reason.message && event.reason.message.includes('fetch') ||
            event.reason.name === 'NetworkError' ||
            event.reason.name === 'TypeError'
        )) {
            event.preventDefault();
        }
    });
    
    // Mejorar la inicialización de PrelineUI
    function initPrelineUISecure() {
        if (typeof HSStaticMethods !== 'undefined') {
            try {
                // Verificar si el DOM está listo
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        setTimeout(() => {
                            try {
                                HSStaticMethods.autoInit();
                            } catch (error) {
                                console.warn('Error inicializando PrelineUI (se ignorará):', error);
                            }
                        }, 100);
                    });
                } else {
                    setTimeout(() => {
                        try {
                            HSStaticMethods.autoInit();
                        } catch (error) {
                            console.warn('Error inicializando PrelineUI (se ignorará):', error);
                        }
                    }, 100);
                }
            } catch (error) {
                console.warn('Error en inicialización segura de PrelineUI:', error);
            }
        }
    }
    
    // Función para reinicializar componentes de manera segura
    window.reinitPrelineSecure = function() {
        if (typeof HSStaticMethods !== 'undefined') {
            try {
                const components = ['HSSelect', 'HSDropdown', 'HSComboBox', 'HSCollapse'];
                components.forEach(component => {
                    if (typeof window[component] !== 'undefined' && window[component].autoInit) {
                        try {
                            window[component].autoInit();
                        } catch (error) {
                            console.warn(`Error reinicializando ${component}:`, error);
                        }
                    }
                });
            } catch (error) {
                console.warn('Error en reinicialización de componentes:', error);
            }
        }
    };
    
    // Función auxiliar para verificar elementos DOM
    window.isValidDOMElement = function(element) {
        return element && 
               typeof element === 'object' && 
               element.nodeType === Node.ELEMENT_NODE &&
               typeof element.closest === 'function';
    };
    
    // Función para agregar event listeners de manera segura
    window.addSafeEventListener = function(element, event, handler, options = {}) {
        if (!window.isValidDOMElement(element)) {
            console.warn('Elemento inválido para event listener:', element);
            return false;
        }
        
        try {
            const safeHandler = function(e) {
                try {
                    // Verificar que el evento tenga propiedades válidas
                    if (e.target && !window.isValidDOMElement(e.target)) {
                        e.stopPropagation();
                        return;
                    }
                    handler.call(this, e);
                } catch (error) {
                    console.warn('Error en event handler:', error);
                }
            };
            
            element.addEventListener(event, safeHandler, options);
            return true;
        } catch (error) {
            console.warn('Error agregando event listener:', error);
            return false;
        }
    };
    
    // Función para hacer fetch requests de manera segura
    window.safeFetch = async function(url, options = {}) {
        const defaultOptions = {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            ...options
        };
        
        // Agregar CSRF token si existe
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken && !defaultOptions.headers['X-CSRF-TOKEN']) {
            defaultOptions.headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }
        
        try {
            const response = await fetch(url, defaultOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
            }
            
            return response;
        } catch (error) {
            console.error('Error en safeFetch:', error);
            throw error;
        }
    };
    
    // Función para mostrar notificaciones de manera consistente
    window.showGlobalNotification = function(type, message, duration = 5000) {
        // Remover notificaciones existentes
        const existing = document.querySelectorAll('.global-notification');
        existing.forEach(el => el.remove());
        
        // Crear nueva notificación
        const notification = document.createElement('div');
        notification.className = `global-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 max-w-sm ${
            type === 'success' ? 'bg-green-100 border border-green-300 text-green-800' : 
            type === 'warning' ? 'bg-yellow-100 border border-yellow-300 text-yellow-800' :
            'bg-red-100 border border-red-300 text-red-800'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-start">
                <i class="fas ${
                    type === 'success' ? 'fa-check-circle' : 
                    type === 'warning' ? 'fa-exclamation-triangle' : 
                    'fa-exclamation-circle'
                } mr-2 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animación de entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Remover automáticamente
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }, duration);
    };
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPrelineUISecure);
    } else {
        initPrelineUISecure();
    }
    
})(); 