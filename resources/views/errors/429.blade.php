@extends('errors.layout')

@section('code', '429')
@section('title', 'Demasiadas Solicitudes')

@section('header-message')
Límite de solicitudes excedido <span class="sparkle">⏳</span>
@endsection

@section('content')
<p class="text-gray-700 text-sm sm:text-base leading-relaxed">
    Ha realizado demasiadas solicitudes en un corto período de tiempo. 
    <br><span class="highlight-text">Por favor aguarde</span> unos momentos antes de intentar nuevamente.
</p>

<div class="mt-4 text-xs sm:text-sm text-gray-600">
    <p>
        <span class="font-medium">¿Por qué sucede esto?</span>
    </p>
    <ul class="list-disc list-inside mt-2 space-y-1">
        <li>Protegemos nuestros servidores de sobrecarga</li>
        <li>Aseguramos un servicio estable para todos</li>
        <li>Es una medida de seguridad automática</li>
    </ul>
    
    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-blue-800 font-medium text-xs flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            Aguarde unos segundos antes de realizar una nueva solicitud
        </p>
    </div>
</div>
@endsection

@section('buttons')
<button onclick="setTimeout(() => window.location.reload(), 3000)" 
        class="btn-back inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span class="relative">Esperar 3 seg</span>
</button>

<button onclick="window.history.back()" 
        class="btn-back inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-primary bg-white border border-primary hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    <span class="relative">Regresar</span>
</button>
@endsection

@section('custom-styles')
@keyframes throttle {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-3px); }
    75% { transform: translateX(3px); }
}
.throttle-animation {
    animation: throttle 0.5s ease-in-out infinite;
}
@endsection 