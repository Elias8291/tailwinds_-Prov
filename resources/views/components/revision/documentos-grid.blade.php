<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($documentos as $doc)
        <div class="bg-white rounded-xl border border-gray-200 p-5 transition-all duration-300 hover:shadow-lg hover:border-rose-200 group">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 flex-shrink-0 bg-rose-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-2xl text-rose-400"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 group-hover:text-[#9d2449] transition-colors">
                            {{ $doc['nombre'] }}
                        </p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap"
                    :class="{
                        'bg-green-100 text-green-800': '{{ strtolower($doc['estado']) }}' === 'aprobado',
                        'bg-red-100 text-red-800': '{{ strtolower($doc['estado']) }}' === 'rechazado',
                        'bg-yellow-100 text-yellow-800': '{{ strtolower($doc['estado']) }}' !== 'aprobado' && '{{ strtolower($doc['estado']) }}' !== 'rechazado'
                    }">
                    {{ ucfirst($doc['estado']) }}
                </span>
            </div>
            <div class="mt-4 flex items-center justify-end">
                @if($doc['ruta_archivo'])
                    <a href="{{ Storage::url($doc['ruta_archivo']) }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-[#9d2449] text-white text-sm font-semibold rounded-lg shadow-md hover:bg-[#7a1d3a] transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-[#9d2449] focus:ring-offset-2">
                        <i class="fas fa-eye mr-2"></i> Ver
                    </a>
                @else
                     <span class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-500 text-sm font-semibold rounded-lg cursor-not-allowed">
                        <i class="fas fa-eye-slash mr-2"></i> No disponible
                    </span>
                @endif
            </div>
        </div>
    @empty
        <div class="sm:col-span-2 lg:col-span-3 text-center py-16 bg-white rounded-xl border border-dashed">
            <i class="fas fa-file-excel text-5xl text-gray-300"></i>
            <p class="mt-4 text-gray-500">No hay documentos requeridos en esta sección.</p>
        </div>
    @endforelse
</div> 