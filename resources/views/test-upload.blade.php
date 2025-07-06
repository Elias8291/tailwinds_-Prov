<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prueba de Subida de Documentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Prueba de Subida de Documentos</h1>
            
            <!-- Información del sistema -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">📊 Configuración Actual del Sistema</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-sm text-gray-600">upload_max_filesize</span>
                        <div class="font-bold text-lg">{{ ini_get('upload_max_filesize') }}</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-sm text-gray-600">post_max_size</span>
                        <div class="font-bold text-lg">{{ ini_get('post_max_size') }}</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-sm text-gray-600">memory_limit</span>
                        <div class="font-bold text-lg">{{ ini_get('memory_limit') }}</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-sm text-gray-600">max_execution_time</span>
                        <div class="font-bold text-lg">{{ ini_get('max_execution_time') }}s</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-sm text-gray-600">Usuario</span>
                        <div class="font-bold text-lg">{{ Auth::user()->email ?? 'No logueado' }}</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <span class="text-sm text-gray-600">Timestamp</span>
                        <div class="font-bold text-lg">{{ now()->format('H:i:s') }}</div>
                    </div>
                </div>
            </div>

            <!-- Formulario de prueba -->
            <div class="bg-white rounded-lg shadow-md p-6" 
                 x-data="uploadTest()" 
                 x-init="init()">
                
                <h2 class="text-xl font-semibold mb-4">📤 Prueba de Subida</h2>
                
                <!-- Selector de archivo -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar archivo PDF para probar
                    </label>
                    <input type="file" 
                           accept=".pdf"
                           @change="handleFileSelect($event)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <!-- Información del archivo seleccionado -->
                <div x-show="fileInfo" class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-blue-800 mb-2">📄 Información del archivo:</h3>
                    <div class="space-y-1 text-sm">
                        <div><strong>Nombre:</strong> <span x-text="fileInfo?.name"></span></div>
                        <div><strong>Tamaño:</strong> <span x-text="fileInfo?.sizeFormatted"></span></div>
                        <div><strong>Tipo:</strong> <span x-text="fileInfo?.type"></span></div>
                        <div><strong>Estado:</strong> 
                            <span :class="fileInfo?.valid ? 'text-green-600' : 'text-red-600'" 
                                  x-text="fileInfo?.valid ? '✅ Válido' : '❌ Inválido'"></span>
                        </div>
                        <div x-show="!fileInfo?.valid" class="text-red-600">
                            <strong>Problema:</strong> <span x-text="fileInfo?.error"></span>
                        </div>
                    </div>
                </div>

                <!-- Botón de subida -->
                <div class="mb-6">
                    <button @click="uploadFile()" 
                            :disabled="!fileInfo?.valid || uploading"
                            :class="uploading ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="px-6 py-3 text-white rounded-lg font-semibold transition-colors">
                        <span x-show="!uploading">🚀 Subir Archivo</span>
                        <span x-show="uploading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Subiendo...
                        </span>
                    </button>
                </div>

                <!-- Progreso -->
                <div x-show="uploading" class="mb-6">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                             :style="'width: ' + progress + '%'"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1" x-text="'Progreso: ' + progress + '%'"></p>
                </div>

                <!-- Resultado -->
                <div x-show="result" class="p-4 rounded-lg" :class="result?.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                    <h3 class="font-semibold mb-2" :class="result?.success ? 'text-green-800' : 'text-red-800'">
                        <span x-show="result?.success">✅ Éxito</span>
                        <span x-show="!result?.success">❌ Error</span>
                    </h3>
                    <div class="text-sm space-y-1">
                        <div><strong>Mensaje:</strong> <span x-text="result?.mensaje"></span></div>
                        <div x-show="result?.debug_info">
                            <strong>Debug:</strong>
                            <pre class="mt-1 text-xs bg-gray-100 p-2 rounded" x-text="JSON.stringify(result?.debug_info, null, 2)"></pre>
                        </div>
                        <div x-show="result?.info">
                            <strong>Info adicional:</strong>
                            <pre class="mt-1 text-xs bg-gray-100 p-2 rounded" x-text="JSON.stringify(result?.info, null, 2)"></pre>
                        </div>
                    </div>
                </div>

                <!-- Log de eventos -->
                <div x-show="logs.length > 0" class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">📋 Log de Eventos</h3>
                    <div class="bg-gray-50 rounded-lg p-4 max-h-60 overflow-y-auto">
                        <template x-for="log in logs" :key="log.id">
                            <div class="text-xs mb-2 font-mono">
                                <span class="text-gray-500" x-text="log.timestamp"></span>
                                <span :class="log.type === 'error' ? 'text-red-600' : log.type === 'success' ? 'text-green-600' : 'text-blue-600'"
                                      x-text="'[' + log.type.toUpperCase() + ']'"></span>
                                <span x-text="log.message"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function uploadTest() {
            return {
                fileInfo: null,
                uploading: false,
                progress: 0,
                result: null,
                logs: [],

                init() {
                    this.addLog('info', 'Sistema inicializado');
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) {
                        this.fileInfo = null;
                        return;
                    }

                    const sizeInMB = file.size / 1024 / 1024;
                    let valid = true;
                    let error = '';

                    if (file.type !== 'application/pdf') {
                        valid = false;
                        error = 'Solo se permiten archivos PDF';
                    } else if (sizeInMB > 100) {
                        valid = false;
                        error = 'El archivo excede 100MB';
                    }

                    this.fileInfo = {
                        name: file.name,
                        size: file.size,
                        sizeFormatted: sizeInMB.toFixed(2) + ' MB',
                        type: file.type,
                        valid: valid,
                        error: error
                    };

                    this.addLog('info', `Archivo seleccionado: ${file.name} (${this.fileInfo.sizeFormatted})`);
                    
                    if (!valid) {
                        this.addLog('error', error);
                    }
                },

                async uploadFile() {
                    if (!this.fileInfo?.valid) return;

                    this.uploading = true;
                    this.progress = 0;
                    this.result = null;

                    this.addLog('info', 'Iniciando subida...');

                    const fileInput = document.querySelector('input[type="file"]');
                    const file = fileInput.files[0];

                    const formData = new FormData();
                    formData.append('archivo', file);
                    formData.append('documento_id', 1); // Usar documento de prueba

                    try {
                        // Simular progreso
                        const progressInterval = setInterval(() => {
                            if (this.progress < 90) {
                                this.progress += 10;
                            }
                        }, 200);

                        const response = await fetch('/tramites-solicitante/upload-documento-local', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        clearInterval(progressInterval);
                        this.progress = 100;

                        const data = await response.json();

                        this.result = data;

                        if (data.success) {
                            this.addLog('success', 'Archivo subido exitosamente');
                        } else {
                            this.addLog('error', `Error: ${data.mensaje}`);
                        }

                    } catch (error) {
                        this.addLog('error', `Error de conexión: ${error.message}`);
                        this.result = {
                            success: false,
                            mensaje: `Error de conexión: ${error.message}`
                        };
                    } finally {
                        this.uploading = false;
                    }
                },

                addLog(type, message) {
                    this.logs.unshift({
                        id: Date.now(),
                        timestamp: new Date().toLocaleTimeString(),
                        type: type,
                        message: message
                    });

                    // Mantener solo los últimos 50 logs
                    if (this.logs.length > 50) {
                        this.logs = this.logs.slice(0, 50);
                    }
                }
            }
        }
    </script>
</body>
</html> 