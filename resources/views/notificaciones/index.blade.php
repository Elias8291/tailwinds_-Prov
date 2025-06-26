@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Notificaciones</h1>
                        <p class="mt-2 text-sm text-gray-600">Gestiona todas tus notificaciones desde aquí</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="marcarTodasComoLeidas()" 
                                class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Marcar todas como leídas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-4 bg-white border-b border-gray-200">
                <div class="flex flex-wrap gap-4">
                    <button onclick="filtrarPor('todas')" 
                            class="filtro-btn px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-primary text-white">
                        Todas
                    </button>
                    <button onclick="filtrarPor('no-leidas')" 
                            class="filtro-btn px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300">
                        No leídas
                    </button>
                    <button onclick="filtrarPor('Informativo')" 
                            class="filtro-btn px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Informativas
                    </button>
                    <button onclick="filtrarPor('Advertencia')" 
                            class="filtro-btn px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Advertencias
                    </button>
                    <button onclick="filtrarPor('Error')" 
                            class="filtro-btn px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Errores
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de notificaciones -->
        <div id="notificaciones-container">
            <!-- Se cargarán via JavaScript -->
        </div>

        <!-- Estado de carga -->
        <div id="loading" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-8 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
                <p class="mt-4 text-gray-600">Cargando notificaciones...</p>
            </div>
        </div>

        <!-- Sin notificaciones -->
        <div id="sin-notificaciones" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hidden">
            <div class="p-8 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No hay notificaciones</h3>
                <p class="mt-2 text-gray-500">No tienes notificaciones en este momento.</p>
            </div>
        </div>
    </div>
</div>

<script>
let notificaciones = [];
let filtroActual = 'todas';

document.addEventListener('DOMContentLoaded', function() {
    cargarNotificaciones();
});

async function cargarNotificaciones() {
    try {
        const response = await fetch('{{ route("notificaciones.header") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const data = await response.json();
            notificaciones = data.notificaciones;
            mostrarNotificaciones();
        }
    } catch (error) {
        console.error('Error al cargar notificaciones:', error);
    } finally {
        document.getElementById('loading').style.display = 'none';
    }
}

function mostrarNotificaciones() {
    const container = document.getElementById('notificaciones-container');
    const sinNotificaciones = document.getElementById('sin-notificaciones');
    
    let notificacionesFiltradas = filtrarNotificaciones();
    
    if (notificacionesFiltradas.length === 0) {
        container.innerHTML = '';
        sinNotificaciones.classList.remove('hidden');
        return;
    }
    
    sinNotificaciones.classList.add('hidden');
    
    let html = '';
    notificacionesFiltradas.forEach(notificacion => {
        const colorClases = {
            'blue': 'bg-blue-50 border-blue-200',
            'yellow': 'bg-yellow-50 border-yellow-200',
            'red': 'bg-red-50 border-red-200',
            'gray': 'bg-gray-50 border-gray-200'
        };
        
        const iconoColorClases = {
            'blue': 'bg-blue-100 text-blue-600',
            'yellow': 'bg-yellow-100 text-yellow-600',
            'red': 'bg-red-100 text-red-600',
            'gray': 'bg-gray-100 text-gray-600'
        };
        
        const iconos = {
            'Informativo': 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'Advertencia': 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z',
            'Error': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
        };
        
        html += `
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 ${!notificacion.leida ? 'border-l-4 border-primary' : ''}">
                <div class="p-6 ${!notificacion.leida ? colorClases[notificacion.color] : 'bg-white'}">
                    <div class="flex justify-between items-start">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full ${iconoColorClases[notificacion.color]}">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconos[notificacion.tipo]}" />
                                    </svg>
                                </span>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center">
                                    <h3 class="text-lg font-medium text-gray-900">${notificacion.titulo}</h3>
                                    ${!notificacion.leida ? '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Nueva</span>' : ''}
                                </div>
                                <p class="mt-2 text-gray-700">${notificacion.mensaje}</p>
                                <div class="mt-3 flex items-center space-x-4">
                                    <span class="text-sm text-gray-500">${notificacion.tiempo_transcurrido}</span>
                                    <span class="text-sm font-medium text-${notificacion.color}-600">${notificacion.tipo}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            ${!notificacion.leida ? `
                                <button onclick="marcarComoLeida(${notificacion.id})" 
                                        class="text-sm text-primary hover:text-primary-dark font-medium">
                                    Marcar como leída
                                </button>
                            ` : ''}
                            <button onclick="eliminarNotificacion(${notificacion.id})" 
                                    class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function filtrarNotificaciones() {
    switch(filtroActual) {
        case 'no-leidas':
            return notificaciones.filter(n => !n.leida);
        case 'Informativo':
        case 'Advertencia': 
        case 'Error':
            return notificaciones.filter(n => n.tipo === filtroActual);
        default:
            return notificaciones;
    }
}

function filtrarPor(filtro) {
    filtroActual = filtro;
    
    // Actualizar botones
    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.classList.remove('bg-primary', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
    });
    
    event.target.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
    event.target.classList.add('bg-primary', 'text-white');
    
    mostrarNotificaciones();
}

async function marcarComoLeida(notificacionId) {
    try {
        const response = await fetch(`{{ url('notificaciones') }}/${notificacionId}/marcar-leida`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const notificacion = notificaciones.find(n => n.id === notificacionId);
            if (notificacion) {
                notificacion.leida = true;
                mostrarNotificaciones();
            }
        }
    } catch (error) {
        console.error('Error al marcar como leída:', error);
    }
}

async function marcarTodasComoLeidas() {
    try {
        const response = await fetch('{{ route("notificaciones.marcar-todas-leidas") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            notificaciones.forEach(n => n.leida = true);
            mostrarNotificaciones();
        }
    } catch (error) {
        console.error('Error al marcar todas como leídas:', error);
    }
}

async function eliminarNotificacion(notificacionId) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta notificación?')) {
        return;
    }
    
    try {
        const response = await fetch(`{{ url('notificaciones') }}/${notificacionId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            notificaciones = notificaciones.filter(n => n.id !== notificacionId);
            mostrarNotificaciones();
        }
    } catch (error) {
        console.error('Error al eliminar notificación:', error);
    }
}
</script>
@endsection 