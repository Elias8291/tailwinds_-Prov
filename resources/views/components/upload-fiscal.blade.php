@props(['label' => 'Constancia de Situación Fiscal'])

<div class="card-custom rounded-xl p-4 md:p-6">
    <div class="flex items-center mb-4">
        <svg class="w-6 h-6 text-[#B4325E] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-800">{{ $label }}</h3>
    </div>
    
    <div class="upload-area rounded-lg p-4 text-center cursor-pointer">
        <input type="file" accept=".pdf" class="hidden" id="fiscal-doc" {{ $attributes }}>
        <label for="fiscal-doc" class="cursor-pointer">
            <svg class="w-8 h-8 text-[#B4325E] mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span class="text-sm text-gray-600">Haga clic para subir su constancia en PDF</span>
        </label>
    </div>

    @error('fiscal_document')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
document.getElementById('fiscal-doc').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        const fileName = e.target.files[0].name;
        const fileSize = (e.target.files[0].size / 1024 / 1024).toFixed(2);
        e.target.nextElementSibling.querySelector('span').textContent = `${fileName} (${fileSize} MB)`;
    }
});
</script> 