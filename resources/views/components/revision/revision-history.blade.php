@props(['history' => []])

<div id="historial-revisiones" class="hidden">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Historial de Revisiones
    </label>
    <div class="bg-white border border-gray-200 rounded-lg p-3 max-h-32 overflow-y-auto">
        <div id="lista-historial" class="space-y-2 text-xs text-gray-600">
            @forelse($history as $revision)
                <div class="revision-item">{{ $revision }}</div>
            @empty
                <div class="text-gray-400">No hay revisiones previas</div>
            @endforelse
        </div>
    </div>
</div> 