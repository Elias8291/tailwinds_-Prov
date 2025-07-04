@props(['initialPriority' => 'media'])

<div>
    <label for="prioridad-documento" class="block text-sm font-medium text-gray-700 mb-2">
        Prioridad del Comentario
    </label>
    <select id="prioridad-documento" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#B4325E] focus:border-[#B4325E]">
        <option value="baja" {{ $initialPriority === 'baja' ? 'selected' : '' }}>🟢 Baja - Observación menor</option>
        <option value="media" {{ $initialPriority === 'media' ? 'selected' : '' }}>🟡 Media - Requiere atención</option>
        <option value="alta" {{ $initialPriority === 'alta' ? 'selected' : '' }}>🔴 Alta - Problema crítico</option>
    </select>
</div> 