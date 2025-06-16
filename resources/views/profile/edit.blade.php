@extends('layouts.app')

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
        <!-- Notificaciones -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-xs">
            @if(session('status'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="bg-white rounded-lg shadow-lg border-l-4 border-emerald-500 p-1.5">
                <div class="flex items-center">
                    <svg class="h-3.5 w-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="ml-1.5 text-xs font-medium text-gray-900">{{ session('status') }}</p>
                    <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endif
        </div>

        <div class="max-w-xs mx-auto">
            <!-- Encabezado del Perfil -->
            <div class="bg-gradient-to-r from-[#B4325E] to-[#93264B] rounded-lg shadow-md p-2 mb-3 text-white">
                <div class="flex items-center gap-2">
                    <!-- Avatar -->
                    <div class="relative">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/30">
                            <span class="text-base font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div class="absolute -bottom-0.5 -right-0.5 bg-white rounded-full p-0.5 shadow-sm">
                            <svg class="w-2.5 h-2.5 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Información del Usuario -->
                    <div class="flex-1 min-w-0">
                        <h2 class="text-sm font-bold truncate">{{ $user->name }}</h2>
                        <p class="text-xs text-white/80 truncate">{{ $user->email }}</p>
                        <div class="flex gap-1 mt-0.5">
                            <div class="bg-white/10 backdrop-blur-sm rounded px-1.5 py-0.5 text-xs">
                                <span class="text-white/60">Miembro desde</span>
                                <span class="ml-0.5">{{ $user->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Edición -->
            <div class="space-y-3">
                <!-- Información del Perfil -->
                <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-sm overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-3 py-1.5 border-b border-gray-100">
                        <h3 class="text-xs font-semibold text-gray-900 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Información del Perfil
                        </h3>
                    </div>
                    <form method="post" action="{{ route('profile.update') }}" class="p-3">
                        @csrf
                        @method('patch')

                        <div class="space-y-2">
                            <div>
                                <label for="name" class="block text-xs font-medium text-gray-700 mb-0.5">Nombre</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                           class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200 text-sm"
                                           required autofocus autocomplete="name">
                                </div>
                                <x-input-error class="mt-0.5" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-medium text-gray-700 mb-0.5">Correo Electrónico</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                           class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200 text-sm"
                                           required autocomplete="username">
                                </div>
                                <x-input-error class="mt-0.5" :messages="$errors->get('email')" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-2">
                            <button type="submit" 
                                    class="inline-flex items-center px-2 py-1 bg-gradient-to-r from-[#B4325E] to-[#93264B] border border-transparent rounded-md font-medium text-xs text-white hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transition-all duration-200">
                                <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Actualizar Contraseña -->
                <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-sm overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-3 py-1.5 border-b border-gray-100">
                        <h3 class="text-xs font-semibold text-gray-900 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Actualizar Contraseña
                        </h3>
                    </div>
                    <div class="p-3">
                        <div class="space-y-2">
                            <div>
                                <label for="current_password" class="block text-xs font-medium text-gray-700 mb-0.5">Contraseña Actual</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input type="password" name="current_password" id="current_password" 
                                           class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200 text-sm"
                                           autocomplete="current-password">
                                </div>
                                <x-input-error :messages="$errors->get('current_password')" class="mt-0.5" />
                            </div>

                            <div>
                                <label for="password" class="block text-xs font-medium text-gray-700 mb-0.5">Nueva Contraseña</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input type="password" name="password" id="password" 
                                           class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200 text-sm"
                                           autocomplete="new-password">
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-0.5" />
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-medium text-gray-700 mb-0.5">Confirmar Contraseña</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-20 transition-colors duration-200 text-sm"
                                           autocomplete="new-password">
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-0.5" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-2">
                            <button type="submit" 
                                    class="inline-flex items-center px-2 py-1 bg-gradient-to-r from-[#B4325E] to-[#93264B] border border-transparent rounded-md font-medium text-xs text-white hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transition-all duration-200">
                                <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Actualizar Contraseña
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 