@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8 bg-gradient-to-br from-gray-50 via-white to-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado con diseño mejorado -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl p-8 mb-8 border border-gray-100/50 transform hover:shadow-2xl transition-all duration-300 relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#9d2449]/5 to-transparent"></div>
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-gradient-to-br from-[#9d2449]/10 to-transparent rounded-full blur-3xl"></div>
            
            <div class="relative flex items-center gap-8">
                <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-2xl p-4 shadow-xl ring-4 ring-[#9d2449]/10 transform hover:scale-105 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent mb-2">
                        Crear Nuevo Documento
                    </h2>
                    <p class="text-gray-600 text-lg">Complete el formulario para registrar un nuevo documento en el sistema</p>
                </div>
            </div>
        </div>

        <!-- Formulario con diseño mejorado -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl border border-gray-100/50 overflow-hidden relative">
            <!-- Decorative elements -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-transparent"></div>
            <div class="absolute -right-40 -bottom-40 w-96 h-96 bg-gradient-to-br from-[#9d2449]/10 to-transparent rounded-full blur-3xl"></div>
            
            <form action="{{ route('documentos.store') }}" method="POST" class="relative p-8">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- Columna Izquierda -->
                    <div class="space-y-8">
                        <!-- Nombre del Documento -->
                        <div class="relative group">
                            <label for="nombre" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#9d2449] transition-colors">
                                Nombre del Documento
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="nombre" 
                                       id="nombre" 
                                       value="{{ old('nombre') }}"
                                       class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#9d2449] focus:ring-0 transition-all duration-300 outline-none hover:border-[#9d2449]/50 placeholder-transparent @error('nombre') border-red-300 @enderror"
                                       placeholder="Ingrese el nombre del documento"
                                       required>
                                <label class="absolute left-5 -top-2.5 bg-white px-2 text-sm text-gray-500 transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#9d2449]">
                                    Ingrese el nombre del documento
                                </label>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-hover:text-[#9d2449] transition-colors">
                                    <i class="fas fa-file-alt text-lg"></i>
                                </div>
                            </div>
                            @error('nombre')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de Persona -->
                        <div class="relative group">
                            <label for="tipo_persona" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#9d2449] transition-colors">
                                Tipo de Persona
                            </label>
                            <div class="relative">
                                <select name="tipo_persona" 
                                        id="tipo_persona"
                                        class="peer block w-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#9d2449] focus:ring-0 transition-all duration-300 outline-none hover:border-[#9d2449]/50 appearance-none cursor-pointer @error('tipo_persona') border-red-300 @enderror"
                                        required>
                                    <option value="">Seleccione una opción</option>
                                    <option value="Física" {{ old('tipo_persona') == 'Física' ? 'selected' : '' }}>Persona Física</option>
                                    <option value="Moral" {{ old('tipo_persona') == 'Moral' ? 'selected' : '' }}>Persona Moral</option>
                                    <option value="Ambas" {{ old('tipo_persona') == 'Ambas' ? 'selected' : '' }}>Ambos Tipos</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <i class="fas fa-chevron-down text-lg text-gray-400 group-hover:text-[#9d2449] transition-colors"></i>
                                </div>
                                <div class="absolute inset-0 rounded-2xl pointer-events-none border-2 border-transparent group-hover:border-[#9d2449]/10 transition-colors"></div>
                            </div>
                            @error('tipo_persona')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Visibilidad con diseño mejorado -->
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-6 border-2 border-gray-100 shadow-lg group hover:border-[#9d2449]/40 transition-all duration-300 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#9d2449]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative flex items-start gap-5">
                                <div class="flex-shrink-0 mt-1">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="es_visible" 
                                               id="es_visible" 
                                               value="1"
                                               {{ old('es_visible', true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#9d2449]"></div>
                                    </label>
                                </div>
                                <div>
                                    <label for="es_visible" class="block text-base font-semibold text-gray-900 group-hover:text-[#9d2449] transition-colors">
                                        Visibilidad del Documento
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">
                                        Al activar esta opción, el documento será visible para todos los usuarios autorizados del sistema
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="space-y-6">
                        <!-- Descripción con diseño mejorado -->
                        <div class="relative group h-full">
                            <label for="descripcion" class="block text-base font-semibold text-gray-700 mb-2 group-hover:text-[#9d2449] transition-colors">
                                Descripción del Documento
                            </label>
                            <div class="relative h-[calc(100%-2rem)]">
                                <textarea name="descripcion" 
                                          id="descripcion" 
                                          rows="8"
                                          class="peer block w-full h-full px-5 py-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-2xl focus:border-[#9d2449] focus:ring-0 transition-all duration-300 outline-none hover:border-[#9d2449]/50 resize-none placeholder-transparent @error('descripcion') border-red-300 @enderror"
                                          placeholder="Describa el propósito y contenido del documento...">{{ old('descripcion') }}</textarea>
                                <label class="absolute left-5 -top-2.5 bg-white px-2 text-sm text-gray-500 transition-all duration-300 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-[#9d2449]">
                                    Describa el propósito y contenido del documento...
                                </label>
                                <div class="absolute top-4 right-4 text-gray-400 group-hover:text-[#9d2449] transition-colors">
                                    <i class="fas fa-align-left text-lg"></i>
                                </div>
                            </div>
                            @error('descripcion')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Botones con diseño mejorado -->
                <div class="flex flex-col sm:flex-row justify-end items-center gap-4 mt-10 pt-6 border-t border-gray-100">
                    <a href="{{ route('documentos.index') }}" 
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
                            <span>Crear Documento</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-[#9d2449] to-[#8a203f]"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-[#8a203f] to-[#9d2449] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 