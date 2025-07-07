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
            <p class="text-sm text-gray-700 mt-1">Información sobre los accionistas de la empresa</p>
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
            <div class="space-y-6">
                <!-- Header simple -->
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Accionistas</h3>
                    <p class="text-gray-600 mb-4">Gestiona la participación accionaria</p>
                    <div class="text-sm text-gray-700 font-medium">
                        <span x-text="accionistas.length"></span> registrados • 
                        <span x-text="totalPorcentaje.toFixed(1) + '%'"></span> asignado
                    </div>
                </div>
                <!-- Nota informativa -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>💡 Consejo:</strong> Si hay un solo accionista, agrégalo con 100%. Si son varios, puedes agregar tantos como necesites hasta completar el 100%.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Grid de cartas simples -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Mensaje cuando no hay accionistas -->
                    <template x-if="accionistas.length === 0">
                        <div class="col-span-full text-center py-8">
                            <div class="bg-gray-50 rounded-lg p-6 border-2 border-dashed border-gray-300">
                                <i class="fas fa-users text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-600 mb-4">No hay accionistas registrados</p>
                                <p class="text-sm text-gray-500">Haz click en "Agregar" para comenzar</p>
                            </div>
                        </div>
                    </template>
                    <!-- Cards de accionistas existentes -->
                    <template x-for="(accionista, index) in accionistas" :key="index">
                        <!-- Carta simple -->
                        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all cursor-pointer"
                             @click="toggleAccionista(index)"
                             :class="accionista.expanded ? 'ring-2 ring-[#9d2449] border-[#9d2449]' : ''">
                            <!-- Número y porcentaje -->
                            <div class="flex justify-between items-center mb-3">
                                <span class="w-8 h-8 bg-[#9d2449] text-white rounded-full flex items-center justify-center text-sm font-bold"
                                      x-text="index + 1"></span>
                                <span class="text-lg font-bold text-[#9d2449]" 
                                      x-text="(parseFloat(accionista.porcentaje) || 0).toFixed(1) + '%'"></span>
                                </div>
                            <!-- Nombre -->
                            <div class="mb-3">
                                <h4 class="font-semibold text-gray-900" 
                                    x-text="accionista.nombre || 'Sin nombre'"></h4>
                                <p class="text-sm text-gray-700" 
                                   x-text="(accionista.apellido_paterno || '') + ' ' + (accionista.apellido_materno || '')"></p>
                                    </div>
                            <!-- Barra de progreso -->
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 bg-[#9d2449] rounded-full transition-all duration-300"
                                     :style="`width: ${Math.min((parseFloat(accionista.porcentaje) || 0), 100)}%`"></div>
                            </div>
                            <!-- Estado -->
                            <div class="mt-3 text-center">
                                <span class="text-xs font-medium"
                                      :class="(accionista.nombre && accionista.apellido_paterno && (parseFloat(accionista.porcentaje) || 0) > 0) 
                                              ? 'text-[#9d2449]' : 'text-gray-500'">
                                    <span x-text="(accionista.nombre && accionista.apellido_paterno && (parseFloat(accionista.porcentaje) || 0) > 0) ? 'Completo' : 'Pendiente'"></span>
                                </span>
                                        </div>
                                        <!-- Botón eliminar -->
                                        <button type="button" 
                                                @click.stop="eliminarAccionista(index)"
                                    class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-xs hover:bg-red-600">
                                <i class="fas fa-times"></i>
                                        </button>
                        </div>
                    </template>
                    <!-- Carta para agregar (siempre visible) -->
                    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-[#9d2449] hover:bg-[#9d2449]/5 transition-all cursor-pointer flex items-center justify-center min-h-[140px]"
                         @click="agregarAccionista()">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-[#9d2449] text-white rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Agregar</span>
                        </div>
                    </div>
                </div>
                <!-- Modal simple -->
                <div x-show="accionistas.some(a => a.expanded) || nuevoAccionista" 
                     x-cloak
                     class="fixed inset-0 bg-black/30 z-50 flex items-center justify-center p-4"
                     @click.self="accionistas.forEach(a => a.expanded = false); nuevoAccionista = null">
                    <!-- Modal para accionistas existentes -->
                    <template x-for="(accionista, index) in accionistas.filter(a => a.expanded)" :key="index">
                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                            <!-- Header modal -->
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-900">
                                    Accionista <span x-text="accionistas.findIndex(a => a.expanded) + 1"></span>
                                </h3>
                                <button @click="accionista.expanded = false"
                                        class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-times"></i>
                                        </button>
                                </div>
                            <!-- Formulario simple -->
                            <div class="space-y-4">
                                    <!-- Nombre -->
                                    <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               x-model="accionista.nombre"
                                           @input="updateAccionista(accionistas.findIndex(a => a.expanded))"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449]"
                                           placeholder="Juan Carlos">
                                    </div>
                                <!-- Apellidos -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Apellido Paterno <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               x-model="accionista.apellido_paterno"
                                               @input="updateAccionista(accionistas.findIndex(a => a.expanded))"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449]"
                                               placeholder="Pérez">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Apellido Materno
                                        </label>
                                        <input type="text" 
                                               x-model="accionista.apellido_materno"
                                               @input="updateAccionista(accionistas.findIndex(a => a.expanded))"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449]"
                                               placeholder="González">
                                    </div>
                                    </div>
                                <!-- Porcentaje -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Porcentaje <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number" 
                                                   x-model="accionista.porcentaje"
                                               @input="updateAccionista(accionistas.findIndex(a => a.expanded))"
                                               class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449]"
                                                   placeholder="0.00" 
                                                   min="0" 
                                                   max="100"
                                               step="0.01">
                                        <span class="absolute right-3 top-2 text-gray-500">%</span>
                                            </div>
                                        </div>
                                <!-- Botones -->
                                <div class="flex gap-3 pt-4">
                                    <button @click="accionista.expanded = false"
                                            class="flex-1 bg-[#9d2449] text-white py-2 rounded-md hover:bg-[#8a203f] transition-colors">
                                        Guardar
                                    </button>
                                    <button @click="eliminarAccionista(accionistas.findIndex(a => a.expanded)); accionista.expanded = false"
                                            class="px-4 bg-red-500 text-white py-2 rounded-md hover:bg-red-600 transition-colors">
                                        Eliminar
                                    </button>
                                </div>
                                            </div>
                                        </div>
                    </template>
                    <!-- Modal para nuevo accionista -->
                    <template x-if="nuevoAccionista">
                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
                            <!-- Header modal -->
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-900">
                                    Nuevo Accionista
                                </h3>
                                <button @click="cancelarNuevoAccionista()"
                                        class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <!-- Formulario simple -->
                            <div class="space-y-4">
                                <!-- Nombre -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="nuevoAccionista.nombre"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449]"
                                           placeholder="Juan Carlos">
                                </div>
                                <!-- Apellidos -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Apellido Paterno <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               x-model="nuevoAccionista.apellido_paterno"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449]"
                                               placeholder="Pérez">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Apellido Materno
                                        </label>
                                        <input type="text" 
                                               x-model="nuevoAccionista.apellido_materno"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449]"
                                               placeholder="González">
                                    </div>
                                        </div>
                                <!-- Porcentaje -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Porcentaje <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                               x-model="nuevoAccionista.porcentaje"
                                               class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-md focus:ring-[#9d2449] focus:border-[#9d2449]"
                                               placeholder="0.00"
                                               min="0"
                                               max="100"
                                               step="0.01">
                                        <span class="absolute right-3 top-2 text-gray-500">%</span>
                                    </div>
                                </div>
                                <!-- Botones -->
                                <div class="flex gap-3 pt-4">
                                    <button @click="guardarNuevoAccionista()"
                                            class="flex-1 bg-[#9d2449] text-white py-2 rounded-md hover:bg-[#8a203f] transition-colors">
                                        Agregar
                                    </button>
                                    <button @click="cancelarNuevoAccionista()"
                                            class="px-4 bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600 transition-colors">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <!-- Resumen total -->
            </div>
            <!-- Botones de navegación -->
            <div class="flex justify-between pt-6 border-t border-gray-200">
                <button type="button" 
                        onclick="navegarAnteriorAccionistas()"
                        class="px-8 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-offset-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Anterior
                </button>
                <button type="submit" 
                        :disabled="loading || totalPorcentaje !== 100"
                        :class="loading || totalPorcentaje !== 100 ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#7a1c38]'"
                        class="px-8 py-3 text-white rounded-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#9d2449] focus:ring-offset-2">
                    <span x-show="!loading">
                        <i class="fas fa-save mr-2"></i>
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
        accionistas: [],
        nuevoAccionista: null, // Accionista temporal
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
            
            // Resetear estados de carga cuando el usuario navega
            this.resetearEstados();
        },
        
        resetearEstados() {
            this.loading = false;
            this.showError = false;
            this.showSuccess = false;
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
                } else {
                    // Inicializar con array vacío si no hay datos
                    this.accionistas = [];
                }
            } catch (error) {
                this.accionistas = [];
            }
        },
        async cargarDatosDesdeTramite(tramiteId) {
            try {
                const response = await fetch(`/api/tramite/${tramiteId}/accionistas`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.accionistas && Array.isArray(data.accionistas) && data.accionistas.length > 0) {
                        this.accionistas = data.accionistas.map(accionista => ({
                            nombre: accionista.nombre || '',
                            apellido_paterno: accionista.apellido_paterno || '',
                            apellido_materno: accionista.apellido_materno || '',
                            porcentaje: parseFloat(accionista.porcentaje || 0),
                            expanded: false
                        }));
                        return true;
                    } else {
                        // Inicializar con array vacío si no hay datos válidos
                        this.accionistas = [];
                    }
                }
                return false;
            } catch (error) {
                this.accionistas = [];
                return false;
            }
        },
        agregarAccionista() {
            // Crear accionista temporal para el modal
            this.nuevoAccionista = {
                nombre: '',
                apellido_paterno: '',
                apellido_materno: '',
                porcentaje: 0,
                esNuevo: true
            };
        },
        guardarNuevoAccionista() {
            // Validar que los campos obligatorios estén llenos
            if (!this.nuevoAccionista.nombre.trim()) {
                this.mostrarError('El nombre es obligatorio');
                return;
            }
            if (!this.nuevoAccionista.apellido_paterno.trim()) {
                this.mostrarError('El apellido paterno es obligatorio');
                return;
            }
            if (!this.nuevoAccionista.porcentaje || this.nuevoAccionista.porcentaje <= 0) {
                this.mostrarError('El porcentaje debe ser mayor a 0');
                return;
            }
            // Agregar al array de accionistas
            this.accionistas.push({
                nombre: this.nuevoAccionista.nombre.trim(),
                apellido_paterno: this.nuevoAccionista.apellido_paterno.trim(),
                apellido_materno: this.nuevoAccionista.apellido_materno.trim(),
                porcentaje: parseFloat(this.nuevoAccionista.porcentaje),
                expanded: false
            });
            // Cerrar modal y limpiar temporal
            this.nuevoAccionista = null;
            this.mostrarExito('Accionista agregado correctamente');
        },
        cancelarNuevoAccionista() {
            this.nuevoAccionista = null;
        },
        toggleAccionista(index) {
            this.accionistas[index].expanded = !this.accionistas[index].expanded;
        },
        updateAccionista(index) {
            // Forzar actualización de la reactividad
            this.$nextTick();
        },
        eliminarAccionista(index) {
            if (this.accionistas.length > 0) {
                this.accionistas.splice(index, 1);
                // Mostrar mensaje si se eliminó un accionista
                this.mostrarExito('Accionista eliminado correctamente');
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
                const response = await fetch('/tramites/guardar-accionistas-formulario', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.mostrarExito('Accionistas guardados correctamente');
                    // Disparar evento para navegar al siguiente paso
                    setTimeout(() => {
                        this.$dispatch('next-step');
                    }, 1000);
                } else {
                    this.mostrarError(data.message || 'Error al guardar los accionistas');
                    if (data.errors) {
                    }
                }
            } catch (error) {
                this.mostrarError('Error de conexión. Por favor, intente nuevamente.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<script>
// Función para navegar al paso anterior desde accionistas
function navegarAnteriorAccionistas() {
    // Método 1: Función global navegarAnterior
    if (typeof window.navegarAnterior === 'function') {
        window.navegarAnterior();
        return;
    }
    // Método 2: Buscar contenedor Alpine.js y retroceder
    const alpineContainer = document.querySelector('[x-data*="currentStep"]');
    if (alpineContainer && typeof Alpine !== 'undefined') {
        try {
            const alpineData = Alpine.$data(alpineContainer);
            if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                if (alpineData.currentStep > 1) {
                    alpineData.currentStep--;
                    return;
                } else {
                    return;
                }
            }
        } catch (error) {
        }
    }
    // Método 3: Disparar evento personalizado en el contenedor
    if (alpineContainer) {
        alpineContainer.dispatchEvent(new CustomEvent('previous-step'));
        return;
    }
    // Método 4: Buscar directamente botones de navegación en el documento
    const prevButtons = document.querySelectorAll('button[onclick*="currentStep--"], button[x-text*="Anterior"]');
    if (prevButtons.length > 0) {
        prevButtons[0].click();
        return;
    }
    // Fallback: intentar manipular directamente
    const stepContainers = document.querySelectorAll('[x-show*="currentStep"]');
    if (stepContainers.length > 0) {
        // Buscar el contenedor activo
        for (let container of stepContainers) {
            if (container.style.display !== 'none' && !container.hasAttribute('hidden')) {
                // Intentar acceder al contexto Alpine
                try {
                    const parentWithData = container.closest('[x-data]');
                    if (parentWithData && Alpine && Alpine.$data) {
                        const data = Alpine.$data(parentWithData);
                        if (data && data.currentStep && data.currentStep > 1) {
                            data.currentStep--;
                            return;
                        }
                    }
                } catch (error) {
                }
            }
        }
    }
}
</script>
@push('styles')
<style>
/* Estilos básicos y limpios */
.transition-all {
    transition: all 0.3s ease;
}
/* Hover effects simples */
.hover\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
/* Focus states con color primario */
input:focus {
    outline: none;
    border-color: #9d2449;
    box-shadow: 0 0 0 3px rgba(157, 36, 73, 0.1);
}
/* Responsive simple */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
    }
}
/* Ocultar elementos en mobile si es necesario */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush
@push('scripts')
@endpush
