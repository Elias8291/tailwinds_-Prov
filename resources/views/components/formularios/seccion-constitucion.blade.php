@props(['title' => 'Constitución', 'tramite' => null, 'datosConstitucion' => [], 'readonly' => false])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8" 
    @if(!$readonly) x-data="constitucionData()" @endif>
    <!-- Encabezado con icono -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
                <i class="fas fa-file-contract text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
                <p class="text-sm text-gray-500 mt-1">Información sobre la constitución de la empresa</p>
            </div>
        </div>
    </div>

    @if($readonly)
        <!-- Vista de solo lectura para revisión -->
        <div class="space-y-6">
            @php
                // Depuración: verificar los datos recibidos
                Log::info('Datos de constitución en componente:', [
                    'datosConstitucion' => $datosConstitucion ?? 'null',
                    'is_empty' => empty($datosConstitucion),
                    'is_array' => is_array($datosConstitucion)
                ]);
            @endphp
            
            @if(!empty($datosConstitucion) && is_array($datosConstitucion))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Escritura</label>
                        <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                            {{ $datosConstitucion['numero_escritura'] ?? 'No especificado' }}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Constitución</label>
                        <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                            {{ $datosConstitucion['fecha_constitucion_formatted'] ?? ($datosConstitucion['fecha_constitucion'] ? \Carbon\Carbon::parse($datosConstitucion['fecha_constitucion'])->format('d/m/Y') : 'No especificado') }}
                        </div>
                    </div>
                    
                    <div class="form-group md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Notario</label>
                        <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                            {{ $datosConstitucion['nombre_notario'] ?? 'No especificado' }}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Entidad Federativa</label>
                        <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                            @php
                                $estados = [
                                    '1' => 'Aguascalientes', '2' => 'Baja California', '3' => 'Baja California Sur', 
                                    '4' => 'Campeche', '5' => 'Coahuila', '6' => 'Colima', '7' => 'Chiapas', 
                                    '8' => 'Chihuahua', '9' => 'Distrito Federal', '10' => 'Durango', 
                                    '11' => 'Guanajuato', '12' => 'Guerrero', '13' => 'Hidalgo', 
                                    '14' => 'Jalisco', '15' => 'Estado de México', '16' => 'Michoacán', 
                                    '17' => 'Morelos', '18' => 'Nayarit', '19' => 'Nuevo León', 
                                    '20' => 'Oaxaca', '21' => 'Puebla', '22' => 'Querétaro', 
                                    '23' => 'Quintana Roo', '24' => 'San Luis Potosí', '25' => 'Sinaloa', 
                                    '26' => 'Sonora', '27' => 'Tabasco', '28' => 'Tamaulipas', 
                                    '29' => 'Tlaxcala', '30' => 'Veracruz', '31' => 'Yucatán', '32' => 'Zacatecas'
                                ];
                            @endphp
                            {{ $datosConstitucion['entidad_federativa_nombre'] ?? ($datosConstitucion['entidad_federativa'] ? $estados[$datosConstitucion['entidad_federativa']] : 'No especificado') }}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Notario</label>
                        <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                            {{ $datosConstitucion['numero_notario'] ?? 'No especificado' }}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Registro</label>
                        <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                            {{ $datosConstitucion['numero_registro'] ?? 'No especificado' }}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inscripción</label>
                        <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                            {{ $datosConstitucion['fecha_inscripcion_formatted'] ?? ($datosConstitucion['fecha_inscripcion'] ? \Carbon\Carbon::parse($datosConstitucion['fecha_inscripcion'])->format('d/m/Y') : 'No especificado') }}
                        </div>
                    </div>
                </div>
            @else
                <!-- Mensaje cuando no hay datos de constitución -->
                <div class="text-center py-8">
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-8">
                        <div class="flex flex-col items-center">
                            <div class="bg-amber-100 rounded-full p-4 mb-4">
                                <i class="fas fa-file-contract text-amber-600 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-amber-800 mb-2">Sin Datos de Constitución</h3>
                            <div class="text-sm text-amber-700 space-y-1 text-center">
                                <p>No se encontró información de constitución para este trámite.</p>
                                <p class="text-xs text-amber-600">
                                    @if(empty($datosConstitucion))
                                        Estado: Datos vacíos
                                    @elseif(!is_array($datosConstitucion))
                                        Estado: Formato de datos incorrecto
                                    @else
                                        Estado: Solo valores por defecto
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Vista editable normal (código existente) -->
        <form class="space-y-8" @submit.prevent="guardarConstitucion" x-ref="constitucionForm">
            <input type="hidden" name="action" value="next">
            <input type="hidden" name="seccion" value="3">
            <input type="hidden" name="tramite_id" value="{{ $datosConstitucion['tramite_id'] ?? ($tramite->id ?? '') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Número de Escritura -->
                <div class="form-group">
                    <label for="numero_escritura" class="block text-sm font-medium text-gray-700 mb-2">
                        Número de Escritura
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="numero_escritura" name="numero_escritura"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: 1234 o 1234/2024"
                               maxlength="15"
                               x-model="numeroEscritura"
                               required>
                    </div>
                </div>

                <!-- Fecha de Constitución -->
                <div class="form-group">
                    <label for="fecha_constitucion" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Constitución
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="date" id="fecha_constitucion" name="fecha_constitucion"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               x-model="fechaConstitucion"
                               required>
                    </div>
                </div>

                <!-- Nombre del Notario -->
                <div class="form-group md:col-span-2">
                    <label for="nombre_notario" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Notario
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="nombre_notario" name="nombre_notario"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: Lic. Juan Pérez González"
                               maxlength="100"
                               x-model="nombreNotario"
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
                        <select id="entidad_federativa" name="entidad_federativa"
                                class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                x-model="entidadFederativa"
                                required>
                            <option value="">Seleccione un estado</option>
                            @php
                            $estados = [
                                '1' => 'Aguascalientes', '2' => 'Baja California', '3' => 'Baja California Sur', 
                                '4' => 'Campeche', '5' => 'Coahuila', '6' => 'Colima', '7' => 'Chiapas', 
                                '8' => 'Chihuahua', '9' => 'Distrito Federal', '10' => 'Durango', 
                                '11' => 'Guanajuato', '12' => 'Guerrero', '13' => 'Hidalgo', 
                                '14' => 'Jalisco', '15' => 'Estado de México', '16' => 'Michoacán', 
                                '17' => 'Morelos', '18' => 'Nayarit', '19' => 'Nuevo León', 
                                '20' => 'Oaxaca', '21' => 'Puebla', '22' => 'Querétaro', 
                                '23' => 'Quintana Roo', '24' => 'San Luis Potosí', '25' => 'Sinaloa', 
                                '26' => 'Sonora', '27' => 'Tabasco', '28' => 'Tamaulipas', 
                                '29' => 'Tlaxcala', '30' => 'Veracruz', '31' => 'Yucatán', '32' => 'Zacatecas'
                            ];
                            @endphp
                            @foreach($estados as $id => $estado)
                                <option value="{{ $id }}">{{ $estado }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Número de Notario -->
                <div class="form-group">
                    <label for="numero_notario" class="block text-sm font-medium text-gray-700 mb-2">
                        Número de Notario
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="numero_notario" name="numero_notario"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: 123"
                               maxlength="10"
                               x-model="numeroNotario"
                               required>
                    </div>
                </div>

                <!-- Número de Registro -->
                <div class="form-group">
                    <label for="numero_registro" class="block text-sm font-medium text-gray-700 mb-2">
                        Número de Registro
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="numero_registro" name="numero_registro"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               placeholder="Ej: 0123456789 o FME123456789"
                               maxlength="14"
                               x-model="numeroRegistro"
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
                        <input type="date" id="fecha_inscripcion" name="fecha_inscripcion"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                               x-model="fechaInscripcion"
                               required>
                    </div>
                </div>
            </div>
            
            <!-- Botones de navegación -->
            <div class="flex justify-between pt-6 border-t border-gray-100">
                <button type="button" 
                        onclick="navegarAnteriorConstitucion()"
                        class="inline-flex items-center bg-gray-600 text-white px-6 py-2 rounded-xl shadow-lg hover:bg-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-gray-600/20">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Anterior
                </button>
                
                <button type="button" 
                        onclick="guardarConstitucionYSiguiente()"
                        class="inline-flex items-center bg-[#9d2449] text-white px-6 py-2 rounded-xl shadow-lg hover:bg-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-[#9d2449]/20">
                    <i class="fas fa-save mr-2"></i> Guardar y Continuar
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    @endif
</div>

@if(!$readonly)
<script>
function constitucionData() {
    return {
        numeroEscritura: '',
        fechaConstitucion: '',
        nombreNotario: '',
        entidadFederativa: '',
        numeroNotario: '',
        numeroRegistro: '',
        fechaInscripcion: '',
        
        async init() {
            // Cargar datos existentes si los hay
            const datosConstitucion = @json($datosConstitucion ?? []);
            const tramite = @json($tramite ?? null);
            
            if (datosConstitucion && Object.keys(datosConstitucion).length > 0) {
                await this.cargarDatosDesdeObjeto(datosConstitucion);
            } else if (tramite && tramite.id) {
                await this.cargarDatosDesdeTramite(tramite.id);
            }
        },

        // Cargar datos desde un objeto
        async cargarDatosDesdeObjeto(datosConstitucion) {
            try {
                this.numeroEscritura = datosConstitucion.numero_escritura || '';
                this.fechaConstitucion = datosConstitucion.fecha_constitucion || '';
                this.nombreNotario = datosConstitucion.nombre_notario || '';
                this.entidadFederativa = datosConstitucion.entidad_federativa || '';
                this.numeroNotario = datosConstitucion.numero_notario || '';
                this.numeroRegistro = datosConstitucion.numero_registro || '';
                this.fechaInscripcion = datosConstitucion.fecha_inscripcion || '';
            } catch (error) {
                console.error('Error al cargar datos desde objeto:', error);
            }
        },

        // Cargar datos desde el trámite
        async cargarDatosDesdeTramite(tramiteId) {
            try {
                const response = await fetch(`/api/tramite/${tramiteId}/constitucion`);
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.constitucion) {
                        await this.cargarDatosDesdeObjeto(data.constitucion);
                    }
                }
            } catch (error) {
                console.error('Error al cargar datos desde trámite:', error);
            }
        },

        // Guardar constitución
        async guardarConstitucion() {
            try {
                // Obtener tramite_id de los datos pasados al componente
                const tramite = @json($tramite ?? null);
                const datosConstitucion = @json($datosConstitucion ?? []);
                
                let tramiteId = null;
                if (tramite && tramite.id) {
                    tramiteId = tramite.id;
                } else if (datosConstitucion && datosConstitucion.tramite_id) {
                    tramiteId = datosConstitucion.tramite_id;
                }
                
                if (!tramiteId) {
                    alert('Error: No se pudo obtener el ID del trámite');
                    return false;
                }
                
                const formData = new FormData();
                formData.append('tramite_id', tramiteId);
                formData.append('numero_escritura', this.numeroEscritura);
                formData.append('fecha_constitucion', this.fechaConstitucion);
                formData.append('nombre_notario', this.nombreNotario);
                formData.append('entidad_federativa', this.entidadFederativa);
                formData.append('numero_notario', this.numeroNotario);
                formData.append('numero_registro', this.numeroRegistro);
                formData.append('fecha_inscripcion', this.fechaInscripcion);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                const response = await fetch('{{ route('tramites.guardar-constitucion-formulario') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const responseText = await response.text();
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

// Función para navegar al paso anterior desde constitución
function navegarAnteriorConstitucion() {
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

// Función para guardar constitución y navegar al siguiente paso
async function guardarConstitucionYSiguiente() {
    try {
        // 1. Buscar el componente Alpine.js de constitución
        const constitucionContainer = document.querySelector('[x-data*="constitucionData"]');
        if (constitucionContainer && typeof Alpine !== 'undefined') {
            const alpineData = Alpine.$data(constitucionContainer);
            
            if (alpineData && typeof alpineData.guardarConstitucion === 'function') {
                const guardado = await alpineData.guardarConstitucion();
                
                if (guardado) {
                    navegarSiguienteDesdeConstitucion();
                }
                return;
            }
        }
        
        // Fallback: intentar navegar sin guardar
        navegarSiguienteDesdeConstitucion();
        
    } catch (error) {
        navegarSiguienteDesdeConstitucion();
    }
}

// Función para navegar al siguiente paso desde constitución
function navegarSiguienteDesdeConstitucion() {
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
@endif

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

/* Animación suave para los inputs */
input, select, textarea {
    @apply transition-all duration-300 bg-white shadow-sm;
}

input:focus, select:focus, textarea:focus {
    @apply transform -translate-y-px shadow-md bg-white;
}

/* Animaciones para los iconos de sección */
.h-12 {
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(157, 36, 73, 0.1), 
                0 2px 4px -1px rgba(157, 36, 73, 0.06);
}

/* Nuevos estilos para mejorar la apariencia de los inputs */
.form-group {
    @apply relative;
}

.form-group input,
.form-group select,
.form-group textarea {
    @apply border-[#4F46E5]/20;
}

.form-group:hover input,
.form-group:hover select,
.form-group:hover textarea {
    @apply border-[#4F46E5]/40;
}
</style> 