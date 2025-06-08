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
                                class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50">
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
                               maxlength="13">
                    </div>
                </div>
            </div>

            <!-- Razón Social -->
            <div class="form-group">
                <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                    Razón Social
                    <span class="text-[#9d2449]">*</span>
                </label>
                <div class="relative group">
                    <input type="text" id="razon_social" name="razon_social"
                           class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50"
                           placeholder="Nombre de la empresa"
                           maxlength="100">
                </div>
            </div>

            <!-- Objeto Social -->
            <div class="form-group">
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

        <!-- Clasificación -->
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sectores -->
                <div class="form-group">
                    <label for="sectores" class="block text-sm font-medium text-gray-700 mb-2">
                        Sectores
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <select id="sectores" name="sectores"
                                class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50">
                            <option value="">Seleccione un sector</option>
                            <option value="1">Construcción</option>
                            <option value="2">Servicios</option>
                            <option value="3">Comercio</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>

                <!-- Actividades -->
                <div class="form-group">
                    <label for="actividad" class="block text-sm font-medium text-gray-700 mb-2">
                        Actividades
                        <span class="text-[#9d2449]">*</span>
                    </label>
                    <div class="relative group">
                        <select id="actividad" name="actividad"
                                class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all group-hover:border-[#9d2449]/50">
                            <option value="">Seleccione una actividad</option>
                            <option value="1">Construcción de edificios</option>
                            <option value="2">Mantenimiento de edificios</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="flex flex-wrap gap-2 mt-3">
                <div class="inline-flex items-center px-3 py-1.5 bg-gradient-to-br from-[#9d2449]/5 to-[#9d2449]/10 border border-[#9d2449]/20 rounded-lg group hover:shadow-sm transition-all duration-200">
                    <span class="text-sm text-gray-700">Construcción de edificios</span>
                    <button type="button" class="ml-2 p-1 text-[#9d2449] hover:text-[#9d2449]/70 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres
    const objetoSocial = document.getElementById('objeto_social');
    const charCount = document.querySelector('.char-count');

    if (objetoSocial && charCount) {
        objetoSocial.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    // Efectos de hover y focus mejorados con animación
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            const iconWrapper = this.parentElement.querySelector('.icon-wrapper');
            if (iconWrapper) {
                iconWrapper.querySelector('i').style.opacity = '1';
                iconWrapper.querySelector('i').style.transform = 'scale(1.1) translateY(-1px)';
            }
        });

        input.addEventListener('blur', function() {
            const iconWrapper = this.parentElement.querySelector('.icon-wrapper');
            if (iconWrapper) {
                iconWrapper.querySelector('i').style.opacity = '0.7';
                iconWrapper.querySelector('i').style.transform = 'scale(1) translateY(0)';
            }
        });
    });

    // Efecto de ondulación para los iconos de sección
    const sectionIcons = document.querySelectorAll('.h-12, .h-9, .h-10');
    sectionIcons.forEach(icon => {
        icon.addEventListener('mouseover', function() {
            this.style.transform = 'scale(1.02)';
        });

        icon.addEventListener('mouseout', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script> 