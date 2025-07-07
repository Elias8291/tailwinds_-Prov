@props(['seccionId', 'estado' => null, 'comentario' => null, 'tramiteId' => null])

<div class="mt-4 border rounded-lg shadow-sm bg-white" 
    x-data="seccionRevision({{ $seccionId }}, {{ json_encode($estado) }}, {{ json_encode($comentario) }}, {{ $tramiteId }})"
    x-init="init()">
    
    <!-- Header con botón para colapsar -->
    <div class="px-6 py-4 flex items-center justify-between border-b cursor-pointer hover:bg-gray-50 transition-colors"
         @click="isCommentsOpen = !isCommentsOpen">
        <h3 class="text-lg font-medium text-gray-900">
            Revisión de Sección
            <span x-show="estado === 'aprobado'" class="ml-2 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 border border-green-200">
                ✓ Aprobado
            </span>
            <span x-show="estado === 'rechazado'" class="ml-2 px-3 py-1 text-sm rounded-full bg-red-100 text-red-800 border border-red-200">
                ✗ Rechazado
            </span>
            <span x-show="!estado || estado === 'pendiente'" class="ml-2 px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                ⏳ Pendiente
            </span>
        </h3>
        <button type="button" class="text-gray-400 hover:text-gray-500">
            <svg class="h-5 w-5 transition-transform" 
                 :class="{ 'transform rotate-180': isCommentsOpen }"
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <!-- Contenido colapsable -->
    <div x-show="isCommentsOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="p-6 space-y-5">
        
        <!-- Área de comentarios -->
        <div class="space-y-3">
            <label for="comentario_seccion_{{ $seccionId }}" class="block text-sm font-medium text-gray-700 mb-2">
                Comentarios de revisión
                <span x-show="estado === 'aprobado' || estado === 'rechazado'" class="ml-2 text-xs text-gray-500 font-normal">
                    (puedes modificar el estado y comentario)
                </span>
            </label>
            <textarea
                id="comentario_seccion_{{ $seccionId }}"
                x-model="comentario"
                rows="3"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-[#9d2449] focus:ring-[#9d2449] sm:text-sm px-3 py-2"
                :disabled="isLoading"
                placeholder="Ingrese sus comentarios aquí..."></textarea>
        </div>

        <!-- Mensajes de error y éxito -->
        <div class="mt-3">
            <template x-if="error">
                <div class="text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-3 rounded-lg" x-text="error"></div>
            </template>
            <template x-if="success">
                <div class="text-sm text-green-700 bg-green-50 border border-green-200 px-4 py-3 rounded-lg" x-text="success"></div>
            </template>
        </div>

        <!-- Botones de acción -->
        <div class="flex space-x-4 pt-2">
            <button
                type="button"
                @click="aprobarSeccion()"
                :disabled="isLoading"
                :class="estado === 'aprobado' 
                    ? 'inline-flex items-center justify-center rounded-lg border border-transparent bg-green-600 py-2.5 px-5 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'
                    : 'inline-flex items-center justify-center rounded-lg border border-transparent bg-[#9d2449] py-2.5 px-5 text-sm font-medium text-white shadow-sm hover:bg-[#8a203f] focus:outline-none focus:ring-2 focus:ring-[#9d2449] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'">
                <template x-if="isLoading && estado !== 'aprobado'">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <svg x-show="!isLoading" class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="estado === 'aprobado' ? 'Aprobado ✓' : 'Aprobar'"></span>
            </button>
            <button
                type="button"
                @click="rechazarSeccion()"
                :disabled="isLoading"
                :class="estado === 'rechazado' 
                    ? 'inline-flex items-center justify-center rounded-lg border border-transparent bg-red-700 py-2.5 px-5 text-sm font-medium text-white shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'
                    : 'inline-flex items-center justify-center rounded-lg border border-transparent bg-red-600 py-2.5 px-5 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'">
                <template x-if="isLoading && estado !== 'rechazado'">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <svg x-show="!isLoading" class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span x-text="estado === 'rechazado' ? 'Rechazado ✗' : 'Rechazar'"></span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('seccionRevision', (seccionId, estadoInicial, comentarioInicial, tramiteId) => ({
        seccionId: seccionId,
        comentario: comentarioInicial || '',
        estado: estadoInicial || '',
        tramiteId: tramiteId,
        isLoading: false,
        error: null,
        success: null,
        isCommentsOpen: false,

        init() {
            this.seccionId = seccionId;
            this.tramiteId = tramiteId;
            // Abrir automáticamente si hay un comentario o estado
            this.isCommentsOpen = !!(this.comentario || this.estado);
        },

        async aprobarSeccion() {
            if (this.isLoading) return;
            
            this.isLoading = true;
            this.error = null;
            this.success = null;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(`/revision/${this.tramiteId}/seccion/${this.seccionId}/aprobar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        comentario: this.comentario || null
                    })
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Error al aprobar la sección');
                }

                this.estado = 'aprobado';
                this.success = 'Sección aprobada correctamente';
                
                // Disparar evento de actualización para el botón de terminar revisión
                if (typeof window.actualizarEstadoRevision === 'function') {
                    window.actualizarEstadoRevision(this.seccionId, 'aprobado');
                }
                
                // Cerrar el panel después de aprobar
                setTimeout(() => {
                    this.isCommentsOpen = false;
                }, 1500);

                // Actualizar la vista si es necesario
                if (typeof window.actualizarProgresoTramite === 'function') {
                    window.actualizarProgresoTramite();
                }
            } catch (error) {
                this.error = error.message || 'Error al aprobar la sección';
                console.error('Error al aprobar sección:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async rechazarSeccion() {
            if (this.isLoading) return;
            
            if (!this.comentario?.trim()) {
                this.error = 'El comentario es requerido para rechazar una sección';
                return;
            }

            this.isLoading = true;
            this.error = null;
            this.success = null;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(`/revision/${this.tramiteId}/seccion/${this.seccionId}/rechazar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        comentario: this.comentario
                    })
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Error al rechazar la sección');
                }

                this.estado = 'rechazado';
                this.success = 'Sección rechazada correctamente';
                
                // Disparar evento de actualización para el botón de terminar revisión
                if (typeof window.actualizarEstadoRevision === 'function') {
                    window.actualizarEstadoRevision(this.seccionId, 'rechazado');
                }
                
                // Cerrar el panel después de rechazar
                setTimeout(() => {
                    this.isCommentsOpen = false;
                }, 1500);

                // Actualizar la vista si es necesario
                if (typeof window.actualizarProgresoTramite === 'function') {
                    window.actualizarProgresoTramite();
                }
            } catch (error) {
                this.error = error.message || 'Error al rechazar la sección';
                console.error('Error al rechazar sección:', error);
            } finally {
                this.isLoading = false;
            }
        }
    }));
});
</script> 