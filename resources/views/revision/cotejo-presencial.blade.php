@extends('layouts.app')

@section('content')
    <div class="max-w-[1800px] mx-auto px-8 py-6 space-y-6">
        <!-- Header Principal -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <x-revision.header 
                :tramiteId="$tramite->id"
                :tipoTramite="$tramite->tipo_tramite"
                :rfc="$tramite->solicitante->rfc ?? ''"
            />
        </div>

        <!-- Alerta de Verificación -->
        <div class="bg-[#9d2449]/10 border border-[#9d2449]/20 rounded-xl p-6">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-shield-check text-[#9d2449] text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-[#9d2449]">Verificación de Documentos Físicos</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Verifique cuidadosamente cada documento físico. Preste especial atención a:
                    </p>
                    <ul class="mt-2 space-y-1 text-sm text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-[#9d2449] mr-2"></i>
                            Calidad del papel y textura
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-[#9d2449] mr-2"></i>
                            Sellos y firmas originales
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-[#9d2449] mr-2"></i>
                            Elementos de seguridad (hologramas, marcas de agua)
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-[#9d2449] mr-2"></i>
                            Coincidencia exacta con la versión digital
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sección de Documentos para Cotejo -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div x-data="{ 
                isOpen: true,
                estado: '{{ $revisionesExistentes[6]['estado'] ?? 'pendiente' }}'
            }">
                <!-- Encabezado -->
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-xl font-bold text-gray-800">Cotejo de Documentos</h2>
                        </div>
                        @if(isset($revisionesExistentes[6]))
                            <span id="estado_seccion_6" class="px-3 py-1 text-sm rounded-full flex items-center space-x-2"
                                :class="{
                                    'bg-green-100 text-green-800': estado === 'aprobado',
                                    'bg-red-100 text-red-800': estado === 'rechazado',
                                    'bg-yellow-100 text-yellow-800': estado === 'pendiente'
                                }">
                                <i class="fas fa-circle text-xs"></i>
                                <span x-text="estado.charAt(0).toUpperCase() + estado.slice(1)"></span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Contenido -->
                <div class="p-6 pt-0">
                    <!-- Contenedor de la sección de documentos -->
                    <div class="space-y-6">
                        @include('components.formularios.seccion-documentos', [
                            'title' => 'Documentos para Cotejo',
                            'tramite' => $tramite,
                            'mostrar_navegacion' => false,
                            'documentos' => collect($documentos ?? [])->map(function($doc) {
                                return [
                                    'id' => $doc['id'] ?? null,
                                    'nombre' => $doc['nombre'] ?? 'Documento sin nombre',
                                    'descripcion' => $doc['descripcion'] ?? 'Sin descripción',
                                    'estado' => $doc['estado'] ?? 'Pendiente',
                                    'ruta_archivo' => $doc['ruta_archivo'] ?? null,
                                    'fecha_subida' => $doc['fecha_entrega'] ?? null,
                                    'observaciones' => $doc['observaciones'] ?? null,
                                    'nombre_original' => $doc['ruta_archivo'] ? basename($doc['ruta_archivo']) : 'archivo.pdf',
                                    'documento_id' => $doc['documento_id'] ?? null,
                                    'validacion_ia' => $doc['validacion_ia'] ?? null,
                                    'documento_cotejado' => $doc['documento_cotejado'] ?? false,
                                    'comentario_revision' => $doc['comentario_revision'] ?? null
                                ];
                            })->toArray(),
                            'readonly' => true,
                            'en_revision' => true,
                            'modo_cotejo' => true
                        ])
                    </div>

                    <!-- Sección de revisión -->
                    <x-revision.seccion-revision 
                        :seccionId="6"
                        :estado="$revisionesExistentes[6]['estado'] ?? 'pendiente'"
                        :observaciones="$revisionesExistentes[6]['observaciones'] ?? ''"
                        :tramiteId="$tramite->id"
                        :modo_cotejo="true"
                    />
                </div>
            </div>
        </div>

        <!-- Botones de Acción del Trámite -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-[#9d2449]/10 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-[#9d2449] text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Finalizar Trámite</h2>
                        <p class="text-sm text-gray-600">Seleccione una acción para finalizar el trámite</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center space-x-4">
                @if($tramite->estado !== 'Aprobado')
                    <button onclick="mostrarModalAprobacion()" 
                            class="flex-1 bg-[#9d2449] hover:bg-[#7a1d3a] text-white px-6 py-3 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-check-circle"></i>
                        <span>Aprobar Trámite</span>
                    </button>

                    <button onclick="cancelarTramite()" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-times-circle"></i>
                        <span>Cancelar Trámite</span>
                    </button>
                @else
                    <div class="flex-1 bg-green-100 text-green-800 px-6 py-3 rounded-xl text-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>Este trámite ya ha sido aprobado</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    #modalAprobacion {
        display: none;
    }
    
    #modalAprobacion .fixed {
        animation: fadeIn 0.3s ease-out;
    }
    
    #modalAprobacion .inline-block {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideIn {
        from { 
            opacity: 0;
            transform: translate3d(0, 100%, 0);
        }
        to { 
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
</style>
@endpush

    @push('scripts')
    <script>
        // Crear el modal de forma dinámica solo cuando se necesite
        let modalAprobacion = null;

        function crearModal() {
            if (!modalAprobacion) {
                modalAprobacion = document.createElement('div');
                modalAprobacion.id = 'modalAprobacion';
                modalAprobacion.style.display = 'none';
                modalAprobacion.innerHTML = `
                    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <!-- Overlay de fondo -->
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                <div>
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-[#9d2449]/10">
                                        <i class="fas fa-check-circle text-[#9d2449] text-2xl"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-5">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                            Aprobar Trámite
                                        </h3>
                                        <div class="mt-4">
                                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                <div class="flex items-center justify-center">
                                                    <div class="text-center">
                                                        <i class="fas fa-spinner fa-spin text-[#9d2449] text-xl mb-2"></i>
                                                        <p class="text-sm text-gray-600">
                                                            Obteniendo número de proveedor...
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                    <button type="button" 
                                            onclick="confirmarAprobacion()"
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#9d2449] text-base font-medium text-white hover:bg-[#7a1d3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] sm:col-start-2 sm:text-sm">
                                        Confirmar Aprobación
                                    </button>
                                    <button type="button" 
                                            onclick="cerrarModalAprobacion()"
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] sm:mt-0 sm:col-start-1 sm:text-sm">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modalAprobacion);
            }
            return modalAprobacion;
        }

        async function mostrarModalAprobacion() {
            try {
                // Verificar si el trámite ya está aprobado
                if ('{{ $tramite->estado }}' === 'Aprobado') {
                    mostrarNotificacion('error', 'Este trámite ya ha sido aprobado');
                    return;
                }

                // Crear el modal si no existe
                const modal = crearModal();

                // Obtener el siguiente PV antes de mostrar el modal
                const responsePV = await fetch('/revision/get-next-pv', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!responsePV.ok) {
                    throw new Error('Error al obtener el siguiente número PV');
                }

                const dataPV = await responsePV.json();
                const nextPV = dataPV.numero_pv;

                // Actualizar el contenido del modal con el PV
                const modalContent = modal.querySelector('.bg-gray-50');
                if (modalContent) {
                    modalContent.innerHTML = `
                        <div class="flex items-center justify-center flex-col">
                            <i class="fas fa-info-circle text-[#9d2449] text-xl mb-2"></i>
                            <p class="text-sm text-gray-600 mb-2">
                                Al aprobar el trámite se asignará el siguiente número de proveedor:
                            </p>
                            <p class="text-2xl font-bold text-[#9d2449] mb-2">${nextPV}</p>
                            <p class="text-xs text-gray-500">
                                Este número es único y será asignado permanentemente al proveedor
                            </p>
                        </div>
                        <input type="hidden" id="nextPV" value="${nextPV}">
                    `;
                }

                modal.style.display = 'block';
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('error', 'Error al obtener el número de proveedor');
            }
        }

        function cerrarModalAprobacion() {
            const modal = document.getElementById('modalAprobacion');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Función para aprobar documento después del cotejo
        async function aprobarDocumento(documentoId) {
            const cotejado = document.getElementById(`cotejado_${documentoId}`).checked;
            const comentario = document.getElementById(`comentario_doc_${documentoId}`).value;
            const coincideDigital = document.getElementById(`coincide_digital_${documentoId}`).checked;
            const coincideFisico = document.getElementById(`coincide_fisico_${documentoId}`).checked;

            if (!cotejado) {
                alert('Debe marcar el documento como cotejado físicamente antes de aprobarlo.');
                return;
            }

            if (!coincideDigital || !coincideFisico) {
                alert('Debe verificar que el documento coincide tanto con la versión digital como con los requisitos físicos.');
                return;
            }

            try {
                const response = await fetch(`/revision/{{ $tramite->id }}/documento/${documentoId}/aprobar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        comentario: comentario || 'Documento verificado y cotejado correctamente',
                        documento_cotejado: true,
                        cotejo_presencial: true,
                        observaciones_cotejo: `Documento verificado físicamente. ${comentario}`,
                        fecha_cotejo: new Date().toISOString()
                    })
                });

                const data = await response.json();
                if (data.success) {
                    actualizarEstadoDocumento(documentoId, 'Aprobado', comentario);
                    mostrarNotificacion('success', 'Documento aprobado y cotejado correctamente');
                } else {
                    mostrarNotificacion('error', data.message || 'Error al aprobar el documento');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('error', 'Error de conexión al aprobar el documento');
            }
        }

        // Función para rechazar documento
        async function rechazarDocumento(documentoId) {
            const comentario = document.getElementById(`comentario_doc_${documentoId}`).value;
            const coincideDigital = document.getElementById(`coincide_digital_${documentoId}`).checked;
            const coincideFisico = document.getElementById(`coincide_fisico_${documentoId}`).checked;
            
            if (!comentario || comentario.trim().length < 10) {
                mostrarNotificacion('error', 'Debe proporcionar un comentario detallado explicando por qué se rechaza el documento');
                return;
            }

            try {
                const response = await fetch(`/revision/{{ $tramite->id }}/documento/${documentoId}/rechazar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        comentario: comentario,
                        documento_cotejado: false,
                        cotejo_presencial: false,
                        observaciones_cotejo: `Documento rechazado en cotejo físico. ${comentario}`,
                        fecha_cotejo: new Date().toISOString()
                    })
                });

                const data = await response.json();
                if (data.success) {
                    actualizarEstadoDocumento(documentoId, 'Rechazado', comentario);
                    mostrarNotificacion('success', 'Documento rechazado correctamente');
                } else {
                    mostrarNotificacion('error', data.message || 'Error al rechazar el documento');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('error', 'Error de conexión al rechazar el documento');
            }
        }

        // Función para mostrar notificaciones
        function mostrarNotificacion(tipo, mensaje) {
            const notificacion = document.createElement('div');
            notificacion.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
                tipo === 'success' ? 'bg-green-100 border border-green-200 text-green-800' : 'bg-red-100 border border-red-200 text-red-800'
            }`;
            
            notificacion.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-2"></i>
                    <span>${mensaje}</span>
                </div>
            `;
            
            document.body.appendChild(notificacion);
            
            setTimeout(() => {
                notificacion.remove();
            }, 5000);
        }

        // Función para actualizar el estado visual del documento
        function actualizarEstadoDocumento(documentoId, nuevoEstado, comentario) {
            const documentoContainer = document.getElementById(`cotejado_${documentoId}`).closest('.bg-white');
            
            if (!documentoContainer) return;

            // Actualizar clases del contenedor
            documentoContainer.classList.remove('border-gray-300', 'border-blue-300', 'border-red-300', 'border-green-300');
            documentoContainer.classList.remove('bg-gray-50', 'bg-blue-50', 'bg-red-50', 'bg-green-50');
            
            if (nuevoEstado === 'Aprobado') {
                documentoContainer.classList.add('border-green-300', 'bg-green-50');
            } else if (nuevoEstado === 'Rechazado') {
                documentoContainer.classList.add('border-red-300', 'bg-red-50');
            }
            
            // Actualizar badge de estado
            const estadoSpan = documentoContainer.querySelector('.px-3.py-1');
            if (estadoSpan) {
                estadoSpan.className = `px-3 py-1 ${
                    nuevoEstado === 'Aprobado' 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'
                } text-xs font-medium rounded-full`;
                estadoSpan.innerHTML = `<i class="fas ${
                    nuevoEstado === 'Aprobado' ? 'fa-check' : 'fa-times'
                } mr-1"></i>${nuevoEstado}`;
            }

            // Actualizar controles
            const checkbox = document.getElementById(`cotejado_${documentoId}`);
            const textarea = document.getElementById(`comentario_doc_${documentoId}`);
            const botonesAccion = documentoContainer.querySelectorAll('button[onclick*="aprobarDocumento"], button[onclick*="rechazarDocumento"]');
            
            if (checkbox) checkbox.disabled = true;
            if (textarea) {
                textarea.value = comentario;
                textarea.disabled = true;
            }
            
            botonesAccion.forEach(boton => {
                boton.disabled = true;
                boton.classList.add('opacity-50', 'cursor-not-allowed');
            });
        }

        // Agregar checkboxes de verificación al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const documentos = document.querySelectorAll('[id^="cotejado_"]');
            documentos.forEach(doc => {
                const documentoId = doc.id.split('_')[1];
                const container = doc.closest('.bg-white');
                if (!container) return;

                const checkboxesContainer = document.createElement('div');
                checkboxesContainer.className = 'mt-4 space-y-2 border-t pt-4';
                checkboxesContainer.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="coincide_digital_${documentoId}" class="rounded text-[#9d2449] focus:ring-[#9d2449]">
                        <label for="coincide_digital_${documentoId}" class="text-sm text-gray-700">
                            Coincide con la versión digital
                        </label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="coincide_fisico_${documentoId}" class="rounded text-[#9d2449] focus:ring-[#9d2449]">
                        <label for="coincide_fisico_${documentoId}" class="text-sm text-gray-700">
                            Cumple con los requisitos físicos (sellos, firmas, etc.)
                        </label>
                    </div>
                `;

                // Insertar antes del área de comentarios
                const comentariosArea = container.querySelector('textarea');
                if (comentariosArea) {
                    comentariosArea.parentNode.insertBefore(checkboxesContainer, comentariosArea);
                }
            });
        });

        // Función para aprobar el trámite completo y generar oficio
        async function confirmarAprobacion() {
            try {
                // Obtener el PV del input oculto
                const nextPV = document.getElementById('nextPV').value;
                if (!nextPV) {
                    throw new Error('No se encontró el número de proveedor');
                }

                // Mostrar indicador de carga
                const btnConfirmar = document.querySelector('button[onclick="confirmarAprobacion()"]');
                const btnCancelar = document.querySelector('button[onclick="cerrarModalAprobacion()"]');
                btnConfirmar.disabled = true;
                btnCancelar.disabled = true;
                btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

                // Aprobar el trámite y crear el proveedor
                const responseAprobacion = await fetch('{{ route("revision.aprobar", ["tramite" => $tramite->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        estado: 'Aprobado',
                        fecha_aprobacion: new Date().toISOString(),
                        numero_pv: nextPV
                    })
                });

                // Verificar si la respuesta es JSON
                const contentType = responseAprobacion.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("La respuesta del servidor no es JSON válido");
                }

                let dataAprobacion;
                try {
                    dataAprobacion = await responseAprobacion.json();
                } catch (error) {
                    console.error('Error al parsear JSON:', error);
                    throw new Error('Error al procesar la respuesta del servidor');
                }

                if (!responseAprobacion.ok || !dataAprobacion.success) {
                    const mensaje = dataAprobacion.message || `Error al aprobar el trámite (${responseAprobacion.status})`;
                    console.error('Error en la respuesta:', {
                        status: responseAprobacion.status,
                        data: dataAprobacion
                    });
                    throw new Error(mensaje);
                }

                // Si la aprobación fue exitosa, generar el oficio
                const responseOficio = await fetch('{{ route("revision.generar-oficio", ["tramite" => $tramite->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tipo_oficio: '{{ $tramite->tipo_tramite }}',
                        proveedor_pv: nextPV
                    })
                });

                // Verificar si la respuesta es JSON
                const contentTypeOficio = responseOficio.headers.get("content-type");
                if (!contentTypeOficio || !contentTypeOficio.includes("application/json")) {
                    throw new Error("La respuesta del servidor para el oficio no es JSON válido");
                }

                let dataOficio;
                try {
                    dataOficio = await responseOficio.json();
                } catch (error) {
                    console.error('Error al parsear JSON del oficio:', error);
                    throw new Error('Error al procesar la respuesta del servidor para el oficio');
                }

                if (!responseOficio.ok || !dataOficio.success) {
                    const mensaje = dataOficio.message || `Error al generar oficio (${responseOficio.status})`;
                    console.error('Error en la respuesta del oficio:', {
                        status: responseOficio.status,
                        data: dataOficio
                    });
                    throw new Error(mensaje);
                }
                if (dataOficio.success) {
                    // Cerrar modal
                    cerrarModalAprobacion();

                    // Mostrar modal de éxito con Tailwind
                    const modalExito = document.createElement('div');
                    modalExito.className = 'fixed inset-0 z-50 overflow-y-auto';
                    modalExito.innerHTML = `
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                <div>
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                        <i class="fas fa-check text-green-600 text-xl"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-5">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900">
                                            Trámite Aprobado Exitosamente
                                        </h3>
                                        <div class="mt-4">
                                            <div class="bg-gray-50 rounded-lg p-4 text-left space-y-2">
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-semibold">Número de Proveedor:</span>
                                                    <span class="text-[#9d2449] font-bold">${dataAprobacion.proveedor_pv}</span>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-semibold">Número de Oficio:</span>
                                                    <span class="text-[#9d2449] font-bold">${dataOficio.numero_oficio}</span>
                                                </p>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500">
                                                El oficio ha sido generado y guardado en el sistema.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-6">
                                    <button type="button"
                                            onclick="window.location.href='/revision'"
                                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#9d2449] text-base font-medium text-white hover:bg-[#7a1d3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] sm:text-sm transition-colors duration-200">
                                        Volver a Revisiones
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modalExito);
                } else {
                    throw new Error(dataOficio.message || 'Error al generar el oficio');
                }
            } catch (error) {
                console.error('Error:', error);
                // Restaurar botones
                const btnConfirmar = document.querySelector('button[onclick="confirmarAprobacion()"]');
                const btnCancelar = document.querySelector('button[onclick="cerrarModalAprobacion()"]');
                btnConfirmar.disabled = false;
                btnCancelar.disabled = false;
                btnConfirmar.innerHTML = 'Confirmar Aprobación';
                // Mostrar error
                mostrarNotificacion('error', error.message || 'Error en el proceso de aprobación');
            }
        }

        // Función para cancelar el trámite
        async function cancelarTramite() {
            const motivo = prompt('Por favor, ingrese el motivo de la cancelación:');
            if (!motivo) return;

            try {
                const response = await fetch(`/tramites/{{ $tramite->id }}/cancelar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        motivo_cancelacion: motivo
                    })
                });

                const data = await response.json();
                if (data.success) {
                    mostrarNotificacion('success', 'Trámite cancelado exitosamente');
                    setTimeout(() => {
                        window.location.href = '/revision';
                    }, 2000);
                } else {
                    mostrarNotificacion('error', data.message || 'Error al cancelar el trámite');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarNotificacion('error', 'Error de conexión al cancelar el trámite');
            }
        }
    </script>
    @endpush
@endsection 