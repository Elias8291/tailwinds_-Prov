@props(['title' => 'Documentos Requeridos', 'tramite' => null, 'mostrar_navegacion' => true])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8" x-data="documentosData()" x-init="init()">
    <!-- Encabezado con icono -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#8a203f] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg">
            <i class="fas fa-file-upload text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1" x-text="descripcionDocumentos"></p>
        </div>
    </div>

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
                                @click="reemplazarDocumento(documento)"
                                class="text-blue-600 hover:text-blue-800 text-xs underline">
                            Reemplazar
                        </button>
                    </div>
                    
                    <!-- Estado para documentos aprobados (NO se pueden reemplazar) -->
                    <div x-show="documento.estado === 'Aprobado'" class="flex items-center">
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                            <i class="fas fa-check mr-1"></i>
                            Aprobado
                        </span>
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
                // Aquí puedes agregar la lógica para finalizar el trámite
                console.log('🏁 Finalizando trámite:', this.tramiteId);
                
                // Simular finalización
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                this.mostrarExito('Trámite finalizado correctamente');
                
                // Redirigir o hacer algo después de finalizar
                setTimeout(() => {
                    window.location.href = '/tramites-solicitante';
                }, 2000);
                
            } catch (error) {
                console.error('❌ Error al finalizar trámite:', error);
                this.mostrarError('Error al finalizar el trámite');
            } finally {
                this.finalizando = false;
            }
        }
    }
}
</script> 