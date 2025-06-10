@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8 bg-gradient-to-br from-gray-50 via-white to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado con diseño mejorado -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl p-8 mb-8 border border-gray-100/50 transform hover:shadow-2xl transition-all duration-300 relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#B4325E]/5 to-transparent"></div>
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-gradient-to-br from-[#B4325E]/10 to-transparent rounded-full blur-3xl"></div>
            
            <div class="relative flex items-center gap-8">
                <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-2xl p-4 shadow-xl ring-4 ring-[#B4325E]/10 transform hover:scale-105 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent mb-2">
                        Registrar Nuevo Proveedor
                    </h2>
                    <p class="text-gray-600 text-lg">Complete la información del nuevo proveedor en el sistema</p>
                </div>
            </div>
        </div>

        <!-- Formulario con diseño mejorado -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl border border-gray-100/50 overflow-hidden relative">
            <!-- Decorative elements -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#B4325E]/5 to-transparent"></div>
            <div class="absolute -right-40 -bottom-40 w-96 h-96 bg-gradient-to-br from-[#B4325E]/10 to-transparent rounded-full blur-3xl"></div>
            
            <form method="POST" action="{{ route('proveedores.store') }}" class="relative p-8 space-y-8">
                @csrf

                <!-- Información del Solicitante -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-6 border-2 border-gray-100 shadow-lg group hover:border-[#B4325E]/40 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#B4325E]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 group-hover:text-[#B4325E] transition-colors">Información del Solicitante</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- RFC -->
                            <div class="relative group">
                                <label for="rfc" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#B4325E] transition-colors">RFC</label>
                                <div class="relative">
                                    <input type="text" 
                                           name="rfc" 
                                           id="rfc" 
                                           value="{{ old('rfc') }}"
                                           class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#B4325E] focus:ring-0 transition-all duration-300 outline-none hover:border-[#B4325E]/50 placeholder-transparent"
                                           placeholder="Ingrese el RFC"
                                           required>
                                    <label class="absolute left-5 -top-2.5 bg-white px-2 text-sm text-gray-500 transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#B4325E]">
                                        Ingrese el RFC
                                    </label>
                                </div>
                                @error('rfc')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipo de Persona -->
                            <div class="relative group">
                                <label for="tipo_persona" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#B4325E] transition-colors">Tipo de Persona</label>
                                <div class="relative">
                                    <select name="tipo_persona" 
                                            id="tipo_persona"
                                            class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#B4325E] focus:ring-0 transition-all duration-300 outline-none hover:border-[#B4325E]/50 appearance-none cursor-pointer"
                                            required>
                                        <option value="">Seleccione una opción</option>
                                        <option value="Fisica" {{ old('tipo_persona') == 'Fisica' ? 'selected' : '' }}>Persona Física</option>
                                        <option value="Moral" {{ old('tipo_persona') == 'Moral' ? 'selected' : '' }}>Persona Moral</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <i class="fas fa-chevron-down text-lg text-gray-400 group-hover:text-[#B4325E] transition-colors"></i>
                                    </div>
                                </div>
                                @error('tipo_persona')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nombre -->
                            <div id="nombre_container" class="relative group hidden">
                                <label for="nombre" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#B4325E] transition-colors">Nombre Completo</label>
                                <div class="relative">
                                    <input type="text" 
                                           name="nombre" 
                                           id="nombre" 
                                           value="{{ old('nombre') }}"
                                           class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#B4325E] focus:ring-0 transition-all duration-300 outline-none hover:border-[#B4325E]/50 placeholder-transparent"
                                           placeholder="Ingrese el nombre completo">
                                    <label class="absolute left-5 -top-2.5 bg-white px-2 text-sm text-gray-500 transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#B4325E]">
                                        Ingrese el nombre completo
                                    </label>
                                </div>
                                @error('nombre')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Razón Social -->
                            <div id="razon_social_container" class="relative group hidden">
                                <label for="razon_social" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#B4325E] transition-colors">Razón Social</label>
                                <div class="relative">
                                    <input type="text" 
                                           name="razon_social" 
                                           id="razon_social" 
                                           value="{{ old('razon_social') }}"
                                           class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#B4325E] focus:ring-0 transition-all duration-300 outline-none hover:border-[#B4325E]/50 placeholder-transparent"
                                           placeholder="Ingrese la razón social">
                                    <label class="absolute left-5 -top-2.5 bg-white px-2 text-sm text-gray-500 transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#B4325E]">
                                        Ingrese la razón social
                                    </label>
                                </div>
                                @error('razon_social')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Proveedor -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-6 border-2 border-gray-100 shadow-lg group hover:border-[#B4325E]/40 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#B4325E]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 group-hover:text-[#B4325E] transition-colors">Información del Proveedor</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- PV -->
                            <div class="relative group">
                                <label for="pv" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#B4325E] transition-colors">PV</label>
                                <div class="relative">
                                    <input type="text" 
                                           name="pv" 
                                           id="pv" 
                                           value="{{ old('pv') }}"
                                           class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#B4325E] focus:ring-0 transition-all duration-300 outline-none hover:border-[#B4325E]/50 placeholder-transparent"
                                           placeholder="Ingrese el PV"
                                           required>
                                    <label class="absolute left-5 -top-2.5 bg-white px-2 text-sm text-gray-500 transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#B4325E]">
                                        Ingrese el PV
                                    </label>
                                </div>
                                @error('pv')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div class="relative group">
                                <label for="estado" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#B4325E] transition-colors">Estado</label>
                                <div class="relative">
                                    <select name="estado" 
                                            id="estado"
                                            class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#B4325E] focus:ring-0 transition-all duration-300 outline-none hover:border-[#B4325E]/50 appearance-none cursor-pointer"
                                            required>
                                        <option value="">Seleccione una opción</option>
                                        <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                        <option value="Pendiente Renovacion" {{ old('estado') == 'Pendiente Renovacion' ? 'selected' : '' }}>Pendiente Renovación</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <i class="fas fa-chevron-down text-lg text-gray-400 group-hover:text-[#B4325E] transition-colors"></i>
                                    </div>
                                </div>
                                @error('estado')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha de Registro -->
                            <div class="relative group">
                                <label for="fecha_registro" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#B4325E] transition-colors">Fecha de Registro</label>
                                <div class="relative">
                                    <input type="date" 
                                           name="fecha_registro" 
                                           id="fecha_registro" 
                                           value="{{ old('fecha_registro') }}"
                                           class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#B4325E] focus:ring-0 transition-all duration-300 outline-none hover:border-[#B4325E]/50"
                                           required>
                                </div>
                                @error('fecha_registro')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha de Vencimiento -->
                            <div class="relative group">
                                <label for="fecha_vencimiento" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#B4325E] transition-colors">Fecha de Vencimiento</label>
                                <div class="relative">
                                    <input type="date" 
                                           name="fecha_vencimiento" 
                                           id="fecha_vencimiento" 
                                           value="{{ old('fecha_vencimiento') }}"
                                           class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#B4325E] focus:ring-0 transition-all duration-300 outline-none hover:border-[#B4325E]/50"
                                           required>
                                </div>
                                @error('fecha_vencimiento')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Observaciones -->
                            <div class="md:col-span-2 relative group">
                                <label for="observaciones" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#B4325E] transition-colors">Observaciones</label>
                                <div class="relative">
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              rows="4"
                                              class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#B4325E] focus:ring-0 transition-all duration-300 outline-none hover:border-[#B4325E]/50 placeholder-transparent resize-none"
                                              placeholder="Ingrese las observaciones">{{ old('observaciones') }}</textarea>
                                    <label class="absolute left-5 -top-2.5 bg-white px-2 text-sm text-gray-500 transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#B4325E]">
                                        Ingrese las observaciones
                                    </label>
                                </div>
                                @error('observaciones')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones con diseño mejorado -->
                <div class="flex flex-col sm:flex-row justify-end items-center gap-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('proveedores.index') }}" 
                       class="group relative w-full sm:w-auto px-8 py-4 border-0 rounded-2xl text-base font-medium text-gray-700 bg-gray-100 overflow-hidden transition-all duration-300 hover:text-white hover:shadow-lg">
                        <div class="relative z-10 flex items-center justify-center gap-2">
                            <i class="fas fa-arrow-left transition-transform duration-300 group-hover:-translate-x-1"></i>
                            <span>Cancelar</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-700 to-gray-800 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    </a>
                    <button type="submit"
                            class="group relative w-full sm:w-auto px-8 py-4 border-0 rounded-2xl text-base font-medium text-white overflow-hidden transition-all duration-300 hover:shadow-lg">
                        <div class="relative z-10 flex items-center justify-center gap-2">
                            <i class="fas fa-save transition-transform duration-300 group-hover:scale-110"></i>
                            <span>Registrar Proveedor</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-[#B4325E] to-[#93264B]"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-[#93264B] to-[#B4325E] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
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