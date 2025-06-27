@extends('layouts.app')

@section('content')
<div class="min-h-screen w-full overflow-x-hidden">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-3xl mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <form method="POST" action="{{ route('documentos.store') }}" class="divide-y divide-gray-100">
                    @csrf

                    <!-- Encabezado -->
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-xl p-3 shadow-lg mb-3">
                                <i class="fas fa-file-alt text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent mb-2">
                                Crear Nuevo Documento
                            </h2>
                            <p class="text-sm text-gray-600">Complete el formulario para registrar un nuevo documento</p>
                        </div>
                    </div>

                    <!-- Información del Documento -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-6">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                                Información del Documento
                            </h3>
                            <div class="w-32 h-0.5 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="w-full max-w-lg mx-auto space-y-5">
                            <!-- Nombre del Documento -->
                            <div>
                                <label for="nombre" class="block text-xs font-medium text-gray-500 mb-1">
                                    Nombre del Documento <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-file-signature text-base"></i>
                                    </span>
                                    <input type="text"
                                           id="nombre"
                                           name="nombre"
                                           value="{{ old('nombre') }}"
                                           class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('nombre') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                           placeholder="Ej: Acta Constitutiva"
                                           required>
                                </div>
                                @error('nombre')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Tipo de Persona -->
                            <div>
                                <label for="tipo_persona" class="block text-xs font-medium text-gray-500 mb-1">
                                    Tipo de Persona <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-user-tag text-base"></i>
                                    </span>
                                    <select name="tipo_persona"
                                            id="tipo_persona"
                                            class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('tipo_persona') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                            required>
                                        <option value="" disabled {{ !old('tipo_persona') ? 'selected' : '' }}>Seleccione una opción</option>
                                        <option value="Física" {{ old('tipo_persona') == 'Física' ? 'selected' : '' }}>Persona Física</option>
                                        <option value="Moral" {{ old('tipo_persona') == 'Moral' ? 'selected' : '' }}>Persona Moral</option>
                                        <option value="Ambas" {{ old('tipo_persona') == 'Ambas' ? 'selected' : '' }}>Ambos Tipos</option>
                                    </select>
                                </div>
                                @error('tipo_persona')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Descripción -->
                            <div>
                                <label for="descripcion" class="block text-xs font-medium text-gray-500 mb-1">
                                    Descripción <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute top-3 left-0 flex items-start pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-align-left text-base"></i>
                                    </span>
                                    <textarea name="descripcion"
                                              id="descripcion"
                                              rows="3"
                                              class="w-full pl-10 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 resize-none @error('descripcion') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                              placeholder="Describe el documento...">{{ old('descripcion') }}</textarea>
                                </div>
                                @error('descripcion')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <!-- Secciones -->
                            <div class="form-group">
                                <div class="relative group">
                                    <label class="block text-sm font-medium text-gray-600 mb-3 group-hover:text-[#9d2449] transition-colors duration-300">
                                        <i class="fas fa-list-ul text-[#9d2449] mr-2"></i>
                                        Secciones donde se revisa
                                    </label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-xl hover:border-[#9d2449]/50 transition-all duration-300">
                                        @foreach($secciones as $seccion)
                                        <label class="inline-flex items-center p-3 rounded-lg border border-gray-200 bg-white/70 hover:border-[#9d2449]/50 hover:bg-[#9d2449]/5 transition-all duration-300 cursor-pointer group/item">
                                            <input type="checkbox" 
                                                   name="secciones[]" 
                                                   value="{{ $seccion->id }}"
                                                   {{ in_array($seccion->id, old('secciones', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-[#9d2449] focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/10 focus:ring-offset-0">
                                            <span class="ml-3 text-sm text-gray-700 group-hover/item:text-gray-900 transition-colors duration-300">
                                                {{ $seccion->nombre }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                    @error('secciones')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Visibilidad -->
                            <div class="form-group">
                                <div class="relative">
                                    <label class="inline-flex items-center w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 bg-white/50 backdrop-blur-sm hover:border-[#9d2449]/50 transition-all duration-300 group cursor-pointer">
                                        <div class="flex items-center gap-3 flex-grow">
                                            <i class="fas fa-eye text-[#9d2449] text-lg group-hover:scale-110 transition-transform duration-300"></i>
                                            <div>
                                                <span class="text-sm font-medium text-gray-900 block leading-none mb-0.5">Visibilidad del Documento</span>
                                                <span class="text-xs text-gray-500 block leading-none">Visible para usuarios autorizados</span>
                                            </div>
                                        </div>
                                        <div class="relative">
                                            <input type="checkbox" 
                                                   name="es_visible" 
                                                   id="es_visible" 
                                                   value="1"
                                                   {{ old('es_visible', true) ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-[#9d2449]/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#9d2449] group-hover:after:scale-95"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="p-4">
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('documentos.index') }}" 
                               class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-300">
                                <i class="fas fa-times mr-2 text-gray-400 group-hover:text-gray-600"></i>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900">Cancelar</span>
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg">
                                <i class="fas fa-save mr-2 text-white/90 group-hover:text-white"></i>
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Crear Documento</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 