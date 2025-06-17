@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

<div class="min-h-screen font-montserrat py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-[#9d2449]">Editar Cita</h2>
                        <p class="text-gray-600 text-sm">Modifique los datos de la cita</p>
                    </div>
                    <a href="{{ route('citas.index') }}" class="inline-flex items-center text-gray-500 hover:text-[#9d2449]">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver
                    </a>
                </div>
            </div>

            <!-- Form Section -->
            <form action="{{ route('citas.update', $cita) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Fecha y Hora -->
                <div>
                    <label for="fecha_hora" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha y Hora <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="fecha_hora" id="fecha_hora" 
                           class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 @error('fecha_hora') border-red-500 @enderror"
                           value="{{ old('fecha_hora', $cita->fecha_hora->format('Y-m-d\TH:i')) }}"
                           min="{{ now()->format('Y-m-d\TH:i') }}">
                    @error('fecha_hora')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Motivo -->
                <div>
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo de la Cita <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="motivo" id="motivo" 
                           class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 @error('motivo') border-red-500 @enderror"
                           value="{{ old('motivo', $cita->motivo) }}"
                           placeholder="Ej: Revisión de documentos">
                    @error('motivo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="estado" id="estado" 
                            class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 @error('estado') border-red-500 @enderror">
                        <option value="pendiente" {{ old('estado', $cita->estado) === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmada" {{ old('estado', $cita->estado) === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="cancelada" {{ old('estado', $cita->estado) === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        <option value="completada" {{ old('estado', $cita->estado) === 'completada' ? 'selected' : '' }}>Completada</option>
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notas -->
                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-1">
                        Notas Adicionales
                    </label>
                    <textarea name="notas" id="notas" rows="4" 
                              class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 @error('notas') border-red-500 @enderror"
                              placeholder="Agregue cualquier información adicional relevante">{{ old('notas', $cita->notas) }}</textarea>
                    @error('notas')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex items-center bg-[#9d2449] text-white px-6 py-2 rounded-xl shadow-lg hover:bg-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-[#9d2449]/20">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 