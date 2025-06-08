<x-app-layout>
    @props(['currentStep' => 1, 'totalSteps' => 4])

    <div class="w-full py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Progress" class="mb-8">
                <ol role="list" class="space-y-4 md:flex md:space-y-0 md:space-x-8">
                    @for ($step = 1; $step <= $totalSteps; $step++)
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
                                            Información General
                                            @break
                                        @case(2)
                                            Documentos
                                            @break
                                        @case(3)
                                            Información Adicional
                                            @break
                                        @case(4)
                                            Revisión
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
</x-app-layout> 