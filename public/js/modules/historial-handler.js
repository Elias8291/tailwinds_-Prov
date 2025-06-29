/**
 * HistorialHandler - Manejo del historial de proveedores
 * Versión sin console.log para mejor performance
 */

export class HistorialHandler {
    constructor() {
        this.lastRFC = null;
    }

    async buscarHistorial(rfc) {
        if (!rfc) return;
        
        try {
            this.mostrarCargando();
            
            const response = await fetch(`/api/historial-proveedor/${rfc}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (response.ok) {
                this.lastRFC = rfc;
                return data.historial || [];
            } else {
                throw new Error(data.error || 'Error al buscar historial');
            }
        } catch (error) {
            throw error;
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