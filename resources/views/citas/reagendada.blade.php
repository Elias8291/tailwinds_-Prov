<!DOCTYPE html>
<html>
<head>
    <title>Cita Reagendada</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative">
            <!-- Icono y Título -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-check text-emerald-500 text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">¡Cita Reagendada con Éxito!</h2>
                <p class="text-gray-600 mt-2">Su cita ha sido reagendada automáticamente</p>
            </div>

            <!-- Detalles de la Cita -->
            <div class="bg-emerald-50 rounded-lg p-4 mb-6">
                <div class="space-y-4">
                    <div class="flex items-center text-emerald-700">
                        <i class="fas fa-calendar-alt w-6 mr-3"></i>
                        <span class="font-semibold">{{ $fecha->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                    </div>
                    <div class="flex items-center text-emerald-700">
                        <i class="fas fa-clock w-6 mr-3"></i>
                        <span class="font-semibold">{{ $fecha->format('H:i') }} hrs</span>
                    </div>
                    <div class="flex items-start text-emerald-700">
                        <i class="fas fa-map-marker-alt w-6 mr-3 mt-1"></i>
                        <div>
                            <span class="font-semibold">Ciudad Administrativa</span><br>
                            <span class="text-sm">Edificio 1, Nivel 1, Módulo de Proveedores</span><br>
                            <span class="text-xs text-emerald-600">Internacional 8, San Miguel 2da Secc, 68270 Tlalixtac de Cabrera, Oax.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Importante -->
            <div class="bg-amber-50 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-amber-500 mt-1 w-6 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-amber-800 mb-2">Importante:</h3>
                        <ul class="text-sm text-amber-700 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check text-amber-500 mr-2"></i>
                                Llegue 15 minutos antes de su cita
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-amber-500 mr-2"></i>
                                Traiga todos sus documentos originales
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-amber-500 mr-2"></i>
                                Se le ha enviado una notificación con los detalles
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Botón de Continuar -->
            <div class="text-center">
                <a href="{{ route('tramites.solicitante.estado', $tramite->id) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                    <i class="fas fa-check-circle mr-2"></i>
                    Entendido
                </a>
            </div>
        </div>
    </div>

    <script>
        // Redirigir después de 5 segundos
        setTimeout(function() {
            window.location.href = '{{ route('tramites.solicitante.estado', $tramite->id) }}';
        }, 5000);
    </script>
</body>
</html> 