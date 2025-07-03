{{-- Navegación de Secciones Compacta --}}
<div class="max-w-[1800px] mx-auto px-8 py-4">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                Secciones de Revisión
            </h3>
            <div class="text-sm text-gray-500">
                Seleccione una sección para revisar
            </div>
        </div>
        <div class="flex flex-wrap gap-2" id="secciones-container">
            {{-- Sección General --}}
            <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border active" data-seccion="general">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    <span>General</span>
                </div>
            </button>
            
            {{-- Sección Datos Generales --}}
            <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="datos-generales">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                    <span>Datos</span>
                </div>
            </button>
            
            {{-- Sección Domicilio --}}
            <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="domicilio">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                    <span>Domicilio</span>
                </div>
            </button>
            
            {{-- Secciones específicas para Persona Moral --}}
            @if($tramite->solicitante && strtolower($tramite->solicitante->tipo_persona) === 'moral')
                <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="constitucion">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        <span>Constitución</span>
                    </div>
                </button>
                
                <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="accionistas">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        <span>Accionistas</span>
                    </div>
                </button>
                
                <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="apoderado">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        <span>Apoderado</span>
                    </div>
                </button>
                
                <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="personal">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        <span>Personal</span>
                    </div>
                </button>
            @endif
            
            {{-- Sección Documentos --}}
            <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="documentos">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                    <span>Documentos</span>
                </div>
            </button>

            {{-- Sección de Proceso (solo para usuarios con permisos) --}}
            @can('revision-tramites.aprobar')
            <button class="seccion-tab px-4 py-2 rounded-lg text-sm font-medium transition-all border" data-seccion="decision-final">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                    <span>Proceso</span>
                </div>
            </button>
            @endcan
        </div>
    </div>
</div> 