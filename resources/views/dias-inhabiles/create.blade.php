@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                    <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                        Registrar Día Inhábil
                    </h2>
                    <p class="text-sm text-gray-500">Ingresa los detalles del día inhábil</p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <form action="{{ route('dias-inhabiles.store') }}" method="POST" class="p-6">
                @csrf

                <div class="space-y-6">
                    <!-- Fecha -->
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700">
                            Fecha
                        </label>
                        <div class="mt-1">
                            <input type="date" 
                                   name="fecha" 
                                   id="fecha" 
                                   required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]"
                                   value="{{ old('fecha') }}">
                        </div>
                        @error('fecha')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">
                            Descripción
                        </label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="descripcion" 
                                   id="descripcion" 
                                   required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]"
                                   value="{{ old('descripcion') }}"
                                   placeholder="Ej: Día festivo, Mantenimiento, etc.">
                        </div>
                        @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('citas.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E]">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E]">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 