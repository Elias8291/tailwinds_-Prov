<div class="p-6">
    <div class="overflow-hidden">
        <!-- Vista móvil -->
        <div class="block md:hidden">
            @foreach ($items as $item)
                <div
                    class="p-4 border-b border-gray-100 table-row-animate hover:bg-gray-50/50 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div
                                class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-primary to-primary-dark  text-white rounded-xl shadow-md flex items-center justify-center font-bold text-xl">
                                {{ strtoupper(substr(data_get($item, $columns[0]['field']), 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ data_get($item, $columns[0]['field']) }}
                                </div>
                                <div class="text-xs text-gray-500">Creado hace un momento</div>
                            </div>
                        </div>
                        @if (isset($actions))
                            <div class="flex items-center space-x-2">
                                {{ $actions($item) }}
                            </div>
                        @endif
                    </div>
                    <div class="mt-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($columns as $index => $col)
                                @if ($index !== 0)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 shadow-sm hover:bg-blue-100 transition-colors duration-200">
                                        {{ data_get($item, $col['field']) }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Vista desktop -->
        <div class="hidden md:block">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 shadow-sm">
                <thead>
                    <tr style="background: linear-gradient(to right, #B4325E, #93264B);" class="text-white">
                        @foreach ($columns as $col)
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs uppercase tracking-wider border-r border-gray-200/30">
                                {{ $col['label'] }}
                            </th>
                        @endforeach
                        @if (isset($actions))
                            <th scope="col" class="px-6 py-4 text-center text-xs uppercase tracking-wider">
                                Acciones
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($items as $item)
                        <tr class="hover:bg-gray-50/50 transition-all duration-200 table-row-animate">
                            @foreach ($columns as $index => $col)
                                <td class="px-6 py-4 border-r border-gray-200">
                                    @if ($index === 0)
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-xl shadow-md flex items-center justify-center font-bold text-xl">
                                                {{ strtoupper(substr(data_get($item, $col['field']), 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm text-gray-900">{{ data_get($item, $col['field']) }}
                                                </div>
                                                <div class="text-xs text-gray-500">Creado hace un momento</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex flex-wrap gap-2">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 shadow-sm hover:bg-blue-100 transition-colors duration-200">
                                                {{ data_get($item, $col['field']) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            @if (isset($actions))
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <div class="flex items-center justify-center space-x-3">
                                        {{ $actions($item) }}
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>




<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }

    .table-row-animate {
        opacity: 0;
        animation: fadeIn 0.3s ease-out forwards;
    }

    .table-row-animate:nth-child(1) {
        animation-delay: 0.1s;
    }

    .table-row-animate:nth-child(2) {
        animation-delay: 0.2s;
    }

    .table-row-animate:nth-child(3) {
        animation-delay: 0.3s;
    }

    .table-row-animate:nth-child(4) {
        animation-delay: 0.4s;
    }

    .table-row-animate:nth-child(5) {
        animation-delay: 0.5s;
    }
</style>
