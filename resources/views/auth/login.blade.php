@extends('layouts.auth')

@section('title', 'Iniciar Sesión - Padrón de Proveedores del Estado de Oaxaca')

@push('styles')
<style>
    .success-checkmark {
        animation: scale-up 0.5s ease-in-out;
    }
    
    @keyframes scale-up {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }
    
    .modal-overlay {
        animation: fade-in 0.3s ease-out;
    }
    
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .modal-content {
        animation: slide-up 0.3s ease-out;
    }
    
    @keyframes slide-up {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@endpush

@section('content')
<form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-4">
    @csrf
    <!-- Header con Logo -->
    <div class="text-center mb-4">
        <div class="flex flex-col items-center justify-center mb-3">
            <div class="w-14 h-14 flex items-center justify-center mb-2 bg-gradient-to-br from-primary/10 to-primary-dark/10 rounded-full p-2">
                <img src="{{ asset('images/logoprin.jpg') }}" alt="Logo Estado de Oaxaca" class="w-full h-full object-contain rounded-full">
            </div>
            <div class="text-center space-y-1">
                <span class="text-primary font-bold text-sm block tracking-wide">ADMINISTRACIÓN</span>
                <span class="text-gray-500 text-xs font-medium uppercase tracking-wider">Gobierno del Estado de Oaxaca</span>
            </div>
        </div>
        
        <div class="space-y-1 mb-2">
            <h1 class="text-lg font-bold text-gray-800 leading-tight">
                Iniciar Sesión
            </h1>
            <h2 class="text-sm font-semibold text-primary leading-tight">
                Padrón de Proveedores del<br class="lg:hidden">
                <span class="text-primary-dark">Estado de Oaxaca</span>
            </h2>
        </div>
    </div>

    <!-- Mensajes de Estado -->
    @if(session('error') || $errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-lg mb-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                @if(session('error'))
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                @endif
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-700">{{ $error }}</p>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endif

    @if(session('verification_success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-lg mb-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('verification_success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-lg mb-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">{{ session('info') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('verification_required'))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-2 mb-3">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-2">
                <p class="text-sm text-blue-700 font-medium">
                    📧 Por favor verifica tu correo electrónico para continuar
                </p>
            </div>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-lg mb-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('session_expired'))
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded-lg mb-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">{{ session('session_expired') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Campos de inicio de sesión -->
    <div class="space-y-2">
        <div>
            <label for="rfc" class="block text-xs font-medium text-gray-700 mb-0.5">RFC</label>
            <div class="relative">
                <input type="text" id="rfc" name="rfc" required 
                       class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-300 uppercase @error('rfc') border-red-500 @enderror text-sm"
                       placeholder="Ej: XAXX010101000">
                @error('rfc')
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5">
                        <svg class="h-3.5 w-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                @enderror
            </div>
            @error('rfc')
                <p class="mt-0.5 text-xs text-red-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
            @if(session('rfc_exists'))
                <p class="mt-0.5 text-xs text-yellow-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    El RFC existe pero la contraseña es incorrecta
                </p>
            @endif
        </div>
        
        <div>
            <label for="password" class="block text-xs font-medium text-gray-700 mb-0.5">Contraseña</label>
            <div class="relative">
                <input type="password" id="password" name="password" required 
                       class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-300 @error('password') border-red-500 @enderror text-sm"
                       placeholder="••••••••">
                <button type="button" 
                        onclick="togglePassword('password')"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="password-toggle-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-0.5 text-xs text-red-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
            @if(session('invalid_credentials'))
                <p class="mt-0.5 text-xs text-red-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    La contraseña es incorrecta
                </p>
            @endif
        </div>

        <div class="flex items-center justify-between pt-1">
           
            <a href="{{ route('password.request') }}" class="text-xs text-primary hover:text-primary-dark transition-colors duration-300">
                ¿Olvidaste tu contraseña?
            </a>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="space-y-1.5 pt-2">
        <button type="submit" class="group w-full bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 relative overflow-hidden text-sm">
            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span>Iniciar Sesión</span>
                <div class="absolute -right-2 w-2 h-2 bg-white/30 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping"></div>
            </div>
        </button>

        <a href="/" class="group w-full bg-white hover:bg-gray-50 text-primary hover:text-primary-dark font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border-2 border-primary/20 hover:border-primary/40 relative overflow-hidden inline-flex items-center justify-center text-sm">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-primary-dark/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Volver</span>
            </div>
        </a>
    </div>
</form>

<!-- Modal de Éxito -->
@if(session('show_success_modal'))
<div id="successModal" class="fixed inset-0 z-50 overflow-y-auto modal-overlay" style="background-color: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md modal-content">
            <!-- Icono de éxito -->
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-10 w-10 text-green-500 success-checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    {{ session('modal_title') }}
                </h3>
                
                <p class="text-gray-600 mb-6">
                    {{ session('modal_message') }}
                </p>
                
                <button onclick="closeSuccessModal()" 
                        class="inline-flex justify-center items-center px-6 py-2.5 bg-primary hover:bg-primary-dark text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <span class="mr-2">Aceptar</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-toggle-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        // Ojo tachado (oculto)
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        `;
    } else {
        input.type = 'password';
        // Ojo abierto (visible)
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        `;
    }
}

function closeSuccessModal() {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Prevenir que se cierre el modal al hacer clic fuera
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                e.stopPropagation(); // Prevenir que se cierre al hacer clic fuera
            }
        });
    }
});
</script>
@endif
@endsection 