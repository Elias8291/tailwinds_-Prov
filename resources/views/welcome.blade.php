@extends('layouts.auth')

@section('title', 'Bienvenido - Padrón de Proveedores de Oaxaca')

@section('content')
<!-- Header con Logo -->
<div class="text-center mb-8">
    <div class="flex flex-col items-center justify-center mb-4">
        <div class="w-16 h-16 flex items-center justify-center mb-3">
            <img src="{{ asset('images/logoprin.jpg') }}" alt="Logo" class="w-full h-full object-contain">
        </div>
        <div class="text-center">
            <span class="text-primary font-bold text-lg block">ADMINISTRACIÓN</span>
            <span class="text-gray-600 text-xs font-medium">Gobierno de Oaxaca</span>
        </div>
    </div>
    
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Bienvenido</h1>
    <h2 class="text-lg font-semibold text-primary mb-4 leading-tight">Padrón de Proveedores<br>de Oaxaca</h2>
    <p class="text-gray-600 text-sm leading-relaxed max-w-xs mx-auto">
        Portal oficial para registro y gestión de proveedores del Estado de Oaxaca.
    </p>
</div>

<!-- Botones de Login Elegantes -->
<div class="space-y-3 mb-6">
    <a href="{{ route('login') }}" class="group w-full bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 relative overflow-hidden inline-flex items-center justify-center text-sm">
        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            <span>Iniciar Sesión</span>
        </div>
    </a>
    
    <a href="{{ route('register') }}" class="group w-full bg-white text-primary font-medium py-2.5 px-4 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 border border-primary/30 hover:border-primary/50 relative overflow-hidden inline-flex items-center justify-center text-sm">
        <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <span>Registrarse</span>
        </div>
    </a>
</div>

<!-- Enlace de recuperación de contraseña -->
<div class="text-center mt-4">
    <a 
        href="{{ route('password.request') }}" 
        class="text-gray-600 hover:text-primary text-sm font-medium transition-colors duration-200 flex items-center justify-center space-x-1"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"/>
        </svg>
        <span>¿Olvidaste tu contraseña?</span>
    </a>
</div>

@endsection