@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-xl p-3 shadow-md">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                            Crear Nuevo Documento
                        </h2>
                        <p class="text-sm text-gray-500">Ingresa los detalles del nuevo documento</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <form action="{{ route('documentos.store') }}" method="POST" class="p-4 sm:p-6 space-y-6">
                @csrf

                <!-- Nombre -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">
                        Nombre del Documento
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               name="nombre" 
                               id="nombre" 
                               value="{{ old('nombre') }}"
                               class="shadow-sm focus:ring-[#9d2449] focus:border-[#9d2449] block w-full sm:text-sm border-gray-300 rounded-lg @error('nombre') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                               required>
                        @error('nombre')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tipo Persona -->
                <div>
                    <label for="tipo_persona" class="block text-sm font-medium text-gray-700">
                        Tipo de Persona
                    </label>
                    <div class="mt-1">
                        <select name="tipo_persona" 
                                id="tipo_persona"
                                class="shadow-sm focus:ring-[#9d2449] focus:border-[#9d2449] block w-full sm:text-sm border-gray-300 rounded-lg @error('tipo_persona') border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                required>
                            <option value="">Selecciona un tipo</option>
                            <option value="Física" {{ old('tipo_persona') == 'Física' ? 'selected' : '' }}>Física</option>
                            <option value="Moral" {{ old('tipo_persona') == 'Moral' ? 'selected' : '' }}>Moral</option>
                            <option value="Ambas" {{ old('tipo_persona') == 'Ambas' ? 'selected' : '' }}>Ambas</option>
                        </select>
                        @error('tipo_persona')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700">
                        Descripción
                    </label>
                    <div class="mt-1">
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  rows="4"
                                  class="shadow-sm focus:ring-[#9d2449] focus:border-[#9d2449] block w-full sm:text-sm border-gray-300 rounded-lg @error('descripcion') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Visibilidad -->
                <div class="flex items-start sm:items-center">
                    <div class="flex items-center h-5">
                        <input type="checkbox" 
                               name="es_visible" 
                               id="es_visible" 
                               value="1"
                               {{ old('es_visible', true) ? 'checked' : '' }}
                               class="focus:ring-[#9d2449] h-4 w-4 text-[#9d2449] border-gray-300 rounded">
                    </div>
                    <div class="ml-3">
                        <label for="es_visible" class="font-medium text-gray-700">Visible</label>
                        <p class="text-sm text-gray-500">El documento será visible para todos los usuarios</p>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('documentos.index') }}" 
                       class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transform hover:scale-105 transition-all duration-300">
                        Crear Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 