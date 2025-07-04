@props(['name' => '', 'version' => 'v1'])

<div class="p-4 border-b border-gray-200 bg-white">
    <div class="space-y-3">
        <div>
            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</label>
            <p id="documento-nombre-revision" class="text-sm font-medium text-gray-900 mt-1">{{ $name ?: '-' }}</p>
        </div>
        <div>
            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Actual</label>
            <div class="mt-1">
                <span id="documento-estado-actual" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    ⏳ Pendiente
                </span>
            </div>
        </div>
        <div>
            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Versión</label>
            <p id="documento-version" class="text-sm text-gray-700 mt-1">{{ $version }}</p>
        </div>
    </div>
</div> 