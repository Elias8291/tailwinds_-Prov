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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* Transiciones suaves */
        .main-wrapper {
            transition: padding-left 300ms ease-in-out;
            min-height: calc(100vh - 4rem);
        }

        /* Fondo con logo repetido */
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
    </style>
  
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen relative" x-data="{ sidebarOpen: false, sidebarHovered: false }">
        <div class="bg-logo-pattern"></div>
        <!-- Header (fixed) -->
        <header class="fixed top-0 inset-x-0 z-50">
            @include('layouts.header')
        </header>

        <!-- Main layout -->
        <div class="flex pt-16">
            <!-- Desktop sidebar -->
            <div class="hidden md:block">
                <div class="fixed top-16 bottom-0 left-0 transition-all duration-300 ease-in-out w-[50px] hover:w-56 bg-gradient-to-b from-white to-gray-50 border-r border-gray-200 shadow-lg overflow-hidden hover:overflow-y-auto z-30"
                     @mouseenter="sidebarHovered = true"
                     @mouseleave="sidebarHovered = false">
                    @include('layouts.sidebar')
                </div>
            </div>

            <!-- Mobile sidebar -->
            <div class="md:hidden">
                @include('layouts.sidebar-mobile')
            </div>

            <!-- Main content area -->
            <div class="flex-1 transition-all duration-300" :class="{ 'md:ml-56': sidebarHovered, 'md:ml-[50px]': !sidebarHovered }">
                <main class="w-full mx-auto px-4 md:px-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @stack('modals')
</body>
</html> 