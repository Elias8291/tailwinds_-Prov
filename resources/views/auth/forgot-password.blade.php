@extends('layouts.auth')

@section('title', 'Recuperar Contraseña - Padrón de Proveedores de Oaxaca')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="space-y-6">
    @csrf
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
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Recuperar Contraseña</h1>
        <p class="text-gray-600 text-sm leading-relaxed max-w-xs mx-auto">
            Ingresa tu correo electrónico y te enviaremos las instrucciones para restablecer tu contraseña
        </p>
    </div>

    <!-- Mensajes de estado -->
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Campo de correo -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
        <input type="email" id="email" name="email" required 
               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-300"
               placeholder="ejemplo@correo.com"
               value="{{ old('email') }}">
    </div>

    <!-- Botones de acción -->
    <div class="space-y-3">
        <button type="submit" class="group w-full bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 relative overflow-hidden text-sm">
            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span>ENVIAR INSTRUCCIONES</span>
            </div>
        </button>

        <a href="{{ route('login') }}" class="group w-full bg-white text-primary font-medium py-2.5 px-4 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 border border-primary/30 hover:border-primary/50 relative overflow-hidden inline-flex items-center justify-center text-sm">
            <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>VOLVER AL LOGIN</span>
            </div>
        </a>
    </div>
</form>
@endsection 