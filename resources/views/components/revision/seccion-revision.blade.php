@props(['seccionId', 'estado' => null, 'comentario' => null])

<div class="mt-6 space-y-4" 
    x-data="seccionRevision({{ $seccionId }}, {{ json_encode($estado) }}, {{ json_encode($comentario) }})"
    x-init="init()">
    
    <!-- Área de comentarios -->
    <div class="space-y-2">
        <label for="comentario_seccion_{{ $seccionId }}" class="block text-sm font-medium text-gray-700">
            Comentarios de revisión
        </label>
        <textarea
            id="comentario_seccion_{{ $seccionId }}"
            x-model="comentario"
            rows="3"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#9d2449] focus:ring-[#9d2449] sm:text-sm"
            :disabled="estado === 'aprobado' || isLoading"
            placeholder="Ingrese sus comentarios aquí..."></textarea>
    </div>

    <!-- Mensajes de error y éxito -->
    <div class="mt-2">
        <template x-if="error">
            <div class="text-sm text-red-600" x-text="error"></div>
        </template>
        <template x-if="success">
            <div class="text-sm text-green-600" x-text="success"></div>
        </template>
    </div>

    <!-- Botones de acción -->
    <div class="flex space-x-3">
        <button
            type="button"
            @click="aprobarSeccion()"
            :disabled="isLoading || estado === 'aprobado'"
            class="inline-flex justify-center rounded-md border border-transparent bg-[#9d2449] py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-[#8a203f] focus:outline-none focus:ring-2 focus:ring-[#9d2449] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <template x-if="isLoading && estado !== 'aprobado'">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <span>Aprobar</span>
        </button>
        <button
            type="button"
            @click="rechazarSeccion()"
            :disabled="isLoading || estado === 'rechazado'"
            class="inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <template x-if="isLoading && estado !== 'rechazado'">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <span>Rechazar</span>
        </button>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('seccionRevision', (seccionId, estadoInicial, comentarioInicial) => ({
        seccionId: seccionId,
        comentario: comentarioInicial || '',
        estado: estadoInicial || '',
        isLoading: false,
        error: null,
        success: null,

        init() {
            this.seccionId = seccionId;
        },

        aprobarSeccion() {
            if (this.isLoading) return;
            
            this.isLoading = true;
            this.error = null;
            this.success = null;

            fetch(`/tramite/${window.tramiteId}/aprobar-seccion/${this.seccionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error al aprobar la sección');
                    });
                }
                return response.json();
            })
            .then(data => {
                this.estado = 'aprobado';
                this.success = 'Sección aprobada correctamente';
                
                const estadoElement = document.getElementById(`estado_seccion_${this.seccionId}`);
                if (estadoElement) {
                    estadoElement.className = 'px-3 py-1 text-sm rounded-full bg-green-100 text-green-800';
                    estadoElement.textContent = 'Aprobado';
                }
            })
            .catch(error => {
                this.error = error.message || 'Error al aprobar la sección';
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        rechazarSeccion() {
            if (this.isLoading) return;
            
            if (!this.comentario?.trim()) {
                this.error = 'El comentario es requerido para rechazar una sección';
                return;
            }

            this.isLoading = true;
            this.error = null;
            this.success = null;

            fetch(`/tramite/${window.tramiteId}/rechazar-seccion/${this.seccionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    comentario: this.comentario
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error al rechazar la sección');
                    });
                }
                return response.json();
            })
            .then(data => {
                this.estado = 'rechazado';
                this.success = 'Sección rechazada correctamente';
                
                const estadoElement = document.getElementById(`estado_seccion_${this.seccionId}`);
                if (estadoElement) {
                    estadoElement.className = 'px-3 py-1 text-sm rounded-full bg-red-100 text-red-800';
                    estadoElement.textContent = 'Rechazado';
                }
            })
            .catch(error => {
                this.error = error.message || 'Error al rechazar la sección';
            })
            .finally(() => {
                this.isLoading = false;
            });
        }
    }));
});
</script> 