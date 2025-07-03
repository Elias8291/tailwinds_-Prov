{{-- Sección de Revisión: Datos Generales --}}
<div class="revision-section" data-seccion="datos-generales">
    {{-- Encabezado de la sección --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Datos Generales</h3>
                <p class="text-sm text-gray-500">Información básica del solicitante y la empresa</p>
            </div>
        </div>
        
        {{-- Estado de revisión --}}
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                ⏳ Pendiente de revisión
            </span>
        </div>
    </div>

    {{-- Contenido de los datos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Información del Solicitante --}}
        <div class="bg-gray-50 rounded-xl p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Información del Solicitante
            </h4>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">RFC</label>
                    <p class="text-sm text-gray-900 font-mono bg-white px-3 py-2 rounded border">
                        {{ $datosSolicitante['rfc'] ?? 'No especificado' }}
                    </p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-500">Tipo de Persona</label>
                    <p class="text-sm text-gray-900 bg-white px-3 py-2 rounded border">
                        {{ $datosSolicitante['tipo_persona'] ?? 'No especificado' }}
                    </p>
                </div>
                
                @if(($datosSolicitante['tipo_persona'] ?? '') === 'Física')
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nombre Completo</label>
                        <p class="text-sm text-gray-900 bg-white px-3 py-2 rounded border">
                            {{ $datosSolicitante['nombre_completo'] ?? 'No especificado' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">CURP</label>
                        <p class="text-sm text-gray-900 font-mono bg-white px-3 py-2 rounded border">
                            {{ $datosSolicitante['curp'] ?? 'No especificado' }}
                        </p>
                    </div>
                @else
                    <div>
                        <label class="text-sm font-medium text-gray-500">Razón Social</label>
                        <p class="text-sm text-gray-900 bg-white px-3 py-2 rounded border">
                            {{ $datosSolicitante['razon_social'] ?? 'No especificado' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Información de la Empresa --}}
        <div class="bg-gray-50 rounded-xl p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Información de la Empresa
            </h4>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Giro de la Empresa</label>
                    <p class="text-sm text-gray-900 bg-white px-3 py-2 rounded border">
                        {{ $datosTramite['giro'] ?? 'No especificado' }}
                    </p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-500">Objeto Social</label>
                    <p class="text-sm text-gray-900 bg-white px-3 py-2 rounded border">
                        {{ $datosSolicitante['objeto_social'] ?? 'No especificado' }}
                    </p>
                </div>
                
                @if(!empty($datosTramite['pagina_web']))
                <div>
                    <label class="text-sm font-medium text-gray-500">Página Web</label>
                    <p class="text-sm text-blue-600 bg-white px-3 py-2 rounded border">
                        <a href="{{ $datosTramite['pagina_web'] }}" target="_blank" class="hover:underline">
                            {{ $datosTramite['pagina_web'] }}
                        </a>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Información de Contacto --}}
    @if(!empty($datosTramite['contacto_nombre']))
    <div class="bg-blue-50 rounded-xl p-6 mb-8">
        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            Información de Contacto
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-500">Nombre del Contacto</label>
                <p class="text-sm text-gray-900 bg-white px-3 py-2 rounded border">
                    {{ $datosTramite['contacto_nombre'] ?? 'No especificado' }}
                </p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Cargo</label>
                <p class="text-sm text-gray-900 bg-white px-3 py-2 rounded border">
                    {{ $datosTramite['contacto_cargo'] ?? 'No especificado' }}
                </p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Correo Electrónico</label>
                <p class="text-sm text-blue-600 bg-white px-3 py-2 rounded border">
                    <a href="mailto:{{ $datosTramite['contacto_correo'] ?? '' }}" class="hover:underline">
                        {{ $datosTramite['contacto_correo'] ?? 'No especificado' }}
                    </a>
                </p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-500">Teléfono</label>
                <p class="text-sm text-gray-900 font-mono bg-white px-3 py-2 rounded border">
                    {{ $datosTramite['contacto_telefono'] ?? 'No especificado' }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Controles de Revisión --}}
    @include('revision.partials.review-controls', [
        'seccion' => 'datos-generales',
        'titulo' => 'Datos Generales'
    ])
</div> 