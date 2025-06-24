<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Página Expirada</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    }
                }
            }
        }
    </script>
    <style>
        .bg-logo-pattern {
            background-image: url('/images/logoNegro.png');
            background-repeat: repeat;
            background-size: 150px auto;
            opacity: 0.05;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }
        @keyframes floatAnimation {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .float-animation {
            animation: floatAnimation 3s ease-in-out infinite;
        }
        @keyframes glowNumber {
            0%, 100% { 
                text-shadow: 
                    0 0 20px rgba(157, 36, 73, 0.2),
                    0 0 40px rgba(157, 36, 73, 0.1),
                    2px 2px 2px rgba(0, 0, 0, 0.1);
            }
            50% { 
                text-shadow: 
                    0 0 30px rgba(157, 36, 73, 0.4),
                    0 0 60px rgba(157, 36, 73, 0.2),
                    2px 2px 2px rgba(0, 0, 0, 0.2);
            }
        }
        .glow-effect {
            animation: glowNumber 3s ease-in-out infinite;
            background: linear-gradient(135deg, #9d2449 0%, #b83055 50%, #9d2449 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }
        .number-container {
            position: relative;
            display: inline-block;
        }
        .number-container::before {
            content: '419';
            position: absolute;
            left: 0;
            top: 0;
            z-index: -1;
            background: linear-gradient(135deg, #fce7ec 0%, #f9d0db 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.5;
            transform: translate(4px, 4px);
            filter: blur(8px);
        }
        @media (max-width: 640px) {
            .bg-logo-pattern {
                background-size: 100px auto;
            }
        }
        .btn-back {
            position: relative;
            overflow: hidden;
        }
        .btn-back::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        .btn-back:hover::after {
            transform: translateX(100%);
        }
        .message-box {
            position: relative;
            background: linear-gradient(135deg, rgba(253, 242, 245, 0.5), rgba(252, 231, 236, 0.8));
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid rgba(157, 36, 73, 0.1);
            box-shadow: 
                0 4px 6px -1px rgba(157, 36, 73, 0.05),
                0 2px 4px -1px rgba(157, 36, 73, 0.03);
        }
        .message-box::before {
            content: '"';
            position: absolute;
            top: -0.5rem;
            left: 1rem;
            font-size: 4rem;
            line-height: 1;
            font-family: serif;
            color: rgba(157, 36, 73, 0.1);
        }
        .sparkle {
            display: inline-block;
            animation: sparkle 1.5s ease-in-out infinite;
        }
        @keyframes sparkle {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }
        .highlight-text {
            background: linear-gradient(120deg, rgba(157, 36, 73, 0.1) 0%, rgba(157, 36, 73, 0.05) 100%);
            padding: 0.2em 0.4em;
            border-radius: 0.3em;
            font-weight: 500;
        }
        @keyframes clockTick {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(6deg); }
        }
        .clock-icon {
            animation: clockTick 1s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 py-6 sm:py-12 px-4 sm:px-6 lg:px-8 relative">
        <div class="bg-logo-pattern"></div>
        <div class="w-full max-w-4xl bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-4 sm:p-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100 relative z-10 mx-4 sm:mx-8">
            <div class="grid md:grid-cols-2 gap-4 sm:gap-8 items-center">
                <!-- Imagen de Error -->
                <div class="flex justify-center order-1 md:order-none">
                    <div class="float-animation">
                        <img src="{{ asset('images/ImagenError.png') }}" alt="Error 419" class="w-48 sm:w-64 h-auto drop-shadow-xl">
                    </div>
                </div>

                <!-- Mensaje de Error -->
                <div class="text-center md:text-left order-2 md:order-none">
                    <div class="relative mb-6">
                        <div class="number-container">
                            <h1 class="text-8xl sm:text-9xl font-bold glow-effect tracking-wider">419</h1>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="space-y-6">
                            <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800">
                                Sesión expirada <span class="sparkle clock-icon">⏰</span>
                            </h2>
                            
                            <div class="message-box">
                                <p class="text-gray-700 text-sm sm:text-base leading-relaxed">
                                    Su sesión ha expirado por motivos de seguridad. 
                                    <br><span class="highlight-text">Esto es normal</span> cuando se permanece inactivo durante un período prolongado.
                                </p>
                                
                                <div class="mt-4 text-xs sm:text-sm text-gray-600">
                                    <p>
                                        <span class="font-medium">¿Por qué pasó esto?</span>
                                    </p>
                                    <ul class="list-disc list-inside mt-2 space-y-1">
                                        <li>La página estuvo abierta durante mucho tiempo</li>
                                        <li>El token de seguridad ha expirado</li>
                                        <li>Es una medida de protección automática del sistema</li>
                                    </ul>
                                    
                                    <div class="mt-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                        <p class="text-amber-800 font-medium text-xs flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Su información no se ha perdido, simplemente recargue la página
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                        <button onclick="window.location.reload()" 
                                class="btn-back inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="relative">Recargar página</span>
                        </button>
                        
                        <button onclick="window.history.back()" 
                                class="btn-back inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-primary bg-white border border-primary hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span class="relative">Página anterior</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 