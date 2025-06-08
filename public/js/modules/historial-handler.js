export class HistorialHandler {
    constructor() {
        this.lastRFC = null;
    }

    async buscarHistorial(rfc) {
        if (!rfc) return;
        
        try {
            this.mostrarCargando();
            
            const response = await fetch(`/api/proveedor/historial?rfc=${encodeURIComponent(rfc)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.lastRFC = rfc;
            }

            this.actualizarContenidoModal(data.html);
            this.mostrarModal();

        } catch (error) {
            console.error('Error al buscar historial:', error);
            this.actualizarContenidoModal(this.generarHTMLError());
        }
    }

    mostrarModal() {
        window.openHistorialModal();
    }

    mostrarCargando() {
        const contenido = document.getElementById('historialProveedorContent');
        if (contenido) {
            contenido.innerHTML = `
                <div class="animate-pulse space-y-6">
                    <div class="bg-white rounded-xl p-6 border border-gray-200">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gray-200 rounded-xl"></div>
                            <div class="flex-1 space-y-4">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-6 border border-gray-200">
                        <div class="space-y-4">
                            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                            <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                        </div>
                    </div>
                </div>`;
        }
    }

    actualizarContenidoModal(html) {
        const contenido = document.getElementById('historialProveedorContent');
        if (contenido) {
            contenido.innerHTML = html;
        }
    }

    generarHTMLError() {
        return `
        <div class="text-center py-6">
            <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">Error</h3>
            <p class="text-gray-500 text-sm">Ocurrió un error al buscar el historial</p>
        </div>`;
    }
} 