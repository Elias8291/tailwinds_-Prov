@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-lg sm:rounded-xl p-2 sm:p-3 shadow-md flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12">
                        <i class="fas fa-users text-white/90 text-lg sm:text-2xl"></i>
                    </div>
                    
                </div>
                
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 mb-6">
            <div class="p-6">
                <form method="GET" action="" class="space-y-5">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-r from-primary to-primary-dark rounded-lg p-2 mr-3">
                            <i class="fas fa-filter text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold bg-gradient-to-r from-primary to-primary-dark bg-clip-text text-transparent">
                            Filtros de Búsqueda
                        </h3>
                    </div>
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Estado -->
                            <div class="group">
                                <label class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                                        <i class="fas fa-user-check text-green-600"></i>
                                    </div>
                                    Estado
                                </label>
                                <div class="relative">
                                    <select name="estado" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-green-300 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all duration-200">
                                        <option value="" {{ request('estado') == '' ? 'selected' : '' }}>Todos</option>
                                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Rol -->
                            <div class="group">
                                <label class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                        <i class="fas fa-user-tag text-blue-600"></i>
                                    </div>
                                    Rol
                                </label>
                                <div class="relative">
                                    <select name="rol" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                        <option value="" {{ request('rol') == '' ? 'selected' : '' }}>Todos</option>
                                        <option value="admin" {{ request('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                        <option value="user" {{ request('rol') == 'user' ? 'selected' : '' }}>Usuario</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Fecha de registro -->
                            <div class="group">
                                <label class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                        <i class="fas fa-calendar-alt text-purple-600"></i>
                                    </div>
                                    Fecha de registro
                                </label>
                                <div class="relative">
                                    <select name="fecha" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200">
                                        <option value="" {{ request('fecha') == '' ? 'selected' : '' }}>Todas las fechas</option>
                                        <option value="hoy" {{ request('fecha') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                                        <option value="semana" {{ request('fecha') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                                        <option value="mes" {{ request('fecha') == 'mes' ? 'selected' : '' }}>Este mes</option>
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
                                <label class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center mr-2">
                                        <i class="fas fa-list-ol text-orange-600"></i>
                                    </div>
                                    Mostrar
                                </label>
                                <div class="relative">
                                    <select name="perPage" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-orange-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all duration-200">
                                        <option value="10" {{ request('perPage', 10) == 10 ? 'selected' : '' }}>10 elementos</option>
                                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25 elementos</option>
                                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50 elementos</option>
                                        <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100 elementos</option>
                                        <option value="all" {{ request('perPage') == 'all' ? 'selected' : '' }}>Todos</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-search mr-2"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Espacio para tabla/lista de usuarios -->
        <x-table
            :items="$usuarios"
            :columns="[
                ['label' => 'Nombre', 'field' => 'nombre'],
                ['label' => 'Correo', 'field' => 'correo'],
                ['label' => 'Rol', 'field' => 'roles.0.name'],
                ['label' => 'Estado', 'field' => 'estado'],
                ['label' => 'Fecha de registro', 'field' => 'created_at'],
            ]"
        />
        @if(method_exists($usuarios, 'hasPages') && $usuarios instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <x-pagination :items="$usuarios" label="usuarios" />
        @endif
    </div>
</div>
@endsection 