@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                    <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                        Registrar Nuevo Proveedor
                    </h2>
                    <p class="text-sm text-gray-500">Complete la información del nuevo proveedor</p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6">
            <form method="POST" action="{{ route('proveedores.store') }}" class="space-y-6">
                @csrf

                <!-- Información del Solicitante -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Solicitante</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="rfc" class="block text-sm font-medium text-gray-700">RFC</label>
                            <input type="text" 
                                   name="rfc" 
                                   id="rfc" 
                                   value="{{ old('rfc') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]"
                                   required>
                            @error('rfc')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tipo_persona" class="block text-sm font-medium text-gray-700">Tipo de Persona</label>
                            <select name="tipo_persona" 
                                    id="tipo_persona"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]"
                                    required>
                                <option value="">Seleccione...</option>
                                <option value="Fisica" {{ old('tipo_persona') == 'Fisica' ? 'selected' : '' }}>Física</option>
                                <option value="Moral" {{ old('tipo_persona') == 'Moral' ? 'selected' : '' }}>Moral</option>
                            </select>
                            @error('tipo_persona')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="nombre_container" class="hidden">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   value="{{ old('nombre') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="razon_social_container" class="hidden">
                            <label for="razon_social" class="block text-sm font-medium text-gray-700">Razón Social</label>
                            <input type="text" 
                                   name="razon_social" 
                                   id="razon_social" 
                                   value="{{ old('razon_social') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]">
                            @error('razon_social')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Información del Proveedor -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Proveedor</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="pv" class="block text-sm font-medium text-gray-700">PV</label>
                            <input type="text" 
                                   name="pv" 
                                   id="pv" 
                                   value="{{ old('pv') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]"
                                   required>
                            @error('pv')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select name="estado" 
                                    id="estado"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]"
                                    required>
                                <option value="">Seleccione...</option>
                                <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                <option value="Pendiente Renovacion" {{ old('estado') == 'Pendiente Renovacion' ? 'selected' : '' }}>Pendiente Renovación</option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_registro" class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                            <input type="date" 
                                   name="fecha_registro" 
                                   id="fecha_registro" 
                                   value="{{ old('fecha_registro') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]"
                                   required>
                            @error('fecha_registro')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                            <input type="date" 
                                   name="fecha_vencimiento" 
                                   id="fecha_vencimiento" 
                                   value="{{ old('fecha_vencimiento') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]"
                                   required>
                            @error('fecha_vencimiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea name="observaciones" 
                                      id="observaciones" 
                                      rows="3"
                                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('proveedores.index') }}" 
                       class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-lg hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transition-all duration-300">
                        Registrar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoPersonaSelect = document.getElementById('tipo_persona');
    const nombreContainer = document.getElementById('nombre_container');
    const razonSocialContainer = document.getElementById('razon_social_container');
    const nombreInput = document.getElementById('nombre');
    const razonSocialInput = document.getElementById('razon_social');

    function toggleFields() {
        const selectedValue = tipoPersonaSelect.value;
        
        if (selectedValue === 'Fisica') {
            nombreContainer.classList.remove('hidden');
            razonSocialContainer.classList.add('hidden');
            nombreInput.required = true;
            razonSocialInput.required = false;
            razonSocialInput.value = '';
        } else if (selectedValue === 'Moral') {
            nombreContainer.classList.add('hidden');
            razonSocialContainer.classList.remove('hidden');
            nombreInput.required = false;
            razonSocialInput.required = true;
            nombreInput.value = '';
        } else {
            nombreContainer.classList.add('hidden');
            razonSocialContainer.classList.add('hidden');
            nombreInput.required = false;
            razonSocialInput.required = false;
        }
    }

    tipoPersonaSelect.addEventListener('change', toggleFields);
    toggleFields(); // Ejecutar al cargar para manejar valores iniciales
});
</script>
@endsection 