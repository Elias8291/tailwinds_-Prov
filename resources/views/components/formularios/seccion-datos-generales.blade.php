@props(['title' => 'Datos Generales', 'datosTramite' => [], 'datosSolicitante' => [], 'readonly' => false])

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="max-w-6xl mx-auto">
    <form id="datos-generales-form" action="{{ route('datos-generales.guardar') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Campos ocultos -->
        <input type="hidden" name="form_action" value="next">
        <input type="hidden" name="seccion" value="1">
        <input type="hidden" name="tramite_id" value="{{ $datosTramite['tramite_id'] ?? request()->route('tramite') ?? session('tramite_id') ?? '' }}">
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
                               value="{{ $datosSolicitante['tipo_persona'] ?? '' }}" 
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
                               value="{{ $datosSolicitante['rfc'] ?? '' }}" 
                               class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                               readonly>
                    </div>
                </div>

            <!-- CURP (Solo persona física) -->
            @if(($datosSolicitante['tipo_persona'] ?? '') === 'Física')
            <div class="form-group mt-6">
                <label for="curp" class="block text-sm font-medium text-gray-700 mb-2">
                    CURP <span class="text-red-500">*</span>
                </label>
                    <input type="text" 
                           id="curp"
                           name="curp"
                           value="{{ $datosSolicitante['curp'] ?? '' }}" 
                           class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                           readonly>
                </div>
            @endif

            <!-- Nombre Completo (Solo persona física) -->
            @if(($datosSolicitante['tipo_persona'] ?? '') === 'Física')
            <div class="form-group mt-6">
                <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre Completo <span class="text-red-500">*</span>
                </label>
                    <input type="text" 
                           id="nombre_completo"
                           name="nombre_completo"
                           value="{{ $datosSolicitante['nombre_completo'] ?? auth()->user()->name ?? '' }}" 
                           class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                           readonly>
            </div>
            @endif

            <!-- Razón Social (Solo persona moral) -->
            @if(($datosSolicitante['tipo_persona'] ?? '') === 'Moral')
            <div class="form-group mt-6">
                <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                    Razón Social <span class="text-red-500">*</span>
                </label>
                    <input type="text" 
                           id="razon_social"
                           name="razon_social"
                           value="{{ $datosSolicitante['razon_social'] ?? auth()->user()->name ?? '' }}" 
                           class="block w-full px-4 py-2.5 text-gray-600 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed"
                           readonly>
            </div>
            @endif

            <!-- Giro -->
            @unless($readonly)
            <div class="form-group mt-6">
                <label for="giro" class="block text-sm font-medium text-gray-700 mb-2">
                    Giro <span class="text-red-500">*</span>
                </label>
                <textarea id="giro" 
                          name="giro" 
                          rows="4"
                          class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all @error('giro') border-red-500 @enderror"
                          placeholder="Describa el giro de la empresa">{{ old('giro', $datosTramite['giro'] ?? '') }}</textarea>
                    @error('giro')
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('giro') }}</p>
                    @enderror
            </div>
            @else
            <div class="form-group mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Giro</label>
                <div class="p-4 bg-gray-100 border border-gray-200 rounded-lg">
                    <p class="text-gray-700">{{ $datosTramite['giro'] ?? 'No especificado' }}</p>
            </div>
        </div>
            @endunless
        </div>

        <!-- Actividades Económicas -->
        @unless($readonly)
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
                    Buscar Actividades *
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
                           placeholder="Escriba para buscar actividad..."
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all"
                           autocomplete="off">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                
                <!-- Dropdown de resultados -->
                <div id="actividad-dropdown" class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl hidden max-h-64 overflow-hidden">
                    <div id="actividad-resultados" class="max-h-48 overflow-y-auto"></div>
                        <div id="actividad-no-resultados" class="px-6 py-8 text-center hidden">
                        <p class="text-gray-500 text-sm">No se encontraron actividades</p>
                                <button type="button" 
                                        id="btn-agregar-manual"
                                class="mt-4 px-4 py-2 bg-[#9d2449] text-white text-sm rounded-lg hover:bg-[#8a203f] transition-colors">
                            <i class="fas fa-plus mr-2"></i>Agregar actividad personalizada
                                </button>
                            </div>
                        </div>
                    </div>

            <!-- Actividades seleccionadas -->
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">Actividades Seleccionadas</label>
                <div id="actividades-seleccionadas" class="min-h-[60px] p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div id="no-actividades-message" class="flex items-center justify-center text-gray-400 text-sm italic">
                        <i class="fas fa-plus-circle mr-2"></i>No hay actividades seleccionadas
                </div>
                </div>
                <input type="hidden" id="actividades_seleccionadas_input" name="actividades_seleccionadas" value="{{ old('actividades_seleccionadas', $datosTramite['actividades_seleccionadas'] ?? '') }}">
                @error('actividades_seleccionadas')
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('actividades_seleccionadas') }}</p>
                    @enderror
            </div>
                </div>
        @else
        <!-- Mostrar actividades en modo solo lectura -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-sm">
                    <i class="fas fa-chart-line text-lg"></i>
            </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Actividades Económicas</h3>
                    <p class="text-sm text-gray-500">Actividades económicas registradas</p>
                </div>
            </div>
            <div class="p-4 bg-gray-100 border border-gray-200 rounded-lg">
                @php
                    $actividades_ids = json_decode($datosTramite['actividades_seleccionadas'] ?? '[]', true);
                    $actividades_nombres = [];
                    if (!empty($actividades_ids)) {
                        $actividades_nombres = \App\Models\Actividad::whereIn('id', $actividades_ids)
                            ->select('id', 'nombre', 'sector_id')
                            ->get()
                            ->keyBy('id');
                    }
                @endphp
                @if(empty($actividades_ids))
                    <p class="text-gray-500 italic">No hay actividades seleccionadas</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($actividades_ids as $actividad_id)
                            @php $actividad = $actividades_nombres->get($actividad_id); @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[#9d2449]/10 text-[#9d2449] border border-[#9d2449]/20">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ $actividad->nombre ?? 'Actividad no encontrada' }}
                            </span>
                        @endforeach
                    </div>
            @endif
        </div>
        </div>
        @endunless

        <!-- Información Adicional -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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
                <label for="pagina_web" class="block text-sm font-medium text-gray-700 mb-2">Página Web</label>
                @unless($readonly)
                <input type="url" 
                       id="pagina_web" 
                       name="pagina_web"
                       class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all @error('pagina_web') border-red-500 @enderror"
                       placeholder="https://www.ejemplo.com"
                       value="{{ old('pagina_web', $datosTramite['pagina_web'] ?? $datosSolicitante['pagina_web'] ?? '') }}">
                    @error('pagina_web')
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('pagina_web') }}</p>
                    @enderror
                @else
                <div class="p-3 bg-gray-100 border border-gray-200 rounded-lg">
                    @if(!empty($datosTramite['pagina_web'] ?? $datosSolicitante['pagina_web'] ?? ''))
                        <a href="{{ $datosTramite['pagina_web'] ?? $datosSolicitante['pagina_web'] }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            {{ $datosTramite['pagina_web'] ?? $datosSolicitante['pagina_web'] }}
                        </a>
                    @else
                        <span class="text-gray-500">No especificada</span>
                @endif
            </div>
                @endunless
        </div>
        </div>

        <!-- Datos de Contacto -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    @unless($readonly)
                    <input type="text" 
                           id="contacto_nombre" 
                           name="contacto_nombre"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all @error('contacto_nombre') border-red-500 @enderror"
                           placeholder="Nombre completo del contacto"
                           value="{{ old('contacto_nombre', $datosTramite['contacto_nombre'] ?? $datosSolicitante['contacto_nombre'] ?? '') }}">
                        @error('contacto_nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('contacto_nombre') }}</p>
                        @enderror
                    @else
                    <div class="p-3 bg-gray-100 border border-gray-200 rounded-lg">
                        {{ $datosTramite['contacto_nombre'] ?? $datosSolicitante['contacto_nombre'] ?? 'No especificado' }}
                </div>
                    @endunless
                </div>

                <!-- Cargo -->
                <div class="form-group">
                    <label for="contacto_cargo" class="block text-sm font-medium text-gray-700 mb-2">
                        Cargo o Puesto <span class="text-red-500">*</span>
                    </label>
                    @unless($readonly)
                    <input type="text" 
                           id="contacto_cargo" 
                           name="contacto_cargo"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all @error('contacto_cargo') border-red-500 @enderror"
                           placeholder="Cargo en la empresa"
                           value="{{ old('contacto_cargo', $datosTramite['contacto_cargo'] ?? $datosSolicitante['contacto_cargo'] ?? '') }}">
                        @error('contacto_cargo')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('contacto_cargo') }}</p>
                        @enderror
                    @else
                    <div class="p-3 bg-gray-100 border border-gray-200 rounded-lg">
                        {{ $datosTramite['contacto_cargo'] ?? $datosSolicitante['contacto_cargo'] ?? 'No especificado' }}
                </div>
                    @endunless
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="contacto_correo" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico <span class="text-red-500">*</span>
                    </label>
                    @unless($readonly)
                    <input type="email" 
                           id="contacto_correo" 
                           name="contacto_correo"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all @error('contacto_correo') border-red-500 @enderror"
                           placeholder="correo@ejemplo.com"
                           value="{{ old('contacto_correo', $datosTramite['contacto_correo'] ?? $datosSolicitante['contacto_correo'] ?? '') }}">
                        @error('contacto_correo')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('contacto_correo') }}</p>
                        @enderror
                    @else
                    <div class="p-3 bg-gray-100 border border-gray-200 rounded-lg">
                        {{ $datosTramite['contacto_correo'] ?? $datosSolicitante['contacto_correo'] ?? 'No especificado' }}
                </div>
                    @endunless
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label for="contacto_telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono de Contacto <span class="text-red-500">*</span>
                    </label>
                    @unless($readonly)
                    <input type="tel" 
                           id="contacto_telefono" 
                           name="contacto_telefono"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all @error('contacto_telefono') border-red-500 @enderror"
                           placeholder="10 dígitos"
                           value="{{ old('contacto_telefono', $datosTramite['contacto_telefono'] ?? $datosSolicitante['contacto_telefono'] ?? '') }}">
                        @error('contacto_telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('contacto_telefono') }}</p>
                        @enderror
                    @else
                    <div class="p-3 bg-gray-100 border border-gray-200 rounded-lg">
                        {{ $datosTramite['contacto_telefono'] ?? $datosSolicitante['contacto_telefono'] ?? 'No especificado' }}
            </div>
                    @endunless
        </div>
        </div>
            </div>

        <!-- Botones de navegación -->
        @unless($readonly)
        @if(!isset($mostrar_navegacion) || $mostrar_navegacion !== false)
        <div class="flex justify-end">
            <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white rounded-lg hover:from-[#8a203f] hover:to-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#9d2449] focus:ring-offset-2">
                <i class="fas fa-save mr-2"></i>
                Guardar y Continuar
                <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        @endif
        @endunless
    </form>
</div>

@unless($readonly)
<script>
document.addEventListener('DOMContentLoaded', function() {

    
    // Variables para el buscador de actividades
    const searchInput = document.getElementById('actividad_search');
    const dropdown = document.getElementById('actividad-dropdown');
    const resultados = document.getElementById('actividad-resultados');
    const noResultados = document.getElementById('actividad-no-resultados');
    const tagsContainer = document.getElementById('actividades-seleccionadas');
    const noActivitiesMessage = document.getElementById('no-actividades-message');
    const hiddenInput = document.getElementById('actividades_seleccionadas_input');
    const formulario = document.getElementById('datos-generales-form');
    
    let searchTimeout;
    let actividadesSeleccionadas = [];

    // MANEJO SIMPLIFICADO DEL FORMULARIO
    if (formulario) {

        
        // Antes de reemplazar el formulario, guardar referencias importantes
        const searchInputOriginal = searchInput;
        const dropdownOriginal = dropdown;
        const resultadosOriginal = resultados;
        const noResultadosOriginal = noResultados;
        const tagsContainerOriginal = tagsContainer;
        const hiddenInputOriginal = hiddenInput;
        
        // Remover todos los event listeners existentes del formulario
        const nuevoFormulario = formulario.cloneNode(true);
        formulario.parentNode.replaceChild(nuevoFormulario, formulario);
        
        // Actualizar referencias después del reemplazo
        const newSearchInput = document.getElementById('actividad_search');
        const newDropdown = document.getElementById('actividad-dropdown');
        const newResultados = document.getElementById('actividad-resultados');
        const newNoResultados = document.getElementById('actividad-no-resultados');
        const newTagsContainer = document.getElementById('actividades-seleccionadas');
        const newHiddenInput = document.getElementById('actividades_seleccionadas_input');
        
        // Reconfigurar event listeners para actividades en el nuevo formulario
        if (newSearchInput) {

            newSearchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                if (searchTimeout) clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => buscarActividades(query, newDropdown, newResultados, newNoResultados), 300);
            });
        }
        
        // Reconfigurar cerrar dropdown
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#actividad_search') && !e.target.closest('#actividad-dropdown')) {
                if (newDropdown) newDropdown.classList.add('hidden');
            }
        });
        
        // Reconfigurar botón agregar manual
        const newBtnAgregarManual = document.getElementById('btn-agregar-manual');
        if (newBtnAgregarManual) {
            newBtnAgregarManual.addEventListener('click', function() {
                const query = newSearchInput.value.trim();
                if (!query) return;

                const actividadPersonalizada = {
                    id: Date.now(),
                    nombre: query,
                    sector: 'Actividad personalizada',
                    custom: true
                };

                if (actividadesSeleccionadas.some(act => act.nombre.toLowerCase() === query.toLowerCase())) {
                    return;
                }

                agregarActividad(actividadPersonalizada, newSearchInput, newTagsContainer, newHiddenInput, newDropdown);
            });
        }
        
        // Actualizar referencias globales
        Object.assign(window, {
            searchInput: newSearchInput,
            dropdown: newDropdown,
            resultados: newResultados,
            noResultados: newNoResultados,
            tagsContainer: newTagsContainer,
            hiddenInput: newHiddenInput
        });
        
        // Cargar actividades existentes después del reemplazo
        cargarActividadesExistentes(newHiddenInput, newTagsContainer);
        

        
        // Agregar el nuevo manejador simplificado del formulario
        nuevoFormulario.addEventListener('submit', function(e) {
            
            // Mostrar todos los datos del formulario
            const formData = new FormData(nuevoFormulario);
            const datosFormulario = {};
            for (let [key, value] of formData.entries()) {
                datosFormulario[key] = value;
            }
            
            // Validación básica
            let esValido = true;
            const camposRequeridos = nuevoFormulario.querySelectorAll('[required]');

            
            // Validar campos requeridos
                          camposRequeridos.forEach(campo => {
                  if (!campo.value.trim()) {
                      esValido = false;
                    
                    // Agregar clase de error
                    campo.classList.add('border-red-500', 'bg-red-50');
                    setTimeout(() => {
                        campo.classList.remove('border-red-500', 'bg-red-50');
                    }, 3000);
                                  }
            });
            
            // Validar actividades seleccionadas
            const actividadesInput = nuevoFormulario.querySelector('#actividades_seleccionadas_input');
                          if (actividadesInput) {
                  const actividades = actividadesInput.value;
                  if (!actividades || actividades === '[]' || actividades.trim() === '') {
                    esValido = false;
                    mostrarError('Debe seleccionar al menos una actividad económica');
                                  } else {
                      try {
                          const actividadesArray = JSON.parse(actividades);
                      } catch (e) {
                        esValido = false;
                    }
                }
                          } else {
            }
            
                          if (!esValido) {
                  e.preventDefault();
                  return false;
              }
            
            // Prevenir el envío normal del formulario
            e.preventDefault();
            
                          // Enviar datos vía AJAX
              enviarDatosAjax(nuevoFormulario);
            
            return false;
        });
    }

    // Función para mostrar errores
    function mostrarError(mensaje) {
        // Crear o actualizar modal de error
        let modal = document.getElementById('modal-error-datos');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'modal-error-datos';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-xl p-6 max-w-md mx-4 shadow-2xl">
                    <div class="flex items-center mb-4">
                        <div class="bg-red-100 rounded-full p-2 mr-3">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Error de Validación</h3>
                    </div>
                    <p class="text-gray-600 mb-4" id="mensaje-error-datos">${mensaje}</p>
                    <button onclick="cerrarModalError()" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                        Entendido
                    </button>
                </div>
            `;
            document.body.appendChild(modal);
        } else {
            document.getElementById('mensaje-error-datos').textContent = mensaje;
            modal.style.display = 'flex';
        }
    }

    // Función global para cerrar modal de error
    window.cerrarModalError = function() {
        const modal = document.getElementById('modal-error-datos');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    // Cargar actividades existentes al inicio
    async function cargarActividadesExistentes(hiddenInput, tagsContainer) {
        if (!hiddenInput.value) return;

        try {
            const actividadesIds = JSON.parse(hiddenInput.value);
            if (!Array.isArray(actividadesIds) || actividadesIds.length === 0) return;

    

            const response = await fetch('/api/actividades/obtener-por-ids', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ids: actividadesIds })
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }

            const result = await response.json();

            
            // Manejar diferentes formatos de respuesta
            let actividades = [];
            if (result.success && result.data) {
                actividades = result.data;
            } else if (Array.isArray(result)) {
                actividades = result;
            } else if (result.actividades) {
                actividades = result.actividades;
            } else {
                throw new Error('Formato de respuesta no reconocido');
            }

            if (!Array.isArray(actividades)) {
                throw new Error('Los datos de actividades no son un array válido');
            }

            // Asegurar que cada actividad tenga nombre
            actividadesSeleccionadas = actividades.map(actividad => {
                if (typeof actividad === 'object' && actividad.nombre) {
                    return {
                        id: actividad.id,
                        nombre: actividad.nombre,
                        sector: actividad.sector || 'Sin sector especificado'
                    };
                } else {
                    // Si no tiene nombre, usar un fallback
                    return {
                        id: actividad.id || actividad,
                        nombre: `Actividad ${actividad.id || actividad}`,
                        sector: 'Información incompleta'
                    };
                }
            });

            actualizarTags(tagsContainer);

            
        } catch (e) {
            console.error('❌ Error al cargar actividades existentes:', e);
            
            // Fallback más robusto: mostrar los IDs con nombres descriptivos
            try {
                const actividadesIds = JSON.parse(hiddenInput.value);
                console.warn('🔄 Aplicando fallback para IDs:', actividadesIds);
                
                actividadesSeleccionadas = actividadesIds.map(id => ({
                    id: id,
                    nombre: `Actividad ${id} (no cargada)`,
                    sector: 'Error al cargar información'
                }));
                
                actualizarTags(tagsContainer);

                
            } catch (fallbackError) {
                console.error('💥 Error crítico en fallback:', fallbackError);
                // Último recurso: limpiar todo
                actividadesSeleccionadas = [];
                actualizarTags(tagsContainer);
            }
        }
    }

    // Ejecutar carga inicial
    cargarActividadesExistentes(hiddenInput, tagsContainer);

    // Función para buscar actividades
    async function buscarActividades(query, dropdown, resultados, noResultados) {
        if (!query || query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`/api/actividades/buscar?q=${encodeURIComponent(query)}&limit=20`);
            if (!response.ok) throw new Error('Error en la búsqueda');
            
            const result = await response.json();
            if (result.success && result.data) {
                const actividadesFiltradas = result.data.filter(actividad => 
                    !actividadesSeleccionadas.some(sel => sel.id === actividad.id)
                );
                mostrarResultados(actividadesFiltradas, dropdown, resultados, noResultados);
            } else {
                mostrarSinResultados(dropdown, noResultados);
            }
        } catch (error) {
            mostrarSinResultados(dropdown, noResultados);
        }
    }

    // Función para mostrar resultados
    function mostrarResultados(actividades, dropdown, resultados, noResultados) {
        resultados.innerHTML = '';
        noResultados.classList.add('hidden');

        if (actividades.length === 0) {
            mostrarSinResultados(dropdown, noResultados);
            return;
        }

        actividades.forEach(actividad => {
            const item = document.createElement('div');
            item.className = 'px-4 py-3 cursor-pointer transition-all duration-200 border-b border-gray-100 last:border-b-0 hover:bg-gray-50';
            item.innerHTML = `
                <div class="font-medium text-gray-900 text-sm">${actividad.nombre}</div>
                <div class="text-xs text-gray-500 mt-1">${actividad.sector || 'Sin sector'}</div>
            `;
            item.addEventListener('click', () => agregarActividad(actividad, 
                document.getElementById('actividad_search'), 
                document.getElementById('actividades-seleccionadas'), 
                document.getElementById('actividades_seleccionadas_input'), 
                dropdown
            ));
            resultados.appendChild(item);
        });

        dropdown.classList.remove('hidden');
    }

    // Función para mostrar sin resultados
    function mostrarSinResultados(dropdown, noResultados) {
        const resultados = document.getElementById('actividad-resultados');
        if (resultados) resultados.innerHTML = '';
        if (noResultados) noResultados.classList.remove('hidden');
        if (dropdown) dropdown.classList.remove('hidden');
    }

    // Función para agregar actividad
    function agregarActividad(actividad, searchInputRef, tagsContainerRef, hiddenInputRef, dropdownRef) {
        if (actividadesSeleccionadas.some(sel => sel.id === actividad.id)) return;

        actividadesSeleccionadas.push(actividad);
        if (searchInputRef) searchInputRef.value = '';
        actualizarTags(tagsContainerRef);
        actualizarInputHidden(hiddenInputRef);
        if (dropdownRef) dropdownRef.classList.add('hidden');
    }

    // Función para remover actividad
    function removerActividad(actividadId) {
        actividadesSeleccionadas = actividadesSeleccionadas.filter(act => act.id !== actividadId);
        const currentTagsContainer = document.getElementById('actividades-seleccionadas');
        const currentHiddenInput = document.getElementById('actividades_seleccionadas_input');
        actualizarTags(currentTagsContainer);
        actualizarInputHidden(currentHiddenInput);
    }

    // Función para actualizar tags visuales
    function actualizarTags(tagsContainerRef) {
        if (!tagsContainerRef) tagsContainerRef = document.getElementById('actividades-seleccionadas');
        tagsContainerRef.innerHTML = '';

        if (actividadesSeleccionadas.length === 0) {
            const emptyMessage = document.createElement('div');
            emptyMessage.id = 'no-actividades-message';
            emptyMessage.className = 'flex items-center justify-center text-gray-400 text-sm italic';
            emptyMessage.innerHTML = '<i class="fas fa-plus-circle mr-2"></i>No hay actividades seleccionadas';
            tagsContainerRef.appendChild(emptyMessage);
        } else {
            actividadesSeleccionadas.forEach(actividad => {
                const tag = document.createElement('div');
                tag.className = 'inline-flex items-center gap-2 px-3 py-1 bg-[#9d2449]/10 text-[#9d2449] border border-[#9d2449]/20 rounded-lg text-sm';
                tag.innerHTML = `
                    <span>${actividad.nombre}</span>
                    <button type="button" onclick="removerActividad(${actividad.id})" class="hover:text-red-500 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                tagsContainerRef.appendChild(tag);
            });
        }
    }

    // Función para actualizar input hidden
    function actualizarInputHidden(hiddenInputRef) {
        if (!hiddenInputRef) hiddenInputRef = document.getElementById('actividades_seleccionadas_input');
        const actividadesIds = actividadesSeleccionadas.map(act => act.id);
        hiddenInputRef.value = JSON.stringify(actividadesIds);
    }

    // Función global para remover actividades
    window.removerActividad = removerActividad;

    // Función para enviar datos vía AJAX
    async function enviarDatosAjax(formulario) {
        try {

            
            // Obtener datos del formulario
            const formData = new FormData(formulario);
            const datos = {};
            for (let [key, value] of formData.entries()) {
                datos[key] = value;
            }
            

            
            // Mostrar indicador de carga
            mostrarIndicadorCarga(true);
            
            // Enviar petición AJAX
            const response = await fetch(formulario.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(datos)
            });
            
            const result = await response.json();
            
            // Ocultar indicador de carga
            mostrarIndicadorCarga(false);
            
            if (response.ok && result.success) {
                // Guardado exitoso

                
                // Mostrar mensaje de éxito
                mostrarMensajeExito('Datos guardados correctamente. Avanzando al siguiente paso...');
                
                // Avanzar al siguiente paso
                setTimeout(() => {
                    avanzarAlPasoSiguiente(result.next_step || 2);
                }, 500);
                
            } else {
                // Error en el guardado
                console.error('❌ Error al guardar:', result);
                
                if (result.errors) {
                    // Errores de validación
                    mostrarErroresValidacion(result.errors);
        } else {
                    // Error general
                    mostrarError(result.message || 'Error al guardar los datos');
                }
            }
            
        } catch (error) {
            console.error('❌ Error en AJAX:', error);
            mostrarIndicadorCarga(false);
            mostrarError('Error de conexión. Por favor, intente nuevamente.');
        }
    }
    
    // Función para mostrar indicador de carga
    function mostrarIndicadorCarga(mostrar) {
        let indicador = document.getElementById('indicador-carga-datos');
        
        if (mostrar) {
            if (!indicador) {
                indicador = document.createElement('div');
                indicador.id = 'indicador-carga-datos';
                indicador.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                indicador.innerHTML = `
                    <div class="bg-white rounded-xl p-6 max-w-sm mx-4 shadow-2xl">
                        <div class="flex items-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#9d2449] mr-4"></div>
                            <span class="text-gray-700">Guardando datos...</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(indicador);
                } else {
                indicador.style.display = 'flex';
                }
            } else {
            if (indicador) {
                indicador.style.display = 'none';
            }
        }
    }
    
    // Función para mostrar mensaje de éxito
    function mostrarMensajeExito(mensaje) {
        const elemento = document.createElement('div');
        elemento.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300';
        elemento.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>${mensaje}</span>
            </div>
        `;
        
        document.body.appendChild(elemento);
        
        // Animar entrada
         setTimeout(() => {
            elemento.classList.add('translate-x-0');
        }, 100);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            elemento.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (elemento.parentNode) {
                    elemento.parentNode.removeChild(elemento);
                }
            }, 300);
        }, 3000);
    }
    
    // Función para mostrar errores de validación
    function mostrarErroresValidacion(errores) {

        
        // Limpiar errores anteriores
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'bg-red-50');
        });
        
        // Mostrar nuevos errores
        for (const [campo, mensajes] of Object.entries(errores)) {
            const elemento = document.getElementById(campo) || document.querySelector(`[name="${campo}"]`);
            if (elemento) {
                // Agregar clases de error
                elemento.classList.add('border-red-500', 'bg-red-50');
                
                // Agregar mensaje de error
                const contenedor = elemento.closest('.form-group') || elemento.parentElement;
                const errorMsg = document.createElement('p');
                errorMsg.className = 'error-message text-xs text-red-500 mt-1 flex items-center';
                errorMsg.innerHTML = `
                    <i class="fas fa-exclamation-triangle mr-1 text-red-400"></i>
                    <span>${Array.isArray(mensajes) ? mensajes[0] : mensajes}</span>
                `;
                contenedor.appendChild(errorMsg);
            }
        }
        
        // Scroll al primer error
        const primerError = document.querySelector('.border-red-500');
        if (primerError) {
            primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    // Función para avanzar al paso siguiente
    function avanzarAlPasoSiguiente(pasoDestino) {

        
        try {
            // Buscar el componente Alpine.js del stepper
            const formContainer = document.querySelector('[x-data*="currentStep"]');
            if (formContainer && window.Alpine) {
                const alpineData = Alpine.$data(formContainer);
                if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                                    alpineData.currentStep = pasoDestino;
                    
                    // Trigger update en caso de que sea necesario
                    if (alpineData.$dispatch) {
                        alpineData.$dispatch('step-changed', { step: pasoDestino });
                    }
                                  } else {
                }
            } else {
            }
        } catch (error) {
            console.error('❌ Error al cambiar paso:', error);
        }
    }
});
</script>

<style>
/* Transiciones suaves */
input, select, textarea, button {
    transition: all 0.2s ease-in-out;
}

/* Efectos hover */
.form-group:hover input:not([readonly]),
.form-group:hover select:not([readonly]),
.form-group:hover textarea:not([readonly]) {
    border-color: rgb(157 36 73 / 0.3);
}

/* Dropdown styles */
#actividad-dropdown {
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endunless
