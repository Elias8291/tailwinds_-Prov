@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-6 font-montserrat">
    <div class="flex flex-col lg:flex-row bg-white rounded-lg shadow-md overflow-hidden border border-gray-200/50 backdrop-blur-sm">
        <div class="flex-1 p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="text-sm text-gray-500 font-medium flex items-center gap-2">
                    <span id="currentTime" class="text-lg font-semibold text-gray-700">10:29 am</span>
                    <span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs">Sesión Activa</span>
                </div>
            </div>
            
            <h2 id="greeting" class="text-2xl font-semibold text-[#9d2449] mb-2">
                Buenos días, {{ auth()->check() ? auth()->user()->name : 'Invitado' }}
            </h2>
            <p class="text-gray-600 mb-2">¿Cómo va tu día? 🌟</p>
            <p class="text-gray-700 mb-6">Bienvenido al Padron de Proveedores del Estado De Oaxaca.</p>

            <div>
                <button class="inline-flex items-center bg-[#9d2449] text-white px-4 py-2 rounded-md shadow-sm hover:bg-[#7a1c38] transition-all">
                    <span>Iniciar trámite</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-semibold text-[#9d2449] mb-6">Descubre Proveedores de Oaxaca</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-gray-200/50 rounded-xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-14 h-14 bg-[#9d2449]/10 rounded-full flex items-center justify-center mb-4 group-hover:bg-[#9d2449]/20 transition-all duration-300">
                                <i class="fas fa-file-upload text-[#9d2449] text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <h4 class="text-base font-semibold text-[#9d2449] mb-2">Subir Documentos</h4>
                            <p class="text-sm text-gray-500">Carga tus documentos oficiales</p>
                        </div>
                    </div>
                    
                    <div class="bg-white border border-gray-200/50 rounded-xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-14 h-14 bg-[#9d2449]/10 rounded-full flex items-center justify-center mb-4 group-hover:bg-[#9d2449]/20 transition-all duration-300">
                                <i class="fas fa-tasks text-[#9d2449] text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <h4 class="text-base font-semibold text-[#9d2449] mb-2">Estado de Registro</h4>
                            <p class="text-sm text-gray-500">Consulta tu proceso</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200/50 rounded-xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-14 h-14 bg-[#9d2449]/10 rounded-full flex items-center justify-center mb-4 group-hover:bg-[#9d2449]/20 transition-all duration-300">
                                <i class="fas fa-users text-[#9d2449] text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <h4 class="text-base font-semibold text-[#9d2449] mb-2">Directorio</h4>
                            <p class="text-sm text-gray-500">Encuentra proveedores locales</p>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200/50 rounded-xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-14 h-14 bg-[#9d2449]/10 rounded-full flex items-center justify-center mb-4 group-hover:bg-[#9d2449]/20 transition-all duration-300">
                                <i class="fas fa-headset text-[#9d2449] text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            <h4 class="text-base font-semibold text-[#9d2449] mb-2">Ayuda</h4>
                            <p class="text-sm text-gray-500">Asistencia con tu registro</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden lg:block w-2/5 bg-[#fff5f8]/90 p-6 flex items-center justify-center">
            <img src="{{ asset('images/mujer_bienvenida.png') }}" alt="Asistente" class="w-3/4 h-auto max-h-[400px] object-contain">
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'pm' : 'am';
            const formattedHours = hours % 12 || 12;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;

            document.getElementById('currentTime').textContent = `${formattedHours}:${formattedMinutes} ${ampm}`;

            const greeting = document.getElementById('greeting');
            const userName = '{{ auth()->check() ? auth()->user()->name : 'Invitado' }}';

            if (hours < 12) {
                greeting.textContent = `Buenos días, ${userName}`;
            } else if (hours >= 12 && hours < 19) {
                greeting.textContent = `Buenas tardes, ${userName}`;
            } else {
                greeting.textContent = `Buenas noches, ${userName}`;
            }
        }

        updateTime();
        setInterval(updateTime, 60000);
    });
</script>
@endsection