<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso No Autorizado</title>
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
            content: '403';
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
                        <img src="{{ asset('images/ImagenError.png') }}" alt="Error 403" class="w-48 sm:w-64 h-auto drop-shadow-xl">
                    </div>
                </div>

                <!-- Mensaje de Error -->
                <div class="text-center md:text-left order-2 md:order-none">
                    <div class="relative mb-6">
                        <div class="number-container">
                            <h1 class="text-8xl sm:text-9xl font-bold glow-effect tracking-wider">403</h1>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="space-y-6">
                            <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800">
                                ¡Alto ahí! <span class="sparkle">✨</span>
                            </h2>
                           
                        </div>
                    </div>
                    
                    <!-- Botón para regresar -->
                    <div class="mt-8 flex justify-center md:justify-start">
                        <button onclick="window.history.back()" 
                                class="btn-back inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span class="relative">Regresar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 