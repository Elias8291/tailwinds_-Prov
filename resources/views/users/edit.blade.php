@extends('layouts.app')

@section('content')
<style>
.input-floating {
    position: relative;
    width: 100%;
}

.input-floating input {
    width: 100%;
    height: 3rem;
    padding: 1rem 3rem 0.5rem 3rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(4px);
    font-size: 1rem;
    transition: all 0.3s ease;
    outline: none;
}

.input-floating input:focus {
    border-color: #9d2449;
    box-shadow: 0 0 0 3px rgba(157, 36, 73, 0.1);
}

.input-floating input.error {
    border-color: #ef4444;
}

.input-floating .input-floating-label {
    position: absolute;
    left: 3rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    color: #6b7280;
    transition: all 0.3s ease;
    pointer-events: none;
    background: white;
    padding: 0 0.5rem;
}

.input-floating input:focus + .input-floating-label,
.input-floating input:not(:placeholder-shown) + .input-floating-label {
    top: 0;
    transform: translateY(-50%);
    font-size: 0.875rem;
    color: #9d2449;
}

.input-floating .input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    transition: color 0.3s ease;
    pointer-events: none;
}

.input-floating input:focus ~ .input-icon {
    color: #9d2449;
}

.input-floating .toggle-password {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    cursor: pointer;
    transition: color 0.3s ease;
}

.input-floating .toggle-password:hover {
    color: #9d2449;
}
</style>
<div class="min-h-screen w-full overflow-x-hidden">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-3xl mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <form method="POST" action="{{ route('users.update', $user) }}" class="divide-y divide-gray-100">
                    @csrf
                    @method('PUT')

                    <!-- Encabezado -->
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center relative">
                            <!-- Flecha de regresar -->
                            <a href="{{ route('users.index') }}" 
                               class="absolute left-0 top-0 inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 transition-all duration-300 group">
                                <i class="fas fa-arrow-left text-sm group-hover:translate-x-[-2px] transition-transform duration-300"></i>
                                <span class="text-sm font-medium">Regresar</span>
                            </a>
                            
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-xl p-3 shadow-lg mb-3">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent mb-2">
                                Editar Usuario
                            </h2>
                            <p class="text-sm text-gray-600">Modifique la información del usuario</p>
                        </div>
                    </div>

                    <!-- Información del Usuario -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-6">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                                Información Personal
                            </h3>
                            <div class="w-32 h-0.5 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="w-full max-w-lg mx-auto space-y-5">
                            <!-- Nombre -->
                            <div>
                                <label for="name" class="block text-xs font-medium text-gray-500 mb-1">
                                    Nombre <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-user text-base"></i>
                                    </span>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $user->nombre) }}"
                                           autocomplete="off"
                                           placeholder="Ej: Juan Pérez"
                                           class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('name') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                           required>
                                </div>
                                @error('name')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-xs font-medium text-gray-500 mb-1">
                                    Correo Electrónico <span class="text-[#9d2449]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-envelope text-base"></i>
                                    </span>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $user->correo) }}"
                                           autocomplete="off"
                                           placeholder="Ej: correo@ejemplo.com"
                                           class="w-full h-11 pl-10 pr-3 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('email') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                                           required>
                                </div>
                                @error('email')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Contraseña -->
                            <div>
                                <label for="password" class="block text-xs font-medium text-gray-500 mb-1">
                                    Nueva Contraseña
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-lock text-base"></i>
                                    </span>
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           autocomplete="new-password"
                                           placeholder="Dejar en blanco para mantener la actual"
                                           class="w-full h-11 pl-10 pr-10 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 @error('password') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-[#9d2449]" onclick="togglePassword('password')">
                                        <i id="password-icon" class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400">Dejar en blanco para mantener la actual</span>
                                @error('password')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <!-- Confirmar Contraseña -->
                            <div>
                                <label for="password_confirmation" class="block text-xs font-medium text-gray-500 mb-1">
                                    Confirmar Contraseña
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-300">
                                        <i class="fas fa-lock text-base"></i>
                                    </span>
                                    <input type="password"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           autocomplete="new-password"
                                           placeholder="Vuelve a escribir la contraseña nueva"
                                           class="w-full h-11 pl-10 pr-10 bg-white border border-gray-200 rounded-lg text-gray-800 shadow-sm focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-[#9d2449]" onclick="togglePassword('password_confirmation')">
                                        <i id="password_confirmation-icon" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-4">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                                Roles del Usuario
                            </h3>
                            <div class="w-20 h-0.5 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mt-2 rounded-full opacity-50"></div>
                        </div>
                        
                        <div class="flex flex-wrap justify-center gap-2 w-full">
                            @foreach($roles as $role)
                            <label class="group relative w-[120px] flex-shrink-0">
                                <input type="radio" 
                                       name="roles[]" 
                                       value="{{ $role->id }}"
                                       class="peer absolute opacity-0"
                                       {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <div class="relative h-full flex flex-col items-center p-2 rounded-lg border-2 border-gray-200 bg-white cursor-pointer transition-all duration-300
                                            peer-checked:border-[#9d2449] peer-checked:bg-[#9d2449]/5 hover:border-[#9d2449]/70 hover:shadow-md group">
                                    
                                    <!-- Palomita de selección -->
                                    <div class="absolute -top-2 -right-2 transform scale-0 peer-checked:scale-100 transition-transform duration-300">
                                        <div class="w-5 h-5 rounded-full border-2 border-[#9d2449] bg-white flex items-center justify-center">
                                            <i class="fas fa-check text-[10px] text-[#9d2449]"></i>
                                        </div>
                                    </div>

                                    <!-- Icono del rol -->
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 
                                                group-hover:bg-[#9d2449]/10 peer-checked:bg-[#9d2449]/20 
                                                transition-colors duration-300 mb-2">
                                        @switch($role->name)
                                            @case('admin')
                                                <i class="fas fa-user-shield text-sm text-gray-400 
                                                          group-hover:text-[#9d2449] peer-checked:text-[#9d2449] 
                                                          transition-colors duration-300"></i>
                                                @break
                                            @case('revisor')
                                                <i class="fas fa-user-tie text-sm text-gray-400 
                                                          group-hover:text-[#9d2449] peer-checked:text-[#9d2449] 
                                                          transition-colors duration-300"></i>
                                                @break
                                            @case('solicitante')
                                                <i class="fas fa-user-edit text-sm text-gray-400 
                                                          group-hover:text-[#9d2449] peer-checked:text-[#9d2449] 
                                                          transition-colors duration-300"></i>
                                                @break
                                            @case('proveedor')
                                                <i class="fas fa-truck text-sm text-gray-400 
                                                          group-hover:text-[#9d2449] peer-checked:text-[#9d2449] 
                                                          transition-colors duration-300"></i>
                                                @break
                                            @default
                                                <i class="fas fa-user text-sm text-gray-400 
                                                          group-hover:text-[#9d2449] peer-checked:text-[#9d2449] 
                                                          transition-colors duration-300"></i>
                                        @endswitch
                                    </div>

                                    <!-- Texto -->
                                    <div class="text-center">
                                        <p class="text-xs font-medium text-gray-700 
                                                  group-hover:text-[#9d2449] peer-checked:text-[#9d2449] 
                                                  transition-colors duration-300">
                                            {{ ucfirst($role->name) }}
                                        </p>
                                        <p class="text-[10px] text-gray-500 
                                                  group-hover:text-[#9d2449]/80 peer-checked:text-[#9d2449]/90
                                                  leading-tight">
                                            @switch($role->name)
                                                @case('admin')
                                                    Acceso total
                                                    @break
                                                @case('revisor')
                                                    Revisa solicitudes
                                                    @break
                                                @case('solicitante')
                                                    Crea solicitudes
                                                    @break
                                                @case('proveedor')
                                                    Proveedor
                                                    @break
                                                @default
                                                    Usuario
                                            @endswitch
                                        </p>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="p-4">
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('users.index') }}" 
                               class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-300">
                                <i class="fas fa-times mr-2 text-gray-400 group-hover:text-gray-600"></i>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900">Cancelar</span>
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg">
                                <i class="fas fa-save mr-2 text-white/90 group-hover:text-white"></i>
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Guardar Cambios</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
// Función para alternar visibilidad de contraseña
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Ejecutar cuando la página se carga
document.addEventListener('DOMContentLoaded', function() {
    // Los floating labels funcionan automáticamente con CSS
});
</script> 