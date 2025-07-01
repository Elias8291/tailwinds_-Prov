/**
 * Módulo de Sistema de Notificaciones
 * Gestiona todas las notificaciones y alertas del sistema de revisión
 */
class RevisionNotifications {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        console.log('🔔 Iniciando Notifications System...');
        this.createNotificationContainer();
    }

    createNotificationContainer() {
        // Crear contenedor si no existe
        let container = document.getElementById('notification-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
        }
        this.container = container;
    }

    /**
     * Mostrar notificación
     * @param {string} mensaje - Mensaje a mostrar
     * @param {string} tipo - Tipo: success, error, warning, info
     * @param {number} duracion - Duración en ms (default: 4000)
     */
    mostrarNotificacion(mensaje, tipo = 'info', duracion = 4000) {
        const notificacion = this.createNotificationElement(mensaje, tipo);
        this.container.appendChild(notificacion);

        // Animación de entrada
        setTimeout(() => {
            notificacion.classList.add('show');
        }, 10);

        // Auto-remover
        setTimeout(() => {
            this.removeNotification(notificacion);
        }, duracion);

        console.log(`🔔 Notificación ${tipo}: ${mensaje}`);
    }

    createNotificationElement(mensaje, tipo) {
        const notificacion = document.createElement('div');
        notificacion.className = `notification ${this.getNotificationClasses(tipo)} transform translate-x-full opacity-0 transition-all duration-300 ease-in-out`;
        
        notificacion.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${this.getNotificationIcon(tipo)}
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">${mensaje}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button type="button" class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        `;

        return notificacion;
    }

    getNotificationClasses(tipo) {
        const classes = {
            'success': 'bg-green-50 border border-green-200 text-green-800',
            'error': 'bg-red-50 border border-red-200 text-red-800',
            'warning': 'bg-yellow-50 border border-yellow-200 text-yellow-800',
            'info': 'bg-blue-50 border border-blue-200 text-blue-800'
        };
        return `max-w-sm p-4 rounded-lg shadow-lg ${classes[tipo] || classes['info']}`;
    }

    getNotificationIcon(tipo) {
        const icons = {
            'success': '<svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>',
            'error': '<svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>',
            'warning': '<svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
            'info': '<svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
        };
        return icons[tipo] || icons['info'];
    }

    removeNotification(notificacion) {
        notificacion.classList.remove('show');
        notificacion.classList.add('translate-x-full', 'opacity-0');
        
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.parentNode.removeChild(notificacion);
            }
        }, 300);
    }

    // Métodos específicos para diferentes tipos
    success(mensaje, duracion) {
        this.mostrarNotificacion(mensaje, 'success', duracion);
    }

    error(mensaje, duracion) {
        this.mostrarNotificacion(mensaje, 'error', duracion);
    }

    warning(mensaje, duracion) {
        this.mostrarNotificacion(mensaje, 'warning', duracion);
    }

    info(mensaje, duracion) {
        this.mostrarNotificacion(mensaje, 'info', duracion);
    }
}

// CSS para las notificaciones
const notificationStyles = `
<style>
.notification.show {
    transform: translateX(0) !important;
    opacity: 1 !important;
}
</style>
`;

// Inyectar estilos
if (!document.querySelector('#notification-styles')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'notification-styles';
    styleElement.innerHTML = notificationStyles;
    document.head.appendChild(styleElement);
}

// Exportar globalmente
window.RevisionNotifications = RevisionNotifications; 