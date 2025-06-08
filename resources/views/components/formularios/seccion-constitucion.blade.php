@props(['title' => 'Constitución'])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
    <!-- Encabezado con icono -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
            <i class="fas fa-file-contract text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Información sobre la constitución de la empresa</p>
        </div>
    </div>

    <form class="space-y-8">
        <input type="hidden" name="action" value="next">
        <input type="hidden" name="seccion" value="3">

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
                           maxlength="15">
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
                           maxlength="100">
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
                            required>
                        <option value="">Seleccione un estado</option>
                        @foreach(config('estados.mexico') as $estado)
                            <option value="{{ $estado }}">{{ $estado }}</option>
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
                           maxlength="10">
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
                           maxlength="14">
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
                           required>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
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