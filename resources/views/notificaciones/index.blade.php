@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<div class="min-h-screen py-6" x-data="moduloNotificaciones()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Notificaciones de éxito/error -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm">
            <div x-show="mensajeExito" 
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
                        <p class="text-sm font-medium text-gray-900" x-text="mensajeExito"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                        <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Centro de Notificaciones
                        </h2>
                        <p class="text-sm text-gray-500">Gestiona todas tus notificaciones del sistema</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span x-show="contadorNoLeidas > 0" 
                          class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="contadorNoLeidas"></span> sin leer
                    </span>
                    <button @click="marcarTodasComoLeidas()" 
                            x-show="contadorNoLeidas > 0"
                            class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transform hover:scale-105 transition-all duration-300 hover:shadow-lg w-full md:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Marcar todas como leídas
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtros -->
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
                
                <div class="space-y-5">
                    <!-- Fila de filtros y botones -->
                    <div class="flex flex-col xl:flex-row xl:items-end xl:gap-4">
                        <!-- Filtros -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 flex-1">
                            <!-- Estado -->
                            <div class="group">
                                <label class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    Estado
                                </label>
                                <div class="relative">
                                    <select x-model="filtros.estado" @change="aplicarFiltros()" 
                                            class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-green-300 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all duration-200">
                                        <option value="">Todas</option>
                                        <option value="no_leidas">No leídas</option>
                                        <option value="leidas">Leídas</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Tipo -->
                            <div class="group">
                                <label class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                    </div>
                                    Tipo
                                </label>
                                <div class="relative">
                                    <select x-model="filtros.tipo" @change="aplicarFiltros()" 
                                            class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                        <option value="">Todos</option>
                                        <option value="Informativo">Informativo</option>
                                        <option value="Advertencia">Advertencia</option>
                                        <option value="Error">Error</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Período -->
                            <div class="group">
                                <label class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    Período
                                </label>
                                <div class="relative">
                                    <select x-model="filtros.periodo" @change="aplicarFiltros()" 
                                            class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200">
                                        <option value="">Todas las fechas</option>
                                        <option value="hoy">Hoy</option>
                                        <option value="semana">Esta semana</option>
                                        <option value="mes">Este mes</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Elementos por vista -->
                            <div class="group">
                                <label class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    Mostrar
                                </label>
                                <div class="relative">
                                    <select x-model="elementosPorPagina" @change="aplicarFiltros()" 
                                            class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-orange-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all duration-200">
                                        <option value="10">10 elementos</option>
                                        <option value="25">25 elementos</option>
                                        <option value="50">50 elementos</option>
                                        <option value="100">100 elementos</option>
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
                        <div class="flex gap-3 mt-4 xl:mt-0 xl:ml-4 justify-end">
                            <button @click="recargarNotificaciones()" 
                                    class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#7a1d37] text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-[#B4325E]/30 transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" :class="{'animate-spin': cargando}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span x-show="!cargando">Actualizar</span>
                                <span x-show="cargando">Cargando...</span>
                            </button>
                            <button @click="limpiarFiltros()" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400/20 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Limpiar
                            </button>
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="border-t border-gray-200 my-6"></div>

                    <!-- Lista de notificaciones -->
                    <div class="overflow-hidden">
                        <!-- Estado de carga -->
                        <div x-show="cargando" class="flex flex-col items-center justify-center py-16">
                            <div class="bg-gradient-to-r from-[#B4325E] to-[#93264B] rounded-full p-3 shadow-lg mb-4">
                                <svg class="animate-spin h-8 w-8 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">Cargando notificaciones...</p>
                            <p class="text-sm text-gray-400 mt-1">Por favor espera un momento</p>
                        </div>

                        <!-- Sin notificaciones -->
                        <div x-show="!cargando && notificacionesFiltradas.length === 0" class="flex flex-col items-center justify-center py-16">
                            <div class="bg-gray-100 rounded-full p-6 shadow-lg mb-6">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay notificaciones</h3>
                            <p class="text-sm text-gray-500 text-center max-w-sm">
                                <span x-show="tieneElementosFiltrados()">No se encontraron notificaciones con los filtros aplicados. Intenta ajustar los criterios de búsqueda.</span>
                                <span x-show="!tieneElementosFiltrados()">No tienes notificaciones en este momento. Te notificaremos cuando haya nuevas actualizaciones.</span>
                            </p>
                        </div>

                        <!-- Lista de notificaciones -->
                        <div x-show="!cargando && notificacionesFiltradas.length > 0" class="space-y-3">
                            <template x-for="notificacion in notificacionesFiltradas" :key="notificacion.id">
                                <div class="bg-white/70 backdrop-blur-sm rounded-lg shadow-sm border border-gray-200/60 hover:shadow-md hover:bg-white/90 transition-all duration-200 group"
                                     :class="!notificacion.leida ? 'ring-1 ring-blue-200 border-blue-200 bg-blue-50/40' : ''">
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <!-- Contenido principal -->
                                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                <!-- Icono compacto -->
                                                <div class="flex-shrink-0">
                                                    <div class="relative">
                                                        <div class="inline-flex items-center justify-center h-9 w-9 rounded-lg shadow-sm"
                                                             :class="{
                                                                 'bg-gradient-to-br from-blue-400 to-blue-500': notificacion.tipo === 'Informativo',
                                                                 'bg-gradient-to-br from-yellow-400 to-yellow-500': notificacion.tipo === 'Advertencia',
                                                                 'bg-gradient-to-br from-red-400 to-red-500': notificacion.tipo === 'Error'
                                                             }">
                                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <!-- Informativo -->
                                                                <path x-show="notificacion.tipo === 'Informativo'" 
                                                                      stroke-linecap="round" stroke-linejoin="round" 
                                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                <!-- Advertencia -->
                                                                <path x-show="notificacion.tipo === 'Advertencia'" 
                                                                      stroke-linecap="round" stroke-linejoin="round" 
                                                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                                <!-- Error -->
                                                                <path x-show="notificacion.tipo === 'Error'" 
                                                                      stroke-linecap="round" stroke-linejoin="round" 
                                                                      d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </div>
                                                        
                                                        <span x-show="!notificacion.leida" 
                                                              class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 border-2 border-white rounded-full animate-pulse"></span>
                                                    </div>
                                                </div>

                                                <!-- Contenido de texto -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <h4 class="text-sm font-semibold text-gray-900 truncate" x-text="notificacion.titulo"></h4>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium flex-shrink-0"
                                                              :class="{
                                                                  'bg-blue-100 text-blue-700': notificacion.tipo === 'Informativo',
                                                                  'bg-yellow-100 text-yellow-700': notificacion.tipo === 'Advertencia',
                                                                  'bg-red-100 text-red-700': notificacion.tipo === 'Error'
                                                              }"
                                                              x-text="notificacion.tipo">
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 line-clamp-2 mb-2" x-text="notificacion.mensaje"></p>
                                                    <div class="flex items-center text-xs text-gray-500 space-x-3">
                                                        <div class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                            <span x-text="notificacion.fecha"></span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span x-text="notificacion.tiempo_transcurrido"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Acciones compactas -->
                                            <div class="flex items-center space-x-1 ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <button x-show="!notificacion.leida" 
                                                        @click="marcarComoLeida(notificacion.id)"
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500/30 transition-colors duration-200"
                                                        title="Marcar como leída">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Leída
                                                </button>
                                                
                                                <button @click="$dispatch('abrir-modal', { id: notificacion.id, titulo: notificacion.titulo })"
                                                        class="inline-flex items-center p-1.5 text-red-500 hover:text-white hover:bg-red-500 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500/30 transition-colors duration-200"
                                                        title="Eliminar notificación">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Información de paginación -->
                        <div x-show="!cargando && notificacionesFiltradas.length > 0" class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-center">
                                <div class="bg-white/60 backdrop-blur-sm rounded-lg px-4 py-2 shadow-sm border border-gray-200">
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Mostrando <span class="font-medium" x-text="notificacionesFiltradas.length"></span> notificaciones
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación (Fuera del contenedor principal) -->
<div x-data="modalEliminacion()"
     @abrir-modal.window="abrirModal($event.detail.id, $event.detail.titulo)"
     x-show="mostrarModal" 
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div x-show="mostrarModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="cerrarModal()"></div>

        <!-- Centrar el modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal -->
        <div x-show="mostrarModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="flex items-center">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-4 text-left">
                        <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                            Confirmar Eliminación
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                ¿Estás seguro de que deseas eliminar la notificación 
                                <span class="font-semibold text-gray-700" x-text="notificacionSeleccionada.titulo"></span>?
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Esta acción no se puede deshacer.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                <button @click="eliminar()" 
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg sm:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Eliminar
                </button>
                <button @click="cerrarModal()" 
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-200 sm:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function moduloNotificaciones() {
    return {
        cargando: false,
        notificaciones: [],
        notificacionesFiltradas: [],
        contadorNoLeidas: 0,
        mensajeExito: '',
        elementosPorPagina: 25,
        filtros: {
            estado: '',
            tipo: '',
            periodo: ''
        },

        
        init() {
            this.cargarNotificaciones();
            // Actualizar contador cada 30 segundos
            setInterval(() => {
                this.cargarContador();
            }, 30000);
        },
        
        async cargarNotificaciones() {
            this.cargando = true;
            try {
                const response = await fetch('{{ route("notificaciones.todas") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.notificaciones = data.notificaciones;
                    this.contadorNoLeidas = data.contador_no_leidas;
                    this.aplicarFiltros();
                }
            } catch (error) {
                console.error('Error al cargar notificaciones:', error);
            } finally {
                this.cargando = false;
            }
        },
        
        async cargarContador() {
            try {
                const response = await fetch('{{ route("notificaciones.contador") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.contadorNoLeidas = data.contador;
                }
            } catch (error) {
                console.error('Error al cargar contador:', error);
            }
        },
        
        aplicarFiltros() {
            let filtradas = [...this.notificaciones];
            
            // Filtro por estado
            if (this.filtros.estado === 'no_leidas') {
                filtradas = filtradas.filter(n => !n.leida);
            } else if (this.filtros.estado === 'leidas') {
                filtradas = filtradas.filter(n => n.leida);
            }
            
            // Filtro por tipo
            if (this.filtros.tipo) {
                filtradas = filtradas.filter(n => n.tipo === this.filtros.tipo);
            }
            
            // Filtro por período
            if (this.filtros.periodo) {
                const ahora = new Date();
                filtradas = filtradas.filter(n => {
                    const fechaNotificacion = new Date(n.fecha.split(' ')[0].split('/').reverse().join('-'));
                    
                    switch (this.filtros.periodo) {
                        case 'hoy':
                            return fechaNotificacion.toDateString() === ahora.toDateString();
                        case 'semana':
                            const inicioSemana = new Date(ahora);
                            inicioSemana.setDate(ahora.getDate() - ahora.getDay());
                            return fechaNotificacion >= inicioSemana;
                        case 'mes':
                            return fechaNotificacion.getMonth() === ahora.getMonth() && 
                                   fechaNotificacion.getFullYear() === ahora.getFullYear();
                        default:
                            return true;
                    }
                });
            }
            
            // Limitar por elementos por página
            this.notificacionesFiltradas = filtradas.slice(0, this.elementosPorPagina);
        },
        
        limpiarFiltros() {
            this.filtros = {
                estado: '',
                tipo: '',
                periodo: ''
            };
            this.elementosPorPagina = 25;
            this.aplicarFiltros();
        },
        
        tieneElementosFiltrados() {
            return this.filtros.estado || this.filtros.tipo || this.filtros.periodo;
        },
        
        async recargarNotificaciones() {
            await this.cargarNotificaciones();
        },
        
        async marcarComoLeida(notificacionId) {
            try {
                const response = await fetch(`{{ url('notificaciones') }}/${notificacionId}/marcar-leida`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    // Actualizar la notificación como leída
                    const notificacion = this.notificaciones.find(n => n.id === notificacionId);
                    if (notificacion && !notificacion.leida) {
                        notificacion.leida = true;
                        this.contadorNoLeidas = Math.max(0, this.contadorNoLeidas - 1);
                        this.aplicarFiltros();
                        this.mostrarMensaje('Notificación marcada como leída');
                    }
                }
            } catch (error) {
                console.error('Error al marcar como leída:', error);
            }
        },
        
        async marcarTodasComoLeidas() {
            try {
                const response = await fetch('{{ route("notificaciones.marcar-todas-leidas") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    // Marcar todas como leídas
                    this.notificaciones.forEach(notificacion => {
                        notificacion.leida = true;
                    });
                    this.contadorNoLeidas = 0;
                    this.aplicarFiltros();
                    this.mostrarMensaje('Todas las notificaciones marcadas como leídas');
                }
            } catch (error) {
                console.error('Error al marcar todas como leídas:', error);
            }
        },
        

        
        mostrarMensaje(mensaje) {
            this.mensajeExito = mensaje;
            setTimeout(() => {
                this.mensajeExito = '';
            }, 3000);
        }
    }
}

function modalEliminacion() {
    return {
        mostrarModal: false,
        notificacionSeleccionada: { id: null, titulo: '' },
        
        abrirModal(id, titulo) {
            this.notificacionSeleccionada = { id: id, titulo: titulo };
            this.mostrarModal = true;
        },
        
        cerrarModal() {
            this.mostrarModal = false;
            this.notificacionSeleccionada = { id: null, titulo: '' };
        },
        
        async eliminar() {
            try {
                const response = await fetch(`{{ url('notificaciones') }}/${this.notificacionSeleccionada.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    this.cerrarModal();
                    // Recargar la página para actualizar la lista
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error al eliminar notificación:', error);
                this.cerrarModal();
            }
        }
    }
}
</script>
@endsection 