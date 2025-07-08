<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Padrón de Proveedores de Oaxaca')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Configuración de Tailwind -->
    {{--
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#9d2449',
                        'primary-dark': '#7a1d37',
                        'primary-light': '#b83055',
                        textDark: '#1f2937'
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    --}}

    <style>
        /* Animaciones personalizadas */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }
            to {
                opacity: 0;
                transform: scale(0.95);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        .animate-fadeOut {
            animation: fadeOut 0.3s ease-out;
        }

        /* Fondo elegante profesional */
        .bg-elegant-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: linear-gradient(135deg, 
                #ffffff 0%, 
                #f8fafc 20%, 
                #f1f5f9 40%, 
                #e2e8f0 60%, 
                #f8fafc 80%, 
                #ffffff 100%);
            pointer-events: none;
            z-index: 1;
        }

        .bg-elegant-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('/images/logoNegro.png');
            background-repeat: repeat;
            background-size: 180px auto;
            opacity: 0.04;
            z-index: 2;
            animation: logoFloat 30s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-10px) scale(1.02);
            }
            100% {
                transform: translateY(0) scale(1);
            }
        }

        /* Elementos decorativos flotantes */
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 2;
            overflow: hidden;
        }

        .floating-element {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle at center, rgba(157, 36, 73, 0.03) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        .floating-element:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            top: 60%;
            right: 15%;
            animation-delay: -5s;
        }

        .floating-element:nth-child(3) {
            bottom: 10%;
            left: 20%;
            animation-delay: -10s;
        }

        .floating-element:nth-child(4) {
            top: 30%;
            right: 30%;
            animation-delay: -15s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg) scale(1);
            }
            25% {
                transform: translate(10px, 10px) rotate(5deg) scale(1.1);
            }
            50% {
                transform: translate(-5px, 15px) rotate(-5deg) scale(0.95);
            }
            75% {
                transform: translate(-15px, -5px) rotate(3deg) scale(1.05);
            }
        }

        /* Partículas decorativas */
        .decorative-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 3;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(157, 36, 73, 0.1);
            border-radius: 50%;
            animation: particleFloat 15s infinite;
        }

        .particle:nth-child(1) { top: 20%; left: 20%; animation-delay: 0s; }
        .particle:nth-child(2) { top: 40%; right: 25%; animation-delay: -2s; }
        .particle:nth-child(3) { bottom: 30%; left: 30%; animation-delay: -4s; }
        .particle:nth-child(4) { top: 50%; right: 40%; animation-delay: -6s; }
        .particle:nth-child(5) { bottom: 40%; right: 35%; animation-delay: -8s; }
        .particle:nth-child(6) { top: 30%; left: 35%; animation-delay: -10s; }

        @keyframes particleFloat {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.3;
            }
            25% {
                transform: translate(50px, -30px) scale(1.2);
                opacity: 0.6;
            }
            50% {
                transform: translate(20px, 40px) scale(0.8);
                opacity: 0.4;
            }
            75% {
                transform: translate(-40px, 20px) scale(1.1);
                opacity: 0.5;
            }
        }

        /* Notificaciones */
        .notification-slide-in {
            animation: slideIn 0.5s ease-out forwards;
        }

        .notification-slide-out {
            animation: slideOut 0.5s ease-out forwards;
        }

        @keyframes slideIn {
            from {
                transform: translate(-50%, -100%);
                opacity: 0;
            }
            to {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translate(-50%, 0);
                opacity: 1;
            }
            to {
                transform: translate(-50%, -100%);
                opacity: 0;
            }
        }

        /* Card personalizada */
        .card-custom {
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="font-inter text-textDark overflow-x-hidden">
    <!-- Fondo elegante con degradado y logo -->
    <div class="bg-elegant-pattern"></div>
    
    <!-- Elementos decorativos flotantes -->
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>
    
    <!-- Partículas decorativas -->
    <div class="decorative-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
    
    <!-- Contenedor Principal -->
    <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
        <div class="w-full max-w-[1000px] mx-auto">
            <div class="grid lg:grid-cols-2 min-h-[500px]">
                <!-- Sección del Carousel -->
                <div class="hidden lg:block relative overflow-hidden rounded-l-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
                    <!-- Carousel Container -->
                    <div id="carousel" class="relative w-full h-full group">
                        <!-- Slide 1 -->
                        <div class="carousel-slide absolute inset-0 transition-all duration-[1200ms] ease-out opacity-100" data-slide="0">
                            <div class="relative w-full h-full">
                            <img src="{{ asset('images/carrousel_1.webp') }}" 
                                     alt="Padrón de Proveedores"
                                     class="w-full h-full object-cover opacity-70 transition-all duration-[3000ms] scale-105 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-br from-slate-900/70 via-slate-800/50 to-slate-900/70"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                            </div>
                            
                            <!-- Contenido Principal -->
                            <div class="absolute inset-0 flex items-center justify-center p-12">
                                <div class="text-center max-w-md mx-auto space-y-8">
                                    <!-- Icono Principal -->
                                    <div class="mx-auto w-20 h-20 bg-gradient-to-br from-white/15 to-white/5 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30 shadow-2xl">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    
                                    <!-- Título Principal -->
                                    <div class="space-y-3">
                                        <h2 class="text-3xl font-light text-white tracking-wider">
                                            Registro de Proveedores
                                        </h2>
                                        <h3 class="text-lg font-medium text-white/90 leading-relaxed">
                                            del Gobierno de Oaxaca
                                        </h3>
                                        <div class="w-16 h-0.5 bg-gradient-to-r from-white/60 to-white/20 mx-auto"></div>
                                    </div>
                                    
                                    <!-- Descripción -->
                                    <p class="text-sm text-white/90 leading-relaxed font-light">
                                        Plataforma oficial para empresas y personas físicas que desean ser proveedores del Gobierno del Estado de Oaxaca. 
                                        Proceso completamente digital y transparente.
                                    </p>
                                    
                                    <!-- Badge Oficial -->
                                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-white/15 backdrop-blur-sm rounded-full border border-white/30">
                                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                        <span class="text-xs text-white/90 font-medium tracking-wide">GOBIERNO DE OAXACA</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="carousel-slide absolute inset-0 transition-all duration-[1200ms] ease-out opacity-0" data-slide="1">
                            <div class="relative w-full h-full">
                            <img src="{{ asset('images/carrousel2.webp') }}" 
                                     alt="Registro con QR del SAT"
                                     class="w-full h-full object-cover opacity-70 transition-all duration-[3000ms] scale-105 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-900/70 via-slate-800/50 to-slate-900/70"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                            </div>
                            
                            <div class="absolute inset-0 flex items-center justify-center p-12">
                                <div class="text-center max-w-md mx-auto space-y-8">
                                    <div class="mx-auto w-20 h-20 bg-gradient-to-br from-white/15 to-white/5 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30 shadow-2xl">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4M4 8h4m0 0V4m0 4h4m0 0v4M4 16h4m0 0v4"/>
                                        </svg>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <h2 class="text-3xl font-light text-white tracking-wider">
                                            Validación con QR
                                        </h2>
                                        <h3 class="text-lg font-medium text-white/90 leading-relaxed">
                                            Constancia Fiscal del SAT
                                        </h3>
                                        <div class="w-16 h-0.5 bg-gradient-to-r from-white/60 to-white/20 mx-auto"></div>
                                    </div>
                                    
                                    <p class="text-sm text-white/90 leading-relaxed font-light">
                                        Suba su Constancia de Situación Fiscal con código QR y nuestro sistema validará automáticamente 
                                        su información directamente con el SAT. Sin papeles, sin filas.
                                    </p>
                                    
                                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-white/15 backdrop-blur-sm rounded-full border border-white/30">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                                        <span class="text-xs text-white/90 font-medium tracking-wide">VALIDACIÓN AUTOMÁTICA</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div class="carousel-slide absolute inset-0 transition-all duration-[1200ms] ease-out opacity-0" data-slide="2">
                            <div class="relative w-full h-full">
                            <img src="{{ asset('images/carrousel3.webp') }}" 
                                     alt="Gestión de Trámites"
                                     class="w-full h-full object-cover opacity-70 transition-all duration-[3000ms] scale-105 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/70 via-slate-800/50 to-slate-900/70"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                            </div>
                            
                            <div class="absolute inset-0 flex items-center justify-center p-12">
                                <div class="text-center max-w-md mx-auto space-y-8">
                                    <div class="mx-auto w-20 h-20 bg-gradient-to-br from-white/15 to-white/5 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30 shadow-2xl">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <h2 class="text-3xl font-light text-white tracking-wider">
                                            Gestión de Trámites
                                        </h2>
                                        <h3 class="text-lg font-medium text-white/90 leading-relaxed">
                                            Inscripción y Renovación
                                        </h3>
                                        <div class="w-16 h-0.5 bg-gradient-to-r from-white/60 to-white/20 mx-auto"></div>
                                    </div>
                                    
                                    <p class="text-sm text-white/90 leading-relaxed font-light">
                                        Gestione la inscripción inicial, renovaciones anuales y actualizaciones de datos. 
                                        Seguimiento en tiempo real del estatus de todos sus trámites gubernamentales.
                                    </p>
                                    
                                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-white/15 backdrop-blur-sm rounded-full border border-white/30">
                                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                        <span class="text-xs text-white/90 font-medium tracking-wide">SEGUIMIENTO EN LÍNEA</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 4 -->
                        <div class="carousel-slide absolute inset-0 transition-all duration-[1200ms] ease-out opacity-0" data-slide="3">
                            <div class="relative w-full h-full">
                            <img src="{{ asset('images/carrousel4.webp') }}" 
                                     alt="Portal de Proveedores"
                                     class="w-full h-full object-cover opacity-70 transition-all duration-[3000ms] scale-105 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-br from-purple-900/70 via-slate-800/50 to-slate-900/70"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                            </div>
                            
                            <div class="absolute inset-0 flex items-center justify-center p-12">
                                <div class="text-center max-w-md mx-auto space-y-8">
                                    <div class="mx-auto w-20 h-20 bg-gradient-to-br from-white/15 to-white/5 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30 shadow-2xl">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <h2 class="text-3xl font-light text-white tracking-wider">
                                            Portal del Proveedor
                                        </h2>
                                        <h3 class="text-lg font-medium text-white/90 leading-relaxed">
                                            Dashboard Personalizado
                                        </h3>
                                        <div class="w-16 h-0.5 bg-gradient-to-r from-white/60 to-white/20 mx-auto"></div>
                                    </div>
                                    
                                    <p class="text-sm text-white/90 leading-relaxed font-light">
                                        Acceda a su portal personalizado con historial de trámites, documentos digitales, 
                                        notificaciones importantes y oportunidades de licitación del gobierno estatal.
                                    </p>
                                    
                                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-white/15 backdrop-blur-sm rounded-full border border-white/30">
                                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                                        <span class="text-xs text-white/90 font-medium tracking-wide">ACCESO PERMANENTE</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Controles de navegación elegantes y compactos -->
                        <div class="absolute top-1/2 left-6 transform -translate-y-1/2 z-10">
                            <button onclick="window.previousSlide()" class="group w-10 h-10 bg-gradient-to-br from-white/15 to-white/5 hover:from-white/25 hover:to-white/10 backdrop-blur-sm rounded-full border border-white/20 hover:border-white/40 flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4 text-white group-hover:text-white transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 rounded-full transition-all duration-300"></div>
                            </button>
                        </div>
                        
                        <div class="absolute top-1/2 right-6 transform -translate-y-1/2 z-10">
                            <button onclick="window.nextSlideManual()" class="group w-10 h-10 bg-gradient-to-br from-white/15 to-white/5 hover:from-white/25 hover:to-white/10 backdrop-blur-sm rounded-full border border-white/20 hover:border-white/40 flex items-center justify-center transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4 text-white group-hover:text-white transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                                <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 rounded-full transition-all duration-300"></div>
                            </button>
                        </div>

                        <!-- Indicadores elegantes -->
                        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-4">
                            <button class="carousel-dot group/dot relative transition-all duration-300" data-slide="0">
                                <div class="w-12 h-1 bg-white/40 rounded-full overflow-hidden">
                                    <div class="dot-progress h-full bg-white rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </button>
                            <button class="carousel-dot group/dot relative transition-all duration-300" data-slide="1">
                                <div class="w-12 h-1 bg-white/40 rounded-full overflow-hidden">
                                    <div class="dot-progress h-full bg-white rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </button>
                            <button class="carousel-dot group/dot relative transition-all duration-300" data-slide="2">
                                <div class="w-12 h-1 bg-white/40 rounded-full overflow-hidden">
                                    <div class="dot-progress h-full bg-white rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </button>
                            <button class="carousel-dot group/dot relative transition-all duration-300" data-slide="3">
                                <div class="w-12 h-1 bg-white/40 rounded-full overflow-hidden">
                                    <div class="dot-progress h-full bg-white rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </button>
                        </div>

                        <!-- Barra de progreso superior minimalista -->
                        <div class="absolute top-0 left-0 right-0 h-0.5 bg-white/10">
                            <div id="progressBar" class="h-full bg-gradient-to-r from-white to-white/80 transition-all duration-100 ease-linear" style="width: 0%"></div>
                        </div>

                        <!-- Elemento decorativo inferior -->
                        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
                    </div>
                </div>

                <!-- Sección del Contenido -->
                <div class="bg-white p-8 rounded-2xl lg:rounded-l-none shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Error -->
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-sm mx-auto overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Error
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="error-message">
                                Ha ocurrido un error. Por favor, inténtelo de nuevo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                        onclick="closeErrorModal()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // ===== CARRUSEL SIMPLE Y FUNCIONAL =====
        
        // Estado del carrusel
        let currentSlideIndex = 0;
        const totalSlides = 4;
        let autoSlideTimer = null;

        // FUNCIÓN ANTERIOR - SIEMPRE DISPONIBLE
        window.previousSlide = function() {
            currentSlideIndex = (currentSlideIndex - 1 + totalSlides) % totalSlides;
            showSlide(currentSlideIndex);
            restartAutoSlide();
        };

        // FUNCIÓN SIGUIENTE - SIEMPRE DISPONIBLE  
        window.nextSlideManual = function() {
            currentSlideIndex = (currentSlideIndex + 1) % totalSlides;
            showSlide(currentSlideIndex);
            restartAutoSlide();
        };

        // FUNCIÓN IR A SLIDE ESPECÍFICO
        window.goToSlide = function(index) {
            currentSlideIndex = index;
            showSlide(currentSlideIndex);
            restartAutoSlide();
        };

        // Mostrar slide específico
        function showSlide(index) {
            
            // Obtener todos los slides
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('.carousel-dot');
            
            if (slides.length === 0) {
                return;
            }

            // Actualizar slides
            slides.forEach((slide, i) => {
                if (i === index) {
                    // Slide activo
                    slide.style.opacity = '1';
                    slide.style.zIndex = '10';
                    slide.style.visibility = 'visible';
                } else {
                    // Slides inactivos
                    slide.style.opacity = '0';
                    slide.style.zIndex = '1';
                    slide.style.visibility = 'hidden';
                }
            });

            // Actualizar dots
            dots.forEach((dot, i) => {
                const progress = dot.querySelector('.dot-progress');
                if (progress) {
                    if (i === index) {
                        progress.style.width = '100%';
                        progress.style.backgroundColor = '#ffffff';
                        dot.style.opacity = '1';
                    } else {
                        progress.style.width = '0%';
                        progress.style.backgroundColor = 'rgba(255,255,255,0.4)';
                        dot.style.opacity = '0.7';
                    }
                }
            });
        }

        // Auto-avance
        function startAutoSlide() {
            autoSlideTimer = setInterval(() => {
                currentSlideIndex = (currentSlideIndex + 1) % totalSlides;
                showSlide(currentSlideIndex);
            }, 7000);
        }

        function restartAutoSlide() {
            clearInterval(autoSlideTimer);
            startAutoSlide();
        }

        // Configurar dots cuando el DOM esté listo
        function setupCarousel() {
            
            const dots = document.querySelectorAll('.carousel-dot');
            
            // Event listeners para dots
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    window.goToSlide(index);
                });
            });

            // Pausar en hover
            const carousel = document.getElementById('carousel');
            if (carousel) {
                carousel.addEventListener('mouseenter', () => {
                    clearInterval(autoSlideTimer);
                });
                
                carousel.addEventListener('mouseleave', () => {
                    startAutoSlide();
                });
            }

            // Mostrar primer slide
            showSlide(0);
            startAutoSlide();
        }

        // Inicializar cuando DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupCarousel);
        } else {
            setupCarousel();
        }
    </script>
</body>
</html> 