@extends('layouts.auth')

@section('title', 'Restablecer Contraseña - Padrón de Proveedores de Oaxaca')

@section('content')
<!-- Header con Logo -->
<div class="text-center mb-8">
    <div class="flex items-center justify-center mb-4">
        <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-dark rounded-xl flex items-center justify-center mr-3 shadow-lg">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0L3 7v10c0 5.55 3.84 9.739 9 9.739s9-4.189 9-9.739V7L12 0z"/>
            </svg>
        </div>
        <div class="text-left">
            <span class="text-primary font-bold text-lg block">ADMINISTRACIÓN</span>
            <span class="text-gray-600 text-xs font-medium">Gobierno de Oaxaca</span>
        </div>
    </div>
    
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Restablecer Contraseña</h1>
    <p class="text-gray-600 text-sm leading-relaxed max-w-xs mx-auto">
        Ingresa tu nueva contraseña para acceder a tu cuenta.
    </p>
</div>

<!-- Formulario de Reset de Contraseña -->
<div class="bg-white rounded-2xl shadow-card p-8">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Campo Email (readonly) -->
        <div>
            <label for="email" class="block text-sm font-bold text-gray-700 mb-3">
                Correo Electrónico
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input 
                    type="email" 
                    id="email" 
                    name="email_display" 
                    value="{{ $email }}" 
                    readonly
                    class="block w-full pl-10 pr-3 py-3.5 border border-gray-300 rounded-xl bg-gray-50 text-gray-600 focus:outline-none focus:ring-0 focus:border-gray-300 shadow-sm text-sm"
                >
            </div>
        </div>

        <!-- Campo Nueva Contraseña -->
        <div>
            <label for="password" class="block text-sm font-bold text-gray-700 mb-3">
                Nueva Contraseña
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    class="block w-full pl-10 pr-3 py-3.5 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm text-sm @error('password') border-red-300 focus:ring-red-500 @enderror"
                    placeholder="Mínimo 8 caracteres"
                >
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Campo Confirmar Contraseña -->
        <div>
            <label for="password-confirm" class="block text-sm font-bold text-gray-700 mb-3">
                Confirmar Nueva Contraseña
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <input 
                    type="password" 
                    id="password-confirm" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    class="block w-full pl-10 pr-3 py-3.5 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm text-sm"
                    placeholder="Confirma tu nueva contraseña"
                >
            </div>
        </div>

        <!-- Botón de Submit -->
        <div class="pt-4">
            <button 
                type="submit" 
                class="group w-full bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-300 shadow-button hover:shadow-button-hover transform hover:-translate-y-0.5 relative overflow-hidden"
            >
                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <span>RESTABLECER CONTRASEÑA</span>
                </div>
            </button>
        </div>
    </form>
</div>

<!-- Link de regreso -->
<div class="text-center mt-6">
    <a 
        href="{{ route('login') }}" 
        class="text-primary hover:text-primary-dark text-sm font-medium transition-colors duration-200 flex items-center justify-center space-x-1"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Volver al inicio de sesión</span>
    </a>
</div>

<!-- Información de seguridad -->
<div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-200">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Información de seguridad</h3>
            <div class="mt-2 text-sm text-blue-700">
                <p>Tu nueva contraseña debe tener al menos 8 caracteres. Una vez restablecida, serás redirigido automáticamente a tu panel de control.</p>
            </div>
        </div>
    </div>
</div>
@endsection 