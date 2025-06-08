@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Notificaciones -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm">
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="bg-white rounded-lg shadow-lg border-l-4 border-emerald-500 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-xl p-3 shadow-md">
                        <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#8a203f] bg-clip-text text-transparent">
                            Gestión de Documentos
                        </h2>
                        <p class="text-sm text-gray-500">Administra los documentos del sistema</p>
                    </div>
                </div>
                <a href="{{ route('documentos.create') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transform hover:scale-105 transition-all duration-300 hover:shadow-lg w-full md:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Crear Nuevo Documento
                </a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-[#9d2449] to-[#8a203f]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Tipo Persona</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Fecha Expiración</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($documentos as $documento)
                    <tr class="hover:bg-gray-50/50 transition-all duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $documento->nombre }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($documento->descripcion, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-lg 
                                @switch($documento->tipo)
                                    @case('Certificado')
                                        bg-blue-50 text-blue-700 border border-blue-100
                                        @break
                                    @case('Copia')
                                        bg-gray-50 text-gray-700 border border-gray-100
                                        @break
                                    @case('Formulario')
                                        bg-green-50 text-green-700 border border-green-100
                                        @break
                                    @case('Carta')
                                        bg-purple-50 text-purple-700 border border-purple-100
                                        @break
                                    @case('Comprobante')
                                        bg-yellow-50 text-yellow-700 border border-yellow-100
                                        @break
                                    @case('Acta')
                                        bg-red-50 text-red-700 border border-red-100
                                        @break
                                    @default
                                        bg-gray-50 text-gray-700 border border-gray-100
                                @endswitch">
                                {{ $documento->tipo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-lg 
                                @switch($documento->tipo_persona)
                                    @case('Física')
                                        bg-indigo-50 text-indigo-700 border border-indigo-100
                                        @break
                                    @case('Moral')
                                        bg-pink-50 text-pink-700 border border-pink-100
                                        @break
                                    @default
                                        bg-purple-50 text-purple-700 border border-purple-100
                                @endswitch">
                                {{ $documento->tipo_persona }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $documento->fecha_expiracion->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-lg {{ $documento->es_visible ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                {{ $documento->es_visible ? 'Visible' : 'No Visible' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <div class="flex items-center justify-center space-x-3">
                                <a href="{{ route('documentos.edit', $documento) }}" 
                                   class="text-[#9d2449] hover:text-[#8a203f] transform hover:scale-110 transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('documentos.destroy', $documento) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200"
                                            onclick="return confirm('¿Está seguro que desea eliminar este documento?')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 