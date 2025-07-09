@props(['seccionId', 'estado' => null, 'comentario' => null, 'tramiteId' => null])

<div class="transform transition-all duration-300 hover:scale-[1.01]" 
    x-data="revisionHandler({{ $seccionId }}, {{ json_encode($estado) }}, {{ json_encode($comentario) }}, {{ $tramiteId }})"
    x-init="init()"
    x-cloak>
    
    <!-- Card Principal -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
         :class="{
            'ring-2 ring-green-100': estado === 'aprobado',
            'ring-2 ring-red-100': estado === 'rechazado'
         }">
        
        <!-- Cabecera -->
        <div class="p-6 flex items-center justify-between bg-gradient-to-r"
             :class="{
                'from-green-50 to-green-50/30': estado === 'aprobado',
                'from-red-50 to-red-50/30': estado === 'rechazado',
                'from-gray-50 to-transparent': !estado || estado === 'pendiente'
             }">
            <div class="flex items-center space-x-4">
                <!-- Icono de Estado con Animación -->
                <div class="relative">
                    <div class="h-12 w-12 rounded-xl flex items-center justify-center transform transition-all duration-300"
                         :class="{
                            'bg-green-100 rotate-0': estado === 'aprobado',
                            'bg-red-100 rotate-0': estado === 'rechazado',
                            'bg-gray-100 rotate-0': !estado || estado === 'pendiente'
                         }">
                        <template x-if="estado === 'aprobado'">
                            <svg class="h-6 w-6 text-green-600 transform transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="estado === 'rechazado'">
                            <svg class="h-6 w-6 text-red-600 transform transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </template>
                        <template x-if="!estado || estado === 'pendiente'">
                            <svg class="h-6 w-6 text-gray-400 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                    </div>
                </div>

                <!-- Información de Estado -->
                <div>
                    <h3 class="text-lg font-semibold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                        Revisión de Sección
                    </h3>
                    <div class="mt-1 flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors duration-200"
                              :class="{
                                'bg-green-100 text-green-800': estado === 'aprobado',
                                'bg-red-100 text-red-800': estado === 'rechazado',
                                'bg-gray-100 text-gray-800': !estado || estado === 'pendiente'
                              }">
                            <span x-text="getEstadoTexto()" class="flex items-center"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Botón Expandir con Animación -->
            <button type="button" 
                    @click="togglePanel()"
                    class="group rounded-xl p-2 transition-all duration-200 hover:bg-gray-100">
                <svg class="h-6 w-6 text-gray-400 transition-transform duration-300 group-hover:text-gray-600" 
                     :class="{ 'transform rotate-180': isPanelOpen }"
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Panel de Revisión con Animaciones -->
        <div x-show="isPanelOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4">
            
            <div class="p-6 space-y-6">
                <!-- Área de Comentarios Mejorada -->
                <div class="space-y-4">
                    <label for="comentario_seccion_{{ $seccionId }}" class="block">
                        <span class="text-sm font-medium text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Comentarios de revisión
                        </span>
                        <span x-show="estado !== 'pendiente'" 
                              class="ml-2 text-xs text-gray-500 italic">
                            (puede modificar el estado y comentario)
                        </span>
                    </label>
                    <div class="relative">
                        <textarea
                            id="comentario_seccion_{{ $seccionId }}"
                            x-model="comentario"
                            rows="3"
                            :disabled="isLoading"
                            class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 text-sm transition-all duration-200"
                            :class="{ 'bg-gray-50': isLoading }"
                            placeholder="Ingrese sus observaciones aquí..."></textarea>
                        <div x-show="isLoading" 
                             class="absolute inset-0 bg-white/60 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                        </div>
                    </div>
                </div>

                <!-- Mensajes de Estado Mejorados -->
                <div class="space-y-3">
                    <template x-if="error">
                        <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700 flex items-center space-x-3 border border-red-100">
                            <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="error" class="flex-1"></span>
                            <button @click="error = null" class="text-red-400 hover:text-red-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                    <template x-if="success">
                        <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 flex items-center space-x-3 border border-green-100">
                            <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="success" class="flex-1"></span>
                            <button @click="success = null" class="text-green-400 hover:text-green-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Botones de Acción Mejorados -->
                <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-3 sm:space-y-0">
                    <button type="button"
                            @click="aprobarSeccion()"
                            :disabled="isLoading"
                            class="flex-1 inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02]"
                            :class="getBtnAprobarClasses()">
                        <template x-if="isLoading && !isAprobado()">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                        </template>
                        <svg x-show="!isLoading" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span x-text="isAprobado() ? 'Aprobado ✓' : 'Aprobar'" class="ml-1"></span>
                    </button>
                    <button type="button"
                            @click="rechazarSeccion()"
                            :disabled="isLoading"
                            class="flex-1 inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02]"
                            :class="getBtnRechazarClasses()">
                        <template x-if="isLoading && !isRechazado()">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                        </template>
                        <svg x-show="!isLoading" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span x-text="isRechazado() ? 'Rechazado ✗' : 'Rechazar'" class="ml-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('revisionHandler', (seccionId, estadoInicial, comentarioInicial, tramiteId) => ({
        seccionId: seccionId,
        comentario: comentarioInicial || '',
        estado: estadoInicial || 'pendiente',
        tramiteId: tramiteId,
        isLoading: false,
        error: null,
        success: null,
        isPanelOpen: false,

        init() {
            this.isPanelOpen = !!(this.comentario || this.estado !== 'pendiente');
            
            this.$watch('estado', (value) => {
                if (typeof window.actualizarEstadoRevision === 'function') {
                    window.actualizarEstadoRevision(this.seccionId, value);
                }
            });
        },

        isAprobado() {
            return this.estado === 'aprobado';
        },

        isRechazado() {
            return this.estado === 'rechazado';
        },

        getEstadoTexto() {
            switch(this.estado) {
                case 'aprobado': return '✓ Aprobado';
                case 'rechazado': return '✗ Rechazado';
                default: return '⏳ En revisión';
            }
        },

        getBtnAprobarClasses() {
            return this.isAprobado()
                ? 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 disabled:bg-green-300 disabled:cursor-not-allowed'
                : 'bg-purple-600 text-white hover:bg-purple-700 focus:ring-purple-500 disabled:bg-purple-300 disabled:cursor-not-allowed';
        },

        getBtnRechazarClasses() {
            return this.isRechazado()
                ? 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 disabled:bg-red-300 disabled:cursor-not-allowed'
                : 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-400 disabled:bg-red-300 disabled:cursor-not-allowed';
        },

        togglePanel() {
            this.isPanelOpen = !this.isPanelOpen;
        },

        async aprobarSeccion() {
            if (this.isLoading) return;
            
            try {
                this.isLoading = true;
                this.error = null;
                this.success = null;

                const response = await this.enviarRevision('aprobar');
                
                if (!response.ok) {
                    throw new Error(response.message || 'Error al aprobar la sección');
                }

                this.estado = 'aprobado';
                this.success = '¡Sección aprobada correctamente! ✨';
                
                setTimeout(() => {
                    this.isPanelOpen = false;
                    this.success = null;
                }, 2000);

            } catch (error) {
                this.error = error.message;
                console.error('Error al aprobar sección:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async rechazarSeccion() {
            if (this.isLoading) return;
            
            if (!this.comentario?.trim()) {
                this.error = 'Por favor, ingrese un comentario para rechazar la sección';
                return;
            }

            try {
                this.isLoading = true;
                this.error = null;
                this.success = null;

                const response = await this.enviarRevision('rechazar');
                
                if (!response.ok) {
                    throw new Error(response.message || 'Error al rechazar la sección');
                }

                this.estado = 'rechazado';
                this.success = 'Sección rechazada correctamente';
                
                setTimeout(() => {
                    this.isPanelOpen = false;
                    this.success = null;
                }, 2000);

            } catch (error) {
                this.error = error.message;
                console.error('Error al rechazar sección:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async enviarRevision(accion) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch(`/revision/${this.tramiteId}/seccion/${this.seccionId}/${accion}`, {
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

            return await response.json();
        }
    }));
});
</script>

<style>
.animate-spin-slow {
    animation: spin 3s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
@endpush 