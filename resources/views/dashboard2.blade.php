@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="max-w-6xl mx-auto px-3 sm:px-4 md:px-6">
       
        <!-- Stats -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-8 max-w-4xl mx-auto">
            <!-- Mis Trámites -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M7 7h6v2H7V7zm0 4h6v2H7v-2z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mis Trámites</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $solicitante ? $solicitante->tramites_count : 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm text-gray-500">
                        Trámites en proceso
                    </div>
                </div>
            </div>

            <!-- Mis Citas -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Próximas Citas</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $solicitante ? $solicitante->citas_pendientes_count : 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm text-gray-500">
                        Citas programadas
                    </div>
                </div>
            </div>

            <!-- Mis Documentos -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v2H7V5zm3 4h3v2h-3V9zM7 9h2v2H7V9zm3 4h3v2h-3v-2zm-3 0h2v2H7v-2z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Documentos Pendientes</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $documentos_pendientes_count }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm text-gray-500">
                        Documentos por revisar
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado de Trámites -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden mt-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Estado de mis trámites
                </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if($solicitante && $solicitante->tramites_count > 0)
                    <p class="text-gray-500 text-sm">Tienes {{ $solicitante->tramites_count }} trámite(s) en proceso.</p>
                @else
                    <p class="text-gray-500 text-sm">No hay trámites activos en este momento.</p>
                @endif
            </div>
        </div>
       
    </div>
</div>
@endsection 