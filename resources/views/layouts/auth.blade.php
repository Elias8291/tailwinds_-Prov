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
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Configuración de Tailwind -->
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
                <div class="hidden lg:block relative overflow-hidden rounded-l-2xl">
                    <!-- Carousel Container -->
                    <div id="carousel" class="relative w-full h-full">
                        <!-- Slide 1 -->
                        <div class="carousel-slide absolute inset-0 transition-all duration-700 opacity-100" data-slide="0">
                            <img src="{{ asset('images/carrousel_1.webp') }}" 
                                 alt="Imagen de carrusel 1"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/40 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                                    <h3 class="text-white text-lg font-bold mb-2">Padrón de Proveedores</h3>
                                    <p class="text-white/90 text-sm leading-relaxed">Sistema integral para la gestión de proveedores del Gobierno de Oaxaca.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="carousel-slide absolute inset-0 transition-all duration-700 opacity-0" data-slide="1">
                            <img src="{{ asset('images/carrousel2.webp') }}" 
                                 alt="Imagen de carrusel 2"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/40 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                                    <h3 class="text-white text-lg font-bold mb-2">Registro Simplificado</h3>
                                    <p class="text-white/90 text-sm leading-relaxed">Proceso de registro digital optimizado para proveedores de Oaxaca.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div class="carousel-slide absolute inset-0 transition-all duration-700 opacity-0" data-slide="2">
                            <img src="{{ asset('images/carrousel3.webp') }}" 
                                 alt="Imagen de carrusel 3"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/40 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                                    <h3 class="text-white text-lg font-bold mb-2">Gestión Eficiente</h3>
                                    <p class="text-white/90 text-sm leading-relaxed">Administra tus documentos y trámites de manera eficiente y segura.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 4 -->
                        <div class="carousel-slide absolute inset-0 transition-all duration-700 opacity-0" data-slide="3">
                            <img src="{{ asset('images/carrousel4.webp') }}" 
                                 alt="Imagen de carrusel 4"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/40 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                                    <h3 class="text-white text-lg font-bold mb-2">Soporte Continuo</h3>
                                    <p class="text-white/90 text-sm leading-relaxed">Asistencia y seguimiento en cada etapa de tu proceso.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Carousel Navigation -->
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                            <button class="carousel-dot w-2 h-2 rounded-full bg-white/50 transition-all duration-300" data-slide="0"></button>
                            <button class="carousel-dot w-2 h-2 rounded-full bg-white/30 transition-all duration-300" data-slide="1"></button>
                            <button class="carousel-dot w-2 h-2 rounded-full bg-white/30 transition-all duration-300" data-slide="2"></button>
                            <button class="carousel-dot w-2 h-2 rounded-full bg-white/30 transition-all duration-300" data-slide="3"></button>
                        </div>
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
        // Carousel functionality
        document.addEventListener('DOMContentLoaded', function() {
            let currentSlide = 0;
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('.carousel-dot');
            const totalSlides = slides.length;
            let slideInterval;

            function updateSlide(index) {
                slides.forEach(slide => {
                    slide.style.opacity = '0';
                    slide.style.zIndex = '0';
                });
                dots.forEach(dot => {
                    dot.classList.remove('bg-white/50');
                    dot.classList.add('bg-white/30');
                });

                slides[index].style.opacity = '1';
                slides[index].style.zIndex = '1';
                dots[index].classList.remove('bg-white/30');
                dots[index].classList.add('bg-white/50');
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlide(currentSlide);
            }

            // Event listeners for dots
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    currentSlide = index;
                    updateSlide(currentSlide);
                    resetInterval();
                });
            });

            function resetInterval() {
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000);
            }

            // Initialize carousel
            updateSlide(currentSlide);
            slideInterval = setInterval(nextSlide, 5000);
        });
    </script>
</body>
</html> 