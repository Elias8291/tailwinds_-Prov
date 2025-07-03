{{-- Header Principal Mejorado --}}
<div class="max-w-[1800px] mx-auto px-8 py-6">
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-6 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            {{-- Información del Trámite --}}
            <div class="flex items-center space-x-6">
                <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                    <svg class="w-8 h-8 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                        Revisión de Trámite
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Proceso de validación y verificación de documentos</p>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mt-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] rounded-full"></div>
                            <span class="font-medium">ID: <span class="text-[#B4325E] font-mono font-semibold">#{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}</span></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                            <span class="font-medium">{{ $tramite->tipo_tramite ?? 'Inscripción' }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            <span class="font-medium">RFC: <span class="font-mono font-semibold text-gray-700">{{ $datosSolicitante['rfc'] ?? 'Sin RFC' }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Estado y Acciones --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                @php
                    $estado = $tramite->estado ?? 'Pendiente';
                    
                    $estadoClasses = [
                        'Aprobado' => 'bg-green-100 text-green-800 border-green-200',
                        'Rechazado' => 'bg-red-100 text-red-800 border-red-200',
                        'En Revision' => 'bg-blue-100 text-blue-800 border-blue-200'
                    ];
                    
                    $estadoDotClasses = [
                        'Aprobado' => 'bg-green-500',
                        'Rechazado' => 'bg-red-500',
                        'En Revision' => 'bg-blue-500'
                    ];
                    
                    $defaultClasses = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                    $defaultDotClass = 'bg-yellow-500';
                @endphp
                
                <div class="px-4 py-2 rounded-xl shadow-sm border {{ $estadoClasses[$estado] ?? $defaultClasses }}">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 rounded-full {{ $estadoDotClasses[$estado] ?? $defaultDotClass }}"></div>
                        <span class="text-sm font-medium">{{ $estado }}</span>
                    </div>
                </div>
                
                <a href="{{ route('revision.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transform hover:scale-105 transition-all duration-300 hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div> 