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
        sectorId: @json($datosTramite['sector_id'] ?? ''),
        
        init() {
            // Cargar datos si es edición
            if (this.esEdicion) {
                // Los datos ya están cargados desde el servidor

                
                // Cargar actividades del sector si existe
                if (this.sectorId) {
                    this.$nextTick(() => {
                        // Disparar el evento change del select de sector para cargar actividades
                        const sectorSelect = document.getElementById('sector_id');
                        if (sectorSelect) {
                            sectorSelect.dispatchEvent(new Event('change'));
                        }
                    });
                }
            }
            

        }
    }
}
</script>

<div x-data="datosGeneralesData()">
    <!-- Indicador de modo edición (si está editando) -->
    <div x-show="esEdicion" class="flex items-center px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg mb-6">
        <i class="fas fa-edit text-amber-600 mr-2"></i>
        <span class="text-sm text-amber-700 font-medium">Editando datos existentes</span>
    </div>

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

        <!-- Información Principal -->
        <div class="space-y-4">
            <!-- Tipo de Proveedor y RFC -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tipo de Proveedor (Solo lectura) -->
                <div class="form-group">
                    <label for="tipo_persona" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Proveedor
                        <span class="text-[#9d2449]">*</span>
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
                        <span class="text-[#9d2449]">*</span>
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
                    <span class="text-[#9d2449]">*</span>
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
                    <span class="text-[#9d2449]">*</span>
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
                    <span class="text-[#9d2449]">*</span>
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
                    <span class="text-[#9d2449]">*</span>
                </label>
                <div class="relative group">
                    <textarea id="giro" name="giro" rows="4"
                              class="block w-full px-4 py-2.5 {{ $readonly ? 'text-gray-600 bg-gray-100 border-gray-200 cursor-not-allowed' : 'text-gray-700 bg-white border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50' }} border rounded-lg resize-none @error('giro') border-red-500 @enderror"
                              placeholder="{{ $readonly ? '' : 'Describa el giro de la empresa' }}"
                              x-model="giro"
                              maxlength="500" 
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



        <!-- Sector y Actividad -->
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Sector -->
                <div class="form-group">
                    <label for="sector_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Sector
                    </label>
                    <div class="relative group">
                        <select id="sector_id" name="sector_id"
                                class="block w-full px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50 @error('sector_id') border-red-500 @enderror"
                                aria-label="Seleccionar sector">
                            <option value="">Seleccione un Sector</option>
                            @foreach(\App\Models\Sector::all() as $sector)
                                <option value="{{ $sector->id }}" 
                                        data-nombre="{{ $sector->nombre }}"
                                        title="{{ $sector->nombre }}"
                                        {{ old('sector_id', $datosTramite['sector_id'] ?? '') == $sector->id ? 'selected' : '' }}>
                                    {{ \Illuminate\Support\Str::limit($sector->nombre, 40) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </div>
                    </div>
                    @error('sector_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actividad -->
                <div class="form-group">
                    <label for="actividad_search" class="block text-sm font-medium text-gray-700 mb-2">
                        Actividad
                    </label>
                    <div class="relative group">
                        <!-- Input de búsqueda -->
                        <input type="text" 
                               id="actividad_search" 
                               placeholder="Escriba para buscar actividad..."
                               class="block w-full px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50 @error('actividad_id') border-red-500 @enderror"
                               aria-label="Buscar actividad"
                               autocomplete="off"
                               disabled>
                        
                        <!-- Icono de búsqueda -->
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i id="actividad-search-icon" class="fas fa-search text-gray-400 text-sm"></i>
                            <i id="actividad-loading-icon" class="fas fa-spinner fa-spin text-gray-400 text-sm hidden"></i>
                        </div>
                        
                        <!-- Dropdown de resultados -->
                        <div id="actividad-dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                            <div id="actividad-resultados" class="py-1">
                                <!-- Los resultados se cargarán aquí -->
                            </div>
                            <div id="actividad-no-resultados" class="px-3 py-2 text-sm text-gray-500 text-center hidden">
                                No se encontraron actividades
                            </div>
                        </div>
                        
                        <!-- Select oculto para compatibilidad -->
                        <select id="actividad_id" name="actividad_id" class="hidden" aria-label="Actividad seleccionada">
                            <option value="">Seleccione una actividad</option>
                        </select>
                    </div>
                    @error('actividad_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tags de Actividades Seleccionadas -->
            <div id="actividades-seleccionadas" class="flex flex-wrap gap-3 p-4 bg-white rounded-xl border border-gray-100 shadow-sm min-h-[60px] transition-all duration-300">
                <!-- Los tags se agregarán aquí dinámicamente -->
                <div class="flex items-center justify-center w-full text-gray-400 text-sm italic" id="no-actividades-message">
                    No hay actividades seleccionadas
                </div>
            </div>

                <!-- Input oculto para almacenar las actividades seleccionadas -->
    <input type="hidden" id="actividades_seleccionadas_input" name="actividades_seleccionadas" value="{{ old('actividades_seleccionadas', $datosTramite['actividades_seleccionadas'] ?? '') }}">
            @error('actividades_seleccionadas')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Página Web -->
        <div class="space-y-4 pt-6 border-t border-gray-100">
            <div class="form-group">
                <label for="pagina_web" class="block text-sm font-medium text-gray-700 mb-2">
                    Página Web
                </label>
                <div class="relative group">
                    <input type="url" id="pagina_web" name="pagina_web"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50 @error('pagina_web') border-red-500 @enderror"
                           placeholder="https://www.ejemplo.com"
                           aria-label="Página web"
                           value="{{ old('pagina_web', $datosTramite['pagina_web'] ?? $datosSolicitante['pagina_web'] ?? '') }}">
                </div>
                @error('pagina_web')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Datos de Contacto -->
        <div class="space-y-6 pt-6 border-t border-gray-100">
            <!-- Título de sección con icono mejorado -->
            <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-[#9d2449]/10 text-[#9d2449] shadow-sm">
                    <i class="fas fa-address-card text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Datos de Contacto</h3>
                    <p class="text-sm text-gray-500">Persona encargada de recibir solicitudes</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="form-group">
                    <label for="contacto_nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="contacto_nombre" name="contacto_nombre"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50 @error('contacto_nombre') border-red-500 @enderror"
                               placeholder="Nombre completo del contacto"
                               maxlength="100"
                               minlength="2"
                               data-validation="required|minLength:2|maxLength:100|alphanumeric"
                               aria-label="Nombre del contacto"
                               value="{{ old('contacto_nombre', $datosTramite['contacto_nombre'] ?? $datosSolicitante['contacto_nombre'] ?? '') }}" required>
                    </div>
                    @error('contacto_nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('contacto_nombre') }}</p>
                    @enderror
                </div>

                <!-- Cargo -->
                <div class="form-group">
                    <label for="contacto_cargo" class="block text-sm font-medium text-gray-700 mb-2">
                        Cargo o Puesto
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="contacto_cargo" name="contacto_cargo"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50 @error('contacto_cargo') border-red-500 @enderror"
                               placeholder="Cargo en la empresa"
                               maxlength="50"
                               minlength="2"
                               data-validation="required|minLength:2|maxLength:50|alphanumeric"
                               aria-label="Cargo del contacto"
                               value="{{ old('contacto_cargo', $datosTramite['contacto_cargo'] ?? $datosSolicitante['contacto_cargo'] ?? '') }}" required>
                    </div>
                    @error('contacto_cargo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="contacto_correo" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="email" id="contacto_correo" name="contacto_correo"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50 @error('contacto_correo') border-red-500 @enderror"
                               placeholder="correo@ejemplo.com"
                               data-validation="required|email"
                               aria-label="Correo del contacto"
                               value="{{ old('contacto_correo', $datosTramite['contacto_correo'] ?? $datosSolicitante['contacto_correo'] ?? '') }}" required>
                    </div>
                    @error('contacto_correo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label for="contacto_telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono de Contacto
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="tel" id="contacto_telefono" name="contacto_telefono"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50 @error('contacto_telefono') border-red-500 @enderror"
                               placeholder="10 dígitos"
                               pattern="[0-9]{10}"
                               maxlength="10"
                               minlength="10"
                               inputmode="numeric"
                               data-validation="required|phone"
                               aria-label="Teléfono del contacto"
                               value="{{ old('contacto_telefono', $datosTramite['contacto_telefono'] ?? $datosSolicitante['contacto_telefono'] ?? '') }}" required>
                    </div>
                    @error('contacto_telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
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
                        class="w-full sm:w-auto px-6 py-3 bg-[#9d2449] text-white rounded-lg hover:bg-[#8a203f] transition-all duration-300 transform-gpu hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <span id="btn-text-datos-generales">
                        <i class="fas fa-save mr-2"></i> Guardar y Continuar <i class="fas fa-arrow-right ml-2"></i>
                    </span>
                    <span id="btn-loading-datos-generales" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
                        class="w-full sm:w-auto px-6 py-3 bg-[#9d2449] text-white rounded-lg hover:bg-[#8a203f] transition-all duration-300 transform-gpu hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <span id="btn-text-datos-generales-alt">
                        <i class="fas fa-save mr-2"></i> Guardar y Continuar <i class="fas fa-arrow-right ml-2"></i>
                    </span>
                    <span id="btn-loading-datos-generales-alt" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            </div>
            @endif
        @endif
    </form>
</div>

<style>
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

/* Estilos para los selects */
select option {
    padding: 8px;
    font-size: 0.875rem;
}

select option:hover {
    background-color: rgba(157, 36, 73, 0.1);
}

/* Tooltips personalizados */
[title] {
    position: relative;
}

[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 4px 8px;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    border-radius: 4px;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 10;
    margin-bottom: 4px;
}

/* Estilos para los tags de actividades */
#actividades-seleccionadas {
    position: relative;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
    backdrop-filter: blur(10px);
    border: 1px solid rgba(157, 36, 73, 0.1);
    transition: all 0.3s ease;
}

/* Estados de validación para el contenedor de actividades */
#actividades-seleccionadas.border-green-300 {
    border-color: rgb(34, 197, 94) !important;
    box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.1), 0 2px 4px rgba(34, 197, 94, 0.05);
}

#actividades-seleccionadas.bg-green-50 {
    /* Se aplica junto con border-green-300 pero sin cambiar el fondo */
}

#actividades-seleccionadas.border-red-300 {
    border-color: rgb(239, 68, 68) !important;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.08), rgba(239, 68, 68, 0.12)) !important;
    box-shadow: 0 0 0 1px rgba(239, 68, 68, 0.1), 0 2px 4px rgba(239, 68, 68, 0.05);
}

#actividades-seleccionadas.bg-red-50 {
    /* Se aplica junto con border-red-300 */
}

/* Efecto de pulso suave para estado válido */
#actividades-seleccionadas.border-green-300 {
    animation: gentle-pulse-green 2s infinite;
}

@keyframes gentle-pulse-green {
    0%, 100% {
        box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.1), 0 2px 4px rgba(34, 197, 94, 0.05);
    }
    50% {
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15), 0 4px 8px rgba(34, 197, 94, 0.1);
    }
}

/* Efecto de pulso para estado inválido */
#actividades-seleccionadas.border-red-300 {
    animation: gentle-pulse-red 1.5s infinite;
}

@keyframes gentle-pulse-red {
    0%, 100% {
        box-shadow: 0 0 0 1px rgba(239, 68, 68, 0.1), 0 2px 4px rgba(239, 68, 68, 0.05);
    }
    50% {
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2), 0 4px 8px rgba(239, 68, 68, 0.1);
    }
}

#actividades-seleccionadas .tag {
    @apply px-4 py-2 rounded-xl text-sm font-medium;
    background: linear-gradient(135deg, 
        rgba(157, 36, 73, 0.08) 0%,
        rgba(157, 36, 73, 0.12) 100%
    );
    border: 1px solid rgba(157, 36, 73, 0.15);
    color: #8a203f;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 
        0 2px 4px rgba(157, 36, 73, 0.06),
        0 1px 2px rgba(157, 36, 73, 0.04),
        inset 0 1px 1px rgba(255, 255, 255, 0.8);
    animation: tagAppear 0.4s cubic-bezier(0.26, 0.53, 0.74, 1.48) forwards;
    position: relative;
    overflow: hidden;
}

#actividades-seleccionadas .tag::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.4) 0%,
        rgba(255, 255, 255, 0) 100%
    );
    opacity: 0;
    transition: opacity 0.3s ease;
}

#actividades-seleccionadas .tag:hover {
    transform: translateY(-2px) scale(1.02);
    background: linear-gradient(135deg, 
        rgba(157, 36, 73, 0.12) 0%,
        rgba(157, 36, 73, 0.18) 100%
    );
    box-shadow: 
        0 4px 8px rgba(157, 36, 73, 0.1),
        0 2px 4px rgba(157, 36, 73, 0.06),
        inset 0 1px 1px rgba(255, 255, 255, 0.9);
    border-color: rgba(157, 36, 73, 0.25);
}

#actividades-seleccionadas .tag:hover::before {
    opacity: 1;
}

#actividades-seleccionadas .tag button {
    @apply rounded-full;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(157, 36, 73, 0.1);
    color: #9d2449;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

#actividades-seleccionadas .tag button::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle, rgba(157, 36, 73, 0.2) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.2s ease;
}

#actividades-seleccionadas .tag button:hover {
    background: rgba(157, 36, 73, 0.15);
    color: #7a1d37;
    transform: rotate(90deg) scale(1.1);
}

#actividades-seleccionadas .tag button:hover::before {
    opacity: 1;
}

#actividades-seleccionadas .tag button:active {
    transform: rotate(90deg) scale(0.95);
}

#actividades-seleccionadas .tag i {
    font-size: 0.75rem;
}

@keyframes tagAppear {
    0% {
        opacity: 0;
        transform: scale(0.8) translateY(10px);
    }
    70% {
        transform: scale(1.05) translateY(-2px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

#actividades-seleccionadas:empty::before {
    content: 'No hay actividades seleccionadas';
    @apply text-gray-400 text-sm italic absolute inset-0 flex items-center justify-center;
    background: linear-gradient(135deg, 
        rgba(157, 36, 73, 0.02) 0%,
        rgba(157, 36, 73, 0.05) 100%
    );
}

/* Estilo para el mensaje de no actividades */
#no-actividades-message {
    background: linear-gradient(135deg, 
        rgba(157, 36, 73, 0.02) 0%,
        rgba(157, 36, 73, 0.05) 100%
    );
    border-radius: 0.75rem;
    padding: 1rem;
}

/* Transiciones suaves */
.transition-all {
    transition: all 0.2s ease-in-out;
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

/* Mantener el color original para los iconos de sección */
.h-12, .h-9, .h-10 {
    @apply bg-gradient-to-br from-[#9d2449]/20 via-[#9d2449]/15 to-[#9d2449]/20;
}

/* Ajustar el color del anillo de focus para que coincida con los nuevos iconos */
input:focus, select:focus, textarea:focus {
    @apply ring-2 ring-[#4F46E5]/30;
    box-shadow: 0 0 0 1px rgba(79, 70, 229, 0.2), 
                0 2px 4px rgba(79, 70, 229, 0.05);
}

/* Ajustar el color del borde en hover */
.form-group:hover input,
.form-group:hover select,
.form-group:hover textarea {
    @apply border-[#4F46E5]/40;
}

/* Animación suave para los inputs */
input, select, textarea {
    @apply transition-all duration-300 bg-white shadow-sm;
}

input:focus, select:focus, textarea:focus {
    @apply transform -translate-y-px shadow-md bg-white;
}

/* Estilo para el scrollbar del textarea */
textarea::-webkit-scrollbar {
    width: 6px;
}

textarea::-webkit-scrollbar-track {
    @apply bg-[#9d2449]/10 rounded-r-lg;
}

textarea::-webkit-scrollbar-thumb {
    @apply bg-[#9d2449] rounded-full;
    opacity: 0.3;
}

textarea::-webkit-scrollbar-thumb:hover {
    opacity: 0.5;
}

/* Eliminar estilos por defecto de select */
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

/* Animaciones para los iconos de sección */
.h-12 {
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(157, 36, 73, 0.1), 
                0 2px 4px -1px rgba(157, 36, 73, 0.06);
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
        rgba(157, 36, 73, 0.2),
        transparent
    );
    transform: rotate(45deg);
    animation: shine 4s infinite;
}

@keyframes shine {
    0% {
        transform: translateX(-100%) rotate(45deg);
    }
    20%, 100% {
        transform: translateX(100%) rotate(45deg);
    }
}

/* Estilos para los inputs y selects */
input, select, textarea {
    @apply bg-white;
    box-shadow: 0 1px 3px rgba(157, 36, 73, 0.05);
}

/* Estilos para los tags */
.inline-flex {
    @apply bg-gradient-to-r from-[#9d2449]/10 to-[#9d2449]/20;
    box-shadow: 0 2px 4px rgba(157, 36, 73, 0.1);
}

/* Estilos para los títulos de sección */
.h-9, .h-10 {
    @apply bg-gradient-to-br from-[#9d2449]/20 via-[#9d2449]/15 to-[#9d2449]/20;
    box-shadow: 0 3px 6px rgba(157, 36, 73, 0.15);
}

/* Animación suave para los botones */
button {
    @apply transition-all duration-300;
}

button:hover {
    @apply transform scale-105;
}

/* Efecto de brillo para los iconos de sección */
.h-12:hover {
    animation: soft-pulse 2s infinite;
    box-shadow: 0 6px 8px -2px rgba(157, 36, 73, 0.15);
}

@keyframes soft-pulse {
    0% {
        box-shadow: 0 4px 6px -1px rgba(157, 36, 73, 0.1);
    }
    50% {
        box-shadow: 0 6px 12px -1px rgba(157, 36, 73, 0.2);
    }
    100% {
        box-shadow: 0 4px 6px -1px rgba(157, 36, 73, 0.1);
    }
}

/* Nuevos estilos para mejorar la apariencia de los inputs */
.form-group {
    @apply relative;
}

.form-group input,
.form-group select,
.form-group textarea {
    @apply border-[#9d2449]/20;
}

.form-group:hover input,
.form-group:hover select,
.form-group:hover textarea {
    @apply border-[#9d2449]/40;
}

.tag {
    @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800;
}

.tag button {
    @apply ml-2 text-gray-400 hover:text-gray-600 focus:outline-none;
}

#sector_id option,
#actividad_id option {
    @apply py-1;
}

/* Estilos específicos para validación de formulario */
.formulario__grupo-correcto input,
.formulario__grupo-correcto textarea,
.formulario__grupo-correcto select {
    @apply border-green-500 bg-green-50;
}

.formulario__grupo-incorrecto input,
.formulario__grupo-incorrecto textarea,
.formulario__grupo-incorrecto select {
    @apply border-red-500 bg-red-50;
}

.formulario__input-error {
    @apply text-red-600 text-sm mt-1;
}

.formulario__input-error-activo {
    @apply block;
}

.formulario__validacion-estado {
    @apply absolute right-3 top-1/2 transform -translate-y-1/2 text-lg z-10;
}

/* Animaciones para los iconos de validación */
.formulario__validacion-estado {
    transition: all 0.3s ease;
}

.formulario__grupo-correcto .formulario__validacion-estado {
    @apply text-green-500;
    animation: bounce-in 0.4s ease-out;
}

.formulario__grupo-incorrecto .formulario__validacion-estado {
    @apply text-red-500;
    animation: shake 0.4s ease-out;
}

@keyframes bounce-in {
    0% {
        transform: translate(-50%, -50%) scale(0);
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
    }
    100% {
        transform: translate(-50%, -50%) scale(1);
    }
}

@keyframes shake {
    0%, 100% {
        transform: translate(-50%, -50%) translateX(0);
    }
    25% {
        transform: translate(-50%, -50%) translateX(-5px);
    }
    75% {
        transform: translate(-50%, -50%) translateX(5px);
    }
}

/* Estilos para mensajes de notificación */
#mensaje-validacion-general {
    z-index: 9999;
}

#mensaje-validacion-general > div {
    animation: slide-in-right 0.3s ease-out;
}

@keyframes slide-in-right {
    0% {
        transform: translateX(100%);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Animación para notificaciones */
.animate-slide-in {
    animation: slide-in-right 0.3s ease-out;
}

/* Estilos para mensajes de error en campos */
.error-message {
    animation: fade-in 0.3s ease-out;
}

@keyframes fade-in {
    0% {
        opacity: 0;
        transform: translateY(-10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animaciones para el modal de errores */
.animate-modal-appear {
    animation: modal-appear 0.3s ease-out;
}

.animate-modal-disappear {
    animation: modal-disappear 0.3s ease-in;
}

@keyframes modal-appear {
    0% {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes modal-disappear {
    0% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
    100% {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
}

/* Scrollbar personalizada para el modal */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Efecto hover para botones del modal */
.bg-red-600:hover {
    background-color: #dc2626 !important;
}

/* Animación suave para el backdrop del modal */
#modal-errores-validacion {
    animation: backdrop-appear 0.3s ease-out;
}

@keyframes backdrop-appear {
    0% {
        background-color: rgba(0, 0, 0, 0);
    }
    100% {
        background-color: rgba(0, 0, 0, 0.5);
    }
}

/* Estilos para el dropdown de búsqueda de actividades */
#actividad-dropdown {
    border: 1px solid rgba(79, 70, 229, 0.2);
    box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.1), 
                0 10px 10px -5px rgba(79, 70, 229, 0.04);
    backdrop-filter: blur(10px);
    animation: dropdownSlideIn 0.2s ease-out;
}

#actividad-dropdown.show {
    display: block;
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

/* Estilo para cada resultado en el dropdown */
.actividad-resultado-item {
    padding: 8px 12px;
    cursor: pointer;
    transition: all 0.15s ease;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.actividad-resultado-item:last-child {
    border-bottom: none;
}

.actividad-resultado-item:hover {
    background: linear-gradient(135deg, 
        rgba(79, 70, 229, 0.08) 0%,
        rgba(79, 70, 229, 0.12) 100%
    );
    color: #4F46E5;
    transform: translateX(4px);
}

.actividad-resultado-item.selected {
    background: linear-gradient(135deg, 
        rgba(79, 70, 229, 0.12) 0%,
        rgba(79, 70, 229, 0.18) 100%
    );
    color: #4F46E5;
    font-weight: 500;
}

.actividad-resultado-nombre {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    line-height: 1.3;
}

.actividad-resultado-sector {
    font-size: 0.75rem;
    color: #6B7280;
    font-style: italic;
}

.actividad-resultado-item:hover .actividad-resultado-nombre {
    color: #4F46E5;
}

.actividad-resultado-item:hover .actividad-resultado-sector {
    color: #6366F1;
}

/* Resaltar texto coincidente */
.highlight {
    background-color: rgba(79, 70, 229, 0.2);
    font-weight: 600;
    padding: 1px 2px;
    border-radius: 2px;
}

/* Loading state para el input */
#actividad_search:disabled {
    background-color: #f9fafb;
    color: #9ca3af;
    cursor: not-allowed;
}

/* Scrollbar personalizada para el dropdown */
#actividad-dropdown::-webkit-scrollbar {
    width: 6px;
}

#actividad-dropdown::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

#actividad-dropdown::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#actividad-dropdown::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animación para el icono de loading */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.fa-spinner.fa-spin {
    animation: spin 1s linear infinite;
}

/* Estado cuando no hay resultados */
#actividad-no-resultados {
    background: linear-gradient(135deg, 
        rgba(107, 114, 128, 0.05) 0%,
        rgba(107, 114, 128, 0.08) 100%
    );
    border-radius: 6px;
    margin: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables necesarias
    const sectorSelect = document.getElementById('sector_id');
    const actividadSelect = document.getElementById('actividad_id');
    const actividadSearchInput = document.getElementById('actividad_search');
    const actividadDropdown = document.getElementById('actividad-dropdown');
    const actividadResultados = document.getElementById('actividad-resultados');
    const actividadNoResultados = document.getElementById('actividad-no-resultados');
    const actividadSearchIcon = document.getElementById('actividad-search-icon');
    const actividadLoadingIcon = document.getElementById('actividad-loading-icon');
    const actividadesContainer = document.getElementById('actividades-seleccionadas');
    const actividadesInput = document.getElementById('actividades_seleccionadas_input');
    const noActividadesMessage = document.getElementById('no-actividades-message');
    let actividadesSeleccionadas = new Set();
    let actividadesData = []; // Cache de todas las actividades
    let searchTimeout;
    let selectedIndex = -1; // Para navegación con teclado

    // Función para actualizar el mensaje de no actividades
    function actualizarMensajeNoActividades() {
        if (noActividadesMessage) {
            noActividadesMessage.style.display = actividadesSeleccionadas.size === 0 ? 'flex' : 'none';
        }
    }

    // Función para actualizar el input oculto de actividades
    function actualizarActividadesInput() {
        if (actividadesInput) {
        actividadesInput.value = JSON.stringify(Array.from(actividadesSeleccionadas));
        actualizarMensajeNoActividades();
    }
    }

    // Función para agregar un tag de actividad
    function agregarTag(id, nombre, sectorNombre) {
        const tag = document.createElement('div');
        tag.className = 'tag';
        tag.setAttribute('title', `${sectorNombre} - ${nombre}`);
        tag.innerHTML = `
            ${nombre}
            <button type="button" data-id="${id}" aria-label="Eliminar actividad">
                <i class="fas fa-times"></i>
            </button>
        `;

        tag.querySelector('button').addEventListener('click', function() {
            const id = this.dataset.id;
            actividadesSeleccionadas.delete(id);
            actualizarActividadesInput();
            tag.remove();

            // Remover del select oculto
            if (actividadSelect) {
                const option = actividadSelect.querySelector(`option[value="${id}"]`);
                if (option) {
                    option.remove();
                }
            }

            // Si hay búsqueda activa, actualizar resultados
            if (actividadSearchInput && actividadSearchInput.value.trim().length >= 2) {
                buscarActividades(actividadSearchInput.value.trim());
            }
        });

        actividadesContainer.appendChild(tag);
        actualizarMensajeNoActividades();
    }

    // Función para cargar todas las actividades (para búsqueda)
    async function cargarTodasLasActividades() {
        try {
            const response = await fetch('/api/actividades');
            if (!response.ok) throw new Error('Error al cargar actividades');
            
            const data = await response.json();
            if (!data.success) throw new Error(data.message || 'Error al cargar actividades');
            
            actividadesData = data.data;
            
            // Habilitar el input de búsqueda
            if (actividadSearchInput) {
                actividadSearchInput.disabled = false;
                actividadSearchInput.placeholder = 'Escriba para buscar actividad...';
            }
            
        } catch (error) {
            console.error('Error cargando actividades:', error);
            if (actividadSearchInput) {
                actividadSearchInput.placeholder = 'Error al cargar actividades';
            }
        }
    }

    // Función para cargar actividades existentes
    async function cargarActividadesExistentes() {
        try {
            const response = await fetch('/api/actividades');
            if (!response.ok) throw new Error('Error al cargar actividades');
            
            const data = await response.json();
            if (!data.success) throw new Error(data.message || 'Error al cargar actividades');
            
            actividadesSeleccionadas.forEach(actividadId => {
                const actividad = data.data.find(act => act.id.toString() === actividadId);
                if (actividad) {
                    const sectorNombre = actividad.sector ? actividad.sector.nombre : 'Sin sector';
                    agregarTag(actividadId, actividad.nombre, sectorNombre);
                }
            });
            
        } catch (error) {
            
        }
    }

    // Función para resaltar texto coincidente
    function resaltarTexto(texto, busqueda) {
        if (!busqueda) return texto;
        
        const regex = new RegExp(`(${busqueda.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return texto.replace(regex, '<span class="highlight">$1</span>');
    }

    // Función para buscar actividades
    function buscarActividades(query) {
        if (!query || query.length < 2) {
            ocultarDropdown();
            return;
        }

        mostrarLoading();

        // Filtrar actividades
        const resultados = actividadesData.filter(actividad => {
            // Buscar en nombre de actividad y sector
            const nombreCoincide = actividad.nombre.toLowerCase().includes(query.toLowerCase());
            const sectorCoincide = actividad.sector && actividad.sector.nombre.toLowerCase().includes(query.toLowerCase());
            
            // Excluir actividades ya seleccionadas
            return (nombreCoincide || sectorCoincide) && !actividadesSeleccionadas.has(actividad.id.toString());
        });

        mostrarResultados(resultados, query);
    }

    // Función para mostrar loading
    function mostrarLoading() {
        if (actividadSearchIcon) actividadSearchIcon.classList.add('hidden');
        if (actividadLoadingIcon) actividadLoadingIcon.classList.remove('hidden');
    }

    // Función para ocultar loading
    function ocultarLoading() {
        if (actividadSearchIcon) actividadSearchIcon.classList.remove('hidden');
        if (actividadLoadingIcon) actividadLoadingIcon.classList.add('hidden');
    }

    // Función para mostrar resultados
    function mostrarResultados(resultados, query) {
        ocultarLoading();
        selectedIndex = -1;

        if (!actividadResultados) return;

        actividadResultados.innerHTML = '';

        if (resultados.length === 0) {
            if (actividadNoResultados) actividadNoResultados.classList.remove('hidden');
            mostrarDropdown();
            return;
        }

        if (actividadNoResultados) actividadNoResultados.classList.add('hidden');

        // Limitar resultados para mejor rendimiento
        const resultadosLimitados = resultados.slice(0, 20);

        resultadosLimitados.forEach((actividad, index) => {
            const item = document.createElement('div');
            item.className = 'actividad-resultado-item';
            item.dataset.id = actividad.id;
            item.dataset.nombre = actividad.nombre;
            item.dataset.sector = actividad.sector ? actividad.sector.nombre : 'Sin sector';
            item.dataset.index = index;

            const nombreResaltado = resaltarTexto(actividad.nombre, query);
            const sectorResaltado = actividad.sector ? resaltarTexto(actividad.sector.nombre, query) : 'Sin sector';

            item.innerHTML = `
                <div class="actividad-resultado-nombre">${nombreResaltado}</div>
                <div class="actividad-resultado-sector">${sectorResaltado}</div>
            `;

            // Event listeners para selección
            item.addEventListener('click', () => seleccionarActividad(actividad));
            item.addEventListener('mouseenter', () => {
                selectedIndex = index;
                actualizarSeleccionVisual();
            });

            actividadResultados.appendChild(item);
        });

        mostrarDropdown();
    }

    // Función para mostrar dropdown
    function mostrarDropdown() {
        if (actividadDropdown) {
            actividadDropdown.classList.remove('hidden');
            actividadDropdown.classList.add('show');
        }
    }

    // Función para ocultar dropdown
    function ocultarDropdown() {
        if (actividadDropdown) {
            actividadDropdown.classList.add('hidden');
            actividadDropdown.classList.remove('show');
        }
        selectedIndex = -1;
        ocultarLoading();
    }

    // Función para seleccionar actividad
    function seleccionarActividad(actividad) {
        const actividadId = actividad.id.toString();
        const actividadNombre = actividad.nombre;
        const sectorNombre = actividad.sector ? actividad.sector.nombre : 'Sin sector';

        if (!actividadesSeleccionadas.has(actividadId)) {
            actividadesSeleccionadas.add(actividadId);
            actualizarActividadesInput();
            agregarTag(actividadId, actividadNombre, sectorNombre);

            // Actualizar select oculto
            if (actividadSelect) {
                const option = document.createElement('option');
                option.value = actividadId;
                option.textContent = actividadNombre;
                option.selected = true;
                actividadSelect.appendChild(option);
            }
        }

        // Limpiar búsqueda
        if (actividadSearchInput) {
            actividadSearchInput.value = '';
        }
        ocultarDropdown();
    }

    // Función para actualizar selección visual con teclado
    function actualizarSeleccionVisual() {
        const items = document.querySelectorAll('.actividad-resultado-item');
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });
    }

    // Función para navegar con teclado
    function navegarConTeclado(direction) {
        const items = document.querySelectorAll('.actividad-resultado-item');
        if (items.length === 0) return;

        if (direction === 'down') {
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
        } else if (direction === 'up') {
            selectedIndex = Math.max(selectedIndex - 1, -1);
        }

        actualizarSeleccionVisual();

        // Scroll al elemento seleccionado
        if (selectedIndex >= 0 && items[selectedIndex]) {
            items[selectedIndex].scrollIntoView({
                block: 'nearest',
                behavior: 'smooth'
            });
        }
    }

    // Event Listeners para búsqueda de actividades
    if (actividadSearchInput) {
        // Búsqueda mientras escribe
        actividadSearchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            
            // Limpiar timeout anterior
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Debounce de 300ms
            searchTimeout = setTimeout(() => {
                buscarActividades(query);
            }, 300);
        });

        // Navegación con teclado
        actividadSearchInput.addEventListener('keydown', function(e) {
            const dropdown = document.getElementById('actividad-dropdown');
            
            if (!dropdown || dropdown.classList.contains('hidden')) {
                return;
            }

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    navegarConTeclado('down');
                    break;
                    
                case 'ArrowUp':
                    e.preventDefault();
                    navegarConTeclado('up');
                    break;
                    
                case 'Enter':
                    e.preventDefault();
                    const items = document.querySelectorAll('.actividad-resultado-item');
                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        const actividadId = items[selectedIndex].dataset.id;
                        const actividad = actividadesData.find(act => act.id.toString() === actividadId);
                        if (actividad) {
                            seleccionarActividad(actividad);
                        }
                    }
                    break;
                    
                case 'Escape':
                    e.preventDefault();
                    ocultarDropdown();
                    this.blur();
                    break;
            }
        });

        // Mostrar dropdown al hacer focus si hay texto
        actividadSearchInput.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 2) {
                buscarActividades(query);
            }
        });

        // Ocultar dropdown al perder focus (con delay para permitir clicks)
        actividadSearchInput.addEventListener('blur', function() {
            setTimeout(() => {
                ocultarDropdown();
            }, 200);
        });
    }

    // Event Listeners originales (mantener compatibilidad)
    if (sectorSelect && actividadSelect) {
        sectorSelect.addEventListener('change', async function() {
            const sectorId = this.value;
            // El select original ahora está oculto, pero mantenemos la funcionalidad
            // El input de búsqueda funciona independientemente del sector
        });
    }

    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#actividad_search') && !e.target.closest('#actividad-dropdown')) {
            ocultarDropdown();
        }
    });

    // Inicializar: cargar todas las actividades al cargar la página
    cargarTodasLasActividades();

    // Inicializar actividades seleccionadas si existen
    const actividadesExistentes = actividadesInput?.value;
    if (actividadesExistentes) {
        try {
            const actividades = JSON.parse(actividadesExistentes);
            if (Array.isArray(actividades) && actividades.length > 0) {
                actividades.forEach(actividadId => {
                    actividadesSeleccionadas.add(actividadId.toString());
                });
                
                // Cargar los nombres de las actividades
                cargarActividadesExistentes();
            }
        } catch (error) {

        }
    }

    // Inicializar el mensaje
    actualizarMensajeNoActividades();
    
    // Configurar placeholder inicial del input de búsqueda
    if (actividadSearchInput) {
        actividadSearchInput.placeholder = 'Cargando actividades...';
        actividadSearchInput.disabled = true;
    }
});

// Función para guardar datos y navegar al siguiente paso
async function guardarYSiguiente() {
    // Mostrar estado de carga
    mostrarEstadoCargaFormulario('datos-generales');
    
    try {
        // 1. Obtener el formulario y preparar datos
        const form = document.getElementById('datos-generales-form');
        if (!form) {
            alert('Error: No se encontró el formulario');
            ocultarEstadoCargaFormulario('datos-generales');
            return;
        }
        
        // 2. Preparar FormData y agregar token CSRF
        const formData = new FormData(form);
        formData.set('action', 'next'); // Asegurar que la acción sea 'next'
        
        // Agregar token CSRF si no existe
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken && !formData.has('_token')) {
            formData.set('_token', csrfToken.getAttribute('content'));
        }
        
        // 3. Usar ruta específica para guardar datos generales (NO usar form.action)
        const saveUrl = '{{ route("datos-generales.guardar") }}';
        
        const response = await fetch(saveUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        // Obtener el texto de la respuesta primero
        const responseText = await response.text();
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status} - ${responseText.substring(0, 200)}`);
        }
        
        // Intentar parsear como JSON
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            throw new Error(`Respuesta del servidor no es JSON válido. Recibido: ${responseText.substring(0, 100)}...`);
        }
        
        if (result.success) {
            // 4. Navegar al siguiente paso
            navegarSiguienteSeccion();
        } else {
            ocultarEstadoCargaFormulario('datos-generales');
            
            // Mostrar errores de validación específicos
            if (result.errors && Object.keys(result.errors).length > 0) {
                mostrarErroresValidacion(result.errors);
            } else {
                mostrarNotificacion('Error al guardar: ' + (result.message || 'Error desconocido'), 'error');
            }
        }
        
    } catch (error) {
        ocultarEstadoCargaFormulario('datos-generales');
        
        // Si es un error HTTP 422 (validación), extraer errores
        if (error.message.includes('422')) {
            try {
                const errorText = error.message.split(' - ')[1];
                const errorData = JSON.parse(errorText);
                if (errorData.errors) {
                    mostrarErroresValidacion(errorData.errors);
                    return;
                }
            } catch (parseError) {
                // Si no se puede parsear, continuar con el manejo normal
            }
        }
        
        mostrarNotificacion('Error de conexión: ' + error.message, 'error');
    }
}

// Función simplificada para navegar al siguiente paso
function navegarSiguienteSeccion() {
    // Método 1: Función global navegarSiguiente (preferido)
    if (typeof window.navegarSiguiente === 'function') {
        window.navegarSiguiente();
        return;
    }
    
    // Método 2: Buscar contenedor Alpine.js y manipular directamente
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
    
    // Método 4: Buscar botón de navegación externa y hacer click
    const siguienteBtn = document.querySelector('button[onclick*="navegarSiguiente"]');
    if (siguienteBtn) {
        siguienteBtn.click();
        return;
    }
    
    alert('Error: No se puede navegar al siguiente paso. Verifique que la página esté cargada correctamente.');
}

// Función para mostrar errores de validación específicos
function mostrarErroresValidacion(errors) {
    // Limpiar errores anteriores
    limpiarErroresValidacion();
    
    let camposConError = [];
    
    Object.keys(errors).forEach(campo => {
        const mensajes = Array.isArray(errors[campo]) ? errors[campo] : [errors[campo]];
        
        // Buscar el campo en el formulario
        let input = document.querySelector(`[name="${campo}"]`);
        
        // Para actividades seleccionadas, usar el contenedor
        if (campo === 'actividades_seleccionadas') {
            const contenedor = document.getElementById('actividades-seleccionadas');
            if (contenedor) {
                mostrarErrorEnContenedor(contenedor, mensajes);
                camposConError.push({
                    nombre: 'Actividades',
                    errores: mensajes
                });
            }
            return;
        }
        
        if (input) {
            mostrarErrorEnCampo(input, mensajes);
            // Obtener el label del campo para el modal
            const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || campo;
            camposConError.push({
                nombre: label,
                errores: mensajes
            });
        }
    });
    
    // Mostrar modal de errores bonito
    mostrarModalErrores(camposConError);
}

// Función para mostrar error en un campo específico
function mostrarErrorEnCampo(input, mensajes) {
    const grupo = input.closest('.form-group');
    if (!grupo) return;
    
    // Agregar clases de error
    input.classList.add('border-red-500', 'bg-red-50');
    
    // Agregar mensaje de error (solo el primero para no sobrecargar)
    let mensajeError = grupo.querySelector('.error-message');
    if (!mensajeError) {
        mensajeError = document.createElement('p');
        mensajeError.className = 'error-message mt-1 text-sm text-red-600';
        grupo.appendChild(mensajeError);
    }
    
    const primerMensaje = Array.isArray(mensajes) ? mensajes[0] : mensajes;
    mensajeError.textContent = primerMensaje;
    
    // Scroll al primer error
    if (!document.querySelector('.border-red-500:not([name="' + input.name + '"])')) {
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Función para mostrar error en el contenedor de actividades
function mostrarErrorEnContenedor(contenedor, mensajes) {
    contenedor.classList.add('border-red-500', 'bg-red-50');
    
    let mensajeError = contenedor.parentElement.querySelector('.error-message');
    if (!mensajeError) {
        mensajeError = document.createElement('p');
        mensajeError.className = 'error-message mt-1 text-sm text-red-600';
        contenedor.parentElement.appendChild(mensajeError);
    }
    
    const primerMensaje = Array.isArray(mensajes) ? mensajes[0] : mensajes;
    mensajeError.textContent = primerMensaje;
    contenedor.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Función para limpiar errores anteriores
function limpiarErroresValidacion() {
    // Limpiar clases de error en inputs
    document.querySelectorAll('.border-red-500').forEach(element => {
        element.classList.remove('border-red-500', 'bg-red-50');
    });
    
    // Eliminar mensajes de error
    document.querySelectorAll('.error-message').forEach(element => {
        element.remove();
    });
    
    // Cerrar modal de errores si existe
    cerrarModalErrores();
}

// Función para mostrar notificaciones simples
function mostrarNotificacion(mensaje, tipo = 'info') {
    const contenedor = document.createElement('div');
    contenedor.className = 'fixed top-4 right-4 z-50 max-w-md';
    
    const clasesTipo = tipo === 'error' 
        ? 'bg-red-100 border border-red-400 text-red-700' 
        : tipo === 'success'
        ? 'bg-green-100 border border-green-400 text-green-700'
        : 'bg-blue-100 border border-blue-400 text-blue-700';
    
    const icono = tipo === 'error' 
        ? 'fa-exclamation-circle' 
        : tipo === 'success'
        ? 'fa-check-circle'
        : 'fa-info-circle';
    
    contenedor.innerHTML = `
        <div class="${clasesTipo} px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 animate-slide-in">
            <div class="flex items-start">
                <i class="fas ${icono} mr-2 mt-0.5 flex-shrink-0"></i>
                <span class="flex-1">${mensaje}</span>
                <button onclick="this.closest('.fixed').remove()" class="ml-4 text-lg leading-none flex-shrink-0">
                    &times;
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(contenedor);
    
    // Auto-remover después de 8 segundos para errores, 5 para otros
    const timeout = tipo === 'error' ? 8000 : 5000;
    setTimeout(() => {
        if (contenedor && contenedor.parentNode) {
            contenedor.remove();
        }
    }, timeout);
}

// Función para mostrar modal de errores bonito con todos los detalles
function mostrarModalErrores(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-validacion');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-validacion';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampo('${campo.nombre}', ${index})">
                    <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>
                    ${campo.nombre}
                    <i class="fas fa-external-link-alt ml-2 text-xs text-red-500"></i>
                </h4>
                <ul class="space-y-1">
                    ${campo.errores.map(error => `
                        <li class="text-red-700 text-sm flex items-start">
                            <i class="fas fa-circle mr-2 text-red-400 text-xs mt-1.5 flex-shrink-0"></i>
                            <span>${error}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    });
    
    modal.innerHTML = `
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"></div>
            
            <!-- Centrar modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-modal-appear">
                <!-- Header del modal -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white bg-opacity-20 rounded-full p-2 mr-3">
                                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en el Formulario
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} que necesitan corrección
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErrores()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Contenido del modal -->
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores antes de continuar:
                        </p>
                        ${erroresHTML}
                    </div>
                </div>
                
                <!-- Footer del modal -->
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <div class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Haga clic en un campo para ir directamente a él</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                            ${camposConError.length} campo${camposConError.length !== 1 ? 's' : ''} con errores
                        </div>
                        <button onclick="cerrarModalErrores()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalErrores();
        }
    });
    
    // Cerrar al hacer click fuera del modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErrores();
        }
    });
}

// Función para cerrar el modal de errores
function cerrarModalErrores() {
    const modal = document.getElementById('modal-errores-validacion');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Función para ir a un campo específico desde el modal
function irACampo(nombreCampo, index) {
    // Cerrar el modal primero
    cerrarModalErrores();
    
    // Esperar a que se cierre el modal
    setTimeout(() => {
        let elemento = null;
        
        // Buscar el campo por diferentes criterios
        if (nombreCampo === 'Actividades') {
            elemento = document.getElementById('actividades-seleccionadas');
        } else {
            // Buscar por el label text
            const labels = document.querySelectorAll('label');
            for (let label of labels) {
                if (label.textContent.replace('*', '').trim() === nombreCampo) {
                    const input = label.nextElementSibling || document.querySelector(`[name="${label.getAttribute('for')}"]`);
                    if (input) {
                        elemento = input;
                        break;
                    }
                }
            }
            
            // Si no se encontró, buscar directamente por name
            if (!elemento) {
                elemento = document.querySelector(`[name*="${nombreCampo.toLowerCase()}"]`);
            }
        }
        
        if (elemento) {
            // Scroll al elemento
            elemento.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            // Hacer focus si es un input
            if (elemento.tagName === 'INPUT' || elemento.tagName === 'TEXTAREA' || elemento.tagName === 'SELECT') {
                setTimeout(() => {
                    elemento.focus();
                    // Efecto de resaltado temporal
                    elemento.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                    setTimeout(() => {
                        elemento.style.boxShadow = '';
                    }, 2000);
                }, 500);
            } else {
                // Para contenedores como actividades
                elemento.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                setTimeout(() => {
                    elemento.style.boxShadow = '';
                }, 2000);
            }
        }
    }, 350);
}

// Función para limpiar error de un campo específico
function limpiarErrorCampo(input) {
    input.classList.remove('border-red-500', 'bg-red-50');
    
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMsg = grupo.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    }
}

// Agregar event listeners cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Agregar event listeners para limpiar errores al escribir
    const inputs = document.querySelectorAll('#datos-generales-form input, #datos-generales-form textarea, #datos-generales-form select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            limpiarErrorCampo(this);
        });
        
        input.addEventListener('change', function() {
            limpiarErrorCampo(this);
        });
    });
    
    // Limpiar errores del contenedor de actividades cuando se modifique
    const actividadesContainer = document.getElementById('actividades-seleccionadas');
    if (actividadesContainer) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    // Limpiar error del contenedor cuando se agregan/quitan actividades
                    actividadesContainer.classList.remove('border-red-500', 'bg-red-50');
                    const errorMsg = actividadesContainer.parentElement.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
        });
        
        observer.observe(actividadesContainer, {
            childList: true,
            subtree: true
        });
    }
});

</script> 