@extends('layouts.app')

@section('title', 'Estado del Trámite')

@push('styles')
    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                max-height: 1000px;
                transform: translateY(0);
            }
        }

        .animate-slideDown {
            animation: slideDown 0.3s ease-out forwards;
        }

        /* Mejora del cursor para elementos clickeables */
        .cursor-pointer:hover {
            transform: translateY(-1px);
            transition: transform 0.2s ease;
        }

        /* Efecto hover para los documentos individuales */
        .documento-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Smooth transition para chevron */
        #chevron-3,
        #chevron-6 {
            transition: transform 0.3s ease;
        }

        /* Estilos para notificaciones */
        .notificacion-estado {
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
        }

        /* Animación de pulso para barra de progreso */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .progress-pulse {
            animation: pulse 1.5s infinite;
        }

        /* Mejoras para botones de subida */
        .btn-upload:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-upload:active {
            transform: translateY(0);
        }

        /* Efecto de escala para documentos actualizados */
        .documento-updated {
            animation: scaleUpdate 0.6s ease-out;
        }

        @keyframes scaleUpdate {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Estilos para el indicador de actualización */
        #indicador-actualizacion {
            backdrop-filter: blur(10px);
            transform: translateX(-100%);
            animation: slideInLeft 0.3s ease-out forwards;
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Animación para elementos que se actualizan */
        .elemento-actualizado {
            animation: highlight 0.8s ease-out;
        }

        @keyframes highlight {
            0% {
                background-color: rgba(59, 130, 246, 0.1);
            }

            50% {
                background-color: rgba(59, 130, 246, 0.2);
            }

            100% {
                background-color: transparent;
            }
        }

        /* ======= NUEVOS ESTILOS MEJORADOS PARA CORRECCIONES ======= */

        /* Animación de shake para elementos que necesitan atención */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-3px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(3px);
            }
        }

        .shake-on-hover:hover {
            animation: shake 0.5s ease-in-out;
        }

        /* Gradiente animado para elementos destacados */
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .gradient-animated {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientShift 3s ease infinite;
        }

        /* Efecto de resplandor para elementos importantes */
        .glow-red {
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
            animation: glowPulse 2s ease-in-out infinite alternate;
        }

        @keyframes glowPulse {
            from {
                box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
            }

            to {
                box-shadow: 0 0 35px rgba(239, 68, 68, 0.6);
            }
        }

        .glow-orange {
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.3);
            animation: glowPulse 2s ease-in-out infinite alternate;
        }

        /* Bordes animados */
        .border-animated {
            position: relative;
            overflow: hidden;
        }

        .border-animated::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(239, 68, 68, 0.4), transparent);
            animation: borderMove 2s linear infinite;
        }

        @keyframes borderMove {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        /* Efecto de partículas flotantes */
        .particles-bg {
            position: relative;
            overflow: hidden;
        }

        .particles-bg::before {
            content: '✨';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            animation: floatParticles 4s ease-in-out infinite;
            font-size: 20px;
            opacity: 0.6;
        }

        @keyframes floatParticles {

            0%,
            100% {
                transform: translateX(-50%) translateY(0px);
            }

            50% {
                transform: translateX(-50%) translateY(-10px);
            }
        }

        /* Efectos de typing para texto */
        .typing-effect {
            overflow: hidden;
            white-space: nowrap;
            animation: typing 3s steps(40, end), blink-cursor 0.75s step-end infinite;
        }

        @keyframes typing {
            from {
                width: 0;
            }

            to {
                width: 100%;
            }
        }

        @keyframes blink-cursor {

            from,
            to {
                border-color: transparent;
            }

            50% {
                border-color: #ef4444;
            }
        }

        /* Efecto de zoom suave */
        .zoom-hover:hover {
            transform: scale(1.02);
            transition: transform 0.3s ease;
        }

        /* Gradiente de fondo para secciones importantes */
        .bg-correction-gradient {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 50%, #fecaca 100%);
            position: relative;
        }

        .bg-correction-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(239, 68, 68, 0.05) 50%, transparent 100%);
            animation: gradientMove 3s ease-in-out infinite;
        }

        @keyframes gradientMove {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Indicador de urgencia */
        .urgency-indicator {
            position: relative;
        }

        .urgency-indicator::after {
            content: '⚠️';
            position: absolute;
            top: -5px;
            right: -5px;
            animation: bounce 1s infinite;
            font-size: 16px;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        /* Efecto de ondas para botones importantes */
        .wave-effect {
            position: relative;
            overflow: hidden;
        }

        .wave-effect::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .wave-effect:hover::before {
            width: 300px;
            height: 300px;
        }

        /* Mejoras para comentarios del revisor */
        .comentario-revisor {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 2px solid transparent;
            background-clip: padding-box;
            position: relative;
        }

        .comentario-revisor::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #ef4444, #f97316, #ef4444);
            border-radius: inherit;
            padding: 2px;
            z-index: -1;
            background-clip: content-box;
        }

        /* Efecto de máquina de escribir para mensajes importantes */
        .typewriter {
            overflow: hidden;
            white-space: nowrap;
            margin: 0 auto;
            animation: typing 2s steps(40, end), blink-caret 0.75s step-end infinite;
            border-right: 3px solid #ef4444;
        }

        @keyframes blink-caret {

            from,
            to {
                border-color: transparent;
            }

            50% {
                border-color: #ef4444;
            }
        }

        /* Efecto de neón para elementos destacados */
        .neon-glow {
            text-shadow: 0 0 5px #ef4444, 0 0 10px #ef4444, 0 0 15px #ef4444;
            animation: neonFlicker 2s ease-in-out infinite alternate;
        }

        @keyframes neonFlicker {

            0%,
            100% {
                text-shadow: 0 0 5px #ef4444, 0 0 10px #ef4444, 0 0 15px #ef4444;
            }

            50% {
                text-shadow: 0 0 2px #ef4444, 0 0 5px #ef4444, 0 0 8px #ef4444;
            }
        }

        /* Mejoras para el hover de cards */
        .card-hover-effect:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Indicador de estado con animación */
        .status-indicator {
            position: relative;
            display: inline-block;
        }

        .status-indicator::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.3) 0%, transparent 70%);
            animation: statusPulse 2s ease-in-out infinite;
        }

        @keyframes statusPulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.5);
                opacity: 0.5;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Contador de tiempo restante */
        .countdown-container {
            position: relative;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-radius: 1rem;
            overflow: hidden;
        }

        .countdown-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(239, 68, 68, 0.1), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .countdown-ring {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .countdown-ring svg {
            transform: rotate(-90deg);
        }

        .countdown-ring circle {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
            transform: translate(40px, 40px);
        }

        .countdown-background {
            stroke: #fee2e2;
        }

        .countdown-progress {
            stroke: #ef4444;
            transition: stroke-dashoffset 1s linear;
        }

        /* Mejoras para mensajes de error */
        .error-message {
            transform: translateY(20px);
            opacity: 0;
            animation: slideUpFade 0.5s ease forwards;
        }

        @keyframes slideUpFade {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Indicador de estado mejorado */
        .status-badge {
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            line-height: 1.25rem;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .status-badge::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            padding: 2px;
            background: linear-gradient(45deg, var(--gradient-from), var(--gradient-to));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
        }

        /* Estilos para la barra de progreso */
        .progress-bar {
            position: relative;
            height: 8px;
            background: #f3f4f6;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: var(--progress);
            background: linear-gradient(90deg, var(--gradient-from), var(--gradient-to));
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        /* Tooltip mejorado */
        .custom-tooltip {
            position: relative;
            display: inline-block;
        }

        .custom-tooltip:hover .tooltip-content {
            opacity: 1;
            transform: translate(-50%, -0.25rem);
            visibility: visible;
        }

        .tooltip-content {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translate(-50%, 0.5rem);
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            font-size: 0.875rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 50;
        }

        .tooltip-content::before {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: white transparent transparent transparent;
        }

        /* Nuevos estilos para mejorar responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .header-icon {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
                gap: 0.5rem;
            }

            .header-actions button,
            .header-actions a {
                width: 100%;
            }

            .status-container {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .status-icon {
                margin-right: 0;
                margin-bottom: 1rem;
            }

            .status-info {
                text-align: center;
                margin-bottom: 1rem;
            }

            .status-progress {
                width: 100%;
                margin-top: 1rem;
            }

            .cita-header {
                flex-direction: column;
                text-align: center;
            }

            .cita-countdown {
                margin-left: 0;
                margin-top: 1rem;
                width: 100%;
            }

            .cita-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
        // Obtener todos los documentos requeridos para este tipo de persona
        $documentosRequeridos = App\Models\Documento::where(function($query) use ($tipoPersona) {
            $query->where('tipo_persona', $tipoPersona)
                  ->orWhere('tipo_persona', 'Ambas');
        })
        ->where('es_visible', true)
        ->get();
    @endphp
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="min-h-screen py-4 sm:py-8">
        <div class="max-w-4xl mx-auto px-4 space-y-6">
            <!-- Header Principal con Estado -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <!-- Header Info -->
                    <div class="flex items-center justify-between header-content mb-6 pb-6 border-b border-gray-100">
                        <div class="flex items-center space-x-5">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-full flex items-center justify-center shadow-lg header-icon">
                                <i class="fas fa-clipboard-check text-white text-xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-800 mb-2">Estado del Trámite</h1>
                                <p class="text-gray-600">{{ $tramite->tipo_tramite }} <span class="text-gray-400">•</span>
                                    <span class="font-medium text-[#9d2449]">ID: {{ $tramite->id }}</span></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 header-actions">
                            <a href="{{ route('tramites.solicitante.index') }}"
                                class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-all duration-200 border border-gray-200 hover:border-gray-300">
                                <i class="fas fa-arrow-left mr-2"></i>
                                <span class="font-medium">Volver</span>
                            </a>
                        </div>
                    </div>

                    <!-- Estado del Trámite -->
                    @if($tramite->estado === 'Cancelado')
                        <div class="flex items-center space-x-2 bg-red-50 rounded-lg px-4 py-2 border border-red-200">
                            <i class="fas fa-times-circle text-red-600"></i>
                            <div>
                                <span class="text-red-700 font-medium">Trámite cancelado:</span>
                                @php
                                    $motivo = str_replace('_', ' ', $tramite->motivo_cancelacion);
                                    $motivo = ucfirst($motivo);
                                @endphp
                                <span class="text-red-600">{{ $motivo }}</span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center space-x-2 bg-emerald-50 rounded-lg px-4 py-2 border border-emerald-200">
                            <i class="fas fa-check-circle text-emerald-600"></i>
                            <span class="text-emerald-700">Trámite activo - Estado: {{ $tramite->estado }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detalles de la Cita -->
            @if ($tramite->estado === 'Por Cotejar')
                @if(!$tramite->cita)
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="w-14 h-14 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-full flex items-center justify-center shadow-lg mr-4">
                                    <i class="fas fa-calendar-plus text-white text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h2 class="text-xl font-bold text-[#9d2449] mb-2">Asignación de Cita</h2>
                                    <p class="text-gray-600 mb-4">Su cita será asignada automáticamente para el siguiente día hábil disponible.</p>
                                    <div class="bg-[#9d2449]/5 rounded-lg p-4 border border-[#9d2449]/20">
                                        <div class="flex items-start">
                                            <i class="fas fa-info-circle text-[#9d2449] mt-0.5 mr-3"></i>
                                            <div class="text-sm">
                                                <span class="font-medium text-[#9d2449]">Información importante:</span>
                                                <ul class="mt-2 ml-4 list-disc space-y-2 text-gray-700">
                                                    <li class="flex items-center">
                                                        <i class="fas fa-clock text-[#9d2449]/70 mr-2"></i>
                                                        Horario de atención: 9:00 a 15:00 hrs
                                                    </li>
                                                    <li class="flex items-center">
                                                        <i class="fas fa-calendar-alt text-[#9d2449]/70 mr-2"></i>
                                                        No se asignan citas en fines de semana ni días inhábiles
                                                    </li>
                                                    <li class="flex items-center">
                                                        <i class="fas fa-bell text-[#9d2449]/70 mr-2"></i>
                                                        Recibirá una notificación con los detalles de su cita
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @php
                        $citaPasada = $tramite->cita->fecha_hora->isPast();
                        $noAsistio = !$tramite->cita->asistio;
                    @endphp

                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="p-6">
                            @if($citaPasada && $noAsistio)
                                <!-- Mensaje de cita vencida -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center shadow-lg mr-4">
                                            <i class="fas fa-calendar-times text-red-600 text-xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h2 class="text-xl font-bold text-red-600 mb-2">Cita Vencida</h2>
                                            <p class="text-gray-600 mb-2">La fecha programada para realizar su cotejo ya pasó</p>
                                            <div class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-clock mr-2"></i>
                                                Su cita estaba programada para el día {{ $tramite->cita->fecha_hora->format('d/m/Y') }} a las {{ $tramite->cita->fecha_hora->format('H:i') }} hrs
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <button onclick="toggleReagendarModal()"
                                            class="px-4 py-2 bg-white text-red-600 border-2 border-red-600 rounded-lg hover:bg-red-50 transition-colors duration-200 flex items-center text-sm font-medium whitespace-nowrap">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            Reagendar Cita
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- Encabezado con Estado y Contador -->
                                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between mb-6 pb-6 border-b border-gray-100">
                                    <div class="flex items-center mb-4 lg:mb-0">
                                        <div class="w-14 h-14 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-full flex items-center justify-center shadow-lg mr-4">
                                            <i class="fas fa-calendar-check text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-[#9d2449] mb-1">Cita para Cotejo Programada</h2>
                                            <p class="text-gray-600">✅ Revisión digital aprobada</p>
                                        </div>
                                    </div>
                                    <div class="bg-[#9d2449]/5 rounded-xl p-4 lg:min-w-[200px]">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-[#9d2449]" id="cita-countdown">
                                                <span id="countdown-days">0</span>d <span id="countdown-hours">00</span>h <span id="countdown-minutes">00</span>m
                                            </div>
                                            <div class="text-sm text-[#9d2449]/70">tiempo restante</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detalles de la Cita -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="space-y-6">
                                        <!-- Fecha y Hora -->
                                        <div class="flex items-start">
                                            <div class="w-10 h-10 bg-[#9d2449]/10 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-calendar-alt text-[#9d2449]"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm text-[#9d2449]">Fecha y Hora</div>
                                                <div class="font-semibold text-gray-900 text-lg mb-2">
                                                    {{ $tramite->cita->fecha_hora->format('d/m/Y') }} -
                                                    {{ $tramite->cita->fecha_hora->format('H:i') }} hrs
                                                </div>
                                                @if($tipoPersona === 'Moral')
                                                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                                        <div class="text-sm text-[#9d2449] mb-1">
                                                            <i class="fas fa-user-tie mr-2"></i>
                                                            Debe presentarse:
                                                        </div>
                                                        @if($tramite->detalleTramite && $tramite->detalleTramite->representanteLegal)
                                                            <div class="font-medium text-gray-900">
                                                                {{ $tramite->detalleTramite->representanteLegal->nombre }}
                                                            </div>
                                                            <div class="text-xs text-gray-600 mt-1">
                                                                Representante Legal Acreditado
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        <i class="fas fa-user text-[#9d2449] mr-1"></i>
                                                        Debe presentarse: <span class="font-medium">El Titular</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Ubicación -->
                                        <div class="flex items-start">
                                            <div class="w-10 h-10 bg-[#9d2449]/10 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-map-marker-alt text-[#9d2449]"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="text-sm text-[#9d2449]">Ubicación</div>
                                                <div class="font-semibold text-gray-900">Ciudad Administrativa</div>
                                                <div class="text-sm font-medium text-gray-800 mt-1">
                                                    Edificio 1, Nivel 1, Módulo de Proveedores
                                                </div>
                                                <div class="text-xs text-gray-600 mt-2">
                                                    <i class="fas fa-map-signs text-gray-400 mr-1"></i>
                                                    Internacional 8, San Miguel 2da Secc,<br>
                                                    68270 Tlalixtac de Cabrera, Oax.
                                                </div>
                                                <a href="https://maps.app.goo.gl/KSN9PvoMqHLh5JCa7" 
                                                   target="_blank" 
                                                   class="inline-flex items-center mt-3 text-sm text-[#9d2449] hover:text-[#7a1d3a] transition-colors duration-200">
                                                    <i class="fas fa-directions mr-1"></i>
                                                    Ver en Google Maps
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-[#9d2449]/5 rounded-xl p-4">
                                        <button onclick="toggleDocumentosModal()" 
                                                class="w-full flex items-center justify-between p-3 bg-white rounded-lg border border-[#9d2449]/20 hover:bg-[#9d2449]/5 transition-all duration-200 group">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-[#9d2449]/10 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-file-alt text-[#9d2449] group-hover:scale-110 transition-transform duration-200"></i>
                                                </div>
                                                <div class="text-left">
                                                    <h4 class="font-medium text-[#9d2449]">Ver documentos a presentar</h4>
                                                    <p class="text-sm text-gray-600">Lista de documentos requeridos para el cotejo</p>
                                                </div>
                                            </div>
                                            <i class="fas fa-chevron-right text-[#9d2449] group-hover:translate-x-1 transition-transform duration-200"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-gray-100">
                                    <div class="flex items-center text-[#7a1d3a]">
                                        <i class="fas fa-exclamation-circle mr-2 text-[#9d2449]"></i>
                                        <span class="text-sm font-medium">La cita es obligatoria para completar el trámite</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <!-- Botón para generar documento -->
                                        <a href="{{ route('membretes.citas.generar', $tramite->id) }}" 
                                           target="_blank"
                                           class="px-4 py-2 bg-[#9d2449] text-white rounded-lg hover:bg-[#7a1d3a] transition-colors duration-200 flex items-center text-sm font-medium">
                                            <i class="fas fa-file-download mr-2"></i>
                                            Generar Documento
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            <!-- Progreso de Secciones -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                <div class="flex items-center mb-8">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-lg flex items-center justify-center shadow-md mr-4">
                        <i class="fas fa-list-check text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#9d2449]">Progreso de Secciones</h3>
                        <p class="text-sm text-gray-600 mt-1">Estado de avance de su trámite</p>
                    </div>
                </div>

                @php
                    $secciones =
                        $tipoPersona === 'Moral'
                            ? [
                                1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user-circle'],
                                2 => ['nombre' => 'Domicilio', 'icono' => 'fa-map-marker-alt'],
                                3 => ['nombre' => 'Constitución', 'icono' => 'fa-building'],
                                4 => ['nombre' => 'Accionistas', 'icono' => 'fa-users'],
                                5 => ['nombre' => 'Apoderado Legal', 'icono' => 'fa-user-tie'],
                                6 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload'],
                            ]
                            : [
                                1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user-circle'],
                                2 => ['nombre' => 'Domicilio', 'icono' => 'fa-map-marker-alt'],
                                3 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload'],
                            ];

                    $progresoMaximo = $tipoPersona === 'Moral' ? 6 : 3;
                    $progresoMostrado = min($tramite->progreso_tramite, $progresoMaximo);
                @endphp

                <div class="space-y-5">
                    @foreach ($secciones as $numero => $seccion)
                        @php
                            $seccionRechazada = $tramite->seccionEstaRechazada($numero);
                            $seccionAprobada = $tramite->seccionEstaAprobada($numero);
                            $estadoRevision = $tramite->getEstadoSeccion($numero);

                            // Lógica especial para la sección de documentos
                            if ($seccion['nombre'] === 'Documentos') {
                                $documentosIndividuales = $tramite->documentosSolicitante()->get();
                                $totalDocs = $documentosIndividuales->count();
                                $aprobados = $documentosIndividuales->where('estado', 'Aprobado')->count();
                                $rechazados = $documentosIndividuales->where('estado', 'Rechazado')->count();
                                $enRevision = $documentosIndividuales
                                    ->whereIn('estado', ['En Revision', 'Pendiente'])
                                    ->count();

                                if ($totalDocs === 0) {
                                    // Sin documentos
                                    $bgColor = 'bg-gray-50 border-gray-200 hover:bg-gray-100';
                                    $iconColor = 'text-gray-500';
                                    $statusColor = 'text-gray-600';
                                    $statusText = 'Sin documentos';
                                    $statusIcon = 'fa-inbox';
                                    $iconBg = 'bg-gray-100';
                                } elseif ($rechazados > 0) {
                                    // Algunos documentos rechazados
                                    $bgColor = 'bg-red-50 border-red-200 hover:bg-red-100';
                                    $iconColor = 'text-red-600';
                                    $statusColor = 'text-red-700';
                                    $statusText = "$rechazados documento(s) rechazado(s)";
                                    $statusIcon = 'fa-exclamation-circle';
                                    $iconBg = 'bg-red-100';
                                    $seccionRechazada = true; // Para mostrar botón de corrección
                                } elseif ($aprobados === $totalDocs) {
                                    // Todos los documentos aprobados
                                    $bgColor = 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100';
                                    $iconColor = 'text-emerald-600';
                                    $statusColor = 'text-emerald-700';
                                    $statusText = "Todos los documentos aprobados ($aprobados/$totalDocs)";
                                    $statusIcon = 'fa-check-circle';
                                    $iconBg = 'bg-emerald-100';
                                } else {
                                    // Documentos en revisión o mixto
                                    $bgColor = 'bg-blue-50 border-blue-200 hover:bg-blue-100';
                                    $iconColor = 'text-blue-600';
                                    $statusColor = 'text-blue-700';
                                    $statusText = "En revisión ($aprobados aprobados, $enRevision pendientes)";
                                    $statusIcon = 'fa-clock';
                                    $iconBg = 'bg-blue-100';
                                }
                            } else {
                                // Lógica original para otras secciones
                                if ($seccionRechazada) {
                                    $bgColor = 'bg-red-50 border-red-200 hover:bg-red-100';
                                    $iconColor = 'text-red-600';
                                    $statusColor = 'text-red-700';
                                    $statusText = 'Requiere corrección';
                                    $statusIcon = 'fa-exclamation-circle';
                                    $iconBg = 'bg-red-100';
                                } elseif ($seccionAprobada) {
                                    $bgColor = 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100';
                                    $iconColor = 'text-emerald-600';
                                    $statusColor = 'text-emerald-700';
                                    $statusText = 'Completado';
                                    $statusIcon = 'fa-check-circle';
                                    $iconBg = 'bg-emerald-100';
                                } elseif ($progresoMostrado >= $numero) {
                                    $bgColor = 'bg-blue-50 border-blue-200 hover:bg-blue-100';
                                    $iconColor = 'text-blue-600';
                                    $statusColor = 'text-blue-700';
                                    $statusText = 'En revisión';
                                    $statusIcon = 'fa-clock';
                                    $iconBg = 'bg-blue-100';
                                } else {
                                    $bgColor = 'bg-gray-50 border-gray-200 hover:bg-gray-100';
                                    $iconColor = 'text-gray-500';
                                    $statusColor = 'text-gray-600';
                                    $statusText = 'Pendiente';
                                    $statusIcon = 'fa-circle';
                                    $iconBg = 'bg-gray-100';
                                }
                            }
                        @endphp

                        <div
                            class="rounded-xl border-2 {{ $bgColor }} transition-all duration-300 shadow-sm hover:shadow-md overflow-hidden {{ $seccionRechazada ? 'glow-red shake-on-hover border-animated urgency-indicator' : '' }}">
                            <div class="flex items-center p-5 cursor-pointer {{ $seccionRechazada ? 'bg-correction-gradient' : '' }}"
                                @if ($seccion['nombre'] === 'Documentos') onclick="toggleDocumentos({{ $numero }})" @endif>
                                <div class="flex items-center flex-1">
                                    <div
                                        class="w-14 h-14 rounded-full {{ $iconBg }} shadow-sm flex items-center justify-center mr-5 {{ $seccionRechazada ? 'status-indicator' : '' }}">
                                        <i class="fas {{ $seccion['icono'] }} {{ $iconColor }} text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4
                                            class="font-bold text-gray-900 text-lg mb-1 {{ $seccionRechazada ? 'neon-glow' : '' }}">
                                            {{ $seccion['nombre'] }}</h4>
                                        <p class="text-sm {{ $statusColor }} flex items-center font-medium">
                                            <i class="fas {{ $statusIcon }} mr-2"></i>
                                            {{ $statusText }}
                                        </p>
                                    </div>
                                </div>

                                @if ($seccion['nombre'] === 'Documentos')
                                    <div class="flex items-center mr-4">
                                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"
                                            id="chevron-{{ $numero }}"></i>
                                    </div>
                                @endif

                                @if ($seccionRechazada)
                                    <div class="flex flex-col items-end ml-4 space-y-2">
                                        <button onclick="corregirSeccion({{ $tramite->id }}, {{ $numero }})"
                                            class="group relative px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 overflow-hidden wave-effect">
                                            <!-- Efecto de brillo -->
                                            <div
                                                class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 group-hover:translate-x-full transition-all duration-700 transform -translate-x-full">
                                            </div>

                                            <div class="relative flex items-center">
                                                <i class="fas fa-tools mr-2 group-hover:animate-bounce"></i>
                                                <span>Corregir Ahora</span>
                                            </div>
                                        </button>

                                        <div
                                            class="text-xs text-red-600 font-medium bg-red-50 px-3 py-1 rounded-full border border-red-200 particles-bg">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Requiere atención
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($seccion['nombre'] === 'Documentos')
                                <!-- Panel expandible de documentos -->
                                <div id="documentos-panel-{{ $numero }}"
                                    class="hidden border-t border-gray-200 bg-white bg-opacity-50">
                                    <div class="p-6">
                                        <h5 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                            <i class="fas fa-file-check mr-2 text-blue-600"></i>
                                            Estado de Documentos Individuales
                                        </h5>

                                        @php
                                            $documentosIndividuales = $tramite
                                                ->documentosSolicitante()
                                                ->with('documento')
                                                ->get();
                                        @endphp

                                        @if ($documentosIndividuales->count() > 0)
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @foreach ($documentosIndividuales as $documento)
                                                    @php
                                                        $estadoDoc = $documento->estado ?? 'Pendiente';
                                                        if ($estadoDoc === 'Aprobado') {
                                                            $colorDoc = 'bg-emerald-50 border-emerald-200';
                                                            $iconoDoc = 'fa-check-circle text-emerald-600';
                                                            $textoDoc = 'text-emerald-700';
                                                        } elseif ($estadoDoc === 'Rechazado') {
                                                            $colorDoc = 'bg-red-50 border-red-200';
                                                            $iconoDoc = 'fa-times-circle text-red-600';
                                                            $textoDoc = 'text-red-700';
                                                        } elseif (
                                                            $estadoDoc === 'En Revision' ||
                                                            $estadoDoc === 'Pendiente'
                                                        ) {
                                                            $colorDoc = 'bg-blue-50 border-blue-200';
                                                            $iconoDoc = 'fa-clock text-blue-600';
                                                            $textoDoc = 'text-blue-700';
                                                        } else {
                                                            $colorDoc = 'bg-gray-50 border-gray-200';
                                                            $iconoDoc = 'fa-circle text-gray-600';
                                                            $textoDoc = 'text-gray-700';
                                                        }
                                                    @endphp

                                                    <div class="documento-card border-2 {{ $colorDoc }} rounded-lg p-4 transition-all duration-200 {{ $estadoDoc === 'Rechazado' ? 'glow-red card-hover-effect border-animated' : '' }}"
                                                        data-documento-id="{{ $documento->id }}"
                                                        data-estado="{{ $estadoDoc }}">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <div class="flex-1">
                                                                <h6
                                                                    class="font-semibold text-gray-900 text-sm truncate pr-2 {{ $estadoDoc === 'Rechazado' ? 'neon-glow' : '' }}">
                                                                    {{ $documento->documento->nombre ?? 'Documento sin nombre' }}
                                                                </h6>
                                                                @if ($documento->documento->descripcion)
                                                                    <p class="text-xs text-gray-600 mt-1">
                                                                        {{ $documento->documento->descripcion }}</p>
                                                                @endif
                                                            </div>
                                                            <div class="flex flex-col items-end">
                                                                <div
                                                                    class="flex items-center {{ $textoDoc }} text-xs font-medium {{ $estadoDoc === 'Rechazado' ? 'status-indicator' : '' }}">
                                                                    <i class="fas {{ $iconoDoc }} mr-1"></i>
                                                                    <span>{{ $estadoDoc === 'En Revision' ? 'En Revisión' : $estadoDoc }}</span>
                                                                </div>
                                                                @if ($documento->fecha_entrega)
                                                                    <div class="text-xs text-gray-500 mt-1">
                                                                        <i class="fas fa-calendar mr-1"></i>
                                                                        {{ $documento->fecha_entrega->format('d/m/Y') }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if ($documento->observaciones && $estadoDoc === 'Rechazado')
                                                            <div
                                                                class="mt-3 p-3 bg-red-100 rounded-lg border-l-4 border-red-400 comentario-revisor">
                                                                <div class="flex items-start">
                                                                    <i
                                                                        class="fas fa-comment-dots text-red-600 mr-2 mt-1"></i>
                                                                    <div class="flex-1">
                                                                        <div class="flex items-center mb-2">
                                                                            <p class="text-xs text-red-800 font-bold">💬
                                                                                Observaciones del Revisor:</p>
                                                                            <div class="ml-auto">
                                                                                <span
                                                                                    class="text-xs bg-red-200 text-red-800 px-2 py-1 rounded-full">
                                                                                    <i
                                                                                        class="fas fa-exclamation-circle mr-1"></i>
                                                                                    Requiere corrección
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="bg-white bg-opacity-80 rounded p-2 border border-red-200">
                                                                            <p
                                                                                class="text-sm text-red-700 font-medium leading-relaxed">
                                                                                {{ $documento->observaciones }}</p>
                                                                        </div>
                                                                        <div
                                                                            class="mt-2 flex items-center justify-between text-xs">
                                                                            <span class="text-red-600">
                                                                                <i class="fas fa-info-circle mr-1"></i>
                                                                                Por favor, revisa y corrige según las
                                                                                observaciones
                                                                            </span>
                                                                            @if ($documento->fecha_revision)
                                                                                <span class="text-gray-500">
                                                                                    <i class="fas fa-history mr-1"></i>
                                                                                    Revisado:
                                                                                    {{ $documento->fecha_revision->format('d/m/Y') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($documento->documento_cotejado)
                                                            <div
                                                                class="mt-2 flex items-center text-xs text-emerald-600 bg-emerald-50 p-2 rounded-lg">
                                                                <i class="fas fa-check-double mr-1"></i>
                                                                <span>Cotejado físicamente</span>
                                                                @if ($documento->fecha_cotejo)
                                                                    <span class="ml-auto text-emerald-500">
                                                                        <i class="fas fa-calendar-check mr-1"></i>
                                                                        {{ $documento->fecha_cotejo->format('d/m/Y') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if ($estadoDoc === 'Rechazado')
                                                            <div
                                                                class="mt-3 border-t border-red-200 pt-3 bg-correction-gradient rounded-lg">
                                                                <div class="p-2 mb-2 bg-white bg-opacity-90 rounded-lg">
                                                                    <div class="flex items-center text-sm text-red-600">
                                                                        <i
                                                                            class="fas fa-lightbulb text-amber-500 mr-2"></i>
                                                                        <span class="font-medium">Instrucciones para
                                                                            corregir:</span>
                                                                    </div>
                                                                    <ul
                                                                        class="mt-1 text-xs text-gray-600 space-y-1 ml-6 list-disc">
                                                                        <li>Asegúrate de que el nuevo documento cumpla con
                                                                            las observaciones</li>
                                                                        <li>El archivo debe estar en formato PDF</li>
                                                                        <li>Verifica que el documento sea legible y esté
                                                                            completo</li>
                                                                    </ul>
                                                                </div>

                                                                <input type="file" id="file-{{ $documento->id }}"
                                                                    accept=".pdf" class="hidden"
                                                                    onchange="handleFileChange({{ $documento->id }}, this)">
                                                                <button
                                                                    onclick="document.getElementById('file-{{ $documento->id }}').click()"
                                                                    class="btn-upload w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-sm font-bold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 wave-effect"
                                                                    id="btn-subir-{{ $documento->id }}">
                                                                    <i class="fas fa-cloud-upload-alt mr-2 text-lg"></i>
                                                                    <span>Subir Nuevo Documento</span>
                                                                    <i
                                                                        class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                                                </button>

                                                                <div id="progress-{{ $documento->id }}"
                                                                    class="hidden mt-3">
                                                                    <div class="flex items-center justify-between mb-1">
                                                                        <span class="text-xs font-medium text-red-700"
                                                                            id="progress-text-{{ $documento->id }}">
                                                                            Preparando archivo...
                                                                        </span>
                                                                        <span class="text-xs text-red-700 font-bold"
                                                                            id="progress-percentage-{{ $documento->id }}">0%</span>
                                                                    </div>
                                                                    <div class="w-full bg-red-100 rounded-full h-2.5">
                                                                        <div class="bg-gradient-to-r from-red-600 to-rose-600 h-2.5 rounded-full transition-all duration-300"
                                                                            style="width: 0%"
                                                                            id="progress-bar-{{ $documento->id }}"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Resumen estadístico -->
                                            @php
                                                $totalDocs = $documentosIndividuales->count();
                                                $aprobados = $documentosIndividuales
                                                    ->where('estado', 'Aprobado')
                                                    ->count();
                                                $rechazados = $documentosIndividuales
                                                    ->where('estado', 'Rechazado')
                                                    ->count();
                                                $enRevision = $documentosIndividuales
                                                    ->whereIn('estado', ['En Revision', 'Pendiente'])
                                                    ->count();
                                            @endphp

                                            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <h6 class="text-sm font-bold text-gray-800 mb-3">Resumen de Documentos</h6>
                                                <div class="grid grid-cols-3 gap-4 text-center">
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-emerald-600 font-bold text-lg">{{ $aprobados }}</span>
                                                        <span class="text-xs text-gray-600">Aprobados</span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-red-600 font-bold text-lg">{{ $rechazados }}</span>
                                                        <span class="text-xs text-gray-600">Rechazados</span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-blue-600 font-bold text-lg">{{ $enRevision }}</span>
                                                        <span class="text-xs text-gray-600">En Revisión</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-8">
                                                <div
                                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <i class="fas fa-inbox text-gray-400 text-xl"></i>
                                                </div>
                                                <p class="text-gray-500 font-medium">No hay documentos adjuntos</p>
                                                <p class="text-gray-400 text-sm mt-1">Los documentos aparecerán aquí cuando
                                                    sean subidos</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($seccionRechazada && $estadoRevision && $estadoRevision->comentario)
                            <div class="ml-4 mr-4 mt-3 relative">
                                <!-- Indicador visual de conexión -->
                                <div class="absolute -top-3 left-12 w-0.5 h-6 bg-red-300"></div>

                                <div
                                    class="bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border-2 border-red-200 p-6 shadow-lg relative overflow-hidden">
                                    <!-- Decoración de fondo -->
                                    <div
                                        class="absolute top-0 right-0 w-32 h-32 bg-red-100 rounded-full opacity-30 -translate-y-8 translate-x-8">
                                    </div>
                                    <div
                                        class="absolute bottom-0 left-0 w-20 h-20 bg-rose-100 rounded-full opacity-40 translate-y-4 -translate-x-4">
                                    </div>

                                    <!-- Contenido del comentario -->
                                    <div class="relative z-10">
                                        <div class="flex items-center mb-4">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-full flex items-center justify-center shadow-lg mr-4">
                                                <i class="fas fa-user-edit text-white text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-red-800 text-lg">💬 Comentario del Revisor</p>
                                                <p class="text-red-600 text-sm">¡Importante! Lee con atención</p>
                                            </div>
                                            <div class="ml-auto">
                                                <div
                                                    class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                                                    <i class="fas fa-exclamation text-white text-sm"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="bg-white bg-opacity-80 rounded-lg p-4 border-l-4 border-red-400 shadow-inner">
                                            <div class="flex items-start">
                                                <i class="fas fa-quote-left text-red-400 mr-3 mt-1 text-lg"></i>
                                                <div class="flex-1">
                                                    <p class="text-red-800 leading-relaxed font-medium text-lg">
                                                        {{ $estadoRevision->comentario }}</p>
                                                </div>
                                                <i class="fas fa-quote-right text-red-400 ml-3 mt-1 text-lg"></i>
                                            </div>
                                        </div>

                                        <!-- Guía de acción -->
                                        <div class="mt-4 p-3 bg-white bg-opacity-60 rounded-lg border border-red-200">
                                            <div class="flex items-center text-red-700">
                                                <i class="fas fa-lightbulb text-amber-500 mr-2"></i>
                                                <span class="text-sm font-semibold">¿Qué hacer ahora?</span>
                                            </div>
                                            <ul class="mt-2 text-sm text-red-600 space-y-1">
                                                <li class="flex items-center">
                                                    <i class="fas fa-check-circle text-emerald-500 mr-2 text-xs"></i>
                                                    Lee cuidadosamente el comentario
                                                </li>
                                                <li class="flex items-center">
                                                    <i class="fas fa-edit text-blue-500 mr-2 text-xs"></i>
                                                    Realiza las correcciones necesarias
                                                </li>
                                                <li class="flex items-center">
                                                    <i class="fas fa-paper-plane text-purple-500 mr-2 text-xs"></i>
                                                    Vuelve a enviar tu trámite
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Sección de Cotejo Presencial -->
            @if($tramite->estado === 'Por Cotejar' && $tramite->cita && $tramite->cita->asistio)
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                    <div class="flex items-center mb-8">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-lg flex items-center justify-center shadow-md mr-4">
                            <i class="fas fa-file-signature text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-[#9d2449]">Cotejo Presencial</h3>
                            <p class="text-sm text-gray-600 mt-1">Resultado de la verificación física de documentos</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @php
                            $documentos = $tramite->documentosSolicitante()->with('documento')->get();
                        @endphp

                        @foreach($documentos as $documento)
                            <div class="bg-white rounded-lg border p-4 transition-all duration-200
                                @if($documento->cotejo_presencial && $documento->documento_cotejado) 
                                    border-emerald-200 bg-emerald-50
                                @elseif($documento->cotejo_presencial && !$documento->documento_cotejado)
                                    border-red-200 bg-red-50
                                @else
                                    border-gray-200
                                @endif">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $documento->documento->nombre }}</h4>
                                        
                                        @if($documento->cotejo_presencial)
                                            @if($documento->documento_cotejado)
                                                <div class="mt-2 flex items-center text-emerald-700">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    <span>Documento verificado y aprobado</span>
                                                </div>
                                                <div class="mt-1 text-sm text-emerald-600">
                                                    <i class="fas fa-calendar-check mr-1"></i>
                                                    Cotejado el {{ $documento->fecha_cotejo->format('d/m/Y H:i') }}
                                                </div>
                                            @else
                                                <div class="mt-2 flex items-center text-red-700">
                                                    <i class="fas fa-times-circle mr-2"></i>
                                                    <span>Documento no aprobado en cotejo físico</span>
                                                </div>
                                                @if($documento->observaciones_cotejo)
                                                    <div class="mt-3 p-3 bg-red-100 rounded-lg">
                                                        <p class="text-sm text-red-700">
                                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                                            {{ $documento->observaciones_cotejo }}
                                                        </p>
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            <div class="mt-2 flex items-center text-gray-600">
                                                <i class="fas fa-clock mr-2"></i>
                                                <span>Pendiente de cotejo físico</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ml-4">
                                        @if($documento->cotejo_presencial && $documento->documento_cotejado)
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">
                                                <i class="fas fa-check mr-1"></i>
                                                Aprobado
                                            </span>
                                        @elseif($documento->cotejo_presencial && !$documento->documento_cotejado)
                                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                                                <i class="fas fa-times mr-1"></i>
                                                Rechazado
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pendiente
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Documentos -->
    <div id="modal-documentos" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative min-h-screen md:min-h-0 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-md rounded-xl shadow-lg relative">
                <!-- Header -->
                <div class="p-4 border-b flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#9d2449]/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-[#9d2449]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Documentos Originales</h3>
                            <p class="text-sm text-gray-500">Persona {{ $tipoPersona }}</p>
                        </div>
                    </div>
                    <button onclick="toggleDocumentosModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Contenido -->
                <div class="p-4 max-h-[calc(100vh-200px)] md:max-h-[500px] overflow-y-auto">
                    <!-- Documentos -->
                    <div class="space-y-3">
                        <!-- Lista de Documentos -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="divide-y divide-gray-200">
                                @foreach($documentosRequeridos as $doc)
                                    <div class="py-2 flex items-center">
                                        <i class="fas fa-check-circle text-[#9d2449] mr-2 text-sm"></i>
                                        <span class="text-sm text-gray-700">{{ $doc->nombre }}</span>
                                    </div>
                                @endforeach

                                <!-- Identificación -->
                                <div class="py-2 flex items-center">
                                    <i class="fas fa-id-card text-[#9d2449] mr-2 text-sm"></i>
                                    <span class="text-sm text-gray-700">
                                        @if($tipoPersona === 'Moral')
                                            Identificación oficial vigente del representante legal
                                        @else
                                            Identificación oficial vigente
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Nota Importante -->
                        <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-amber-500 mt-0.5 mr-2"></i>
                                <div class="text-sm text-amber-700">
                                    <span class="font-medium mb-1">Importante:</span> Todos los documentos deben presentarse en original
                                    @if($tipoPersona === 'Moral')
                                        y solo el representante legal 
                                        @if($tramite->detalleTramite && $tramite->detalleTramite->representanteLegal)
                                            ({{ $tramite->detalleTramite->representanteLegal->nombre }})
                                        @endif
                                        puede realizar el cotejo
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Recordatorio -->
                        <div class="bg-blue-50 rounded-lg p-2 text-center">
                            <p class="text-xs text-blue-700">
                                <i class="fas fa-clock mr-1"></i>
                                Favor de llegar 15 minutos antes de su cita
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Reagendar Cita -->
    <div id="modal-reagendar" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative min-h-screen md:min-h-0 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-md rounded-xl shadow-lg relative">
                <!-- Header -->
                <div class="p-4 border-b flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#9d2449]/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-[#9d2449]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Reagendar Cita</h3>
                            <p class="text-sm text-gray-500">Se asignará la siguiente fecha disponible</p>
                        </div>
                    </div>
                    <button onclick="toggleReagendarModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Contenido -->
                <div class="p-6">
                    <div class="mb-6">
                        <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-amber-500 mt-0.5 mr-2"></i>
                                <div class="text-sm text-amber-700">
                                    <p class="font-medium mb-1">Importante:</p>
                                    <ul class="list-disc ml-4 space-y-1">
                                        <li>Solo puede reagendar su cita una vez</li>
                                        <li>Se le asignará el siguiente día hábil disponible</li>
                                        <li>Asegúrese de asistir en la fecha asignada</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button onclick="reagendarCita({{ $tramite->id }})"
                                class="w-full bg-[#9d2449] text-white rounded-lg px-6 py-3 font-medium hover:bg-[#7a1d3a] transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-calendar-check mr-2"></i>
                            <span>Continuar con Reagendamiento</span>
                        </button>
                        <button onclick="toggleReagendarModal()"
                                class="mt-3 w-full text-gray-600 hover:text-gray-800 font-medium">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Éxito -->
    <div id="modal-exito" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative">
                <!-- Icono y Título -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-check text-emerald-500 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">¡Cita Reagendada con Éxito!</h2>
                    <p class="text-gray-600 mt-2">Su cita ha sido reagendada automáticamente</p>
                </div>

                <!-- Detalles de la Cita -->
                <div class="bg-emerald-50 rounded-lg p-4 mb-6">
                    <div class="space-y-4">
                        <div class="flex items-center text-emerald-700">
                            <i class="fas fa-calendar-alt w-6 mr-3"></i>
                            <span class="font-semibold" id="nueva-fecha"></span>
                        </div>
                        <div class="flex items-center text-emerald-700">
                            <i class="fas fa-clock w-6 mr-3"></i>
                            <span class="font-semibold">09:00 hrs</span>
                        </div>
                        <div class="flex items-start text-emerald-700">
                            <i class="fas fa-map-marker-alt w-6 mr-3 mt-1"></i>
                            <div>
                                <span class="font-semibold">Ciudad Administrativa</span><br>
                                <span class="text-sm">Edificio 1, Nivel 1, Módulo de Proveedores</span><br>
                                <span class="text-xs text-emerald-600">Internacional 8, San Miguel 2da Secc, 68270 Tlalixtac de Cabrera, Oax.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Importante -->
                <div class="bg-amber-50 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-amber-500 mt-1 w-6 mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-amber-800 mb-2">Importante:</h3>
                            <ul class="text-sm text-amber-700 space-y-2">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-amber-500 mr-2"></i>
                                    Llegue 15 minutos antes de su cita
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-amber-500 mr-2"></i>
                                    Traiga todos sus documentos originales
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-amber-500 mr-2"></i>
                                    Se le ha enviado una notificación con los detalles
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botón de Continuar -->
                <div class="text-center">
                    <button onclick="finalizarReagendamiento()" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                        <i class="fas fa-check-circle mr-2"></i>
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/modules/estado-tramite-handler.js') }}"></script>
    <script>
        // Inicializar el handler cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            initEstadoTramiteHandler({{ $tramite->id }});
            
            // Si no hay cita, asignar automáticamente
            @if($tramite->estado === 'Por Cotejar' && !$tramite->cita)
                reagendarCita({{ $tramite->id }});
            @endif
        });

        function updateCitaCountdown() {
            @if($tramite->cita)
                const fechaCita = new Date('{{ $tramite->cita->fecha_hora }}');
                const now = new Date();
                const difference = fechaCita - now;

                if (difference <= 0) {
                    document.getElementById('countdown-days').textContent = '0';
                    document.getElementById('countdown-hours').textContent = '00';
                    document.getElementById('countdown-minutes').textContent = '00';
                    return;
                }

                const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));

                document.getElementById('countdown-days').textContent = days;
                document.getElementById('countdown-hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('countdown-minutes').textContent = minutes.toString().padStart(2, '0');
            @endif
        }

        // Actualizar cada minuto solo si hay cita
        @if($tramite->cita)
            setInterval(updateCitaCountdown, 60000);
            updateCitaCountdown();
        @endif

        function toggleReagendarModal() {
            const modal = document.getElementById('modal-reagendar');
            modal.classList.toggle('hidden');
        }

        function toggleModalExito() {
            const modal = document.getElementById('modal-exito');
            modal.classList.toggle('hidden');
        }

        function finalizarReagendamiento() {
            toggleModalExito();
            window.location.reload();
        }

        function reagendarCita(tramiteId) {
            // Enviar solicitud POST para reagendar
            fetch(`/citas/reagendar/${tramiteId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Formatear la fecha para el modal de éxito
                    const fechaCita = new Date(data.fecha);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    const fechaFormateada = fechaCita.toLocaleDateString('es-ES', options);
                    
                    // Actualizar la fecha en el modal de éxito
                    document.getElementById('nueva-fecha').textContent = fechaFormateada;
                    
                    // Mostrar el modal de éxito
                    toggleModalExito();
                } else {
                    // Simplemente recargar la página si hay error
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Simplemente recargar la página si hay error
                window.location.reload();
            });
        }

        function toggleDocumentosModal() {
            const modal = document.getElementById('modal-documentos');
            modal.classList.toggle('hidden');
        }
    </script>
@endsection
