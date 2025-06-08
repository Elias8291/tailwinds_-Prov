@props([
    'action' => '#',
    'method' => 'POST',
    'datosPrevios' => [],
    'isRevisor' => false,
    'seccion' => 5,
    'totalSecciones' => 6,
    'isConfirmationSection' => false,
    'estados' => [],
    'title' => 'Apoderado Legal'
])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 transition-all duration-300 hover:shadow-xl">
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

    <form id="formulario5" action="{{ $action }}" method="{{ $method }}" class="space-y-8">
        @csrf
        <div id="section-5" class="form-section">
            <!-- Datos del Apoderado -->
            <div class="space-y-6">
                <div class="flex items-center space-x-2 mb-4">
                    <i class="fas fa-user-tie text-[#9d2449]"></i>
                    <h4 class="text-lg font-medium text-gray-700">Datos del Apoderado o Representante Legal</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre del Apoderado -->
                    <div class="form-group">
                        <label for="nombre-apoderado" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <input type="text" id="nombre-apoderado" name="nombre-apoderado"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   placeholder="Ej: Lic. Juan Pérez González"
                                   value="{{ $datosPrevios['nombre-apoderado'] ?? '' }}"
                                   maxlength="100">
                        </div>
                    </div>

                    <!-- Número de Escritura -->
                    <div class="form-group">
                        <label for="numero-escritura" class="block text-sm font-medium text-gray-700 mb-2">
                            Número de Escritura
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <input type="text" id="numero-escritura" name="numero-escritura"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   placeholder="Ej: 12345"
                                   value="{{ $datosPrevios['numero-escritura'] ?? '' }}"
                                   maxlength="10">
                        </div>
                    </div>

                    <!-- Nombre del Notario -->
                    <div class="form-group">
                        <label for="nombre-notario" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Notario
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <input type="text" id="nombre-notario" name="nombre-notario"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   placeholder="Ej: Lic. María López Ramírez"
                                   value="{{ $datosPrevios['nombre-notario'] ?? '' }}"
                                   maxlength="100">
                        </div>
                    </div>

                    <!-- Número del Notario -->
                    <div class="form-group">
                        <label for="numero-notario" class="block text-sm font-medium text-gray-700 mb-2">
                            Número del Notario
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <input type="text" id="numero-notario" name="numero-notario"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   placeholder="Ej: 123"
                                   value="{{ $datosPrevios['numero-notario'] ?? '' }}"
                                   maxlength="10">
                        </div>
                    </div>

                    <!-- Entidad Federativa -->
                    <div class="form-group">
                        <label for="entidad-federativa" class="block text-sm font-medium text-gray-700 mb-2">
                            Entidad Federativa
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <select id="entidad-federativa" name="entidad-federativa"
                                    class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40 appearance-none">
                                <option value="">Seleccione un estado</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado['id'] }}" {{ isset($datosPrevios['entidad-federativa']) && $datosPrevios['entidad-federativa'] == $estado['id'] ? 'selected' : '' }}>
                                        {{ $estado['nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Fecha de Escritura -->
                    <div class="form-group">
                        <label for="fecha-escritura" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Escritura
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <input type="date" id="fecha-escritura" name="fecha-escritura"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                   value="{{ $datosPrevios['fecha-escritura'] ?? '' }}">
                        </div>
                    </div>
                </div>

                <!-- Datos de Inscripción -->
                <div class="mt-8">
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-book text-[#9d2449]"></i>
                        <h4 class="text-lg font-medium text-gray-700">Datos de Inscripción en el Registro Público</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Número de Registro -->
                        <div class="form-group">
                            <label for="numero-registro" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Registro o Folio Mercantil
                                <span class="text-[#9d2449]">*</span>
                            </label>
                            <div class="relative group">
                                <input type="text" id="numero-registro" name="numero-registro"
                                       class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                       placeholder="Ej: 987654"
                                       value="{{ $datosPrevios['numero-registro'] ?? '' }}"
                                       maxlength="10">
                            </div>
                        </div>

                        <!-- Fecha de Inscripción -->
                        <div class="form-group">
                            <label for="fecha-inscripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Inscripción
                                <span class="text-[#9d2449]">*</span>
                            </label>
                            <div class="relative group">
                                <input type="date" id="fecha-inscripcion" name="fecha-inscripcion"
                                       class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all duration-300 hover:border-[#4F46E5]/40"
                                       value="{{ $datosPrevios['fecha-inscripcion'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

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
