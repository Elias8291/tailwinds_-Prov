<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Validador de formularios -->
    <script src="{{ asset('js/validators/form-validator.js') }}" defer></script>
    <script src="{{ asset('js/validators/tramite-validator.js') }}" defer></script>
    <!-- Sistema de estados de carga -->
    <script src="{{ asset('js/components/loading-states.js') }}" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        
        /* Transiciones suaves */
        .main-wrapper {
            transition: padding-left 300ms ease-in-out;
            min-height: calc(100vh - 4rem);
        }

        /* Fondo con logo */
        .bg-logo-pattern {
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
            z-index: 0;
        }

        .bg-logo-pattern::before {
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
            z-index: 1;
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
    </style>
</head>
<body class="font-sans antialiased">
    <!-- Fondo con logo -->
    <div class="bg-logo-pattern"></div>

    <div class="min-h-screen relative" x-data="{ sidebarOpen: false, sidebarHovered: false }">
        <!-- Header (fixed) -->
        <header class="fixed top-0 inset-x-0 z-50">
            @include('layouts.header')
        </header>

        <!-- Main layout -->
        <div class="flex pt-16">
            <!-- Desktop sidebar -->
            <div class="hidden md:block">
                @include('layouts.sidebar')
            </div>

            <!-- Mobile sidebar -->
            <div class="md:hidden">
                @include('layouts.sidebar-mobile')
            </div>

            <!-- Main content area -->
            <div class="flex-1 transition-all duration-300 md:ml-[65px]" 
                 x-data="{ sidebarHovered: false }"
                 @sidebar-hover.window="sidebarHovered = $event.detail"
                 :class="{ 'md:ml-72': sidebarHovered, 'md:ml-[65px]': !sidebarHovered }">
                <main class="w-full mx-auto">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')
    
    <script>
        // Prevenir el uso de los botones atrás/adelante del navegador
        (function() {
            // Agregar una entrada al historial del navegador
            history.pushState(null, null, location.href);
            
            // Escuchar el evento popstate (botón atrás)
            window.addEventListener('popstate', function(event) {
                // Redirigir al usuario a la página actual
                history.pushState(null, null, location.href);
                
                // Mostrar mensaje opcional
                console.log('Navegación con botones del navegador no permitida en esta sesión.');
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