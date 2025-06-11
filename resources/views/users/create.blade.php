@extends('layouts.app')

@section('content')
<div class="min-h-screen w-full overflow-x-hidden">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-3xl mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <form method="POST" action="{{ route('users.store') }}" class="divide-y divide-gray-100">
                    @csrf

                    <!-- Encabezado -->
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-xl p-3 shadow-lg mb-3">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent mb-2">
                                Crear Nuevo Usuario
                            </h2>
                            <p class="text-sm text-gray-600">Complete la información para crear un nuevo usuario</p>
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

                        <div class="w-full max-w-lg mx-auto space-y-6">
                            <!-- Nombre -->
                            <div class="form-group">
                                <div class="relative group">
                                    <input type="text" 
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           class="peer w-full h-12 px-12 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-xl text-gray-800 appearance-none focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 hover:border-[#9d2449]/50 @error('name') border-red-300 hover:border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
                                           placeholder=" "
                                           required>
                                    <label for="name" 
                                           class="absolute left-11 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 
                                                  peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-12 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-11 peer-focus:bg-white 
                                                  peer-focus:text-[#9d2449] peer-focus:text-sm group-hover:text-[#9d2449]">
                                        Nombre<span class="text-[#9d2449] ml-1">*</span>
                                    </label>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 peer-focus:text-[#9d2449] group-hover:text-[#9d2449]">
                                        <i class="fas fa-user text-lg transition-colors duration-300"></i>
                                    </div>
                                    @error('name')
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                    </div>
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <div class="relative group">
                                    <input type="email" 
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           class="peer w-full h-12 px-12 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-xl text-gray-800 appearance-none focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 hover:border-[#9d2449]/50 @error('email') border-red-300 hover:border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
                                           placeholder=" "
                                           required>
                                    <label for="email" 
                                           class="absolute left-11 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 
                                                  peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-12 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-11 peer-focus:bg-white 
                                                  peer-focus:text-[#9d2449] peer-focus:text-sm group-hover:text-[#9d2449]">
                                        Correo Electrónico<span class="text-[#9d2449] ml-1">*</span>
                                    </label>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 peer-focus:text-[#9d2449] group-hover:text-[#9d2449]">
                                        <i class="fas fa-envelope text-lg transition-colors duration-300"></i>
                                    </div>
                                    @error('email')
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                    </div>
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="form-group">
                                <div class="relative group">
                                    <input type="password" 
                                           id="password"
                                           name="password"
                                           class="peer w-full h-12 px-12 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-xl text-gray-800 appearance-none focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 hover:border-[#9d2449]/50 @error('password') border-red-300 hover:border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
                                           placeholder=" "
                                           required>
                                    <label for="password" 
                                           class="absolute left-11 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 
                                                  peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-12 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-11 peer-focus:bg-white 
                                                  peer-focus:text-[#9d2449] peer-focus:text-sm group-hover:text-[#9d2449]">
                                        Contraseña<span class="text-[#9d2449] ml-1">*</span>
                                    </label>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 peer-focus:text-[#9d2449] group-hover:text-[#9d2449]">
                                        <i class="fas fa-lock text-lg transition-colors duration-300"></i>
                                    </div>
                                    @error('password')
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                    </div>
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="form-group">
                                <div class="relative group">
                                    <input type="password" 
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           class="peer w-full h-12 px-12 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-xl text-gray-800 appearance-none focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 hover:border-[#9d2449]/50"
                                           placeholder=" "
                                           required>
                                    <label for="password_confirmation" 
                                           class="absolute left-11 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 
                                                  peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-12 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-11 peer-focus:bg-white 
                                                  peer-focus:text-[#9d2449] peer-focus:text-sm group-hover:text-[#9d2449]">
                                        Confirmar Contraseña<span class="text-[#9d2449] ml-1">*</span>
                                    </label>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 peer-focus:text-[#9d2449] group-hover:text-[#9d2449]">
                                        <i class="fas fa-lock text-lg transition-colors duration-300"></i>
                                    </div>
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
                                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
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
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Crear Usuario</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 