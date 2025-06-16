@props(['title' => 'Apoderado Legal', 'tramite' => null, 'datosApoderado' => [], 'readonly' => false])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8" 
     @if(!$readonly) x-data="apoderadoData()" x-init="init()" @endif>
    <!-- Encabezado con icono -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg">
            <i class="fas fa-user-tie text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Información del apoderado legal y datos notariales</p>
        </div>
    </div>

    @if($readonly)
        <!-- Vista de solo lectura para revisión -->
        <div class="space-y-6">
            @if(!empty($datosApoderado))
                <!-- Datos del Apoderado -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-2 mb-6">
                        <i class="fas fa-user-tie text-[#9d2449]"></i>
                        <h4 class="text-lg font-medium text-gray-700">Datos del Apoderado o Representante Legal</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                                {{ $datosApoderado['nombre_apoderado'] ?? 'No especificado' }}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Número de Escritura</label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                                {{ $datosApoderado['numero_escritura'] ?? 'No especificado' }}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Notario</label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                                {{ $datosApoderado['nombre_notario'] ?? 'No especificado' }}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Número del Notario</label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                                {{ $datosApoderado['numero_notario'] ?? 'No especificado' }}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Entidad Federativa</label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                                @php
                                    $estados = [
                                        '1' => 'Aguascalientes', '2' => 'Baja California', '3' => 'Baja California Sur', 
                                        '4' => 'Campeche', '5' => 'Coahuila', '6' => 'Colima', '7' => 'Chiapas', 
                                        '8' => 'Chihuahua', '9' => 'Ciudad de México', '10' => 'Durango', 
                                        '11' => 'Guanajuato', '12' => 'Guerrero', '13' => 'Hidalgo', 
                                        '14' => 'Jalisco', '15' => 'México', '16' => 'Michoacán', 
                                        '17' => 'Morelos', '18' => 'Nayarit', '19' => 'Nuevo León', 
                                        '20' => 'Oaxaca', '21' => 'Puebla', '22' => 'Querétaro', 
                                        '23' => 'Quintana Roo', '24' => 'San Luis Potosí', '25' => 'Sinaloa', 
                                        '26' => 'Sonora', '27' => 'Tabasco', '28' => 'Tamaulipas', 
                                        '29' => 'Tlaxcala', '30' => 'Veracruz', '31' => 'Yucatán', '32' => 'Zacatecas'
                                    ];
                                    $entidadId = $datosApoderado['entidad_federativa'] ?? '';
                                @endphp
                                {{ $estados[$entidadId] ?? 'No especificado' }}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Escritura</label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                                @php
                                    $fechaEscritura = $datosApoderado['fecha_escritura'] ?? '';
                                    if (!empty($fechaEscritura) && $fechaEscritura !== 'No disponible') {
                                        try {
                                            $fechaEscrituraFormateada = \Carbon\Carbon::parse($fechaEscritura)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $fechaEscrituraFormateada = $fechaEscritura;
                                        }
                                    } else {
                                        $fechaEscrituraFormateada = 'No especificado';
                                    }
                                @endphp
                                {{ $fechaEscrituraFormateada }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos de Inscripción -->
                <div class="mt-8">
                    <div class="flex items-center space-x-2 mb-6">
                        <i class="fas fa-book text-[#9d2449]"></i>
                        <h4 class="text-lg font-medium text-gray-700">Datos de Inscripción en el Registro Público</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Número de Registro o Folio Mercantil</label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                                {{ $datosApoderado['numero_registro'] ?? 'No especificado' }}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inscripción</label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                                @php
                                    $fechaInscripcionApoderado = $datosApoderado['fecha_inscripcion'] ?? '';
                                    if (!empty($fechaInscripcionApoderado) && $fechaInscripcionApoderado !== 'No disponible') {
                                        try {
                                            $fechaInscripcionApoderadoFormateada = \Carbon\Carbon::parse($fechaInscripcionApoderado)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $fechaInscripcionApoderadoFormateada = $fechaInscripcionApoderado;
                                        }
                                    } else {
                                        $fechaInscripcionApoderadoFormateada = 'No especificado';
                                    }
                                @endphp
                                {{ $fechaInscripcionApoderadoFormateada }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Mensaje cuando no hay datos de apoderado -->
                <div class="text-center py-8">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <i class="fas fa-user-tie text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">No hay información del apoderado legal registrada para este trámite.</p>
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

        <form class="space-y-8" @submit.prevent="guardarApoderado" x-ref="apoderadoForm">
            <input type="hidden" name="action" value="next">
            <input type="hidden" name="seccion" value="5">
            <input type="hidden" name="tramite_id" :value="tramiteId">

            <!-- Datos del Apoderado -->
            <div class="space-y-6">
            <div class="flex items-center space-x-2 mb-6">
                    <i class="fas fa-user-tie text-[#9d2449]"></i>
                    <h4 class="text-lg font-medium text-gray-700">Datos del Apoderado o Representante Legal</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre del Apoderado -->
                    <div class="form-group">
                    <label for="nombre_apoderado" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                        <input type="text" 
                               id="nombre_apoderado" 
                               name="nombre_apoderado"
                               x-model="nombreApoderado"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   placeholder="Ej: Lic. Juan Pérez González"
                               maxlength="100"
                               required>
                        </div>
                    </div>

                    <!-- Número de Escritura -->
                    <div class="form-group">
                    <label for="numero_escritura" class="block text-sm font-medium text-gray-700 mb-2">
                            Número de Escritura
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                        <input type="text" 
                               id="numero_escritura" 
                               name="numero_escritura"
                               x-model="numeroEscritura"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   placeholder="Ej: 12345"
                               maxlength="15"
                               required>
                        </div>
                    </div>

                    <!-- Nombre del Notario -->
                    <div class="form-group">
                    <label for="nombre_notario" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Notario
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                        <input type="text" 
                               id="nombre_notario" 
                               name="nombre_notario"
                               x-model="nombreNotario"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   placeholder="Ej: Lic. María López Ramírez"
                               maxlength="100"
                               required>
                        </div>
                    </div>

                    <!-- Número del Notario -->
                    <div class="form-group">
                    <label for="numero_notario" class="block text-sm font-medium text-gray-700 mb-2">
                            Número del Notario
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                        <input type="text" 
                               id="numero_notario" 
                               name="numero_notario"
                               x-model="numeroNotario"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   placeholder="Ej: 123"
                               maxlength="10"
                               required>
                        </div>
                    </div>

                    <!-- Entidad Federativa -->
                    <div class="form-group">
                    <label for="entidad_federativa" class="block text-sm font-medium text-gray-700 mb-2">
                            Entidad Federativa
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                        <select id="entidad_federativa" 
                                name="entidad_federativa"
                                x-model="entidadFederativa"
                                class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40 appearance-none"
                                required>
                                <option value="">Seleccione un estado</option>
                            <option value="1">Aguascalientes</option>
                            <option value="2">Baja California</option>
                            <option value="3">Baja California Sur</option>
                            <option value="4">Campeche</option>
                            <option value="5">Coahuila</option>
                            <option value="6">Colima</option>
                            <option value="7">Chiapas</option>
                            <option value="8">Chihuahua</option>
                            <option value="9">Ciudad de México</option>
                            <option value="10">Durango</option>
                            <option value="11">Guanajuato</option>
                            <option value="12">Guerrero</option>
                            <option value="13">Hidalgo</option>
                            <option value="14">Jalisco</option>
                            <option value="15">México</option>
                            <option value="16">Michoacán</option>
                            <option value="17">Morelos</option>
                            <option value="18">Nayarit</option>
                            <option value="19">Nuevo León</option>
                            <option value="20">Oaxaca</option>
                            <option value="21">Puebla</option>
                            <option value="22">Querétaro</option>
                            <option value="23">Quintana Roo</option>
                            <option value="24">San Luis Potosí</option>
                            <option value="25">Sinaloa</option>
                            <option value="26">Sonora</option>
                            <option value="27">Tabasco</option>
                            <option value="28">Tamaulipas</option>
                            <option value="29">Tlaxcala</option>
                            <option value="30">Veracruz</option>
                            <option value="31">Yucatán</option>
                            <option value="32">Zacatecas</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Fecha de Escritura -->
                    <div class="form-group">
                    <label for="fecha_escritura" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Escritura
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                        <input type="date" 
                               id="fecha_escritura" 
                               name="fecha_escritura"
                               x-model="fechaEscritura"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                               required>
                        </div>
                    </div>
                </div>

                <!-- Datos de Inscripción -->
                <div class="mt-8">
                <div class="flex items-center space-x-2 mb-6">
                        <i class="fas fa-book text-[#9d2449]"></i>
                        <h4 class="text-lg font-medium text-gray-700">Datos de Inscripción en el Registro Público</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Número de Registro -->
                        <div class="form-group">
                        <label for="numero_registro" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Registro o Folio Mercantil
                                <span class="text-[#9d2449]">*</span>
                            </label>
                            <div class="relative group">
                            <input type="text" 
                                   id="numero_registro" 
                                   name="numero_registro"
                                   x-model="numeroRegistro"
                                       class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                       placeholder="Ej: 987654"
                                   maxlength="20"
                                   required>
                            </div>
                        </div>

                        <!-- Fecha de Inscripción -->
                        <div class="form-group">
                        <label for="fecha_inscripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Inscripción
                                <span class="text-[#9d2449]">*</span>
                            </label>
                            <div class="relative group">
                            <input type="date" 
                                   id="fecha_inscripcion" 
                                   name="fecha_inscripcion"
                                   x-model="fechaInscripcion"
                                       class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   required>
                        </div>
                    </div>
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
    @endif
</div>

<script>
function apoderadoData() {
    return {
        tramiteId: null,
        nombreApoderado: '',
        numeroEscritura: '',
        nombreNotario: '',
        numeroNotario: '',
        entidadFederativa: '',
        fechaEscritura: '',
        numeroRegistro: '',
        fechaInscripcion: '',
        loading: false,
        showError: false,
        errorMessage: '',
        showSuccess: false,
        successMessage: '',
        
        async init() {
            console.log('🚀 Inicializando componente de apoderado legal');
            
            // Obtener tramite_id
            const datosApoderado = @json($datosApoderado ?? []);
            const tramite = @json($tramite ?? null);
            
            console.log('📊 Datos recibidos:', { datosApoderado, tramite });
            
            if (datosApoderado && Object.keys(datosApoderado).length > 0 && datosApoderado.tramite_id) {
                console.log('📋 Cargando desde datosApoderado');
                this.tramiteId = datosApoderado.tramite_id;
                await this.cargarDatosDesdeObjeto(datosApoderado);
            } else if (tramite && tramite.id) {
                console.log('🔍 Cargando desde tramite.id:', tramite.id);
                this.tramiteId = tramite.id;
                await this.cargarDatosDesdeTramite(tramite.id);
            } else {
                console.log('⚠️ No se encontró tramite_id válido');
            }
            
            console.log('✅ Inicialización completada. tramiteId:', this.tramiteId);
        },

        async cargarDatosDesdeObjeto(datosApoderado) {
            try {
                console.log('📝 Cargando datos desde objeto:', datosApoderado);
                
                this.nombreApoderado = datosApoderado.nombre_apoderado || '';
                this.numeroEscritura = datosApoderado.numero_escritura || '';
                this.nombreNotario = datosApoderado.nombre_notario || '';
                this.numeroNotario = datosApoderado.numero_notario || '';
                this.entidadFederativa = datosApoderado.entidad_federativa ? String(datosApoderado.entidad_federativa) : '';
                this.fechaEscritura = datosApoderado.fecha_escritura || '';
                this.numeroRegistro = datosApoderado.numero_registro || '';
                this.fechaInscripcion = datosApoderado.fecha_inscripcion || '';
                
                console.log('✅ Datos cargados en el formulario:', {
                    nombreApoderado: this.nombreApoderado,
                    numeroEscritura: this.numeroEscritura,
                    nombreNotario: this.nombreNotario,
                    numeroNotario: this.numeroNotario,
                    entidadFederativa: this.entidadFederativa,
                    fechaEscritura: this.fechaEscritura,
                    numeroRegistro: this.numeroRegistro,
                    fechaInscripcion: this.fechaInscripcion
                });
            } catch (error) {
                console.error('❌ Error al cargar datos desde objeto:', error);
            }
        },

        async cargarDatosDesdeTramite(tramiteId) {
            try {
                console.log('🔍 Cargando datos de apoderado para trámite:', tramiteId);
                
                const response = await fetch(`/api/tramite/${tramiteId}/apoderado`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('📋 Datos de apoderado recibidos:', data);
                    
                    // Cargar datos tanto si success es true como false (para datos vacíos)
                    if (data.apoderado) {
                        await this.cargarDatosDesdeObjeto(data.apoderado);
                        
                        // Si hay datos reales (no solo estructura vacía), mostrar mensaje de éxito
                        if (data.success && data.apoderado.nombre_apoderado) {
                            console.log('✅ Datos de apoderado cargados exitosamente');
                            return true;
                        } else {
                            console.log('ℹ️ Estructura de apoderado inicializada (sin datos previos)');
                            return true;
                        }
                    }
                }
                return false;
            } catch (error) {
                console.error('❌ Error al cargar datos de apoderado:', error);
                return false;
            }
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

        async guardarApoderado() {
            if (this.loading) return;

            // Validaciones
            if (!this.tramiteId) {
                this.mostrarError('No se pudo identificar el trámite');
                return;
            }

            if (!this.nombreApoderado.trim()) {
                this.mostrarError('El nombre del apoderado es obligatorio');
                return;
            }

            if (!this.numeroEscritura.trim()) {
                this.mostrarError('El número de escritura es obligatorio');
                return;
            }

            if (!this.nombreNotario.trim()) {
                this.mostrarError('El nombre del notario es obligatorio');
                return;
            }

            if (!this.numeroNotario.trim()) {
                this.mostrarError('El número del notario es obligatorio');
                return;
            }

            if (!this.entidadFederativa) {
                this.mostrarError('La entidad federativa es obligatoria');
                return;
            }

            if (!this.fechaEscritura) {
                this.mostrarError('La fecha de escritura es obligatoria');
                return;
            }

            if (!this.numeroRegistro.trim()) {
                this.mostrarError('El número de registro es obligatorio');
                return;
            }

            if (!this.fechaInscripcion) {
                this.mostrarError('La fecha de inscripción es obligatoria');
                return;
            }

            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('tramite_id', this.tramiteId);
                formData.append('nombre_apoderado', this.nombreApoderado.trim());
                formData.append('numero_escritura', this.numeroEscritura.trim());
                formData.append('nombre_notario', this.nombreNotario.trim());
                formData.append('numero_notario', this.numeroNotario.trim());
                formData.append('entidad_federativa', this.entidadFederativa);
                formData.append('fecha_escritura', this.fechaEscritura);
                formData.append('numero_registro', this.numeroRegistro.trim());
                formData.append('fecha_inscripcion', this.fechaInscripcion);

                // Agregar CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }

                console.log('📤 Enviando datos de apoderado legal');

                const response = await fetch('/tramites/guardar-apoderado-formulario', {
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
                    this.mostrarExito('Datos del apoderado legal guardados correctamente');
                    
                    // Disparar evento para navegar al siguiente paso
                    setTimeout(() => {
                        this.$dispatch('next-step');
                    }, 1000);
                } else {
                    this.mostrarError(data.message || 'Error al guardar los datos del apoderado legal');
                    if (data.errors) {
                        console.error('Errores de validación:', data.errors);
                    }
                }
            } catch (error) {
                console.error('❌ Error al guardar apoderado legal:', error);
                this.mostrarError('Error de conexión. Por favor, intente nuevamente.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style>
/* Estilos base */
.form-group {
    @apply relative mb-4;
}

/* Estilos para campos con error */
.has-error input,
.has-error select {
    @apply border-red-300 !important;
}

.has-error .text-gray-500 {
    @apply text-red-500;
}

/* Transiciones y efectos hover */
input, select, button {
    @apply transition-all duration-300;
}

input:focus, select:focus {
    @apply outline-none ring-2 ring-[#4F46E5]/20 border-[#4F46E5];
}

/* Estilos para el select */
select {
    @apply cursor-pointer;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

/* Estilos para los asteriscos de campos requeridos */
.text-[#9d2449] {
    @apply inline-block ml-1;
}

/* Mejoras en la accesibilidad */
input:focus-visible,
select:focus-visible,
button:focus-visible {
    @apply ring-2 ring-offset-2 ring-[#4F46E5]/20;
}

/* Estilos para las notificaciones */
.notification {
    @apply fixed bottom-4 right-4 p-4 rounded-lg bg-white shadow-lg z-50 max-w-sm;
    animation: slideIn 0.3s ease-out;
}

.notification.error {
    @apply border-l-4 border-red-500;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Estilos para los iconos de sección */
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

/* Mejoras en la accesibilidad y estados hover */
.form-group:hover input,
.form-group:hover select {
    @apply border-[#9d2449]/30;
}

input:focus, select:focus {
    @apply ring-2 ring-[#9d2449]/20 border-[#9d2449];
    box-shadow: 0 0 0 1px rgba(157, 36, 73, 0.1), 
                0 2px 4px rgba(157, 36, 73, 0.05);
}

/* Estilos para los botones */
.btn-primary {
    @apply bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white;
}

.btn-primary:hover {
    @apply from-[#8a203f] to-[#7a1c38];
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(157, 36, 73, 0.1),
                0 2px 4px -1px rgba(157, 36, 73, 0.06);
}
</style>
