<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval' data: blob:;">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="{{ asset('js/error-handler.js') }}"></script>

    <script src="{{ asset('js/components/loading-states.js') }}" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireStyles
</head>

<body class="font-sans antialiased">
    <script src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js" defer></script>

    <div class="bg-logo-pattern"></div>

    <div class="min-h-screen relative" x-data="{ sidebarOpen: false, sidebarHovered: false }">
        <header class="fixed top-0 inset-x-0 z-50">
            @include('layouts.header')
        </header>

        <div class="flex pt-16">
            <div class="hidden md:block">
                @include('layouts.sidebar')
            </div>

            <div class="md:hidden">
                @include('layouts.sidebar-mobile')
            </div>

            <div class="flex-1 transition-all duration-300 md:ml-[65px]" x-data="{ sidebarHovered: false }"
                @sidebar-hover.window="sidebarHovered = $event.detail"
                :class="{ 'md:ml-72': sidebarHovered, 'md:ml-[65px]': !sidebarHovered }">
                <main class="w-full mx-auto">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')

</body>

</html>
