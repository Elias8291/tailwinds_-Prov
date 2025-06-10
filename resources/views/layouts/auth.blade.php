<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Padrón de Proveedores de Oaxaca')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#9d2449',
                        'primary-dark': '#7a1d37',
                        'primary-light': '#b83055',
                        'primary-50': '#fdf2f5',
                        'primary-100': '#fce7ec',
                        'primary-200': '#f9d0db'
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif']
                    },
                    boxShadow: {
                        'elegant': '0 32px 64px rgba(0, 0, 0, 0.08), 0 16px 32px rgba(157, 36, 73, 0.05), 0 0 0 1px rgba(255, 255, 255, 0.2)',
                        'button': '0 8px 25px rgba(157, 36, 73, 0.25)',
                        'button-hover': '0 12px 35px rgba(157, 36, 73, 0.35)'
                    },
                    zIndex: {
                        '100': '100'
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

        .bg-elegant-pattern::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(ellipse at 25% 20%, rgba(157, 36, 73, 0.02) 0%, transparent 60%),
                radial-gradient(ellipse at 75% 80%, rgba(59, 130, 246, 0.015) 0%, transparent 60%),
                radial-gradient(ellipse at 50% 50%, rgba(157, 36, 73, 0.01) 0%, transparent 70%);
            z-index: 3;
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
            border-radius: 50%;
            background: linear-gradient(135deg, 
                rgba(157, 36, 73, 0.08), 
                rgba(59, 130, 246, 0.06), 
                rgba(157, 36, 73, 0.04));
            backdrop-filter: blur(2px);
            animation: float 25s infinite linear;
            border: 1px solid rgba(157, 36, 73, 0.1);
            box-shadow: 
                0 4px 12px rgba(157, 36, 73, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .floating-element:nth-child(1) {
            width: 120px;
            height: 120px;
            top: 15%;
            left: 8%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 180px;
            height: 180px;
            top: 55%;
            right: 10%;
            animation-delay: -8s;
        }

        .floating-element:nth-child(3) {
            width: 90px;
            height: 90px;
            top: 25%;
            right: 35%;
            animation-delay: -16s;
        }

        .floating-element:nth-child(4) {
            width: 140px;
            height: 140px;
            bottom: 15%;
            left: 15%;
            animation-delay: -4s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0) scale(1);
                opacity: 0.6;
            }
            25% {
                transform: translateY(-15px) translateX(10px) scale(1.05);
                opacity: 0.4;
            }
            50% {
                transform: translateY(-25px) translateX(-5px) scale(0.95);
                opacity: 0.3;
            }
            75% {
                transform: translateY(-10px) translateX(-10px) scale(1.02);
                opacity: 0.5;
            }
        }

        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0);
                opacity: 0.04;
            }
            50% {
                transform: translateY(-5px);
                opacity: 0.06;
            }
        }

        @keyframes sparkle {
            0%, 100% {
                opacity: 0;
                transform: scale(0.5);
            }
            50% {
                opacity: 1;
                transform: scale(1);
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
            z-index: 2;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: linear-gradient(45deg, rgba(157, 36, 73, 0.3), rgba(59, 130, 246, 0.2));
            border-radius: 50%;
            animation: sparkle 4s infinite;
        }

        .particle:nth-child(1) {
            width: 3px;
            height: 3px;
            top: 20%;
            left: 15%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 2px;
            height: 2px;
            top: 40%;
            right: 20%;
            animation-delay: 1s;
        }

        .particle:nth-child(3) {
            width: 4px;
            height: 4px;
            top: 70%;
            left: 25%;
            animation-delay: 2s;
        }

        .particle:nth-child(4) {
            width: 2px;
            height: 2px;
            top: 30%;
            right: 40%;
            animation-delay: 3s;
        }

        .particle:nth-child(5) {
            width: 3px;
            height: 3px;
            bottom: 30%;
            right: 15%;
            animation-delay: 0.5s;
        }

        .particle:nth-child(6) {
            width: 2px;
            height: 2px;
            bottom: 50%;
            left: 30%;
            animation-delay: 1.5s;
        }

        #errorModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            display: none;
        }

        #errorModal.show {
            display: flex;
        }

        .bg-white-solid {
            background-color: rgba(255, 255, 255, 0.95) !important;
        }

        .content-wrapper {
            background-color: #ffffff !important;
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
        <div class="w-full max-w-5xl bg-white/98 backdrop-blur-lg rounded-3xl shadow-elegant overflow-hidden border border-white/30 relative">
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
                                    <h3 class="text-white text-lg font-bold mb-2">Portal Oficial</h3>
                                    <p class="text-white/90 text-sm leading-relaxed">Accede al portal oficial del gobierno de Oaxaca para gestión completa.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Dots -->
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
                        <button class="carousel-dot w-3 h-3 rounded-full transition-all duration-300 bg-white/90 shadow-lg" data-slide="0"></button>
                        <button class="carousel-dot w-3 h-3 rounded-full transition-all duration-300 bg-white/40 shadow-lg" data-slide="1"></button>
                        <button class="carousel-dot w-3 h-3 rounded-full transition-all duration-300 bg-white/40 shadow-lg" data-slide="2"></button>
                        <button class="carousel-dot w-3 h-3 rounded-full transition-all duration-300 bg-white/40 shadow-lg" data-slide="3"></button>
                    </div>

                    <!-- Navigation Arrows -->
                    <button id="prevBtn" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white p-2 rounded-full transition-all duration-300 z-20 border border-white/20 hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button id="nextBtn" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white p-2 rounded-full transition-all duration-300 z-20 border border-white/20 hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido dinámico del panel derecho -->
                <div id="dynamicContainer" class="p-6 lg:p-10 flex flex-col justify-center bg-white min-h-[500px] content-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Error -->
    <div id="errorModal" class="fixed inset-0 z-[9999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Fondo oscuro -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity"></div>

        <!-- Contenedor central -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <!-- Panel del modal -->
                <div class="relative transform overflow-hidden rounded-xl bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <!-- Icono de error -->
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <!-- Contenido del mensaje -->
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                Error al procesar el documento
                            </h3>
                            <div class="mt-2">
                                <p id="errorMessage" class="text-sm text-gray-600"></p>
                            </div>
                        </div>
                    </div>
                    <!-- Botón de cerrar -->
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                onclick="closeErrorModal()"
                                class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark transition-colors duration-200 sm:ml-3 sm:w-auto">
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Datos SAT -->
    <div id="satDataModal" class="hidden fixed inset-0 z-[9999]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity ease-out duration-300"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
                    <!-- Header del Modal -->
                    <div class="bg-gradient-to-r from-primary to-primary-dark px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-white/10 rounded-lg p-2">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-lg font-semibold text-white">Datos del SAT</h3>
                            </div>
                            <!-- Botón X para cerrar -->
                            <button type="button" onclick="closeSatDataModal()" class="rounded-lg bg-white/10 p-2 hover:bg-white/20 transition-colors duration-200">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Contenido del Modal -->
                    <div class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto bg-gradient-to-b from-white to-gray-50">
                        <div id="satDataContent" class="space-y-6">
                            <!-- El contenido se llenará dinámicamente -->
                        </div>
                    </div>

                    <!-- Footer del Modal -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t border-gray-100">
                        <button type="button" 
                                onclick="closeSatDataModal()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/50 transition-all duration-200 shadow-sm hover:shadow-md">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funciones del modal de error
        function showErrorModal() {
            const modal = document.getElementById('errorModal');
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.remove('hidden');
            }
        }

        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.add('hidden');
            }
        }

        // Funciones del modal de datos SAT
        function showSatDataModal() {
            const modal = document.getElementById('satDataModal');
            document.body.classList.add('overflow-hidden');
            modal.classList.remove('hidden');
            modal.classList.add('animate-fadeIn');
        }

        function closeSatDataModal() {
            const modal = document.getElementById('satDataModal');
            document.body.classList.remove('overflow-hidden');
            modal.classList.add('animate-fadeOut');
            setTimeout(() => {
                modal.classList.remove('animate-fadeIn', 'animate-fadeOut');
                modal.classList.add('hidden');
            }, 300);
        }

        window.showErrorModal = showErrorModal;
        window.closeErrorModal = closeErrorModal;
        window.showSatDataModal = showSatDataModal;
        window.closeSatDataModal = closeSatDataModal;

        // Carousel functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');
        const totalSlides = slides.length;

        function showSlide(index) {
            slides.forEach((slide, slideIndex) => {
                slide.classList.remove('opacity-100');
                slide.classList.add('opacity-0');
                if (slideIndex === index) {
                    setTimeout(() => {
                        slide.classList.remove('opacity-0');
                        slide.classList.add('opacity-100');
                    }, 100);
                }
            });

            dots.forEach((dot, dotIndex) => {
                dot.classList.remove('bg-white/90', 'scale-110');
                dot.classList.add('bg-white/40', 'scale-100');
                if (dotIndex === index) {
                    dot.classList.remove('bg-white/40', 'scale-100');
                    dot.classList.add('bg-white/90', 'scale-110');
                }
            });

            currentSlide = index;
        }

        function nextSlide() {
            showSlide((currentSlide + 1) % totalSlides);
        }

        function prevSlide() {
            showSlide((currentSlide - 1 + totalSlides) % totalSlides);
        }

        // Event listeners
        document.getElementById('nextBtn')?.addEventListener('click', nextSlide);
        document.getElementById('prevBtn')?.addEventListener('click', prevSlide);
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => showSlide(index));
        });

        // Auto-play cada 5 segundos
        let autoPlay = setInterval(nextSlide, 5000);

        // Touch/swipe support
        const carousel = document.getElementById('carousel');
        let startX = 0;
        let endX = 0;
        let startY = 0;
        let endY = 0;

        if (carousel) {
            carousel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            }, { passive: true });

            carousel.addEventListener('touchend', (e) => {
                endX = e.changedTouches[0].clientX;
                endY = e.changedTouches[0].clientY;
                handleSwipe();
            }, { passive: true });
        }

        function handleSwipe() {
            const swipeThreshold = 50;
            const diffX = startX - endX;
            const diffY = Math.abs(startY - endY);

            if (Math.abs(diffX) > swipeThreshold && diffY < Math.abs(diffX)) {
                if (diffX > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }

        // Parallax effect
        document.addEventListener('mousemove', (e) => {
            if (!carousel) return;

            const rect = carousel.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width;
            const y = (e.clientY - rect.top) / rect.height;

            const currentSlideElement = slides[currentSlide];
            const img = currentSlideElement.querySelector('img');
            if (img) {
                const moveX = (x - 0.5) * 20;
                const moveY = (y - 0.5) * 20;
                img.style.transform = `scale(1.05) translate(${moveX}px, ${moveY}px)`;
            }
        });

        carousel?.addEventListener('mouseleave', () => {
            const currentSlideElement = slides[currentSlide];
            const img = currentSlideElement.querySelector('img');
            if (img) {
                img.style.transform = 'scale(1.05) translate(0, 0)';
            }
        });

        // Animaciones de transición
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                .animate-fade-in {
                    animation: fadeIn 0.3s ease-out;
                }
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            `;
            document.head.appendChild(style);
        });
        
        // Prevenir el uso de los botones atrás/adelante del navegador en páginas de autenticación
        (function() {
            // Agregar una entrada al historial del navegador
            history.pushState(null, null, location.href);
            
            // Escuchar el evento popstate (botón atrás)
            window.addEventListener('popstate', function(event) {
                // Redirigir al usuario a la página actual
                history.pushState(null, null, location.href);
                
                // Mostrar mensaje opcional
                console.log('Navegación con botones del navegador no permitida.');
            });
            
            // Prevenir teclas de navegación comunes
            document.addEventListener('keydown', function(e) {
                // Alt + Flecha izquierda (Atrás)
                if (e.altKey && e.keyCode === 37) {
                    e.preventDefault();
                    return false;
                }
                // Alt + Flecha derecha (Adelante)
                if (e.altKey && e.keyCode === 39) {
                    e.preventDefault();
                    return false;
                }
                // Backspace fuera de inputs (IE/Edge comportamiento de atrás)
                if (e.keyCode === 8) {
                    var target = e.target || e.srcElement;
                    if (target.tagName !== 'INPUT' && target.tagName !== 'TEXTAREA' && !target.isContentEditable) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        })();
    </script>
</body>
</html> 