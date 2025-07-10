@props(['seccionId', 'estado' => null, 'comentario' => null, 'tramiteId' => null])

<div class="mt-4 border rounded-lg shadow-sm bg-white" 
    x-data="seccionRevision({{ $seccionId }}, {{ json_encode($estado) }}, {{ json_encode($comentario) }}, {{ $tramiteId }})"
    x-init="init()">
    
    <!-- Header con botón para colapsar -->
    <div class="px-6 py-4 flex items-center justify-between border-b cursor-pointer hover:bg-gray-50/70 transition-colors"
         @click="isCommentsOpen = !isCommentsOpen">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-3">
            <i class="fas fa-clipboard-check text-xl text-[#9d2449]"></i>
            <span>Revisión de Sección</span>
            <span x-show="estado === 'aprobado'" class="ml-2 px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200 shadow-sm">
                Aprobado
            </span>
            <span x-show="estado === 'rechazado'" class="ml-2 px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200 shadow-sm">
                Rechazado
            </span>
            <span x-show="!estado || estado === 'pendiente'" class="ml-2 px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm">
                Pendiente
            </span>
        </h3>
        <button type="button" class="text-gray-400 hover:text-gray-600 transition-transform" :class="{ 'transform rotate-180': isCommentsOpen }">
            <i class="fas fa-chevron-down text-sm"></i>
        </button>
    </div>

    <!-- Contenido colapsable -->
    <div x-show="isCommentsOpen" 
         x-transition
         class="p-6 space-y-5 bg-gray-50/70 backdrop-blur-sm">
        
        <!-- Área de comentarios -->
        <div>
            <label for="comentario_seccion_{{ $seccionId }}" class="block text-sm font-semibold text-gray-800 mb-2">
                <i class="fas fa-edit mr-2 text-[#9d2449]"></i>Añadir Comentarios
            </label>
            <textarea
                id="comentario_seccion_{{ $seccionId }}"
                x-model="comentario"
                rows="4"
                class="block w-full rounded-xl border-gray-300 bg-white p-4 text-sm text-gray-800 placeholder-gray-500 shadow-inner transition-all duration-300 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/30 focus:outline-none resize-y min-h-[120px]"
                :disabled="isLoading"
                placeholder="Si rechaza, este comentario será visible para el solicitante."></textarea>
        </div>

        <!-- Mensajes de error y éxito -->
        <div class="min-h-[2rem]">
            <template x-if="error">
                <div class="text-sm text-red-700 bg-red-100 border border-red-200 px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-times-circle"></i><span x-text="error"></span>
                </div>
            </template>
            <template x-if="success">
                <div class="text-sm text-green-700 bg-green-100 border border-green-200 px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-check-circle"></i><span x-text="success"></span>
                </div>
            </template>
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button
                type="button"
                @click="rechazarSeccion()"
                :disabled="isLoading || comentario.trim().length < 10"
                class="font-semibold py-2 px-5 rounded-lg text-red-600 bg-white border-2 border-red-200 hover:bg-red-50 hover:border-red-500 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-times-circle"></i>
                <span x-text="isLoading ? 'Procesando...' : 'Rechazar'"></span>
            </button>
             <button
                type="button"
                @click="aprobarSeccion()"
                :disabled="isLoading"
                class="font-semibold py-2 px-5 rounded-lg text-white bg-green-600 hover:bg-green-700 transition-all shadow-md disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span x-text="isLoading ? 'Procesando...' : 'Aprobar'"></span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('seccionRevision', (seccionId, estadoInicial, comentarioInicial, tramiteId) => ({
        seccionId: seccionId,
        comentario: comentarioInicial || '',
        estado: estadoInicial || null,
        tramiteId: tramiteId,
        isLoading: false,
        error: null,
        success: null,
        isCommentsOpen: false,

        init() {
            this.seccionId = seccionId;
            this.tramiteId = tramiteId;
            // Abrir automáticamente si hay un comentario o el estado es pendiente
            this.isCommentsOpen = !!(this.comentario || this.estado === 'pendiente' || !this.estado);
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
                this.success = data.message || 'Sección aprobada correctamente.';
                
                if (typeof window.actualizarEstadoRevision === 'function') {
                    window.actualizarEstadoRevision(this.seccionId, 'aprobado');
                }
                
                setTimeout(() => {
                    this.isCommentsOpen = false;
                    this.success = null;
                }, 2000);

            } catch (error) {
                this.error = error.message;
            } finally {
                this.isLoading = false;
            }
        },

        async rechazarSeccion() {
            if (this.isLoading) return;
            
            if (!this.comentario?.trim() || this.comentario.trim().length < 10) {
                this.error = 'El comentario es obligatorio y debe tener al menos 10 caracteres.';
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
                this.success = data.message || 'Sección rechazada correctamente.';
                
                if (typeof window.actualizarEstadoRevision === 'function') {
                    window.actualizarEstadoRevision(this.seccionId, 'rechazado');
                }
                
                setTimeout(() => {
                    this.isCommentsOpen = false;
                    this.success = null;
                }, 2000);

            } catch (error) {
                this.error = error.message;
            } finally {
                this.isLoading = false;
            }
        }
    }));
});
</script> 