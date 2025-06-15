@props(['title' => 'Domicilio'])

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

    <form class="space-y-8" @submit.prevent="guardarDomicilio" x-ref="domicilioForm">
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
                            required>
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
                           x-model="numeroInterior">
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
                    onclick="guardarDomicilioYSiguiente()"
                    class="inline-flex items-center bg-[#9d2449] text-white px-6 py-2 rounded-xl shadow-lg hover:bg-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-[#9d2449]/20">
                <i class="fas fa-save mr-2"></i> Guardar y Continuar
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </form>
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
                console.log('🔍 === INICIO DEBUG PASO A PASO ===');
                console.log('🔍 PASO 1: Tramite ID recibido:', tramiteId);
                
                const response = await fetch(`/api/tramite/${tramiteId}/domicilio`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                console.log('📡 PASO 2: Respuesta HTTP recibida');
                console.log('   - Status:', response.status);
                console.log('   - Status Text:', response.statusText);
                console.log('   - URL llamada:', response.url);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('📋 PASO 3: Datos JSON parseados');
                    console.log('   - Respuesta completa:', JSON.stringify(data, null, 2));
                    
                    // Verificar estructura paso a paso
                    console.log('🔎 PASO 4: Verificando estructura de datos');
                    console.log('   - data.success existe:', 'success' in data ? '✅' : '❌');
                    console.log('   - data.success valor:', data.success);
                    console.log('   - data.domicilio existe:', 'domicilio' in data ? '✅' : '❌');
                    
                    if ('domicilio' in data && data.domicilio) {
                        console.log('   - data.domicilio:', JSON.stringify(data.domicilio, null, 2));
                        console.log('   - domicilio.codigo_postal existe:', 'codigo_postal' in data.domicilio ? '✅' : '❌');
                        console.log('   - domicilio.codigo_postal valor:', data.domicilio.codigo_postal);
                        console.log('   - domicilio.tramite_id:', data.domicilio.tramite_id);
                        console.log('   - domicilio.direccion_id:', data.domicilio.direccion_id);
                    }
                    
                    // Verificar si la respuesta fue exitosa y tiene datos
                    if (data.success && data.domicilio && data.domicilio.codigo_postal) {
                        console.log('✅ PASO 5: ÉXITO - Datos completos encontrados');
                        console.log('   - Código postal:', data.domicilio.codigo_postal);
                        console.log('   - Estado:', data.domicilio.estado);
                        console.log('   - Municipio:', data.domicilio.municipio);
                        
                        // Cargar todos los datos obtenidos del servidor
                        await this.cargarDatosDesdeObjeto(data.domicilio);
                        return true; // Datos cargados exitosamente
                    }
                    // Si hay estructura de domicilio pero sin código postal completo
                    else if (data.domicilio) {
                        console.log('⚠️ PASO 5: PARCIAL - Datos incompletos encontrados');
                        console.log('   - Razón del fallo:', !data.success ? 'success=false' : 'codigo_postal vacío');
                        
                        // Intentar cargar al menos el código postal si existe
                        if (data.domicilio.codigo_postal) {
                            console.log('📮 Intentando cargar código postal parcial:', data.domicilio.codigo_postal);
                            this.cp = data.domicilio.codigo_postal.toString();
                            await this.loadLocationData();
                        }
                        return false; // Datos parciales
                    }
                    else {
                        console.log('❌ PASO 5: FALLO - No se encontraron datos de domicilio');
                        console.log('   - data.domicilio es:', data.domicilio);
                        console.log('   - Claves disponibles en data:', Object.keys(data));
                    }
                }
                else {
                    console.error('❌ PASO 3: FALLO - Error en respuesta HTTP');
                    console.error('   - Status:', response.status);
                    console.error('   - Status Text:', response.statusText);
                    
                    // Intentar leer el cuerpo de la respuesta de error
                    try {
                        const errorText = await response.text();
                        console.error('   - Cuerpo del error:', errorText);
                    } catch (e) {
                        console.error('   - No se pudo leer el cuerpo del error');
                    }
                }
                
                console.log('🔍 === FIN DEBUG - RETORNANDO FALSE ===');
                return false; // No hay datos
            } catch (error) {
                console.error('❌ EXCEPCIÓN EN cargarDatosDesdeTramite:');
                console.error('   - Mensaje:', error.message);
                console.error('   - Stack:', error.stack);
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
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = JSON.parse(responseText);
                
                if (result.success) {
                    return true;
                } else {
                    const errorMsg = result.message || (result.errors ? Object.values(result.errors).flat().join(', ') : 'Error desconocido');
                    alert('Error al guardar: ' + errorMsg);
                    return false;
                }
            } catch (error) {
                alert('Error al guardar los datos: ' + error.message);
                return false;
            }
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
    try {
        // 1. Buscar el componente Alpine.js de domicilio
        const domicilioContainer = document.querySelector('[x-data*="domicilioData"]');
        if (domicilioContainer && typeof Alpine !== 'undefined') {
            const alpineData = Alpine.$data(domicilioContainer);
            
            if (alpineData && typeof alpineData.guardarDomicilio === 'function') {
                const guardado = await alpineData.guardarDomicilio();
                
                if (guardado) {
                    navegarSiguienteDesdeDomicilio();
                }
                return;
            }
        }
        
        // Fallback: intentar navegar sin guardar
        navegarSiguienteDesdeDomicilio();
        
    } catch (error) {
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