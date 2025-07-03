{{-- Controles de Revisión Reutilizables --}}
<div class="revision-controls bg-white border border-gray-200 rounded-xl p-6 mt-6" data-seccion="{{ $seccion ?? 'general' }}">
    <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        Revisión de {{ $titulo ?? 'Sección' }}
    </h4>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Comentarios --}}
        <div>
            <label for="comentarios-{{ $seccion ?? 'general' }}" class="block text-sm font-medium text-gray-700 mb-2">
                Comentarios de Revisión
            </label>
            <textarea 
                id="comentarios-{{ $seccion ?? 'general' }}" 
                name="comentarios" 
                rows="4" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                placeholder="Agregue sus observaciones sobre esta sección..."></textarea>
        </div>
        
        {{-- Controles de Estado --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Estado de Revisión</label>
            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="radio" 
                           name="estado-{{ $seccion ?? 'general' }}" 
                           value="pendiente" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" 
                           checked>
                    <span class="ml-3 text-sm text-gray-700 flex items-center">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                        Pendiente de revisión
                    </span>
                </label>
                
                <label class="flex items-center">
                    <input type="radio" 
                           name="estado-{{ $seccion ?? 'general' }}" 
                           value="aprobado" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <span class="ml-3 text-sm text-gray-700 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Aprobado
                    </span>
                </label>
                
                <label class="flex items-center">
                    <input type="radio" 
                           name="estado-{{ $seccion ?? 'general' }}" 
                           value="rechazado" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <span class="ml-3 text-sm text-gray-700 flex items-center">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                        Rechazado
                    </span>
                </label>
                
                <label class="flex items-center">
                    <input type="radio" 
                           name="estado-{{ $seccion ?? 'general' }}" 
                           value="correccion" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <span class="ml-3 text-sm text-gray-700 flex items-center">
                        <span class="w-2 h-2 bg-yellow-600 rounded-full mr-2"></span>
                        Requiere corrección
                    </span>
                </label>
            </div>
            
            {{-- Nivel de prioridad --}}
            <div class="mt-4">
                <label for="prioridad-{{ $seccion ?? 'general' }}" class="block text-sm font-medium text-gray-700 mb-2">
                    Prioridad del Comentario
                </label>
                <select id="prioridad-{{ $seccion ?? 'general' }}" 
                        name="prioridad"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="baja">🟢 Baja - Observación menor</option>
                    <option value="media" selected>🟡 Media - Requiere atención</option>
                    <option value="alta">🔴 Alta - Problema crítico</option>
                </select>
            </div>
        </div>
    </div>
    
    {{-- Botones de Acción --}}
    <div class="flex flex-wrap gap-3 mt-6 pt-4 border-t border-gray-200">
        <button type="button" 
                onclick="RevisionTramite.sections.approve('{{ $seccion ?? 'general' }}')"
                class="flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all transform hover:scale-105">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Aprobar Sección
        </button>
        
        <button type="button" 
                onclick="RevisionTramite.sections.reject('{{ $seccion ?? 'general' }}')"
                class="flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-all transform hover:scale-105">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Rechazar Sección
        </button>
        
        <button type="button" 
                onclick="RevisionTramite.sections.requestCorrection('{{ $seccion ?? 'general' }}')"
                class="flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-all transform hover:scale-105">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Solicitar Corrección
        </button>
        
        <button type="button" 
                onclick="RevisionTramite.sections.saveComment('{{ $seccion ?? 'general' }}')"
                class="flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-all transform hover:scale-105">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
            Guardar Solo Comentario
        </button>
    </div>
    
    {{-- Historial de revisiones (se muestra dinámicamente) --}}
    <div id="historial-{{ $seccion ?? 'general' }}" class="hidden mt-6 pt-4 border-t border-gray-200">
        <h5 class="text-sm font-medium text-gray-900 mb-3">Historial de Revisiones</h5>
        <div id="lista-historial-{{ $seccion ?? 'general' }}" class="space-y-2 max-h-40 overflow-y-auto">
            {{-- Se llena dinámicamente --}}
        </div>
    </div>
</div> 