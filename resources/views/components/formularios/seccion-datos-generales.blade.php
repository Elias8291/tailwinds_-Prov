@props(['title' => 'Datos Generales', 'datosTramite' => [], 'datosSolicitante' => [], 'readonly' => false])

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="max-w-6xl mx-auto" @if(!$readonly) x-data="datosGeneralesData()" x-init="init()" @endif>
    @if(!$readonly)
    <!-- Vista editable normal -->
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
    
    <form @submit.prevent="guardarDatosGenerales()" class="space-y-8">
        <!-- Campos ocultos -->
        <input type="hidden" name="form_action" value="next">
        <input type="hidden" name="seccion" value="1">
        <input type="hidden" name="tramite_id" x-model="tramiteId">
        <input type="hidden" name="tipo_tramite" value="{{ $datosTramite['tipo_tramite'] ?? request()->route('tipo_tramite') ?? 'inscripcion' }}">

        <!-- Datos del Proveedor -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-sm">
                    <i class="fas fa-building text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Datos del Proveedor</h3>
                    <p class="text-sm text-gray-500">Información general del solicitante</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tipo de Proveedor -->
                <div class="form-group">
                    <label for="tipo_persona" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Proveedor <span class="text-red-500">*</span>
                    </label>
                        <input type="text" 
                               id="tipo_persona"
                               name="tipo_persona"
                           x-model="tipoPersona" 
                               class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                               readonly>
                        </div>

                <!-- RFC -->
                <div class="form-group">
                    <label for="rfc" class="block text-sm font-medium text-gray-700 mb-2">
                        RFC <span class="text-red-500">*</span>
                    </label>
                        <input type="text" 
                               id="rfc"
                               name="rfc"
                           x-model="rfc" 
                               class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                               readonly>
                    </div>
                </div>

            <!-- CURP (Solo persona física) -->
            <div x-show="tipoPersona === 'Física'" class="form-group mt-6">
                <label for="curp" class="block text-sm font-medium text-gray-700 mb-2">
                    CURP <span class="text-red-500">*</span>
                </label>
                    <input type="text" 
                           id="curp"
                           name="curp"
                       x-model="curp" 
                           class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                           readonly>
                </div>

            <!-- Nombre Completo (Solo persona física) -->
            <div x-show="tipoPersona === 'Física'" class="form-group mt-6">
                <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre Completo <span class="text-red-500">*</span>
                </label>
                    <input type="text" 
                           id="nombre_completo"
                           name="nombre_completo"
                       x-model="nombreCompleto" 
                           class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                           readonly>
            </div>

            <!-- Razón Social (Solo persona moral) -->
            <div x-show="tipoPersona === 'Moral'" class="form-group mt-6">
                <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                    Razón Social <span class="text-red-500">*</span>
                </label>
                    <input type="text" 
                           id="razon_social"
                           name="razon_social"
                       x-model="razonSocial" 
                           class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                           readonly>
            </div>

            <!-- Giro -->
            <div class="form-group mt-6">
                <label for="giro" class="block text-sm font-medium text-gray-700 mb-2">
                    Giro <span class="text-red-500">*</span>
                </label>
                <textarea id="giro" 
                          name="giro" 
                          x-model="giro"
                          @input="validateGiro()"
                          rows="4"
                          class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all"
                          :class="{ 'border-red-500 bg-red-50': errors.giro }"
                          placeholder="Describa el giro de la empresa (mínimo 10 caracteres)"></textarea>
                <div x-show="errors.giro" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                        <span class="text-sm text-red-700" x-text="errors.giro"></span>
            </div>
                </div>
            </div>

            <!-- Página Web -->
            <div class="form-group mt-6">
                <label for="pagina_web" class="block text-sm font-medium text-gray-700 mb-2">
                    Página Web <span class="text-gray-400">(Opcional)</span>
                </label>
                <input type="url" 
                       id="pagina_web"
                       name="pagina_web"
                       x-model="paginaWeb"
                       @input="validatePaginaWeb()"
                       class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all"
                       :class="{ 'border-red-500 bg-red-50': errors.pagina_web }"
                       placeholder="https://www.ejemplo.com">
                <div x-show="errors.pagina_web" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                        <span class="text-sm text-red-700" x-text="errors.pagina_web"></span>
            </div>
        </div>
            </div>
        </div>

        <!-- Actividades Económicas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-sm">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Actividades Económicas</h3>
                    <p class="text-sm text-gray-500">Selecciona las actividades económicas que realizas</p>
                </div>
            </div>

            <!-- Buscador de actividades -->
            <div class="form-group mb-6">
                <label for="actividad_search" class="block text-sm font-medium text-gray-700 mb-2">
                    Buscar Actividades <span class="text-red-500">*</span>
                    </label>
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-lightbulb mr-2"></i>
                        Agrega las actividades económicas tal como aparecen en tu constancia de situación fiscal.
                            </p>
                    </div>
                
                <div class="relative">
                    <input type="text" 
                           id="actividad_search" 
                           x-model="busquedaActividad"
                           @input="buscarActividades()"
                           @focus="mostrarDropdown = true"
                           placeholder="Escriba para buscar actividad..."
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all"
                           :class="{ 
                               'border-red-500 bg-red-50': errors.actividades_seleccionadas,
                               'border-[#9d2449]/30 bg-[#9d2449]/5': cargandoActividades
                           }"
                           autocomplete="off">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i x-show="!cargandoActividades" class="fas fa-search text-gray-400 transition-all"></i>
                        <div x-show="cargandoActividades" class="w-4 h-4 border-2 border-gray-300 border-t-[#9d2449] rounded-full animate-spin"></div>
                    </div>
                </div>
                
                <!-- Dropdown de resultados -->
                <div x-show="mostrarDropdown && (resultadosActividades.length > 0 || busquedaActividad.length > 0 || cargandoActividades)" 
                     x-cloak
                     class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-hidden">
                    <div class="max-h-48 overflow-y-auto">
                        <!-- Indicador de carga -->
                        <div x-show="cargandoActividades" class="px-6 py-8 text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <div class="relative">
                                    <div class="w-6 h-6 border-2 border-[#9d2449]/20 border-t-[#9d2449] rounded-full animate-spin"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Buscando actividades...</p>
                                    <p class="text-xs text-gray-500">Un momento por favor</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resultados de búsqueda -->
                        <template x-for="actividad in resultadosActividades" :key="actividad.id">
                            <div @click="agregarActividad(actividad)" 
                                 class="px-6 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" x-text="actividad.nombre"></p>
                                        <p class="text-xs text-gray-500" x-text="actividad.sector || 'Sin sector'"></p>
                                        <p x-show="actividad.codigo_scian" class="text-xs text-blue-600" x-text="'Código: ' + actividad.codigo_scian"></p>
                                    </div>
                                    <i class="fas fa-plus text-[#9d2449]"></i>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Mensaje cuando no hay resultados -->
                        <div x-show="!cargandoActividades && resultadosActividades.length === 0 && busquedaActividad.length > 0" 
                             class="px-6 py-8 text-center">
                            <div class="mb-4">
                                <i class="fas fa-search text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-500 text-sm">No se encontraron actividades</p>
                                <p class="text-gray-400 text-xs">Intenta con otras palabras clave</p>
                            </div>
                            <button type="button" 
                                    @click="agregarActividadPersonalizada()"
                                    class="mt-4 px-4 py-2 bg-[#9d2449] text-white text-sm rounded-lg hover:bg-[#8a203f] transition-colors">
                                <i class="fas fa-plus mr-2"></i>Agregar actividad personalizada
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividades seleccionadas -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">Actividades Seleccionadas</label>
                <div class="min-h-[60px] p-4 bg-gray-50 border border-gray-200 rounded-lg"
                     :class="{ 'border-red-500 bg-red-50': errors.actividades_seleccionadas }">
                    <div x-show="actividadesSeleccionadas.length === 0" 
                         class="flex items-center justify-center text-gray-400 text-sm italic">
                        <i class="fas fa-plus-circle mr-2"></i>No hay actividades seleccionadas
                </div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(actividad, index) in actividadesSeleccionadas" :key="actividad.id">
                            <div class="flex items-center bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                <span x-text="actividad.nombre" class="text-gray-700"></span>
                                <button type="button" 
                                        @click="removerActividad(index)"
                                        class="ml-2 text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                    </div>
                        </template>
        </div>
        </div>
                <div x-show="errors.actividades_seleccionadas" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                        <span class="text-sm text-red-700" x-text="errors.actividades_seleccionadas"></span>
                </div>
                </div>
        </div>
        </div>

        <!-- Información de Contacto -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-sm">
                    <i class="fas fa-address-book text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Información de Contacto</h3>
                    <p class="text-sm text-gray-500">Datos de la persona de contacto</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre del Contacto -->
                <div class="form-group">
                    <label for="contacto_nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Contacto <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="contacto_nombre" 
                           name="contacto_nombre"
                           x-model="contactoNombre"
                           @input="validateContactoNombre()"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all"
                           :class="{ 'border-red-500 bg-red-50': errors.contacto_nombre }"
                           placeholder="Ej: Juan Pérez González">
                    <div x-show="errors.contacto_nombre" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-sm text-red-700" x-text="errors.contacto_nombre"></span>
                </div>
                    </div>
                </div>

                <!-- Cargo del Contacto -->
                <div class="form-group">
                    <label for="contacto_cargo" class="block text-sm font-medium text-gray-700 mb-2">
                        Cargo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="contacto_cargo" 
                           name="contacto_cargo"
                           x-model="contactoCargo"
                           @input="validateContactoCargo()"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all"
                           :class="{ 'border-red-500 bg-red-50': errors.contacto_cargo }"
                           placeholder="Ej: Gerente General">
                    <div x-show="errors.contacto_cargo" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-sm text-red-700" x-text="errors.contacto_cargo"></span>
                </div>
                    </div>
                </div>

                <!-- Correo del Contacto -->
                <div class="form-group">
                    <label for="contacto_correo" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="contacto_correo" 
                           name="contacto_correo"
                           x-model="contactoCorreo"
                           @input="validateContactoCorreo()"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all"
                           :class="{ 'border-red-500 bg-red-50': errors.contacto_correo }"
                           placeholder="contacto@empresa.com">
                    <div x-show="errors.contacto_correo" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-sm text-red-700" x-text="errors.contacto_correo"></span>
                </div>
                    </div>
                </div>

                <!-- Teléfono del Contacto -->
                <div class="form-group">
                    <label for="contacto_telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           id="contacto_telefono" 
                           name="contacto_telefono"
                           x-model="contactoTelefono"
                           @input="validateContactoTelefono()"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all"
                           :class="{ 'border-red-500 bg-red-50': errors.contacto_telefono }"
                           placeholder="5512345678"
                           maxlength="10">
                    <div x-show="errors.contacto_telefono" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-sm text-red-700" x-text="errors.contacto_telefono"></span>
            </div>
                    </div>
        </div>
        </div>
            </div>

        <!-- Botones de navegación -->
        <div class="flex justify-between pt-6 border-t border-gray-200">
            <button type="button" 
                    onclick="navegarAnteriorDatosGenerales()"
                    class="flex items-center px-6 py-3 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Anterior
            </button>
            <button type="submit" 
                    :disabled="loading"
                    :class="loading ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#6d1a32]'"
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
    @else
        <!-- Vista de solo lectura para revisión -->
        <!-- Código existente de solo lectura -->
        <!-- El código existente de readonly permanece igual -->
    @endif
</div>

<script>
function datosGeneralesData() {
    return {
        // Datos del formulario
        tramiteId: null,
        tipoPersona: '',
        rfc: '',
        curp: '',
        nombreCompleto: '',
        razonSocial: '',
        giro: '',
        paginaWeb: '',
        contactoNombre: '',
        contactoCargo: '',
        contactoCorreo: '',
        contactoTelefono: '',
        
        // Actividades
        busquedaActividad: '',
        actividadesSeleccionadas: [],
        resultadosActividades: [],
        mostrarDropdown: false,
        todasLasActividades: [],
        cargandoActividades: false,
        
        // Estados del formulario
        loading: false,
        showError: false,
        errorMessage: '',
        showSuccess: false,
        successMessage: '',
        errors: {},
        searchTimeout: null,
        
        async init() {
            // Cargar datos iniciales
            const datosTramite = @json($datosTramite ?? []);
            const datosSolicitante = @json($datosSolicitante ?? []);
            
            if (datosTramite && Object.keys(datosTramite).length > 0) {
                this.tramiteId = datosTramite.tramite_id;
                this.giro = datosTramite.giro || '';
                this.paginaWeb = datosTramite.pagina_web || '';
                this.contactoNombre = datosTramite.contacto_nombre || '';
                this.contactoCargo = datosTramite.contacto_cargo || '';
                this.contactoCorreo = datosTramite.contacto_correo || '';
                this.contactoTelefono = datosTramite.contacto_telefono || '';
            }
            
            if (datosSolicitante && Object.keys(datosSolicitante).length > 0) {
                this.tipoPersona = datosSolicitante.tipo_persona || 'Física';
                this.rfc = datosSolicitante.rfc || '';
                this.curp = datosSolicitante.curp || '';
                this.nombreCompleto = datosSolicitante.nombre_completo || '';
                this.razonSocial = datosSolicitante.razon_social || '';
            }
            
            // Cargar actividades
            await this.cargarActividades();
            
            // Escuchar clics fuera del dropdown
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.relative')) {
                    this.mostrarDropdown = false;
                    this.cargandoActividades = false;
                }
            });
        },
        
        async cargarActividades() {
            try {
                const response = await fetch('/api/actividades');
                const data = await response.json();
                if (data.success) {
                    this.todasLasActividades = data.data;
                }
            } catch (error) {
                console.error('Error al cargar actividades:', error);
            }
        },
        
        async buscarActividades() {
            // Limpiar timeout anterior
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            if (this.busquedaActividad.length < 2) {
                this.resultadosActividades = [];
                this.cargandoActividades = false;
                return;
            }
            
            // Mostrar loading inmediatamente
            this.cargandoActividades = true;
            
            // Debounce de 300ms
            this.searchTimeout = setTimeout(async () => {
                try {
                    // Buscar usando la API específica de búsqueda
                    const response = await fetch(`/api/actividades/buscar?q=${encodeURIComponent(this.busquedaActividad)}&limit=10`);
                    const data = await response.json();
                    
                    if (data.success && data.data) {
                        // Filtrar actividades ya seleccionadas
                        this.resultadosActividades = data.data.filter(actividad => 
                            !this.actividadesSeleccionadas.find(sel => sel.id === actividad.id)
                        );
                    } else {
                        this.resultadosActividades = [];
                    }
                } catch (error) {
                    console.error('Error al buscar actividades:', error);
                    this.resultadosActividades = [];
                } finally {
                    this.cargandoActividades = false;
                }
            }, 300);
        },
        
        agregarActividad(actividad) {
            this.actividadesSeleccionadas.push(actividad);
            this.busquedaActividad = '';
            this.resultadosActividades = [];
            this.mostrarDropdown = false;
            this.cargandoActividades = false;
            this.clearError('actividades_seleccionadas');
        },
        
        agregarActividadPersonalizada() {
            if (this.busquedaActividad.trim()) {
                const actividadPersonalizada = {
                    id: 'custom_' + Date.now(),
                    nombre: this.busquedaActividad.trim(),
                    sector: 'Actividad personalizada',
                    codigo_scian: null,
                    personalizada: true
                };
                this.agregarActividad(actividadPersonalizada);
            }
        },
        
        removerActividad(index) {
            this.actividadesSeleccionadas.splice(index, 1);
        },
        
        // Validaciones del lado del cliente
        validateGiro() {
            if (!this.giro || this.giro.trim().length < 10) {
                this.setError('giro', 'El giro debe tener al menos 10 caracteres');
            return false;
            }
            if (this.giro.length > 500) {
                this.setError('giro', 'El giro no puede exceder 500 caracteres');
                return false;
            }
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.,;:\-\(\)\/]+$/.test(this.giro)) {
                this.setError('giro', 'El giro contiene caracteres no permitidos');
                return false;
            }
            this.clearError('giro');
            return true;
        },
        
        validatePaginaWeb() {
            if (!this.paginaWeb) {
                this.clearError('pagina_web');
                return true;
            }
            
            const urlRegex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/i;
            if (!urlRegex.test(this.paginaWeb)) {
                this.setError('pagina_web', 'El formato de la URL no es válido');
                return false;
            }
            this.clearError('pagina_web');
            return true;
        },
        
        validateContactoNombre() {
            if (!this.contactoNombre || this.contactoNombre.trim().length < 2) {
                this.setError('contacto_nombre', 'El nombre debe tener al menos 2 caracteres');
                return false;
            }
            if (this.contactoNombre.length > 100) {
                this.setError('contacto_nombre', 'El nombre no puede exceder 100 caracteres');
                return false;
            }
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/.test(this.contactoNombre)) {
                this.setError('contacto_nombre', 'El nombre solo puede contener letras, espacios y apostrofes');
                return false;
            }
            this.clearError('contacto_nombre');
            return true;
        },
        
        validateContactoCargo() {
            if (!this.contactoCargo || this.contactoCargo.trim().length < 3) {
                this.setError('contacto_cargo', 'El cargo debe tener al menos 3 caracteres');
                return false;
            }
            if (this.contactoCargo.length > 50) {
                this.setError('contacto_cargo', 'El cargo no puede exceder 50 caracteres');
                return false;
            }
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/.test(this.contactoCargo)) {
                this.setError('contacto_cargo', 'El cargo solo puede contener letras, espacios y apostrofes');
                return false;
            }
            this.clearError('contacto_cargo');
            return true;
        },
        
        validateContactoCorreo() {
            if (!this.contactoCorreo) {
                this.setError('contacto_correo', 'El correo electrónico es obligatorio');
                return false;
            }
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(this.contactoCorreo)) {
                this.setError('contacto_correo', 'El formato del correo electrónico no es válido');
                return false;
            }
            this.clearError('contacto_correo');
            return true;
        },
        
        validateContactoTelefono() {
            if (!this.contactoTelefono) {
                this.setError('contacto_telefono', 'El teléfono es obligatorio');
                return false;
            }
            // Solo permitir números y que sean exactamente 10 dígitos
            this.contactoTelefono = this.contactoTelefono.replace(/\D/g, '');
            if (this.contactoTelefono.length !== 10) {
                this.setError('contacto_telefono', 'El teléfono debe tener exactamente 10 dígitos');
                return false;
            }
            this.clearError('contacto_telefono');
            return true;
        },
        
        validateActividades() {
            if (this.actividadesSeleccionadas.length === 0) {
                this.setError('actividades_seleccionadas', 'Debe seleccionar al menos una actividad económica');
                return false;
            }
            this.clearError('actividades_seleccionadas');
            return true;
        },
        
        setError(field, message) {
            this.errors[field] = message;
        },
        
        clearError(field) {
            delete this.errors[field];
        },
        
        clearAllErrors() {
            this.errors = {};
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
        
        async guardarDatosGenerales() {
            if (this.loading) return;
            
            // Ejecutar todas las validaciones y mostrar errores específicos
            const validaciones = [
                this.validateGiro(),
                this.validatePaginaWeb(),
                this.validateContactoNombre(),
                this.validateContactoCargo(),
                this.validateContactoCorreo(),
                this.validateContactoTelefono(),
                this.validateActividades()
            ];
            
            const formularioEsValido = validaciones.every(v => v === true);
            
            if (!formularioEsValido) {
                // Mostrar mensaje general de error
                this.mostrarError('Por favor, corrija los errores marcados en rojo antes de continuar.');
                
                // Hacer scroll al primer error
                setTimeout(() => {
                    const primerError = document.querySelector('.border-red-500');
                    if (primerError) {
                        primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        primerError.focus();
                    }
                }, 100);
                
                return;
            }
            
            this.loading = true;
            this.clearAllErrors();
            
            try {
                const formData = new FormData();
                formData.append('tramite_id', this.tramiteId);
                formData.append('giro', this.giro.trim());
                formData.append('pagina_web', this.paginaWeb.trim());
                formData.append('contacto_nombre', this.contactoNombre.trim());
                formData.append('contacto_cargo', this.contactoCargo.trim());
                formData.append('contacto_correo', this.contactoCorreo.trim());
                formData.append('contacto_telefono', this.contactoTelefono.trim());
                formData.append('actividades_seleccionadas', JSON.stringify(this.actividadesSeleccionadas));
                
                // Agregar CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }
                
                const response = await fetch('/formularios/datos-generales/guardar', {
                method: 'POST',
                    body: formData,
                headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    this.mostrarExito('Datos generales guardados correctamente');
                    // Disparar evento para navegar al siguiente paso
                setTimeout(() => {
                        this.$dispatch('next-step');
                    }, 1000);
            } else {
                    if (response.status === 422 && data.errors) {
                        // Errores de validación del servidor
                        this.procesarErroresServidor(data.errors);
                        this.mostrarErroresValidacion(data.errors);
                        
                        // Hacer scroll al primer error del servidor
                        setTimeout(() => {
                            const primerError = document.querySelector('.border-red-500');
                            if (primerError) {
                                primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }, 100);
        } else {
                        this.mostrarError(data.message || 'Error al guardar los datos generales');
                }
            }
        } catch (error) {
                console.error('Error en AJAX:', error);
                this.mostrarError('Error de conexión. Por favor, intente nuevamente.');
            } finally {
                this.loading = false;
            }
        },
        
        mostrarErroresValidacion(errores) {
            for (const [campo, mensajes] of Object.entries(errores)) {
                const mensaje = Array.isArray(mensajes) ? mensajes[0] : mensajes;
                this.setError(campo, mensaje);
            }
            this.mostrarError('Por favor corrija los errores indicados en el formulario');
        },
        
        // Procesar errores del servidor para mostrarlos en los campos
        procesarErroresServidor(errores) {
            if (typeof errores === 'object') {
                Object.keys(errores).forEach(campo => {
                    const mensajes = Array.isArray(errores[campo]) ? errores[campo] : [errores[campo]];
                    this.setError(campo, mensajes[0]);
                });
            }
        }
    }
}

// Función para navegar al paso anterior
function navegarAnteriorDatosGenerales() {
    if (typeof window.navegarAnterior === 'function') {
        window.navegarAnterior();
        return;
    }
    
    const alpineContainer = document.querySelector('[x-data*="currentStep"]');
    if (alpineContainer && typeof Alpine !== 'undefined') {
        try {
            const alpineData = Alpine.$data(alpineContainer);
                if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                if (alpineData.currentStep > 1) {
                    alpineData.currentStep--;
                }
            }
        } catch (error) {
            console.error('Error al navegar:', error);
        }
    }
}
</script>
