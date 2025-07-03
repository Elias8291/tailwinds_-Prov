@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <!-- Título del Trámite -->
    <div class="max-w-6xl mx-auto mb-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 backdrop-blur-lg border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-3 shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent animate-shimmer"></div>
                    <svg class="w-6 h-6 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                        {{ ucfirst($datosTramite['tipo_tramite'] ?? 'Trámite') }} al Padrón de Proveedores
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Complete el formulario con la información requerida</p>
                </div>
            </div>
        </div>
    </div>

            <!-- Form Container -->
    <div class="max-w-6xl mx-auto mt-4 sm:mt-8 md:mt-16 bg-white rounded-xl shadow-lg p-3 sm:p-4 md:p-8 relative z-10"
         x-data="{ 
            currentStep: 0,  // Cambiado a 0 para incluir términos y condiciones
            totalSteps: 4,   // Incrementado para incluir términos
            tipoPersona: 'Física',
            isPersonaFisica: true,
            rfc: '',
            curp: '',
            tramiteId: null,
            terminosAceptados: false,
            tipoTramite: 'inscripcion',
            steps: [
                {number: '📋', label: 'Términos y Condiciones'},
                {number: '01', label: 'Datos Generales'},
                {number: '02', label: 'Domicilio'},
                {number: '03', label: 'Documentos'}
            ],
            isLoading: true,
            loadingError: false,
            loadingMessage: 'Cargando información del trámite...',
            
            async init() {

                
                // NO mostrar la página hasta que todo esté cargado
                // this.$el.classList.remove('invisible'); // Lo haremos al final
                
                // Obtener datos del trámite desde el controlador
                try {
                    this.loadingMessage = 'Cargando datos del trámite...';
                    const response = await fetch('/tramites-solicitante/datos-tramite');
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();

                    
                    this.loadingMessage = 'Procesando información...';
                    
                    // Actualizar datos con validación completa
                    await new Promise(resolve => setTimeout(resolve, 300));
                    
                    // Verificar si ya se aceptaron términos para este trámite
                    const terminosKey = `terminos_aceptados_${data.tipo_tramite || 'inscripcion'}_${data.rfc || 'sin_rfc'}`;
                    const terminosGuardados = localStorage.getItem(terminosKey);
                    
                    if (terminosGuardados) {
                        const terminosData = JSON.parse(terminosGuardados);
                        this.terminosAceptados = terminosData.aceptado || false;
                        // Si ya aceptó términos, comenzar en paso 1
                        this.currentStep = this.terminosAceptados ? (data.paso_inicial || data.progreso_tramite || 1) : 0;
                    } else {
                        // Si no hay términos guardados, comenzar en paso 0
                        this.currentStep = 0;
                    }
                    
                    this.tipoPersona = data.tipo_persona || 'Física';
                    this.isPersonaFisica = this.tipoPersona === 'Física';
                    this.totalSteps = this.isPersonaFisica ? 4 : 7; // +1 por términos
                    this.rfc = data.rfc || '';
                    this.curp = data.curp || '';
                    this.tramiteId = data.tramite_id || null;
                    this.tipoTramite = data.tipo_tramite || 'inscripcion';
                    
                    this.steps = this.isPersonaFisica ? 
                        [
                            {number: '📋', label: 'Términos y Condiciones'},
                            {number: '01', label: 'Datos Generales'},
                            {number: '02', label: 'Domicilio'},
                            {number: '03', label: 'Documentos'}
                        ] : 
                        [
                            {number: '📋', label: 'Términos y Condiciones'},
                            {number: '01', label: 'Datos Generales'},
                            {number: '02', label: 'Domicilio'},
                            {number: '03', label: 'Constitución'},
                            {number: '04', label: 'Accionistas'},
                            {number: '05', label: 'Apoderado Legal'},
                            {number: '06', label: 'Documentos'}
                        ];
                    

                    this.loadingMessage = 'Casi listo...';
                    
                    // Pequeño delay para mostrar el mensaje final
                    await new Promise(resolve => setTimeout(resolve, 500));
                    
                } catch (error) {
                    console.error('❌ Error al cargar datos:', error);
                    this.loadingError = true;
                    this.loadingMessage = 'Cargando configuración básica...';
                    
                    // Fallback: usar datos por defecto
                    await new Promise(resolve => setTimeout(resolve, 1000));
                }
                
                // Finalizar loading
                this.isLoading = false;

            },
            
            // Función para aceptar términos y avanzar
            aceptarTerminos() {
                if (!this.terminosAceptados) {
                    alert('Debe aceptar los términos y condiciones para continuar.');
                    return;
                }
                
                // Guardar aceptación en localStorage
                const terminosKey = `terminos_aceptados_${this.tipoTramite}_${this.rfc || 'sin_rfc'}`;
                localStorage.setItem(terminosKey, JSON.stringify({
                    aceptado: true,
                    fecha: new Date().toISOString(),
                    tipoTramite: this.tipoTramite,
                    rfc: this.rfc
                }));
                
                // Avanzar al siguiente paso
                this.currentStep = 1;
            },
            
            // Función para obtener contenido de términos específico
            obtenerTerminosEspecificos() {
                const terminos = {
                    inscripcion: {
                        titulo: 'Términos y Condiciones - Inscripción al Padrón',
                        descripcion: 'Para su proceso de inscripción inicial al padrón de proveedores',
                        condiciones: [
                            {
                                titulo: 'Requisitos para Inscripción',
                                icono: 'fas fa-user-plus',
                                color: 'blue',
                                contenido: 'Como solicitante de primera inscripción, deberá cumplir con todos los requisitos establecidos por la normativa vigente. La documentación debe ser actual y veraz.',
                                puntos: [
                                    'Documentación completa y actualizada',
                                    'Información veraz y verificable',
                                    'Cumplimiento de requisitos fiscales',
                                    'Capacidad técnica y financiera demostrable'
                                ]
                            },
                            {
                                titulo: 'Proceso de Evaluación',
                                icono: 'fas fa-search',
                                color: 'green',
                                contenido: 'Su solicitud será evaluada por nuestro equipo técnico especializado para verificar el cumplimiento de todos los criterios.',
                                puntos: [
                                    'Revisión documental completa',
                                    'Verificación de datos con autoridades',
                                    'Evaluación de capacidades técnicas',
                                    'Tiempo estimado: 15-20 días hábiles'
                                ]
                            }
                        ]
                    },
                    renovacion: {
                        titulo: 'Términos y Condiciones - Renovación de Registro',
                        descripcion: 'Para la renovación de su registro en el padrón de proveedores',
                        condiciones: [
                            {
                                titulo: 'Renovación Oportuna',
                                icono: 'fas fa-sync-alt',
                                color: 'orange',
                                contenido: 'La renovación debe realizarse antes del vencimiento para mantener su estatus activo sin interrupciones.',
                                puntos: [
                                    'Presentar solicitud 30 días antes del vencimiento',
                                    'Actualizar información que haya cambiado',
                                    'Mantener capacidad técnica y financiera',
                                    'Estar al corriente en obligaciones fiscales'
                                ]
                            },
                            {
                                titulo: 'Documentación Actualizada',
                                icono: 'fas fa-file-alt',
                                color: 'purple',
                                contenido: 'Debe presentar documentación actualizada que demuestre el mantenimiento de sus capacidades.',
                                puntos: [
                                    'Documentos no mayores a 3 meses',
                                    'Estados financieros actualizados',
                                    'Constancias de cumplimiento fiscal',
                                    'Certificaciones vigentes'
                                ]
                            }
                        ]
                    },
                    actualizacion: {
                        titulo: 'Términos y Condiciones - Actualización de Datos',
                        descripcion: 'Para la actualización de información en su registro',
                        condiciones: [
                            {
                                titulo: 'Cambios Significativos',
                                icono: 'fas fa-edit',
                                color: 'indigo',
                                contenido: 'Debe notificar cualquier cambio relevante en su información registrada dentro de 30 días naturales.',
                                puntos: [
                                    'Cambios en razón social o denominación',
                                    'Modificaciones en domicilio fiscal',
                                    'Cambios en representación legal',
                                    'Actualizaciones en capacidad técnica'
                                ]
                            },
                            {
                                titulo: 'Proceso de Actualización',
                                icono: 'fas fa-cogs',
                                color: 'teal',
                                contenido: 'Las actualizaciones serán revisadas para verificar que no afecten su elegibilidad en el padrón.',
                                puntos: [
                                    'Revisión de impacto en capacidades',
                                    'Verificación de nueva documentación',
                                    'Mantenimiento de requisitos mínimos',
                                    'Proceso expedito: 5-10 días hábiles'
                                ]
                            }
                        ]
                    }
                };
                
                return terminos[this.tipoTramite] || terminos.inscripcion;
            }
         }"
         @next-step="
            if (currentStep < totalSteps - 1) {
                currentStep++;
            }
         "
         @prev-step="
            if (currentStep > 0) {
                currentStep--;
            }
         "
         class="invisible">
         
        <!-- Loading Overlay Elegante -->
        <div x-show="isLoading" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-white/95 backdrop-blur-sm z-50 flex items-center justify-center rounded-xl">
            
            <div class="text-center max-w-sm mx-auto p-8">
                <!-- Spinner elegante con Tailwind -->
                <div class="relative mb-6">
                    <div class="w-20 h-20 mx-auto relative">
                        <!-- Anillo externo -->
                        <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
                        <!-- Anillo animado -->
                        <div class="absolute inset-0 border-4 border-transparent border-t-[#9d2449] rounded-full animate-spin"></div>
                        <!-- Punto central -->
                        <div class="absolute inset-4 bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Mensaje de carga -->
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Preparando Formulario</h3>
                <p class="text-sm text-gray-600 mb-4" x-text="loadingMessage">Cargando datos del trámite...</p>
                
                <!-- Barra de progreso con Tailwind -->
                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden mb-4">
                    <div class="h-full bg-gradient-to-r from-[#9d2449] to-[#7a1d37] rounded-full animate-pulse w-full"></div>
                </div>
                
                <!-- Mensaje de error si ocurre -->
                <div x-show="loadingError" class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-amber-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="text-xs text-amber-700">Continuando con datos básicos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contador de Tiempo Límite - Dentro del contenedor -->
        @if(isset($tiempoLimite))
        <div class="mb-6"
             x-data="{
                vencido: {{ $tiempoLimite['vencido'] ? 'true' : 'false' }},
                timestampLimite: {{ $tiempoLimite['timestamp_limite'] ?? 0 }},
                horasRestantes: {{ $tiempoLimite['horas_restantes'] ?? 0 }},
                minutosRestantes: {{ $tiempoLimite['minutos_restantes'] ?? 0 }},
                segundosRestantes: {{ $tiempoLimite['segundos_restantes'] ?? 0 }},
                color: '{{ $tiempoLimite['color'] ?? 'green' }}',
                
                init() {
                    if (!this.vencido && this.timestampLimite > 0) {
                        this.actualizarContador();
                        setInterval(() => {
                            this.actualizarContador();
                        }, 1000);
                    }
                },
                
                actualizarContador() {
                    const ahora = Math.floor(Date.now() / 1000);
                    const diferencia = this.timestampLimite - ahora;
                    
                    if (diferencia <= 0) {
                        this.vencido = true;
                        this.horasRestantes = 0;
                        this.minutosRestantes = 0;
                        this.segundosRestantes = 0;
                        this.color = 'red';
                        return;
                    }
                    
                    this.horasRestantes = Math.floor(diferencia / 3600);
                    this.minutosRestantes = Math.floor((diferencia % 3600) / 60);
                    this.segundosRestantes = diferencia % 60;
                    
                    // Actualizar color según tiempo restante
                    if (this.horasRestantes <= 6) {
                        this.color = 'red';
                    } else if (this.horasRestantes <= 12) {
                        this.color = 'yellow';
                    } else {
                        this.color = 'green';
                    }
                }
             }">
            <div class="rounded-lg p-3 tiempo-limite-container-mini {{ $tiempoLimite['vencido'] ? 'tiempo-vencido' : 'tiempo-color-' . $tiempoLimite['color'] }}">
                <div class="flex items-center justify-between">
                    <!-- Información compacta -->
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg flex-shrink-0" 
                             :class="{
                                'bg-red-500': vencido || color === 'red',
                                'bg-yellow-500': !vencido && color === 'yellow',
                                'bg-blue-500': !vencido && (color === 'blue' || color === 'green')
                             }">
                            <div class="w-4 h-4 text-white relative">
                                <!-- Emoji como icono -->
                                <span class="text-sm" x-show="vencido">⚠️</span>
                                <span class="text-sm" x-show="!vencido && color === 'red'">🔥</span>
                                <span class="text-sm" x-show="!vencido && color === 'yellow'">⚡</span>
                                <span class="text-sm" x-show="!vencido && (color === 'blue' || color === 'green')">⏰</span>
                            </div>
                        </div>
                        
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold truncate" 
                                    :class="{
                                        'text-red-800': vencido || color === 'red',
                                        'text-yellow-800': !vencido && color === 'yellow',
                                        'text-blue-800': !vencido && (color === 'blue' || color === 'green')
                                    }">
                                    <span x-show="vencido">¡Tiempo Vencido!</span>
                                    <span x-show="!vencido && color === 'red'">¡Tiempo Crítico!</span>
                                    <span x-show="!vencido && color === 'yellow'">¡Poco Tiempo!</span>
                                    <span x-show="!vencido && (color === 'blue' || color === 'green')">Tiempo Límite</span>
                                </h4>
                                
                                <!-- Fechas mini -->
                                <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500">
                                    <span class="bg-gray-100 px-2 py-1 rounded-full">
                                        📅 {{ $tiempoLimite['fecha_limite_corta'] }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="text-xs mt-0.5" 
                               :class="{
                                    'text-red-600': vencido || color === 'red',
                                    'text-yellow-600': !vencido && color === 'yellow',
                                    'text-blue-600': !vencido && (color === 'blue' || color === 'green')
                                }">
                                <span x-show="vencido">El plazo de 48h ha expirado</span>
                                <span x-show="!vencido">48 horas para completar</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Contador compacto -->
                    <div class="text-right flex-shrink-0">
                        <div x-show="!vencido" class="relative">
                            <!-- Contador principal -->
                            <div class="text-lg sm:text-xl font-bold font-mono tiempo-contador-mini relative">
                                <div class="flex items-center gap-1">
                                    <!-- Horas - Rojo elegante -->
                                    <div class="text-center text-red-600">
                                        <div x-text="String(horasRestantes).padStart(2, '0')" class="leading-tight">{{ str_pad($tiempoLimite['horas_restantes'], 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs opacity-80 text-red-500">h</div>
                                    </div>
                                    <div class="text-sm text-gray-400">:</div>
                                    <!-- Minutos - Cyan/Turquesa -->
                                    <div class="text-center text-cyan-600">
                                        <div x-text="String(minutosRestantes).padStart(2, '0')" class="leading-tight">{{ str_pad($tiempoLimite['minutos_restantes'], 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs opacity-80 text-cyan-500">m</div>
                                    </div>
                                    <div class="text-sm text-gray-400">:</div>
                                    <!-- Segundos - Púrpura elegante -->
                                    <div class="text-center text-purple-600">
                                        <div x-text="String(segundosRestantes).padStart(2, '0')" class="leading-tight">{{ str_pad($tiempoLimite['segundos_restantes'], 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs opacity-80 text-purple-500">s</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Barra de progreso mini con gradiente elegante -->
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-1 overflow-hidden">
                                <div class="h-full transition-all duration-1000 rounded-full bg-gradient-to-r from-red-500 via-cyan-500 to-purple-500"
                                     :style="'width: ' + (100 - ((horasRestantes * 3600 + minutosRestantes * 60 + segundosRestantes) / (48 * 3600) * 100)) + '%'">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mensaje de vencido compacto -->
                        <div x-show="vencido" class="text-center">
                            <div class="text-lg font-bold text-red-700">
                                💀 VENCIDO
                            </div>
                            <div class="text-xs text-red-500 mt-1">
                                00:00:00
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Mobile Progress Indicator -->
        <div class="md:hidden mb-4 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-800 text-white shadow-lg">
                <span class="text-xl font-bold" x-text="currentStep">0</span>
                <span class="text-xs">/</span>
                <span class="text-sm" x-text="totalSteps - 1"></span>
            </div>
            <div class="mt-1 text-xs text-gray-600 font-medium" x-text="steps[currentStep]?.label || ''"></div>
            <div class="mt-2 text-xs text-gray-500">
                <span x-text="Math.round((currentStep / (totalSteps - 1)) * 100) + '%'">0%</span> Completado
            </div>
        </div>

        <!-- Desktop Progress Container -->
        <div class="hidden md:block">
            <div class="max-w-6xl mx-auto mb-10 h-[100px] flex flex-col md:flex-row items-center gap-6">
                <!-- Progress Info -->
                <div class="flex flex-col items-center min-w-[80px]">
                    <span class="text-2xl md:text-3xl font-bold text-red-800 h-[36px] flex items-center" x-text="Math.round((currentStep / (totalSteps - 1)) * 100) + '%'">0%</span>
                    <span class="text-xs uppercase text-gray-500 tracking-wide">Completado</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full h-2 relative">
                    <div class="h-2 bg-gray-200 rounded-full absolute inset-0">
                        <div class="h-full bg-red-800 rounded-full transition-all duration-500 transform-gpu" x-bind:style="'width: ' + (currentStep / (totalSteps - 1) * 100) + '%'"></div>
                    </div>
                </div>
            </div>

            <!-- Progress Tracker (Steps) - Only visible on desktop -->
            <div class="relative max-w-6xl mx-auto mb-12 h-[80px]">
                <div class="absolute top-4 left-10 right-10 h-0.5 bg-gray-200"></div>
                <div class="absolute top-4 left-10 h-0.5 bg-red-800 transition-all duration-600 transform-gpu" x-bind:style="'width: ' + (currentStep / (totalSteps - 1) * 100) + '%'"></div>
                <div class="flex justify-between">
                    <template x-for="(step, index) in steps" :key="index">
                        <div class="flex flex-col items-center relative z-10 w-24">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 border-2 border-gray-200 text-gray-500 font-semibold text-sm transition-all duration-300 transform-gpu"
                                 :class="{
                                    'bg-red-800 border-red-800 text-white': currentStep > index,
                                    'bg-red-800 border-red-800 text-white shadow-[0_0_0_3px_rgba(157,36,73,0.2)]': currentStep === index,
                                    'bg-gray-100 border-gray-200 text-gray-500': currentStep < index
                                 }"
                                 @click="if(currentStep > index || (index === 0 && terminosAceptados)) currentStep = index">
                                <span x-text="step.number"></span>
                            </div>
                            <span class="mt-2 text-xs text-center text-gray-500 font-medium" x-text="step.label"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

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
            <div class="max-w-6xl mx-auto">
                
                <!-- =============================================== -->
                <!-- PASO 0: TÉRMINOS Y CONDICIONES ESPECÍFICOS    -->
                <!-- =============================================== -->
                <div x-show="currentStep === 0" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2">
                    
                    <div class="max-w-4xl mx-auto space-y-6">
                        
                        <!-- Encabezado Simple -->
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-full mb-4 shadow-lg">
                                <i class="fas fa-file-contract text-2xl text-white"></i>
                                        </div>
                            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                                                Términos y Condiciones
                                            </h1>
                            <p class="text-gray-600">
                                Por favor lea y acepte los siguientes términos para continuar
                                            </p>
                                        </div>

                        <!-- Información del Trámite -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mb-3 shadow-md">
                                        <i class="fas fa-clipboard-list text-white"></i>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1">Trámite</p>
                                    <p class="font-semibold text-blue-700 capitalize" x-text="tipoTramite">Inscripción</p>
                                </div>
                                <div class="flex flex-col items-center" x-show="rfc">
                                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mb-3 shadow-md">
                                        <i class="fas fa-hashtag text-white"></i>
                            </div>
                                    <p class="text-sm text-gray-600 mb-1">RFC</p>
                                    <p class="font-semibold text-green-700 font-mono" x-text="rfc">---</p>
                                        </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mb-3 shadow-md">
                                        <i class="fas fa-user text-white"></i>
                                        </div>
                                    <p class="text-sm text-gray-600 mb-1">Tipo</p>
                                    <p class="font-semibold text-purple-700" x-text="tipoPersona">Física</p>
                                    </div>
                                        </div>
                                    </div>
                                    
                        <!-- Términos Principales -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-list-check text-[#9d2449]"></i>
                                        </div>
                                <h2 class="text-xl font-bold text-gray-800">Condiciones del Trámite</h2>
                        </div>

                            <div class="space-y-4 text-gray-700">
                                <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                    <div class="w-6 h-6 bg-[#9d2449] rounded-full flex items-center justify-center mr-4 mt-0.5">
                                        <i class="fas fa-check text-white text-xs"></i>
                            </div>
                                    <p>Toda la información proporcionada debe ser <strong>veraz y actualizada</strong>.</p>
                                        </div>
                                
                                <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                    <div class="w-6 h-6 bg-[#9d2449] rounded-full flex items-center justify-center mr-4 mt-0.5">
                                        <i class="fas fa-check text-white text-xs"></i>
                                                        </div>
                                    <p>Debe cumplir con todos los <strong>requisitos establecidos</strong> para el tipo de trámite.</p>
                                            </div>
                                            
                                <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                    <div class="w-6 h-6 bg-[#9d2449] rounded-full flex items-center justify-center mr-4 mt-0.5">
                                        <i class="fas fa-clock text-white text-xs"></i>
                                                        </div>
                                    <p>El proceso de revisión puede tomar entre <strong>15 a 20 días hábiles</strong>.</p>
                                            </div>
                                            
                                <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                    <div class="w-6 h-6 bg-[#9d2449] rounded-full flex items-center justify-center mr-4 mt-0.5">
                                        <i class="fas fa-calendar-check text-white text-xs"></i>
                                                        </div>
                                    <p>Debe asistir a un <strong>cotejo presencial</strong> una vez aprobado el trámite.</p>
                                            </div>
                                            
                                <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                    <div class="w-6 h-6 bg-[#9d2449] rounded-full flex items-center justify-center mr-4 mt-0.5">
                                        <i class="fas fa-shield-alt text-white text-xs"></i>
                                                        </div>
                                    <p>Sus datos serán protegidos conforme a la <strong>normativa vigente</strong>.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                        <!-- Advertencias Importantes -->
                        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl border border-amber-200 p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                                        </div>
                                <h3 class="text-lg font-semibold text-amber-800">Importante</h3>
                                                        </div>
                            <div class="space-y-3 text-amber-800">
                                                    <div class="flex items-start">
                                    <span class="text-amber-600 mr-3 mt-1">•</span>
                                    <p>Si no asiste al cotejo presencial, el trámite se reiniciará.</p>
                                                        </div>
                                            <div class="flex items-start">
                                    <span class="text-amber-600 mr-3 mt-1">•</span>
                                    <p>La información falsa puede resultar en la cancelación del proceso.</p>
                                                </div>
                                                <div class="flex items-start">
                                    <span class="text-amber-600 mr-3 mt-1">•</span>
                                    <p>Las notificaciones se enviarán a su correo electrónico.</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                        <!-- Aceptación -->
                        <div class="bg-white rounded-xl border-2 border-[#9d2449] p-6 shadow-lg">
                                <div class="flex items-start space-x-4">
                                        <input type="checkbox" 
                                               x-model="terminosAceptados"
                                               id="aceptar-terminos"
                                       class="w-5 h-5 text-[#9d2449] border-2 border-[#9d2449] rounded focus:ring-[#9d2449] mt-1">
                                    <div class="flex-1">
                                        <label for="aceptar-terminos" class="cursor-pointer">
                                        <h3 class="text-lg font-bold text-[#9d2449] mb-2 flex items-center">
                                            <i class="fas fa-file-signature mr-2"></i>
                                            Acepto los términos y condiciones
                                            </h3>
                                        <p class="text-gray-700">
                                            Declaro haber leído y aceptado todas las condiciones mencionadas. 
                                            Confirmo que la información que proporcionaré es veraz y completa.
                                            </p>
                                        </label>
                                    </div>
                                </div>
                                
                            <div class="mt-6 text-center">
                                    <button @click="aceptarTerminos()" 
                                            :disabled="!terminosAceptados"
                                            :class="terminosAceptados ? 
                                            'bg-gradient-to-r from-[#9d2449] to-[#7a1d37] hover:from-[#8a203f] hover:to-[#6b1a30] text-white shadow-lg hover:shadow-xl transform hover:scale-105' : 
                                            'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                        class="px-8 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center mx-auto space-x-2">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>Continuar con el Trámite</span>
                                    </button>
                                </div>
                            </div>
                    </div>
                </div>

                <!-- Datos Generales -->
                <div x-show="currentStep === 1" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-4">
                    
                                         <!-- Contenido siempre listo (no hay skeleton individual) -->
                     @include('components.formularios.seccion-datos-generales', [
                         'datosTramite' => isset($datosTramite) ? $datosTramite : [],
                            'datosSolicitante' => isset($solicitante) ? [
                                'rfc' => $solicitante->rfc ?? $datosTramite['rfc'] ?? '',
                                'curp' => $solicitante->curp ?? $datosTramite['curp'] ?? '',
                                'tipo_persona' => $solicitante->tipo_persona ?? $datosTramite['tipo_persona'] ?? 'Física',
                                'nombre_completo' => $solicitante->nombre_completo ?? $datosTramite['nombre_completo'] ?? '',
                                'razon_social' => $solicitante->razon_social ?? $datosTramite['razon_social'] ?? '',
                                'giro' => $solicitante->giro ?? $datosTramite['giro'] ?? ''
                            ] : [
                                'rfc' => $datosTramite['rfc'] ?? '',
                                'curp' => $datosTramite['curp'] ?? '',
                                'tipo_persona' => $datosTramite['tipo_persona'] ?? 'Física',
                                'nombre_completo' => $datosTramite['nombre_completo'] ?? '',
                                'razon_social' => $datosTramite['razon_social'] ?? '',
                                'giro' => $datosTramite['giro'] ?? ''
                            ]
                        ])
                    </div>
                </div>

                <!-- Domicilio -->
                <div x-show="currentStep === 2" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-4"
                     @next-step="currentStep++">
                    
                    <!-- Contenido siempre listo (no hay skeleton individual) -->
                    @include('components.formularios.seccion-domicilio', [
                        'datosTramite' => isset($datosTramite) ? $datosTramite : [],
                        'direccion' => isset($direccion) ? $direccion : null
                    ])
                </div>

                <!-- Constitución (Personas Morales) -->
                <div x-show="currentStep === 3 && !isPersonaFisica" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-4"
                     @next-step="currentStep++">
                    
                    <!-- Contenido siempre listo (no hay skeleton individual) -->
                    @include('components.formularios.seccion-constitucion', [
                        'datosTramite' => isset($datosTramite) ? $datosTramite : [],
                        'datosConstitutivo' => isset($datosConstitutivo) ? $datosConstitutivo : null,
                        'modificacionEstatuto' => isset($modificacionEstatuto) ? $modificacionEstatuto : null,
                        'instrumentoNotarial' => isset($instrumentoNotarial) ? $instrumentoNotarial : null
                    ])
                </div>

                <!-- Accionistas (Personas Morales) -->
                <div x-show="currentStep === 4 && !isPersonaFisica" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-4"
                     @next-step="currentStep++">
                    
                    <!-- Contenido siempre listo (no hay skeleton individual) -->
                    @include('components.formularios.seccion-accionistas', [
                        'datosTramite' => isset($datosTramite) ? $datosTramite : [],
                        'accionistas' => isset($accionistas) ? $accionistas : []
                    ])
                </div>

                <!-- Apoderado Legal (Personas Morales) -->
                <div x-show="currentStep === 5 && !isPersonaFisica" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-4"
                     @next-step="currentStep++">
                    
                    <!-- Contenido siempre listo (no hay skeleton individual) -->
                    @include('components.formularios.seccion-apoderado', [
                        'datosTramite' => isset($datosTramite) ? $datosTramite : [],
                        'representanteLegal' => isset($representanteLegal) ? $representanteLegal : null
                    ])
                </div>

                <!-- Documentos -->
                <div x-show="(currentStep === 3 && isPersonaFisica) || (currentStep === 6 && !isPersonaFisica)" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-4">
                    
                    <!-- Contenido siempre listo (no hay skeleton individual) -->
                    @include('components.formularios.seccion-documentos', [
                        'datosTramite' => isset($datosTramite) ? $datosTramite : [],
                        'documentos' => isset($documentos) ? $documentos : [],
                        'documentosRequeridos' => isset($documentosRequeridos) ? $documentosRequeridos : []
                    ])
                </div>


            </div>
        </div>
    </div>

    <!-- Controles de Scroll Inteligentes -->
    <div id="scroll-controls" 
         class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-40 opacity-0 transition-all duration-500 ease-out"
         x-data="{ 
            visible: false,
            scrolling: false,
            autoShow: true,
            canScrollUp: false,
            canScrollDown: false,
            
            init() {
                // Mostrar automáticamente después de 1.5 segundos
                setTimeout(() => {
                    this.autoShow = true;
                    this.checkVisibility();
                }, 1500);
                
                this.checkVisibility();
                window.addEventListener('scroll', () => {
                    if (!this.scrolling) {
                        this.checkVisibility();
                    }
                });
            },
            
            checkVisibility() {
                const scrollHeight = document.documentElement.scrollHeight;
                const clientHeight = document.documentElement.clientHeight;
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Determinar direcciones disponibles
                this.canScrollUp = scrollTop > 100;
                this.canScrollDown = scrollTop < scrollHeight - clientHeight - 100;
                
                // Mostrar si hay contenido suficiente y al menos una dirección disponible
                this.visible = this.autoShow && 
                               (scrollHeight > clientHeight + 300) && 
                               (this.canScrollUp || this.canScrollDown);
                
                // Actualizar clases
                const controls = document.getElementById('scroll-controls');
                if (this.visible) {
                    controls.classList.remove('opacity-0', 'pointer-events-none', 'scale-75');
                    controls.classList.add('opacity-100', 'scale-100');
                } else {
                    controls.classList.add('opacity-0', 'pointer-events-none', 'scale-75');
                    controls.classList.remove('opacity-100', 'scale-100');
                }
            },
            
            async scrollUp() {
                if (this.scrolling || !this.canScrollUp) return;
                
                this.scrolling = true;
                const currentScroll = window.pageYOffset;
                const windowHeight = window.innerHeight;
                
                let targetScroll = Math.max(0, currentScroll - windowHeight * 0.7);
                
                window.scrollTo({
                    top: targetScroll,
                    behavior: 'smooth'
                });
                
                setTimeout(() => {
                    this.scrolling = false;
                    this.checkVisibility();
                }, 1000);
            },
            
            async scrollDown() {
                if (this.scrolling || !this.canScrollDown) return;
                
                this.scrolling = true;
                const currentScroll = window.pageYOffset;
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;
                
                let targetScroll = Math.min(
                    documentHeight - windowHeight,
                    currentScroll + windowHeight * 0.7
                );
                
                window.scrollTo({
                    top: targetScroll,
                    behavior: 'smooth'
                });
                
                setTimeout(() => {
                    this.scrolling = false;
                    this.checkVisibility();
                }, 1000);
            }
         }">
        
        <!-- Contenedor de controles -->
        <div class="flex flex-col items-center space-y-1">
            
            <!-- Botón de scroll hacia arriba -->
            <div x-show="canScrollUp" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 -translate-y-2 scale-75"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-2 scale-75"
                 @click="scrollUp()"
                 class="group cursor-pointer select-none flex items-center space-x-2 px-3 py-2 rounded-full bg-white/90 backdrop-blur-sm shadow-md hover:shadow-lg border border-gray-200 hover:border-blue-300 transition-all duration-300">
                
                <!-- Botón circular pequeño -->
                <div class="relative w-8 h-8 bg-gradient-to-t from-gray-100 to-white group-hover:from-blue-100 group-hover:to-blue-50 rounded-full shadow-sm transition-all duration-300 flex items-center justify-center">
                    <!-- Flecha hacia arriba -->
                    <svg class="w-3 h-3 text-gray-600 group-hover:text-blue-600 transform transition-all duration-300 group-hover:-translate-y-0.5" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24"
                         stroke-width="3">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              d="M5 15l7-7 7 7"/>
                    </svg>
                </div>
                
                <!-- Texto al lado -->
                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700 transition-colors duration-300">
                    Subir
                </span>
            </div>
            
            <!-- Separador elegante -->
            <div class="flex justify-center my-1" x-show="canScrollUp && canScrollDown">
                <div class="w-8 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
            </div>
            
            <!-- Botón de scroll hacia abajo -->
            <div x-show="canScrollDown" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-75"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-75"
                 @click="scrollDown()"
                 class="group cursor-pointer select-none flex items-center space-x-2 px-3 py-2 rounded-full bg-white/90 backdrop-blur-sm shadow-md hover:shadow-lg border border-gray-200 hover:border-blue-300 transition-all duration-300">
                
                <!-- Botón circular pequeño -->
                <div class="relative w-8 h-8 bg-gradient-to-b from-gray-100 to-white group-hover:from-blue-100 group-hover:to-blue-50 rounded-full shadow-sm transition-all duration-300 flex items-center justify-center">
                    <!-- Flecha hacia abajo -->
                    <svg class="w-3 h-3 text-gray-600 group-hover:text-blue-600 transform transition-all duration-300 group-hover:translate-y-0.5" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24"
                         stroke-width="3">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                
                <!-- Texto al lado -->
                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700 transition-colors duration-300">
                    Bajar
                </span>
            </div>
            
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { 
        display: none !important; 
    }
    
    .bg-white {
        background-color: #ffffff !important;
    }
    
    .bg-opacity-90 {
        --tw-bg-opacity: 0.9 !important;
    }

    /* Estilos personalizados para formularios */
    .form-input,
    .form-select,
    .form-textarea {
        @apply w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base;
        @apply border border-gray-200 rounded-lg;
        @apply bg-white;
        @apply transition-all duration-200;
        @apply focus:border-red-800 focus:ring-2 focus:ring-red-800/20;
        @apply placeholder-gray-400;
    }

    /* Contenedor de input con icono */
    .input-icon-container {
        @apply relative flex items-center;
    }

    .input-icon-container i {
        @apply absolute left-3 text-gray-400 pointer-events-none;
        @apply transition-colors duration-200;
    }

    .input-icon-container input,
    .input-icon-container select {
        @apply pl-9;
    }

    .input-icon-container:focus-within i {
        @apply text-red-800;
    }

    /* Estilos para inputs */
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="number"],
    select,
    textarea {
        @apply form-input;
    }

    /* Estilos para labels */
    label {
        @apply text-sm font-medium text-gray-700;
        @apply mb-1 block;
    }

    /* Estilos para botones */
    .btn {
        @apply px-4 sm:px-6 py-2.5 sm:py-3;
        @apply text-sm sm:text-base font-medium;
        @apply rounded-lg;
        @apply transition-all duration-300;
        @apply transform-gpu hover:-translate-y-0.5;
        @apply flex items-center justify-center gap-2;
    }

    .btn-primary {
        @apply bg-red-800 text-white;
        @apply hover:bg-red-900;
        @apply shadow-sm hover:shadow-md;
    }

    .btn-secondary {
        @apply bg-gray-600 text-white;
        @apply hover:bg-gray-700;
        @apply shadow-sm hover:shadow-md;
    }

    /* Mejoras para móvil */
    @media (max-width: 640px) {
        .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        input, select, textarea {
            font-size: 16px !important;
        }

        .form-section {
            @apply p-4 rounded-lg border border-gray-100;
            @apply bg-white shadow-sm;
        }
    }

    /* Animaciones */
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        @apply transform-gpu scale-[1.01];
    }

    /* Estilos para campos requeridos */
    .required::after {
        content: '*';
        @apply text-red-500 ml-1;
    }

    /* Estilos para mensajes de error */
    .error-message {
        @apply text-xs text-red-500 mt-1;
    }

    /* Estilos para grupos de campos */
    .form-group {
        @apply mb-4 last:mb-0;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }
    
    .animate-shimmer {
        animation: shimmer 2s infinite;
    }
    
    /* Mejoras para el contenedor del título */
    .max-w-6xl.mx-auto.mb-6 .bg-white {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .max-w-6xl.mx-auto.mb-6 .bg-white:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(157, 36, 73, 0.1);
    }

    /* ✨ NUEVAS ANIMACIONES PARA MODALES ✨ */
    @keyframes fade-in {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    @keyframes fade-in-delay {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    @keyframes progress-bar {
        from { 
            width: 0%; 
        }
        to { 
            width: 100%; 
        }
    }

    @keyframes modal-scale-in {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 1.2s ease-out forwards;
    }

    .animate-fade-in-delay {
        animation: fade-in-delay 1.2s ease-out 0.6s forwards;
        opacity: 0;
    }

    .animate-progress-bar {
        animation: progress-bar 2.5s ease-out forwards;
    }

    .animate-modal-scale {
        animation: modal-scale-in 0.3s ease-out forwards;
    }

    .animate-celebration {
        animation: celebration-bounce 1.5s ease-in-out infinite;
    }

    /* Efectos de hover para botones del modal */
    .modal-button-hover:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(157, 36, 73, 0.3);
    }

    /* Animación de pulso personalizada */
    @keyframes custom-pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.05);
        }
    }

    .animate-custom-pulse {
        animation: custom-pulse 2s ease-in-out infinite;
    }

    /* Animación de rotación suave */
    @keyframes gentle-spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .animate-gentle-spin {
        animation: gentle-spin 2s linear infinite;
    }

    /* Backdrop blur mejorado */
    .backdrop-blur-custom {
        backdrop-filter: blur(12px) saturate(180%);
        -webkit-backdrop-filter: blur(12px) saturate(180%);
    }

    /* Sombras personalizadas */
    .shadow-custom {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .shadow-custom-xl {
        box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.3);
    }

    /* ✨ NUEVAS ANIMACIONES PARA PANTALLAS COMPLETAS ✨ */
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes bounce-in {
        0% {
            opacity: 0;
            transform: scale(0.3) translateY(-100px);
        }
        50% {
            opacity: 1;
            transform: scale(1.05) translateY(0);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    @keyframes celebration-float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-30px) rotate(5deg);
        }
    }

    @keyframes twinkle {
        0%, 100% {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.2) rotate(180deg);
        }
    }

    @keyframes progress-wave {
        0% {
            width: 0%;
        }
        100% {
            width: 100%;
        }
    }

    @keyframes shimmer-wave {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.8s ease-out forwards;
    }

    .animate-bounce-in {
        animation: bounce-in 1s ease-out forwards;
    }

    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    .animate-celebration-float {
        animation: celebration-float 4s ease-in-out infinite;
    }

    .animate-twinkle {
        animation: twinkle 2s ease-in-out infinite;
    }

    .animate-progress-wave {
        animation: progress-wave 3s ease-out forwards;
    }

    .animate-shimmer-wave {
        animation: shimmer-wave 2s ease-in-out infinite;
    }

    /* Mejoras de responsive para pantallas completas */
    @media (max-width: 768px) {
        .text-5xl {
            font-size: 2.5rem;
        }
        .text-6xl {
            font-size: 3rem;
        }
        .text-7xl {
            font-size: 3.5rem;
        }
    }

    /* Efectos de glassmorphism */
    .glass-effect {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* ⏰ ESTILOS PARA CONTADOR DE TIEMPO LÍMITE - VERSIÓN MINI */
    .tiempo-limite-container-mini {
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }

    .tiempo-vencido {
        background: linear-gradient(135deg, #fee2e2, #fca5a5) !important;
        border-color: #ef4444 !important;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2) !important;
    }

    .tiempo-color-red {
        background: linear-gradient(135deg, #fee2e2, #fca5a5) !important;
        border-color: #ef4444 !important;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2) !important;
    }

    .tiempo-color-yellow {
        background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
        border-color: #f59e0b !important;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2) !important;
    }

    .tiempo-color-green, .tiempo-color-blue {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2) !important;
    }

    .tiempo-contador-mini {
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        letter-spacing: 1px;
    }

    /* Animaciones elegantes para cada unidad de tiempo */
    @keyframes pulso-horas {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
            text-shadow: 0 0 8px rgba(220, 38, 38, 0.4);
        }
        50% {
            opacity: 0.9;
            transform: scale(1.03);
            text-shadow: 0 0 12px rgba(220, 38, 38, 0.6);
        }
    }

    @keyframes pulso-minutos {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
            text-shadow: 0 0 8px rgba(8, 145, 178, 0.4);
        }
        50% {
            opacity: 0.9;
            transform: scale(1.03);
            text-shadow: 0 0 12px rgba(8, 145, 178, 0.6);
        }
    }

    @keyframes pulso-segundos {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
            text-shadow: 0 0 8px rgba(124, 58, 237, 0.4);
        }
        50% {
            opacity: 0.9;
            transform: scale(1.03);
            text-shadow: 0 0 12px rgba(124, 58, 237, 0.6);
        }
    }

    /* Aplicar animaciones cuando el tiempo es crítico */
    .tiempo-color-red .text-red-600 {
        animation: pulso-horas 2s infinite;
    }

    .tiempo-color-red .text-cyan-600 {
        animation: pulso-minutos 1.8s infinite;
        animation-delay: 0.2s;
    }

    .tiempo-color-red .text-purple-600 {
        animation: pulso-segundos 1.5s infinite;
        animation-delay: 0.4s;
    }

    /* Animación de parpadeo para emojis */
    @keyframes parpadeo-emoji {
        0%, 50%, 100% {
            opacity: 1;
        }
        25%, 75% {
            opacity: 0.6;
        }
    }

    .tiempo-color-red .text-sm {
        animation: parpadeo-emoji 1.5s infinite;
    }

    /* Animación elegante para la barra de progreso con gradiente */
    @keyframes gradiente-glow {
        0%, 100% {
            box-shadow: 0 0 8px rgba(220, 38, 38, 0.3);
        }
        33% {
            box-shadow: 0 0 10px rgba(8, 145, 178, 0.3);
        }
        66% {
            box-shadow: 0 0 12px rgba(124, 58, 237, 0.3);
        }
    }

    .tiempo-limite-container-mini .bg-gradient-to-r {
        animation: gradiente-glow 4s infinite ease-in-out;
    }

    /* Efectos hover elegantes para cada color */
    .tiempo-contador-mini .text-red-600:hover {
        transform: scale(1.08);
        text-shadow: 0 0 16px rgba(220, 38, 38, 0.7);
        transition: all 0.3s ease;
    }

    .tiempo-contador-mini .text-cyan-600:hover {
        transform: scale(1.08);
        text-shadow: 0 0 16px rgba(8, 145, 178, 0.7);
        transition: all 0.3s ease;
    }

    .tiempo-contador-mini .text-purple-600:hover {
        transform: scale(1.08);
        text-shadow: 0 0 16px rgba(124, 58, 237, 0.7);
        transition: all 0.3s ease;
    }

    /* Efectos suaves para los labels */
    .tiempo-contador-mini .text-red-500:hover,
    .tiempo-contador-mini .text-cyan-500:hover,
    .tiempo-contador-mini .text-purple-500:hover {
        opacity: 1;
        transform: scale(1.1);
        transition: all 0.3s ease;
    }

    /* Responsive para móvil - versión mini */
    @media (max-width: 640px) {
        .tiempo-limite-container-mini {
            padding: 0.75rem !important;
        }
        
        .tiempo-limite-container-mini .flex {
            gap: 0.5rem;
        }
        
        .tiempo-contador-mini {
            font-size: 1.1rem !important;
        }
        
        .tiempo-limite-container-mini .hidden {
            display: none !important;
        }
    }

    /* Hover effects para hacer más interactivo */
    .tiempo-limite-container-mini:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Estilo especial para la fecha compacta */
    .tiempo-limite-container-mini .bg-gray-100 {
        background: rgba(243, 244, 246, 0.8) !important;
        backdrop-filter: blur(5px);
        transition: all 0.2s ease;
    }

    .tiempo-limite-container-mini .bg-gray-100:hover {
        background: rgba(229, 231, 235, 0.9) !important;
    }

    /* ⚡ Mejoras de accesibilidad para usuarios con preferencias de movimiento reducido */
    @media (prefers-reduced-motion: reduce) {
        .animate-pulse,
        .animate-spin {
            animation: none !important;
        }
        
        [x-transition] {
            transition: none !important;
        }
    }

    /* 🎡 ESTILOS PARA CONTROLES DE SCROLL BIDIRECCIONALES */
    #scroll-controls {
        will-change: transform, opacity;
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.08));
    }

    /* Animación de entrada suave para los controles */
    @keyframes controls-fade-in {
        from {
            opacity: 0;
            transform: translate(-50%, 15px) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0) scale(1);
        }
    }

    #scroll-controls.opacity-100 {
        animation: controls-fade-in 0.5s ease-out;
    }

    /* Animación de salida suave */
    @keyframes controls-fade-out {
        from {
            opacity: 1;
            transform: translate(-50%, 0) scale(1);
        }
        to {
            opacity: 0;
            transform: translate(-50%, 15px) scale(0.8);
        }
    }

    #scroll-controls.opacity-0 {
        animation: controls-fade-out 0.3s ease-in;
    }

    /* Efectos de hover mejorados para botones de scroll */
    #scroll-controls .group:hover > div:first-child {
        transform: translateY(-1px);
        box-shadow: 
            0 6px 20px rgba(0, 0, 0, 0.15),
            0 0 0 1px rgba(255, 255, 255, 0.9) inset;
    }

    /* Animaciones específicas para cada dirección */
    @keyframes arrow-bounce-up {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-2px);
        }
    }

    @keyframes arrow-bounce-down {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(2px);
        }
    }

    #scroll-controls .group:hover svg[d*="5 15l7-7 7 7"] {
        animation: arrow-bounce-up 0.8s ease-in-out infinite;
    }

    #scroll-controls .group:hover svg[d*="19 9l-7 7-7-7"] {
        animation: arrow-bounce-down 0.8s ease-in-out infinite;
    }

    /* Pulso suave para indicadores centrales */
    @keyframes indicator-pulse {
        0%, 100% {
            opacity: 0.6;
            transform: scale(1);
        }
        50% {
            opacity: 1;
            transform: scale(1.2);
        }
    }

    #scroll-controls .bg-blue-400 {
        animation: indicator-pulse 2s ease-in-out infinite;
    }

    /* Estados de scrolling activo */
    #scroll-controls.scrolling .group > div:first-child {
        transform: scale(0.95);
        opacity: 0.7;
        pointer-events: none;
    }

    #scroll-controls.scrolling svg {
        animation: spin 1s linear infinite;
    }

    /* Efectos responsivos para móvil */
    @media (max-width: 640px) {
        #scroll-controls {
            bottom: 2rem;
            transform: translate(-50%, 0) scale(0.95);
        }
        
        #scroll-controls .w-8 {
            width: 1.75rem;
            height: 1.75rem;
        }
        
        #scroll-controls svg {
            width: 0.65rem;
            height: 0.65rem;
        }
        
        #scroll-controls .space-y-2 {
            gap: 0.25rem;
        }
        
        #scroll-controls .text-sm {
            font-size: 0.75rem;
        }
        
        #scroll-controls .px-3 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        #scroll-controls .py-2 {
            padding-top: 0.375rem;
            padding-bottom: 0.375rem;
        }
        
        #scroll-controls .space-x-2 > * + * {
            margin-left: 0.375rem;
        }
    }

    /* Accesibilidad mejorada */
    #scroll-controls .group:focus-within > div:first-child {
        outline: none;
        box-shadow: 
            0 6px 20px rgba(0, 0, 0, 0.15),
            0 0 0 3px rgba(59, 130, 246, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.9) inset;
    }

    /* Gradientes específicos para cada dirección */
    #scroll-controls .bg-gradient-to-t {
        background: linear-gradient(to top, 
            rgba(249, 250, 251, 0.9), 
            rgba(243, 244, 246, 0.8));
    }

    #scroll-controls .bg-gradient-to-b {
        background: linear-gradient(to bottom, 
            rgba(249, 250, 251, 0.8), 
            rgba(243, 244, 246, 0.9));
    }

    /* Hover states para gradientes */
    #scroll-controls .group:hover .bg-gradient-to-t {
        background: linear-gradient(to top, 
            rgba(249, 250, 251, 1), 
            rgba(239, 241, 245, 1));
    }

    #scroll-controls .group:hover .bg-gradient-to-b {
        background: linear-gradient(to bottom, 
            rgba(249, 250, 251, 1), 
            rgba(239, 241, 245, 1));
    }

    /* Sombras mejoradas */
    #scroll-controls .shadow-md {
        box-shadow: 
            0 4px 6px -1px rgba(0, 0, 0, 0.1), 
            0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    #scroll-controls .hover\\:shadow-lg:hover {
        box-shadow: 
            0 10px 15px -3px rgba(0, 0, 0, 0.1), 
            0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Animación especial de primera aparición */
    @keyframes controls-first-appearance {
        0% {
            opacity: 0;
            transform: translate(-50%, 25px) scale(0.2);
        }
        50% {
            transform: translate(-50%, -5px) scale(1.15);
        }
        100% {
            opacity: 1;
            transform: translate(-50%, 0) scale(1);
        }
    }

    #scroll-controls[data-first-show="true"] {
        animation: controls-first-appearance 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* Efectos de transición para botones que aparecen/desaparecen */
    #scroll-controls [x-transition] {
        transition-property: opacity, transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Estilos para los botones compactos */
    #scroll-controls .text-sm {
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        letter-spacing: 0.01em;
        font-weight: 500;
    }

    /* Efectos especiales para los botones */
    #scroll-controls .rounded-full {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    /* Animaciones para hover en contenedor completo */
    #scroll-controls .group:hover {
        transform: scale(1.02);
        background: rgba(255, 255, 255, 0.95);
    }

    /* Efectos de entrada suave */
    @keyframes button-fade-in {
        from {
            opacity: 0;
            transform: translateY(8px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    #scroll-controls [x-show] {
        animation: button-fade-in 0.4s ease-out;
    }

    /* Efecto de pulso sutil en hover */
    #scroll-controls .group:hover .w-8 {
        animation: gentle-pulse 1.5s ease-in-out infinite;
    }

    @keyframes gentle-pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    /* Sombras mejoradas */
    #scroll-controls .shadow-md {
        box-shadow: 
            0 4px 6px -1px rgba(0, 0, 0, 0.1), 
            0 2px 4px -1px rgba(0, 0, 0, 0.06),
            0 0 0 1px rgba(255, 255, 255, 0.05) inset;
    }

    #scroll-controls .hover\\:shadow-lg:hover {
        box-shadow: 
            0 10px 15px -3px rgba(0, 0, 0, 0.1), 
            0 4px 6px -2px rgba(0, 0, 0, 0.05),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    }

    /* Efecto de gradiente en hover para el separador */
    #scroll-controls .via-gray-300 {
        transition: all 0.3s ease;
    }

    #scroll-controls:hover .via-gray-300 {
        background: linear-gradient(to right, transparent, rgb(59 130 246 / 0.3), transparent);
    }



    /* 📋 ESTILOS SIMPLES PARA TÉRMINOS Y CONDICIONES */
    
    /* Transiciones suaves */
    .terminos-card {
        transition: all 0.3s ease;
    }
    
    .terminos-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    /* Efectos para el checkbox */
    .terminos-checkbox:checked {
        animation: checkboxBounce 0.3s ease;
    }
    
    @keyframes checkboxBounce {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    /* Botón de continuar */
    .btn-terminos-continuar {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .btn-terminos-continuar:hover {
        transform: translateY(-1px) scale(1.05);
        box-shadow: 0 8px 20px rgba(157, 36, 73, 0.3);
    }
    
    .btn-terminos-continuar::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-terminos-continuar:hover::before {
        left: 100%;
    }
    
    /* Iconos animados */
    .terminos-icon {
        transition: all 0.3s ease;
    }
    
    .terminos-icon:hover {
        transform: scale(1.1);
    }
    
    /* Efectos para las secciones de términos */
    .terminos-section {
        transition: all 0.3s ease;
    }
    
    .terminos-section:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }
    
    /* Animaciones de entrada */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .terminos-fade-in {
        animation: fadeInUp 0.6s ease-out;
    }
    
    /* Estados de focus */
    .terminos-checkbox:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(157, 36, 73, 0.2);
    }
    
    .btn-terminos-continuar:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(157, 36, 73, 0.2);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .terminos-card {
            margin-bottom: 1rem;
        }
        
        .btn-terminos-continuar {
            width: 100%;
            justify-content: center;
        }
    }
    
    /* Accesibilidad */
    @media (prefers-reduced-motion: reduce) {
        .terminos-card,
        .terminos-icon,
        .btn-terminos-continuar,
        .terminos-fade-in {
            animation: none;
            transition: none;
        }
    }


</style>
@endpush

@push('scripts')
<script>

    // 🔽 FUNCIONALIDAD AVANZADA DE DESPLAZAMIENTO AUTOMÁTICO
    document.addEventListener('DOMContentLoaded', function() {
        
        // Configuración de desplazamiento inteligente
        const scrollConfig = {
            speed: 800,           // Duración en ms
            offset: 100,          // Offset del scroll
            easing: 'easeInOutQuart' // Tipo de easing
        };
        
        // Función de easing personalizada
        function easeInOutQuart(t) {
            return t < 0.5 ? 8 * t * t * t * t : 1 - 8 * (--t) * t * t * t;
        }
        
        // Función de scroll suave personalizada
        function smoothScrollTo(targetPosition, duration = scrollConfig.speed) {
            const startPosition = window.pageYOffset;
            const distance = targetPosition - startPosition;
            let startTime = null;
            
            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);
                
                const ease = easeInOutQuart(progress);
                window.scrollTo(0, startPosition + (distance * ease));
                
                if (progress < 1) {
                    requestAnimationFrame(animation);
                } else {
                    // Callback cuando termina el scroll
                    const controls = document.getElementById('scroll-controls');
                    if (controls) {
                        controls.classList.remove('scrolling');
                        // Recheck visibility
                        if (window.Alpine) {
                            const component = Alpine.$data(controls);
                            if (component && component.checkVisibility) {
                                component.checkVisibility();
                            }
                        }
                    }
                }
            }
            
            requestAnimationFrame(animation);
        }
        
        // Función para detectar secciones visibles
        function findNextSection() {
            const sections = document.querySelectorAll('[x-show*="currentStep"], .form-section, .max-w-6xl > div');
            const currentScroll = window.pageYOffset;
            const windowHeight = window.innerHeight;
            
            for (let section of sections) {
                const rect = section.getBoundingClientRect();
                const sectionTop = rect.top + currentScroll;
                
                // Si la sección está más abajo que la posición actual + un offset
                if (sectionTop > currentScroll + 50) {
                    return Math.min(sectionTop - scrollConfig.offset, 
                                  document.documentElement.scrollHeight - windowHeight);
                }
            }
            
            // Si no hay más secciones, ir al final del documento
            return document.documentElement.scrollHeight - windowHeight;
        }
        
                 // Función global para desplazamiento inteligente con controles
        window.smartScrollDown = function() {
            const controls = document.getElementById('scroll-controls');
            if (!controls) return;
            
            // Marcar como scrolling
            controls.classList.add('scrolling');
            
            // Encontrar la siguiente sección o calcular scroll automático
            let targetPosition = findNextSection();
            
            // Si estamos cerca del final, ir al final completo
            const currentScroll = window.pageYOffset;
            const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
            
            if (currentScroll > maxScroll - 200) {
                targetPosition = maxScroll;
            }
            
            // Realizar scroll suave
            smoothScrollTo(targetPosition);
        };
        
        // Función para auto-hide de los controles
        function autoHideControls() {
            const controls = document.getElementById('scroll-controls');
            if (!controls) return;
            
            const scrollPercent = (window.pageYOffset / 
                (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            
            // Trigger Alpine component update
            if (window.Alpine) {
                const component = Alpine.$data(controls);
                if (component && component.checkVisibility) {
                    component.checkVisibility();
                }
            }
        }
        
        // Función especial para la primera aparición
        function showControlsFirstTime() {
            const controls = document.getElementById('scroll-controls');
            if (!controls) return;
            
            // Marcar para animación especial
            controls.setAttribute('data-first-show', 'true');
            
            // Trigger Alpine component
            if (window.Alpine) {
                const component = Alpine.$data(controls);
                if (component && component.checkVisibility) {
                    component.autoShow = true;
                    component.checkVisibility();
                }
            }
            
            // Remover el atributo después de la animación
            setTimeout(() => {
                controls.removeAttribute('data-first-show');
            }, 1000);
        }
        
                 // Event listeners mejorados para los controles
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(autoHideControls, 50);
        }, { passive: true });
        
        // Detectar cambios en el contenido del formulario
        const observer = new MutationObserver(function(mutations) {
            let shouldUpdate = false;
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || 
                    (mutation.type === 'attributes' && mutation.attributeName === 'x-show')) {
                    shouldUpdate = true;
                }
            });
            
            if (shouldUpdate) {
                setTimeout(() => {
                    autoHideControls();
                    // Trigger Alpine component update if exists
                    const controls = document.getElementById('scroll-controls');
                    if (controls && window.Alpine) {
                        const component = Alpine.$data(controls);
                        if (component && component.checkVisibility) {
                            component.checkVisibility();
                        }
                    }
                }, 100);
            }
        });
        
        // Observar cambios en el contenedor principal
        const formContainer = document.querySelector('[x-data*="currentStep"]');
        if (formContainer) {
            observer.observe(formContainer, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['x-show', 'class', 'style']
            });
        }
        
                 // Inicialización con animación especial
        setTimeout(() => {
            autoHideControls();
            // Mostrar controles con animación especial después de cargar
            setTimeout(showControlsFirstTime, 1000);
        }, 500);


    });
    
    // Función de compatibilidad con Alpine
    document.addEventListener('alpine:init', () => {
        // El componente de Alpine ya maneja la lógica básica
        // Esta es una mejora adicional
    });
</script>
<script>
    // ✅ FUNCIÓN GLOBAL DE NAVEGACIÓN PARA FORMULARIOS
    window.navegarSiguiente = function() {
        // Ejecutando window.navegarSiguiente() en create.blade.php
        
        // Buscar el contenedor principal de Alpine.js
        const alpineContainer = document.querySelector('[x-data*="currentStep"]');
        
        if (alpineContainer) {
            try {
                // Intentar acceder al componente Alpine y aumentar currentStep
                if (typeof Alpine !== 'undefined') {
                    const alpineData = Alpine.$data(alpineContainer);
                    
                    if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                        if (alpineData.currentStep < alpineData.totalSteps) {
                            alpineData.currentStep++;
                            return;
                        } else {
                            return;
                        }
                    }
                }
                
                // Fallback: usar event dispatch para comunicarse con Alpine
                alpineContainer.dispatchEvent(new CustomEvent('next-step'));
                return;
                
            } catch (error) {
                // Error en navegarSiguiente
            }
        }
        
        // Último fallback: buscar cualquier elemento con x-data
        const anyAlpineEl = document.querySelector('[x-data]');
        if (anyAlpineEl) {
            anyAlpineEl.dispatchEvent(new CustomEvent('next-step'));
        }
    };

    // ✅ FUNCIÓN GLOBAL DE NAVEGACIÓN ANTERIOR
    window.navegarAnterior = function() {
        const alpineContainer = document.querySelector('[x-data*="currentStep"]');
        
        if (alpineContainer) {
            try {
                if (typeof Alpine !== 'undefined') {
                    const alpineData = Alpine.$data(alpineContainer);
                    if (alpineData && typeof alpineData.currentStep !== 'undefined') {
                        if (alpineData.currentStep > 1) {
                            alpineData.currentStep--;
                        }
                        return;
                    }
                }
                
                // Fallback con evento
                alpineContainer.dispatchEvent(new CustomEvent('prev-step'));
                
            } catch (error) {
                // Error en navegarAnterior
            }
        }
    };

    // Función de prueba de navegación (desarrollo)
    window.testNavegacion = function() {
        // Probar navegación
        if (typeof window.navegarSiguiente === 'function') {
            window.navegarSiguiente();
        }
    };

    function finalizarTramite() {
        // Crear modal de confirmación personalizado
        mostrarModalConfirmacion();
    }

    function mostrarModalConfirmacion() {
        // Crear el modal
        const modal = document.createElement('div');
        modal.id = 'confirmModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                <div class="p-8 text-center">
                    <!-- Icono -->
                    <div class="mx-auto mb-6 w-20 h-20 bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    
                    <!-- Título -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        ¿Finalizar Trámite?
                    </h3>
                    
                    <!-- Descripción -->
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Su trámite será enviado para revisión por nuestro equipo especializado. 
                        Una vez enviado, no podrá realizar cambios hasta que sea revisado.
                    </p>
                    
                    <!-- Botones -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button onclick="cerrarModal()" 
                                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all duration-300 font-medium">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button onclick="confirmarEnvio()" 
                                class="px-6 py-3 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium transform hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>Enviar Trámite
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Animar entrada
        setTimeout(() => {
            const content = document.getElementById('modalContent');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function cerrarModal() {
        const modal = document.getElementById('confirmModal');
        const content = document.getElementById('modalContent');
        
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
        
        setTimeout(() => {
            if (modal) {
                document.body.removeChild(modal);
            }
        }, 300);
    }

    function confirmarEnvio() {
        // Cerrar modal de confirmación
        cerrarModal();
        
        // Mostrar modal de envío
        setTimeout(() => {
            mostrarModalEnvio();
        }, 300);
        
        // Enviar el trámite
        enviarTramite();
    }

    function mostrarModalEnvio() {
        const overlay = document.createElement('div');
        overlay.id = 'sendingOverlay';
        overlay.className = 'fixed inset-0 z-50';
        overlay.innerHTML = `
            <!-- Fondo animado con gradiente -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449] via-[#8a203f] to-[#7a1d37]">
                <!-- Partículas flotantes -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white bg-opacity-10 rounded-full blur-xl animate-pulse"></div>
                    <div class="absolute top-3/4 right-1/4 w-24 h-24 bg-white bg-opacity-5 rounded-full blur-lg animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-1/4 left-1/3 w-40 h-40 bg-white bg-opacity-5 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s"></div>
                </div>
                
                <!-- Patrón geométrico sutil -->
                <div class="absolute inset-0 opacity-10">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid)" />
                    </svg>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="relative z-10 flex items-center justify-center min-h-screen p-6">
                <div class="text-center max-w-lg mx-auto">
                    
                    <!-- Logo/Imagen principal con efectos -->
                    <div class="relative mb-12">
                        <div class="relative">
                            <!-- Anillos concéntricos animados -->
                            <div class="absolute inset-0 rounded-full border-4 border-white border-opacity-20 animate-ping"></div>
                            <div class="absolute inset-4 rounded-full border-2 border-white border-opacity-30 animate-ping" style="animation-delay: 0.5s"></div>
                            <div class="absolute inset-8 rounded-full border border-white border-opacity-40 animate-ping" style="animation-delay: 1s"></div>
                            
                            <!-- Imagen principal -->
                            <div class="relative w-48 h-48 mx-auto bg-white bg-opacity-20 backdrop-blur-sm rounded-3xl shadow-2xl p-6 animate-float">
                                <img src="{{ asset('images/exito-elias.png') }}" 
                                     alt="Procesando" 
                                     class="w-full h-full object-contain rounded-2xl">
                                
                                <!-- Indicador de carga giratorio -->
                                <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-white rounded-full shadow-xl flex items-center justify-center animate-spin">
                                    <div class="w-8 h-8 border-4 border-[#9d2449] border-t-transparent rounded-full animate-spin"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Título principal -->
                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 animate-fade-in-up tracking-tight">
                        Procesando...
                    </h1>
                    
                    <!-- Subtítulo -->
                    <p class="text-xl md:text-2xl text-white text-opacity-90 mb-8 animate-fade-in-up font-light leading-relaxed" style="animation-delay: 0.3s">
                        Enviando su trámite para revisión
                    </p>
                    
                    <!-- Barra de progreso elegante -->
                    <div class="mb-12 animate-fade-in-up" style="animation-delay: 0.6s">
                        <div class="relative w-full max-w-md mx-auto">
                            <!-- Fondo de la barra -->
                            <div class="h-2 bg-white bg-opacity-20 rounded-full shadow-inner backdrop-blur-sm">
                                <!-- Barra de progreso con gradiente -->
                                <div class="h-full bg-gradient-to-r from-white via-blue-100 to-white rounded-full shadow-lg animate-progress-wave relative overflow-hidden">
                                    <!-- Efecto de brillo que se mueve -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white via-transparent opacity-60 animate-shimmer-wave"></div>
                                </div>
                            </div>
                            
                            <!-- Porcentaje -->
                            <div class="text-center mt-4">
                                <span class="text-white text-opacity-80 font-medium">Completando proceso...</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicadores de estado -->
                    <div class="flex justify-center items-center space-x-8 mb-8 animate-fade-in-up" style="animation-delay: 0.9s">
                        <!-- Paso 1 -->
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 animate-pulse">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="text-white text-opacity-70 text-sm">Validando</span>
                        </div>
                        
                        <!-- Paso 2 -->
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 animate-pulse" style="animation-delay: 0.3s">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <span class="text-white text-opacity-70 text-sm">Enviando</span>
                        </div>
                        
                        <!-- Paso 3 -->
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 animate-pulse" style="animation-delay: 0.6s">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-white text-opacity-70 text-sm">Confirmando</span>
                        </div>
                    </div>
                    
                    <!-- Mensaje de espera -->
                    <div class="text-center animate-fade-in-up" style="animation-delay: 1.2s">
                        <p class="text-white text-opacity-80 text-lg mb-2">Por favor, no cierre esta ventana</p>
                        <p class="text-white text-opacity-60">Este proceso puede tomar unos segundos</p>
                    </div>
                    
                    <!-- Puntos de carga decorativos -->
                    <div class="flex justify-center space-x-3 mt-8 animate-fade-in-up" style="animation-delay: 1.5s">
                        <div class="w-3 h-3 bg-white bg-opacity-60 rounded-full animate-bounce"></div>
                        <div class="w-3 h-3 bg-white bg-opacity-60 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-3 h-3 bg-white bg-opacity-60 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-3 h-3 bg-white bg-opacity-60 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
    }

    function mostrarModalExito(mensaje, redirectUrl) {
        // Remover overlay de envío
        const sendingOverlay = document.getElementById('sendingOverlay');
        if (sendingOverlay) {
            document.body.removeChild(sendingOverlay);
        }
        
        const overlay = document.createElement('div');
        overlay.id = 'successOverlay';
        overlay.className = 'fixed inset-0 z-50';
        overlay.innerHTML = `
            <!-- Fondo celebratorio con gradiente -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 via-green-600 to-teal-700">
                <!-- Confeti animado -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute top-10 left-10 w-4 h-4 bg-yellow-300 rounded-full animate-bounce opacity-80"></div>
                    <div class="absolute top-20 right-20 w-3 h-3 bg-pink-400 rounded-full animate-bounce opacity-70" style="animation-delay: 0.2s"></div>
                    <div class="absolute top-1/3 left-1/4 w-5 h-5 bg-blue-400 rounded-full animate-bounce opacity-60" style="animation-delay: 0.4s"></div>
                    <div class="absolute bottom-1/4 right-1/3 w-4 h-4 bg-purple-400 rounded-full animate-bounce opacity-75" style="animation-delay: 0.6s"></div>
                    <div class="absolute bottom-20 left-20 w-3 h-3 bg-orange-400 rounded-full animate-bounce opacity-80" style="animation-delay: 0.8s"></div>
                    <div class="absolute top-1/2 right-10 w-6 h-6 bg-red-400 rounded-full animate-bounce opacity-70" style="animation-delay: 1s"></div>
                </div>
                
                <!-- Patrón de celebración -->
                <div class="absolute inset-0 opacity-10">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="celebration" width="60" height="60" patternUnits="userSpaceOnUse">
                                <circle cx="30" cy="30" r="2" fill="white" opacity="0.3"/>
                                <circle cx="10" cy="10" r="1" fill="white" opacity="0.2"/>
                                <circle cx="50" cy="50" r="1.5" fill="white" opacity="0.4"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#celebration)" />
                    </svg>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="relative z-10 flex items-center justify-center min-h-screen p-6">
                <div class="text-center max-w-2xl mx-auto">
                    
                    <!-- Imagen de éxito con efectos espectaculares -->
                    <div class="relative mb-12">
                        <div class="relative">
                            <!-- Anillos de celebración -->
                            <div class="absolute inset-0 rounded-full border-4 border-white border-opacity-30 animate-ping"></div>
                            <div class="absolute inset-8 rounded-full border-2 border-yellow-300 border-opacity-50 animate-ping" style="animation-delay: 0.3s"></div>
                            <div class="absolute inset-16 rounded-full border border-white border-opacity-40 animate-ping" style="animation-delay: 0.6s"></div>
                            
                            <!-- Imagen principal con marco elegante -->
                            <div class="relative w-56 h-56 mx-auto bg-white bg-opacity-20 backdrop-blur-sm rounded-full shadow-2xl p-8 animate-celebration-float">
                                <img src="{{ asset('images/exito-elias.png') }}" 
                                     alt="¡Éxito!" 
                                     class="w-full h-full object-contain rounded-full">
                                
                                <!-- Badge de éxito grande -->
                                <div class="absolute -top-6 -right-6 w-20 h-20 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full flex items-center justify-center shadow-2xl animate-pulse">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                
                                <!-- Estrellas decorativas -->
                                <div class="absolute -top-8 -left-8 text-yellow-300 text-3xl animate-twinkle">✨</div>
                                <div class="absolute -bottom-8 -right-8 text-yellow-300 text-2xl animate-twinkle" style="animation-delay: 0.5s">⭐</div>
                                <div class="absolute -bottom-4 -left-12 text-yellow-300 text-xl animate-twinkle" style="animation-delay: 1s">🌟</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Título de celebración -->
                    <h1 class="text-6xl md:text-7xl font-bold text-white mb-6 animate-bounce-in tracking-tight">
                        ¡Éxito!
                    </h1>
                    
                    <!-- Mensaje principal -->
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-6 animate-fade-in-up" style="animation-delay: 0.3s">
                        ¡Trámite Enviado!
                    </h2>
                    
                    <!-- Descripción -->
                    <p class="text-xl md:text-2xl text-white text-opacity-90 mb-8 animate-fade-in-up font-light leading-relaxed" style="animation-delay: 0.6s">
                        ${mensaje}
                    </p>
                    
                    <!-- Tarjeta informativa elegante -->
                    <div class="bg-white bg-opacity-20 backdrop-blur-md rounded-2xl p-6 mb-8 shadow-xl animate-fade-in-up" style="animation-delay: 0.9s">
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-12 h-12 bg-blue-500 bg-opacity-80 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <h3 class="text-white font-semibold text-lg">¿Qué sigue?</h3>
                                <p class="text-white text-opacity-80">Será redirigido al estado de su trámite</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contador regresivo elegante -->
                    <div class="text-center animate-fade-in-up" style="animation-delay: 1.2s">
                        <div class="inline-flex items-center bg-white bg-opacity-20 backdrop-blur-md rounded-full px-6 py-3 shadow-lg">
                            <div class="w-8 h-8 bg-white bg-opacity-30 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-white font-medium text-lg">
                                Redirigiendo en <span id="countdown" class="font-bold text-yellow-300">3</span> segundos
                            </span>
                        </div>
                    </div>
                    
                    <!-- Elementos decorativos flotantes -->
                    <div class="absolute top-20 left-10 text-4xl animate-float" style="animation-delay: 0.5s">🎉</div>
                    <div class="absolute bottom-20 right-10 text-3xl animate-float" style="animation-delay: 1s">🎊</div>
                    <div class="absolute top-1/2 left-5 text-2xl animate-float" style="animation-delay: 1.5s">🎈</div>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Contador regresivo
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        const interval = setInterval(() => {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = redirectUrl;
            }
        }, 1000);
    }

    function mostrarModalError(mensaje) {
        // Remover overlay de envío si existe
        const sendingOverlay = document.getElementById('sendingOverlay');
        if (sendingOverlay) {
            document.body.removeChild(sendingOverlay);
        }
        
        const modal = document.createElement('div');
        modal.id = 'errorModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
                <div class="p-8 text-center">
                    <!-- Icono de error -->
                    <div class="mx-auto mb-6 w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    
                    <!-- Título -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Error al Enviar
                    </h3>
                    
                    <!-- Mensaje -->
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        ${mensaje}
                    </p>
                    
                    <!-- Botón -->
                    <button onclick="cerrarModalError()" 
                            class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                        <i class="fas fa-times mr-2"></i>Cerrar
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
    }

    function cerrarModalError() {
        const modal = document.getElementById('errorModal');
        if (modal) {
            document.body.removeChild(modal);
        }
    }

    function enviarTramite() {
        fetch('/tramites-solicitante/finalizar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar modal de éxito
                const redirectUrl = data.redirect || '/tramites-solicitante';
                mostrarModalExito(data.message, redirectUrl);
            } else {
                mostrarModalError('Error al finalizar el trámite: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarModalError('Error de conexión al finalizar el trámite');
        });
    }
</script>
@endpush
@endsection

