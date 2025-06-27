@extends('layouts.app')

@section('content')
<div class="min-h-screen w-full overflow-x-hidden">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-lg mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <form action="{{ route('dias-inhabiles.store') }}" method="POST" class="divide-y divide-gray-100">
                    @csrf

                    <!-- Encabezado -->
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-xl p-3 shadow-lg mb-3">
                                <i class="fas fa-calendar-times text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent mb-2">
                                Registrar Día Inhábil
                            </h2>
                            <p class="text-sm text-gray-600">Agrega un nuevo día inhábil al calendario</p>
                        </div>
                    </div>

                    <!-- Información del Día Inhábil -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-6">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                                Información del Día Inhábil
                            </h3>
                            <div class="w-32 h-0.5 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mt-2 rounded-full opacity-50"></div>
                        </div>
                        <div class="w-full max-w-lg mx-auto space-y-5">
                            <!-- Campo de Fecha -->
                            <div>
                                <label for="fecha_inicio" class="block text-xs font-medium text-gray-500 mb-1">
                                    Fecha <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-calendar-alt text-base"></i>
                                    </span>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" required
                                           class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('fecha_inicio') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                           value="{{ old('fecha_inicio') }}"
                                           placeholder="Ej: 2024-07-01">
                                </div>
                                @error('fecha_inicio')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Campo de Descripción -->
                            <div>
                                <label for="descripcion" class="block text-xs font-medium text-gray-500 mb-1">
                                    Descripción <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute top-3 left-0 flex items-start pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-align-left text-base"></i>
                                    </span>
                                    <input type="text" name="descripcion" id="descripcion" required
                                           class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('descripcion') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                           placeholder="Ej: Día festivo, Asueto administrativo, etc."
                                           value="{{ old('descripcion') }}">
                                </div>
                                @error('descripcion')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="p-4">
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('citas.index') }}" 
                               class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-300">
                                <i class="fas fa-times mr-2 text-gray-400 group-hover:text-gray-600"></i>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900">Cancelar</span>
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg">
                                <i class="fas fa-save mr-2 text-white/90 group-hover:text-white"></i>
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Guardar Día Inhábil</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 