@extends('layouts.app')

@section('content')
<div class="min-h-screen w-full overflow-x-hidden">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-3xl mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <form method="POST" action="{{ route('proveedores.update', $proveedor->pv) }}" class="divide-y divide-gray-100">
                    @csrf
                    @method('PUT')

                    <!-- Encabezado -->
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-xl p-3 shadow-lg mb-3">
                                <i class="fas fa-user-edit text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent mb-2">
                                Editar Proveedor
                            </h2>
                            <p class="text-sm text-gray-600">Modifique la información del proveedor</p>
                        </div>
                    </div>

                    <!-- Información del Solicitante -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-6">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                                Información del Solicitante
                            </h3>
                            <div class="w-32 h-0.5 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="w-full max-w-lg mx-auto space-y-5">
                            <!-- RFC -->
                            <div>
                                <label for="rfc" class="block text-xs font-medium text-gray-500 mb-1">
                                    RFC <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-fingerprint text-base"></i>
                                    </span>
                                    <input type="text"
                                           id="rfc"
                                           name="rfc"
                                           value="{{ old('rfc', $proveedor->solicitante->rfc) }}"
                                           placeholder="Ej: XAXX010101000"
                                           class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('rfc') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                           required>
                                </div>
                                @error('rfc')
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
                                            class="w-full h-11 pl-10 pr-10 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 appearance-none @error('tipo_persona') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                            required>
                                        <option value="" disabled>Seleccione una opción</option>
                                        <option value="Fisica" {{ old('tipo_persona', $proveedor->solicitante->tipo_persona) == 'Fisica' ? 'selected' : '' }}>Persona Física</option>
                                        <option value="Moral" {{ old('tipo_persona', $proveedor->solicitante->tipo_persona) == 'Moral' ? 'selected' : '' }}>Persona Moral</option>
                                    </select>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                                @error('tipo_persona')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Nombre -->
                            <div id="nombre_container" class="{{ old('tipo_persona', $proveedor->solicitante->tipo_persona) == 'Fisica' ? '' : 'hidden' }}">
                                <label for="nombre" class="block text-xs font-medium text-gray-500 mb-1">
                                    Nombre Completo <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-user text-base"></i>
                                    </span>
                                    <input type="text"
                                           id="nombre"
                                           name="nombre"
                                           value="{{ old('nombre', $proveedor->solicitante->nombre) }}"
                                           placeholder="Ej: Juan Pérez"
                                           class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('nombre') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror">
                                </div>
                                @error('nombre')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Razón Social -->
                            <div id="razon_social_container" class="{{ old('tipo_persona', $proveedor->solicitante->tipo_persona) == 'Moral' ? '' : 'hidden' }}">
                                <label for="razon_social" class="block text-xs font-medium text-gray-500 mb-1">
                                    Razón Social <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-building text-base"></i>
                                    </span>
                                    <input type="text"
                                           id="razon_social"
                                           name="razon_social"
                                           value="{{ old('razon_social', $proveedor->solicitante->razon_social) }}"
                                           placeholder="Ej: Empresa S.A. de C.V."
                                           class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('razon_social') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror">
                                </div>
                                @error('razon_social')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Información del Proveedor -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-6">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                                Información del Proveedor
                            </h3>
                            <div class="w-32 h-0.5 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="w-full max-w-lg mx-auto space-y-5">
                            <!-- PV -->
                            <div>
                                <label for="pv" class="block text-xs font-medium text-gray-500 mb-1">
                                    PV <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-id-badge text-base"></i>
                                    </span>
                                    <input type="text"
                                           id="pv"
                                           value="{{ $proveedor->pv }}"
                                           class="w-full h-11 pl-10 pr-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-800 shadow cursor-not-allowed"
                                           disabled>
                                </div>
                            </div>
                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-xs font-medium text-gray-500 mb-1">
                                    Estado <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-flag text-base"></i>
                                    </span>
                                    <select name="estado"
                                            id="estado"
                                            class="w-full h-11 pl-10 pr-10 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 appearance-none @error('estado') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                            required>
                                        <option value="" disabled>Seleccione una opción</option>
                                        <option value="Activo" {{ old('estado', $proveedor->estado) == 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="Inactivo" {{ old('estado', $proveedor->estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                        <option value="Pendiente Renovacion" {{ old('estado', $proveedor->estado) == 'Pendiente Renovacion' ? 'selected' : '' }}>Pendiente Renovación</option>
                                    </select>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                                @error('estado')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Fechas -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Fecha de Registro -->
                                <div>
                                    <label for="fecha_registro" class="block text-xs font-medium text-gray-500 mb-1">
                                        Fecha de Registro <span class="text-[#9d2449]">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                            <i class="fas fa-calendar-plus text-base"></i>
                                        </span>
                                        <input type="date"
                                               id="fecha_registro"
                                               name="fecha_registro"
                                               value="{{ old('fecha_registro', $proveedor->fecha_registro->format('Y-m-d')) }}"
                                               class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('fecha_registro') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                               required>
                                    </div>
                                </div>
                                <!-- Fecha de Vencimiento -->
                                <div>
                                    <label for="fecha_vencimiento" class="block text-xs font-medium text-gray-500 mb-1">
                                        Fecha de Vencimiento <span class="text-[#9d2449]">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                            <i class="fas fa-calendar-times text-base"></i>
                                        </span>
                                        <input type="date"
                                               id="fecha_vencimiento"
                                               name="fecha_vencimiento"
                                               value="{{ old('fecha_vencimiento', $proveedor->fecha_vencimiento->format('Y-m-d')) }}"
                                               class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('fecha_vencimiento') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="p-4">
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('proveedores.index') }}" 
                               class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-300">
                                <i class="fas fa-times mr-2 text-gray-400 group-hover:text-gray-600"></i>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900">Cancelar</span>
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg">
                                <i class="fas fa-save mr-2 text-white/90 group-hover:text-white"></i>
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Actualizar Proveedor</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoPersonaSelect = document.getElementById('tipo_persona');
    const nombreContainer = document.getElementById('nombre_container');
    const razonSocialContainer = document.getElementById('razon_social_container');

    function toggleFields() {
        const selectedValue = tipoPersonaSelect.value;
        
        if (selectedValue === 'Fisica') {
            nombreContainer.classList.remove('hidden');
            razonSocialContainer.classList.add('hidden');
        } else if (selectedValue === 'Moral') {
            nombreContainer.classList.add('hidden');
            razonSocialContainer.classList.remove('hidden');
        } else {
            nombreContainer.classList.add('hidden');
            razonSocialContainer.classList.add('hidden');
        }
    }

    tipoPersonaSelect.addEventListener('change', toggleFields);
    toggleFields(); // Run on initial load
});
</script>
@endpush

@endsection 