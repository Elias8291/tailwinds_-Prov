@extends('layouts.app')

@section('title', 'Mi Estado')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4">
        @if(!$proveedor)
            <!-- Estado Sin Inscripción -->
            <div class="max-w-sm mx-auto">
                <div class="bg-white rounded-lg shadow-lg border-t-4 border-t-[#9d2449] overflow-hidden">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-[#9d2449]/5 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-slash text-[#9d2449] text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Sin Registro</h3>
                        <p class="text-gray-600 text-sm mb-6">Inicie su registro como proveedor</p>
                        <a href="{{ route('tramites.solicitante.index') }}" 
                           class="inline-flex items-center px-6 py-2 bg-[#9d2449] text-white text-sm font-medium rounded-md hover:bg-[#7c1d39] transition-all duration-300">
                            <i class="fas fa-plus mr-2"></i>
                            Registrarse
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Credencial de Proveedor -->
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100">
                <!-- Encabezado -->
                <div class="bg-[#9d2449] text-white p-5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-[#b62952] rounded-full transform translate-x-32 -translate-y-32 opacity-50"></div>
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[#7c1d39] rounded-full transform translate-x-16 -translate-y-16 opacity-50"></div>
                    
                    <div class="relative flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold">Credencial de Proveedor</h2>
                            <p class="text-white/80 text-sm">Gobierno del Estado de Oaxaca</p>
                        </div>
                        <div class="w-12 h-12 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white/20">
                            <i class="fas fa-user-tie text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Información Principal -->
                <div class="p-5 border-b border-gray-100">
                    <div class="grid grid-cols-12 gap-4">
                        <!-- PV y Estado -->
                        <div class="col-span-4">
                            <div class="mb-3">
                                <p class="text-xs font-medium text-gray-500">Número de Proveedor</p>
                                <p class="text-2xl font-bold text-[#9d2449] tracking-wider font-mono">{{ $proveedor->pv }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 rounded-full @if($proveedor->estado === 'Activo') bg-emerald-500 @else bg-red-500 @endif"></div>
                                <span class="text-sm font-medium text-gray-700">{{ $proveedor->estado }}</span>
                            </div>
                        </div>
                        
                        <!-- RFC y Razón Social -->
                        <div class="col-span-8">
                            <div class="mb-3">
                                <p class="text-xs font-medium text-gray-500">Razón Social</p>
                                <p class="text-base font-semibold text-gray-800 truncate">{{ $proveedor->solicitante->razon_social ?? $proveedor->solicitante->nombre_completo ?? 'N/D' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">RFC</p>
                                <p class="text-base font-semibold text-gray-800">{{ $proveedor->solicitante->rfc ?? 'N/D' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fechas y Estado -->
                <div class="p-5 border-b border-gray-100">
                    <div class="grid grid-cols-12 gap-4">
                        <!-- Fechas -->
                        <div class="col-span-8 grid grid-cols-2 gap-4">
                            <div class="bg-gray-50/50 rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500">Registro</p>
                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                    @php
                                        $fechaRegistro = null;
                                        try {
                                            if ($proveedor->fecha_registro instanceof \Carbon\Carbon) {
                                                $fechaRegistro = $proveedor->fecha_registro;
                                            } elseif (!empty($proveedor->fecha_registro)) {
                                                $fechaRegistro = \Carbon\Carbon::parse($proveedor->fecha_registro);
                                            }
                                        } catch (\Exception $e) {
                                            $fechaRegistro = null;
                                        }
                                    @endphp
                                    {{ $fechaRegistro ? $fechaRegistro->format('d/m/Y') : 'N/D' }}
                                </p>
                            </div>

                            <div class="bg-gray-50/50 rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500">Vencimiento</p>
                                <p class="text-sm font-semibold text-gray-800 mt-1">
                                    {{ $fechaVencimiento instanceof \Carbon\Carbon ? $fechaVencimiento->format('d/m/Y') : 'No definida' }}
                                </p>
                            </div>
                        </div>

                        <!-- Estado Badge -->
                        <div class="col-span-4 flex items-center justify-end">
                            <div class="inline-flex items-center px-3 py-1.5 rounded-lg border
                                @if($estadoInscripcion === 'Activa') bg-emerald-50 text-emerald-700 border-emerald-200
                                @elseif($estadoInscripcion === 'Por vencer') bg-amber-50 text-amber-700 border-amber-200
                                @elseif($estadoInscripcion === 'Vencida') bg-red-50 text-red-700 border-red-200
                                @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                <i class="fas @if($estadoInscripcion === 'Activa') fa-shield-check @elseif($estadoInscripcion === 'Por vencer') fa-clock @else fa-exclamation-circle @endif mr-2"></i>
                                <span class="text-sm font-medium">{{ strtoupper($estadoInscripcion) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tiempo Restante -->
                @if($diasRestantes !== null && isset($infoProveedor['tiempo_restante']))
                    <div class="p-5">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                @if($diasRestantes < 0) bg-red-100 text-red-700
                                @elseif($diasRestantes <= 30) bg-amber-100 text-amber-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                <i class="fas @if($diasRestantes < 0) fa-exclamation-triangle @elseif($diasRestantes <= 30) fa-clock @else fa-shield-check @endif text-lg"></i>
                            </div>
                            <p class="text-sm text-gray-600">{{ $infoProveedor['tiempo_restante']['texto'] }}</p>
                        </div>

                        @if(isset($infoProveedor['tiempo_restante']['desglose']))
                            <div class="grid grid-cols-4 gap-3 bg-gray-50/50 rounded-lg p-4">
                                @foreach(['años' => 'Años', 'meses' => 'Meses', 'dias' => 'Días', 'horas' => 'Horas'] as $key => $label)
                                    @if($infoProveedor['tiempo_restante']['desglose'][$key] > 0)
                                        <div class="text-center p-2 bg-white rounded-lg shadow-sm">
                                            <span class="text-xl font-bold text-[#9d2449] block">
                                                {{ $infoProveedor['tiempo_restante']['desglose'][$key] }}
                                            </span>
                                            <span class="text-xs text-gray-500 font-medium">{{ $label }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-5">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-[#9d2449]/5 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar text-[#9d2449] text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-gray-800">Fecha Actual</h4>
                                <p class="text-sm text-[#9d2449]">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection 