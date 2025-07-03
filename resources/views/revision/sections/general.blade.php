{{-- Sección General: Resumen del Trámite --}}
<div class="revision-section" data-seccion="general">
    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Información General del Trámite</h3>
                <p class="text-sm text-gray-500">Resumen y estado actual del proceso</p>
            </div>
        </div>
    </div>

    {{-- Resumen del Trámite --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Información Básica --}}
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
            <h4 class="text-lg font-medium text-blue-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707L16.586 6.293A1 1 0 0016.172 6H4a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                </svg>
                Información del Trámite
            </h4>
            
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-blue-700">ID del Trámite</label>
                    <p class="text-lg font-mono font-bold text-blue-900">
                        #{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-blue-700">Tipo de Trámite</label>
                    <p class="text-sm text-blue-900 bg-white px-3 py-2 rounded border">
                        {{ $tramite->tipo_tramite ?? 'Inscripción' }}
                    </p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-blue-700">Fecha de Solicitud</label>
                    <p class="text-sm text-blue-900 bg-white px-3 py-2 rounded border">
                        {{ $tramite->created_at ? $tramite->created_at->format('d/m/Y H:i') : 'No especificada' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Estado del Trámite --}}
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
            <h4 class="text-lg font-medium text-green-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Estado Actual
            </h4>
            
            @php
                $estado = $tramite->estado ?? 'Pendiente';
                $estadoInfo = [
                    'Pendiente' => ['color' => 'yellow', 'icon' => '⏳', 'description' => 'En espera de revisión'],
                    'En Revision' => ['color' => 'blue', 'icon' => '🔍', 'description' => 'Siendo revisado por el equipo'],
                    'Aprobado' => ['color' => 'green', 'icon' => '✅', 'description' => 'Trámite aprobado exitosamente'],
                    'Rechazado' => ['color' => 'red', 'icon' => '❌', 'description' => 'Trámite rechazado'],
                    'Correccion' => ['color' => 'orange', 'icon' => '📝', 'description' => 'Requiere correcciones']
                ];
                $info = $estadoInfo[$estado] ?? $estadoInfo['Pendiente'];
            @endphp
            
            <div class="text-center">
                <div class="text-4xl mb-2">{{ $info['icon'] }}</div>
                <p class="text-xl font-bold text-{{ $info['color'] }}-800 mb-2">{{ $estado }}</p>
                <p class="text-sm text-{{ $info['color'] }}-700">{{ $info['description'] }}</p>
            </div>
            
            @if($tramite->updated_at)
                <div class="mt-4 pt-4 border-t border-green-200">
                    <p class="text-xs text-green-600">
                        Última actualización: {{ $tramite->updated_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Información del Solicitante --}}
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
            <h4 class="text-lg font-medium text-purple-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Solicitante
            </h4>
            
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-purple-700">RFC</label>
                    <p class="text-sm font-mono text-purple-900 bg-white px-3 py-2 rounded border">
                        {{ $datosSolicitante['rfc'] ?? 'No especificado' }}
                    </p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-purple-700">Tipo de Persona</label>
                    <p class="text-sm text-purple-900 bg-white px-3 py-2 rounded border">
                        {{ $datosSolicitante['tipo_persona'] ?? 'No especificado' }}
                    </p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-purple-700">
                        @if(($datosSolicitante['tipo_persona'] ?? '') === 'Física')
                            Nombre Completo
                        @else
                            Razón Social
                        @endif
                    </label>
                    <p class="text-sm text-purple-900 bg-white px-3 py-2 rounded border">
                        @if(($datosSolicitante['tipo_persona'] ?? '') === 'Física')
                            {{ $datosSolicitante['nombre_completo'] ?? 'No especificado' }}
                        @else
                            {{ $datosSolicitante['razon_social'] ?? 'No especificado' }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Progreso de Revisión --}}
    <div class="bg-gray-50 rounded-xl p-6 mb-8">
        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Progreso de Revisión por Secciones
        </h4>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Secciones dinámicas basadas en tipo de persona --}}
            @php
                $secciones = [
                    'datos-generales' => 'Datos Generales',
                    'domicilio' => 'Domicilio',
                    'documentos' => 'Documentos'
                ];
                
                if($tramite->solicitante && strtolower($tramite->solicitante->tipo_persona) === 'moral') {
                    $secciones['constitucion'] = 'Constitución';
                    $secciones['accionistas'] = 'Accionistas';
                    $secciones['apoderado'] = 'Apoderado';
                }
            @endphp
            
            @foreach($secciones as $key => $nombre)
                <div class="text-center p-4 bg-white rounded-lg border">
                    <div class="w-12 h-12 mx-auto mb-2 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span class="text-yellow-600 font-bold">⏳</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ $nombre }}</p>
                    <p class="text-xs text-gray-500">Pendiente</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Instrucciones para el Revisor --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h4 class="text-lg font-medium text-blue-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Instrucciones para la Revisión
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h5 class="font-medium text-blue-800 mb-2">📋 Proceso de Revisión</h5>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Revise cada sección usando las pestañas superiores</li>
                    <li>• Verifique la información contra los documentos</li>
                    <li>• Use el panel lateral para ver documentos</li>
                    <li>• Agregue comentarios específicos en cada sección</li>
                </ul>
            </div>
            
            <div>
                <h5 class="font-medium text-blue-800 mb-2">✅ Estados de Revisión</h5>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• <span class="font-medium">Aprobado:</span> Información correcta</li>
                    <li>• <span class="font-medium">Rechazado:</span> Información incorrecta</li>
                    <li>• <span class="font-medium">Corrección:</span> Requiere modificaciones</li>
                    <li>• <span class="font-medium">Comentario:</span> Solo observaciones</li>
                </ul>
            </div>
        </div>
    </div>
</div> 