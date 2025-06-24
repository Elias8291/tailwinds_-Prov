@extends('layouts.app')

@section('title', 'Agendar Cita Presencial')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-4 sm:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-2xl shadow-lg mb-4">
                <i class="fas fa-calendar-check text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Agendar Cita Presencial</h1>
            <p class="text-lg text-gray-600">Para cotejo de documentos originales</p>
        </div>

        <!-- Success Banner -->
        <div class="mb-8 p-4 sm:p-6 bg-gradient-to-r from-emerald-50 to-green-50 rounded-2xl border-l-4 border-emerald-500 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-emerald-800 mb-1">¡Documentación Aprobada!</h3>
                    <p class="text-emerald-700">Su trámite #{{ $tramite->id }} ha sido revisado y aprobado. Complete el proceso agendando su cita presencial.</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <form id="appointmentForm" action="{{ route('citas.store.agendar') }}" method="POST" class="space-y-0">
                @csrf
                <input type="hidden" name="motivo" value="Cotejo presencial de documentos - Trámite #{{ $tramite->id }}">
                <input type="hidden" name="tramite_id" value="{{ $tramite->id }}">
                
                <!-- Calendar Section -->
                <div class="p-6 sm:p-8 border-b border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-calendar-alt mr-3 text-[#9d2449]"></i>
                        Seleccionar Fecha y Hora
                    </h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                        <!-- Date Selection -->
                        <div class="space-y-4">
                            <div>
                                <label for="appointment_date" class="block text-sm font-semibold text-gray-700 mb-3">
                                    Fecha de la Cita <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                    </div>
                                    <input type="date" 
                                           name="appointment_date" 
                                           id="appointment_date" 
                                           required
                                           min="{{ now()->addDay()->format('Y-m-d') }}"
                                           class="w-full pl-12 pr-4 py-3 sm:py-4 bg-white border-2 border-gray-300 rounded-xl text-gray-800 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 focus:outline-none transition-all duration-300 text-base sm:text-lg font-medium">
                                </div>
                            </div>
                            
                            <!-- Date Info -->
                            <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">Horarios disponibles</p>
                                        <p class="text-xs text-blue-700">Lunes a Viernes, 9:00 AM - 5:00 PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Time Selection -->
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Horario Disponible <span class="text-red-500">*</span>
                            </label>
                            
                            <!-- Morning Slots -->
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-600 mb-3">Horario Matutino</h4>
                                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                    @foreach(['09:00', '10:00', '11:00', '12:00'] as $time)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="appointment_time" value="{{ $time }}" required class="sr-only peer">
                                        <div class="p-3 sm:p-4 border-2 border-gray-300 rounded-xl text-center transition-all duration-200 hover:border-[#9d2449] hover:bg-[#9d2449]/5 peer-checked:border-[#9d2449] peer-checked:bg-[#9d2449] peer-checked:text-white peer-checked:shadow-lg group">
                                            <span class="font-semibold text-sm sm:text-base">{{ $time }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Afternoon Slots -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-600 mb-3">Horario Vespertino</h4>
                                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                    @foreach(['14:00', '15:00', '16:00', '17:00'] as $time)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="appointment_time" value="{{ $time }}" required class="sr-only peer">
                                        <div class="p-3 sm:p-4 border-2 border-gray-300 rounded-xl text-center transition-all duration-200 hover:border-[#9d2449] hover:bg-[#9d2449]/5 peer-checked:border-[#9d2449] peer-checked:bg-[#9d2449] peer-checked:text-white peer-checked:shadow-lg group">
                                            <span class="font-semibold text-sm sm:text-base">{{ $time }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Information Section -->
                <div class="p-6 sm:p-8 border-b border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-info-circle mr-3 text-[#9d2449]"></i>
                        Información Importante
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Important Info -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalles de la Cita</h3>
                            <div class="space-y-3">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-file-alt text-blue-600 mt-1 text-sm"></i>
                                    <p class="text-sm text-gray-700">Debe traer <strong>documentos originales</strong> para cotejo y verificación</p>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-id-card text-blue-600 mt-1 text-sm"></i>
                                    <p class="text-sm text-gray-700">Identificación oficial vigente (INE, Pasaporte o Cédula)</p>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-clock text-blue-600 mt-1 text-sm"></i>
                                    <p class="text-sm text-gray-700">Llegue <strong>15 minutos antes</strong> de su cita</p>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-map-marker-alt text-blue-600 mt-1 text-sm"></i>
                                    <p class="text-sm text-gray-700">Oficinas centrales - Av. Principal #123, Col. Centro</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Required Documents -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Documentos Requeridos</h3>
                            <div class="space-y-3">
                                @if($tramite->solicitante && $tramite->solicitante->tipo_persona === 'Moral')
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                        <span class="text-sm text-gray-700">Acta Constitutiva Original</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                        <span class="text-sm text-gray-700">Poder Notarial del Representante</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                        <span class="text-sm text-gray-700">RFC de la Empresa</span>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                        <span class="text-sm text-gray-700">Acta de Nacimiento Original</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                        <span class="text-sm text-gray-700">CURP Original</span>
                                    </div>
                                @endif
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                    <span class="text-sm text-gray-700">Comprobante de Domicilio (máximo 3 meses)</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                    <span class="text-sm text-gray-700">Identificación Oficial Vigente</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notes Section -->
                <div class="p-6 sm:p-8 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notas Adicionales (Opcional)</h3>
                    <textarea name="notas" 
                              id="appointment_notes" 
                              rows="4"
                              placeholder="Agregue cualquier comentario o solicitud especial..."
                              class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-xl text-gray-800 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 focus:outline-none transition-all duration-300 resize-none"></textarea>
                </div>
                
                <!-- Action Buttons -->
                <div class="p-6 sm:p-8 bg-gray-50">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('tramites.solicitante.estado', $tramite->id) }}" 
                           class="flex-1 px-6 py-4 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-semibold text-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver al Estado
                        </a>
                        <button type="submit"
                                class="flex-1 px-6 py-4 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white rounded-xl hover:from-[#8a203f] hover:to-[#9d2449] transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Confirmar Cita
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-auto p-8 text-center">
        <div class="w-16 h-16 bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-full mx-auto mb-4 flex items-center justify-center">
            <i class="fas fa-spinner fa-spin text-white text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Procesando Cita</h3>
        <p class="text-gray-600">Por favor espere...</p>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-8 text-center">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-check text-white text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white">¡Cita Agendada!</h3>
        </div>
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-emerald-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-calendar-check text-emerald-600 text-2xl"></i>
            </div>
            <h4 class="text-xl font-bold text-gray-900 mb-2">Cita Confirmada</h4>
            <p class="text-gray-600 mb-4">Su cita para cotejo presencial ha sido agendada exitosamente.</p>
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Recibirá un correo de confirmación con todos los detalles.
                </p>
            </div>
            <button onclick="closeSuccessModal()" 
                    class="w-full px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors duration-200 font-semibold">
                <i class="fas fa-check mr-2"></i>
                Entendido
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const appointmentForm = document.getElementById('appointmentForm');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const successModal = document.getElementById('successModal');
    
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const date = formData.get('appointment_date');
            const time = formData.get('appointment_time');
            
            if (!date || !time) {
                alert('Por favor seleccione una fecha y horario para la cita');
                return;
            }
            
            // Show loading
            loadingOverlay.classList.remove('hidden');
            
            // Combine date and time for datetime field
            const datetime = date + ' ' + time + ':00';
            formData.set('fecha_hora', datetime);
            formData.delete('appointment_date');
            formData.delete('appointment_time');
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success !== false) {
                    // Hide loading and show success
                    loadingOverlay.classList.add('hidden');
                    successModal.classList.remove('hidden');
                } else {
                    throw new Error(data.message || 'Error al agendar la cita');
                }
                
            } catch (error) {
                console.error('Error:', error);
                loadingOverlay.classList.add('hidden');
                alert('Error al agendar la cita: ' + error.message);
            }
        });
    }
});

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    // Redirect back to status page
    window.location.href = "{{ route('tramites.solicitante.estado', $tramite->id) }}";
}
</script>
@endsection 