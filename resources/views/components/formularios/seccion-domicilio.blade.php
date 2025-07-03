@props(['title' => 'Domicilio', 'datosDomicilio' => [], 'datosSAT' => [], 'readonly' => false])
<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8" 
    x-data="domicilioData()">
    <!-- Encabezado con icono -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
                <i class="fas fa-map-marker-alt text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
                <p class="text-sm text-gray-500 mt-1">Información sobre la ubicación de la empresa</p>
            </div>
        </div>
    </div>
    @if($readonly)
        <!-- Vista de solo lectura para revisión -->
        <div class="space-y-6">
            <!-- Información del Domicilio -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código Postal</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $datosDomicilio['codigo_postal'] ?? 'No especificado' }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $datosDomicilio['estado'] ?? 'No especificado' }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Municipio</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $datosDomicilio['municipio'] ?? 'No especificado' }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asentamiento</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $datosDomicilio['colonia'] ?? 'No especificado' }}
                    </div>
                </div>
                <div class="form-group md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Calle</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $datosDomicilio['calle'] ?? 'No especificado' }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número Exterior</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $datosDomicilio['numero_exterior'] ?? 'No especificado' }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número Interior</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $datosDomicilio['numero_interior'] ?? 'No especificado' }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entre Calle</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $datosDomicilio['entre_calle_1'] ?? 'No especificado' }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Y Calle</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $datosDomicilio['entre_calle_2'] ?? 'No especificado' }}
                    </div>
                </div>
            </div>
            <!-- Información SAT si existe -->
            @if(!empty($datosSAT))
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información SAT</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($datosSAT as $key => $value)
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                        <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                            {{ $value ?? 'No especificado' }}
                        </div>
                    </div>
                    @endforeach
                </div>
                    </div>
    @endif
</div>

    @else
        <!-- Formulario editable normal -->
        <!-- Container para alertas de error -->
        <div id="domicilio-error-container" class="mb-6 hidden">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error al guardar los datos</h3>
                        <div id="domicilio-error-message" class="mt-2 text-sm text-red-700"></div>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" onclick="document.getElementById('domicilio-error-container').classList.add('hidden')" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form class="space-y-8" @submit.prevent="guardarDomicilio" x-ref="domicilioForm" data-validate="true">
            <input type="hidden" name="action" value="next">
            <input type="hidden" name="seccion" value="2">
            <input type="hidden" name="tramite_id" value="{{ $datosDomicilio['tramite_id'] ?? ($tramite->id ?? '') }}">
            
            <!-- Código Postal y Ubicación -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código Postal -->
                <div class="form-group">
                    <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-2">
                        Código Postal
                    </label>
                    <div class="relative group">
                        <input type="text" id="codigo_postal" name="codigo_postal"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: 12345"
                               pattern="[0-9]{4,5}"
                               maxlength="5"
                               x-model="cp"
                               aria-label="Código postal">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Al ingresar el código postal se llenarán automáticamente algunos campos</p>
                </div>

                <!-- Estado -->
                <div class="form-group">
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado
                    </label>
                    <div class="relative group">
                        <input type="text" id="estado" name="estado"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all"
                               placeholder="Ej: Jalisco"
                               x-model="estado"
                               aria-label="Estado"
                               readonly>
                    </div>
                </div>

                <!-- Municipio -->
                <div class="form-group">
                    <label for="municipio" class="block text-sm font-medium text-gray-700 mb-2">
                        Municipio
                    </label>
                    <div class="relative group">
                        <input type="text" id="municipio" name="municipio"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all"
                               placeholder="Ej: Guadalajara"
                               x-model="municipio"
                               aria-label="Municipio"
                               readonly>
                    </div>
                </div>

                <!-- Colonia -->
                <div class="form-group">
                    <label for="colonia" class="block text-sm font-medium text-gray-700 mb-2">
                        Asentamiento
                    </label>
                    <div class="relative group">
                        <select id="colonia" name="colonia"
                                class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                x-model="colonia"
                                aria-label="Seleccionar asentamiento">
                            <option value="">Seleccione un Asentamiento</option>
                            <template x-for="asentamiento in asentamientos" :key="asentamiento.id">
                                <option :value="asentamiento.id" x-text="asentamiento.nombre"></option>
                            </template>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dirección -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Calle -->
                <div class="form-group md:col-span-2">
                    <label for="calle" class="block text-sm font-medium text-gray-700 mb-2">
                        Calle
                    </label>
                    <div class="relative group">
                        <input type="text" id="calle" name="calle"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: Av. Principal"
                               maxlength="100"
                               x-model="nombreVialidad"
                               aria-label="Calle">
                    </div>
                </div>

                <!-- Número Exterior -->
                <div class="form-group">
                    <label for="numero_exterior" class="block text-sm font-medium text-gray-700 mb-2">
                        Número Exterior
                    </label>
                    <div class="relative group">
                        <input type="text" id="numero_exterior" name="numero_exterior"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: 123 o S/N"
                               pattern="[A-Za-z0-9\/]+"
                               maxlength="10"
                               x-model="numeroExterior"
                               aria-label="Número exterior">
                    </div>
                </div>

                <!-- Número Interior -->
                <div class="form-group">
                    <label for="numero_interior" class="block text-sm font-medium text-gray-700 mb-2">
                        Número Interior
                    </label>
                    <div class="relative group">
                        <input type="text" id="numero_interior" name="numero_interior"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: 5A"
                               pattern="[A-Za-z0-9]+"
                               maxlength="10"
                               x-model="numeroInterior"
                               aria-label="Número interior">
                    </div>
                </div>

                <!-- Entre Calles -->
                <div class="form-group">
                    <label for="entre_calle_1" class="block text-sm font-medium text-gray-700 mb-2">
                        Entre Calle
                    </label>
                    <div class="relative group">
                        <input type="text" id="entre_calle_1" name="entre_calle_1"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: Calle Independencia"
                               pattern="[A-Za-z0-9\s]+"
                               maxlength="100"
                               aria-label="Entre calle">
                    </div>
                </div>
                <div class="form-group">
                    <label for="entre_calle_2" class="block text-sm font-medium text-gray-700 mb-2">
                        Y Calle
                    </label>
                    <div class="relative group">
                        <input type="text" id="entre_calle_2" name="entre_calle_2"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: Calle Morelos"
                               pattern="[A-Za-z0-9\s]+"
                               maxlength="100"
                               aria-label="Y calle">
                    </div>
                </div>
            </div>

            <!-- Botones de navegación -->
            <div class="flex justify-between pt-6 border-t border-gray-100">
                <button type="button" 
                        onclick="navegarAnteriorDomicilio()"
                        class="inline-flex items-center bg-gray-600 text-white px-6 py-2 rounded-xl shadow-lg hover:bg-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-gray-600/20">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Anterior
                </button>
                <button type="button" 
                        id="btn-guardar-domicilio"
                        onclick="guardarDomicilioYSiguiente()"
                        class="inline-flex items-center bg-[#9d2449] text-white px-6 py-2 rounded-xl shadow-lg hover:bg-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-[#9d2449]/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <span id="btn-text-domicilio">
                        <i class="fas fa-save mr-2"></i> Guardar y Continuar
                        <i class="fas fa-arrow-right ml-2"></i>
                    </span>
                    <span id="btn-loading-domicilio" class="hidden">
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
function domicilioData() {
    return {
        cp: '',
        estado: '',
        municipio: '',
        colonia: '',
        nombreVialidad: '',
        numeroExterior: '',
        numeroInterior: '',
        asentamientos: [],
        async loadLocationData() {
            if (this.cp.length === 5) {
                try {
                    const response = await fetch(`/api/location-data/${this.cp}`);
                    const data = await response.json();
                    if (data.success) {
                        // Solo actualizar estado y municipio si no están ya establecidos
                        if (!this.estado || this.estado === '') {
                            this.estado = data.estado;
                        }
                        if (!this.municipio || this.municipio === '') {
                            this.municipio = data.municipio;
                        }
                        // Siempre actualizar asentamientos disponibles
                        this.asentamientos = data.asentamientos;
                        // Solo limpiar colonia si no hay una preseleccionada
                        if (!this.colonia || this.colonia === '') {
                            this.colonia = '';
                        }
                    } else {
                        this.estado = '';
                        this.municipio = '';
                        this.asentamientos = [];
                        this.colonia = '';
                    }
                } catch (error) {
                    this.estado = '';
                    this.municipio = '';
                    this.asentamientos = [];
                    this.colonia = '';
                }
            }
        },
        async init() {
            // 1. Verificar si hay datos de domicilio del trámite
            const datosDomicilio = @json($datosDomicilio ?? []);
            const tramite = @json($tramite ?? null);
            // 2. Verificar si hay código postal del SAT en la sesión
            const codigoPostalSat = @json(session('codigo_postal_sat', null));
            // PRIORIDAD PRINCIPAL: Obtener datos directamente desde la base de datos usando tramite_id
            if (tramite && tramite.id) {
                const resultado = await this.cargarDatosDesdeTramite(tramite.id);
                if (!resultado) {
                    // Si no se pudieron cargar datos desde el trámite, intentar con datos pasados o SAT
                    if (datosDomicilio && datosDomicilio.codigo_postal) {
                        await this.cargarDatosDesdeObjeto(datosDomicilio);
                    } else if (codigoPostalSat && codigoPostalSat.length === 5) {
                        this.cp = codigoPostalSat;
                        await this.loadLocationData();
                    }
                }
            }
            // Fallback final: Si no hay trámite, usar datos pasados o SAT
            else if (datosDomicilio && datosDomicilio.codigo_postal) {
                await this.cargarDatosDesdeObjeto(datosDomicilio);
            } 
            else if (codigoPostalSat && codigoPostalSat.length === 5) {
                this.cp = codigoPostalSat;
                await this.loadLocationData();
            }
            // 3. Configurar watcher para cambios en el código postal
            this.$watch('cp', (value) => {
                if (value && value.length === 5) {
                    this.loadLocationData();
                } else if (value.length < 5) {
                    this.estado = '';
                    this.municipio = '';
                    this.asentamientos = [];
                    this.colonia = '';
                }
            });
        },
        // Método para cargar datos desde un objeto de datos
        async cargarDatosDesdeObjeto(datosDomicilio) {
            try {
                // Cargar datos básicos del domicilio existente
                this.cp = datosDomicilio.codigo_postal.toString();
                this.nombreVialidad = datosDomicilio.calle || '';
                this.numeroExterior = datosDomicilio.numero_exterior || '';
                this.numeroInterior = datosDomicilio.numero_interior || '';
                // Cargar datos de ubicación primero para obtener asentamientos
                await this.loadLocationData();
                // Después de cargar asentamientos, seleccionar el correcto
                if (datosDomicilio.asentamiento_id) {
                    this.colonia = datosDomicilio.asentamiento_id.toString();
                }
                // Si hay datos de estado y municipio ya disponibles, usarlos (sobrescribir si es necesario)
                if (datosDomicilio.estado) {
                    this.estado = datosDomicilio.estado;
                }
                if (datosDomicilio.municipio) {
                    this.municipio = datosDomicilio.municipio;
                }
                // Inicializar campos de las calles después de que el DOM esté listo
                this.$nextTick(() => {
                    const entreCalle1Input = document.getElementById('entre_calle_1');
                    const entreCalle2Input = document.getElementById('entre_calle_2');
                    if (entreCalle1Input && datosDomicilio.entre_calle_1) {
                        entreCalle1Input.value = datosDomicilio.entre_calle_1;
                    }
                    if (entreCalle2Input && datosDomicilio.entre_calle_2) {
                        entreCalle2Input.value = datosDomicilio.entre_calle_2;
                    }
                });
            } catch (error) {
                // Error silencioso
            }
        },
        // Método principal para cargar datos desde el trámite usando la cadena: tramite_id → detalle_tramite → direccion_id → codigo_postal
        async cargarDatosDesdeTramite(tramiteId) {
            try {
                        // Inicio debug paso a paso
        // PASO 1: Tramite ID recibido
                const response = await fetch(`/api/tramite/${tramiteId}/domicilio`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    // Verificar estructura paso a paso
                    if ('domicilio' in data && data.domicilio) {
                    }
                    // Verificar si la respuesta fue exitosa y tiene datos
                    if (data.success && data.domicilio && data.domicilio.codigo_postal) {
                        // Cargar todos los datos obtenidos del servidor
                        await this.cargarDatosDesdeObjeto(data.domicilio);
                        return true; // Datos cargados exitosamente
                    }
                    // Si hay estructura de domicilio pero sin código postal completo
                    else if (data.domicilio) {
                        // Intentar cargar al menos el código postal si existe
                        if (data.domicilio.codigo_postal) {
                            this.cp = data.domicilio.codigo_postal.toString();
                            await this.loadLocationData();
                        }
                        return false; // Datos parciales
                    }
                    else {
                    }
                }
                else {
                    // Intentar leer el cuerpo de la respuesta de error
                    try {
                        const errorText = await response.text();
                    } catch (e) {
                    }
                }
                return false; // No hay datos
            } catch (error) {
                return false;
            }
        },
        async guardarDomicilio() {
            const form = this.$refs.domicilioForm;
            const formData = new FormData(form);
            // Asegurar que todos los datos estén incluidos
            formData.set('codigo_postal', this.cp);
            formData.set('calle', this.nombreVialidad);
            formData.set('numero_exterior', this.numeroExterior);
            formData.set('numero_interior', this.numeroInterior || '');
            formData.set('colonia', this.colonia);
            // Obtener tramite_id de los datos pasados al componente o del trámite
            const datosDomicilio = @json($datosDomicilio ?? []);
            const tramite = @json($tramite ?? null);
            if (datosDomicilio && datosDomicilio.tramite_id) {
                formData.set('tramite_id', datosDomicilio.tramite_id);
            } else if (tramite && tramite.id) {
                formData.set('tramite_id', tramite.id);
            }
            // Agregar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.set('_token', csrfToken.getAttribute('content'));
            }
            try {
                const response = await fetch(@json(route("tramites.guardar-domicilio-formulario")), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const responseText = await response.text();
                const result = JSON.parse(responseText);
                
                if (response.ok && result.success) {
                    // Limpiar errores anteriores
                    this.limpiarErroresDomicilio();
                    return true;
                } else {
                    // Manejar errores 422 (Unprocessable Content) específicamente
                    if (response.status === 422 && result.errors) {
                        // Errores de validación del servidor
                        this.mostrarErroresValidacionDomicilio(result.errors, result.message);
                    } else if (result.errors) {
                        // Otros errores con detalles de validación
                        this.mostrarErroresValidacionDomicilio(result.errors, result.message);
                    } else {
                        // Error general sin errores específicos
                        this.mostrarErrorGeneralDomicilio(result.message || 'Error al guardar los datos de domicilio');
                    }
                    return false;
                }
            } catch (error) {
                console.error('❌ Error en AJAX domicilio:', error);
                this.mostrarErrorGeneralDomicilio('Error de conexión. Por favor, intente nuevamente.');
                return false;
            }
        },
        
        // Método para limpiar errores anteriores
        limpiarErroresDomicilio() {
            document.querySelectorAll('.error-message-domicilio').forEach(el => el.remove());
            document.querySelectorAll('.alerta-error-general-domicilio').forEach(el => el.remove());
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500', 'bg-red-50');
                el.classList.add('border-gray-200');
            });
            
            // Ocultar contenedor de errores elegante
            const errorContainer = document.getElementById('domicilio-error-container');
            if (errorContainer) {
                errorContainer.classList.add('hidden');
            }
        },
        
        // Mostrar error en el contenedor elegante
        mostrarErrorDomicilio(mensaje) {
            const errorContainer = document.getElementById('domicilio-error-container');
            const errorMessage = document.getElementById('domicilio-error-message');
            
            if (errorContainer && errorMessage) {
                errorMessage.textContent = mensaje;
                errorContainer.classList.remove('hidden');
                
                // Auto-ocultar después de 10 segundos
                setTimeout(() => {
                    errorContainer.classList.add('hidden');
                }, 10000);
                
                // Scroll al error
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        },
        
        // Método para mostrar errores de validación del servidor
        mostrarErroresValidacionDomicilio(errores, mensajeGeneral = null) {
            // Limpiar errores anteriores
            this.limpiarErroresDomicilio();
            
            // Mostrar mensaje general en el contenedor elegante si existe
            if (mensajeGeneral) {
                this.mostrarErrorDomicilio(mensajeGeneral);
            }
            
            let erroresNoMapeados = [];
            
            // Mostrar errores específicos por campo
            for (const [campo, mensajes] of Object.entries(errores)) {
                const mensaje = Array.isArray(mensajes) ? mensajes[0] : mensajes;
                
                // Mapear nombres de campos del servidor al frontend
                const campoMapeado = this.mapearCampoServidorDomicilio(campo);
                const elemento = document.getElementById(campoMapeado) || 
                               document.querySelector(`[name="${campoMapeado}"]`) ||
                               document.querySelector(`[name="${campo}"]`);
                
                if (elemento) {
                    // Estilo de error elegante
                    elemento.classList.remove('border-gray-200');
                    elemento.classList.add('border-red-500', 'bg-red-50');
                    
                    // Crear mensaje de error elegante
                    const contenedor = elemento.closest('.form-group') || elemento.parentElement;
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message-domicilio mt-2 p-3 bg-red-50 border border-red-200 rounded-lg';
                    errorMsg.innerHTML = `
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-sm text-red-700">${mensaje}</span>
                        </div>
                    `;
                    contenedor.appendChild(errorMsg);
                } else {
                    // Guardar errores que no se pudieron mapear
                    erroresNoMapeados.push(mensaje);
                }
            }
            
            // Si no hay errores específicos pero hay errores, mostrar en contenedor elegante
            if (erroresNoMapeados.length > 0) {
                const todosLosErrores = Object.values(errores).flat().join(' ');
                this.mostrarErrorDomicilio(todosLosErrores);
            }
            
            // Scroll al primer error
            const primerError = document.querySelector('.border-red-500, #domicilio-error-container:not(.hidden)');
            if (primerError) {
                primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },
        
        // Método para mapear nombres de campos del servidor al frontend
        mapearCampoServidorDomicilio(campo) {
            const mapeo = {
                'codigo_postal': 'codigo_postal',
                'colonia': 'colonia',
                'calle': 'calle',
                'numero_exterior': 'numero_exterior',
                'numero_interior': 'numero_interior',
                'entre_calle_1': 'entre_calle_1',
                'entre_calle_2': 'entre_calle_2'
            };
            return mapeo[campo] || campo;
        },
        
        // Método para mostrar error general sin errores específicos
        mostrarErrorGeneralDomicilio(mensaje) {
            this.mostrarErrorDomicilio(mensaje);
        },
        
        // Método para mostrar alerta general (error, warning, info)
        mostrarAlertaGeneralDomicilio(mensaje, tipo = 'error') {
            // Limpiar alertas anteriores
            document.querySelectorAll('.alerta-error-general-domicilio').forEach(el => el.remove());
            
            const formulario = this.$refs.domicilioForm;
            const alerta = document.createElement('div');
            
            // Configurar estilos según el tipo
            let estilos, icono, titulo, colorTexto, colorBoton;
            switch(tipo) {
                case 'warning':
                    estilos = 'bg-yellow-50 border-l-4 border-yellow-500';
                    icono = 'fas fa-exclamation-triangle text-yellow-500';
                    titulo = 'Atención';
                    colorTexto = 'text-yellow-800';
                    colorBoton = 'text-yellow-400 hover:text-yellow-600';
                    break;
                case 'info':
                    estilos = 'bg-blue-50 border-l-4 border-blue-500';
                    icono = 'fas fa-info-circle text-blue-500';
                    titulo = 'Información';
                    colorTexto = 'text-blue-800';
                    colorBoton = 'text-blue-400 hover:text-blue-600';
                    break;
                default: // error
                    estilos = 'bg-red-50 border-l-4 border-red-500';
                    icono = 'fas fa-exclamation-triangle text-red-500';
                    titulo = 'Error';
                    colorTexto = 'text-red-800';
                    colorBoton = 'text-red-400 hover:text-red-600';
            }
            
            alerta.className = `alerta-error-general-domicilio mb-6 p-4 ${estilos} rounded-r-lg shadow-sm`;
            alerta.innerHTML = `
                <div class="flex items-start">
                    <i class="${icono} mr-3 mt-1 flex-shrink-0"></i>
                    <div class="flex-1">
                        <h4 class="${colorTexto} font-medium mb-2">${titulo}</h4>
                        <p class="text-sm ${colorTexto.replace('800', '700')}">${mensaje}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" 
                            class="ml-2 ${colorBoton} focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            formulario.insertBefore(alerta, formulario.firstChild);
            
            // Auto-remover después de 10 segundos
            setTimeout(() => {
                if (alerta.parentNode) {
                    alerta.remove();
                }
            }, 10000);
        }
    }
}
// Función para navegar al paso anterior desde domicilio
function navegarAnteriorDomicilio() {
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
                }
            }
        } catch (error) {
            // Error silencioso
        }
    }
    // Método 3: Disparar evento personalizado
    if (alpineContainer) {
        alpineContainer.dispatchEvent(new CustomEvent('prev-step'));
        return;
    }
}
// Función para guardar domicilio y navegar al siguiente paso
async function guardarDomicilioYSiguiente() {
    // Mostrar estado de carga
    mostrarEstadoCarga('btn-guardar-domicilio', 'btn-text-domicilio', 'btn-loading-domicilio');
    try {
        // 1. Buscar el componente Alpine.js de domicilio
        const domicilioContainer = document.querySelector('[x-data*="domicilioData"]');
        if (domicilioContainer && typeof Alpine !== 'undefined') {
            const alpineData = Alpine.$data(domicilioContainer);
            if (alpineData && typeof alpineData.guardarDomicilio === 'function') {
                const guardado = await alpineData.guardarDomicilio();
                if (guardado) {
                    navegarSiguienteDesdeDomicilio();
                } else {
                    ocultarEstadoCarga('btn-guardar-domicilio', 'btn-text-domicilio', 'btn-loading-domicilio');
                }
                return;
            }
        }
        // Fallback: intentar navegar sin guardar
        navegarSiguienteDesdeDomicilio();
    } catch (error) {
        ocultarEstadoCarga('btn-guardar-domicilio', 'btn-text-domicilio', 'btn-loading-domicilio');
        navegarSiguienteDesdeDomicilio();
    }
}
// Función para navegar al siguiente paso desde domicilio
function navegarSiguienteDesdeDomicilio() {
    // Método 1: Función global navegarSiguiente
    if (typeof window.navegarSiguiente === 'function') {
        window.navegarSiguiente();
        return;
    }
    // Método 2: Buscar contenedor Alpine.js y avanzar
    const alpineContainer = document.querySelector('[x-data*="currentStep"]');
    if (alpineContainer && typeof Alpine !== 'undefined') {
        try {
            const alpineData = Alpine.$data(alpineContainer);
            if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                if (alpineData.currentStep < alpineData.totalSteps) {
                    alpineData.currentStep++;
                    return;
                }
            }
        } catch (error) {
            // Error silencioso
        }
    }
    // Método 3: Disparar evento personalizado
    if (alpineContainer) {
        alpineContainer.dispatchEvent(new CustomEvent('next-step'));
        return;
    }
}
</script>
<style>
.h-12 {
    @apply bg-gradient-to-br from-[#9d2449] to-[#8a203f];
    position: relative;
    overflow: hidden;
}
.h-12::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
    );
    transform: rotate(45deg);
    animation: shine 3s infinite;
}
@keyframes shine {
    0% {
        transform: translateX(-100%) rotate(45deg);
    }
    20%, 100% {
        transform: translateX(100%) rotate(45deg);
    }
}
.form-group:hover input:not([readonly]),
.form-group:hover select {
    @apply border-[#9d2449]/30;
}
input:focus:not([readonly]), 
select:focus {
    @apply ring-2 ring-[#9d2449]/20 border-[#9d2449];
    box-shadow: 0 0 0 1px rgba(157, 36, 73, 0.1), 
                0 2px 4px rgba(157, 36, 73, 0.05);
}
input[readonly] {
    @apply bg-gray-50;
}
.btn-primary {
    @apply bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white;
}
.btn-primary:hover {
    @apply from-[#8a203f] to-[#7a1c38];
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(157, 36, 73, 0.1),
                0 2px 4px -1px rgba(157, 36, 73, 0.06);
}
input, select, textarea {
    @apply transition-all duration-300 bg-white shadow-sm;
}
input:focus:not([readonly]), 
select:focus, 
textarea:focus {
    @apply transform -translate-y-px shadow-md bg-white;
}
.form-group {
    @apply relative;
}
.form-group input,
.form-group select,
.form-group textarea {
    @apply border-[#4F46E5]/20;
}
.form-group:hover input:not([readonly]),
.form-group:hover select,
.form-group:hover textarea {
    @apply border-[#4F46E5]/40;
}
</style> 
