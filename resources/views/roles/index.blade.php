@extends('layouts.app')

@section('content')
<style>
    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-100%); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideOutUp {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-100%); }
    }
    .notification-slide-in {
        animation: slideInDown 0.5s ease-out forwards;
    }
    .notification-slide-out {
        animation: slideOutUp 0.5s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    .table-row-animate {
        opacity: 0;
        animation: fadeIn 0.3s ease-out forwards;
    }
    .table-row-animate:nth-child(1) { animation-delay: 0.1s; }
    .table-row-animate:nth-child(2) { animation-delay: 0.2s; }
    .table-row-animate:nth-child(3) { animation-delay: 0.3s; }
    .table-row-animate:nth-child(4) { animation-delay: 0.4s; }
    .table-row-animate:nth-child(5) { animation-delay: 0.5s; }

    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-100%); }
        to { opacity: 1; transform: translateY(0); }
    }
    .notification-animate {
        animation: slideInDown 0.5s ease-out forwards;
    }
</style>

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
                 class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-4 notification-slide-in">
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

        <!-- Encabezado con animación y diseño mejorado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                        <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Gestión de Roles
                        </h2>
                        <p class="text-sm text-gray-500">Administra los roles y permisos del sistema</p>
                    </div>
                </div>
                <a href="{{ route('roles.create') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transform hover:scale-105 transition-all duration-300 hover:shadow-lg w-full md:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Crear Nuevo Rol
                </a>
            </div>
        </div>

        <!-- Filtros y Tabla -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 mb-6">
            <div class="p-6">
                <!-- Header compacto -->
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-[#B4325E] to-[#93264B] rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                        Filtros de Búsqueda
                    </h3>
                </div>
                
                <form method="GET" action="{{ route('roles.index') }}" class="space-y-5">
                    <!-- Fila de filtros y botones -->
                    <div class="flex flex-col lg:flex-row lg:items-end lg:gap-4">
                        <!-- Filtros -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 flex-1">
                            <!-- Búsqueda general -->
                            <div class="group">
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    Buscar Rol
                                </label>
                                <div class="relative">
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                           placeholder="Nombre del rol..."
                                           class="w-full bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 placeholder-gray-400 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Ordenar por -->
                            <div class="group">
                                <label for="sort" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                    Ordenar por
                                </label>
                                <div class="relative">
                                    <select name="sort" id="sort" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-green-300 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all duration-200">
                                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nombre</option>
                                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Fecha creación</option>
                                        <option value="permissions_count" {{ request('sort') == 'permissions_count' ? 'selected' : '' }}>Número de permisos</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Elementos por página -->
                            <div class="group">
                                <label for="perPage" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    Mostrar
                                </label>
                                <div class="relative">
                                    <select name="perPage" id="perPage" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200">
                                        @foreach([10, 25, 50, 100] as $option)
                                        <option value="{{ $option }}" {{ request('perPage', 10) == $option ? 'selected' : '' }}>
                                            {{ $option }} elementos
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Botones de acción -->
                        <div class="flex gap-3 mt-4 lg:mt-0 lg:ml-4 justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#7a1d37] text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-[#B4325E]/30 transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
                                </svg>
                                Filtrar
                            </button>
                            <a href="{{ route('roles.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400/20 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Limpiar
                            </a>
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="border-t border-gray-200 my-6"></div>

                    <!-- Tabla de Roles -->
                    <div class="overflow-hidden">
                <!-- Vista móvil -->
                <div class="block md:hidden">
                    @foreach($roles as $role)
                    <div class="p-4 border-b border-gray-100 table-row-animate hover:bg-gray-50/50 transition-all duration-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-xl shadow-md flex items-center justify-center font-bold text-xl">
                                    {{ strtoupper(substr($role->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $role->name }}</div>
                                    <div class="text-xs text-gray-500">Creado {{ $role->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('roles.edit', $role) }}" 
                                   class="p-2 text-[#B4325E] hover:text-[#93264B] hover:bg-[#B4325E]/10 rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                @if(!in_array($role->name, ['admin', 'solicitante']))
                                <button type="button"
                                        @click="$dispatch('open-modal', 'confirm-role-deletion-{{ $role->id }}')"
                                        class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="flex flex-wrap gap-2">
                                @foreach($role->permissions as $permission)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 shadow-sm hover:bg-blue-100 transition-colors duration-200">
                                    {{ $permission->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Vista desktop -->
                <div class="hidden md:block">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 shadow-sm">
                        <thead>
                            <tr class="bg-[#B4325E] text-white">
                                <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider border-r border-gray-200/30">
                                    Rol
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider border-r border-gray-200/30">
                                    Permisos
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($roles as $role)
                            <tr class="hover:bg-gray-50/50 transition-all duration-200 table-row-animate">
                                <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-xl shadow-md flex items-center justify-center font-bold text-xl">
                                            {{ strtoupper(substr($role->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm text-gray-900">{{ $role->name }}</div>
                                            <div class="text-xs text-gray-500">Creado {{ $role->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($role->permissions as $permission)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs bg-blue-50 text-blue-700 border border-blue-100 shadow-sm hover:bg-blue-100 transition-colors duration-200">
                                            {{ $permission->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <div class="flex items-center justify-center space-x-3">
                                        @can('roles.editar')
                                        <a href="{{ route('roles.edit', $role) }}" 
                                           class="text-primary hover:text-primary-dark transform hover:scale-110 transition-all duration-200 p-2 rounded-lg hover:bg-primary-50">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                        @endcan

                                        @if(!in_array($role->name, ['admin', 'solicitante']))
                                            @can('roles.eliminar')
                                            <button type="button"
                                                    @click="$dispatch('open-modal', 'confirm-role-deletion-{{ $role->id }}')"
                                                    class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200 p-2 rounded-lg hover:bg-red-50">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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

                        <!-- Paginación usando componente -->
                        <x-pagination :items="$roles" label="roles" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales de Confirmación de Eliminación -->
@foreach($roles as $role)
    @if(!in_array($role->name, ['admin', 'solicitante']))
    <x-modal name="confirm-role-deletion-{{ $role->id }}" focusable maxWidth="md">
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
                        ¿Estás seguro de que deseas eliminar el rol "{{ $role->name }}"? Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-center space-x-3">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">
                    Cancelar
                </button>

                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                        Eliminar Rol
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
    @endif
@endforeach

<script>
window.addEventListener('show-modal', event => {
    document.body.style.overflow = 'visible';
});
</script>

@endsection 