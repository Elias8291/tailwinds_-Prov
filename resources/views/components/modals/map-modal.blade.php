@props(['address' => ''])

<div id="mapa-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" onclick="cerrarModalMapa()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-7xl max-h-[95vh] overflow-hidden border-4 border-green-200">
            <!-- Header mejorado -->
            <div class="p-6 border-b bg-gradient-to-r from-green-600 via-teal-600 to-emerald-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">🗺️ Verificación Geográfica Completa</h3>
                            <p class="text-green-100 text-sm mt-1">Análisis detallado del domicilio y calles aledañas</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- Controles del mapa -->
                        <div class="flex items-center space-x-2 bg-white bg-opacity-20 rounded-lg px-3 py-2">
                            <button onclick="cambiarVistaMapaModal('roadmap')" 
                                    class="px-2 py-1 bg-white bg-opacity-30 hover:bg-opacity-50 rounded text-white text-xs font-medium transition-all">
                                🗺️ Mapa
                            </button>
                            <button onclick="cambiarVistaMapaModal('satellite')" 
                                    class="px-2 py-1 bg-white bg-opacity-30 hover:bg-opacity-50 rounded text-white text-xs font-medium transition-all">
                                🛰️ Satélite
                            </button>
                            <button onclick="cambiarVistaMapaModal('hybrid')" 
                                    class="px-2 py-1 bg-white bg-opacity-30 hover:bg-opacity-50 rounded text-white text-xs font-medium transition-all">
                                🔗 Híbrido
                            </button>
                        </div>
                        <button type="button" class="text-white hover:text-green-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" onclick="cerrarModalMapa()">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Información de la dirección en header -->
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-green-50 border-b border-green-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">📍 Dirección Completa:</p>
                            <p id="direccion-modal-completa" class="text-lg font-semibold text-gray-900">{{ $address ?: 'Cargando dirección...' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="copiarDireccion()" 
                                class="flex items-center space-x-2 px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-all text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span>Copiar Dirección</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Contenedor del mapa -->
            <div id="mapa-modal-container" class="w-full h-[60vh]"></div>
        </div>
    </div>
</div> 