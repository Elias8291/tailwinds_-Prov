@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8 bg-gradient-to-br from-gray-50 via-white to-gray-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="terminosData()">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-2xl shadow-lg mb-6">
                <i class="fas fa-file-contract text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent mb-4">
                Términos y Condiciones
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Antes de continuar con su trámite de <span class="font-semibold text-[#9d2449]">{{ ucfirst($tipoTramite) }}</span>, 
                es necesario que lea y acepte nuestros términos y condiciones.
            </p>
        </div>

        <!-- Información del Trámite -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-[#9d2449]/10 to-[#7a1d37]/10 px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-[#9d2449] mr-3"></i>
                    Resumen del Trámite
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-clipboard-list text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tipo de Trámite</p>
                            <p class="text-lg font-bold text-gray-800">{{ ucfirst($tipoTramite) }}</p>
                        </div>
                    </div>
                    
                    @if(isset($rfc) && $rfc)
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-hashtag text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">RFC</p>
                            <p class="text-lg font-bold text-gray-800 font-mono">{{ $rfc }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if(isset($tipoPersona) && $tipoPersona)
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-tag text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tipo de Persona</p>
                            <p class="text-lg font-bold text-gray-800">{{ $tipoPersona }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-clock text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tiempo Estimado</p>
                            <p class="text-lg font-bold text-gray-800">15-30 minutos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido de Términos y Condiciones -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-[#9d2449]/10 to-[#7a1d37]/10 px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-scroll text-[#9d2449] mr-3"></i>
                    Términos y Condiciones del Servicio
                </h2>
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
                            <li>Tamaño máximo por archivo: 10MB</li>
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

        <!-- Checkbox de Aceptación y Botones -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Checkbox de Aceptación -->
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

                    <!-- Botones de Acción -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-between">
                        <a href="{{ route('tramites.index') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 shadow-md">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Cancelar y Volver
                        </a>
                        
                        <button @click="continuarTramite()" 
                                :disabled="!terminosAceptados"
                                :class="terminosAceptados ? 'bg-gradient-to-r from-[#9d2449] to-[#7a1d37] hover:from-[#7a1d37] hover:to-[#9d2449] transform hover:scale-105 cursor-pointer' : 'bg-gray-400 cursor-not-allowed opacity-50'"
                                class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold transition-all duration-200 shadow-lg">
                            <i class="fas fa-check mr-2"></i>
                            Aceptar y Continuar
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div x-show="cargando" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 shadow-2xl">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-full mb-4">
                        <i class="fas fa-spinner fa-spin text-2xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Iniciando Trámite...</h3>
                    <p class="text-gray-600">Por favor espere mientras procesamos su solicitud</p>
                </div>
            </div>
        </div>

    </div>
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
    background: linear-gradient(to bottom, #9d2449, #7a1d37);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #7a1d37, #6d1a32);
}
</style>

<script>
function terminosData() {
    return {
        terminosAceptados: false,
        cargando: false,
        
        async continuarTramite() {
            if (!this.terminosAceptados) {
                alert('Debe aceptar los términos y condiciones para continuar.');
                return;
            }
            
            this.cargando = true;
            
            try {
                // Marcar términos como aceptados en localStorage
                const terminosKey = `terminos_aceptados_{{ $tipoTramite }}_{{ $rfc ?? 'sin_rfc' }}`;
                localStorage.setItem(terminosKey, JSON.stringify({
                    aceptado: true,
                    fecha: new Date().toISOString(),
                    tipoTramite: '{{ $tipoTramite }}',
                    rfc: '{{ $rfc ?? '' }}'
                }));
                
                // Preparar datos para el trámite incluyendo términos aceptados
                const datosForm = {
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                    tipo_tramite: '{{ $tipoTramite }}',
                    rfc: '{{ $rfc ?? '' }}',
                    tipo_persona: '{{ $tipoPersona ?? '' }}',
                    terminos_aceptados: true
                };
                
                @if(isset($tramiteId) && $tramiteId)
                datosForm.tramite_id = '{{ $tramiteId }}';
                @endif
                
                // Enviar request para iniciar trámite con términos aceptados
                const response = await fetch('{{ route('tramites.iniciar') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datosForm)
                });
                
                const data = await response.json();
                
                if (data.success && data.redirect) {
                    // Redirigir al formulario del trámite
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.message || 'Error al procesar el trámite');
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error al iniciar el trámite: ' + error.message);
                this.cargando = false;
            }
        }
    }
}
</script>
@endsection