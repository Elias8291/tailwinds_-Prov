@extends('layouts.app')

@section('content')
<div class="min-h-screen w-full overflow-x-hidden">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-3xl mx-auto">
            <!-- Formulario -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <form method="POST" action="{{ route('roles.update', $role) }}" class="divide-y divide-gray-100">
                    @csrf
                    @method('PUT')

                    <!-- Encabezado -->
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-lg mb-3">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent mb-2">
                                Editar Rol
                            </h2>
                            <p class="text-sm text-gray-600">Modifique la información y permisos del rol</p>
                        </div>
                    </div>

                    <!-- Información del Rol -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-4">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                                Información del Rol
                            </h3>
                            <div class="w-20 h-0.5 bg-gradient-to-r from-[#B4325E] to-[#93264B] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="w-full max-w-lg mx-auto space-y-4">
                            <!-- Nombre del Rol -->
                            <div class="form-group">
                                <div class="relative">
                                    <input type="text" 
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $role->name) }}"
                                           class="peer w-full h-11 px-4 bg-gray-50/30 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-[#B4325E] focus:bg-white transition-all duration-300"
                                           placeholder="Nombre del Rol"
                                           required>
                                    <label for="name" 
                                           class="absolute left-2 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                                                  peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-2 peer-focus:bg-white 
                                                  peer-focus:text-[#B4325E] peer-focus:text-sm">
                                        Nombre del Rol<span class="text-[#B4325E] ml-1">*</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permisos -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-4">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                                Permisos del Rol
                            </h3>
                            <div class="w-20 h-0.5 bg-gradient-to-r from-[#B4325E] to-[#93264B] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 w-full">
                            @foreach($permissions as $permission)
                            <label class="group relative">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->id }}"
                                       class="peer absolute opacity-0"
                                       {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <div class="relative flex items-center p-3 rounded-lg border-2 border-gray-200 bg-white cursor-pointer transition-all duration-300
                                            peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 hover:border-emerald-300 hover:shadow-md">
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

                                    <!-- Contenido del permiso -->
                                    <div class="flex items-center">
                                        <i class="fas fa-key text-sm text-gray-400 group-hover:text-emerald-600 peer-checked:text-emerald-600 transition-colors duration-300 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 group-hover:text-emerald-600 peer-checked:text-emerald-700">
                                                {{ ucfirst($permission->name) }}
                                            </p>
                                            <p class="text-[10px] text-gray-500 group-hover:text-emerald-500 peer-checked:text-emerald-600">
                                                {{ $permission->description ?? 'Permiso del sistema' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="p-4">
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('roles.index') }}" 
                               class="w-full sm:w-auto group inline-flex items-center justify-center px-4 py-2 rounded-lg border-2 border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-300">
                                <i class="fas fa-times mr-2 text-gray-400 group-hover:text-gray-600"></i>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900">Cancelar</span>
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto group inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transition-all duration-300 shadow-md hover:shadow-lg">
                                <i class="fas fa-save mr-2 text-white/90 group-hover:text-white"></i>
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Actualizar Rol</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 