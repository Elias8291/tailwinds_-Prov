@props(['currentStep' => 1, 'solicitante' => null])

<div class="min-h-screen py-6">
    <!-- Barra de Progreso -->
    <div class="w-full py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Progress" class="mb-8">
                <ol role="list" class="space-y-4 md:flex md:space-y-0 md:space-x-8">
                    @for ($step = 1; $step <= 4; $step++)
                        <li class="md:flex-1">
                            <div class="group flex flex-col border-l-4 border-[#9d2449] py-2 pl-4 md:border-l-0 md:border-t-4 md:pl-0 md:pt-4 md:pb-0
                                {{ $step < $currentStep ? 'border-[#9d2449]' : ($step === $currentStep ? 'border-[#9d2449]' : 'border-gray-200') }}">
                                <span class="text-sm font-medium
                                    {{ $step < $currentStep ? 'text-[#9d2449]' : ($step === $currentStep ? 'text-[#9d2449]' : 'text-gray-500') }}">
                                    Paso {{ $step }}
                                </span>
                                <span class="text-sm font-medium
                                    {{ $step < $currentStep ? 'text-gray-500' : ($step === $currentStep ? 'text-gray-900' : 'text-gray-500') }}">
                                    @switch($step)
                                        @case(1)
                                            Datos Generales
                                            @break
                                        @case(2)
                                            Documentación
                                            @break
                                        @case(3)
                                            Información Fiscal
                                            @break
                                        @case(4)
                                            Revisión Final
                                            @break
                                    @endswitch
                                </span>
                            </div>
                        </li>
                    @endfor
                </ol>
            </nav>
        </div>
    </div>

    <!-- Formulario del Trámite -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('tramites.store') }}" 
              method="POST" 
              enctype="multipart/form-data">
            @csrf

            <!-- Sección 1: Datos Generales -->
            @if($currentStep === 1)
            <x-form-section 
                title="Datos Generales" 
                description="Información básica del solicitante">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-field
                        label="Nombre o Razón Social"
                        name="nombre"
                        value="{{ old('nombre', optional($solicitante)->nombre) }}"
                        required
                    />

                    <x-form-field
                        label="RFC"
                        name="rfc"
                        value="{{ old('rfc', optional($solicitante)->rfc) }}"
                        required
                        helper="Registro Federal de Contribuyentes con homoclave"
                    />

                    <x-form-field
                        label="Correo Electrónico"
                        name="correo"
                        type="email"
                        value="{{ old('correo', optional($solicitante)->correo) }}"
                        required
                    />

                    <x-form-field
                        label="Teléfono"
                        name="telefono"
                        type="tel"
                        value="{{ old('telefono', optional($solicitante)->telefono) }}"
                        required
                    />
                </div>

                <x-slot name="actions">
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transform hover:scale-105 transition-all duration-300">
                        Siguiente
                    </button>
                </x-slot>
            </x-form-section>
            @endif

            <!-- Sección 2: Documentación -->
            @if($currentStep === 2)
            <x-form-section 
                title="Documentación Requerida" 
                description="Sube los documentos necesarios para el trámite">
                
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-4 space-y-4">
                        <h3 class="font-medium text-gray-900">Documentos de Identidad</h3>
                        
                        <div class="space-y-4">
                            <x-form-field
                                type="file"
                                label="Identificación Oficial"
                                name="identificacion"
                                required
                                helper="Formato PDF, máximo 2MB"
                            />

                            <x-form-field
                                type="file"
                                label="Comprobante de Domicilio"
                                name="comprobante_domicilio"
                                required
                                helper="No mayor a 3 meses de antigüedad"
                            />
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 space-y-4">
                        <h3 class="font-medium text-gray-900">Documentos Fiscales</h3>
                        
                        <div class="space-y-4">
                            <x-form-field
                                type="file"
                                label="Constancia de Situación Fiscal"
                                name="constancia_fiscal"
                                required
                                helper="Actualizada, no mayor a 3 meses"
                            />

                            <x-form-field
                                type="file"
                                label="Opinión de Cumplimiento"
                                name="opinion_cumplimiento"
                                required
                                helper="Positiva, vigente"
                            />
                        </div>
                    </div>
                </div>

                <x-slot name="actions">
                    <button type="button" 
                            onclick="window.location.href='{{ route('tramites.paso', ['step' => 1]) }}'"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]">
                        Anterior
                    </button>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transform hover:scale-105 transition-all duration-300">
                        Siguiente
                    </button>
                </x-slot>
            </x-form-section>
            @endif

            <!-- Sección 3: Información Fiscal -->
            @if($currentStep === 3)
            <x-form-section 
                title="Información Fiscal" 
                description="Datos fiscales adicionales">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-field
                        label="Régimen Fiscal"
                        name="regimen_fiscal"
                        type="select"
                        required
                    >
                        <option value="">Seleccione una opción</option>
                        <option value="601">General de Ley</option>
                        <option value="612">Personas Físicas con Actividades Empresariales</option>
                        <option value="621">Incorporación Fiscal</option>
                    </x-form-field>

                    <x-form-field
                        label="Actividad Económica"
                        name="actividad_economica"
                        type="text"
                        required
                    />

                    <div class="col-span-2">
                        <x-form-field
                            label="Dirección Fiscal"
                            name="direccion_fiscal"
                            type="textarea"
                            required
                        />
                    </div>
                </div>

                <x-slot name="actions">
                    <button type="button" 
                            onclick="window.location.href='{{ route('tramites.paso', ['step' => 2]) }}'"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]">
                        Anterior
                    </button>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transform hover:scale-105 transition-all duration-300">
                        Siguiente
                    </button>
                </x-slot>
            </x-form-section>
            @endif

            <!-- Sección 4: Revisión Final -->
            @if($currentStep === 4)
            <x-form-section 
                title="Revisión Final" 
                description="Verifica que toda la información sea correcta">
                
                <div class="space-y-6">
                    <!-- Resumen de Datos Generales -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-4">Datos Generales</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre/Razón Social</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $solicitante->nombre }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">RFC</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $solicitante->rfc }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Correo Electrónico</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $solicitante->correo }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $solicitante->telefono }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Documentos Cargados -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-4">Documentos Cargados</h3>
                        <ul class="space-y-2">
                            @foreach(['identificacion', 'comprobante_domicilio', 'constancia_fiscal', 'opinion_cumplimiento'] as $doc)
                                <li class="flex items-center text-sm">
                                    @if($solicitante->documentos->where('tipo', $doc)->first())
                                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-gray-900">{{ ucwords(str_replace('_', ' ', $doc)) }}</span>
                                    @else
                                        <svg class="h-5 w-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span class="text-red-500">{{ ucwords(str_replace('_', ' ', $doc)) }} - Faltante</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Información Fiscal -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-4">Información Fiscal</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Régimen Fiscal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $solicitante->regimen_fiscal }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Actividad Económica</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $solicitante->actividad_economica }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dirección Fiscal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $solicitante->direccion_fiscal }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <x-slot name="actions">
                    <button type="button" 
                            onclick="window.location.href='{{ route('tramites.paso', ['step' => 3]) }}'"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]">
                        Anterior
                    </button>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-[#9d2449] to-[#8a203f] hover:from-[#8a203f] hover:to-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transform hover:scale-105 transition-all duration-300">
                        Enviar Trámite
                    </button>
                </x-slot>
            </x-form-section>
            @endif
        </form>
    </div>
</div> 