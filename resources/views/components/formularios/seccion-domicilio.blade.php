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
@push('scripts')
<script src="{{ asset('js/validators/domicilio-validator.js') }}"></script>
@endpush
    @else
        <!-- Formulario editable normal -->
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
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="codigo_postal" name="codigo_postal"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: 12345"
                               pattern="[0-9]{4,5}"
                               maxlength="5"
                               x-model="cp"
                               aria-label="Código postal"
                               required>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Al ingresar el código postal se llenarán automáticamente algunos campos</p>
                </div>
                <!-- Estado -->
                <div class="form-group">
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="estado" name="estado"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all"
                               placeholder="Ej: Jalisco"
                               x-model="estado"
                               aria-label="Estado"
                               readonly
                               required>
                    </div>
                </div>
                <!-- Municipio -->
                <div class="form-group">
                    <label for="municipio" class="block text-sm font-medium text-gray-700 mb-2">
                        Municipio
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="municipio" name="municipio"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all"
                               placeholder="Ej: Guadalajara"
                               x-model="municipio"
                               aria-label="Municipio"
                               readonly
                               required>
                    </div>
                </div>
                <!-- Colonia -->
                <div class="form-group">
                    <label for="colonia" class="block text-sm font-medium text-gray-700 mb-2">
                        Asentamiento
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <select id="colonia" name="colonia"
                                class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                x-model="colonia"
                                required
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
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="calle" name="calle"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: Av. Principal"
                               maxlength="100"
                               x-model="nombreVialidad"
                               aria-label="Calle"
                               required>
                    </div>
                </div>
                <!-- Número Exterior -->
                <div class="form-group">
                    <label for="numero_exterior" class="block text-sm font-medium text-gray-700 mb-2">
                        Número Exterior
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="numero_exterior" name="numero_exterior"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: 123 o S/N"
                               pattern="[A-Za-z0-9\/]+"
                               maxlength="10"
                               x-model="numeroExterior"
                               aria-label="Número exterior"
                               required>
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
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="entre_calle_1" name="entre_calle_1"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: Calle Independencia"
                               pattern="[A-Za-z0-9\s]+"
                               maxlength="100"
                               aria-label="Entre calle"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="entre_calle_2" class="block text-sm font-medium text-gray-700 mb-2">
                        Y Calle
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="entre_calle_2" name="entre_calle_2"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: Calle Morelos"
                               pattern="[A-Za-z0-9\s]+"
                               maxlength="100"
                               aria-label="Y calle"
                               required>
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
            
            // 4. Configurar validación en tiempo real
            this.setupRealTimeValidation();
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
        // Función para validar formulario
        validarFormulario() {
            let esValido = true;
            const errores = [];
            
            // Validar código postal (exactamente 5 dígitos)
            const cpPattern = /^[0-9]{5}$/;
            if (!this.cp || !cpPattern.test(this.cp)) {
                esValido = false;
                errores.push('El código postal debe tener exactamente 5 dígitos');
                this.marcarCampoError('codigo_postal');
            }
            
            // Validar estado
            if (!this.estado || this.estado.trim() === '') {
                esValido = false;
                errores.push('El estado es requerido');
                this.marcarCampoError('estado');
            }
            
            // Validar municipio
            if (!this.municipio || this.municipio.trim() === '') {
                esValido = false;
                errores.push('El municipio es requerido');
                this.marcarCampoError('municipio');
            }
            
            // Validar colonia/asentamiento
            if (!this.colonia || this.colonia.toString().trim() === '' || this.colonia === '0') {
                esValido = false;
                errores.push('Debe seleccionar un asentamiento válido');
                this.marcarCampoError('colonia');
            }
            
            // Validar calle (mínimo 3 caracteres, máximo 100, caracteres válidos)
            const callePattern = /^[a-zA-ZÀ-ÿñÑ0-9\s\.\,\-\(\)\/]+$/;
            if (!this.nombreVialidad || this.nombreVialidad.trim().length < 3) {
                esValido = false;
                errores.push('La calle debe tener al menos 3 caracteres');
                this.marcarCampoError('calle');
            } else if (this.nombreVialidad.trim().length > 100) {
                esValido = false;
                errores.push('La calle debe tener máximo 100 caracteres');
                this.marcarCampoError('calle');
            } else if (!callePattern.test(this.nombreVialidad.trim())) {
                esValido = false;
                errores.push('La calle contiene caracteres no válidos');
                this.marcarCampoError('calle');
            }
            
            // Validar número exterior (requerido, máximo 10 caracteres, caracteres válidos)
            const numeroExtPattern = /^[a-zA-Z0-9\s\-\.\/SN]+$/;
            if (!this.numeroExterior || this.numeroExterior.trim() === '') {
                esValido = false;
                errores.push('El número exterior es obligatorio');
                this.marcarCampoError('numero_exterior');
            } else if (this.numeroExterior.trim().length > 10) {
                esValido = false;
                errores.push('El número exterior debe tener máximo 10 caracteres');
                this.marcarCampoError('numero_exterior');
            } else if (!numeroExtPattern.test(this.numeroExterior.trim())) {
                esValido = false;
                errores.push('El número exterior contiene caracteres no válidos (use letras, números, guiones, puntos o S/N)');
                this.marcarCampoError('numero_exterior');
            }
            
            // Validar número interior (opcional, pero si se proporciona debe ser válido)
            const numeroIntPattern = /^[a-zA-Z0-9\s\-\.\/]*$/;
            if (this.numeroInterior && this.numeroInterior.trim() !== '') {
                if (this.numeroInterior.trim().length > 10) {
                    esValido = false;
                    errores.push('El número interior debe tener máximo 10 caracteres');
                    this.marcarCampoError('numero_interior');
                } else if (!numeroIntPattern.test(this.numeroInterior.trim())) {
                    esValido = false;
                    errores.push('El número interior contiene caracteres no válidos');
                    this.marcarCampoError('numero_interior');
                }
            }
            
            // Validar entre calles (ambas requeridas, mínimo 3 caracteres, máximo 100)
            const entreCalle1 = document.getElementById('entre_calle_1')?.value || '';
            const entreCalle2 = document.getElementById('entre_calle_2')?.value || '';
            
            if (!entreCalle1.trim()) {
                esValido = false;
                errores.push('La primera calle de referencia es obligatoria');
                this.marcarCampoError('entre_calle_1');
            } else if (entreCalle1.trim().length < 3) {
                esValido = false;
                errores.push('La primera calle de referencia debe tener al menos 3 caracteres');
                this.marcarCampoError('entre_calle_1');
            } else if (entreCalle1.trim().length > 100) {
                esValido = false;
                errores.push('La primera calle de referencia debe tener máximo 100 caracteres');
                this.marcarCampoError('entre_calle_1');
            } else if (!callePattern.test(entreCalle1.trim())) {
                esValido = false;
                errores.push('La primera calle de referencia contiene caracteres no válidos');
                this.marcarCampoError('entre_calle_1');
            }
            
            if (!entreCalle2.trim()) {
                esValido = false;
                errores.push('La segunda calle de referencia es obligatoria');
                this.marcarCampoError('entre_calle_2');
            } else if (entreCalle2.trim().length < 3) {
                esValido = false;
                errores.push('La segunda calle de referencia debe tener al menos 3 caracteres');
                this.marcarCampoError('entre_calle_2');
            } else if (entreCalle2.trim().length > 100) {
                esValido = false;
                errores.push('La segunda calle de referencia debe tener máximo 100 caracteres');
                this.marcarCampoError('entre_calle_2');
            } else if (!callePattern.test(entreCalle2.trim())) {
                esValido = false;
                errores.push('La segunda calle de referencia contiene caracteres no válidos');
                this.marcarCampoError('entre_calle_2');
            }
            
            if (!esValido) {
                this.mostrarErrores(errores);
            }
            
            return esValido;
        },
        
        // Función para marcar campo con error
        marcarCampoError(campoId) {
            const campo = document.getElementById(campoId);
            if (campo) {
                campo.classList.add('border-red-500', 'bg-red-50');
                setTimeout(() => {
                    campo.classList.remove('border-red-500', 'bg-red-50');
                }, 5000);
            }
        },
        
        // Función para mostrar errores
        mostrarErrores(errores) {
            let modal = document.getElementById('modal-error-domicilio');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'modal-error-domicilio';
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                modal.innerHTML = `
                    <div class="bg-white rounded-xl p-6 max-w-md mx-4 shadow-2xl">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-100 rounded-full p-2 mr-3">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Error de Validación</h3>
                        </div>
                        <div id="lista-errores-domicilio" class="mb-4"></div>
                        <button onclick="cerrarModalErrorDomicilio()" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            Entendido
                        </button>
                    </div>
                `;
                document.body.appendChild(modal);
            }
            
            const listaErrores = document.getElementById('lista-errores-domicilio');
            listaErrores.innerHTML = '<ul class="text-sm text-gray-600 space-y-1">' + 
                errores.map(error => `<li class="flex items-start"><i class="fas fa-times text-red-500 mr-2 mt-0.5 text-xs"></i>${error}</li>`).join('') + 
                '</ul>';
                
            modal.style.display = 'flex';
        },

        async guardarDomicilio() {
            // Primero validar el formulario
            if (!this.validarFormulario()) {
                return false;
            }
            
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
                
                if (!response.ok) {
                    const errorData = await response.json();
                    if (response.status === 422 && errorData.errors) {
                        // Errores de validación del servidor
                        this.mostrarErroresValidacion(errorData.errors);
                        return false;
                    } else {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                }
                
                const result = await response.json();
                if (result.success) {
                    this.mostrarMensajeExito('Domicilio guardado correctamente');
                    return true;
                } else {
                    const errorMsg = result.message || 'Error desconocido';
                    this.mostrarErrores([errorMsg]);
                    return false;
                }
            } catch (error) {
                console.error('Error al guardar domicilio:', error);
                this.mostrarErrores(['Error de conexión. Por favor, intente nuevamente.']);
                return false;
            }
        },
        
        // Función para mostrar errores de validación del servidor
        mostrarErroresValidacion(errores) {
            // Limpiar errores anteriores
            document.querySelectorAll('.error-message-domicilio').forEach(el => el.remove());
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500', 'bg-red-50');
            });
            
            const erroresArray = [];
            
            // Mostrar nuevos errores
            for (const [campo, mensajes] of Object.entries(errores)) {
                const elemento = document.getElementById(campo) || document.querySelector(`[name="${campo}"]`);
                if (elemento) {
                    // Agregar clases de error
                    elemento.classList.add('border-red-500', 'bg-red-50');
                    
                    // Agregar mensaje de error
                    const contenedor = elemento.closest('.form-group') || elemento.parentElement;
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'error-message-domicilio text-xs text-red-500 mt-1 flex items-center';
                    errorMsg.innerHTML = `
                        <i class="fas fa-exclamation-triangle mr-1 text-red-400"></i>
                        <span>${Array.isArray(mensajes) ? mensajes[0] : mensajes}</span>
                    `;
                    contenedor.appendChild(errorMsg);
                }
                
                // Agregar a la lista de errores para el modal
                erroresArray.push(Array.isArray(mensajes) ? mensajes[0] : mensajes);
            }
            
            // Mostrar modal con todos los errores
            if (erroresArray.length > 0) {
                this.mostrarErrores(erroresArray);
            }
            
            // Scroll al primer error
            const primerError = document.querySelector('.border-red-500');
            if (primerError) {
                primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },
        
        // Función para configurar validación en tiempo real
        setupRealTimeValidation() {
            // Limpiar errores cuando el usuario comience a escribir
            this.$watch('cp', () => this.limpiarErrorCampo('codigo_postal'));
            this.$watch('nombreVialidad', () => this.limpiarErrorCampo('calle'));
            this.$watch('numeroExterior', () => this.limpiarErrorCampo('numero_exterior'));
            this.$watch('numeroInterior', () => this.limpiarErrorCampo('numero_interior'));
            this.$watch('colonia', () => this.limpiarErrorCampo('colonia'));
            
            // Configurar listeners para campos que no están en Alpine
            this.$nextTick(() => {
                const entreCalle1 = document.getElementById('entre_calle_1');
                const entreCalle2 = document.getElementById('entre_calle_2');
                
                if (entreCalle1) {
                    entreCalle1.addEventListener('input', () => this.limpiarErrorCampo('entre_calle_1'));
                }
                if (entreCalle2) {
                    entreCalle2.addEventListener('input', () => this.limpiarErrorCampo('entre_calle_2'));
                }
            });
        },
        
        // Función para limpiar error de un campo específico
        limpiarErrorCampo(campoId) {
            const campo = document.getElementById(campoId);
            if (campo) {
                campo.classList.remove('border-red-500', 'bg-red-50');
            }
            
            // Limpiar mensaje de error asociado
            const contenedor = campo?.closest('.form-group') || campo?.parentElement;
            if (contenedor) {
                const errorMsg = contenedor.querySelector('.error-message-domicilio');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        },
        
        // Función para mostrar mensaje de éxito
        mostrarMensajeExito(mensaje) {
            const elemento = document.createElement('div');
            elemento.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
            elemento.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${mensaje}</span>
                </div>
            `;
            
            document.body.appendChild(elemento);
            
            // Animar entrada
            setTimeout(() => {
                elemento.classList.remove('translate-x-full');
            }, 100);
            
            // Remover después de 3 segundos
            setTimeout(() => {
                elemento.classList.add('translate-x-full');
                setTimeout(() => {
                    if (elemento.parentNode) {
                        elemento.parentNode.removeChild(elemento);
                    }
                }, 300);
            }, 3000);
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

// Función global para cerrar modal de error de domicilio
window.cerrarModalErrorDomicilio = function() {
    const modal = document.getElementById('modal-error-domicilio');
    if (modal) {
        modal.style.display = 'none';
    }
};
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

/* Estilos para campos con error */
.form-group input.border-red-500,
.form-group select.border-red-500 {
    @apply border-red-500 bg-red-50;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-group input.border-red-500:focus,
.form-group select.border-red-500:focus {
    @apply border-red-500 ring-2 ring-red-500/20;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
}

/* Animación para mensaje de error */
.error-message-domicilio {
    animation: fadeInError 0.3s ease-out;
}

@keyframes fadeInError {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Estilos para campos válidos después de corrección */
.form-group input:not(.border-red-500):focus,
.form-group select:not(.border-red-500):focus {
    @apply border-green-500 ring-2 ring-green-500/20;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}
</style> 
