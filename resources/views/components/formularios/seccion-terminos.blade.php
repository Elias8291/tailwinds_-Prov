@props([
    'tipoTramite' => 'inscripcion',
    'rfc' => '',
    'tipoPersona' => 'Física'
])

<div x-data="{ 
    terminosAceptados: false,
    modalAbierto: false,
    aceptarTerminos() {
        this.modalAbierto = true;
    },
    continuarSiguienteSeccion() {
        this.modalAbierto = false;
        // Emitir evento para el componente padre
        $dispatch('terminos-aceptados', { aceptado: true });
        
        // Enviar formulario para iniciar trámite
        document.getElementById('form-iniciar-tramite').submit();
    }
}">
    <!-- Contenido de Términos y Condiciones -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8 overflow-hidden">
        <!-- Header con información del trámite -->
        <div class="bg-gradient-to-r from-[#9d2449]/10 to-[#7a1d37]/10 px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-scroll text-[#9d2449] mr-3"></i>
                    Términos y Condiciones del Servicio
                </h2>
                <div class="text-sm text-gray-600">
                    <span class="font-medium">Trámite:</span> {{ ucfirst($tipoTramite) }}
                    @if($rfc)
                        <span class="mx-2">|</span>
                        <span class="font-medium">RFC:</span> {{ $rfc }}
                    @endif
                    @if($tipoPersona)
                        <span class="mx-2">|</span>
                        <span class="font-medium">Tipo:</span> {{ $tipoPersona }}
                    @endif
                </div>
            </div>
        </div>
        
        <div class="max-h-96 overflow-y-auto custom-scrollbar">
            <div class="p-6 space-y-6 text-gray-700 leading-relaxed">
                
                <section>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                        1. Aceptación de los Términos
                    </h3>
                    <p class="mb-4">
                        Al utilizar nuestros servicios de tramitación en línea, usted acepta estar sujeto a estos términos y condiciones. 
                        Si no está de acuerdo con alguna parte de estos términos, no podrá acceder al servicio.
                    </p>
                </section>

                <section>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-database text-green-500 mr-2"></i>
                        2. Protección de Datos Personales
                    </h3>
                    <p class="mb-4">
                        Sus datos personales serán tratados de conformidad con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares. 
                        La información proporcionada será utilizada únicamente para el procesamiento de su trámite y cumplimiento de obligaciones legales.
                    </p>
                    <ul class="list-disc list-inside ml-4 space-y-2 text-sm">
                        <li>Los datos se almacenan de forma segura y encriptada</li>
                        <li>No se comparten con terceros sin su consentimiento</li>
                        <li>Puede solicitar la corrección o eliminación de sus datos</li>
                    </ul>
                </section>

                <section>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-file-upload text-purple-500 mr-2"></i>
                        3. Documentos y Archivos
                    </h3>
                    <p class="mb-4">
                        Los documentos que cargue en el sistema deben ser legibles, actuales y verídicos. 
                        Usted es responsable de la veracidad de la información proporcionada.
                    </p>
                    <ul class="list-disc list-inside ml-4 space-y-2 text-sm">
                        <li>Formatos aceptados: PDF, JPG, PNG</li>
                                                                <li>Tamaño máximo por archivo: 50MB</li>
                        <li>Los documentos falsos o alterados pueden resultar en la cancelación del trámite</li>
                    </ul>
                </section>

                <section>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                        4. Tiempos de Procesamiento
                    </h3>
                    <p class="mb-4">
                        Los tiempos de procesamiento son estimados y pueden variar según la complejidad del trámite y la carga de trabajo. 
                        Nos comprometemos a mantenerlo informado sobre el estatus de su solicitud.
                    </p>
                </section>

                <section>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-gavel text-red-500 mr-2"></i>
                        5. Responsabilidades y Limitaciones
                    </h3>
                    <p class="mb-4">
                        El usuario es responsable de proporcionar información veraz y mantener actualizados sus datos de contacto. 
                        No nos hacemos responsables por retrasos causados por información incorrecta o incompleta.
                    </p>
                </section>

                <section>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-envelope text-indigo-500 mr-2"></i>
                        6. Comunicaciones
                    </h3>
                    <p class="mb-4">
                        Las notificaciones oficiales se enviarán al correo electrónico registrado. 
                        Es su responsabilidad mantener actualizada esta información y revisar regularmente su correo.
                    </p>
                </section>

                <section>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-sync-alt text-teal-500 mr-2"></i>
                        7. Modificaciones
                    </h3>
                    <p class="mb-4">
                        Nos reservamos el derecho de modificar estos términos en cualquier momento. 
                        Las modificaciones entrarán en vigor inmediatamente después de su publicación en el sitio web.
                    </p>
                </section>
            </div>
        </div>
    </div>

    <!-- Checkbox de Aceptación -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
                <label class="flex items-start space-x-4 cursor-pointer">
                    <input type="checkbox" 
                           x-model="terminosAceptados"
                           class="mt-1 h-5 w-5 text-[#9d2449] border-2 border-gray-300 rounded focus:ring-[#9d2449] focus:ring-2">
                    <div>
                        <p class="text-gray-800 font-medium">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            He leído y acepto los términos y condiciones
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            Al marcar esta casilla, confirmo que he leído, entendido y acepto cumplir con todos los términos y condiciones establecidos.
                        </p>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex justify-between items-center mt-6">
        <a href="{{ route('tramites.solicitante.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]/50 transition-all duration-200 ease-in-out">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Cancelar
        </a>

        <button type="button"
                @click="aceptarTerminos()"
                :disabled="!terminosAceptados"
                :class="{'opacity-50 cursor-not-allowed': !terminosAceptados}"
                class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-[#9d2449] hover:bg-[#7a1d37] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transition-all duration-200 ease-in-out transform hover:scale-105">
            <span>Continuar al Registro</span>
            <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </button>
    </div>

    <!-- Modal de Confirmación -->
    <div x-show="modalAbierto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay de fondo -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
                 @click="modalAbierto = false"
                 aria-hidden="true"></div>

            <!-- Centrado del modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Contenido del Modal -->
            <div x-show="modalAbierto"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full">
                        <i class="text-2xl text-green-600 fas fa-check"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            Confirmación de Términos y Condiciones
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Has aceptado los términos y condiciones del servicio. ¿Deseas continuar con el registro?
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="button"
                            @click="continuarSiguienteSeccion()"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-[#9d2449] border border-transparent rounded-md shadow-sm hover:bg-[#7a1d37] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] sm:col-start-2">
                        Continuar
                    </button>
                    <button type="button"
                            @click="modalAbierto = false"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] sm:mt-0 sm:col-start-1">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para iniciar trámite -->
    <form id="form-iniciar-tramite" method="POST" action="{{ route('tramites.iniciar') }}" class="hidden">
        @csrf
        <input type="hidden" name="tipoTramite" value="{{ $tipoTramite }}">
        @if($rfc)
            <input type="hidden" name="rfc" value="{{ $rfc }}">
        @endif
        @if($tipoPersona)
            <input type="hidden" name="tipoPersona" value="{{ $tipoPersona }}">
        @endif
    </form>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style> 