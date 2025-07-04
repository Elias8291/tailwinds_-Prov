@props(['initialComment' => ''])

<div>
    <label for="comentario-documento" class="block text-sm font-medium text-gray-700 mb-2">
        Comentarios de Revisión
    </label>
    <textarea 
        id="comentario-documento" 
        rows="4" 
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#B4325E] focus:border-[#B4325E] resize-none"
        placeholder="Agregue sus observaciones sobre este documento...">{{ $initialComment }}</textarea>
</div> 