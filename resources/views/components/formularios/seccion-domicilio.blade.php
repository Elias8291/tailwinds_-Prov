@props(['title' => 'Domicilio', 'datosDomicilio' => []])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8" 
    x-data="{ 
        cp: '{{ $datosDomicilio['codigo_postal'] ?? '' }}',
        estado: '{{ $datosDomicilio['estado'] ?? '' }}',
        municipio: '{{ $datosDomicilio['municipio'] ?? '' }}',
        colonia: '{{ $datosDomicilio['colonia'] ?? '' }}',
        nombreVialidad: '{{ $datosDomicilio['calle'] ?? '' }}',
        numeroExterior: '{{ $datosDomicilio['numero_exterior'] ?? '' }}',
        numeroInterior: '{{ $datosDomicilio['numero_interior'] ?? '' }}',
        asentamientos: [],
        showSatModal: false,
        satData: null,
        
        async loadLocationData() {
            if (this.cp.length === 5) {
                try {
                    const response = await fetch(`/api/location-data/${this.cp}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.estado = data.estado;
                        this.municipio = data.municipio;
                        this.asentamientos = data.asentamientos;
                    }
                } catch (error) {
                    console.error('Error loading location data:', error);
                }
            }
        },

        init() {
            // Cargar datos existentes si están disponibles
            const satData = sessionStorage.getItem('satData');
            if (satData) {
                try {
                    const data = JSON.parse(satData);
                    this.satData = data;
                    this.cp = data.cp || this.cp;
                    this.estado = data.estado || this.estado;
                    this.municipio = data.municipio || this.municipio;
                    this.colonia = data.colonia || this.colonia;
                    this.nombreVialidad = data.calle || this.nombreVialidad;
                    this.numeroExterior = data.numeroExterior || this.numeroExterior;
                    this.numeroInterior = data.numeroInterior || this.numeroInterior;
                    
                    if (this.cp) {
                        this.loadLocationData();
                    }
                } catch (error) {
                    console.error('Error parsing SAT data:', error);
                }
            }
            
            // Watch for changes in código postal
            this.$watch('cp', (value) => {
                if (value.length === 5) {
                    this.loadLocationData();
                }
            });
        },

        async guardarDomicilio() {
            const form = this.$refs.domicilioForm;
            const formData = new FormData(form);
            
            // Agregar datos desde Alpine.js
            formData.append('codigo_postal', this.cp);
            formData.append('calle', this.nombreVialidad);
            formData.append('numero_exterior', this.numeroExterior);
            formData.append('numero_interior', this.numeroInterior);
            formData.append('colonia', this.colonia);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            try {
                const response = await fetch('{{ route("tramites.guardar-domicilio-formulario") }}', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Mostrar mensaje de éxito
                    alert('Datos de domicilio guardados correctamente');
                    
                    // Avanzar al siguiente paso si estamos en el formulario principal
                    if (window.Alpine && this.$dispatch) {
                        this.$dispatch('next-step');
                    }
                } else {
                    // Mostrar errores
                    console.error('Errores:', result.errors || result.message);
                    alert('Error al guardar: ' + (result.message || 'Error desconocido'));
                }
            } catch (error) {
                console.error('Error al guardar domicilio:', error);
                alert('Error al guardar los datos');
            }
        }
    }"
    @guardar-domicilio.window="guardarDomicilio()">
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

        <!-- Botón para ver datos del SAT -->
        <button x-show="satData" 
                @click="showSatModal = true"
                class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold text-[#9d2449] bg-[#9d2449]/5 hover:bg-[#9d2449]/10 transition-all duration-300 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver Datos SAT
        </button>
    </div>

    <form class="space-y-8" @submit.prevent="guardarDomicilio" x-ref="domicilioForm">
        <input type="hidden" name="action" value="next">
        <input type="hidden" name="seccion" value="2">
        @if(isset($datosDomicilio['tramite_id']))
            <input type="hidden" name="tramite_id" value="{{ $datosDomicilio['tramite_id'] }}">
        @endif
        
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
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50 @error('codigo_postal') border-red-500 @enderror"
                           placeholder="Ej: 12345"
                           pattern="[0-9]{4,5}"
                           maxlength="5"
                           x-model="cp"
                           value="{{ old('codigo_postal', $datosDomicilio['codigo_postal'] ?? '') }}"
                           required>
                </div>
                @error('codigo_postal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @else
                    <p class="mt-1 text-sm text-gray-500">Al ingresar el código postal se llenarán automáticamente algunos campos</p>
                @enderror
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
                            class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50 @error('colonia') border-red-500 @enderror"
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
                @error('colonia')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                           value="{{ old('calle', $datosDomicilio['calle'] ?? '') }}"
                           required>
                </div>
                @error('calle')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                           value="{{ old('numero_exterior', $datosDomicilio['numero_exterior'] ?? '') }}"
                           required>
                </div>
                @error('numero_exterior')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                           value="{{ old('numero_interior', $datosDomicilio['numero_interior'] ?? '') }}">
                </div>
                @error('numero_interior')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                           value="{{ old('entre_calle_1', $datosDomicilio['entre_calle_1'] ?? '') }}"
                           required>
                </div>
                @error('entre_calle_1')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                           value="{{ old('entre_calle_2', $datosDomicilio['entre_calle_2'] ?? '') }}"
                           required>
                </div>
                @error('entre_calle_2')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </form>

    <!-- Modal para mostrar datos del SAT -->
    <div x-show="showSatModal" 
         x-cloak
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
         @click.self="showSatModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 overflow-hidden transform transition-all"
             @click.away="showSatModal = false">
            <div class="bg-gradient-to-r from-[#9d2449]/8 to-[#7a1d37]/8 px-6 py-4 border-b border-[#9d2449]/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-2 shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Datos de la Constancia SAT</h3>
                    </div>
                    <button @click="showSatModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 max-h-[80vh] overflow-y-auto">
                <div class="space-y-6">
                    <!-- Sección de información principal -->
                    <div class="bg-white rounded-xl p-6 border border-[#B4325E]/10 shadow-sm">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="satData?.tipoPersona === 'Moral' ? 
                                    (satData?.razonSocial || 'Razón Social No Disponible') : 
                                    (satData?.nombreCompleto || 'Nombre No Disponible')">
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                        RFC: <span x-text="satData?.rfc || 'No disponible'"></span>
                                    </span>
                                    <template x-if="satData?.curp">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                            CURP: <span x-text="satData?.curp"></span>
                                        </span>
                                    </template>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                        Persona <span x-text="satData?.tipoPersona || 'No especificada'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Secciones dinámicas -->
                    <template x-if="satData?.sections">
                        <template x-for="section in satData.sections" :key="section.title">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <h4 class="text-lg font-semibold text-gray-800" x-text="section.title || 'Información Adicional'"></h4>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    <template x-for="field in section.fields" :key="field.label">
                                        <div class="px-6 py-4">
                                            <div class="flex flex-col sm:flex-row sm:items-center">
                                                <div class="sm:w-1/3">
                                                    <span class="text-sm font-medium text-gray-500" x-text="field.label"></span>
                                                </div>
                                                <div class="sm:w-2/3 mt-1 sm:mt-0">
                                                    <span class="text-sm text-gray-900" x-text="field.value"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </template>

                    <!-- Información de dirección -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h4 class="text-lg font-semibold text-gray-800">Dirección</h4>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2">
                                <template x-if="satData?.nombreVialidad">
                                    <p class="text-sm text-gray-900">
                                        <span x-text="satData.nombreVialidad"></span>
                                        <template x-if="satData?.numeroExterior">
                                            <span x-text="' #' + satData.numeroExterior"></span>
                                        </template>
                                        <template x-if="satData?.numeroInterior">
                                            <span x-text="' Int. ' + satData.numeroInterior"></span>
                                        </template>
                                    </p>
                                </template>
                                <template x-if="satData?.colonia">
                                    <p class="text-sm text-gray-600">
                                        Col. <span x-text="satData.colonia"></span>
                                    </p>
                                </template>
                                <template x-if="satData?.cp">
                                    <p class="text-sm text-gray-600">
                                        CP <span x-text="satData.cp"></span>
                                    </p>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-end">
                    <button @click="showSatModal = false"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
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

/* Estilos para campos readonly */
input[readonly] {
    @apply bg-gray-50;
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

/* Animación suave para los inputs */
input, select, textarea {
    @apply transition-all duration-300 bg-white shadow-sm;
}

input:focus:not([readonly]), 
select:focus, 
textarea:focus {
    @apply transform -translate-y-px shadow-md bg-white;
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

.form-group:hover input:not([readonly]),
.form-group:hover select,
.form-group:hover textarea {
    @apply border-[#4F46E5]/40;
}

/* Estilos para el modal */
[x-cloak] {
    display: none !important;
}

.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    transform-origin: center;
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Estilos adicionales para las secciones del SAT */
.sat-section {
    @apply transition-all duration-300;
}

.sat-section:hover {
    @apply transform -translate-y-0.5 shadow-md;
}

.sat-badge {
    @apply inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium;
    background: linear-gradient(135deg, rgba(180, 50, 94, 0.1) 0%, rgba(147, 38, 75, 0.1) 100%);
}
</style> 