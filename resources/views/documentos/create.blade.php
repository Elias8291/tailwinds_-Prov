@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
            <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
                <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Nuevo Documento</h2>
                    <p class="text-sm text-gray-500 mt-1">Complete la información del documento</p>
                </div>
            </div>

            <form action="{{ route('documentos.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="form-group md:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Documento
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <input type="text" id="nombre" name="nombre"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                   placeholder="Ej: Acta Constitutiva"
                                   value="{{ old('nombre') }}"
                                   required>
                        </div>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo -->
                    <div class="form-group">
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Documento
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <select id="tipo" name="tipo"
                                    class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                    required>
                                <option value="">Seleccione un tipo</option>
                                <option value="Certificado" {{ old('tipo') == 'Certificado' ? 'selected' : '' }}>Certificado</option>
                                <option value="Copia" {{ old('tipo') == 'Copia' ? 'selected' : '' }}>Copia</option>
                                <option value="Formulario" {{ old('tipo') == 'Formulario' ? 'selected' : '' }}>Formulario</option>
                                <option value="Carta" {{ old('tipo') == 'Carta' ? 'selected' : '' }}>Carta</option>
                                <option value="Comprobante" {{ old('tipo') == 'Comprobante' ? 'selected' : '' }}>Comprobante</option>
                                <option value="Acta" {{ old('tipo') == 'Acta' ? 'selected' : '' }}>Acta</option>
                                <option value="Otro" {{ old('tipo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        @error('tipo')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Persona -->
                    <div class="form-group">
                        <label for="tipo_persona" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Persona
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <select id="tipo_persona" name="tipo_persona"
                                    class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                    required>
                                <option value="">Seleccione un tipo</option>
                                <option value="Física" {{ old('tipo_persona') == 'Física' ? 'selected' : '' }}>Física</option>
                                <option value="Moral" {{ old('tipo_persona') == 'Moral' ? 'selected' : '' }}>Moral</option>
                                <option value="Ambas" {{ old('tipo_persona') == 'Ambas' ? 'selected' : '' }}>Ambas</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        @error('tipo_persona')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Expiración -->
                    <div class="form-group">
                        <label for="fecha_expiracion" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Expiración
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <input type="date" id="fecha_expiracion" name="fecha_expiracion"
                                   class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                   value="{{ old('fecha_expiracion') }}"
                                   required>
                        </div>
                        @error('fecha_expiracion')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Es Visible -->
                    <div class="form-group">
                        <label for="es_visible" class="block text-sm font-medium text-gray-700 mb-2">
                            Visibilidad
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <select id="es_visible" name="es_visible"
                                    class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                    required>
                                <option value="1" {{ old('es_visible', '1') == '1' ? 'selected' : '' }}>Visible</option>
                                <option value="0" {{ old('es_visible') == '0' ? 'selected' : '' }}>No Visible</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        @error('es_visible')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="form-group md:col-span-2">
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción
                            <span class="text-[#9d2449]">*</span>
                        </label>
                        <div class="relative group">
                            <textarea id="descripcion" name="descripcion"
                                      class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all group-hover:border-[#4F46E5]/50"
                                      rows="4"
                                      placeholder="Ingrese una descripción del documento"
                                      required>{{ old('descripcion') }}</textarea>
                        </div>
                        @error('descripcion')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('documentos.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-800 transition-colors duration-200">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-[#9d2449] hover:bg-[#8a203f] rounded-lg transition-all duration-300 transform hover:scale-105">
                        Guardar Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Estilos base */
.form-group {
    @apply relative mb-4;
}

/* Estilos para campos con error */
.has-error input,
.has-error select,
.has-error textarea {
    @apply border-red-300 !important;
}

.has-error .text-gray-500 {
    @apply text-red-500;
}

/* Transiciones y efectos hover */
input, select, textarea, button {
    @apply transition-all duration-300;
}

input:focus, select:focus, textarea:focus {
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
textarea:focus-visible,
button:focus-visible {
    @apply ring-2 ring-offset-2 ring-[#4F46E5]/20;
}

/* Estilos para el icono de sección */
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
.form-group:hover select,
.form-group:hover textarea {
    @apply border-[#9d2449]/30;
}

input:focus, select:focus, textarea:focus {
    @apply ring-2 ring-[#9d2449]/20 border-[#9d2449];
    box-shadow: 0 0 0 1px rgba(157, 36, 73, 0.1), 
                0 2px 4px rgba(157, 36, 73, 0.05);
}
</style>
@endsection 