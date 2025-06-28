@props(['title' => 'Datos Generales', 'datosTramite' => [], 'datosSolicitante' => [], 'readonly' => false])

<!-- Asegúrate de incluir Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Incluir dependencias para validación y carga -->
<script src="{{ asset('js/components/loading-states.js') }}" defer></script>
<script src="{{ asset('js/validators/datos-generales-validator.js') }}" defer></script>

<script>
function datosGeneralesData() {
    return {
        tipoPersona: @json($datosSolicitante['tipo_persona'] ?? $datosTramite['tipo_persona'] ?? ''),
        rfc: @json($datosSolicitante['rfc'] ?? $datosTramite['rfc'] ?? ''),
        curp: @json($datosSolicitante['curp'] ?? $datosTramite['curp'] ?? ''),
        nombreCompleto: @json($datosSolicitante['nombre_completo'] ?? $datosTramite['nombre_completo'] ?? ''),
        razonSocial: @json($datosSolicitante['razon_social'] ?? $datosTramite['razon_social'] ?? ''),
        giro: @json($datosTramite['giro'] ?? ''),
        esEdicion: @json(isset($datosTramite['tramite_id']) && $datosTramite['tramite_id'] ? true : false),
        
        async init() {
                // Los datos ya están cargados desde el servidor

            // Test de conectividad con el controlador
            await this.testConectividad();
                
            // Inicializar validaciones después de que el DOM esté listo
                    this.$nextTick(() => {
                if (typeof initDatosGeneralesValidation === 'function') {
                    initDatosGeneralesValidation();
                }
            });
        },

        async testConectividad() {
            try {
                console.log('🧪 Probando conectividad con el controlador...');
                
                // Test 1: Ruta de controller con auth
                try {
                    const response1 = await fetch('/formularios/datos-generales/test', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data1 = await response1.json();
                    console.log('✅ Test de controlador con auth exitoso:', data1);
                } catch (error1) {
                    console.error('❌ Error en test de controlador con auth:', error1);
                }

                // Test 2: Ruta de debug simple sin auth
                try {
                    const response2 = await fetch('/debug/datos-generales', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            test: 'data',
                            timestamp: new Date().toISOString()
                        })
                    });
                    
                    const data2 = await response2.json();
                    console.log('✅ Test de ruta debug exitoso:', data2);
                } catch (error2) {
                    console.error('❌ Error en test de ruta debug:', error2);
                }

            } catch (error) {
                console.error('❌ Error general en test de conectividad:', error);
            }
        }
    }
}
</script>

<div x-data="datosGeneralesData()">


    <form id="datos-generales-form" action="{{ route('datos-generales.guardar') }}" method="POST" class="space-y-6" data-validate="true">
        @csrf
        <input type="hidden" name="action" value="next">
        <input type="hidden" name="seccion" value="1">
        
        @if(isset($datosTramite['tramite_id']))
            <input type="hidden" name="tramite_id" value="{{ $datosTramite['tramite_id'] }}">
        @endif
        
        @if(isset($datosTramite['tipo_tramite']))
            <input type="hidden" name="tipo_tramite" value="{{ $datosTramite['tipo_tramite'] }}">
        @endif

        <!-- Datos del Proveedor -->
        <div class="space-y-6 pt-2">
            <!-- Título de sección con icono -->
            <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-sm">
                    <i class="fas fa-building text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Datos del Proveedor</h3>
                    <p class="text-sm text-gray-500">Información general del solicitante</p>
                </div>
            </div>

        <!-- Información Principal -->
        <div class="space-y-4">
            <!-- Tipo de Proveedor y RFC -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tipo de Proveedor (Solo lectura) -->
                <div class="form-group">
                    <label for="tipo_persona" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Proveedor
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="tipo_persona"
                               name="tipo_persona"
                               value="{{ $datosSolicitante['tipo_persona'] ?? '' }}" 
                               class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                               aria-label="Tipo de proveedor"
                               readonly>
                        </div>
                </div>

                <!-- RFC (Solo lectura) -->
                <div class="form-group">
                    <label for="rfc" class="block text-sm font-medium text-gray-700 mb-2">
                        RFC
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="rfc"
                               name="rfc"
                               value="{{ $datosSolicitante['rfc'] ?? '' }}" 
                               class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                               aria-label="RFC"
                               readonly>
                    </div>
                </div>
            </div>

            <!-- CURP - Solo visible para persona física (Solo lectura) -->
            @if(($datosSolicitante['tipo_persona'] ?? '') === 'Física')
            <div class="form-group">
                <label for="curp" class="block text-sm font-medium text-gray-700 mb-2">
                    CURP
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" 
                           id="curp"
                           name="curp"
                           value="{{ $datosSolicitante['curp'] ?? '' }}" 
                           class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                           aria-label="CURP"
                           readonly>
                </div>
                </div>
            @endif

            <!-- Nombre Completo (Solo para persona física) -->
            @if(($datosSolicitante['tipo_persona'] ?? '') === 'Física')
            <div class="form-group">
                <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre Completo
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" 
                           id="nombre_completo"
                           name="nombre_completo"
                           value="{{ $datosSolicitante['nombre_completo'] ?? auth()->user()->name ?? '' }}" 
                           class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                           aria-label="Nombre completo"
                           readonly>
                </div>
            </div>
            @endif

            <!-- Razón Social (Solo para persona moral) -->
            @if(($datosSolicitante['tipo_persona'] ?? '') === 'Moral')
            <div class="form-group">
                <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                    Razón Social
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" 
                           id="razon_social"
                           name="razon_social"
                           value="{{ $datosSolicitante['razon_social'] ?? auth()->user()->name ?? '' }}" 
                           class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                           aria-label="Razón social"
                           readonly>
                </div>
            </div>
            @endif

            <!-- Giro -->
            <div class="form-group">
                <label for="giro" class="block text-sm font-medium text-gray-700 mb-2">
                    Giro
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative group">
                    <textarea id="giro" name="giro" rows="4"
                              class="block w-full px-4 py-2.5 {{ $readonly ? 'text-gray-600 bg-gray-100 border-gray-200 cursor-not-allowed' : 'text-gray-700 bg-white border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all hover:border-[#9d2449]/50' }} border rounded-lg resize-none @error('giro') border-red-500 @enderror"
                              placeholder="{{ $readonly ? '' : 'Describa el giro de la empresa' }}"
                              x-model="giro"
                              maxlength="500" 
                              minlength="10"
                              data-validation="required|minLength:10|maxLength:500"
                              aria-label="Giro de la empresa"
                              {{ $readonly ? 'readonly' : 'required' }}>{{ old('giro', $datosTramite['giro'] ?? '') }}</textarea>
                    @if(!$readonly)
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                        <span x-text="giro ? giro.length : 0">0</span>/500
                    </div>
                    @endif
                </div>
                @if(!$readonly)
                    @error('giro')
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('giro') }}</p>
                    @enderror
                @endif
            </div>
            </div>
        </div>

        <!-- Actividades Económicas -->
        <div class="space-y-6 pt-6 border-t border-gray-100">
            <!-- Título de sección con icono -->
            <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-sm">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Actividades Económicas</h3>
                    <p class="text-sm text-gray-500">{{ $readonly ? 'Actividades económicas registradas' : 'Selecciona las actividades económicas que realizas' }}</p>
                </div>
            </div>
        <div class="space-y-6">
            @if($readonly)
                <!-- Mostrar actividades en modo solo lectura -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Actividades Seleccionadas
                    </label>
                    <div class="p-4 bg-gray-100 border border-gray-200 rounded-lg">
                        @php
                            $actividades_ids = json_decode($datosTramite['actividades_seleccionadas'] ?? '[]', true);
                        @endphp
                        @if(empty($actividades_ids))
                            <p class="text-gray-500 italic">No hay actividades seleccionadas</p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach($actividades_ids as $actividad_id)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[#9d2449]/10 text-[#9d2449] border border-[#9d2449]/20">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Actividad ID: {{ $actividad_id }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Actividad en modo editable -->
                <div class="form-group">
                <label for="actividad_search" class="block text-sm font-medium text-gray-700 mb-2">
                    Buscar Actividades *
                    </label>
                
                <!-- Nota informativa -->
                <div class="mb-4 p-3 bg-gradient-to-r from-[#9d2449]/5 to-[#9d2449]/10 border border-[#9d2449]/20 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-lightbulb text-[#9d2449] text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700 font-medium mb-1">¿Cómo agregar tus actividades?</p>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Agrega las actividades económicas <span class="font-semibold text-[#9d2449]">tal como aparecen en tu constancia de situación fiscal</span>. 
                                Puedes buscar por palabras clave y agregar <span class="font-semibold text-[#9d2449]">todas las actividades</span> que realizas. Si no encuentras una actividad, puedes agregarla manualmente.
                            </p>
                    </div>
                    </div>
                </div>

                    <div class="relative group">
                    <!-- Input de búsqueda -->
                    <input type="text" 
                           id="actividad_search" 
                           placeholder="Escriba para buscar actividad..."
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all hover:border-[#9d2449]/50 @error('actividades_seleccionadas') border-red-500 @enderror"
                           aria-label="Buscar actividad"
                           autocomplete="off">
                    
                    <!-- Icono de búsqueda -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i id="actividad-search-icon" class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                    
                    <!-- Dropdown de resultados -->
                    <div id="actividad-dropdown" class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl hidden max-h-64 overflow-hidden backdrop-blur-sm">
                        <!-- Header del dropdown -->
                        <div class="px-4 py-3 bg-gradient-to-r from-[#9d2449]/5 to-[#9d2449]/10 border-b border-gray-100">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-search text-[#9d2449] mr-2"></i>
                                <span>Resultados de búsqueda</span>
                    </div>
                        </div>
                        
                        <!-- Contenedor de resultados con scroll -->
                        <div class="max-h-48 overflow-y-auto">
                            <div id="actividad-resultados">
                                <!-- Los resultados se cargarán aquí -->
                            </div>
                        </div>
                        
                        <!-- Mensaje sin resultados -->
                        <div id="actividad-no-resultados" class="px-6 py-8 text-center hidden">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-search text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm font-medium mb-2">No se encontraron actividades</p>
                                <p class="text-gray-400 text-xs mb-4">Intenta con otros términos de búsqueda</p>
                                
                                <!-- Botón para agregar actividad manualmente -->
                                <button type="button" 
                                        id="btn-agregar-manual"
                                        onclick="agregarActividadManual()"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white text-sm rounded-lg hover:from-[#8a203f] hover:to-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-md">
                                    <i class="fas fa-plus mr-2"></i>
                                    Agregar actividad personalizada
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @error('actividades_seleccionadas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
            </div>

            <!-- Tags de Actividades Seleccionadas -->
            <div id="actividades-seleccionadas" class="flex flex-wrap gap-3 p-4 bg-gradient-to-br from-[#9d2449]/5 to-white rounded-xl border border-[#9d2449]/20 shadow-sm min-h-[60px] transition-all duration-300 hover:shadow-md hover:border-[#9d2449]/30">
                <!-- Los tags se agregarán aquí dinámicamente -->
                <div class="flex items-center justify-center w-full text-gray-400 text-sm italic" id="no-actividades-message">
                    <i class="fas fa-plus-circle mr-2"></i>
                    No hay actividades seleccionadas
                </div>
            </div>

                <!-- Input oculto para almacenar las actividades seleccionadas -->
    <input type="hidden" id="actividades_seleccionadas_input" name="actividades_seleccionadas" value="{{ old('actividades_seleccionadas', $datosTramite['actividades_seleccionadas'] ?? '') }}">
            @endif
        </div>
        </div>

        <!-- Información Adicional -->
        <div class="space-y-6 pt-6 border-t border-gray-100">
            <!-- Título de sección con icono -->
            <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-sm">
                    <i class="fas fa-globe text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Información Adicional</h3>
                    <p class="text-sm text-gray-500">Datos opcionales del solicitante</p>
                </div>
            </div>

            <div class="form-group">
                <label for="pagina_web" class="block text-sm font-medium text-gray-700 mb-2">
                    Página Web
                </label>
                <div class="relative group">
                    <input type="url" id="pagina_web" name="pagina_web"
                           class="block w-full px-4 py-2.5 {{ $readonly ? 'text-gray-600 bg-gray-100 border-gray-200 cursor-not-allowed' : 'text-gray-700 bg-white border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all hover:border-[#9d2449]/50' }} border rounded-lg @error('pagina_web') border-red-500 @enderror"
                           placeholder="{{ $readonly ? '' : 'https://www.ejemplo.com' }}"
                           data-validation="url"
                           aria-label="Página web"
                           value="{{ old('pagina_web', $datosTramite['pagina_web'] ?? $datosSolicitante['pagina_web'] ?? '') }}"
                           {{ $readonly ? 'readonly' : '' }}>
                </div>
                @if(!$readonly)
                    @error('pagina_web')
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('pagina_web') }}</p>
                    @enderror
                @endif
            </div>
        </div>

        <!-- Datos de Contacto -->
        <div class="space-y-6 pt-6 border-t border-gray-100">
            <!-- Título de sección con icono mejorado -->
            <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-sm">
                    <i class="fas fa-address-card text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Datos de Contacto</h3>
                    <p class="text-sm text-gray-500">Persona de referencia para comunicaciones</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="form-group">
                    <label for="contacto_nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="contacto_nombre" name="contacto_nombre"
                               class="block w-full px-4 py-2.5 {{ $readonly ? 'text-gray-600 bg-gray-100 border-gray-200 cursor-not-allowed' : 'text-gray-700 bg-white border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all hover:border-[#9d2449]/50' }} border rounded-lg @error('contacto_nombre') border-red-500 @enderror"
                               placeholder="{{ $readonly ? '' : 'Nombre completo del contacto' }}"
                               maxlength="100"
                               minlength="2"
                               data-validation="required|minLength:2|maxLength:100|text"
                               aria-label="Nombre del contacto"
                               value="{{ old('contacto_nombre', $datosTramite['contacto_nombre'] ?? $datosSolicitante['contacto_nombre'] ?? '') }}" 
                               {{ $readonly ? 'readonly' : 'required' }}>
                    </div>
                    @if(!$readonly)
                        @error('contacto_nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('contacto_nombre') }}</p>
                        @enderror
                    @endif
                </div>

                <!-- Cargo -->
                <div class="form-group">
                    <label for="contacto_cargo" class="block text-sm font-medium text-gray-700 mb-2">
                        Cargo o Puesto
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="contacto_cargo" name="contacto_cargo"
                               class="block w-full px-4 py-2.5 {{ $readonly ? 'text-gray-600 bg-gray-100 border-gray-200 cursor-not-allowed' : 'text-gray-700 bg-white border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all hover:border-[#9d2449]/50' }} border rounded-lg @error('contacto_cargo') border-red-500 @enderror"
                               placeholder="{{ $readonly ? '' : 'Cargo en la empresa' }}"
                               maxlength="50"
                               minlength="2"
                               data-validation="required|minLength:2|maxLength:50|text"
                               aria-label="Cargo del contacto"
                               value="{{ old('contacto_cargo', $datosTramite['contacto_cargo'] ?? $datosSolicitante['contacto_cargo'] ?? '') }}" 
                               {{ $readonly ? 'readonly' : 'required' }}>
                    </div>
                    @if(!$readonly)
                        @error('contacto_cargo')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('contacto_cargo') }}</p>
                        @enderror
                    @endif
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="contacto_correo" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <input type="email" id="contacto_correo" name="contacto_correo"
                               class="block w-full px-4 py-2.5 {{ $readonly ? 'text-gray-600 bg-gray-100 border-gray-200 cursor-not-allowed' : 'text-gray-700 bg-white border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all hover:border-[#9d2449]/50' }} border rounded-lg @error('contacto_correo') border-red-500 @enderror"
                               placeholder="{{ $readonly ? '' : 'correo@ejemplo.com' }}"
                               data-validation="required|email"
                               aria-label="Correo del contacto"
                               value="{{ old('contacto_correo', $datosTramite['contacto_correo'] ?? $datosSolicitante['contacto_correo'] ?? '') }}" 
                               {{ $readonly ? 'readonly' : 'required' }}>
                    </div>
                    @if(!$readonly)
                        @error('contacto_correo')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('contacto_correo') }}</p>
                        @enderror
                    @endif
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label for="contacto_telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono de Contacto
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <input type="tel" id="contacto_telefono" name="contacto_telefono"
                               class="block w-full px-4 py-2.5 {{ $readonly ? 'text-gray-600 bg-gray-100 border-gray-200 cursor-not-allowed' : 'text-gray-700 bg-white border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all hover:border-[#9d2449]/50' }} border rounded-lg @error('contacto_telefono') border-red-500 @enderror"
                               placeholder="{{ $readonly ? '' : '10 dígitos' }}"
                               pattern="[0-9]{10}"
                               maxlength="10"
                               minlength="10"
                               inputmode="numeric"
                               data-validation="required|phone|minLength:10|maxLength:10"
                               aria-label="Teléfono del contacto"
                               value="{{ old('contacto_telefono', $datosTramite['contacto_telefono'] ?? $datosSolicitante['contacto_telefono'] ?? '') }}" 
                               {{ $readonly ? 'readonly' : 'required' }}>
                    </div>
                    @if(!$readonly)
                        @error('contacto_telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('contacto_telefono') }}</p>
                        @enderror
                    @endif
            </div>
        </div>
        </div>

        @if(!$readonly)
            @if(!isset($mostrar_navegacion) || $mostrar_navegacion !== false)
            <!-- Botones de navegación -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="button" 
                        id="btn-guardar-datos-generales"
                        onclick="guardarYSiguiente()"
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white rounded-lg hover:from-[#8a203f] hover:to-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <span id="btn-text-datos-generales">
                        <i class="fas fa-save mr-2"></i> Guardar y Continuar <i class="fas fa-arrow-right ml-2"></i>
                    </span>
                    <span id="btn-loading-datos-generales" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 004 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            </div>
            @else
            <!-- Botón navegación integrado cuando mostrar_navegacion es false -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="button" 
                        id="btn-guardar-datos-generales-alt"
                        onclick="guardarYSiguiente()"
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white rounded-lg hover:from-[#8a203f] hover:to-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <span id="btn-text-datos-generales-alt">
                        <i class="fas fa-save mr-2"></i> Guardar y Continuar <i class="fas fa-arrow-right ml-2"></i>
                    </span>
                    <span id="btn-loading-datos-generales-alt" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            </div>
            @endif
        @else
            <!-- Mensaje informativo en modo solo lectura -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <div class="text-center text-gray-500">
                    <i class="fas fa-eye mr-2"></i>
                    Modo solo lectura - Los datos no pueden ser modificados
                </div>
            </div>
        @endif
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Solo ejecutar en modo editable
    @if($readonly)
        return; // No ejecutar JavaScript en modo solo lectura
    @endif

    // Variables
    const searchInput = document.getElementById('actividad_search');
    const dropdown = document.getElementById('actividad-dropdown');
    const resultados = document.getElementById('actividad-resultados');
    const noResultados = document.getElementById('actividad-no-resultados');
    const searchIcon = document.getElementById('actividad-search-icon');
    const tagsContainer = document.getElementById('actividades-seleccionadas');
    const noActivitiesMessage = document.getElementById('no-actividades-message');
    const hiddenInput = document.getElementById('actividades_seleccionadas_input');
    
    let searchTimeout;
    let selectedIndex = -1;
    let actividadesSeleccionadas = [];

    // Función para mostrar loading
    function mostrarLoading() {
        searchIcon.className = 'fas fa-spinner fa-spin text-[#9d2449] text-sm';
    }

    // Función para ocultar loading
    function ocultarLoading() {
        searchIcon.className = 'fas fa-search text-gray-400 text-sm';
    }

    // Función para buscar actividades
    async function buscarActividades(query) {
        if (!query || query.length < 2) {
            ocultarDropdown();
            return;
        }

        mostrarLoading();

        try {
            const response = await fetch(`/api/actividades/buscar?q=${encodeURIComponent(query)}&limit=20`);
            
            if (!response.ok) {
                throw new Error('Error en la búsqueda');
            }

            const result = await response.json();
            
            if (result.success && result.data) {
                // Filtrar actividades ya seleccionadas
                const actividadesFiltradas = result.data.filter(actividad => 
                    !actividadesSeleccionadas.some(sel => sel.id === actividad.id)
                );
                mostrarResultados(actividadesFiltradas, query);
            } else {
                mostrarSinResultados();
            }
        } catch (error) {
            console.error('Error buscando actividades:', error);
            mostrarSinResultados();
        } finally {
            ocultarLoading();
        }
    }

    // Función para mostrar resultados
    function mostrarResultados(actividades, query) {
        resultados.innerHTML = '';
        noResultados.classList.add('hidden');
        selectedIndex = -1;

        if (actividades.length === 0) {
            mostrarSinResultados();
            return;
        }

        actividades.forEach((actividad, index) => {
            const item = document.createElement('div');
            item.className = 'px-4 py-3 cursor-pointer transition-all duration-200 border-b border-gray-100/50 last:border-b-0 hover:bg-gradient-to-r hover:from-[#9d2449]/5 hover:to-[#9d2449]/10 hover:text-[#9d2449] group';
            item.dataset.id = actividad.id;
            item.dataset.index = index;
            
            const nombre = resaltarTexto(actividad.nombre, query);
            const sector = typeof actividad.sector === 'string' ? actividad.sector : 'Sin sector';
            
            item.innerHTML = `
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900 group-hover:text-[#9d2449] transition-colors text-sm leading-tight">${nombre}</div>
                        <div class="flex items-center mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 group-hover:bg-[#9d2449]/10 group-hover:text-[#9d2449] transition-colors">
                                <i class="fas fa-building mr-1 text-xs"></i>
                                ${sector}
                            </span>
                        </div>
                    </div>
                    <div class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-plus-circle text-[#9d2449] text-lg"></i>
                    </div>
                </div>
            `;

            item.addEventListener('click', () => agregarActividad(actividad));
            item.addEventListener('mouseenter', () => {
                selectedIndex = index;
                actualizarSeleccionVisual();
            });

            resultados.appendChild(item);
        });

        mostrarDropdown();
    }

    // Función para mostrar sin resultados
    function mostrarSinResultados() {
        resultados.innerHTML = '';
        noResultados.classList.remove('hidden');
        
        // Asegurar que el botón esté disponible
        const btnAgregar = document.getElementById('btn-agregar-manual');
        if (btnAgregar) {
            btnAgregar.style.display = 'inline-flex';
        }
        
        mostrarDropdown();
    }

    // Función para mostrar dropdown
    function mostrarDropdown() {
        dropdown.classList.remove('hidden');
    }

    // Función para ocultar dropdown
    function ocultarDropdown() {
        dropdown.classList.add('hidden');
        selectedIndex = -1;
    }

    // Función para agregar actividad (selección múltiple)
    function agregarActividad(actividad) {
        // Verificar si ya está seleccionada
        if (actividadesSeleccionadas.some(sel => sel.id === actividad.id)) {
            return;
        }

        // Agregar a la lista
        actividadesSeleccionadas.push(actividad);
        
        // Limpiar búsqueda
        searchInput.value = '';
        
        // Actualizar interfaz
        actualizarTags();
        actualizarInputHidden();
        
        // Cerrar dropdown
        ocultarDropdown();
    }

    // Función para remover actividad
    function removerActividad(actividadId) {
        actividadesSeleccionadas = actividadesSeleccionadas.filter(act => act.id !== actividadId);
        actualizarTags();
        actualizarInputHidden();
    }

    // Función para actualizar tags visuales
    function actualizarTags() {
        // Limpiar contenedor
        tagsContainer.innerHTML = '';
        
        if (actividadesSeleccionadas.length === 0) {
            // Mostrar mensaje vacío
            const emptyMessage = document.createElement('div');
            emptyMessage.id = 'no-actividades-message';
            emptyMessage.className = 'flex items-center justify-center w-full text-gray-400 text-sm italic';
            emptyMessage.innerHTML = '<i class="fas fa-plus-circle mr-2"></i> No hay actividades seleccionadas';
            tagsContainer.appendChild(emptyMessage);
        } else {
            // Mostrar tags
            actividadesSeleccionadas.forEach(actividad => {
                const tag = document.createElement('div');
                
                // Estilo diferente para actividades personalizadas
                const isCustom = actividad.custom || false;
                const baseClass = 'inline-flex items-center gap-2 px-4 py-2 rounded-lg border text-sm font-medium transition-all duration-200 hover:shadow-sm animate-pulse-once';
                
                if (isCustom) {
                    tag.className = baseClass + ' bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 border-blue-200 hover:from-blue-100 hover:to-blue-150';
                } else {
                    tag.className = baseClass + ' bg-gradient-to-r from-[#9d2449]/10 to-[#9d2449]/15 text-[#9d2449] border-[#9d2449]/20 hover:from-[#9d2449]/15 hover:to-[#9d2449]/20';
                }
                
                const sector = (typeof actividad.sector === 'string' && actividad.sector) ? ` - ${actividad.sector}` : '';
                const icon = isCustom ? 'fas fa-edit' : 'fas fa-check-circle';
                const iconColor = isCustom ? 'text-blue-600' : 'text-[#9d2449]';
                
                tag.innerHTML = `
                    <span class="flex items-center gap-1">
                        <i class="${icon} ${iconColor}"></i>
                        <span class="font-medium">${actividad.nombre}</span>
                        <span class="text-xs opacity-75">${sector}</span>
                    </span>
                    <button type="button" class="ml-1 hover:text-red-500 transition-colors duration-150 rounded-full p-1 hover:bg-red-100" onclick="removerActividad(${actividad.id})">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                
                tagsContainer.appendChild(tag);
            });
        }
    }

    // Función para actualizar input hidden
    function actualizarInputHidden() {
        const actividadesIds = actividadesSeleccionadas.map(act => act.id);
        hiddenInput.value = JSON.stringify(actividadesIds);
    }

    // Función para resaltar texto
    function resaltarTexto(texto, busqueda) {
        if (!busqueda) return texto;
        const regex = new RegExp(`(${busqueda.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return texto.replace(regex, '<mark class="bg-yellow-200 px-1 rounded">$1</mark>');
    }

    // Función para actualizar selección visual
    function actualizarSeleccionVisual() {
        const items = resultados.querySelectorAll('[data-index]');
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('bg-gradient-to-r', 'from-[#9d2449]/10', 'to-[#9d2449]/15', 'text-[#9d2449]');
                item.classList.remove('hover:from-[#9d2449]/5', 'hover:to-[#9d2449]/10');
            } else {
                item.classList.remove('bg-gradient-to-r', 'from-[#9d2449]/10', 'to-[#9d2449]/15', 'text-[#9d2449]');
                item.classList.add('hover:from-[#9d2449]/5', 'hover:to-[#9d2449]/10');
            }
        });
    }

    // Event listeners
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        searchTimeout = setTimeout(() => {
            buscarActividades(query);
        }, 300);
    });

    // Navegación con teclado
    searchInput.addEventListener('keydown', function(e) {
        const items = resultados.querySelectorAll('[data-index]');
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                actualizarSeleccionVisual();
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                actualizarSeleccionVisual();
                break;
                
            case 'Enter':
                e.preventDefault();
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    const actividadId = parseInt(items[selectedIndex].dataset.id);
                    const actividad = {
                        id: actividadId,
                        nombre: items[selectedIndex].querySelector('.font-medium').textContent.replace(/<[^>]*>/g, ''),
                        sector: items[selectedIndex].querySelector('.text-xs').textContent.trim()
                    };
                    agregarActividad(actividad);
                }
                break;
                
            case 'Escape':
                e.preventDefault();
                ocultarDropdown();
                break;
        }
    });

    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#actividad_search') && !e.target.closest('#actividad-dropdown')) {
            ocultarDropdown();
        }
    });

    // Función global para remover actividades (llamada desde HTML)
    window.removerActividad = removerActividad;

    // Función global para agregar actividad manual
    window.agregarActividadManual = function() {
        const query = searchInput.value.trim();
        
        if (!query || query.length < 2) {
            alert('Por favor escriba el nombre de la actividad que desea agregar (mínimo 2 caracteres).');
            searchInput.focus();
                return;
            }

        // Crear actividad personalizada
        const actividadPersonalizada = {
            id: Date.now(), // ID único temporal
            nombre: query,
            sector: 'Actividad personalizada',
            custom: true // Marcador para identificar actividades personalizadas
        };

        // Verificar si ya existe
        const yaExiste = actividadesSeleccionadas.some(act => 
            act.nombre.toLowerCase() === query.toLowerCase()
        );

        if (yaExiste) {
            alert('Esta actividad ya ha sido agregada.');
            return;
        }

        // Agregar la actividad
        agregarActividad(actividadPersonalizada);
        
        // Limpiar búsqueda y cerrar dropdown
        searchInput.value = '';
        ocultarDropdown();
        
        // Mostrar mensaje de confirmación
        setTimeout(() => {
            alert('Actividad personalizada agregada correctamente.');
        }, 100);
    };

    // Manejo del formulario con validaciones
    window.guardarYSiguiente = function() {
        console.log('🔧 Iniciando guardarYSiguiente');
        
        const form = document.getElementById('datos-generales-form');
        if (!form) {
            console.error('❌ Formulario datos-generales-form no encontrado');
            alert('Error: Formulario no encontrado');
            return;
        }
        
        console.log('✅ Formulario encontrado:', form);
        console.log('📋 Action del formulario:', form.action);
        
        // Verificar que form.action sea válido antes de usar replace
        const formActionStr = form.action ? String(form.action) : '';
        console.log('🔗 URL completa construida:', formActionStr);
        console.log('🌐 Base URL:', window.location.origin);
        
        // Solo usar replace si formActionStr es válido
        const rutaRelativa = formActionStr ? formActionStr.replace(window.location.origin, '') : '/formularios/datos-generales/guardar';
        console.log('📍 Ruta relativa:', rutaRelativa);
        
        const btnGuardar = document.getElementById('btn-guardar-datos-generales') || document.getElementById('btn-guardar-datos-generales-alt');
        const btnText = document.getElementById('btn-text-datos-generales') || document.getElementById('btn-text-datos-generales-alt');
        const btnLoading = document.getElementById('btn-loading-datos-generales') || document.getElementById('btn-loading-datos-generales-alt');

        // Validar formulario completo usando el validador
        if (typeof validarFormularioDatosGeneralesCompleto === 'function') {
            console.log('🔍 Ejecutando validaciones del cliente');
            if (!validarFormularioDatosGeneralesCompleto()) {
                console.log('❌ Validación del cliente falló');
                return; // El validador ya muestra los errores
            }
            console.log('✅ Validación del cliente exitosa');
        } else {
            console.log('⚠️ Validador no disponible, usando fallback');
            // Fallback: validar que haya al menos una actividad seleccionada
            if (actividadesSeleccionadas.length === 0) {
                alert('Debe seleccionar al menos una actividad económica.');
                return;
            }
        }

        // Mostrar loading
        if (btnGuardar) btnGuardar.disabled = true;
        if (btnText) btnText.classList.add('hidden');
        if (btnLoading) btnLoading.classList.remove('hidden');

        console.log('📤 Preparando envío del formulario');
        
        // Enviar formulario
        const formData = new FormData(form);
        
        // Debug: mostrar datos del formulario
        console.log('📋 Datos del formulario:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}:`, value);
        }

        // Construir URL absoluta para debug
        const baseUrl = window.location.origin;
        // Forzar la URL correcta del servidor
        const absoluteUrl = baseUrl + '/formularios/datos-generales/guardar';
        
        console.log('🔗 URL base:', baseUrl);
        console.log('📋 Action original:', formActionStr);
        console.log('🎯 URL absoluta construida:', absoluteUrl);

        fetch(absoluteUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('📥 Respuesta recibida:', response.status, response.statusText);
        if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(result => {
            console.log('✅ Resultado procesado:', result);
        if (result.success) {
                console.log('🎉 Guardado exitoso, navegando al siguiente paso');
                // Navegar al siguiente paso
    if (typeof window.navegarSiguiente === 'function') {
                    console.log('🔄 Llamando a window.navegarSiguiente()');
        window.navegarSiguiente();
                } else {
                    console.log('⚠️ window.navegarSiguiente no disponible, recargando página');
                    location.reload();
                }
            } else {
                console.error('❌ Error del servidor:', result.message || result.errors);
                const errorMsg = result.message || (result.errors ? Object.values(result.errors).flat().join(', ') : 'Error al guardar');
                alert('Error: ' + errorMsg);
            }
        })
        .catch(error => {
            console.error('💥 Error en fetch:', error);
            alert('Error de conexión: ' + error.message);
        })
        .finally(() => {
            console.log('🔄 Limpiando estado de loading');
            // Ocultar loading
            if (btnGuardar) btnGuardar.disabled = false;
            if (btnText) btnText.classList.remove('hidden');
            if (btnLoading) btnLoading.classList.add('hidden');
        });
    };

});
</script>

<style>
/* Animación para los nuevos tags */
@keyframes pulse-once {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.animate-pulse-once {
    animation: pulse-once 0.3s ease-out;
}

/* Scrollbar personalizada para dropdown */
#actividad-dropdown .max-h-48::-webkit-scrollbar {
    width: 8px;
}

#actividad-dropdown .max-h-48::-webkit-scrollbar-track {
    background: linear-gradient(to bottom, rgba(157, 36, 73, 0.05), rgba(157, 36, 73, 0.1));
    border-radius: 4px;
    margin: 4px 0;
}

#actividad-dropdown .max-h-48::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, rgba(157, 36, 73, 0.3), rgba(157, 36, 73, 0.5));
    border-radius: 4px;
    border: 1px solid rgba(157, 36, 73, 0.1);
}

#actividad-dropdown .max-h-48::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, rgba(157, 36, 73, 0.5), rgba(157, 36, 73, 0.7));
}

/* Animación para el dropdown */
#actividad-dropdown {
    animation: dropdownSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    transform-origin: top;
}

@keyframes dropdownSlideIn {
    0% {
        opacity: 0;
        transform: translateY(-10px) scale(0.95);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Efectos hover suaves */
.form-group:hover input,
.form-group:hover select,
.form-group:hover textarea {
    border-color: rgb(157 36 73 / 0.5);
}

/* Transiciones suaves */
input, select, textarea, button {
    transition: all 0.2s ease-in-out;
}

/* Estilos de validación */
.border-green-300 {
    border-color: rgb(134 239 172) !important;
}

.focus\:border-green-500:focus {
    border-color: rgb(34 197 94) !important;
}

.focus\:ring-green-200:focus {
    --tw-ring-color: rgb(187 247 208) !important;
}

.border-red-500 {
    border-color: rgb(239 68 68) !important;
}

.focus\:border-red-500:focus {
    border-color: rgb(239 68 68) !important;
}

.focus\:ring-red-200:focus {
    --tw-ring-color: rgb(254 202 202) !important;
}

/* Animación para mensajes de error */
.error-message {
    animation: slideInError 0.3s ease-out;
}

@keyframes slideInError {
    0% {
        opacity: 0;
        transform: translateY(-10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>