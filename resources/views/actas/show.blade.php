                <!-- Formulario de Revisión -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Agregar Revisión</h3>
                    <form action="{{ route('revisiones.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="acta_id" value="{{ $acta->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="fecha_revision" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Revisión</label>
                                <input type="date" name="fecha_revision" id="fecha_revision" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select name="estado" id="estado" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="En Proceso">En Proceso</option>
                                    <option value="Completado">Completado</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="4" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Agregar Revisión
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Lista de Revisiones -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Revisiones</h3>
                    @if($acta->revisiones->count() > 0)
                        <div class="space-y-4">
                            @foreach($acta->revisiones as $revision)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                                                @if($revision->estado === 'Completado') bg-green-100 text-green-800
                                                @elseif($revision->estado === 'En Proceso') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $revision->estado }}
                                            </span>
                                            <span class="text-sm text-gray-500 ml-2">
                                                {{ \Carbon\Carbon::parse($revision->fecha_revision)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <form action="{{ route('revisiones.destroy', $revision) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="text-red-600 hover:text-red-800 focus:outline-none"
                                                onclick="return confirm('¿Está seguro de eliminar esta revisión?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <p class="text-gray-700">{{ $revision->observaciones }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No hay revisiones registradas.</p>
                    @endif
                </div>

                <!-- Datos Generales -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                    <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4">01</span>
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-3 text-xl"></i>
                                <span>Datos Generales</span>
                            </div>
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Nombre del Trámite</label>
                                <input type="text" value="{{ $acta->nombre_tramite }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Número de Expediente</label>
                                <input type="text" value="{{ $acta->numero_expediente }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Fecha de Presentación</label>
                                <input type="text" value="{{ $acta->fecha_presentacion }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Estado Actual</label>
                                <input type="text" value="{{ $acta->estado_actual }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos de Contacto -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                    <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4">02</span>
                            <div class="flex items-center">
                                <i class="fas fa-address-card mr-3 text-xl"></i>
                                <span>Datos de Contacto</span>
                            </div>
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Nombre del Solicitante</label>
                                <input type="text" value="{{ $acta->nombre_solicitante }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Teléfono</label>
                                <input type="text" value="{{ $acta->telefono }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                            <div class="space-y-3 md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700">Correo Electrónico</label>
                                <input type="email" value="{{ $acta->correo_electronico }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Domicilio -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                    <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4">03</span>
                            <div class="flex items-center">
                                <i class="fas fa-home mr-3 text-xl"></i>
                                <span>Domicilio</span>
                            </div>
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Calle y Número</label>
                                <input type="text" value="{{ $acta->calle_numero }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Colonia</label>
                                <input type="text" value="{{ $acta->colonia }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Código Postal</label>
                                <input type="text" value="{{ $acta->codigo_postal }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Municipio</label>
                                <input type="text" value="{{ $acta->municipio }}" readonly
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documentos Requeridos -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                    <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4">04</span>
                            <div class="flex items-center">
                                <i class="fas fa-file-alt mr-3 text-xl"></i>
                                <span>Documentos Requeridos</span>
                            </div>
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="space-y-8">
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Documentos Presentados</label>
                                <textarea readonly rows="3"
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">{{ $acta->documentos_presentados }}</textarea>
                            </div>
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Documentos Faltantes</label>
                                <textarea readonly rows="3"
                                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">{{ $acta->documentos_faltantes }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comentarios Generales -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
                    <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <span class="bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4">05</span>
                            <div class="flex items-center">
                                <i class="fas fa-comments mr-3 text-xl"></i>
                                <span>Comentarios Generales</span>
                            </div>
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="space-y-3">
                            <textarea readonly rows="4"
                                class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#9d2449]/20 shadow-sm">{{ $acta->comentarios_generales }}</textarea>
                        </div>
                    </div>
                </div> 
