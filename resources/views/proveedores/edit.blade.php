@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                    <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                        Editar Proveedor
                    </h2>
                    <p class="text-sm text-gray-500">Modifica los datos del proveedor</p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-gray-100">
            <form method="POST" action="{{ route('proveedores.update', $proveedor->pv) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- PV -->
                <div>
                    <label for="pv" class="block text-sm font-medium text-gray-700">
                        Número de Proveedor (PV)
                    </label>
                    <input type="text" name="pv" id="pv" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200 bg-gray-50"
                           required maxlength="10" value="{{ old('pv', $proveedor->pv) }}" readonly>
                    @error('pv')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Registro -->
                <div>
                    <label for="fecha_registro" class="block text-sm font-medium text-gray-700">
                        Fecha de Registro
                    </label>
                    <input type="date" name="fecha_registro" id="fecha_registro" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200"
                           required value="{{ old('fecha_registro', $proveedor->fecha_registro) }}">
                    @error('fecha_registro')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Vencimiento -->
                <div>
                    <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700">
                        Fecha de Vencimiento
                    </label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200"
                           required value="{{ old('fecha_vencimiento', $proveedor->fecha_vencimiento) }}">
                    @error('fecha_vencimiento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">
                        Estado
                    </label>
                    <select name="estado" id="estado" 
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200"
                            required>
                        <option value="Activo" {{ old('estado', $proveedor->estado) == 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ old('estado', $proveedor->estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                        <option value="Pendiente Renovacion" {{ old('estado', $proveedor->estado) == 'Pendiente Renovacion' ? 'selected' : '' }}>Pendiente Renovación</option>
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Observaciones -->
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700">
                        Observaciones
                    </label>
                    <textarea name="observaciones" id="observaciones" rows="4"
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200">{{ old('observaciones', $proveedor->observaciones) }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3 pt-6">
                    <a href="{{ route('proveedores.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transition-colors duration-200">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transition-colors duration-200">
                        Actualizar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 