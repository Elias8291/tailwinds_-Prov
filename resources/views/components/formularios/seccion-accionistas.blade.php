@props(['title' => 'Accionistas', 'tramite' => null, 'datosAccionistas' => [], 'accionistas' => [], 'readonly' => false])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8" 
     @if(!$readonly) x-data="accionistasData()" x-init="init()" @endif>
    <!-- Encabezado con icono -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Información sobre los accionistas de la empresa</p>
        </div>
    </div>

    @if($readonly)
        <!-- Vista de solo lectura para revisión -->
        <div class="space-y-6">
            @if(count($accionistas) > 0)
                @php
                    $totalPorcentaje = 0;
                @endphp
                
                @foreach($accionistas as $index => $accionista)
                    @php
                        $totalPorcentaje += floatval($accionista['porcentaje_participacion'] ?? $accionista['porcentaje'] ?? 0);
                    @endphp
                    
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900">
                                Accionista {{ $index + 1 }}
                            </h4>
                            <span class="px-3 py-1 bg-[#9d2449] text-white text-sm font-medium rounded-full">
                                {{ number_format(floatval($accionista['porcentaje_participacion'] ?? $accionista['porcentaje'] ?? 0), 2) }}%
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                                <div class="px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-gray-700">
                                    {{ $accionista['accionista']['nombre'] ?? $accionista['nombre'] ?? 'No especificado' }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Paterno</label>
                                <div class="px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-gray-700">
                                    {{ $accionista['accionista']['apellido_paterno'] ?? $accionista['apellido_paterno'] ?? 'No especificado' }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Materno</label>
                                <div class="px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-gray-700">
                                    {{ $accionista['accionista']['apellido_materno'] ?? $accionista['apellido_materno'] ?? 'No especificado' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Resumen de porcentajes -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-blue-700">Total de participación:</span>
                        <span class="text-lg font-bold {{ $totalPorcentaje == 100 ? 'text-green-600' : 'text-orange-600' }}">
                            {{ number_format($totalPorcentaje, 2) }}%
                        </span>
                    </div>
                    @if($totalPorcentaje != 100)
                    <div class="mt-2 text-xs text-orange-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        El total debe sumar exactamente 100%
                    </div>
                    @endif
                </div>
            @else
                <!-- Mensaje cuando no hay accionistas -->
                <div class="text-center py-8">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <i class="fas fa-users text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">No hay accionistas registrados para este trámite.</p>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Vista editable normal (código existente) -->
        <!-- Alert de Errores -->
        <div x-show="showError" x-cloak class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                <p class="text-red-700 text-sm" x-text="errorMessage"></p>
            </div>
        </div>

        <!-- Alert de Éxito -->
        <div x-show="showSuccess" x-cloak class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-700 text-sm" x-text="successMessage"></p>
            </div>
        </div>

        <form class="space-y-8" @submit.prevent="guardarAccionistas" x-ref="accionistasForm">
            <input type="hidden" name="action" value="next">
            <input type="hidden" name="seccion" value="4">
            <input type="hidden" name="tramite_id" :value="tramiteId">

            <div class="space-y-4">
                <!-- Contenedor de Accionistas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 mb-5 max-h-[500px] overflow-y-auto p-1">
                    <template x-for="(accionista, index) in accionistas" :key="index">
                        <div class="relative bg-white rounded-lg shadow-sm transition-all duration-300 hover:shadow-md overflow-hidden border-l-3"
                             :class="accionista.expanded ? 'col-span-full bg-white border-l-[#9d2449]' : 'border-l-blue-400 cursor-pointer'"
                             @click="!accionista.expanded && toggleAccionista(index)">
                            
                            <!-- Vista compacta (no expandida) -->
                            <div x-show="!accionista.expanded" class="relative">
                                <!-- Porcentaje destacado en la esquina superior derecha -->
                                <div class="absolute top-2 right-2 flex flex-col items-center">
                                    <div class="bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white px-3 py-1 rounded-full shadow-md">
                                        <span class="text-sm font-bold" x-text="(parseFloat(accionista.porcentaje) || 0).toFixed(1) + '%'"></span>
                                    </div>
                                    <div class="w-0 h-0 border-l-[6px] border-r-[6px] border-t-[6px] border-l-transparent border-r-transparent border-t-[#8a203f] -mt-[1px]"></div>
                                </div>
                                
                                <!-- Contenido principal -->
                                <div class="flex items-center p-3 pr-16">
                                    <!-- Número del accionista -->
                                    <div class="w-8 h-8 bg-[#9d2449] text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 flex-shrink-0 shadow-md">
                                        <span x-text="index + 1"></span>
                                    </div>
                                    
                                    <!-- Información del accionista -->
                                    <div class="flex-grow min-w-0">
                                        <div class="text-sm font-semibold text-gray-900 truncate mb-1">
                                            <span x-text="accionista.nombre || 'Sin nombre'"></span>
                                            <span x-text="accionista.apellido_paterno ? ' ' + accionista.apellido_paterno : ''"></span>
                                        </div>
                                        <div class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            <span>Accionista</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Vista expandida -->
                            <div x-show="accionista.expanded" x-cloak>
                                <!-- Header de la tarjeta expandida -->
                                <div class="flex justify-between items-center p-4 bg-gradient-to-r from-[#9d2449]/5 to-[#8a203f]/5 border-b border-gray-100">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-[#9d2449] text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 shadow-md">
                                            <span x-text="index + 1"></span>
                                        </div>
                                        <div>
                                            <h4 class="text-base font-semibold text-gray-900" x-text="`Accionista ${index + 1}`"></h4>
                                            <p class="text-xs text-gray-500">Información detallada</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3">
                                        <!-- Indicador de porcentaje actual -->
                                        <div class="bg-white border-2 border-[#9d2449] px-3 py-1 rounded-full shadow-sm">
                                            <span class="text-sm font-bold text-[#9d2449]" x-text="(parseFloat(accionista.porcentaje) || 0).toFixed(1) + '%'"></span>
                                        </div>
                                        
                                        <!-- Botón eliminar -->
                                        <button type="button" 
                                                @click.stop="eliminarAccionista(index)"
                                                x-show="accionistas.length > 1"
                                                class="w-8 h-8 bg-red-50 text-red-500 hover:bg-red-100 rounded-full flex items-center justify-center transition-colors duration-200 shadow-sm">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                        
                                        <!-- Botón colapsar -->
                                        <button type="button" 
                                                @click.stop="toggleAccionista(index)"
                                                class="w-8 h-8 bg-gray-50 text-gray-500 hover:bg-gray-100 rounded-full flex items-center justify-center transition-colors duration-200 shadow-sm">
                                            <i class="fas fa-chevron-up text-sm"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Campos del formulario expandido -->
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Nombre -->
                                    <div>
                                        <label :for="`accionista_nombre_${index}`" class="block text-xs font-medium text-gray-700 mb-1">
                                            Nombre <span class="text-[#9d2449]">*</span>
                                        </label>
                                        <input type="text" 
                                               :id="`accionista_nombre_${index}`"
                                               :name="`accionistas[${index}][nombre]`"
                                               x-model="accionista.nombre"
                                               @input="updateAccionista(index)"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449] transition-colors duration-200" 
                                               placeholder="Nombre(s)"
                                               required>
                                    </div>

                                    <!-- Apellido Paterno -->
                                    <div>
                                        <label :for="`accionista_apellido_paterno_${index}`" class="block text-xs font-medium text-gray-700 mb-1">
                                            Apellido Paterno <span class="text-[#9d2449]">*</span>
                                        </label>
                                        <input type="text" 
                                               :id="`accionista_apellido_paterno_${index}`"
                                               :name="`accionistas[${index}][apellido_paterno]`"
                                               x-model="accionista.apellido_paterno"
                                               @input="updateAccionista(index)"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449] transition-colors duration-200" 
                                               placeholder="Apellido paterno"
                                               required>
                                    </div>

                                    <!-- Apellido Materno -->
                                    <div>
                                        <label :for="`accionista_apellido_materno_${index}`" class="block text-xs font-medium text-gray-700 mb-1">
                                            Apellido Materno
                                        </label>
                                        <input type="text" 
                                               :id="`accionista_apellido_materno_${index}`"
                                               :name="`accionistas[${index}][apellido_materno]`"
                                               x-model="accionista.apellido_materno"
                                               @input="updateAccionista(index)"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449] transition-colors duration-200" 
                                               placeholder="Apellido materno">
                                    </div>

                                    <!-- Porcentaje de Acciones -->
                                    <div class="col-span-1 md:col-span-2">
                                        <label :for="`accionista_porcentaje_${index}`" class="block text-xs font-medium text-gray-700 mb-2">
                                            Porcentaje de Participación <span class="text-[#9d2449]">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number" 
                                                   :id="`accionista_porcentaje_${index}`"
                                                   :name="`accionistas[${index}][porcentaje]`"
                                                   x-model="accionista.porcentaje"
                                                   @input="updateAccionista(index)"
                                                   class="w-full pl-4 pr-12 py-3 text-lg font-semibold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9d2449] focus:border-[#9d2449] transition-all duration-200 bg-gradient-to-r from-white to-gray-50" 
                                                   placeholder="0.00" 
                                                   min="0" 
                                                   max="100"
                                                   step="0.01"
                                                   required>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                                <span class="text-lg font-bold text-[#9d2449]">%</span>
                                            </div>
                                        </div>
                                        <!-- Barra de progreso visual -->
                                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-[#9d2449] to-[#8a203f] h-2 rounded-full transition-all duration-300 relative overflow-hidden"
                                                 :style="`width: ${Math.min((parseFloat(accionista.porcentaje) || 0), 100)}%`">
                                                <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                                            <span>0%</span>
                                            <span class="font-medium" :class="(parseFloat(accionista.porcentaje) || 0) > 100 ? 'text-red-500' : 'text-[#9d2449]'"
                                                  x-text="`${(parseFloat(accionista.porcentaje) || 0).toFixed(2)}%`"></span>
                                            <span>100%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Botón para agregar accionista -->
                <button type="button" 
                        @click="agregarAccionista()"
                        class="w-full py-3 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200 border-2 border-dashed border-gray-300 hover:border-gray-400">
                    <i class="fas fa-plus mr-2"></i>
                    Agregar Accionista
                </button>

                <!-- Resumen de porcentajes -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-blue-700">Total de participación:</span>
                        <span class="text-lg font-bold" 
                              :class="totalPorcentaje === 100 ? 'text-green-600' : 'text-orange-600'"
                              x-text="totalPorcentaje.toFixed(2) + '%'"></span>
                    </div>
                    <div x-show="totalPorcentaje !== 100" class="mt-2 text-xs text-orange-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        El total debe sumar exactamente 100%
                    </div>
                </div>
            </div>

            <!-- Botones de navegación -->
            <div class="flex justify-between pt-6 border-t border-gray-200">
                <button type="button" 
                        @click="$dispatch('previous-step')"
                        class="flex items-center px-6 py-3 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Anterior
                </button>

                <button type="submit" 
                        :disabled="loading || totalPorcentaje !== 100"
                        :class="loading || totalPorcentaje !== 100 ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#6d1a32]'"
                        class="flex items-center px-6 py-3 text-white rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    <span x-show="!loading">
                        Guardar y Continuar
                        <i class="fas fa-arrow-right ml-2"></i>
                    </span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>

<script>
function accionistasData() {
    return {
        tramiteId: null,
        accionistas: [
            { nombre: '', apellido_paterno: '', apellido_materno: '', porcentaje: 0, expanded: false }
        ],
        loading: false,
        showError: false,
        errorMessage: '',
        showSuccess: false,
        successMessage: '',
        
        async init() {
            // Obtener tramite_id
            const datosAccionistas = @json($datosAccionistas ?? []);
            const tramite = @json($tramite ?? null);
            
            if (datosAccionistas && datosAccionistas.tramite_id) {
                this.tramiteId = datosAccionistas.tramite_id;
                await this.cargarDatosDesdeObjeto(datosAccionistas);
            } else if (tramite && tramite.id) {
                this.tramiteId = tramite.id;
                await this.cargarDatosDesdeTramite(tramite.id);
            }
        },

        async cargarDatosDesdeObjeto(datosAccionistas) {
            try {
                if (datosAccionistas.accionistas && Array.isArray(datosAccionistas.accionistas) && datosAccionistas.accionistas.length > 0) {
                    this.accionistas = datosAccionistas.accionistas.map(accionista => ({
                        nombre: accionista.nombre || '',
                        apellido_paterno: accionista.apellido_paterno || '',
                        apellido_materno: accionista.apellido_materno || '',
                        porcentaje: parseFloat(accionista.porcentaje || 0),
                        expanded: false
                    }));
                }
            } catch (error) {
                console.error('Error al cargar datos desde objeto:', error);
            }
        },

        async cargarDatosDesdeTramite(tramiteId) {
            try {
                console.log('🔍 Cargando datos de accionistas para trámite:', tramiteId);
                
                const response = await fetch(`/api/tramite/${tramiteId}/accionistas`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('📋 Datos de accionistas recibidos:', data);
                    
                    if (data.success && data.accionistas && Array.isArray(data.accionistas) && data.accionistas.length > 0) {
                        this.accionistas = data.accionistas.map(accionista => ({
                            nombre: accionista.nombre || '',
                            apellido_paterno: accionista.apellido_paterno || '',
                            apellido_materno: accionista.apellido_materno || '',
                            porcentaje: parseFloat(accionista.porcentaje || 0),
                            expanded: false
                        }));
                        return true;
                    }
                }
                return false;
            } catch (error) {
                console.error('❌ Error al cargar datos de accionistas:', error);
                return false;
            }
        },

        agregarAccionista() {
            this.accionistas.push({
                nombre: '',
                apellido_paterno: '',
                apellido_materno: '',
                porcentaje: 0,
                expanded: true
            });
        },

        toggleAccionista(index) {
            this.accionistas[index].expanded = !this.accionistas[index].expanded;
        },

        updateAccionista(index) {
            // Forzar actualización de la reactividad
            this.$nextTick();
        },

        eliminarAccionista(index) {
            if (this.accionistas.length > 1) {
                this.accionistas.splice(index, 1);
            }
        },

        get totalPorcentaje() {
            return this.accionistas.reduce((total, accionista) => {
                return total + (parseFloat(accionista.porcentaje) || 0);
            }, 0);
        },

        mostrarError(mensaje) {
            this.errorMessage = mensaje;
            this.showError = true;
            this.showSuccess = false;
            setTimeout(() => {
                this.showError = false;
            }, 5000);
        },

        mostrarExito(mensaje) {
            this.successMessage = mensaje;
            this.showSuccess = true;
            this.showError = false;
            setTimeout(() => {
                this.showSuccess = false;
            }, 3000);
        },

        async guardarAccionistas() {
            if (this.loading) return;

            // Validaciones
            if (!this.tramiteId) {
                this.mostrarError('No se pudo identificar el trámite');
                return;
            }

            if (this.accionistas.length === 0) {
                this.mostrarError('Debe agregar al menos un accionista');
                return;
            }

            // Validar que todos los campos estén llenos
            for (let i = 0; i < this.accionistas.length; i++) {
                const accionista = this.accionistas[i];
                if (!accionista.nombre.trim()) {
                    this.mostrarError(`El nombre del accionista ${i + 1} es obligatorio`);
                    return;
                }
                if (!accionista.apellido_paterno.trim()) {
                    this.mostrarError(`El apellido paterno del accionista ${i + 1} es obligatorio`);
                    return;
                }
                if (!accionista.porcentaje || accionista.porcentaje <= 0) {
                    this.mostrarError(`El porcentaje del accionista ${i + 1} debe ser mayor a 0`);
                    return;
                }
            }

            if (Math.abs(this.totalPorcentaje - 100) > 0.01) {
                this.mostrarError('El total de participación debe sumar exactamente 100%');
                return;
            }

            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('tramite_id', this.tramiteId);
                
                // Agregar accionistas
                this.accionistas.forEach((accionista, index) => {
                    formData.append(`accionistas[${index}][nombre]`, accionista.nombre.trim());
                    formData.append(`accionistas[${index}][apellido_paterno]`, accionista.apellido_paterno.trim());
                    formData.append(`accionistas[${index}][apellido_materno]`, accionista.apellido_materno.trim());
                    formData.append(`accionistas[${index}][porcentaje]`, accionista.porcentaje);
                });

                // Agregar CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }

                console.log('📤 Enviando datos de accionistas:', this.accionistas);

                const response = await fetch('/tramites/guardar-accionistas-formulario', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                console.log('📥 Respuesta del servidor:', data);

                if (data.success) {
                    this.mostrarExito('Accionistas guardados correctamente');
                    
                    // Disparar evento para navegar al siguiente paso
                    setTimeout(() => {
                        this.$dispatch('next-step');
                    }, 1000);
                } else {
                    this.mostrarError(data.message || 'Error al guardar los accionistas');
                    if (data.errors) {
                        console.error('Errores de validación:', data.errors);
                    }
                }
            } catch (error) {
                console.error('❌ Error al guardar accionistas:', error);
                this.mostrarError('Error de conexión. Por favor, intente nuevamente.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

@push('styles')
<style>
.border-l-3 {
    border-left-width: 3px;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/validators/accionistas-validator.js') }}"></script>
@endpush