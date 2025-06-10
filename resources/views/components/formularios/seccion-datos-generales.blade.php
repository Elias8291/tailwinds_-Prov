@props(['title' => 'Datos Generales'])

<!-- Asegúrate de incluir Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
    <!-- Encabezado con icono mejorado -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
            <i class="fas fa-building text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Complete la información general del proveedor</p>
        </div>
    </div>

    <form class="space-y-8">
        <input type="hidden" name="action" value="next">
        <input type="hidden" name="seccion" value="1">
        <!-- Información Principal -->
        <div class="space-y-6">
            <!-- Tipo de Proveedor y RFC -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tipo de Proveedor -->
                <div class="form-group">
                    <label for="tipo_persona" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Proveedor
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <select id="tipo_persona" name="tipo_persona" 
                                class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50"
                                x-model="tipoPersona"
                                :value="tipoPersona">
                            <option value="">Seleccione un tipo</option>
                            <option value="Física">Física</option>
                            <option value="Moral">Moral</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-[#9d2449]/50"></i>
                        </div>
                    </div>
                </div>

                <!-- RFC -->
                <div class="form-group">
                    <label for="rfc" class="block text-sm font-medium text-gray-700 mb-2">
                        RFC
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="rfc" name="rfc"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50"
                               placeholder="Ej. XAXX010101000"
                               maxlength="13"
                               x-model="rfc">
                    </div>
                </div>
            </div>

            <!-- CURP - Solo visible para persona física -->
            <div class="form-group" x-show="tipoPersona === 'Física'" x-transition>
                <label for="curp" class="block text-sm font-medium text-gray-700 mb-2">
                    CURP
                    <span class="text-[#9d2449]">*</span>
                </label>
                <div class="relative group">
                    <input type="text" id="curp" name="curp"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50"
                           placeholder="Ej. XAXX010101HDFXXX01"
                           maxlength="18"
                           x-model="curp"
                           x-bind:required="tipoPersona === 'Física'">
                </div>
            </div>

            <!-- Razón Social -->
            @if($datosTramite['tipo_persona'] === 'Moral' || ($datosTramite['tipo_tramite'] === 'actualizacion' && !empty($datosTramite['nombre_completo'])))
            <div class="form-group">
                <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                    <span x-text="tipoPersona === 'Moral' ? 'Razón Social' : 'Nombre Completo'"></span>
                    <span class="text-[#9d2449]">*</span>
                </label>
                <div class="relative group">
                    <input type="text" id="razon_social" name="razon_social"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50"
                           :placeholder="tipoPersona === 'Moral' ? 'Nombre de la empresa' : 'Nombre completo'"
                           maxlength="100"
                           x-model="razonSocial"
                           value="{{ $datosTramite['nombre_completo'] }}"
                           {{ $datosTramite['tipo_tramite'] === 'inscripcion' ? 'readonly' : '' }}>
                </div>
            </div>
            @endif

            <!-- Objeto Social -->
            <div class="form-group mb-8">
                <label for="objeto_social" class="block text-sm font-medium text-gray-700 mb-2">
                    Objeto Social
                    <span class="text-[#9d2449]">*</span>
                </label>
                <div class="relative group">
                    <textarea id="objeto_social" name="objeto_social" rows="4"
                              class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50 resize-none"
                              placeholder="Describa el objeto social de la empresa"
                              maxlength="500"></textarea>
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                        <span class="char-count">0</span>/500
                    </div>
                </div>
            </div>
        </div>

        <!-- Sector y Actividad -->
        <div class="space-y-8">
            <!-- Sector con búsqueda -->
            <div class="form-group">
                <label for="sector_search" class="block text-sm font-medium text-gray-700 mb-2">
                    Buscar Sector
                </label>
                <div class="relative group">
                    <input type="text" id="sector_search" 
                           class="block w-full px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                           placeholder="Escriba para buscar un sector...">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Sector -->
                <div class="form-group">
                    <label for="sector_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Sector
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <select id="sector_id" name="sector_id"
                                class="block w-full px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                required>
                            <option value="">Seleccione un Sector</option>
                            @foreach(\App\Models\Sector::all() as $sector)
                                <option value="{{ $sector->id }}" 
                                        data-nombre="{{ $sector->nombre }}"
                                        title="{{ $sector->nombre }}">
                                    {{ \Illuminate\Support\Str::limit($sector->nombre, 40) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>

                <!-- Actividad -->
                <div class="form-group">
                    <label for="actividad_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Actividad
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <select id="actividad_id" name="actividad_id"
                                class="block w-full px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                required disabled>
                            <option value="">Primero seleccione un sector</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </div>
                    </div>
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
            <input type="hidden" id="actividades_seleccionadas_input" name="actividades_seleccionadas" value="">
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
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50"
                               placeholder="Nombre completo del contacto"
                               maxlength="40">
                    </div>
                </div>

                <!-- Cargo -->
                <div class="form-group">
                    <label for="contacto_cargo" class="block text-sm font-medium text-gray-700 mb-2">
                        Cargo o Puesto
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" id="contacto_cargo" name="contacto_cargo"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50"
                               placeholder="Cargo en la empresa"
                               maxlength="50">
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="contacto_correo" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="email" id="contacto_correo" name="contacto_correo"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50"
                               placeholder="correo@ejemplo.com">
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label for="contacto_telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono de Contacto
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <input type="tel" id="contacto_telefono" name="contacto_telefono"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50"
                               placeholder="10 dígitos"
                               pattern="[0-9]{10}"
                               maxlength="10"
                               inputmode="numeric">
                    </div>
                </div>
            </div>
        </div>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres
    const objetoSocial = document.getElementById('objeto_social');
    const charCount = document.querySelector('.char-count');
    const noActividadesMessage = document.getElementById('no-actividades-message');

    if (objetoSocial && charCount) {
        objetoSocial.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    // Manejo de sectores y actividades
    const sectorSearch = document.getElementById('sector_search');
    const sectorSelect = document.getElementById('sector_id');
    const actividadSelect = document.getElementById('actividad_id');
    const actividadesContainer = document.getElementById('actividades-seleccionadas');
    const actividadesInput = document.getElementById('actividades_seleccionadas_input');
    let actividadesSeleccionadas = new Set();

    function actualizarMensajeNoActividades() {
        if (noActividadesMessage) {
            noActividadesMessage.style.display = actividadesSeleccionadas.size === 0 ? 'flex' : 'none';
        }
    }

    // Función de búsqueda de sectores
    if (sectorSearch) {
        sectorSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = sectorSelect.options;

            for (let i = 1; i < options.length; i++) {
                const optionText = options[i].text.toLowerCase();
                const optionTitle = options[i].title.toLowerCase();
                const match = optionText.includes(searchTerm) || optionTitle.includes(searchTerm);
                options[i].style.display = match ? '' : 'none';
            }
        });
    }

    function actualizarActividadesInput() {
        actividadesInput.value = JSON.stringify(Array.from(actividadesSeleccionadas));
        actualizarMensajeNoActividades();
    }

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

            // Restaurar la opción en el select si corresponde al sector actual
            if (sectorSelect.value) {
                cargarActividades(sectorSelect.value);
            }
        });

        actividadesContainer.appendChild(tag);
        actualizarMensajeNoActividades();
    }

    if (sectorSelect && actividadSelect) {
        sectorSelect.addEventListener('change', async function() {
            const sectorId = this.value;
            actividadSelect.disabled = true;
            actividadSelect.innerHTML = '<option value="">Cargando actividades...</option>';

            if (!sectorId) {
                actividadSelect.innerHTML = '<option value="">Primero seleccione un sector</option>';
                actividadSelect.disabled = true;
                return;
            }

            try {
                const response = await fetch(`/api/sectores/${sectorId}/actividades`);
                if (!response.ok) throw new Error('Error al cargar actividades');

                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Error al cargar actividades');

                actividadSelect.innerHTML = '<option value="">Seleccione una actividad</option>';
                data.data.forEach(actividad => {
                    if (!actividadesSeleccionadas.has(actividad.id.toString())) {
                        const option = document.createElement('option');
                        option.value = actividad.id;
                        option.textContent = actividad.nombre;
                        option.title = actividad.nombre;
                        actividadSelect.appendChild(option);
                    }
                });

                actividadSelect.disabled = false;

            } catch (error) {
                console.error('Error:', error);
                actividadSelect.innerHTML = '<option value="">Error al cargar actividades</option>';
                actividadSelect.disabled = true;
            }
        });

        actividadSelect.addEventListener('change', function() {
            const actividadId = this.value;
            if (!actividadId) return;

            const actividadNombre = this.options[this.selectedIndex].text;
            const sectorNombre = sectorSelect.options[sectorSelect.selectedIndex].getAttribute('data-nombre');

            if (!actividadesSeleccionadas.has(actividadId)) {
                actividadesSeleccionadas.add(actividadId);
                actualizarActividadesInput();
                agregarTag(actividadId, actividadNombre, sectorNombre);
            }

            // Resetear el select
            this.value = '';
        });
    }

    // Inicializar el mensaje
    actualizarMensajeNoActividades();
});
</script> 