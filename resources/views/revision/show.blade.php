@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header de Revisión -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('revision.index') }}" 
                       class="h-8 w-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-[#9d2449] to-[#7a1d37] text-white">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                            Revisión #{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}
                        </h1>
                        <p class="text-xs text-gray-500">
                            {{ ucfirst($tramite->tipo_tramite) }} • 
                            {{ $tramite->solicitante->razon_social ?? $tramite->solicitante->nombre_completo ?? 'Sin información' }}
                        </p>
                    </div>
                </div>
                
                <!-- Estado y Acciones -->
                <div class="flex items-center space-x-3">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                        {{ $tramite->estado == 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                           ($tramite->estado == 'En Revision' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ $tramite->estado }}
                    </span>
                    
                    <!-- Botones de Acción -->
                    <div class="flex items-center space-x-2">
                        <!-- Aprobar Todo -->
                        <form method="POST" action="{{ route('revision.aprobar-todo', $tramite->id) }}" style="display: inline;" 
                              onsubmit="return confirm('¿Está seguro de que desea aprobar todo el trámite?')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                <i class="fas fa-check mr-1 text-xs"></i>Aprobar Todo
                            </button>
                        </form>
                        
                        <!-- Rechazar Todo -->
                        <button type="button" onclick="document.getElementById('modalRechazarTodo').classList.remove('hidden')" 
                                class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-times mr-1 text-xs"></i>Rechazar Todo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes Flash -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 mb-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-emerald-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-emerald-800">Éxito</h3>
                        <div class="mt-2 text-sm text-emerald-700">
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-amber-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-amber-800">Errores de validación</h3>
                        <div class="mt-2 text-sm text-amber-700">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Secciones de Revisión -->
        <div class="space-y-6">
            <!-- 01. Datos Generales -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-8 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-12 h-12 flex items-center justify-center text-base font-bold mr-4">01</span>
                            <div class="flex items-center">
                                <i class="fas fa-user-circle mr-3 text-2xl"></i>
                                <span>Datos Generales</span>
                            </div>
                        </h2>
                        <div class="flex items-center space-x-4">
                            @php
                                $estado = $revisionesExistentes[1]['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-4 py-2 rounded-full border text-sm font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-2 text-lg"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                            
                            <!-- Documentos de la sección -->
                            @if(isset($documentosPorSeccion['datos_generales']) && count($documentosPorSeccion['datos_generales']) > 0)
                                <div class="flex items-center space-x-3">
                                    @foreach($documentosPorSeccion['datos_generales'] as $documento)
                                        <div class="tooltip-container tooltip-pdf">
                                        <button onclick="mostrarDocumento('datos_generales', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
<<<<<<< HEAD
                                                   class="documento-btn bg-blue-100 hover:bg-blue-200 text-blue-700 p-2 rounded-lg border border-blue-300"
                                                   data-doc-seccion="datos_generales" 
                                                   data-doc-ruta="{{ $documento['ruta_archivo'] }}" 
                                                   data-doc-nombre="{{ $documento['nombre'] }}"
                                                   oncontextmenu="seleccionarParaComparar(event, 'datos_generales', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                   title="Clic izquierdo: Ver | Clic derecho: Seleccionar para comparar">
                                            <i class="fas fa-file-pdf text-sm"></i>
=======
                                               class="bg-blue-100 hover:bg-blue-200 text-blue-700 p-3 rounded-xl transition-all duration-200 border border-blue-300 relative z-50 hover:scale-105"
                                               title="{{ $documento['nombre'] }} - {{ $documento['descripcion'] }}">
                                            <i class="fas fa-file-pdf text-base"></i>
>>>>>>> 7da0074dcfac1c8bf40176272177a4fa8c3b0daa
                                        </button>
                                            <div class="custom-tooltip">
                                                <div class="font-medium">{{ $documento['nombre'] }}</div>
                                                <div class="text-xs opacity-75 mt-1">Clic izquierdo: Ver | Clic derecho: Comparar</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <span class="text-sm font-medium text-gray-600">{{ count($documentosPorSeccion['datos_generales']) }} doc(s)</span>
                                </div>
                            @else
                                <span class="text-sm text-gray-400 italic">Sin documentos</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Contenido dinámico: Formulario completo o dividido -->
                <div id="contenido-datos_generales">
                    <!-- Inicialmente solo el formulario -->
                    <div class="p-8">
                        @include('components.formularios.seccion-datos-generales', [
                            'datosTramite' => $datosTramite,
                            'datosSolicitante' => $tramite->solicitante ? [
                                'rfc' => $tramite->solicitante->rfc ?? $datosTramite['rfc'] ?? '',
                                'curp' => $tramite->solicitante->curp ?? $datosTramite['curp'] ?? '',
                                'tipo_persona' => $tramite->solicitante->tipo_persona ?? $datosTramite['tipo_persona'] ?? 'Física',
                                'nombre_completo' => $tramite->solicitante->nombre_completo ?? $datosTramite['nombre_completo'] ?? '',
                                'razon_social' => $tramite->solicitante->razon_social ?? $datosTramite['razon_social'] ?? '',
                                'giro' => $tramite->solicitante->giro ?? $datosTramite['giro'] ?? ''
                            ] : [],
                            'readonly' => true
                        ])
                        
                        <!-- Panel de revisión compacto -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 shadow-sm">
                                <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                    <i class="fas fa-clipboard-check text-[#9d2449] mr-2 text-xl"></i>
                                    Revisión
                                </h4>
                                
                                <!-- Formularios compactos -->
                                <div class="flex flex-col sm:flex-row gap-6">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 1]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-4">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                      class="w-full text-sm rounded-xl border-gray-300 focus:border-green-400 focus:ring-2 focus:ring-green-200 resize-none px-4 py-3 leading-relaxed shadow-sm" 
                                                      rows="3">{{ $revisionesExistentes[1]['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-4 py-3 text-sm font-medium bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all duration-150 flex items-center justify-center shadow-sm hover:shadow-md">
                                                <i class="fas fa-check mr-2 text-base"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 1]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-4">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                      class="w-full text-sm rounded-xl border-gray-300 focus:border-red-400 focus:ring-2 focus:ring-red-200 resize-none px-4 py-3 leading-relaxed shadow-sm" 
                                                      rows="3" required>{{ $estado === 'rechazado' ? ($revisionesExistentes[1]['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-4 py-3 text-sm font-medium bg-rose-500 text-white rounded-xl hover:bg-rose-600 transition-all duration-150 flex items-center justify-center shadow-sm hover:shadow-md">
                                                <i class="fas fa-times mr-2 text-base"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 02. Domicilio -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-8 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-12 h-12 flex items-center justify-center text-base font-bold mr-4">02</span>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-3 text-2xl"></i>
                                <span>Domicilio</span>
                            </div>
                        </h2>
                        <div class="flex items-center space-x-4">
                            @php
                                $estado = $revisionesExistentes[2]['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-4 py-2 rounded-full border text-sm font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-2 text-lg"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                            
                            <!-- Documentos de la sección y Mapa -->
                            <div class="flex items-center space-x-4">
                                @if(isset($documentosPorSeccion['domicilio']) && count($documentosPorSeccion['domicilio']) > 0)
                                    <div class="flex items-center space-x-3">
                                        @foreach($documentosPorSeccion['domicilio'] as $documento)
                                            <div class="tooltip-container tooltip-general">
                                            <button onclick="mostrarDocumento('domicilio', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
<<<<<<< HEAD
                                                       class="documento-btn bg-green-100 hover:bg-green-200 text-green-700 p-2 rounded-lg border border-green-300"
                                                       data-doc-seccion="domicilio" 
                                                       data-doc-ruta="{{ $documento['ruta_archivo'] }}" 
                                                       data-doc-nombre="{{ $documento['nombre'] }}"
                                                       oncontextmenu="seleccionarParaComparar(event, 'domicilio', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                       title="Clic izquierdo: Ver | Clic derecho: Seleccionar para comparar">
                                                <i class="fas fa-file-pdf text-sm"></i>
=======
                                                   class="bg-green-100 hover:bg-green-200 text-green-700 p-3 rounded-xl transition-all duration-200 border border-green-300 relative z-50 hover:scale-105"
                                                   title="{{ $documento['nombre'] }} - {{ $documento['descripcion'] }}">
                                                <i class="fas fa-file-pdf text-base"></i>
>>>>>>> 7da0074dcfac1c8bf40176272177a4fa8c3b0daa
                                            </button>
                                                <div class="custom-tooltip">
                                                    <div class="font-medium">{{ $documento['nombre'] }}</div>
                                                    <div class="text-xs opacity-75 mt-1">Clic izquierdo: Ver | Clic derecho: Comparar</div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <span class="text-sm font-medium text-gray-600">{{ count($documentosPorSeccion['domicilio']) }} doc(s)</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Sin documentos</span>
                                @endif
                                
                                <!-- Botón del Mapa -->
                                <div class="tooltip-container tooltip-map">
                                <button onclick="mostrarMapa('domicilio')"
<<<<<<< HEAD
                                           class="documento-btn bg-blue-100 hover:bg-blue-200 text-blue-700 p-2 rounded-lg border border-blue-300">
                                    <i class="fas fa-map-marker-alt text-sm"></i>
=======
                                       class="bg-blue-100 hover:bg-blue-200 text-blue-700 p-3 rounded-xl transition-all duration-200 border border-blue-300 relative z-50 hover:scale-105"
                                       title="Ver ubicación en mapa">
                                    <i class="fas fa-map-marker-alt text-base"></i>
>>>>>>> 7da0074dcfac1c8bf40176272177a4fa8c3b0daa
                                </button>
                                    <div class="custom-tooltip">
                                        <div class="font-medium">Ver Ubicación</div>
                                        <div class="text-xs opacity-90 mt-1">Mapa interactivo con análisis de zona</div>
                                        <div class="text-xs opacity-75 mt-1">Clic para abrir mapa</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contenido dinámico: Formulario completo o dividido -->
                <div id="contenido-domicilio">
                    <!-- Inicialmente solo el formulario -->
                    <div class="p-8">
                        @include('components.formularios.seccion-domicilio', [
                            'datosDomicilio' => $datosDomicilio,
                            'readonly' => true
                        ])
                        
                        <!-- Panel de revisión compacto -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 shadow-sm">
                                <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                    <i class="fas fa-clipboard-check text-[#9d2449] mr-2 text-xl"></i>
                                    Revisión
                                </h4>
                                
                                <!-- Formularios compactos -->
                                <div class="flex flex-col sm:flex-row gap-6">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 2]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-4">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                      class="w-full text-sm rounded-xl border-gray-300 focus:border-green-400 focus:ring-2 focus:ring-green-200 resize-none px-4 py-3 leading-relaxed shadow-sm" 
                                                      rows="3">{{ $revisionesExistentes[2]['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-4 py-3 text-sm font-medium bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all duration-150 flex items-center justify-center shadow-sm hover:shadow-md">
                                                <i class="fas fa-check mr-2 text-base"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 2]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-4">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                      class="w-full text-sm rounded-xl border-gray-300 focus:border-red-400 focus:ring-2 focus:ring-red-200 resize-none px-4 py-3 leading-relaxed shadow-sm" 
                                                      rows="3" required>{{ $estado === 'rechazado' ? ($revisionesExistentes[2]['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-4 py-3 text-sm font-medium bg-rose-500 text-white rounded-xl hover:bg-rose-600 transition-all duration-150 flex items-center justify-center shadow-sm hover:shadow-md">
                                                <i class="fas fa-times mr-2 text-base"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($tramite->solicitante && strtolower($tramite->solicitante->tipo_persona) === 'moral')
            <!-- 03. Constitución -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-gray-100 to-gray-200 px-4 py-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-700 flex items-center">
                            <span class="bg-[#9d2449] text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold mr-3">03</span>
                            <div class="flex items-center">
                                <i class="fas fa-building mr-2 text-lg text-[#9d2449]"></i>
                                <span>Constitución</span>
                            </div>
                        </h2>
                        <div class="flex items-center space-x-3">
                            @php
                                $estado = $revisionesExistentes[3]['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-2 py-1 rounded-full border text-xs font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-1 text-xs"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                            
                            <!-- Documentos de la sección -->
                            @if(isset($documentosPorSeccion['constitucion']) && count($documentosPorSeccion['constitucion']) > 0)
                                <div class="flex items-center space-x-1">
                                    @foreach($documentosPorSeccion['constitucion'] as $documento)
                                        <div class="tooltip-container tooltip-pdf">
                                        <button onclick="mostrarDocumento('constitucion', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                   class="documento-btn bg-purple-100 hover:bg-purple-200 text-purple-700 p-2 rounded-lg border border-purple-300"
                                                   data-doc-seccion="constitucion" 
                                                   data-doc-ruta="{{ $documento['ruta_archivo'] }}" 
                                                   data-doc-nombre="{{ $documento['nombre'] }}"
                                                   oncontextmenu="seleccionarParaComparar(event, 'constitucion', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                   title="Clic izquierdo: Ver | Clic derecho: Seleccionar para comparar">
                                            <i class="fas fa-file-pdf text-sm"></i>
                                        </button>
                                            <div class="custom-tooltip">
                                                <div class="font-medium">{{ $documento['nombre'] }}</div>
                                                <div class="text-xs opacity-75 mt-1">Clic izquierdo: Ver | Clic derecho: Comparar</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <span class="text-xs text-gray-500 ml-2">{{ count($documentosPorSeccion['constitucion']) }} doc(s)</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Sin documentos</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Contenido dinámico: Formulario completo o dividido -->
                <div id="contenido-constitucion">
                    <!-- Inicialmente solo el formulario -->
                    <div class="p-4">
                        @include('components.formularios.seccion-constitucion', [
                            'datosConstitucion' => $constitucion,
                            'readonly' => true
                        ])
                        
                        <!-- Panel de revisión compacto -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-clipboard-check text-[#9d2449] mr-2 text-xs"></i>
                                    Revisión
                                </h4>
                                
                                <!-- Formularios compactos -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 3]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-2">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-3 py-2 leading-relaxed" 
                                                      rows="3">{{ $revisionesExistentes[3]['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-3 py-2 text-xs font-medium bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center">
                                                <i class="fas fa-check mr-1"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 3]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-2">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-3 py-2 leading-relaxed" 
                                                      rows="3" required>{{ $estado === 'rechazado' ? ($revisionesExistentes[3]['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-3 py-2 text-xs font-medium bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center">
                                                <i class="fas fa-times mr-1"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 04. Accionistas -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-gray-100 to-gray-200 px-4 py-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-700 flex items-center">
                            <span class="bg-[#9d2449] text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold mr-3">04</span>
                            <div class="flex items-center">
                                <i class="fas fa-users mr-2 text-lg text-[#9d2449]"></i>
                                <span>Accionistas</span>
                            </div>
                        </h2>
                        <div class="flex items-center space-x-3">
                            @php
                                $estado = $revisionesExistentes[4]['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-2 py-1 rounded-full border text-xs font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-1 text-xs"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                            
                            <!-- Documentos de la sección -->
                            @if(isset($documentosPorSeccion['accionistas']) && count($documentosPorSeccion['accionistas']) > 0)
                                <div class="flex items-center space-x-1">
                                    @foreach($documentosPorSeccion['accionistas'] as $documento)
                                        <div class="tooltip-container tooltip-general">
                                        <button onclick="mostrarDocumento('accionistas', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                   class="documento-btn bg-amber-100 hover:bg-amber-200 text-amber-700 p-2 rounded-lg border border-amber-300"
                                                   data-doc-seccion="accionistas" 
                                                   data-doc-ruta="{{ $documento['ruta_archivo'] }}" 
                                                   data-doc-nombre="{{ $documento['nombre'] }}"
                                                   oncontextmenu="seleccionarParaComparar(event, 'accionistas', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                   title="Clic izquierdo: Ver | Clic derecho: Seleccionar para comparar">
                                            <i class="fas fa-file-pdf text-sm"></i>
                                        </button>
                                            <div class="custom-tooltip">
                                                <div class="font-medium">{{ $documento['nombre'] }}</div>
                                                <div class="text-xs opacity-75 mt-1">Clic izquierdo: Ver | Clic derecho: Comparar</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <span class="text-xs text-gray-500 ml-2">{{ count($documentosPorSeccion['accionistas']) }} doc(s)</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Sin documentos</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Contenido dinámico: Formulario completo o dividido -->
                <div id="contenido-accionistas">
                    <!-- Inicialmente solo el formulario -->
                    <div class="p-4">
                        @include('components.formularios.seccion-accionistas', [
                            'accionistas' => $accionistas,
                            'readonly' => true
                        ])
                        
                        <!-- Panel de revisión compacto -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-clipboard-check text-[#9d2449] mr-2 text-xs"></i>
                                    Revisión
                                </h4>
                                
                                <!-- Formularios compactos -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 4]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-2">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-3 py-2 leading-relaxed" 
                                                      rows="3">{{ $revisionesExistentes[4]['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-3 py-2 text-xs font-medium bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center">
                                                <i class="fas fa-check mr-1"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 4]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-2">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-3 py-2 leading-relaxed" 
                                                      rows="3" required>{{ $estado === 'rechazado' ? ($revisionesExistentes[4]['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-3 py-2 text-xs font-medium bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center">
                                                <i class="fas fa-times mr-1"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 05. Apoderado Legal -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-gray-100 to-gray-200 px-4 py-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-700 flex items-center">
                            <span class="bg-[#9d2449] text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold mr-3">05</span>
                            <div class="flex items-center">
                                <i class="fas fa-user-tie mr-2 text-lg text-[#9d2449]"></i>
                                <span>Apoderado Legal</span>
                            </div>
                        </h2>
                        <div class="flex items-center space-x-3">
                            @php
                                $estado = $revisionesExistentes[5]['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-2 py-1 rounded-full border text-xs font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-1 text-xs"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                            
                            <!-- Documentos de la sección -->
                            @if(isset($documentosPorSeccion['apoderado']) && count($documentosPorSeccion['apoderado']) > 0)
                                <div class="flex items-center space-x-1">
                                    @foreach($documentosPorSeccion['apoderado'] as $documento)
                                        <div class="tooltip-container tooltip-pdf">
                                        <button onclick="mostrarDocumento('apoderado', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                   class="documento-btn bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-lg border border-red-300"
                                                   data-doc-seccion="apoderado" 
                                                   data-doc-ruta="{{ $documento['ruta_archivo'] }}" 
                                                   data-doc-nombre="{{ $documento['nombre'] }}"
                                                   oncontextmenu="seleccionarParaComparar(event, 'apoderado', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                   title="Clic izquierdo: Ver | Clic derecho: Seleccionar para comparar">
                                            <i class="fas fa-file-pdf text-sm"></i>
                                        </button>
                                            <div class="custom-tooltip">
                                                <div class="font-medium">{{ $documento['nombre'] }}</div>
                                                <div class="text-xs opacity-75 mt-1">Clic izquierdo: Ver | Clic derecho: Comparar</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <span class="text-xs text-gray-500 ml-2">{{ count($documentosPorSeccion['apoderado']) }} doc(s)</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Sin documentos</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Contenido dinámico: Formulario completo o dividido -->
                <div id="contenido-apoderado">
                    <!-- Inicialmente solo el formulario -->
                    <div class="p-4">
                        @include('components.formularios.seccion-apoderado', [
                            'datosApoderado' => $apoderado,
                            'readonly' => true
                        ])
                        
                        <!-- Panel de revisión compacto -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-clipboard-check text-[#9d2449] mr-2 text-xs"></i>
                                    Revisión
                                </h4>
                                
                                <!-- Formularios compactos -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 5]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-2">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-3 py-2 leading-relaxed" 
                                                      rows="3">{{ $revisionesExistentes[5]['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-3 py-2 text-xs font-medium bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center">
                                                <i class="fas fa-check mr-1"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 5]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-2">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-3 py-2 leading-relaxed" 
                                                      rows="3" required>{{ $estado === 'rechazado' ? ($revisionesExistentes[5]['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-3 py-2 text-xs font-medium bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center">
                                                <i class="fas fa-times mr-1"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- 06. Documentos -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-gray-100 to-gray-200 px-4 py-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-700 flex items-center">
                            <span class="bg-[#9d2449] text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold mr-3">06</span>
                            <div class="flex items-center">
                                <i class="fas fa-folder mr-2 text-lg text-[#9d2449]"></i>
                                <span>Documentos</span>
                            </div>
                        </h2>
                        <div class="flex items-center space-x-3">
                            @php
                                $estado = $revisionesExistentes[6]['estado'] ?? 'pendiente';
                                $statusClass = $estado === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                              ($estado === 'rechazado' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200');
                                $iconClass = $estado === 'aprobado' ? 'fas fa-check-circle text-emerald-500' : 
                                            ($estado === 'rechazado' ? 'fas fa-times-circle text-rose-500' : 'fas fa-clock text-amber-500');
                            @endphp
                            <div class="flex items-center px-2 py-1 rounded-full border text-xs font-medium {{ $statusClass }}">
                                <i class="{{ $iconClass }} mr-1 text-xs"></i>
                                <span>{{ ucfirst($estado) }}</span>
                            </div>
                            
                            <!-- Documentos de la sección -->
                            @if(isset($documentosPorSeccion['documentos']) && count($documentosPorSeccion['documentos']) > 0)
                                <div class="flex items-center space-x-1">
                                    @foreach($documentosPorSeccion['documentos'] as $documento)
                                        <div class="tooltip-container tooltip-general">
                                        <button onclick="mostrarDocumento('documentos', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                   class="documento-btn bg-indigo-100 hover:bg-indigo-200 text-indigo-700 p-2 rounded-lg border border-indigo-300"
                                                   data-doc-seccion="documentos" 
                                                   data-doc-ruta="{{ $documento['ruta_archivo'] }}" 
                                                   data-doc-nombre="{{ $documento['nombre'] }}"
                                                   oncontextmenu="seleccionarParaComparar(event, 'documentos', '{{ $documento['ruta_archivo'] }}', '{{ $documento['nombre'] }}')"
                                                   title="Clic izquierdo: Ver | Clic derecho: Seleccionar para comparar">
                                            <i class="fas fa-file-pdf text-sm"></i>
                                        </button>
                                            <div class="custom-tooltip">
                                                <div class="font-medium">{{ $documento['nombre'] }}</div>
                                                <div class="text-xs opacity-75 mt-1">Clic izquierdo: Ver | Clic derecho: Comparar</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <span class="text-xs text-gray-500 ml-2">{{ count($documentosPorSeccion['documentos']) }} doc(s)</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Sin documentos</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Contenido dinámico: Formulario completo o dividido -->
                <div id="contenido-documentos">
                    <!-- Inicialmente solo el formulario -->
                    <div class="p-4">
                        @include('components.formularios.seccion-documentos', [
                            'documentos' => $documentos,
                            'readonly' => true
                        ])
                        
                        <!-- Panel de revisión compacto -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-clipboard-check text-[#9d2449] mr-2 text-xs"></i>
                                    Revisión
                                </h4>
                                
                                <!-- Formularios compactos -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <!-- Formulario de Aprobar -->
                                    <form method="POST" action="{{ route('revision.seccion.aprobar', [$tramite->id, 6]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-2">
                                            <textarea name="comentario"
                                                      placeholder="Comentarios opcionales..." 
                                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-green-400 focus:ring-1 focus:ring-green-200 resize-none px-3 py-2 leading-relaxed" 
                                                      rows="3">{{ $revisionesExistentes[6]['comentario'] ?? '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-3 py-2 text-xs font-medium bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-150 flex items-center justify-center">
                                                <i class="fas fa-check mr-1"></i>Aprobar
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Formulario de Rechazar -->
                                    <form method="POST" action="{{ route('revision.seccion.rechazar', [$tramite->id, 6]) }}" class="flex-1">
                                        @csrf
                                        <div class="space-y-2">
                                            <textarea name="comentario"
                                                      placeholder="Motivo del rechazo (requerido)..." 
                                                      class="w-full text-sm rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none px-3 py-2 leading-relaxed" 
                                                      rows="3" required>{{ $estado === 'rechazado' ? ($revisionesExistentes[6]['comentario'] ?? '') : '' }}</textarea>
                                            <button type="submit" 
                                                    class="w-full px-3 py-2 text-xs font-medium bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-150 flex items-center justify-center">
                                                <i class="fas fa-times mr-1"></i>Rechazar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Comparación de Documentos -->
        <div id="panel-comparacion" class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 mt-6 hidden">
            <div class="bg-gradient-to-r from-blue-100 to-indigo-200 px-4 py-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                        <span class="bg-[#9d2449] text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold mr-3">
                            <i class="fas fa-columns"></i>
                        </span>
                        <span>Comparación de Documentos</span>
                    </h3>
                    <div class="flex items-center space-x-2">
                        <span id="contador-seleccionados" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">0/2 seleccionados</span>
                        <button onclick="limpiarSeleccionComparacion()" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded">
                            <i class="fas fa-eraser mr-1"></i>Limpiar
                        </button>
                        <button onclick="cerrarComparacion()" class="text-xs bg-red-200 hover:bg-red-300 text-red-700 px-2 py-1 rounded">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-4">
                <!-- Lista de documentos seleccionados -->
                <div id="documentos-seleccionados" class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Documentos seleccionados:</h4>
                    <div id="lista-seleccionados" class="space-y-2 text-sm text-gray-600">
                        <p class="italic">Haga clic derecho en los documentos para seleccionarlos</p>
                    </div>
                </div>
                
                <!-- Botón para comparar -->
                <div class="flex justify-center">
                    <button id="btn-comparar" onclick="iniciarComparacion()" disabled 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-eye mr-2"></i>Comparar Documentos
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel de Comentarios Generales de Revisión -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mt-8">
            <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-8 py-5">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <span class="bg-white/20 text-white rounded-full w-12 h-12 flex items-center justify-center text-base font-bold mr-4">
                        <i class="fas fa-comments text-xl"></i>
                    </span>
                    <span>Comentarios Generales</span>
                </h3>
            </div>
            
            <!-- Panel de revisión compacto -->
            <div class="p-8">
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-700 mb-6 flex items-center">
                        <i class="fas fa-clipboard-check text-[#9d2449] mr-2 text-xl"></i>
                        Comentarios Existentes
                    </h4>
                    
                    <!-- Comentarios existentes -->
                    <div class="mb-8">
                        @if(isset($comentariosGenerales) && count($comentariosGenerales) > 0)
                            <div class="space-y-4">
                                @foreach($comentariosGenerales as $comentario)
                                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center">
                                                <div class="bg-[#9d2449] rounded-full w-10 h-10 flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $comentario['autor'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $comentario['fecha'] }}</p>
                                                </div>
                                            </div>
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">General</span>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-gray-700 text-sm leading-relaxed">{{ trim($comentario['texto']) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center justify-center h-32 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <div class="text-center text-gray-400">
                                    <i class="fas fa-comment-slash text-3xl mb-2"></i>
                                    <p class="text-sm font-medium">No hay comentarios</p>
                                </div>
                            </div>
                        @endif
                    </div>
                
                    <!-- Formulario compacto -->
                    <form method="POST" action="{{ route('revision.agregar-comentario', $tramite->id) }}" class="space-y-4">
                        @csrf
                        <div class="relative">
                            <textarea name="comentario_general" rows="3" required
                                    class="w-full text-sm rounded-xl border-gray-300 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 resize-none px-4 py-3 leading-relaxed shadow-sm"
                                    placeholder="Escribe un nuevo comentario general..."></textarea>
                            <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-3 text-sm font-medium bg-[#9d2449] hover:bg-[#7a1d37] text-white rounded-xl transition-all duration-200 flex items-center shadow-sm hover:shadow-md">
                                <i class="fas fa-paper-plane mr-2 text-base"></i>Agregar Comentario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->

<!-- Modal de Comparación de Documentos -->
<div id="modal-comparacion-documentos" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50">
    <div class="h-full flex flex-col">
        <!-- Header del modal -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-columns text-blue-600 mr-3 text-xl"></i>
                <h3 class="text-lg font-semibold text-gray-900">Comparación de Documentos</h3>
            </div>
            <div class="flex items-center space-x-4">
                <div id="info-documentos-comparacion" class="text-sm text-gray-600"></div>
                <button onclick="cerrarModalComparacion()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Contenido del modal - documentos lado a lado -->
        <div class="flex-1 flex overflow-hidden">
            <!-- Documento 1 -->
            <div class="flex-1 flex flex-col border-r border-gray-300">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">1</div>
                        <span id="nombre-documento-1" class="text-sm font-medium text-gray-700"></span>
                        <span id="seccion-documento-1" class="text-xs text-gray-500 ml-2"></span>
                    </div>
                </div>
                <div class="flex-1 overflow-hidden">
                    <iframe id="iframe-documento-1" class="w-full h-full border-0"></iframe>
                </div>
            </div>
            
            <!-- Documento 2 -->
            <div class="flex-1 flex flex-col">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">2</div>
                        <span id="nombre-documento-2" class="text-sm font-medium text-gray-700"></span>
                        <span id="seccion-documento-2" class="text-xs text-gray-500 ml-2"></span>
                    </div>
                </div>
                <div class="flex-1 overflow-hidden">
                    <iframe id="iframe-documento-2" class="w-full h-full border-0"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalRechazarTodo" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-4 max-w-sm w-full mx-4">
        <form method="POST" action="{{ route('revision.rechazar-todo', $tramite->id) }}">
            @csrf
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Rechazar Todo</h3>
            <p class="text-gray-600 text-sm mb-3">¿Está seguro? Debe proporcionar un motivo.</p>
            <textarea name="comentario_general" rows="3" required class="w-full text-sm rounded-lg border-gray-300 focus:border-red-400 focus:ring-1 focus:ring-red-200 mb-3" placeholder="Motivo del rechazo..."></textarea>
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('modalRechazarTodo').classList.add('hidden')" class="flex-1 px-3 py-2 text-sm bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancelar</button>
                <button type="submit" class="flex-1 px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">Rechazar</button>
            </div>
        </form>
    </div>
</div>



@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/* Contenedores de sección con overflow visible para tooltips */
.bg-white.rounded-xl.shadow-lg {
    position: relative;
    overflow: visible !important;
}

.bg-gradient-to-r.from-gray-100.to-gray-200 {
    overflow: visible !important;
}

/* Tooltips elegantes que salen fuera del contenedor */
.tooltip-container {
    position: relative;
    z-index: 100;
}

.tooltip-container:hover .custom-tooltip {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(-5px);
}

.custom-tooltip {
    position: absolute;
    bottom: calc(100% + 10px);
    left: 50%;
    transform: translateX(-50%) translateY(5px);
    background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 500;
    white-space: nowrap;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s ease-in-out;
    pointer-events: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15), 0 4px 10px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    max-width: 250px;
    text-align: center;
    line-height: 1.3;
}

.custom-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 6px solid transparent;
    border-top-color: #1f2937;
    z-index: 10001;
}

/* Estilos específicos para botones de documentos */
.documento-btn {
    transition: all 0.2s ease-in-out;
    position: relative;
    z-index: 50;
}

.documento-btn:hover {
    transform: translateY(-1px);
    z-index: 100;
}

/* Diferentes colores para diferentes tipos de tooltips */
.tooltip-pdf .custom-tooltip {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
}

.tooltip-pdf .custom-tooltip::after {
    border-top-color: #dc2626;
}

.tooltip-map .custom-tooltip {
    background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
}

.tooltip-map .custom-tooltip::after {
    border-top-color: #2563eb;
}

.tooltip-general .custom-tooltip {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
}

.tooltip-general .custom-tooltip::after {
    border-top-color: #059669;
}

/* Asegurar que los contenedores padre no corten los tooltips */
.min-h-screen, .max-w-full, .space-y-6 {
    overflow: visible !important;
}

/* Ajustes para responsive */
@media (max-width: 768px) {
    .custom-tooltip {
        font-size: 10px;
        padding: 6px 8px;
        max-width: 200px;
        bottom: calc(100% + 8px);
    }
}

/* Animación suave para los iconos */
.documento-btn i, .tooltip-container i {
    transition: transform 0.2s ease-in-out;
}

.documento-btn:hover i, .tooltip-container:hover i {
    transform: scale(1.1);
}

/* Estilos para comparación de documentos */
.documento-seleccionado {
    position: relative;
    box-shadow: 0 0 0 2px #3b82f6 !important;
    background: #eff6ff !important;
}

.documento-seleccionado::after {
    content: '✓';
    position: absolute;
    top: -4px;
    right: -4px;
    background: #3b82f6;
    color: white;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    z-index: 10;
}

/* Prevenir selección de texto en botones */
.documento-btn {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

/* Estilos para el modal de comparación */
#modal-comparacion-documentos {
    backdrop-filter: blur(4px);
}

#modal-comparacion-documentos iframe {
    background: white;
}

/* Indicador de carga para iframes */
.iframe-loading {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>
@endpush

@push('scripts')
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCgXSEgnOeCKaE80Zc6ouGxxcHK61vZAR8&libraries=places"></script>
<script src="{{ asset('js/components/document-viewer.js') }}"></script>
<script src="{{ asset('js/components/map-handler.js') }}"></script>
@endpush

<script>
// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de revisión inicializado');
    console.log('📖 DocumentViewer disponible:', typeof documentViewer !== 'undefined');
    console.log('🗺️ MapHandler disponible:', typeof mapHandler !== 'undefined');
});

// Función para mostrar mapa usando el nuevo componente
function mostrarMapa(seccion) {
    // Cerrar cualquier documento o mapa abierto en otras secciones
    if (window.documentViewer) {
        window.documentViewer.closeAllOtherSections(seccion);
    }
    
    // Obtener el contenedor de la sección
    const contenedor = document.getElementById('contenido-' + seccion);
    if (!contenedor) {
        console.error('❌ Contenedor no encontrado para la sección:', seccion);
        return;
    }
    
    // Guardar contenido original si no está guardado
    if (window.documentViewer && !window.documentViewer.originalContent.has(seccion)) {
        window.documentViewer.originalContent.set(seccion, contenedor.innerHTML);
    }
    
    // Obtener el contenido actual del formulario
    const formularioActual = contenedor.innerHTML;
    
    // Obtener datos de domicilio para construir la dirección
    const direccion = window.obtenerDireccionCompleta ? window.obtenerDireccionCompleta() : 'Dirección no disponible';
    
    // Crear el header con información del mapa
    const headerMapa = document.createElement('div');
    headerMapa.className = 'mb-3 p-3 bg-white rounded-lg border border-gray-200 shadow-sm flex-shrink-0';
    headerMapa.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center min-w-0 flex-1">
                <i class="fas fa-map-marker-alt text-blue-500 mr-2 flex-shrink-0"></i>
                <span class="text-sm font-medium text-gray-700">Ubicación en Mapa</span>
            </div>
            <div class="flex items-center space-x-2 ml-3 flex-shrink-0">
                <span class="text-xs text-gray-500 truncate" title="${direccion}">${direccion}</span>
                <button onclick="cerrarMapa('${seccion}')" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs transition-colors whitespace-nowrap">
                    <i class="fas fa-times mr-1"></i>Cerrar
                </button>
            </div>
        </div>
    `;
    
    // Crear el contenedor del mapa
    const mapaDiv = document.createElement('div');
    mapaDiv.id = 'mapa-' + seccion;
    mapaDiv.className = 'w-full flex-1 border border-gray-300 rounded min-h-0';
    mapaDiv.style.minHeight = '500px';
    
    // Crear el nuevo layout dividido
    const layoutDividido = document.createElement('div');
    layoutDividido.className = 'grid grid-cols-1 lg:grid-cols-2 gap-4 min-h-[600px]';
    layoutDividido.setAttribute('data-map-layout', 'true');
    
    layoutDividido.innerHTML = `
        <div class="border-r border-gray-200 pr-4">
            ${formularioActual}
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <div id="visor-${seccion}" class="h-full flex flex-col">
            </div>
        </div>
    `;
    
    // Reemplazar el contenido
    contenedor.innerHTML = '';
    contenedor.appendChild(layoutDividido);
    
    // Agregar el header y el mapa al visor
    const visor = document.getElementById('visor-' + seccion);
    visor.appendChild(headerMapa);
    visor.appendChild(mapaDiv);
    
    // Agregar a secciones abiertas si hay documentViewer
    if (window.documentViewer) {
        window.documentViewer.openSections.add(seccion);
    }
    
    // Inicializar el mapa después de que el DOM esté listo
    setTimeout(() => {
        if (window.mapHandler) {
            window.mapHandler.initializeMap(seccion, direccion);
        } else if (window.inicializarMapa) {
            window.inicializarMapa(seccion, direccion);
        }
        
        // Scroll a la sección
        if (window.documentViewer) {
            window.documentViewer.scrollToSection(seccion);
        }
    }, 100);
}

// Función para cerrar mapa
function cerrarMapa(seccion) {
    if (window.documentViewer) {
        window.documentViewer.closeDocument(seccion);
    }
    
    // Limpiar el mapa
    if (window.mapHandler) {
        window.mapHandler.cleanup();
    }
}

// Estado global para comparación de documentos
window.estadoComparacion = {
    documentosSeleccionados: [],
    maxDocumentos: 2
};

// Función para seleccionar documento para comparar
function seleccionarParaComparar(event, seccion, ruta, nombre) {
    event.preventDefault(); // Prevenir menú contextual
    event.stopPropagation(); // Prevenir propagación del evento
    
    const boton = event.target.closest('.documento-btn');
    const docId = `${seccion}_${ruta}`;
    
    // Verificar si ya está seleccionado
    const indiceExistente = window.estadoComparacion.documentosSeleccionados.findIndex(doc => doc.id === docId);
    
    if (indiceExistente !== -1) {
        // Deseleccionar
        window.estadoComparacion.documentosSeleccionados.splice(indiceExistente, 1);
        boton.classList.remove('documento-seleccionado');
    } else {
        // Verificar límite máximo
        if (window.estadoComparacion.documentosSeleccionados.length >= window.estadoComparacion.maxDocumentos) {
            // Deseleccionar el primer documento
            const primerDoc = window.estadoComparacion.documentosSeleccionados.shift();
            const primerBoton = document.querySelector(`[data-doc-seccion="${primerDoc.seccion}"][data-doc-ruta="${primerDoc.ruta}"]`);
            if (primerBoton) {
                primerBoton.classList.remove('documento-seleccionado');
            }
        }
        
        // Seleccionar nuevo documento
        window.estadoComparacion.documentosSeleccionados.push({
            id: docId,
            seccion: seccion,
            ruta: ruta,
            nombre: nombre
        });
        boton.classList.add('documento-seleccionado');
    }
    
    actualizarPanelComparacion();
}

// Función para actualizar el panel de comparación
function actualizarPanelComparacion() {
    const panel = document.getElementById('panel-comparacion');
    const contador = document.getElementById('contador-seleccionados');
    const listaSeleccionados = document.getElementById('lista-seleccionados');
    const btnComparar = document.getElementById('btn-comparar');
    
    const numSeleccionados = window.estadoComparacion.documentosSeleccionados.length;
    
    // Mostrar u ocultar panel
    if (numSeleccionados > 0) {
        panel.classList.remove('hidden');
    } else {
        panel.classList.add('hidden');
        return;
    }
    
    // Actualizar contador
    contador.textContent = `${numSeleccionados}/${window.estadoComparacion.maxDocumentos} seleccionados`;
    
    // Actualizar lista
    if (numSeleccionados === 0) {
        listaSeleccionados.innerHTML = '<p class="italic">Haga clic derecho en los documentos para seleccionarlos</p>';
    } else {
        listaSeleccionados.innerHTML = window.estadoComparacion.documentosSeleccionados.map((doc, index) => 
            `<div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                <div class="flex items-center">
                    <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-2">${index + 1}</span>
                    <span class="font-medium">${doc.nombre}</span>
                    <span class="text-xs text-gray-500 ml-2">(${doc.seccion})</span>
                </div>
                <button onclick="deseleccionarDocumento('${doc.id}')" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>`
        ).join('');
    }
    
    // Habilitar/deshabilitar botón de comparar
    btnComparar.disabled = numSeleccionados !== 2;
}

// Función para deseleccionar un documento específico
function deseleccionarDocumento(docId) {
    const indice = window.estadoComparacion.documentosSeleccionados.findIndex(doc => doc.id === docId);
    if (indice !== -1) {
        const doc = window.estadoComparacion.documentosSeleccionados[indice];
        const boton = document.querySelector(`[data-doc-seccion="${doc.seccion}"][data-doc-ruta="${doc.ruta}"]`);
        if (boton) {
            boton.classList.remove('documento-seleccionado');
        }
        window.estadoComparacion.documentosSeleccionados.splice(indice, 1);
        actualizarPanelComparacion();
    }
}

// Función para limpiar selección
function limpiarSeleccionComparacion() {
    // Deseleccionar todos los botones
    document.querySelectorAll('.documento-seleccionado').forEach(boton => {
        boton.classList.remove('documento-seleccionado');
    });
    
    // Limpiar estado
    window.estadoComparacion.documentosSeleccionados = [];
    actualizarPanelComparacion();
}

// Función para cerrar panel de comparación
function cerrarComparacion() {
    limpiarSeleccionComparacion();
}

// Función para iniciar comparación
function iniciarComparacion() {
    if (window.estadoComparacion.documentosSeleccionados.length !== 2) {
        alert('Debe seleccionar exactamente 2 documentos para comparar');
        return;
    }
    
    mostrarModalComparacion();
}

// Función para mostrar modal de comparación
function mostrarModalComparacion() {
    const modal = document.getElementById('modal-comparacion-documentos');
    const docs = window.estadoComparacion.documentosSeleccionados;
    
    // Actualizar información en el header
    document.getElementById('info-documentos-comparacion').textContent = 
        `Comparando: ${docs[0].nombre} vs ${docs[1].nombre}`;
    
    // Actualizar nombres y secciones
    document.getElementById('nombre-documento-1').textContent = docs[0].nombre;
    document.getElementById('seccion-documento-1').textContent = `(${docs[0].seccion})`;
    document.getElementById('nombre-documento-2').textContent = docs[1].nombre;
    document.getElementById('seccion-documento-2').textContent = `(${docs[1].seccion})`;
    
    // Configurar iframes
    const iframe1 = document.getElementById('iframe-documento-1');
    const iframe2 = document.getElementById('iframe-documento-2');
    
    // Agregar clase de carga
    iframe1.classList.add('iframe-loading');
    iframe2.classList.add('iframe-loading');
    
    // Cargar documentos
    iframe1.src = docs[0].ruta;
    iframe2.src = docs[1].ruta;
    
    // Remover clase de carga cuando termine de cargar
    iframe1.onload = () => iframe1.classList.remove('iframe-loading');
    iframe2.onload = () => iframe2.classList.remove('iframe-loading');
    
    // Mostrar modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Función para cerrar modal de comparación
function cerrarModalComparacion() {
    const modal = document.getElementById('modal-comparacion-documentos');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Limpiar iframes
    document.getElementById('iframe-documento-1').src = '';
    document.getElementById('iframe-documento-2').src = '';
}

// JavaScript mínimo para cerrar modales al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.id === 'modalRechazarTodo') {
        document.getElementById('modalRechazarTodo').classList.add('hidden');
    }
    if (e.target.id === 'modal-comparacion-documentos') {
        cerrarModalComparacion();
    }
});

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modalRechazo = document.getElementById('modalRechazarTodo');
        const modalComparacion = document.getElementById('modal-comparacion-documentos');
        
        if (modalRechazo && !modalRechazo.classList.contains('hidden')) {
            modalRechazo.classList.add('hidden');
        }
        if (modalComparacion && !modalComparacion.classList.contains('hidden')) {
            cerrarModalComparacion();
        }
    }
});
</script> 
@endsection 