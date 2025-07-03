{{-- Contenido Principal de Revisión --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900" id="seccion-titulo">Información General</h2>
            <p class="text-sm text-gray-500 mt-1" id="seccion-descripcion">Resumen del trámite y datos del solicitante</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="w-3 h-3 rounded-full bg-blue-500" id="seccion-status"></div>
            <span class="text-sm font-medium text-gray-600" id="seccion-estado">Activa</span>
        </div>
    </div>
</div>

{{-- Contenedor de Secciones --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    
    {{-- Sección General (Por defecto activa) --}}
    <div id="contenido-general" class="section-content p-6">
        @include('revision.sections.general', [
            'tramite' => $tramite,
            'datosTramite' => $datosTramite,
            'datosSolicitante' => $datosSolicitante
        ])
    </div>

    {{-- Sección Datos Generales --}}
    <div id="contenido-datos-generales" class="section-content hidden p-6">
        @include('components.formularios.seccion-datos-generales', [
            'tramite' => $tramite,
            'datosTramite' => $datosTramite,
            'datosSolicitante' => $datosSolicitante,
            'readonly' => true
        ])
        
        {{-- Controles de Revisión --}}
        @include('revision.partials.review-controls', [
            'seccion' => 'datos-generales',
            'titulo' => 'Datos Generales'
        ])
    </div>

    {{-- Sección Domicilio --}}
    <div id="contenido-domicilio" class="section-content hidden p-6">
        @include('components.formularios.seccion-domicilio', [
            'tramite' => $tramite,
            'datosDomicilio' => $datosDomicilio,
            'readonly' => true
        ])
        
        {{-- Controles de Revisión --}}
        @include('revision.partials.review-controls', [
            'seccion' => 'domicilio',
            'titulo' => 'Domicilio'
        ])
    </div>

    {{-- Secciones específicas para Persona Moral --}}
    @if($tramite->solicitante && strtolower($tramite->solicitante->tipo_persona) === 'moral')
        {{-- Sección Constitución --}}
        <div id="contenido-constitucion" class="section-content hidden p-6">
            @include('components.formularios.seccion-constitucion', [
                'tramite' => $tramite,
                'datosConstitucion' => $datosConstitucion ?? null,
                'readonly' => true
            ])
            
            {{-- Controles de Revisión --}}
            @include('revision.partials.review-controls', [
                'seccion' => 'constitucion',
                'titulo' => 'Constitución'
            ])
        </div>

        {{-- Sección Accionistas --}}
        <div id="contenido-accionistas" class="section-content hidden p-6">
            @include('components.formularios.seccion-accionistas', [
                'tramite' => $tramite,
                'accionistas' => $accionistas ?? [],
                'datosAccionistas' => $datosAccionistas ?? [],
                'readonly' => true
            ])
            
            {{-- Controles de Revisión --}}
            @include('revision.partials.review-controls', [
                'seccion' => 'accionistas',
                'titulo' => 'Accionistas'
            ])
        </div>

        {{-- Sección Apoderado --}}
        <div id="contenido-apoderado" class="section-content hidden p-6">
            @include('components.formularios.seccion-apoderado', [
                'tramite' => $tramite,
                'datosApoderado' => $datosApoderado ?? null,
                'readonly' => true
            ])
            
            {{-- Controles de Revisión --}}
            @include('revision.partials.review-controls', [
                'seccion' => 'apoderado',
                'titulo' => 'Apoderado Legal'
            ])
        </div>

        {{-- Sección Personal --}}
        <div id="contenido-personal" class="section-content hidden p-6">
            {{-- Esta sección está vacía por ahora --}}
            <div class="text-gray-500 text-center py-8">
                <p>Sección de personal en desarrollo</p>
            </div>
            
            {{-- Controles de Revisión --}}
            @include('revision.partials.review-controls', [
                'seccion' => 'personal',
                'titulo' => 'Personal'
            ])
        </div>
    @endif

    {{-- Sección Documentos --}}
    <div id="contenido-documentos" class="section-content hidden p-6">
        @include('components.formularios.seccion-documentos', [
            'tramite' => $tramite,
            'documentosPorSeccion' => $documentosPorSeccion ?? [],
            'readonly' => true
        ])
        
        {{-- Controles de Revisión --}}
        @include('revision.partials.review-controls', [
            'seccion' => 'documentos',
            'titulo' => 'Documentos'
        ])
    </div>

    {{-- Sección Proceso (solo para usuarios con permisos) --}}
    @can('revision-tramites.aprobar')
    <div id="contenido-decision-final" class="section-content hidden p-6">
        {{-- Sección de proceso de aprobación --}}
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
            <h4 class="text-lg font-medium text-purple-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Decisión Final del Trámite
            </h4>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-purple-700 mb-2">
                        Comentarios Finales del Proceso
                    </label>
                    <textarea 
                        id="comentarios-finales" 
                        name="comentarios-finales" 
                        rows="4" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none"
                        placeholder="Agregue comentarios finales sobre la decisión del trámite..."></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button type="button" 
                            onclick="RevisionTramite.sections.approve('tramite-completo')"
                            class="flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Aprobar Trámite
                    </button>
                    
                    <button type="button" 
                            onclick="RevisionTramite.sections.reject('tramite-completo')"
                            class="flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Rechazar Trámite
                    </button>
                    
                    <button type="button" 
                            onclick="RevisionTramite.sections.requestCorrection('tramite-completo')"
                            class="flex items-center justify-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-all transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Solicitar Correcciones
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan
</div> 