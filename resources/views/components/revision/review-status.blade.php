@props(['initialStatus' => 'pendiente'])

<div>
    <label class="block text-sm font-medium text-gray-700 mb-3">Estado de Revisión</label>
    <div class="space-y-2">
        <label class="flex items-center">
            <input type="radio" name="estado-documento" value="pendiente" class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300" {{ $initialStatus === 'pendiente' ? 'checked' : '' }}>
            <span class="ml-2 text-sm text-gray-700">⏳ Pendiente de revisión</span>
        </label>
        <label class="flex items-center">
            <input type="radio" name="estado-documento" value="aprobado" class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300" {{ $initialStatus === 'aprobado' ? 'checked' : '' }}>
            <span class="ml-2 text-sm text-gray-700">✅ Aprobado</span>
        </label>
        <label class="flex items-center">
            <input type="radio" name="estado-documento" value="rechazado" class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300" {{ $initialStatus === 'rechazado' ? 'checked' : '' }}>
            <span class="ml-2 text-sm text-gray-700">❌ Rechazado</span>
        </label>
        <label class="flex items-center">
            <input type="radio" name="estado-documento" value="correccion" class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300" {{ $initialStatus === 'correccion' ? 'checked' : '' }}>
            <span class="ml-2 text-sm text-gray-700">📝 Requiere corrección</span>
        </label>
    </div>
</div> 