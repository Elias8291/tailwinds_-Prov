@extends('layouts.app')

@section('content')
<div class="min-h-screen w-full overflow-x-hidden">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-3xl mx-auto">
            <!-- Formulario -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <!-- Errores de validación -->
                @if ($errors->any())
                <div class="p-4 border-b border-gray-100">
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Hay errores en el formulario:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('users.update', $user) }}" class="divide-y divide-gray-100">
                    @csrf
                    @method('PUT')

                    <!-- Encabezado -->
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-lg mb-3">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent mb-2">
                                Editar Usuario
                            </h2>
                            <p class="text-sm text-gray-600">Modifique la información del usuario y sus roles</p>
                        </div>
                    </div>

                    <!-- Información del Usuario -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-4">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                                Información del Usuario
                            </h3>
                            <div class="w-20 h-0.5 bg-gradient-to-r from-[#B4325E] to-[#93264B] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <!-- Nombre -->
                            <div class="form-group">
                                <div class="relative">
                                    <input type="text" 
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $user->name) }}"
                                           class="peer w-full h-11 px-4 bg-gray-50/30 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-[#B4325E] focus:bg-white transition-all duration-300"
                                           placeholder="Nombre"
                                           required>
                                    <label for="name" 
                                           class="absolute left-2 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                                                  peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-2 peer-focus:bg-white 
                                                  peer-focus:text-[#B4325E] peer-focus:text-sm">
                                        Nombre<span class="text-[#B4325E] ml-1">*</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <div class="relative">
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $user->email) }}"
                                           class="peer w-full h-11 px-4 bg-gray-50/30 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-[#B4325E] focus:bg-white transition-all duration-300"
                                           placeholder="Correo Electrónico"
                                           required>
                                    <label for="email" 
                                           class="absolute left-2 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                                                  peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-2 peer-focus:bg-white 
                                                  peer-focus:text-[#B4325E] peer-focus:text-sm">
                                        Correo Electrónico<span class="text-[#B4325E] ml-1">*</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="form-group">
                                <div class="relative">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="peer w-full h-11 px-4 bg-gray-50/30 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-[#B4325E] focus:bg-white transition-all duration-300"
                                           placeholder="Nueva Contraseña">
                                    <label for="password" 
                                           class="absolute left-2 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                                                  peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-2 peer-focus:bg-white 
                                                  peer-focus:text-[#B4325E] peer-focus:text-sm">
                                        Nueva Contraseña
                                    </label>
                                    <div class="flex items-center mt-1.5 ml-1">
                                        <i class="fas fa-info-circle text-gray-400 text-xs mr-1.5"></i>
                                        <span class="text-xs text-gray-500">Dejar en blanco para mantener la actual</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="form-group">
                                <div class="relative">
                                    <input type="password"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           class="peer w-full h-11 px-4 bg-gray-50/30 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-[#B4325E] focus:bg-white transition-all duration-300"
                                           placeholder="Confirmar Nueva Contraseña">
                                    <label for="password_confirmation" 
                                           class="absolute left-2 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                                                  peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-2 peer-focus:bg-white 
                                                  peer-focus:text-[#B4325E] peer-focus:text-sm">
                                        Confirmar Nueva Contraseña
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-4">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                                Roles del Usuario
                            </h3>
                            <div class="w-20 h-0.5 bg-gradient-to-r from-[#B4325E] to-[#93264B] mt-2 rounded-full opacity-50"></div>
                        </div>
                        
                        <div class="flex flex-wrap justify-center gap-2 w-full">
                            @foreach($roles as $role)
                            <label class="group relative w-[120px] flex-shrink-0">
                                <input type="radio" 
                                       name="role" 
                                       value="{{ $role->id }}"
                                       class="peer absolute opacity-0"
                                       {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <div class="relative h-full flex flex-col items-center p-2 rounded-lg border-2 border-gray-200 bg-white cursor-pointer transition-all duration-300
                                            peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 hover:border-emerald-300 hover:shadow-md group">
                                    <!-- Indicador de selección -->
                                    <div class="absolute -top-1.5 -right-1.5 transform transition-all duration-300">
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 bg-white 
                                                    group-hover:border-emerald-500 group-hover:bg-emerald-50
                                                    peer-checked:border-emerald-500 peer-checked:bg-emerald-500
                                                    flex items-center justify-center transition-all duration-300">
                                            <svg class="w-2.5 h-2.5 text-white transform scale-0 peer-checked:scale-100 transition-transform duration-300"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Icono del rol -->
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 
                                                group-hover:bg-emerald-100/50 peer-checked:bg-emerald-100 
                                                transition-colors duration-300 mb-2">
                                        @switch($role->name)
                                            @case('admin')
                                                <i class="fas fa-user-shield text-sm text-gray-400 
                                                          group-hover:text-emerald-600 peer-checked:text-emerald-600 
                                                          transition-colors duration-300"></i>
                                                @break
                                            @case('revisor')
                                                <i class="fas fa-user-tie text-sm text-gray-400 
                                                          group-hover:text-emerald-600 peer-checked:text-emerald-600 
                                                          transition-colors duration-300"></i>
                                                @break
                                            @case('solicitante')
                                                <i class="fas fa-user-edit text-sm text-gray-400 
                                                          group-hover:text-emerald-600 peer-checked:text-emerald-600 
                                                          transition-colors duration-300"></i>
                                                @break
                                            @case('proveedor')
                                                <i class="fas fa-truck text-sm text-gray-400 
                                                          group-hover:text-emerald-600 peer-checked:text-emerald-600 
                                                          transition-colors duration-300"></i>
                                                @break
                                            @default
                                                <i class="fas fa-user text-sm text-gray-400 
                                                          group-hover:text-emerald-600 peer-checked:text-emerald-600 
                                                          transition-colors duration-300"></i>
                                        @endswitch
                                    </div>

                                    <!-- Texto -->
                                    <div class="text-center">
                                        <p class="text-xs font-medium text-gray-700 
                                                  group-hover:text-emerald-600 peer-checked:text-emerald-700 
                                                  transition-colors duration-300">
                                            {{ ucfirst($role->name) }}
                                        </p>
                                        <p class="text-[10px] text-gray-500 
                                                  group-hover:text-emerald-500 peer-checked:text-emerald-600 
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
                               class="w-full sm:w-auto group inline-flex items-center justify-center px-4 py-2 rounded-lg border-2 border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-300">
                                <i class="fas fa-times mr-2 text-gray-400 group-hover:text-gray-600"></i>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900">Cancelar</span>
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto group inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transition-all duration-300 shadow-md hover:shadow-lg">
                                <i class="fas fa-save mr-2 text-white/90 group-hover:text-white"></i>
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Actualizar Usuario</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 