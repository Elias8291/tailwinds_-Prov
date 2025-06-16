@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                            <i class="fas fa-calendar-times text-2xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-[#B4325E]">Registrar Día Inhábil</h2>
                            <p class="text-sm text-gray-500">Ingresa los detalles del día inhábil</p>
                        </div>
                    </div>
                    <a href="{{ route('citas.index') }}" class="inline-flex items-center text-gray-500 hover:text-[#B4325E]">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <form action="{{ route('dias-inhabiles.store') }}" method="POST" class="p-6 space-y-6 bg-white">
                @csrf

                <!-- Fecha -->
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha <span class="text-[#B4325E]">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <input type="date" 
                               name="fecha" 
                               id="fecha" 
                               required
                               class="w-full pl-10 pr-4 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 focus:outline-none transition-all duration-300 hover:border-[#B4325E]/50 @error('fecha') border-red-300 hover:border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
                               value="{{ old('fecha') }}">
                    </div>
                    @error('fecha')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción <span class="text-[#B4325E]">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <input type="text" 
                               name="descripcion" 
                               id="descripcion" 
                               required
                               class="w-full pl-10 pr-4 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-gray-800 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 focus:outline-none transition-all duration-300 hover:border-[#B4325E]/50 @error('descripcion') border-red-300 hover:border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
                               placeholder="Ej: Día festivo, Mantenimiento, etc."
                               value="{{ old('descripcion') }}">
                    </div>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('citas.index') }}" 
                       class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-xl hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300 shadow-md hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 