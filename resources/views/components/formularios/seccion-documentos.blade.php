@props(['title' => 'Documentos Requeridos', 'tramite' => null, 'mostrar_navegacion' => true, 'documentos' => [], 'readonly' => false])
<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8" 
     @if(!$readonly) x-data="documentosData()" x-init="init()" @endif>
    <!-- Encabezado con icono -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg">
            <i class="fas fa-file-upload text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                @if($readonly)
                    Documentos adjuntos al trámite
                @else
                    <span x-text="descripcionDocumentos"></span>
                @endif
            </p>
        </div>
    </div>
    @if($readonly)
        <!-- Vista de solo lectura para revisión -->
        <div class="space-y-6">
            @if(count($documentos) > 0)
                @foreach($documentos as $documento)
                <div class="bg-white border-2 rounded-lg p-6 transition-all duration-300
                    @if($documento['estado'] === 'Aprobado') border-green-300 bg-green-50
                    @elseif($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo'])) border-blue-300 bg-blue-50
                    @elseif($documento['estado'] === 'Rechazado') border-red-300 bg-red-50
                    @else border-gray-300 @endif">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="relative">
                                <i class="fas fa-file-pdf text-2xl mr-3
                                    @if($documento['estado'] === 'Aprobado') text-green-600
                                    @elseif($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo'])) text-blue-600
                                    @elseif($documento['estado'] === 'Rechazado') text-red-600
                                    @else text-[#9d2449] @endif"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $documento['nombre'] }}</h4>
                                <p class="text-xs text-gray-500">{{ $documento['descripcion'] ?? 'Documento requerido' }}</p>
                                <!-- Estado del documento -->
                                @if($documento['estado'] !== 'Pendiente' || !empty($documento['ruta_archivo']))
                                <div class="flex items-center mt-1">
                                    <i class="text-xs mr-1
                                        @if($documento['estado'] === 'Aprobado') fas fa-check-circle text-green-500
                                        @elseif($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo'])) fas fa-clock text-blue-500
                                        @elseif($documento['estado'] === 'Rechazado') fas fa-times-circle text-red-500
                                        @endif"></i>
                                    <span class="text-xs font-medium
                                        @if($documento['estado'] === 'Aprobado') text-green-600
                                        @elseif($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo'])) text-blue-600
                                        @elseif($documento['estado'] === 'Rechazado') text-red-600
                                        @endif">
                                        @if($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo']))
                                            En Revisión
                                        @else
                                            {{ $documento['estado'] }}
                                        @endif
                                    </span>
                                </div>
                                @endif
                                <!-- Observaciones para documentos rechazados -->
                                @if($documento['estado'] === 'Rechazado' && !empty($documento['observaciones']))
                                <div class="mt-1">
                                    <p class="text-xs text-red-600">{{ $documento['observaciones'] }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        <!-- Botones de acción para revisión -->
                        <div class="flex items-center space-x-2">
                            @if(!empty($documento['ruta_archivo']))
                                @if(request()->is('revision/*'))
                                    {{-- En contexto de revisión, usar la ruta específica de revisión --}}
                                    <a href="{{ route('revision.ver-documento', ['tramite' => $tramite->id ?? 0, 'documento' => $documento['id']]) }}" 
                                       target="_blank"
                                       class="text-green-600 hover:text-green-800 text-xs underline">
                                        <i class="fas fa-eye mr-1"></i>
                                        Ver
                                    </a>
                                @else
                                    {{-- En otros contextos, usar la ruta normal --}}
                                <a href="{{ route('tramites.solicitante.ver-documento', ['tramite' => $tramite->id ?? 0, 'documento' => $documento['id']]) }}" 
                                   target="_blank"
                                   class="text-green-600 hover:text-green-800 text-xs underline">
                                    <i class="fas fa-eye mr-1"></i>
                                    Ver
                                </a>
                                @endif
                            @endif
                            @if($documento['estado'] === 'Pendiente')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pendiente
                                </span>
                            @elseif($documento['estado'] === 'Aprobado')
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-check mr-1"></i>
                                    Aprobado
                                </span>
                            @elseif($documento['estado'] === 'Rechazado')
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-times mr-1"></i>
                                    Rechazado
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- Información adicional del archivo -->
                    @if(!empty($documento['ruta_archivo']))
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-file-pdf text-[#9d2449] mr-2"></i>
                            <span>{{ $documento['nombre_original'] ?? 'Documento adjunto' }}</span>
                            @if(!empty($documento['fecha_subida']))
                            <span class="ml-auto text-xs text-gray-500">
                                @php
                                    $fechaSubida = $documento['fecha_subida'] ?? '';
                                    if (!empty($fechaSubida) && $fechaSubida !== 'No disponible') {
                                        try {
                                            $fechaSubidaFormateada = \Carbon\Carbon::parse($fechaSubida)->format('d/m/Y H:i');
                                        } catch (\Exception $e) {
                                            $fechaSubidaFormateada = $fechaSubida;
                                        }
                                    } else {
                                        $fechaSubidaFormateada = 'No especificado';
                                    }
                                @endphp
                                Subido: {{ $fechaSubidaFormateada }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif
                    <!-- Panel de Revisión Individual por Documento -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-clipboard-check text-[#9d2449] mr-2"></i>
                            Revisión de Documento
                        </h5>
                        <!-- Opción de Documento Cotejado -->
                        <div class="mb-4">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="documento_cotejado[{{ $documento['id'] }}]" 
                                       id="cotejado_{{ $documento['id'] }}"
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                       {{ (isset($documento['documento_cotejado']) && $documento['documento_cotejado']) ? 'checked' : '' }}>
                                <div class="flex items-center">
                                    <i class="fas fa-check-double text-green-600 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Documento cotejado físicamente</span>
                                </div>
                            </label>
                            <p class="text-xs text-gray-500 ml-7 mt-1">
                                Marcar cuando el documento físico ha sido verificado y cotejado correctamente
                            </p>
                        </div>
                        <!-- Campo de Comentario Individual -->
                        <div class="mb-4">
                            <label for="comentario_doc_{{ $documento['id'] }}" class="block text-sm font-medium text-gray-700 mb-2">
                                Comentario específico para este documento
                            </label>
                            <div class="py-3 px-4 bg-white rounded-lg border border-gray-200 shadow-sm relative">
                                <textarea id="comentario_doc_{{ $documento['id'] }}" 
                                          name="comentario_documento[{{ $documento['id'] }}]"
                                          rows="3"
                                          class="px-0 w-full text-sm text-gray-700 border-0 focus:ring-0 focus:outline-none bg-white resize-none placeholder-gray-400"
                                          placeholder="Comentarios específicos sobre este documento...">{{ $documento['comentario_revision'] ?? '' }}</textarea>
                            </div>
                        </div>
                        <!-- Botones de Acción por Documento -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button type="button" 
                                    onclick="aprobarDocumento({{ $documento['id'] }})"
                                    class="flex-1 inline-flex items-center justify-center py-2 px-3 text-xs font-medium text-white bg-gradient-to-r from-green-400 to-green-500 rounded-lg focus:ring-4 focus:ring-green-100 hover:from-green-500 hover:to-green-600 transition-colors duration-150">
                                <i class="fas fa-check mr-1"></i>
                                Aprobar Documento
                            </button>
                            <button type="button" 
                                    onclick="rechazarDocumento({{ $documento['id'] }})"
                                    class="flex-1 inline-flex items-center justify-center py-2 px-3 text-xs font-medium text-white bg-gradient-to-r from-rose-400 to-rose-500 rounded-lg focus:ring-4 focus:ring-rose-100 hover:from-rose-500 hover:to-rose-600 transition-colors duration-150">
                                <i class="fas fa-times mr-1"></i>
                                Rechazar Documento
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <!-- Mensaje cuando no hay documentos -->
                <div class="text-center py-8">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <i class="fas fa-exclamation-circle text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">No hay documentos adjuntos a este trámite.</p>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Vista editable normal -->
        <!-- Alert de Errores -->
        <div x-show="showError" x-cloak class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                <p class="text-red-700 text-sm" x-text="errorMessage"></p>
            </div>
        </div>
        <!-- Alert de Éxito -->
        <div x-show="showSuccess" x-cloak class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                <div class="text-green-700 text-sm" x-html="successMessage"></div>
            </div>
        </div>
        <!-- Lista de Documentos -->
        <div class="space-y-4">
            <template x-for="documento in documentos" :key="documento.id">
                <div class="bg-white border rounded-xl p-6 transition-all duration-300 group shadow-sm hover:shadow-lg"
                     :class="{
                         'border-green-400 bg-green-50 shadow-green-100': documento.estado === 'Aprobado',
                         'border-blue-400 bg-blue-50 shadow-blue-100': documento.estado === 'Pendiente' && documento.ruta_archivo,
                         'border-red-400 bg-red-50 shadow-red-100': documento.estado === 'Rechazado',
                         'border-gray-200 hover:border-[#9d2449]/40 hover:bg-gray-50': documento.estado === 'Pendiente' && !documento.ruta_archivo
                     }">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="relative">
                                <i class="fas fa-file-pdf text-2xl mr-3 group-hover:scale-110 transition-transform duration-300"
                                   :class="{
                                       'text-green-600': documento.estado === 'Aprobado',
                                       'text-blue-600': documento.estado === 'Pendiente' && documento.ruta_archivo,
                                       'text-red-600': documento.estado === 'Rechazado',
                                       'text-[#9d2449]': documento.estado === 'Pendiente' && !documento.ruta_archivo
                                   }"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900" x-text="documento.nombre"></h4>
                                <p class="text-xs text-gray-500" x-text="documento.descripcion || 'PDF, máximo 10MB'"></p>
                                <!-- Estado del documento -->
                                <div x-show="documento.estado !== 'Pendiente' || documento.ruta_archivo" class="flex items-center mt-1">
                                    <i :class="{
                                        'fas fa-check-circle text-green-500': documento.estado === 'Aprobado',
                                        'fas fa-clock text-blue-500': documento.estado === 'Pendiente' && documento.ruta_archivo,
                                        'fas fa-times-circle text-red-500': documento.estado === 'Rechazado'
                                    }" class="text-xs mr-1"></i>
                                    <span class="text-xs font-medium"
                                          :class="{
                                              'text-green-600': documento.estado === 'Aprobado',
                                              'text-blue-600': documento.estado === 'Pendiente' && documento.ruta_archivo,
                                              'text-red-600': documento.estado === 'Rechazado'
                                          }"
                                          x-text="documento.estado === 'Pendiente' && documento.ruta_archivo ? 'En Revisión' : documento.estado"></span>
                                </div>
                                <!-- Observaciones para documentos rechazados -->
                                <div x-show="documento.estado === 'Rechazado' && documento.observaciones" class="mt-1">
                                    <p class="text-xs text-red-600" x-text="documento.observaciones"></p>
                                </div>
                            </div>
                        </div>
                        <!-- Botón de selección para documentos pendientes sin archivo o rechazados -->
                        <div x-show="(documento.estado === 'Pendiente' && !documento.ruta_archivo) || documento.estado === 'Rechazado'">
                            <input type="file" 
                                   :name="`documento_${documento.id}`" 
                                   accept=".pdf"
                                   class="hidden" 
                                   :id="`documento_${documento.id}`"
                                   :aria-label="`Seleccionar archivo para ${documento.nombre}`"
                                   @change="handleFileSelect($event, documento)"
                                   required>
                            <label :for="`documento_${documento.id}`" 
                                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white rounded-lg text-sm font-medium hover:from-[#8a203f] hover:to-[#6d1a32] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] cursor-pointer transition-all duration-300 shadow-md hover:shadow-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>
                                    <span x-text="documento.estado === 'Rechazado' ? 'Subir Nuevo' : 'Seleccionar archivo'"></span>
                                </div>
                            </label>
                        </div>
                        <!-- Estado para documentos en revisión -->
                        <div x-show="documento.estado === 'Pendiente' && documento.ruta_archivo" class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                <i class="fas fa-clock mr-1"></i>
                                En Revisión
                            </span>
                            <button type="button" 
                                    @click="verDocumento(documento)"
                                    class="text-green-600 hover:text-green-800 text-xs underline transition-colors duration-200">
                                <i class="fas fa-eye mr-1"></i>
                                Ver
                            </button>
                            <button type="button" 
                                    @click="reemplazarDocumento(documento)"
                                    class="text-blue-600 hover:text-blue-800 text-xs underline transition-colors duration-200">
                                Reemplazar
                            </button>
                            <button type="button" 
                                    @click="verValidacionIA(documento)"
                                    x-show="documento.validacion_ia"
                                    class="text-purple-600 hover:text-purple-800 text-xs underline transition-colors duration-200"
                                    :class="{
                                        'text-green-600 hover:text-green-800': documento.validacion_ia && documento.validacion_ia.es_correcto,
                                        'text-red-600 hover:text-red-800': documento.validacion_ia && !documento.validacion_ia.es_correcto,
                                        'text-purple-600 hover:text-purple-800': documento.validacion_ia && documento.validacion_ia.es_correcto === null
                                    }"
                                    :title="`IA: ${documento.validacion_ia?.confianza_porcentaje || 'N/A'} de confianza`">
                                <i class="fas fa-brain mr-1"></i>
                                IA
                            </button>
                        </div>
                        <!-- Estado para documentos aprobados (NO se pueden reemplazar) -->
                        <div x-show="documento.estado === 'Aprobado'" class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                <i class="fas fa-check mr-1"></i>
                                Aprobado
                            </span>
                            <button type="button" 
                                    @click="verDocumento(documento)"
                                    class="text-green-600 hover:text-green-800 text-xs underline transition-colors duration-200">
                                <i class="fas fa-eye mr-1"></i>
                                Ver
                            </button>
                            <button type="button" 
                                    @click="verValidacionIA(documento)"
                                    x-show="documento.validacion_ia"
                                    class="text-purple-600 hover:text-purple-800 text-xs underline transition-colors duration-200"
                                    :class="{
                                        'text-green-600 hover:text-green-800': documento.validacion_ia && documento.validacion_ia.es_correcto,
                                        'text-red-600 hover:text-red-800': documento.validacion_ia && !documento.validacion_ia.es_correcto,
                                        'text-purple-600 hover:text-purple-800': documento.validacion_ia && documento.validacion_ia.es_correcto === null
                                    }"
                                    :title="`IA: ${documento.validacion_ia?.confianza_porcentaje || 'N/A'} de confianza`">
                                <i class="fas fa-brain mr-1"></i>
                                IA
                            </button>
                        </div>
                        <!-- Estado para documentos rechazados -->
                        <div x-show="documento.estado === 'Rechazado'" class="flex items-center">
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                <i class="fas fa-times mr-1"></i>
                                Rechazado
                            </span>
                        </div>
                    </div>
                    <!-- Preview del archivo seleccionado -->
                    <div x-show="documento.archivo_seleccionado && documento.estado !== 'Aprobado'" 
                         class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-file-pdf text-[#9d2449] mr-2"></i>
                                <span class="text-sm text-gray-900 font-medium" x-text="documento.nombre_archivo"></span>
                            </div>
                            <button type="button" 
                                    @click="removerArchivo(documento)"
                                    class="text-red-600 hover:text-red-800 transition-colors duration-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Análisis IA Avanzado (Vista Editable) -->
                    <div x-show="documento.validacion_ia && (documento.estado === 'Pendiente' || documento.estado === 'Aprobado')" 
                         class="mt-4 border rounded-xl p-4 transition-all duration-300 hover:shadow-md"
                         :class="{
                             'bg-emerald-50 border-emerald-200': documento.validacion_ia && documento.validacion_ia.es_correcto === true && documento.validacion_ia.confianza >= 0.90,
                             'bg-emerald-50 border-emerald-200': documento.validacion_ia && documento.validacion_ia.es_correcto === true && documento.validacion_ia.confianza >= 0.75,
                             'bg-amber-50 border-amber-200': documento.validacion_ia && documento.validacion_ia.es_correcto === true && documento.validacion_ia.confianza < 0.75,
                             'bg-red-50 border-red-200': documento.validacion_ia && documento.validacion_ia.es_correcto === false && documento.validacion_ia.confianza >= 0.85,
                             'bg-orange-50 border-orange-200': documento.validacion_ia && documento.validacion_ia.es_correcto === false && documento.validacion_ia.confianza >= 0.70,
                             'bg-slate-50 border-slate-200': documento.validacion_ia && (documento.validacion_ia.es_correcto === null || documento.validacion_ia.confianza < 0.70)
                         }">
                        <!-- Encabezado del análisis -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="p-1.5 bg-white rounded-lg shadow-sm">
                                    <i :class="{
                                        'fas fa-shield-check text-emerald-600': documento.validacion_ia && documento.validacion_ia.es_correcto === true && documento.validacion_ia.confianza >= 0.90,
                                        'fas fa-check-circle text-emerald-600': documento.validacion_ia && documento.validacion_ia.es_correcto === true && documento.validacion_ia.confianza >= 0.75,
                                        'fas fa-exclamation-triangle text-amber-600': documento.validacion_ia && documento.validacion_ia.es_correcto === true && documento.validacion_ia.confianza < 0.75,
                                        'fas fa-exclamation-circle text-red-600': documento.validacion_ia && documento.validacion_ia.es_correcto === false && documento.validacion_ia.confianza >= 0.85,
                                        'fas fa-exclamation-triangle text-orange-600': documento.validacion_ia && documento.validacion_ia.es_correcto === false && documento.validacion_ia.confianza >= 0.70,
                                        'fas fa-search text-slate-600': documento.validacion_ia && (documento.validacion_ia.es_correcto === null || documento.validacion_ia.confianza < 0.70)
                                    }" class="text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold"
                                        :class="{
                                            'text-emerald-700': documento.validacion_ia && documento.validacion_ia.es_correcto === true,
                                            'text-red-700': documento.validacion_ia && documento.validacion_ia.es_correcto === false,
                                            'text-slate-700': documento.validacion_ia && documento.validacion_ia.es_correcto === null
                                        }">🎨 Análisis Visual IA</h4>
                                    <p class="text-xs opacity-75"
                                       :class="{
                                           'text-emerald-700': documento.validacion_ia && documento.validacion_ia.es_correcto === true,
                                           'text-red-700': documento.validacion_ia && documento.validacion_ia.es_correcto === false,
                                           'text-slate-700': documento.validacion_ia && documento.validacion_ia.es_correcto === null
                                       }"
                                       x-text="getEstadoAnalisis(documento)"></p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-mono font-bold rounded-full"
                                      :class="{
                                          'bg-emerald-100 text-emerald-800': documento.validacion_ia && documento.validacion_ia.es_correcto === true,
                                          'bg-red-100 text-red-800': documento.validacion_ia && documento.validacion_ia.es_correcto === false,
                                          'bg-slate-100 text-slate-800': documento.validacion_ia && documento.validacion_ia.es_correcto === null
                                      }"
                                      x-text="documento.validacion_ia?.confianza_porcentaje || 'N/A'">
                                </span>
                                <div :class="{
                                    'w-2 h-2 bg-green-400 rounded-full animate-pulse': documento.validacion_ia && documento.validacion_ia.confianza >= 0.85,
                                    'w-2 h-2 bg-yellow-400 rounded-full animate-pulse': documento.validacion_ia && documento.validacion_ia.confianza >= 0.70 && documento.validacion_ia.confianza < 0.85,
                                    'w-2 h-2 bg-red-400 rounded-full animate-pulse': documento.validacion_ia && documento.validacion_ia.confianza < 0.70
                                }"></div>
                            </div>
                        </div>
                        <!-- Mensaje principal -->
                        <p class="text-sm mb-3 leading-relaxed"
                           :class="{
                               'text-emerald-700': documento.validacion_ia && documento.validacion_ia.es_correcto === true,
                               'text-red-700': documento.validacion_ia && documento.validacion_ia.es_correcto === false,
                               'text-slate-700': documento.validacion_ia && documento.validacion_ia.es_correcto === null
                           }"
                           x-text="getMensajeContextualIA(documento)">
                        </p>
                        <!-- Características detectadas (si están disponibles) -->
                        <div x-show="documento.validacion_ia?.caracteristicas_visuales" 
                             class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-3">
                            <!-- Tipo detectado -->
                            <div x-show="documento.validacion_ia?.prediccion" 
                                 class="flex items-center justify-between p-2 bg-white rounded-lg text-xs">
                                <span class="text-gray-600">📄 Tipo detectado:</span>
                                <span class="font-medium"
                                      :class="{
                                          'text-emerald-700': documento.validacion_ia && documento.validacion_ia.es_correcto === true,
                                          'text-red-700': documento.validacion_ia && documento.validacion_ia.es_correcto === false,
                                          'text-slate-700': documento.validacion_ia && documento.validacion_ia.es_correcto === null
                                      }"
                                      x-text="documento.validacion_ia?.prediccion"></span>
                            </div>
                            <!-- Características visuales dinámicas -->
                            <template x-if="documento.validacion_ia?.caracteristicas_visuales?.color_scheme">
                                <div class="flex items-center justify-between p-2 bg-white rounded-lg text-xs">
                                    <span class="text-gray-600">🎨 Esquema color:</span>
                                    <span class="font-medium" x-text="getColorSchemeDisplay(documento.validacion_ia.caracteristicas_visuales.color_scheme)"></span>
                                </div>
                            </template>
                            <template x-if="documento.validacion_ia?.caracteristicas_visuales?.has_logos">
                                <div class="flex items-center justify-between p-2 bg-white rounded-lg text-xs">
                                    <span class="text-gray-600">🏢 Logos oficiales:</span>
                                    <span class="text-green-600 font-medium">✓ Detectados</span>
                                </div>
                            </template>
                            <template x-if="documento.validacion_ia?.caracteristicas_visuales?.has_qr_code">
                                <div class="flex items-center justify-between p-2 bg-white rounded-lg text-xs">
                                    <span class="text-gray-600">📱 Código QR:</span>
                                    <span class="text-blue-600 font-medium">✓ Presente</span>
                                </div>
                            </template>
                        </div>
                        <!-- Información temporal -->
                        <div x-show="documento.validacion_ia?.procesado_en" 
                             class="text-xs opacity-75 border-t pt-2"
                             :class="{
                                 'text-emerald-700 border-emerald-200': documento.validacion_ia && documento.validacion_ia.es_correcto === true,
                                 'text-red-700 border-red-200': documento.validacion_ia && documento.validacion_ia.es_correcto === false,
                                 'text-slate-700 border-slate-200': documento.validacion_ia && documento.validacion_ia.es_correcto === null
                             }">
                            <span>⏱️ Procesado: </span><span x-text="documento.validacion_ia?.procesado_en"></span>
                            <span x-show="documento.validacion_ia?.tiempo_procesamiento" class="ml-3">⚡ </span>
                            <span x-show="documento.validacion_ia?.tiempo_procesamiento" x-text="documento.validacion_ia?.tiempo_procesamiento"></span>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Mensaje cuando no hay documentos -->
            <div x-show="documentos.length === 0" x-cloak class="text-center py-8">
                <div class="bg-gray-50 rounded-lg p-6">
                    <i class="fas fa-exclamation-circle text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500">No hay documentos configurados para este tipo de persona.</p>
                </div>
            </div>
        </div>
        <!-- Botones de navegación -->
        <div x-show="mostrarNavegacion" x-cloak class="flex justify-between pt-6 border-t border-gray-200 mt-8">
            <button type="button" 
                    onclick="navegarAnteriorDocumentos()"
                    class="flex items-center px-6 py-3 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Anterior
            </button>
            <button type="button" 
                    @click="finalizarTramite()"
                    :disabled="!todosDocumentosEnviados"
                    :class="!todosDocumentosEnviados ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#6d1a32]'"
                    class="flex items-center px-6 py-3 text-white rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                <span x-show="!finalizando">
                    Finalizar Trámite
                    <i class="fas fa-check ml-2"></i>
                </span>
                <span x-show="finalizando" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Finalizando...
                </span>
            </button>
        </div>
    @endif
</div>
<script>
function documentosData() {
    return {
        tramiteId: null,
        tipoPersona: 'Física',
        documentos: [],
        showError: false,
        errorMessage: '',
        showSuccess: false,
        successMessage: '',
        finalizando: false,
        mostrarNavegacion: @json($mostrar_navegacion ?? true),
        async init() {
            // Obtener tramite_id
            const tramite = @json($tramite ?? null);
            if (tramite && tramite.id) {
                this.tramiteId = tramite.id;
                this.tipoPersona = tramite.solicitante?.tipo_persona || 'Física';
                await this.cargarDocumentos();
            } else {
                await this.obtenerDatosTramite();
            }
        },
        async obtenerDatosTramite() {
            try {
                const response = await fetch('/tramites-solicitante/datos-tramite');
                const data = await response.json();
                if (data.success) {
                    this.tramiteId = data.tramite_id;
                    this.tipoPersona = data.tipo_persona;
                    await this.cargarDocumentos();
                } else {
                    this.mostrarError('No se pudo obtener información del trámite');
                }
            } catch (error) {
                this.mostrarError('Error al cargar información del trámite');
            }
        },
        async cargarDocumentos() {
            try {
                const response = await fetch('/tramites-solicitante/documentos');
                const data = await response.json();
                if (data.success && data.documentos) {
                    this.documentos = data.documentos.map(doc => ({
                        ...doc,
                        estado: doc.estado || 'Pendiente',
                        archivo_seleccionado: false,
                        nombre_archivo: '',
                        observaciones: doc.observaciones || null
                    }));
                } else {
                    this.documentos = [];
                }
            } catch (error) {
                this.mostrarError('Error al cargar los documentos');
            }
        },
        async handleFileSelect(event, documento) {
            const file = event.target.files[0];
            if (!file) return;
            // Actualizar estado del documento
            documento.archivo_seleccionado = true;
            documento.nombre_archivo = file.name;
            // Subir archivo
            await this.subirDocumento(documento, file);
        },
        async subirDocumento(documento, file) {
            // Validaciones del lado del cliente primero
            const erroresValidacion = this.validateArchivo(file);
            if (erroresValidacion.length > 0) {
                this.mostrarError(erroresValidacion.join('. '));
                documento.archivo_seleccionado = false;
                documento.nombre_archivo = '';
                return false;
            }
            
            try {
                const formData = new FormData();
                formData.append('archivo', file);
                formData.append('documento_id', documento.id);
                // Agregar CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }
                const response = await fetch('/tramites-solicitante/upload-documento', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    documento.estado = 'Pendiente';
                    documento.ruta_archivo = data.ruta;
                    documento.docSolicitanteId = data.docSolicitanteId;
                    documento.observaciones = null;
                    this.mostrarExito(data.mensaje || 'Documento subido correctamente');
                } else {
                    // Manejar errores de validación del servidor
                    if (data.errors) {
                        this.procesarErroresServidor(data.errors);
                        this.mostrarErroresValidacion(data.errors);
                    } else {
                        this.mostrarError(data.message || data.mensaje || 'Error al subir el documento');
                    }
                    documento.archivo_seleccionado = false;
                    documento.nombre_archivo = '';
                }
            } catch (error) {
                this.mostrarError('Error de conexión al subir el documento');
                documento.archivo_seleccionado = false;
                documento.nombre_archivo = '';
            }
        },
        reemplazarDocumento(documento) {
            // Solo permitir reemplazar si NO está aprobado
            if (documento.estado === 'Aprobado') {
                this.mostrarError('No se puede reemplazar un documento que ya ha sido aprobado');
                return;
            }
            documento.estado = 'Pendiente';
            documento.archivo_seleccionado = false;
            documento.nombre_archivo = '';
            documento.ruta_archivo = null;
            documento.observaciones = null;
            // Limpiar el input file
            const input = document.getElementById(`documento_${documento.id}`);
            if (input) input.value = '';
        },
        removerArchivo(documento) {
            documento.archivo_seleccionado = false;
            documento.nombre_archivo = '';
            // Limpiar el input file
            const input = document.getElementById(`documento_${documento.id}`);
            if (input) input.value = '';
        },
        get descripcionDocumentos() {
            return `Documentos necesarios para ${this.tipoPersona === 'Física' ? 'persona física' : 'persona moral'}`;
        },
        get todosDocumentosEnviados() {
            return this.documentos.length > 0 && this.documentos.every(doc => 
                doc.estado === 'Aprobado' || (doc.estado === 'Pendiente' && doc.ruta_archivo)
            );
        },
        mostrarError(mensaje) {
            this.errorMessage = mensaje;
            this.showError = true;
            this.showSuccess = false;
            setTimeout(() => {
                this.showError = false;
            }, 5000);
        },
        mostrarExito(mensaje) {
            this.successMessage = mensaje;
            this.showSuccess = true;
            this.showError = false;
            setTimeout(() => {
                this.showSuccess = false;
            }, 3000);
        },
        mostrarErroresValidacion(errores) {
            // Limpiar errores anteriores
            document.querySelectorAll('.error-message-documentos').forEach(el => el.remove());
            
            let mensajesError = [];
            
            // Procesar errores del servidor
            for (const [campo, mensajes] of Object.entries(errores)) {
                const mensaje = Array.isArray(mensajes) ? mensajes[0] : mensajes;
                mensajesError.push(mensaje);
            }
            
            // Mostrar errores en una alerta elegante
            if (mensajesError.length > 0) {
                this.mostrarAlertaErrores(mensajesError);
            }
        },
        mostrarAlertaErrores(errores) {
            const container = document.querySelector('[x-data*="documentosData"]');
            if (!container) return;
            
            const alerta = document.createElement('div');
            alerta.className = 'error-message-documentos mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm';
            
            let contenidoErrores = errores.map(error => 
                `<li class="text-sm text-red-700">${error}</li>`
            ).join('');
            
            alerta.innerHTML = `
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle mr-3 text-red-500 mt-1 flex-shrink-0"></i>
                    <div class="flex-1">
                        <h4 class="text-red-800 font-medium mb-2">Errores de validación</h4>
                        <ul class="space-y-1">${contenidoErrores}</ul>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" 
                            class="ml-2 text-red-400 hover:text-red-600 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            container.insertBefore(alerta, container.firstChild);
            
            // Auto-remover después de 8 segundos
            setTimeout(() => {
                if (alerta.parentNode) {
                    alerta.remove();
                }
            }, 8000);
        },
        async finalizarTramite() {
            this.finalizando = true;
            try {
                const formData = new FormData();
                formData.append('tramite_id', this.tramiteId);
                // Agregar CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }
                const response = await fetch('/tramites-solicitante/finalizar-tramite', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    // Mostrar mensaje de éxito y redirigir al estado del trámite
                    this.mostrarExito('¡Trámite enviado correctamente! Redirigiendo...');
                    // Esperar un momento para que se vea el mensaje y luego redirigir
                    setTimeout(() => {
                        if (typeof this.tramiteId !== 'number' && typeof this.tramiteId !== 'string') {
                            this.mostrarError('Error: ID de trámite inválido');
                            return;
                        }
                        window.location.href = `/tramites-solicitante/estado/${this.tramiteId}`;
                    }, 2000);
                } else {
                    this.mostrarError(data.message || 'Error al finalizar el trámite');
                }
            } catch (error) {
                this.mostrarError('Error de conexión al finalizar el trámite');
            } finally {
                this.finalizando = false;
            }
        },
        verDocumento(documento) {
            if (!documento.ruta_archivo || !this.tramiteId) {
                this.mostrarError('No se puede acceder al documento');
                return;
            }
            if (typeof this.tramiteId !== 'number' && typeof this.tramiteId !== 'string') {
                this.mostrarError('Error: ID de trámite inválido');
                return;
            }
            // Detectar si es móvil
            const esMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            if (esMobile) {
                // En móvil, forzar descarga
                const url = `/tramites-solicitante/ver-documento/${this.tramiteId}/${documento.id}?download=1`;
                window.location.href = url;
            } else {
                // En desktop, abrir en nueva pestaña
                const url = `/tramites-solicitante/ver-documento/${this.tramiteId}/${documento.id}`;
                window.open(url, '_blank');
            }
        },
        verValidacionIA(documento) {
            if (!documento.validacion_ia) {
                this.mostrarError('No hay información de validación IA disponible para este documento');
                return;
            }
            const validacion = documento.validacion_ia;
            const esCorrectoTexto = validacion.es_correcto === true ? '✅ Correcto' : 
                                   validacion.es_correcto === false ? '❌ Incorrecto' : '❓ Incierto';
            let mensaje = `🤖 <strong>Análisis de IA para "${documento.nombre}"</strong><br><br>`;
            mensaje += `<div class="space-y-2">`;
            mensaje += `<div class="flex justify-between items-center">`;
            mensaje += `<strong>Tipo Detectado:</strong> <span class="text-blue-600">${validacion.prediccion}</span>`;
            mensaje += `</div>`;
            mensaje += `<div class="flex justify-between items-center">`;
            mensaje += `<strong>Confianza:</strong> <span class="font-mono text-lg ${validacion.confianza >= 0.8 ? 'text-green-600' : validacion.confianza >= 0.6 ? 'text-yellow-600' : 'text-red-600'}">${validacion.confianza_porcentaje}</span>`;
            mensaje += `</div>`;
            mensaje += `<div class="flex justify-between items-center">`;
            mensaje += `<strong>Resultado:</strong> <span class="${validacion.es_correcto ? 'text-green-600' : 'text-red-600'}">${esCorrectoTexto}</span>`;
            mensaje += `</div>`;
            if (validacion.procesado_en) {
                mensaje += `<div class="flex justify-between items-center">`;
                mensaje += `<strong>Procesado:</strong> <span class="text-gray-600">${validacion.procesado_en}</span>`;
                mensaje += `</div>`;
            }
            if (validacion.tiempo_procesamiento) {
                mensaje += `<div class="flex justify-between items-center">`;
                mensaje += `<strong>Tiempo:</strong> <span class="text-gray-600">${validacion.tiempo_procesamiento}</span>`;
                mensaje += `</div>`;
            }
            mensaje += `</div>`;
            // Mostrar interpretación del resultado
            if (validacion.es_correcto === true) {
                mensaje += `<br><div class="p-3 bg-green-50 border border-green-200 rounded-lg">`;
                mensaje += `<p class="text-green-800 text-sm">🎯 <strong>Excelente:</strong> El documento subido corresponde exactamente al tipo esperado.</p>`;
                mensaje += `</div>`;
            } else if (validacion.es_correcto === false) {
                mensaje += `<br><div class="p-3 bg-red-50 border border-red-200 rounded-lg">`;
                mensaje += `<p class="text-red-800 text-sm">⚠️ <strong>Atención:</strong> El documento no parece corresponder al tipo esperado. Verifique que subió el archivo correcto.</p>`;
                mensaje += `</div>`;
            } else {
                mensaje += `<br><div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">`;
                mensaje += `<p class="text-yellow-800 text-sm">🤔 <strong>Revisión requerida:</strong> La IA no puede determinar con certeza el tipo de documento.</p>`;
                mensaje += `</div>`;
            }
            this.mostrarExito(mensaje);
        },
        getMensajeContextualIA(documento) {
            if (!documento.validacion_ia) {
                return '';
            }
            const validacion = documento.validacion_ia;
            const confianza = validacion.confianza || 0;
            const esCorrectoBoolean = validacion.es_correcto;
            // Mensaje mejorado basado en análisis visual
            if (esCorrectoBoolean === true) {
                if (confianza >= 0.90) {
                    return "Análisis visual y textual confirman que es el documento correcto";
                } else if (confianza >= 0.75) {
                    return "Las características visuales coinciden con el tipo esperado";
                } else {
                    return "Algunos elementos coinciden pero hay dudas menores";
                }
            } else if (esCorrectoBoolean === false) {
                const tipoPredicho = validacion.prediccion || 'desconocido';
                if (confianza >= 0.85) {
                    return `El análisis visual indica que es '${tipoPredicho}', no '${documento.nombre}'`;
                } else if (confianza >= 0.70) {
                    return "Las características no coinciden completamente con lo esperado";
                } else {
                    return "El análisis no puede determinar el tipo con certeza";
                }
            } else {
                return "Se requiere revisión manual del documento";
            }
        },
        getEstadoAnalisis(documento) {
            if (!documento.validacion_ia) {
                return 'Sin análisis';
            }
            const validacion = documento.validacion_ia;
            const confianza = validacion.confianza || 0;
            const esCorrectoBoolean = validacion.es_correcto;
            if (esCorrectoBoolean === true) {
                if (confianza >= 0.90) {
                    return "Documento Verificado";
                } else if (confianza >= 0.75) {
                    return "Muy Probable";
                } else {
                    return "Posible Coincidencia";
                }
            } else if (esCorrectoBoolean === false) {
                if (confianza >= 0.85) {
                    return "Error Detectado";
                } else if (confianza >= 0.70) {
                    return "Posible Error";
                } else {
                    return "Revisión Necesaria";
                }
            } else {
                return "Análisis Inconcluso";
            }
        },
        getColorSchemeDisplay(colorScheme) {
            const schemes = {
                'institutional_blue': '🏛️ Institucional',
                'government_green': '🏛️ Gubernamental', 
                'official_multicolor': '🌈 Oficial',
                'corporate_gray': '🏢 Corporativo',
                'standard_black': '⚫ Estándar',
                'security_red': '🛡️ Seguridad'
            };
            return schemes[colorScheme] || '⚫ Estándar';
        },
        // Validaciones del lado del cliente para documentos
        validateArchivo(archivo) {
            const errores = [];
            
            if (!archivo) {
                errores.push('Debe seleccionar un archivo para subir');
                return errores;
            }
            
            // Verificar tipo de archivo
            if (archivo.type !== 'application/pdf') {
                errores.push('Solo se permiten archivos en formato PDF');
            }
            
            // Verificar tamaño (máximo 10MB)
            const maxSize = 10 * 1024 * 1024; // 10MB en bytes
            if (archivo.size > maxSize) {
                errores.push('El archivo no puede ser mayor a 10 MB');
            }
            
            // Verificar tamaño mínimo (al menos 1KB)
            if (archivo.size < 1024) {
                errores.push('El archivo es demasiado pequeño. Debe tener al menos 1 KB');
            }
            
            // Verificar nombre del archivo
            if (archivo.name.length > 255) {
                errores.push('El nombre del archivo es demasiado largo. Máximo 255 caracteres');
            }
            
            // Verificar caracteres del nombre
            if (!/^[a-zA-Z0-9\s\.\-_\(\)]+\.pdf$/i.test(archivo.name)) {
                errores.push('El nombre del archivo contiene caracteres no permitidos. Use solo letras, números, espacios, guiones y paréntesis');
            }
            
            return errores;
        },
        
        validateComentarioRechazo(comentario) {
            const errores = [];
            
            if (!comentario || comentario.trim().length < 10) {
                errores.push('El comentario debe tener al menos 10 caracteres');
            }
            
            if (comentario && comentario.length > 1000) {
                errores.push('El comentario no puede exceder 1000 caracteres');
            }
            
            return errores; 
        },
        
        // Validar archivo antes de subir
        async validarYSubirArchivo(archivo, documentoId) {
            // Validaciones del lado del cliente primero
            const erroresValidacion = this.validateArchivo(archivo);
            
            if (erroresValidacion.length > 0) {
                this.mostrarError(erroresValidacion.join('. '));
                return false;
            }
            
            // Si pasa las validaciones del cliente, proceder con la subida
            return await this.subirDocumento(archivo, documentoId);
        },
        
        // Mostrar errores de validación específicos
        mostrarErrorValidacion(campo, mensaje) {
            // Buscar el campo específico
            const campoElement = document.querySelector(`[name="${campo}"], #${campo}`);
            if (campoElement) {
                // Agregar clase de error
                campoElement.classList.add('border-red-500', 'bg-red-50');
                
                // Buscar o crear contenedor de error
                let errorContainer = campoElement.parentElement.querySelector('.validation-error');
                if (!errorContainer) {
                    errorContainer = document.createElement('div');
                    errorContainer.className = 'validation-error mt-2 p-3 bg-red-50 border border-red-200 rounded-lg';
                    campoElement.parentElement.appendChild(errorContainer);
                }
                
                errorContainer.innerHTML = `
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                        <span class="text-sm text-red-700">${mensaje}</span>
                    </div>
                `;
            }
        },
        
        // Limpiar errores de validación
        limpiarErroresValidacion() {
            // Remover clases de error
            const camposConError = document.querySelectorAll('.border-red-500');
            camposConError.forEach(campo => {
                campo.classList.remove('border-red-500', 'bg-red-50');
            });
            
            // Remover contenedores de error
            const contenedoresError = document.querySelectorAll('.validation-error');
            contenedoresError.forEach(contenedor => contenedor.remove());
        },
        
        // Procesar errores del servidor
        procesarErroresServidor(errores) {
            if (typeof errores === 'object') {
                Object.keys(errores).forEach(campo => {
                    const mensajes = Array.isArray(errores[campo]) ? errores[campo] : [errores[campo]];
                    this.mostrarErrorValidacion(campo, mensajes[0]);
                });
            }
        }
    }
}
</script>
<script>
// Función para navegar al paso anterior desde documentos
function navegarAnteriorDocumentos() {
    // Método 1: Función global navegarAnterior
    if (typeof window.navegarAnterior === 'function') {
        window.navegarAnterior();
        return;
    }
    // Método 2: Buscar contenedor Alpine.js y retroceder
    const alpineContainer = document.querySelector('[x-data*="currentStep"]');
    if (alpineContainer && typeof Alpine !== 'undefined') {
        try {
            const alpineData = Alpine.$data(alpineContainer);
            if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                if (alpineData.currentStep > 1) {
                    alpineData.currentStep--;
                    return;
                } else {
                    return;
                }
            }
        } catch (error) {
        }
    }
    // Método 3: Disparar evento personalizado en el contenedor
    if (alpineContainer) {
        alpineContainer.dispatchEvent(new CustomEvent('previous-step'));
        return;
    }
    // Método 4: Buscar directamente botones de navegación en el documento
    const prevButtons = document.querySelectorAll('button[onclick*="currentStep--"], button[x-text*="Anterior"]');
    if (prevButtons.length > 0) {
        prevButtons[0].click();
        return;
    }
    // Fallback: intentar manipular directamente
    const stepContainers = document.querySelectorAll('[x-show*="currentStep"]');
    if (stepContainers.length > 0) {
        // Buscar el contenedor activo
        for (let container of stepContainers) {
            if (container.style.display !== 'none' && !container.hasAttribute('hidden')) {
                // Intentar acceder al contexto Alpine
                try {
                    const parentWithData = container.closest('[x-data]');
                    if (parentWithData && Alpine && Alpine.$data) {
                        const data = Alpine.$data(parentWithData);
                        if (data && data.currentStep && data.currentStep > 1) {
                            data.currentStep--;
                            return;
                        }
                    }
                } catch (error) {
                }
            }
        }
    }
}
</script>
@push('styles')
<style>
/* Mejora en la animación de bounce */
@keyframes custom-bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}
.custom-bounce {
    animation: custom-bounce 1s ease-in-out infinite;
}
</style>
@endpush
@push('scripts')
@endpush
