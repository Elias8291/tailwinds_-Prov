            // Función para aceptar términos y avanzar
            aceptarTerminos() {
                if (!this.terminosAceptados) {
                    Swal.fire({
                        title: 'Términos y Condiciones',
                        text: 'Debe aceptar los términos y condiciones para continuar.',
                        icon: 'warning',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#9d2449'
                    });
                    return;
                }
                
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Guardando aceptación de términos',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Enviar aceptación al servidor
                fetch('/tramites-solicitante/aceptar-terminos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tramite_id: this.tramiteId,
                        tipo_tramite: this.tipoTramite
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Guardar aceptación en localStorage
                        const terminosKey = `terminos_aceptados_${this.tipoTramite}_${this.rfc || 'sin_rfc'}`;
                        localStorage.setItem(terminosKey, JSON.stringify({
                            aceptado: true,
                            fecha: new Date().toISOString(),
                            tipoTramite: this.tipoTramite,
                            rfc: this.rfc
                        }));
                        
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            title: '¡Términos Aceptados!',
                            text: 'Continuando con el registro...',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            timerProgressBar: true,
                            background: '#fff',
                            iconColor: '#9d2449',
                            customClass: {
                                popup: 'rounded-xl shadow-xl border border-gray-100',
                                title: 'text-gray-800 font-bold',
                                htmlContainer: 'text-gray-600'
                            }
                        });
                        
                        // Avanzar al siguiente paso
                        setTimeout(() => {
                            this.currentStep = 1;
                        }, 1500);
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Error al procesar la aceptación de términos',
                            icon: 'error',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#9d2449'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Error de conexión al procesar la aceptación de términos',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#9d2449'
                    });
                });
            },

            <!-- Form Sections Container -->
            <div class="min-h-[400px] sm:min-h-[500px] relative">
                <!-- Sección Loading Indicator para cambio de pasos -->
                <div x-data="{ showTransition: false }"
                     x-init="
                        $watch('currentStep', () => {
                            showTransition = true;
                            setTimeout(() => { showTransition = false; }, 300);
                        })
                     "
                     x-show="showTransition"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 bg-white/80 backdrop-blur-sm z-40 flex items-center justify-center rounded-lg">
                    
                    <div class="text-center">
                        <div class="w-8 h-8 border-2 border-[#9d2449] border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">Cargando sección...</p>
                    </div>
                </div>
                
                <!-- Form Sections -->
                <div class="max-w-6xl mx-auto mt-4 sm:mt-8 md:mt-16 bg-white rounded-xl shadow-lg p-3 sm:p-4 md:p-8 relative z-10"
                     x-data="{ 
                        currentStep: 0,  // Siempre comenzar en paso 0 (términos y condiciones)
                        totalSteps: 4,
                        tipoPersona: 'Física',
                        isPersonaFisica: true,
                        rfc: '',
                        curp: '',
                        tramiteId: null,
                        terminosAceptados: false,
                        tipoTramite: 'inscripcion',
                        steps: [
                            {number: '��', label: 'Términos y Condiciones'},
                            {number: '01', label: 'Datos Generales'},
                            {number: '02', label: 'Domicilio'},
                            {number: '03', label: 'Documentos'}
                        ],
                     }">
                    <!-- Términos y Condiciones (Paso 0) -->
                    <div x-show="currentStep === 0"
                         x-cloak
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200 transform"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2">
                        <x-formularios.seccion-terminos 
                            :tipoTramite="$datosTramite['tipo_tramite'] ?? 'inscripcion'"
                            :rfc="$datosTramite['rfc'] ?? ''"
                            :tipoPersona="$datosTramite['tipo_persona'] ?? 'Física'"
                        />
                    </div>

                    <!-- Datos Generales (Paso 1) -->
                    <div x-show="currentStep === 1"
                         x-cloak
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200 transform"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-4">
                        @include('components.formularios.seccion-datos-generales', [
                            'datosTramite' => isset($datosTramite) ? $datosTramite : [],
                            'datosSolicitante' => isset($solicitante) ? [
                                'rfc' => $solicitante->rfc ?? $datosTramite['rfc'] ?? '',
                                'curp' => $solicitante->curp ?? $datosTramite['curp'] ?? '',
                                'tipo_persona' => $solicitante->tipo_persona ?? $datosTramite['tipo_persona'] ?? 'Física',
                                'nombre_completo' => $solicitante->nombre_completo ?? $datosTramite['nombre_completo'] ?? '',
                                'razon_social' => $solicitante->razon_social ?? $datosTramite['razon_social'] ?? '',
                                'giro' => $solicitante->giro ?? $datosTramite['giro'] ?? ''
                            ] : []
                        ])
                    </div>

                    <!-- ... rest of the sections ... -->
                </div>
            </div>
        </div>
    </div>
</div> 