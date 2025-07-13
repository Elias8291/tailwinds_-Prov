@props(['seccion', 'tramite'])

<div x-data="{
        open: false,
        loading: false,
        estado: 'pendiente',
        comentario: '',
        revisadoPor: null,
        fechaRevision: null,
        
        async cargarEstado() {
            try {
                const response = await fetch(`/revision/{{ $tramite->id }}/seccion/{{ $seccion['id'] }}/estado`);
                const data = await response.json();
                
                if (data.success) {
                    this.estado = data.data.estado;
                    this.comentario = data.data.comentario || '';
                    this.revisadoPor = data.data.revisado_por;
                    this.fechaRevision = data.data.fecha_revision;
                }
            } catch (error) {
                console.error('Error al cargar estado:', error);
            }
        },
        
        async aprobarSeccion() {
            if (this.loading) return;
            
            this.loading = true;
            try {
                const response = await fetch(`/revision/{{ $tramite->id }}/seccion/{{ $seccion['id'] }}/aprobar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        comentario: this.comentario
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.estado = 'aprobado';
                    this.revisadoPor = data.data.revisado_por;
                    this.fechaRevision = data.data.fecha_revision;
                    this.open = false;
                    
                    // Mostrar notificación de éxito
                    this.mostrarNotificacion('Sección aprobada exitosamente', 'success');
                } else {
                    this.mostrarNotificacion(data.message || 'Error al aprobar la sección', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.mostrarNotificacion('Error de conexión', 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async rechazarSeccion() {
            if (this.loading || !this.comentario.trim()) {
                this.mostrarNotificacion('El comentario es obligatorio para rechazar', 'warning');
                return;
            }
            
            this.loading = true;
            try {
                const response = await fetch(`/revision/{{ $tramite->id }}/seccion/{{ $seccion['id'] }}/rechazar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        comentario: this.comentario
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.estado = 'rechazado';
                    this.revisadoPor = data.data.revisado_por;
                    this.fechaRevision = data.data.fecha_revision;
                    this.open = false;
                    
                    // Mostrar notificación de éxito
                    this.mostrarNotificacion('Sección rechazada exitosamente', 'success');
                } else {
                    this.mostrarNotificacion(data.message || 'Error al rechazar la sección', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.mostrarNotificacion('Error de conexión', 'error');
            } finally {
                this.loading = false;
            }
        },
        
        mostrarNotificacion(mensaje, tipo) {
            // Crear una notificación temporal
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
                tipo === 'success' ? 'bg-green-500 text-white' :
                tipo === 'error' ? 'bg-red-500 text-white' :
                tipo === 'warning' ? 'bg-yellow-500 text-black' : 'bg-blue-500 text-white'
            }`;
            notification.textContent = mensaje;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }"
    x-init="cargarEstado()"
    class="bg-gray-50">
    
    <div class="p-4 text-center">
        <!-- Indicador de Estado -->
        <div class="mb-3" x-show="estado !== 'pendiente'">
            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                 :class="{
                     'bg-green-100 text-green-800': estado === 'aprobado',
                     'bg-red-100 text-red-800': estado === 'rechazado'
                 }">
                <i class="fas mr-2" 
                   :class="{
                       'fa-check-circle': estado === 'aprobado',
                       'fa-times-circle': estado === 'rechazado'
                   }"></i>
                <span x-text="estado === 'aprobado' ? 'Aprobado' : 'Rechazado'"></span>
            </div>
            <p class="text-xs text-gray-500 mt-1" x-show="fechaRevision">
                <span x-text="fechaRevision"></span> por <span x-text="revisadoPor"></span>
            </p>
        </div>
        
        <button @click="open = !open" 
                class="inline-flex items-center px-6 py-2 text-sm font-bold rounded-full text-white shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                :class="open ? 'bg-red-500 hover:bg-red-600' : 'bg-primary hover:bg-primary-dark'"
                :disabled="loading">
            <i class="fas transition-transform duration-300" 
               :class="open ? 'fa-times rotate-90' : 'fa-pen-to-square'"></i>
            <span class="ml-2" x-text="open ? 'Cerrar Revisión' : 'Evaluar Sección'"></span>
            <div x-show="loading" class="ml-2">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </button>
    </div>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="border-t border-primary-100 bg-primary-50/50 p-4" style="display: none;">
        
        <div class="max-w-2xl mx-auto">
            <div class="space-y-4">
                <div>
                    <label for="comentario-seccion-{{$seccion['id']}}" class="block text-sm font-semibold text-primary-dark mb-2">
                        <i class="fas fa-edit mr-1 text-primary"></i> Observaciones Generales de la Sección
                    </label>
                    <textarea x-model="comentario" 
                              id="comentario-seccion-{{$seccion['id']}}" 
                              rows="3"
                              class="w-full p-3 text-sm bg-white border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary transition duration-150 ease-in-out"
                              placeholder="Añadir un comentario para la sección '{{ $seccion['nombre'] }}'..."
                              :disabled="loading"></textarea>
                </div>

                <div class="flex flex-col sm:flex-row justify-end items-center gap-3 pt-2">
                    <button @click="rechazarSeccion()" 
                            :disabled="loading"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2 border border-primary text-sm font-bold rounded-full text-primary bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-150 disabled:opacity-50">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span>Rechazar Sección</span>
                    </button>
                    <button @click="aprobarSeccion()" 
                            :disabled="loading"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2 border border-transparent text-sm font-bold rounded-full text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-dark transition-all duration-150 disabled:opacity-50">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>Aprobar Sección</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> 