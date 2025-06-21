@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
<style>
.profile-header {
    background-image: url('/images/logo.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    min-height: 200px;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(5px);
}

.profile-header-content {
    position: relative;
    z-index: 1;
}

/* Estilos adicionales para el carrusel */
#carouselContainer {
    min-height: 400px;
}

.carousel-card {
    min-height: 380px;
}

/* Animaciones suaves */
#carouselContainer {
    transition: transform 0.3s ease-in-out;
}

/* Responsivo para pantallas pequeñas */
@media (max-width: 768px) {
    #carouselContainer .flex-none {
        width: 100% !important;
        margin-right: 0 !important;
    }
    
    #historyControls {
        display: none !important;
    }
}

/* Estilos para botones de navegación */
.nav-button {
    transition: all 0.2s ease-in-out;
}

.nav-button:hover:not(:disabled) {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.nav-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Estilos para indicadores de estado */
.status-dot {
    position: relative;
    display: inline-block;
}

.status-dot::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(180, 50, 94, 0.7);
    }
    
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 10px rgba(180, 50, 94, 0);
    }
    
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(180, 50, 94, 0);
    }
}
</style>
@endpush

<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
        <!-- Main Container -->
        <div class="max-w-4xl mx-auto">
            <!-- Success Message -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 shadow-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2 text-xl"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- Error Message -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 border border-red-200 shadow-lg flex items-center justify-between">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- Profile Header Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6 border border-gray-100">
                <!-- Profile Info -->
                <div class="bg-white p-6">
                    <div class="flex items-center">
                        <!-- Avatar -->
                        <div class="w-20 h-20 border-4 border-gray-100 rounded-full bg-gray-50 flex items-center justify-center shadow-lg ring-4 ring-gray-50">
                            <span class="text-2xl font-bold text-gray-600">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        </div>
                        
                        <!-- User Info -->
                        <div class="ml-6 flex-1">
                            <div class="flex items-center space-x-2">
                                <h1 class="text-xl font-bold text-gray-800">{{ auth()->user()->name ?? 'Usuario' }}</h1>
                                <span class="bg-gray-100 rounded-full p-1" title="Usuario Verificado">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-600 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mt-1">{{ auth()->user()->email ?? 'email@ejemplo.com' }}</p>
                            <div class="mt-2 inline-flex items-center px-3 py-1 bg-gray-50 rounded-full">
                                <i class="fas fa-building text-gray-600 text-xs mr-2"></i>
                                <span class="text-xs text-gray-600">Padrón de Proveedores del Estado de Oaxaca</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2">
                    <!-- Personal Info Card -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center mb-4">
                            <i class="fas fa-user-circle mr-2 text-[#B4325E]"></i>
                            Información Personal
                        </h3>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex flex-col sm:flex-row border-b pb-2">
                                <span class="font-bold w-full sm:w-24 text-gray-600">Nombre:</span>
                                <span class="text-gray-800 break-words">{{ auth()->user()->name ?? 'N/A' }}</span>
                            </li>
                            <li class="flex flex-col sm:flex-row border-b pb-2">
                                <span class="font-bold w-full sm:w-24 text-gray-600">Email:</span>
                                <span class="text-gray-800 break-words">{{ auth()->user()->correo ?? auth()->user()->email ?? 'N/A' }}</span>
                            </li>
                            <li class="flex flex-col sm:flex-row border-b pb-2">
                                <span class="font-bold w-full sm:w-24 text-gray-600">RFC:</span>
                                <span class="text-gray-800 break-words">{{ auth()->user()->rfc ?? 'N/A' }}</span>
                            </li>
                            <li class="flex flex-col sm:flex-row border-b pb-2">
                                <span class="font-bold w-full sm:w-24 text-gray-600">Registro:</span>
                                <span class="text-gray-800">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d/m/Y') : 'N/A' }}</span>
                            </li>
                            <li class="flex flex-col sm:flex-row border-b pb-2">
                                <span class="font-bold w-full sm:w-24 text-gray-600">Último:</span>
                                <span class="text-gray-800 text-sm">{{ auth()->user()->ultimo_acceso ? auth()->user()->ultimo_acceso->format('d/m/Y H:i') : 'N/A' }}</span>
                            </li>
                            <li class="flex flex-col sm:flex-row border-b pb-2">
                                <span class="font-bold w-full sm:w-24 text-gray-600">Estado:</span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1 sm:mt-0 w-fit {{ auth()->user()->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(auth()->user()->estado ?? 'activo') }}
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- Statistics Section -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center mb-4">
                            <i class="fas fa-chart-bar mr-2 text-[#B4325E]"></i>
                            Estadísticas del Sistema
                        </h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Total Trámites -->
                            <div class="px-4 py-4 bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-bold text-sm text-indigo-600">Mis Trámites</span>
                                    <span class="text-xs bg-indigo-200 text-indigo-700 px-2 py-1 rounded-lg">Total</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center justify-center w-10 h-10 bg-indigo-500 bg-opacity-20 rounded-full">
                                        <i class="fas fa-file-alt text-indigo-600 text-lg"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-indigo-600">
                                            {{ auth()->user()->solicitante ? auth()->user()->solicitante->tramites()->count() : 0 }}
                                        </div>
                                        <div class="text-xs text-gray-500">Trámites realizados</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Documentos -->
                            <div class="px-4 py-4 bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-bold text-sm text-green-600">Documentos</span>
                                    <span class="text-xs bg-green-200 text-green-700 px-2 py-1 rounded-lg">Subidos</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center justify-center w-10 h-10 bg-green-500 bg-opacity-20 rounded-full">
                                        <i class="fas fa-folder text-green-600 text-lg"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-green-600">
                                            {{ auth()->user()->solicitante ? auth()->user()->solicitante->documentosSolicitante()->count() : 0 }}
                                        </div>
                                        <div class="text-xs text-gray-500">Archivos subidos</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Citas -->
                            <div class="px-4 py-4 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-bold text-sm text-blue-600">Citas</span>
                                    <span class="text-xs bg-blue-200 text-blue-700 px-2 py-1 rounded-lg">Programadas</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-500 bg-opacity-20 rounded-full">
                                        <i class="fas fa-calendar text-blue-600 text-lg"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-blue-600">
                                            {{ auth()->user()->citas()->count() }}
                                        </div>
                                        <div class="text-xs text-gray-500">Citas totales</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Security Card -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center mb-4">
                            <i class="fas fa-lock mr-2 text-[#B4325E]"></i>
                            Cambiar Contraseña
                        </h3>
                        <form id="passwordForm" action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div class="relative">
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="current_password" 
                                           class="w-full rounded-xl border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/20 pr-10">
                                    <button type="button" onclick="togglePassword('current_password')" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="current_password_toggle"></i>
                                    </button>
                                </div>
                                <div id="current_password_error" class="hidden text-red-500 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    <span>La contraseña actual es incorrecta</span>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" 
                                           class="w-full rounded-xl border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/20 pr-10"
                                           oninput="validatePasswordsInRealTime()">
                                    <button type="button" onclick="togglePassword('password')" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="password_toggle"></i>
                                    </button>
                                </div>
                                <div id="password_error" class="hidden text-red-500 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    <span id="password_error_text"></span>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="w-full rounded-xl border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/20 pr-10"
                                           oninput="validatePasswordsInRealTime()">
                                    <button type="button" onclick="togglePassword('password_confirmation')" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="password_confirmation_toggle"></i>
                                    </button>
                                </div>
                                <div id="password_success" class="hidden text-green-500 text-sm mt-1 flex items-center">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    <span>Las contraseñas coinciden</span>
                                </div>
                            </div>

                            <button type="button" onclick="confirmPasswordChange()" 
                                    class="w-full bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white px-4 py-3 rounded-xl shadow-lg hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300 transform hover:scale-105 focus:ring-2 focus:ring-[#B4325E]/20 font-semibold">
                                <i class="fas fa-key mr-2"></i>
                                Actualizar Contraseña
                            </button>
                        </form>
                    </div>

                    <!-- Success Modal -->
                    <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
                        <div class="relative top-4 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
                            <div class="mt-3 text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                    <i class="fas fa-check text-green-600 text-xl"></i>
                                </div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">¡Éxito!</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500">
                                        Tu contraseña ha sido actualizada correctamente.
                                    </p>
                                </div>
                                <div class="items-center px-4 py-3">
                                    <button onclick="closeSuccessModal()" 
                                            class="px-4 py-2 bg-[#B4325E] text-white text-base font-medium rounded-xl shadow-sm hover:bg-[#93264B] focus:outline-none focus:ring-2 focus:ring-[#B4325E]/20">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation Modal -->
                    <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
                        <div class="relative top-4 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
                            <div class="mt-3 text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                                </div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirmar Cambio</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500">
                                        ¿Estás seguro que deseas cambiar tu contraseña?
                                    </p>
                                </div>
                                <div class="items-center px-4 py-3 flex justify-center space-x-4">
                                    <button onclick="closeConfirmationModal()" 
                                            class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-xl shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400/20">
                                        Cancelar
                                    </button>
                                    <button onclick="submitPasswordChange()" 
                                            class="px-4 py-2 bg-[#B4325E] text-white text-base font-medium rounded-xl shadow-sm hover:bg-[#93264B] focus:outline-none focus:ring-2 focus:ring-[#B4325E]/20">
                                        Confirmar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Modal -->
                    <div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
                        <div class="relative top-4 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
                            <div class="mt-3 text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                                </div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Error</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500" id="errorModalMessage">
                                        Ha ocurrido un error al cambiar la contraseña.
                                    </p>
                                </div>
                                <div class="items-center px-4 py-3">
                                    <button onclick="closeErrorModal()" 
                                            class="px-4 py-2 bg-[#B4325E] text-white text-base font-medium rounded-xl shadow-sm hover:bg-[#93264B] focus:outline-none focus:ring-2 focus:ring-[#B4325E]/20">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="bg-white rounded-2xl shadow-lg p-4 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-900 flex items-center mb-3">
                            <i class="fas fa-history mr-2 text-[#B4325E]"></i>
                            Actividad Reciente
                        </h3>
                        <div class="relative">
                            <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            
                            <!-- Timeline Items -->
                            <div class="space-y-2">
                                <div class="flex items-start space-x-2">
                                    <div class="w-5 h-5 bg-[#B4325E] rounded-full flex items-center justify-center z-10 shadow-lg">
                                        <i class="fas fa-user text-white text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-900">Perfil actualizado</p>
                                        <p class="text-[10px] text-gray-500">Hace 2 minutos</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-2">
                                    <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center z-10 shadow-lg">
                                        <i class="fas fa-file text-white text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-900">Nuevo documento subido</p>
                                        <p class="text-[10px] text-gray-500">Hace 1 hora</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-2">
                                    <div class="w-5 h-5 bg-green-600 rounded-full flex items-center justify-center z-10 shadow-lg">
                                        <i class="fas fa-check text-white text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-900">Trámite aprobado</p>
                                        <p class="text-[10px] text-gray-500">Hace 2 días</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-2">
                                    <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center z-10 shadow-lg">
                                        <i class="fas fa-calendar text-white text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-900">Cita programada</p>
                                        <p class="text-[10px] text-gray-500">Hace 1 semana</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial del RFC como Proveedor Section - Solo mostrar si hay registros -->
        @if(auth()->user()->rfc)
            @php
                $rfc = auth()->user()->rfc;
                $solicitantes = \App\Models\Solicitante::where('rfc', $rfc)->get();
                $totalProveedores = 0;
                foreach($solicitantes as $solicitante) {
                    $totalProveedores += \App\Models\Proveedor::where('solicitante_id', $solicitante->id)->count();
                }
            @endphp
            
            @if($totalProveedores > 0)
                <div class="max-w-4xl mx-auto mt-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="p-2 bg-[#B4325E]/10 rounded-lg mr-3">
                                    <svg class="w-6 h-6 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                Historial del RFC como Proveedor
                            </h3>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">RFC:</span>
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $rfc }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">Registros:</span>
                                    <span class="bg-[#B4325E] text-white px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $totalProveedores }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-[#B4325E] mr-3"></div>
                                <span class="text-sm text-gray-600">Cargando historial del RFC...</span>
                            </div>
                        </div>

                        <!-- Controles del Carrusel -->
                        <div class="mb-6 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <h4 class="text-lg font-semibold text-gray-800">Todos los Registros PV</h4>
                                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                    <i class="fas fa-arrow-left mr-1"></i>
                                    <i class="fas fa-arrow-right mr-1"></i>
                                    Navega con las flechas
                                </span>
                            </div>
                            <div id="historyControls" class="flex items-center space-x-3">
                                <button id="prevBtn" onclick="navigateCarousel('prev')" class="nav-button p-3 bg-white border border-gray-300 hover:bg-gray-50 rounded-full shadow-sm transition-all duration-200" disabled>
                                    <i class="fas fa-chevron-left text-gray-600"></i>
                                </button>
                                <div class="flex items-center space-x-2 bg-white px-4 py-2 rounded-full border border-gray-200 shadow-sm">
                                    <span id="carouselIndicator" class="text-sm font-medium text-gray-700">1 / 1</span>
                                    <i class="fas fa-history text-gray-400 text-xs"></i>
                                </div>
                                <button id="nextBtn" onclick="navigateCarousel('next')" class="nav-button p-3 bg-white border border-gray-300 hover:bg-gray-50 rounded-full shadow-sm transition-all duration-200" disabled>
                                    <i class="fas fa-chevron-right text-gray-600"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Carrusel de Todos los PV -->
                        <div class="relative">
                            <div class="overflow-hidden">
                                <div id="carouselContainer" class="flex transition-transform duration-300 ease-in-out">
                                    <!-- Los cards se cargarán aquí dinámicamente -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById(inputId + '_toggle');
    
    if (input.type === 'password') {
        input.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

function validatePasswordsInRealTime() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const errorDiv = document.getElementById('password_error');
    const successDiv = document.getElementById('password_success');
    const errorText = document.getElementById('password_error_text');
    
    // Si ambos campos están vacíos, ocultar mensajes
    if (!password && !confirmation) {
        errorDiv.classList.add('hidden');
        successDiv.classList.add('hidden');
        return;
    }
    
    // Si solo uno de los campos está lleno, mostrar mensaje de espera
    if ((password && !confirmation) || (!password && confirmation)) {
        errorDiv.classList.add('hidden');
        successDiv.classList.add('hidden');
        return;
    }
    
    // Si ambos campos tienen contenido, validar
    if (password && confirmation) {
        if (password !== confirmation) {
            errorText.textContent = 'Las contraseñas no coinciden';
            errorDiv.classList.remove('hidden');
            successDiv.classList.add('hidden');
        } else {
            errorDiv.classList.add('hidden');
            successDiv.classList.remove('hidden');
        }
    }
}

function confirmPasswordChange() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    
    if (password !== confirmation) {
        return;
    }
    
    document.getElementById('confirmationModal').classList.remove('hidden');
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').classList.add('hidden');
}

function submitPasswordChange() {
    document.getElementById('passwordForm').submit();
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}

// Mostrar modal de éxito si hay mensaje de estado
@if(session('status'))
    document.getElementById('successModal').classList.remove('hidden');
@endif

// Mostrar error de contraseña actual incorrecta
@if($errors->has('current_password'))
    document.getElementById('errorModal').classList.remove('hidden');
    document.getElementById('errorModalMessage').textContent = 'La contraseña actual es incorrecta';
@endif

// Variables globales para el carrusel
let currentIndex = 0;
let historialProveedoresData = [];

// Cargar historial del RFC como proveedor al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    @if(auth()->user()->rfc)
        @php
            $rfc = auth()->user()->rfc;
            $solicitantes = \App\Models\Solicitante::where('rfc', $rfc)->get();
            $totalProveedores = 0;
            foreach($solicitantes as $solicitante) {
                $totalProveedores += \App\Models\Proveedor::where('solicitante_id', $solicitante->id)->count();
            }
        @endphp
        @if($totalProveedores > 0)
            loadRfcProveedorHistoryFromDatabase();
        @endif
    @endif
});

// Función para cargar el historial del RFC desde la base de datos local
function loadRfcProveedorHistoryFromDatabase() {
    const loadingDiv = document.querySelector('.bg-blue-50');
    
    // Obtener los datos del historial desde el backend (renderizados en el HTML)
    historialProveedoresData = [
        @if(auth()->user()->rfc)
            @php
                // Buscar todos los solicitantes con este RFC
                $rfc = auth()->user()->rfc;
                $solicitantes = \App\Models\Solicitante::where('rfc', $rfc)->get();
                $historialProveedores = collect();
                
                foreach($solicitantes as $solicitante) {
                    $proveedores = \App\Models\Proveedor::where('solicitante_id', $solicitante->id)
                        ->orderBy('fecha_registro', 'desc')
                        ->get();
                    $historialProveedores = $historialProveedores->merge($proveedores);
                }
                
                // Ordenar por fecha de registro descendente
                $historialProveedores = $historialProveedores->sortByDesc('fecha_registro');
            @endphp
            
            @if($historialProveedores->count() > 0)
                @foreach($historialProveedores as $proveedor)
                {
                    pv: '{{ $proveedor->pv }}',
                    fecha_registro: '{{ $proveedor->fecha_registro }}',
                    fecha_vencimiento: '{{ $proveedor->fecha_vencimiento }}',
                    estado: '{{ $proveedor->estado }}',
                    observaciones: '{{ $proveedor->observaciones ?? "" }}',
                    solicitante_nombre: '{{ $proveedor->solicitante->nombre_completo ?? $proveedor->solicitante->razon_social ?? "Sin nombre" }}',
                    tipo_persona: '{{ $proveedor->solicitante->tipo_persona }}',
                    rfc: '{{ $proveedor->solicitante->rfc }}',
                    created_at: '{{ $proveedor->created_at }}',
                    updated_at: '{{ $proveedor->updated_at }}'
                }@if(!$loop->last),@endif
                @endforeach
            @endif
        @endif
    ];
    
    if (loadingDiv) {
        loadingDiv.style.display = 'none';
    }
    
    // Cargar contenido en el carrusel
    loadCarouselView();
    updateCarouselControls();
}

// Función para generar el HTML de un card de proveedor para el carrusel
function generateProveedorCard(proveedor) {
    const fechaRegistro = proveedor.fecha_registro ? new Date(proveedor.fecha_registro) : null;
    const fechaVencimiento = proveedor.fecha_vencimiento ? new Date(proveedor.fecha_vencimiento) : null;
    const tiempoRestante = calcularTiempoRestanteProveedor(proveedor.fecha_vencimiento);
    const estadoInfo = determinarEstadoProveedorYClases(proveedor.estado || (tiempoRestante?.vencido ? 'Inactivo' : 'Activo'));
    
    let tipoInscripcion = 'Inscripción como Proveedor';
    if (fechaRegistro) {
        const añoRegistro = fechaRegistro.getFullYear();
        tipoInscripcion = `Inscripción ${añoRegistro}`;
    }

    return `
        <div class="flex-none w-80 mr-6 card-animation">
            <div class="bg-white rounded-xl border ${estadoInfo.border} shadow-lg carousel-card ${estadoInfo.hover} h-full">
                <!-- Header del card -->
                <div class="p-4 gradient-header rounded-t-xl border-b">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 ${estadoInfo.dot} rounded-full status-dot"></div>
                            <span class="font-bold text-gray-800">PV: ${proveedor.pv}</span>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full ${estadoInfo.badge}">
                            ${proveedor.estado || 'Sin estado'}
                        </span>
                    </div>
                </div>
                
                <!-- Contenido del card -->
                <div class="p-4 space-y-4">
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg mb-2">${tipoInscripcion}</h4>
                        <p class="text-sm text-gray-600">
                            RFC ${proveedor.rfc} - ${proveedor.tipo_persona === 'Física' ? 'Persona Física' : 'Persona Moral'}
                        </p>
                        <p class="text-sm text-gray-800 font-medium mt-1">
                            ${proveedor.solicitante_nombre}
                        </p>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Fecha de registro:</span>
                            <span class="font-medium">${fechaRegistro ? fechaRegistro.toLocaleDateString('es-MX') : 'N/A'}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Vencimiento:</span>
                            <span class="font-medium">${fechaVencimiento ? fechaVencimiento.toLocaleDateString('es-MX') : 'N/A'}</span>
                        </div>
                    </div>
                    
                    ${tiempoRestante ? `
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <p class="text-xs font-medium text-gray-700 mb-1">Estado de vigencia:</p>
                            <p class="text-sm ${tiempoRestante.clase} font-medium">
                                ${tiempoRestante.texto}
                            </p>
                        </div>
                    ` : ''}
                    
                    ${proveedor.observaciones ? `
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <p class="text-xs font-medium text-gray-700 mb-1">Observaciones:</p>
                            <p class="text-xs text-gray-600">${proveedor.observaciones}</p>
                        </div>
                    ` : ''}
                    
                    <button onclick="showProveedorInscripcionDetails('${proveedor.pv}')" 
                            class="w-full bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] text-white py-2 px-4 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                        <i class="fas fa-eye mr-2"></i>
                        Ver Detalles Completos
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Función para cargar el carrusel de proveedores
function loadCarouselView() {
    const carouselContainer = document.getElementById('carouselContainer');
    
    if (historialProveedoresData.length > 0) {
        carouselContainer.innerHTML = historialProveedoresData.map(proveedor => 
            generateProveedorCard(proveedor)
        ).join('');
    } else {
        carouselContainer.innerHTML = `
            <div class="flex-none w-full">
                <div class="bg-white rounded-xl border border-gray-200 shadow-lg p-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-history text-gray-400 text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-medium text-gray-900 mb-2">Sin registros PV</h4>
                    <p class="text-gray-500 mb-4">
                        Este RFC ({{ auth()->user()->rfc }}) no tiene registros como proveedor en el sistema.
                    </p>
                    <p class="text-sm text-gray-400">
                        Si consideras que esto es un error, contacta al administrador del sistema.
                    </p>
                </div>
            </div>
        `;
    }
}

// Funciones de navegación del carrusel
function navigateCarousel(direction) {
    const container = document.getElementById('carouselContainer');
    const cardWidth = 320; // 80 (w-80) * 4 (rem base) = 320px + margin
    
    if (direction === 'next' && currentIndex < historialProveedoresData.length - 1) {
        currentIndex++;
    } else if (direction === 'prev' && currentIndex > 0) {
        currentIndex--;
    }
    
    const translateX = -currentIndex * cardWidth;
    container.style.transform = `translateX(${translateX}px)`;
    updateCarouselControls();
}

// Actualizar controles del carrusel
function updateCarouselControls() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const indicator = document.getElementById('carouselIndicator');
    
    if (historialProveedoresData.length === 0) {
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        indicator.textContent = '0 / 0';
        return;
    }
    
    prevBtn.disabled = currentIndex === 0;
    nextBtn.disabled = currentIndex === historialProveedoresData.length - 1;
    
    indicator.textContent = `${currentIndex + 1} / ${historialProveedoresData.length}`;
    
    // Actualizar estilos de los botones
    prevBtn.className = `p-2 rounded-full transition-colors duration-200 ${
        prevBtn.disabled 
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
            : 'bg-gray-100 hover:bg-gray-200 text-gray-600 cursor-pointer'
    }`;
    
    nextBtn.className = `p-2 rounded-full transition-colors duration-200 ${
        nextBtn.disabled 
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
            : 'bg-gray-100 hover:bg-gray-200 text-gray-600 cursor-pointer'
    }`;
}



function calcularTiempoRestanteProveedor(fechaVencimiento) {
    if (!fechaVencimiento) return null;

    const ahora = new Date();
    const vencimiento = new Date(fechaVencimiento);
    const diferencia = vencimiento - ahora;

    // Si ya venció
    if (diferencia < 0) {
        const tiempoVencido = Math.abs(diferencia);
        const diasVencidos = Math.floor(tiempoVencido / (1000 * 60 * 60 * 24));
        return {
            texto: `Vencido hace ${diasVencidos} días`,
            clase: 'text-red-600',
            vencido: true,
            detalle: `Fecha de vencimiento: ${vencimiento.toLocaleDateString('es-MX', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })}`
        };
    }

    // Calcular tiempo restante
    const milisegundosEnHora = 1000 * 60 * 60;
    const milisegundosEnDia = milisegundosEnHora * 24;
    const milisegundosEnMes = milisegundosEnDia * 30.44;

    const mesesTotales = Math.floor(diferencia / milisegundosEnMes);
    const restoDespuesMeses = diferencia % milisegundosEnMes;
    const diasTotales = Math.floor(restoDespuesMeses / milisegundosEnDia);
    const restoDespuesDias = restoDespuesMeses % milisegundosEnDia;
    const horasTotales = Math.floor(restoDespuesDias / milisegundosEnHora);

    // Determinar clase de color
    let clase = '';
    if (mesesTotales > 3) {
        clase = 'text-green-600';
    } else if (mesesTotales > 0 || diasTotales > 15) {
        clase = 'text-yellow-600';
    } else {
        clase = 'text-red-600';
    }

    const partesMensaje = [];
    partesMensaje.push(`${mesesTotales} ${mesesTotales === 1 ? 'mes' : 'meses'}`);
    partesMensaje.push(`${diasTotales} ${diasTotales === 1 ? 'día' : 'días'}`);
    partesMensaje.push(`${horasTotales} ${horasTotales === 1 ? 'hora' : 'horas'}`);

    return {
        texto: `Tiempo para vencer: ${partesMensaje.join(', ')}`,
        clase,
        vencido: false,
        detalle: `Fecha de vencimiento: ${vencimiento.toLocaleDateString('es-MX', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })} a las ${vencimiento.toLocaleTimeString('es-MX', {
            hour: '2-digit',
            minute: '2-digit'
        })}`
    };
}

function determinarEstadoProveedorYClases(estado) {
    const clases = {
        'Activo': {
            dot: 'bg-green-500',
            badge: 'bg-green-100 text-green-800',
            border: 'border-green-200',
            hover: 'hover:border-green-300',
            textColor: 'text-green-600'
        },
        'Vigente': {
            dot: 'bg-green-500',
            badge: 'bg-green-100 text-green-800',
            border: 'border-green-200',
            hover: 'hover:border-green-300',
            textColor: 'text-green-600'
        },
        'Pendiente Renovación': {
            dot: 'bg-yellow-500',
            badge: 'bg-yellow-100 text-yellow-800',
            border: 'border-yellow-200',
            hover: 'hover:border-yellow-300',
            textColor: 'text-yellow-600'
        },
        'Por Vencer': {
            dot: 'bg-yellow-500',
            badge: 'bg-yellow-100 text-yellow-800',
            border: 'border-yellow-200',
            hover: 'hover:border-yellow-300',
            textColor: 'text-yellow-600'
        },
        'Vencido': {
            dot: 'bg-red-500',
            badge: 'bg-red-100 text-red-800',
            border: 'border-red-200',
            hover: 'hover:border-red-300',
            textColor: 'text-red-600'
        },
        'Inactivo': {
            dot: 'bg-red-500',
            badge: 'bg-red-100 text-red-800',
            border: 'border-red-200',
            hover: 'hover:border-red-300',
            textColor: 'text-red-600'
        },
        'Suspendido': {
            dot: 'bg-red-500',
            badge: 'bg-red-100 text-red-800',
            border: 'border-red-200',
            hover: 'hover:border-red-300',
            textColor: 'text-red-600'
        }
    };

    return clases[estado] || clases['Inactivo'];
}

// Función para mostrar detalles de una inscripción específica como proveedor
function showProveedorInscripcionDetails(pv) {
    console.log('Mostrar detalles del proveedor PV:', pv);
    // Redirigir a la página de detalles del proveedor
    window.location.href = `/proveedores/${pv}`;
}
</script>
@endpush
@endsection 