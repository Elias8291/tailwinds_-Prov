@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-4 shadow-lg">
                        <svg class="w-8 h-8 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-purple-700 bg-clip-text text-transparent">
                            Revisión de Trámite
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Vista alternativa de revisión</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="space-y-6">
            @foreach($secciones as $seccion)
                <x-revision.seccion-revision-v2 
                    :seccionId="$seccion->id"
                    :estado="$seccion->estado"
                    :comentario="$seccion->comentario"
                    :tramiteId="$tramite->id"
                />
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush 