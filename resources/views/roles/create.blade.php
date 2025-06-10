@extends('layouts.app')

@section('content')
<div class="min-h-screen w-full overflow-x-hidden">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-3xl mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <form method="POST" action="{{ route('roles.store') }}" class="divide-y divide-gray-100">
                    @csrf

                    <!-- Encabezado -->
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-xl p-3 shadow-lg mb-3">
                                <i class="fas fa-user-shield text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent mb-2">
                                Crear Nuevo Rol
                            </h2>
                            <p class="text-sm text-gray-600">Defina un nuevo rol y sus permisos asociados</p>
                        </div>
                    </div>

                    <!-- Información del Rol -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-6">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                                Información del Rol
                            </h3>
                            <div class="w-32 h-0.5 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="w-full max-w-lg mx-auto space-y-6">
                            <!-- Nombre del Rol -->
                            <div class="form-group">
                                <div class="relative group">
                                    <input type="text" 
                                           id="name"
                                           name="name"
                                           class="peer w-full h-12 px-12 bg-white/50 backdrop-blur-sm border-2 border-gray-200 rounded-xl text-gray-800 appearance-none focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/10 focus:outline-none transition-all duration-300 hover:border-[#9d2449]/50 @error('name') border-red-300 hover:border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
                                           placeholder=" "
                                           required>
                                    <label for="name" 
                                           class="absolute left-11 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 
                                                  peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-12 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-11 peer-focus:bg-white 
                                                  peer-focus:text-[#9d2449] peer-focus:text-sm group-hover:text-[#9d2449]">
                                        Nombre del Rol<span class="text-[#9d2449] ml-1">*</span>
                                    </label>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 peer-focus:text-[#9d2449] group-hover:text-[#9d2449]">
                                        <i class="fas fa-tag text-lg transition-colors duration-300"></i>
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
                        </div>
                    </div>

                    <!-- Permisos -->
                    <div class="p-4">
                        <div class="flex flex-col items-center mb-6">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                                Permisos
                            </h3>
                            <div class="w-32 h-0.5 bg-gradient-to-r from-[#9d2449] to-[#8a203f] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="w-full max-w-4xl mx-auto">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($permissions as $permission)
                                <label class="relative flex items-start p-4 rounded-xl border-2 border-gray-200 hover:border-[#9d2449] transition-all duration-300 cursor-pointer group">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->name }}"
                                           class="mt-1 h-4 w-4 text-[#9d2449] focus:ring-[#9d2449] focus:ring-offset-0 border-gray-300 rounded transition-colors duration-300 accent-[#9d2449]">
                                    <span class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900 group-hover:text-[#9d2449] transition-colors duration-300">
                                            {{ $permission->name }}
                                        </span>
                                        <span class="block text-xs text-gray-500 mt-0.5">
                                            Otorga acceso a esta funcionalidad
                                        </span>
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="p-4">
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('roles.index') }}" 
                               class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-300">
                                <i class="fas fa-times mr-2 text-gray-400 group-hover:text-gray-600"></i>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900">Cancelar</span>
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto group inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg">
                                <i class="fas fa-save mr-2 text-white/90 group-hover:text-white"></i>
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Crear Rol</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 