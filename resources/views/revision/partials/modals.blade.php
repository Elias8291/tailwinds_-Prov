{{-- Modal para ver documentos --}}
<div id="documento-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="RevisionTramite.modals.closeDocument()"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-7xl max-h-[95vh] overflow-hidden">
            {{-- Header del modal --}}
            <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-[#B4325E] to-[#93264B]">
                <h3 class="text-xl font-semibold text-white" id="documento-modal-title">
                    Visualización de Documento
                </h3>
                <button type="button" class="text-white hover:text-gray-200 transition-colors" onclick="RevisionTramite.modals.closeDocument()">
                    <span class="sr-only">Cerrar</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Contenido principal del modal --}}
            <div class="flex h-[85vh]">
                {{-- Panel izquierdo - Documento --}}
                <div class="flex-1 flex flex-col">
                    <div class="flex-1 overflow-hidden">
                        <iframe id="documento-iframe" class="w-full h-full" frameborder="0"></iframe>
                    </div>
                </div>
                
                {{-- Panel derecho - Controles de revisión --}}
                <div class="w-80 bg-gray-50 border-l border-gray-200 p-4 overflow-y-auto">
                    {{-- Controles de revisión simplificados para modal --}}
                    <div class="revision-controls bg-white border border-gray-200 rounded-xl p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Revisión de Documento
                        </h4>
                        
                        <div class="space-y-4">
                            {{-- Comentarios --}}
                            <div>
                                <label for="comentarios-documento" class="block text-sm font-medium text-gray-700 mb-2">
                                    Comentarios de Revisión
                                </label>
                                <textarea 
                                    id="comentarios-documento"
                                    name="comentarios" 
                                    rows="4" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#B4325E] focus:border-[#B4325E] resize-none"
                                    placeholder="Agregue sus observaciones sobre este documento..."></textarea>
                            </div>
                            
                            {{-- Controles de Estado --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Estado de Revisión</label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="estado-documento" 
                                               value="pendiente" 
                                               class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300" 
                                               checked>
                                        <span class="ml-3 text-sm text-gray-700 flex items-center">
                                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                            Pendiente de revisión
                                        </span>
                                    </label>
                                    
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="estado-documento" 
                                               value="aprobado" 
                                               class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300">
                                        <span class="ml-3 text-sm text-gray-700 flex items-center">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            Aprobado
                                        </span>
                                    </label>
                                    
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="estado-documento" 
                                               value="rechazado" 
                                               class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300">
                                        <span class="ml-3 text-sm text-gray-700 flex items-center">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                            Rechazado
                                        </span>
                                    </label>
                                    
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="estado-documento" 
                                               value="correccion" 
                                               class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300">
                                        <span class="ml-3 text-sm text-gray-700 flex items-center">
                                            <span class="w-2 h-2 bg-yellow-600 rounded-full mr-2"></span>
                                            Requiere corrección
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            {{-- Botones de Acción --}}
                            <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                                <button type="button" 
                                        onclick="RevisionTramite.sections.approve('documento')"
                                        class="flex items-center justify-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Aprobar
                                </button>
                                
                                <button type="button" 
                                        onclick="RevisionTramite.sections.reject('documento')"
                                        class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-all">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Rechazar
                                </button>
                                
                                <button type="button" 
                                        onclick="RevisionTramite.sections.requestCorrection('documento')"
                                        class="flex items-center justify-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-all">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Corrección
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal para ver mapa completo --}}
<div id="mapa-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" onclick="RevisionTramite.modals.closeMap()"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-7xl max-h-[95vh] overflow-hidden border-4 border-green-200">
            {{-- Header mejorado --}}
            <div class="p-6 border-b bg-gradient-to-r from-green-600 via-teal-600 to-emerald-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">🗺️ Verificación Geográfica Completa</h3>
                            <p class="text-green-100 text-sm mt-1">Análisis detallado del domicilio y calles aledañas</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        {{-- Controles del mapa --}}
                        <div class="flex items-center space-x-2 bg-white bg-opacity-20 rounded-lg px-3 py-2">
                            <button onclick="RevisionTramite.maps.changeView('roadmap')" 
                                    class="px-2 py-1 bg-white bg-opacity-30 hover:bg-opacity-50 rounded text-white text-xs font-medium transition-all">
                                🗺️ Mapa
                            </button>
                            <button onclick="RevisionTramite.maps.changeView('satellite')" 
                                    class="px-2 py-1 bg-white bg-opacity-30 hover:bg-opacity-50 rounded text-white text-xs font-medium transition-all">
                                🛰️ Satélite
                            </button>
                            <button onclick="RevisionTramite.maps.changeView('hybrid')" 
                                    class="px-2 py-1 bg-white bg-opacity-30 hover:bg-opacity-50 rounded text-white text-xs font-medium transition-all">
                                🔗 Híbrido
                            </button>
                        </div>
                        <button type="button" class="text-white hover:text-green-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" onclick="RevisionTramite.modals.closeMap()">
                            <span class="sr-only">Cerrar</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Información de la dirección --}}
            <div class="bg-gradient-to-r from-green-100 to-teal-100 p-4 border-b">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-green-800">Domicilio a Verificar</h4>
                        <p class="text-green-700 text-sm" id="direccion-completa">Cargando dirección...</p>
                    </div>
                </div>
            </div>
            
            {{-- Contenedor del mapa principal --}}
            <div class="flex-1 overflow-hidden" style="height: 75vh;">
                <div id="mapa-domicilio" class="w-full h-full relative">
                    <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-green-50 to-teal-50 z-10">
                        <div class="text-center">
                            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mb-4"></div>
                            <p class="text-lg font-medium text-gray-700">Cargando mapa detallado...</p>
                            <p class="text-sm text-gray-500 mt-2">Preparando vista de calles aledañas</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Footer con información adicional --}}
            <div class="bg-gray-50 border-t p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Usa los controles para explorar el área</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span>Powered by Google Maps</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 