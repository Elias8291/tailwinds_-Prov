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
                <div id="accionistas-container">
                    <template x-for="(accionista, index) in accionistas" :key="index">
                        <div class="bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Nombre -->
                                <div>
                                        <label :for="`accionista_nombre_${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre
                                            <span class="text-[#9d2449]">*</span>
                                        </label>
                                        <input type="text" 
                                               :id="`accionista_nombre_${index}`"
                                               :name="`accionistas[${index}][nombre]`"
                                               x-model="accionista.nombre"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" 
                                               placeholder="Nombre(s)"
                                               :aria-label="`Nombre del accionista ${index + 1}`"
                                               required>
                                </div>

                                <!-- Apellido Paterno -->
                                <div>
                                    <label :for="`accionista_apellido_paterno_${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                                        Apellido Paterno
                                        <span class="text-[#9d2449]">*</span>
                                </label>
                                    <input type="text" 
                                           :id="`accionista_apellido_paterno_${index}`"
                                           :name="`accionistas[${index}][apellido_paterno]`"
                                           x-model="accionista.apellido_paterno"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" 
                                           placeholder="Apellido paterno"
                                           :aria-label="`Apellido paterno del accionista ${index + 1}`"
                                           required>
                            </div>

                                <!-- Apellido Materno -->
                            <div>
                                    <label :for="`accionista_apellido_materno_${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                                        Apellido Materno
                                </label>
                                    <input type="text" 
                                           :id="`accionista_apellido_materno_${index}`"
                                           :name="`accionistas[${index}][apellido_materno]`"
                                           x-model="accionista.apellido_materno"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" 
                                           placeholder="Apellido materno"
                                           :aria-label="`Apellido materno del accionista ${index + 1}`">
                            </div>

                                <!-- Porcentaje de Acciones -->
                            <div>
                                    <label :for="`accionista_porcentaje_${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                                    % Acciones
                                        <span class="text-[#9d2449]">*</span>
                                </label>
                                    <div class="flex">
                                        <input type="number" 
                                               :id="`accionista_porcentaje_${index}`"
                                               :name="`accionistas[${index}][porcentaje]`"
                                               x-model="accionista.porcentaje"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" 
                                               placeholder="0" 
                                               min="0" 
                                               max="100"
                                               step="0.01"
                                               :aria-label="`Porcentaje de acciones del accionista ${index + 1}`"
                                               required>
                                        <button type="button" 
                                                @click="eliminarAccionista(index)"
                                                x-show="accionistas.length > 1"
                                                class="ml-2 text-red-500 hover:text-red-700 px-3 py-2 rounded-lg hover:bg-red-50 transition duration-200">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
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
            { nombre: '', apellido_paterno: '', apellido_materno: '', porcentaje: 0 }
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
                        porcentaje: parseFloat(accionista.porcentaje || 0)
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
                            porcentaje: parseFloat(accionista.porcentaje || 0)
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
                porcentaje: 0
            });
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