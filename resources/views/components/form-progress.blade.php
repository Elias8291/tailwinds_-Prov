@props(['currentStep' => 1, 'totalSteps' => 6, 'progress' => 0, 'sections' => []])

<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Form Container with Glass Effect -->
    <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 mt-24 mb-6">
        <!-- Progress Container -->
        <div class="max-w-3xl mx-auto mb-12">
            <!-- Progress Info -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-[#9D2449]">{{ $progress }}%</span>
                        <span class="text-sm text-gray-500 uppercase tracking-wider">Completado</span>
                    </div>
                    @if(isset($tipoPersona))
                    <div class="text-sm text-gray-600">
                        <span>Formulario para persona:</span>
                        <span class="font-semibold">{{ $tipoPersona }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Mobile Step Indicator -->
                <div class="flex items-center gap-2 sm:hidden">
                    <span class="text-sm text-gray-500">Paso</span>
                    <span class="w-8 h-8 flex items-center justify-center rounded-full bg-[#9D2449] text-white font-semibold">
                        {{ $currentStep }}
                    </span>
                    <span class="text-sm text-gray-500">de {{ $totalSteps }}</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="relative h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="absolute top-0 left-0 h-full bg-[#9D2449] transition-all duration-500 ease-out rounded-full"
                     style="width: {{ $progress }}%">
                </div>
            </div>

            <!-- Desktop Steps -->
            <div class="hidden sm:block mt-8">
                <div class="relative flex justify-between">
                    <!-- Connecting Line -->
                    <div class="absolute top-5 left-0 w-full h-0.5 bg-gray-200">
                        <div class="h-full bg-[#9D2449] transition-all duration-500" 
                             style="width: {{ ($currentStep - 1) / ($totalSteps - 1) * 100 }}%">
                        </div>
                    </div>

                    <!-- Steps -->
                    @foreach($sections as $index => $title)
                    <div class="relative flex flex-col items-center">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full border-2 
                                  {{ $index + 1 <= $currentStep 
                                     ? 'bg-[#9D2449] border-[#9D2449] text-white' 
                                     : 'bg-white border-gray-300 text-gray-500' }} 
                                  font-semibold text-sm transition-all duration-300">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <span class="absolute top-12 text-xs font-medium text-gray-500 whitespace-nowrap 
                                   transform -translate-x-1/2 left-1/2 text-center max-w-[120px]">
                            {{ $title }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Form Content -->
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            {{ $slot }}
        </div>
    </div>
</div> 