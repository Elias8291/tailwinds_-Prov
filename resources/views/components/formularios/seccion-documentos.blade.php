@props(['title' => 'Documentos Requeridos', 'tramite' => null, 'mostrar_navegacion' => true, 'documentos' => [], 'readonly' => false])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8" 
     @if(!$readonly) x-data="documentosData()" x-init="init()" @endif>
    <!-- Encabezado con icono -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg">
            <i class="fas fa-file-upload text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                @if($readonly)
                    Documentos adjuntos al trámite
                @else
                    <span x-text="descripcionDocumentos"></span>
                @endif
            </p>
        </div>
    </div>

    @if($readonly)
        <!-- Vista de solo lectura para revisión -->
        <div class="space-y-6">
            @if(count($documentos) > 0)
                @foreach($documentos as $documento)
                <div class="bg-white border-2 rounded-lg p-6 transition-all duration-300
                    @if($documento['estado'] === 'Aprobado') border-green-300 bg-green-50
                    @elseif($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo'])) border-blue-300 bg-blue-50
                    @elseif($documento['estado'] === 'Rechazado') border-red-300 bg-red-50
                    @else border-gray-300 @endif">
                    
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-2xl mr-3
                                @if($documento['estado'] === 'Aprobado') text-green-600
                                @elseif($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo'])) text-blue-600
                                @elseif($documento['estado'] === 'Rechazado') text-red-600
                                @else text-[#9d2449] @endif"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $documento['nombre'] }}</h4>
                                <p class="text-xs text-gray-500">{{ $documento['descripcion'] ?? 'Documento requerido' }}</p>
                                
                                <!-- Estado del documento -->
                                @if($documento['estado'] !== 'Pendiente' || !empty($documento['ruta_archivo']))
                                <div class="flex items-center mt-1">
                                    <i class="text-xs mr-1
                                        @if($documento['estado'] === 'Aprobado') fas fa-check-circle text-green-500
                                        @elseif($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo'])) fas fa-clock text-blue-500
                                        @elseif($documento['estado'] === 'Rechazado') fas fa-times-circle text-red-500
                                        @endif"></i>
                                    <span class="text-xs font-medium
                                        @if($documento['estado'] === 'Aprobado') text-green-600
                                        @elseif($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo'])) text-blue-600
                                        @elseif($documento['estado'] === 'Rechazado') text-red-600
                                        @endif">
                                        @if($documento['estado'] === 'Pendiente' && !empty($documento['ruta_archivo']))
                                            En Revisión
                                        @else
                                            {{ $documento['estado'] }}
                                        @endif
                                    </span>
                                </div>
                                @endif
                                
                                <!-- Observaciones para documentos rechazados -->
                                @if($documento['estado'] === 'Rechazado' && !empty($documento['observaciones']))
                                <div class="mt-1">
                                    <p class="text-xs text-red-600">{{ $documento['observaciones'] }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Botones de acción para revisión -->
                        <div class="flex items-center space-x-2">
                            @if(!empty($documento['ruta_archivo']))
                                <a href="{{ route('tramites.solicitante.ver-documento', ['tramite' => $tramite->id ?? 0, 'documento' => $documento['id']]) }}" 
                                   target="_blank"
                                   class="text-green-600 hover:text-green-800 text-xs underline">
                                    <i class="fas fa-eye mr-1"></i>
                                    Ver
                                </a>
                            @endif
                            
                            @if($documento['estado'] === 'Pendiente')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pendiente
                                </span>
                            @elseif($documento['estado'] === 'Aprobado')
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-check mr-1"></i>
                                    Aprobado
                                </span>
                            @elseif($documento['estado'] === 'Rechazado')
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-times mr-1"></i>
                                    Rechazado
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Información adicional del archivo -->
                    @if(!empty($documento['ruta_archivo']))
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-file-pdf text-[#9d2449] mr-2"></i>
                            <span>{{ $documento['nombre_original'] ?? 'Documento adjunto' }}</span>
                            @if(!empty($documento['fecha_subida']))
                            <span class="ml-auto text-xs text-gray-500">
                                @php
                                    $fechaSubida = $documento['fecha_subida'] ?? '';
                                    if (!empty($fechaSubida) && $fechaSubida !== 'No disponible') {
                                        try {
                                            $fechaSubidaFormateada = \Carbon\Carbon::parse($fechaSubida)->format('d/m/Y H:i');
                                        } catch (\Exception $e) {
                                            $fechaSubidaFormateada = $fechaSubida;
                                        }
                                    } else {
                                        $fechaSubidaFormateada = 'No especificado';
                                    }
                                @endphp
                                Subido: {{ $fechaSubidaFormateada }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <!-- Mensaje cuando no hay documentos -->
                <div class="text-center py-8">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <i class="fas fa-exclamation-circle text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">No hay documentos adjuntos a este trámite.</p>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Vista editable normal (código existente) -->
        <!-- Alert de Errores -->
        <div x-show="showError" x-cloak class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                <p class="text-red-700 text-sm" x-text="errorMessage"></p>
            </div>
        </div>

        <!-- Alert de Éxito -->
        <div x-show="showSuccess" x-cloak class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-700 text-sm" x-text="successMessage"></p>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" x-cloak class="text-center py-8">
            <div class="bg-gray-50 rounded-lg p-6">
                <i class="fas fa-spinner fa-spin text-gray-400 text-3xl mb-3"></i>
                <p class="text-gray-500">Cargando documentos...</p>
            </div>
        </div>

        <!-- Lista de Documentos -->
        <div x-show="!loading" x-cloak class="space-y-6">
            <template x-for="documento in documentos" :key="documento.id">
                <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-[#9d2449]/50 transition-all duration-300 group"
                     :class="{
                         'border-green-300 bg-green-50': documento.estado === 'Aprobado',
                         'border-blue-300 bg-blue-50': documento.estado === 'Pendiente' && documento.ruta_archivo,
                         'border-red-300 bg-red-50': documento.estado === 'Rechazado'
                     }">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                            <i class="fas fa-file-pdf text-2xl mr-3 group-hover:scale-110 transition-transform duration-300"
                               :class="{
                                   'text-green-600': documento.estado === 'Aprobado',
                                   'text-blue-600': documento.estado === 'Pendiente' && documento.ruta_archivo,
                                   'text-red-600': documento.estado === 'Rechazado',
                                   'text-[#9d2449]': documento.estado === 'Pendiente' && !documento.ruta_archivo
                               }"></i>
                                    <div>
                                <h4 class="text-sm font-medium text-gray-900" x-text="documento.nombre"></h4>
                                <p class="text-xs text-gray-500" x-text="documento.descripcion || 'PDF, máximo 10MB'"></p>
                                
                                <!-- Estado del documento -->
                                <div x-show="documento.estado !== 'Pendiente' || documento.ruta_archivo" class="flex items-center mt-1">
                                    <i :class="{
                                        'fas fa-check-circle text-green-500': documento.estado === 'Aprobado',
                                        'fas fa-clock text-blue-500': documento.estado === 'Pendiente' && documento.ruta_archivo,
                                        'fas fa-times-circle text-red-500': documento.estado === 'Rechazado'
                                    }" class="text-xs mr-1"></i>
                                    <span class="text-xs font-medium"
                                          :class="{
                                              'text-green-600': documento.estado === 'Aprobado',
                                              'text-blue-600': documento.estado === 'Pendiente' && documento.ruta_archivo,
                                              'text-red-600': documento.estado === 'Rechazado'
                                          }"
                                          x-text="documento.estado === 'Pendiente' && documento.ruta_archivo ? 'En Revisión' : documento.estado"></span>
                                    </div>
                                
                                <!-- Observaciones para documentos rechazados -->
                                <div x-show="documento.estado === 'Rechazado' && documento.observaciones" class="mt-1">
                                    <p class="text-xs text-red-600" x-text="documento.observaciones"></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botón de selección para documentos pendientes sin archivo o rechazados -->
                        <div x-show="(documento.estado === 'Pendiente' && !documento.ruta_archivo) || documento.estado === 'Rechazado'">
                                <input type="file" 
                                   :name="`documento_${documento.id}`" 
                                       accept=".pdf"
                                       class="hidden" 
                                   :id="`documento_${documento.id}`"
                                   @change="handleFileSelect($event, documento)"
                                       required>
                            <label :for="`documento_${documento.id}`" 
                                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] cursor-pointer transition-all duration-300">
                                <span x-show="!documento.uploading" x-text="documento.estado === 'Rechazado' ? 'Subir Nuevo' : 'Seleccionar archivo'"></span>
                                <span x-show="documento.uploading" class="flex items-center">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Subiendo...
                                </span>
                                </label>
                            </div>
                        
                        <!-- Estado para documentos en revisión -->
                        <div x-show="documento.estado === 'Pendiente' && documento.ruta_archivo" class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                <i class="fas fa-clock mr-1"></i>
                                En Revisión
                            </span>
                            <button type="button" 
                                    @click="verDocumento(documento)"
                                    class="text-green-600 hover:text-green-800 text-xs underline">
                                <i class="fas fa-eye mr-1"></i>
                                Ver
                            </button>
                            <button type="button" 
                                    @click="reemplazarDocumento(documento)"
                                    class="text-blue-600 hover:text-blue-800 text-xs underline">
                                Reemplazar
                            </button>
                        </div>
                        
                        <!-- Estado para documentos aprobados (NO se pueden reemplazar) -->
                        <div x-show="documento.estado === 'Aprobado'" class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                <i class="fas fa-check mr-1"></i>
                                Aprobado
                            </span>
                            <button type="button" 
                                    @click="verDocumento(documento)"
                                    class="text-green-600 hover:text-green-800 text-xs underline">
                                <i class="fas fa-eye mr-1"></i>
                                Ver
                            </button>
                        </div>
                        
                        <!-- Estado para documentos rechazados -->
                        <div x-show="documento.estado === 'Rechazado'" class="flex items-center">
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                <i class="fas fa-times mr-1"></i>
                                Rechazado
                            </span>
                        </div>
                    </div>
                    
                    <!-- Preview del archivo seleccionado -->
                    <div x-show="documento.archivo_seleccionado && documento.estado !== 'Aprobado'" 
                         class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-pdf text-[#9d2449] mr-2"></i>
                                <span class="text-sm text-gray-900 font-medium" x-text="documento.nombre_archivo"></span>
                                    </div>
                            <button type="button" 
                                    @click="removerArchivo(documento)"
                                    class="text-red-600 hover:text-red-800 transition-colors duration-300">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
            </template>

            <!-- Mensaje cuando no hay documentos -->
            <div x-show="documentos.length === 0 && !loading" x-cloak class="text-center py-8">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <i class="fas fa-exclamation-circle text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-500">No hay documentos configurados para este tipo de persona.</p>
                            </div>
                        </div>
        </div>

        <!-- Botones de navegación -->
        <div x-show="mostrarNavegacion && !loading" x-cloak class="flex justify-between pt-6 border-t border-gray-200 mt-8">
            <button type="button" 
                    @click="$dispatch('previous-step')"
                    class="flex items-center px-6 py-3 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Anterior
            </button>

            <button type="button" 
                    @click="finalizarTramite()"
                    :disabled="!todosDocumentosEnviados"
                    :class="!todosDocumentosEnviados ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#6d1a32]'"
                    class="flex items-center px-6 py-3 text-white rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                <span x-show="!finalizando">
                    Finalizar Trámite
                    <i class="fas fa-check ml-2"></i>
                </span>
                <span x-show="finalizando" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Finalizando...
                </span>
            </button>
        </div>
    @endif
</div>

<script>
function documentosData() {
    return {
        tramiteId: null,
        tipoPersona: 'Física',
        documentos: [],
        loading: true,
        showError: false,
        errorMessage: '',
        showSuccess: false,
        successMessage: '',
        finalizando: false,
        mostrarNavegacion: @json($mostrar_navegacion ?? true),
        
        async init() {
            // Obtener tramite_id
            const tramite = @json($tramite ?? null);
            
            if (tramite && tramite.id) {
                this.tramiteId = tramite.id;
                this.tipoPersona = tramite.solicitante?.tipo_persona || 'Física';
                await this.cargarDocumentos();
            } else {
                await this.obtenerDatosTramite();
            }
        },

        async obtenerDatosTramite() {
            try {
                const response = await fetch('/tramites-solicitante/datos-tramite');
                const data = await response.json();
                
                if (data.success) {
                    this.tramiteId = data.tramite_id;
                    this.tipoPersona = data.tipo_persona;
                    await this.cargarDocumentos();
                } else {
                    this.mostrarError('No se pudo obtener información del trámite');
        }
    } catch (error) {
                console.error('Error al obtener datos del trámite:', error);
                this.mostrarError('Error al cargar información del trámite');
            }
        },

        async cargarDocumentos() {
            try {
                this.loading = true;
                console.log('🔍 Cargando documentos para trámite:', this.tramiteId);
                
                const response = await fetch('/tramites-solicitante/documentos');
                const data = await response.json();
                
                if (data.success && data.documentos) {
                    this.documentos = data.documentos.map(doc => ({
                        ...doc,
                        estado: doc.estado || 'Pendiente',
                        uploading: false,
                        archivo_seleccionado: false,
                        nombre_archivo: '',
                        observaciones: doc.observaciones || null
                    }));
                    
                    console.log('📋 Documentos cargados:', this.documentos);
                } else {
                    this.documentos = [];
                    console.log('❌ No se encontraron documentos');
                }
            } catch (error) {
                console.error('❌ Error al cargar documentos:', error);
                this.mostrarError('Error al cargar los documentos');
            } finally {
                this.loading = false;
            }
        },

        async handleFileSelect(event, documento) {
            const file = event.target.files[0];
            if (!file) return;

            // Validaciones
                    if (file.size > 10 * 1024 * 1024) {
                this.mostrarError('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
                event.target.value = '';
                        return;
                    }
                    
                    if (!file.type.includes('pdf')) {
                this.mostrarError('Solo se permiten archivos PDF.');
                event.target.value = '';
                        return;
                    }

            // Actualizar estado del documento
            documento.archivo_seleccionado = true;
            documento.nombre_archivo = file.name;
            documento.uploading = true;
                    
            // Subir archivo
            await this.subirDocumento(documento, file);
        },

        async subirDocumento(documento, file) {
            try {
    const formData = new FormData();
                formData.append('archivo', file);
                formData.append('documento_id', documento.id);

                // Agregar CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }

                console.log('📤 Subiendo documento:', documento.nombre);

        const response = await fetch('/tramites-solicitante/upload-documento', {
            method: 'POST',
                    body: formData,
            headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
        });
        
        const data = await response.json();
                console.log('📥 Respuesta del servidor:', data);

                if (data.success) {
                    documento.estado = 'Pendiente'; // Cambiado de 'Enviado' a 'Pendiente' para indicar que está en revisión
                    documento.ruta_archivo = data.ruta;
                    documento.docSolicitanteId = data.docSolicitanteId;
                    documento.observaciones = null; // Limpiar observaciones previas
                    this.mostrarExito(`Documento "${documento.nombre}" subido correctamente y en revisión`);
                } else {
                    this.mostrarError(data.mensaje || 'Error al subir el documento');
                    documento.archivo_seleccionado = false;
                    documento.nombre_archivo = '';
        }
    } catch (error) {
                console.error('❌ Error al subir documento:', error);
                this.mostrarError('Error de conexión al subir el documento');
                documento.archivo_seleccionado = false;
                documento.nombre_archivo = '';
            } finally {
                documento.uploading = false;
            }
        },

        reemplazarDocumento(documento) {
            // Solo permitir reemplazar si NO está aprobado
            if (documento.estado === 'Aprobado') {
                this.mostrarError('No se puede reemplazar un documento que ya ha sido aprobado');
                return;
}

            documento.estado = 'Pendiente';
            documento.archivo_seleccionado = false;
            documento.nombre_archivo = '';
            documento.ruta_archivo = null;
            documento.observaciones = null;
            // Limpiar el input file
            const input = document.getElementById(`documento_${documento.id}`);
            if (input) input.value = '';
        },

        removerArchivo(documento) {
            documento.archivo_seleccionado = false;
            documento.nombre_archivo = '';
            // Limpiar el input file
            const input = document.getElementById(`documento_${documento.id}`);
            if (input) input.value = '';
        },

        get descripcionDocumentos() {
            return `Documentos necesarios para ${this.tipoPersona === 'Física' ? 'persona física' : 'persona moral'}`;
        },

        get todosDocumentosEnviados() {
            return this.documentos.length > 0 && this.documentos.every(doc => 
                doc.estado === 'Aprobado' || (doc.estado === 'Pendiente' && doc.ruta_archivo)
            );
        },

        mostrarError(mensaje) {
            this.errorMessage = mensaje;
            this.showError = true;
            this.showSuccess = false;
            setTimeout(() => {
                this.showError = false;
            }, 5000);
        },

        mostrarExito(mensaje) {
            this.successMessage = mensaje;
            this.showSuccess = true;
            this.showError = false;
            setTimeout(() => {
                this.showSuccess = false;
            }, 3000);
        },

        async finalizarTramite() {
            if (!this.todosDocumentosEnviados) {
                this.mostrarError('Debe subir todos los documentos requeridos antes de finalizar el trámite');
                return;
            }
            
            // Verificar si hay documentos rechazados
            const documentosRechazados = this.documentos.filter(doc => doc.estado === 'Rechazado');
            if (documentosRechazados.length > 0) {
                this.mostrarError('Hay documentos rechazados que deben ser corregidos antes de finalizar el trámite');
                return;
            }

            this.finalizando = true;
            
            try {
                console.log('🏁 Finalizando trámite:', this.tramiteId);
                
                const formData = new FormData();
                formData.append('tramite_id', this.tramiteId);
                
                // Agregar CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }

                const response = await fetch('/tramites-solicitante/finalizar-tramite', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                console.log('📥 Respuesta finalización:', data);

                if (data.success) {
                    // Mostrar vista de confirmación
                    this.mostrarVistaConfirmacion(data);
                } else {
                    this.mostrarError(data.message || 'Error al finalizar el trámite');
                }
                
            } catch (error) {
                console.error('❌ Error al finalizar trámite:', error);
                this.mostrarError('Error de conexión al finalizar el trámite');
            } finally {
                this.finalizando = false;
            }
        },

        mostrarVistaConfirmacion(data) {
            // Ocultar el formulario de documentos y mostrar confirmación
            document.querySelector('[x-data="documentosData()"]').innerHTML = `
                <div class="text-center py-12">
                    <div class="max-w-md mx-auto">
                        <!-- Icono de éxito -->
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                            <i class="fas fa-check text-green-600 text-2xl"></i>
                        </div>
                        
                        <!-- Título -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            ¡Trámite Enviado Correctamente!
                        </h2>
                        
                        <!-- Mensaje -->
                        <p class="text-gray-600 mb-6">
                            Su trámite ha sido enviado para revisión. Recibirá una notificación cuando el proceso de revisión haya finalizado.
                        </p>
                        
                        <!-- Estado del trámite -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-clock text-blue-600 mr-2"></i>
                                <span class="text-blue-800 font-medium">Estado: En Revisión</span>
                            </div>
                            <p class="text-blue-600 text-sm mt-2">
                                Progreso del trámite: 100% completado
                            </p>
                        </div>
                        
                        <!-- Información adicional -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="font-medium text-gray-900 mb-2">¿Qué sigue?</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Su trámite será revisado por nuestro equipo</li>
                                <li>• Recibirá notificaciones sobre el estado</li>
                                <li>• Podrá editar el formulario si se requieren cambios</li>
                            </ul>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="space-y-3">
                            <button onclick="window.location.href='/tramites-solicitante'" 
                                    class="w-full bg-[#9d2449] text-white px-6 py-3 rounded-lg hover:bg-[#8a203f] transition duration-200">
                                <i class="fas fa-home mr-2"></i>
                                Volver al Inicio
                            </button>
                            
                            <button onclick="window.print()" 
                                    class="w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition duration-200">
                                <i class="fas fa-print mr-2"></i>
                                Imprimir Comprobante
                            </button>
                        </div>
                    </div>
                </div>
            `;
        },

        verDocumento(documento) {
            if (!documento.ruta_archivo || !this.tramiteId) {
                this.mostrarError('No se puede acceder al documento');
                return;
            }

            // Detectar si es móvil
            const esMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (esMobile) {
                // En móvil, forzar descarga
                window.location.href = `/tramites-solicitante/ver-documento/${this.tramiteId}/${documento.id}?download=1`;
            } else {
                // En desktop, abrir en nueva pestaña
                window.open(`/tramites-solicitante/ver-documento/${this.tramiteId}/${documento.id}`, '_blank');
            }
        }
    }
}
</script>