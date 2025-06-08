@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Notificaciones -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm">
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="bg-white rounded-lg shadow-lg border-l-4 border-emerald-500 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" class="inline-flex rounded-md p-1.5 text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                        <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Gestión de Usuarios
                        </h2>
                        <p class="text-sm text-gray-500">Administra los usuarios del sistema</p>
                    </div>
                </div>
                <a href="{{ route('users.create') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transform hover:scale-105 transition-all duration-300 hover:shadow-lg w-full md:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Crear Nuevo Usuario
                </a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <!-- Vista móvil -->
            <div class="block md:hidden">
                @foreach($users as $user)
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50/50 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-xl shadow-md flex items-center justify-center font-bold text-xl">
                                {{ strtoupper(substr($user->nombre, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $user->nombre }}</div>
                                <div class="text-sm text-gray-500">{{ $user->correo }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @can('usuarios.edit')
                            <a href="{{ route('users.edit', $user) }}" 
                               class="p-2 text-[#B4325E] hover:text-[#93264B] hover:bg-[#B4325E]/10 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>
                            @endcan

                            @if($user->id !== auth()->id())
                                @can('usuarios.destroy')
                                <button type="button"
                                        @click="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->id }}')"
                                        class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endcan
                            @endif
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                {{ $role->name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Vista desktop -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-[#B4325E] text-white">
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Usuario</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Roles</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-xl shadow-md flex items-center justify-center font-bold text-xl">
                                        {{ strtoupper(substr($user->nombre, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->nombre }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->correo }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->correo }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                        {{ $role->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                        Verificado
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <div class="flex items-center justify-center space-x-3">
                                    @can('usuarios.edit')
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="text-primary hover:text-primary-dark transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                    @endcan

                                    @if($user->id !== auth()->id())
                                        @can('usuarios.destroy')
                                        <button type="button"
                                                @click="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->id }}')"
                                                class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modales de Confirmación de Eliminación -->
@foreach($users as $user)
    @if($user->id !== auth()->id())
    <x-modal name="confirm-user-deletion-{{ $user->id }}" focusable maxWidth="md">
        <div class="p-6">
            <div class="flex items-center justify-center space-x-4 mb-6">
                <div class="flex-shrink-0 h-12 w-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900">
                        Confirmar Eliminación
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        ¿Estás seguro de que deseas eliminar al usuario "{{ $user->nombre }}"? Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-center space-x-3">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">
                    Cancelar
                </button>

                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                        Eliminar Usuario
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
    @endif
@endforeach

@endsection 