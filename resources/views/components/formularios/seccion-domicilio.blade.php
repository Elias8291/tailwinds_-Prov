@props(['title' => 'Domicilio'])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
    <!-- Encabezado con icono -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
            <i class="fas fa-map-marker-alt text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Información sobre la ubicación de la empresa</p>
        </div>
    </div>

    <form class="space-y-8">
        <input type="hidden" name="action" value="next">
        <input type="hidden" name="seccion" value="2">
        
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

/* Animación suave para los inputs */
input, select, textarea {
    @apply transition-all duration-300 bg-white shadow-sm;
}

input:focus, select:focus, textarea:focus {
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

.form-group:hover input,
.form-group:hover select,
.form-group:hover textarea {
    @apply border-[#4F46E5]/40;
}
</style> 