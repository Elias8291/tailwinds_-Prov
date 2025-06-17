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
</script>
@endpush
@endsection 