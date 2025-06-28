@extends('layouts.auth')

@section('title', 'Bienvenido - Padrón de Proveedores del Estado de Oaxaca')

@section('content')
<div class="bg-white p-4 rounded-2xl lg:rounded-l-none shadow-2xl relative overflow-hidden">
    <!-- Decoración de fondo -->
    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-primary/10 to-primary-dark/10 rounded-full -translate-y-10 translate-x-10"></div>
    <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-primary/5 to-primary-dark/5 rounded-full translate-y-8 -translate-x-8"></div>
    
    <!-- Header con Logo -->
    <div class="text-center mb-4 relative z-10">
        <div class="flex flex-col items-center justify-center mb-3">
            <div class="w-14 h-14 lg:w-16 lg:h-16 flex items-center justify-center mb-2 bg-gradient-to-br from-primary/10 to-primary-dark/10 rounded-full p-2">
                <img src="{{ asset('images/logoprin.jpg') }}" alt="Logo Estado de Oaxaca" class="w-full h-full object-contain rounded-full">
            </div>
            <div class="text-center space-y-1">
                <span class="text-primary font-bold text-sm lg:text-base block tracking-wide">ADMINISTRACIÓN</span>
                <span class="text-gray-500 text-xs font-medium uppercase tracking-wider">Gobierno del Estado de Oaxaca</span>
            </div>
        </div>
        
        <div class="space-y-1 mb-2">
            <h1 class="text-lg lg:text-xl font-bold text-gray-800 leading-tight">
                ¡Bienvenido!
            </h1>
            <h2 class="text-sm lg:text-base font-semibold text-primary leading-tight">
                Padrón de Proveedores del<br class="lg:hidden">
                <span class="text-primary-dark">Estado de Oaxaca</span>
            </h2>
        </div>
        
        <p class="text-gray-600 text-xs leading-relaxed max-w-xs mx-auto">
            Portal oficial para registro y gestión de proveedores del Estado de Oaxaca. 
            <span class="block mt-1 text-xs text-gray-500">Transparencia, eficiencia y desarrollo económico.</span>
        </p>
    </div>

    <!-- Botones de Acción -->
    <div class="space-y-2 mb-3 relative z-10">
        <a href="{{ route('login') }}" class="group w-full bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 relative overflow-hidden inline-flex items-center justify-center text-sm">
            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span>Iniciar Sesión</span>
                <div class="absolute -right-2 w-2 h-2 bg-white/30 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping"></div>
            </div>
        </a>
        
        <a href="{{ route('register') }}" class="group w-full bg-white hover:bg-gray-50 text-primary hover:text-primary-dark font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border-2 border-primary/20 hover:border-primary/40 relative overflow-hidden inline-flex items-center justify-center text-sm">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-primary-dark/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span>Registrarse como Proveedor</span>
            </div>
        </a>
    </div>

    <!-- Enlace de recuperación de contraseña -->
    <div class="text-center relative z-10 mb-3">
        <a 
            href="{{ route('password.request') }}" 
            class="text-gray-500 hover:text-primary text-xs font-medium transition-all duration-200 flex items-center justify-center space-x-2 hover:bg-gray-50 py-1.5 px-3 rounded-lg"
        >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"/>
            </svg>
            <span>¿Olvidaste tu contraseña?</span>
        </a>
    </div>

    <!-- Footer informativo -->
    <div class="pt-3 border-t border-gray-100 text-center relative z-10">
        <p class="text-xs text-gray-400 leading-relaxed">
            Sistema oficial del Gobierno del Estado de Oaxaca<br>
            <span class="text-gray-500 font-medium">Secretaría de Administración</span>
        </p>
    </div>
</div>

@endsection