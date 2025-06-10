@props(['rfc' => null, 'estado' => null, 'fecha_vencimiento' => null])

@php
    $puedeInscribirse = !$rfc || ($estado === 'Inactivo');
    $puedeActualizar = $estado === 'Activo';
    $puedeRenovar = $estado === 'Activo' && $fecha_vencimiento && 
                    now()->diffInDays(\Carbon\Carbon::parse($fecha_vencimiento)) <= 7;
@endphp

<!-- Contenedor de Servicios -->
<div class="mt-6 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80">
    <div class="p-5">
        <!-- Encabezado del contenedor -->
        <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-2.5 shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                    Servicios Disponibles
                </h2>
                <p class="text-sm text-gray-500">Seleccione el tipo de trámite que desea realizar</p>
            </div>
        </div>

        <!-- Grid de servicios -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($puedeInscribirse)
            <!-- Inscripción -->
            <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                        Inscripción
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        Registre su primera vez al padrón de contribuyentes con todos los requisitos necesarios.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                            Nuevo registro
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Asignar Cuenta -->
            <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                        Asignar Cuenta
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        Vincule su cuenta de usuario con un registro de solicitante existente.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">
                            Vincular cuenta
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

            @if($puedeRenovar)
            <!-- Renovación -->
            <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                        Renovación
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        Renueve su registro existente para mantener vigente su situación fiscal.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                            Renovar registro
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

            @if($puedeActualizar)
            <!-- Actualización -->
            <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                        Actualización
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        Modifique sus datos personales, fiscales o de actividad económica.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                            Modificar datos
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endif
        </div>

        @if(!$rfc)
        <!-- Información adicional cuando no hay RFC -->
        <div class="mt-6 bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-xl border border-[#9d2449]/10 p-4">
            <div class="flex items-start gap-3">
                <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-1.5 shadow-md mt-0.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 mb-1">Información Importante</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Todos los trámites requieren documentación específica. Asegúrese de tener los documentos necesarios antes de iniciar el proceso. 
                        Para más información, puede consultar nuestra guía de requisitos o contactar con nuestro equipo de soporte.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div> 