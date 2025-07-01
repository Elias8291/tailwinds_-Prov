<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Páginas de Error</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">
                Prueba de Páginas de Error
            </h1>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 mb-6 text-center">
                    Haz clic en cualquiera de los botones para probar las diferentes páginas de error:
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Error 403 -->
                    <a href="{{ route('test.error.403') }}" 
                       class="bg-red-500 hover:bg-red-600 text-white p-4 rounded-lg text-center transition-colors duration-200 block">
                        <div class="text-2xl font-bold mb-2">403</div>
                        <div class="text-sm">Acceso No Autorizado</div>
                    </a>
                    
                    <!-- Error 404 -->
                    <a href="{{ route('test.error.404') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center transition-colors duration-200 block">
                        <div class="text-2xl font-bold mb-2">404</div>
                        <div class="text-sm">Página No Encontrada</div>
                    </a>
                    
                    <!-- Error 419 -->
                    <a href="{{ route('test.error.419') }}" 
                       class="bg-amber-500 hover:bg-amber-600 text-white p-4 rounded-lg text-center transition-colors duration-200 block">
                        <div class="text-2xl font-bold mb-2">419</div>
                        <div class="text-sm">Página Expirada</div>
                    </a>
                    
                    <!-- Error 429 -->
                    <a href="{{ route('test.error.429') }}" 
                       class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center transition-colors duration-200 block">
                        <div class="text-2xl font-bold mb-2">429</div>
                        <div class="text-sm">Demasiadas Solicitudes</div>
                    </a>
                    
                    <!-- Error 500 -->
                    <a href="{{ route('test.error.500') }}" 
                       class="bg-gray-700 hover:bg-gray-800 text-white p-4 rounded-lg text-center transition-colors duration-200 block">
                        <div class="text-2xl font-bold mb-2">500</div>
                        <div class="text-sm">Error Interno del Servidor</div>
                    </a>
                    
                    <!-- Error 503 -->
                    <a href="{{ route('test.error.503') }}" 
                       class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-lg text-center transition-colors duration-200 block">
                        <div class="text-2xl font-bold mb-2">503</div>
                        <div class="text-sm">Servicio No Disponible</div>
                    </a>
                </div>
                
                <div class="mt-8 text-center">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-800 text-sm">
                            <strong>Nota:</strong> Estas rutas de prueba solo están disponibles en modo de desarrollo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 