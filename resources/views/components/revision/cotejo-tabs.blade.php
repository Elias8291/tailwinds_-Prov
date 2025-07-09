@php
    // Reuse the earlier tabbed markup for mobile fallback
@endphp

<!-- Desktop Split-Pane (md and up) -->
<div x-data="{
        leftWidth: 50,
        isDragging: false,
        documentVisible: false,
        startDrag(event) {
            this.isDragging = true;
            const moveHandler = (e) => {
                if (!this.isDragging) return;
                const containerRect = this.$el.getBoundingClientRect();
                let newLeftWidth = ((e.clientX - containerRect.left) / containerRect.width) * 100;
                if (newLeftWidth < 25) newLeftWidth = 25;
                if (newLeftWidth > 75) newLeftWidth = 75;
                this.leftWidth = newLeftWidth;
            };
            const stopHandler = () => {
                this.isDragging = false;
                window.removeEventListener('mousemove', moveHandler);
                window.removeEventListener('mouseup', stopHandler);
                window.removeEventListener('mouseleave', stopHandler);
            };
            window.addEventListener('mousemove', moveHandler);
            window.addEventListener('mouseup', stopHandler);
            window.addEventListener('mouseleave', stopHandler);
        }
    }" 
    class="hidden md:flex w-full border border-gray-200 rounded-lg overflow-hidden bg-white">
    <!-- Left Panel -->
    <div class="relative transition-all duration-300"
         :style="{ width: documentVisible ? leftWidth + '%' : '100%' }">
        <div class="sticky top-0 bg-white/80 backdrop-blur-sm p-4 lg:p-6 border-b border-gray-200 z-10 flex justify-between items-center">
            <h3 class="text-base lg:text-lg font-bold text-gray-800">
                <i class="fas fa-file-invoice mr-2"></i> Datos del Formulario
            </h3>
            <button @click="documentVisible = !documentVisible"
                    class="text-sm font-medium text-[#9d2449] hover:text-[#7a1d3a] px-3 py-1 bg-rose-50 rounded-lg flex items-center">
                <span x-show="documentVisible" class="flex items-center"><i class="fas fa-eye-slash mr-2"></i>Ocultar Documento</span>
                <span x-show="!documentVisible" class="flex items-center"><i class="fas fa-eye mr-2"></i>Mostrar Documento</span>
            </button>
        </div>
        <div class="px-4 lg:px-6 pb-6">
            @if($formulario)
                <div class="prose max-w-none">
                    {!! $formulario !!}
                </div>
            @else
                <div class="text-center py-12 text-gray-500 h-full flex items-center justify-center">
                    <div>
                        <i class="fas fa-info-circle text-4xl text-gray-300"></i>
                        <p class="mt-4">No hay datos de formulario para esta sección.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Divider -->
    <div x-show="documentVisible" x-transition @mousedown.prevent="startDrag"
         class="w-1.5 flex-shrink-0 bg-gray-200 hover:bg-[#9d2449] cursor-col-resize transition-colors duration-200"></div>

    <!-- Right Panel -->
    <div x-show="documentVisible" x-transition class="flex-1 flex flex-col min-w-0">
        <h3 class="text-base lg:text-lg font-bold text-gray-800 flex-shrink-0 bg-white p-4 lg:p-6 border-b border-gray-200">
            <i class="fas fa-file-pdf mr-2"></i> Documento Adjunto
        </h3>
        <div class="flex-grow p-4 lg:p-6 bg-gray-50">
            @if(!empty($documento['ruta_archivo']))
                <div class="w-full rounded-md overflow-hidden border border-gray-300">
                    <iframe src="{{ Storage::url($documento['ruta_archivo']) }}" width="100%" class="bg-white h-screen"></iframe>
                </div>
            @else
                <div class="h-full flex items-center justify-center text-center bg-gray-100 rounded-lg">
                    <div>
                        <i class="fas fa-file-excel text-4xl text-gray-400"></i>
                        <p class="mt-4">No hay un documento cargado para esta sección.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Mobile Tabs (below md) -->
<div x-data="{ activeTab: 'form' }" class="md:hidden w-full border border-gray-200 rounded-lg overflow-hidden bg-white">
    <!-- Tabs -->
    <div class="border-b border-gray-200 bg-gray-50">
        <nav class="flex">
            @if($formulario)
            <button @click="activeTab='form'" :class="activeTab==='form' ? 'text-[#9d2449] border-b-2 border-[#9d2449]' : 'text-gray-600 hover:text-gray-800'" class="w-full py-3 text-sm font-medium flex items-center justify-center gap-2">
                <i class="fas fa-file-invoice"></i><span>Formulario</span>
            </button>
            @endif
            @if(!empty($documento))
            <button @click="activeTab='doc'" :class="activeTab==='doc' ? 'text-[#9d2449] border-b-2 border-[#9d2449]' : 'text-gray-600 hover:text-gray-800'" class="w-full py-3 text-sm font-medium flex items-center justify-center gap-2">
                <i class="fas fa-file-pdf"></i><span>Documento</span>
            </button>
            @endif
        </nav>
    </div>
    <!-- Content -->
    <div class="p-4 bg-white">
        <!-- Formulario tab -->
        <div x-show="activeTab==='form'">
            @if($formulario)
                <div class="prose max-w-none">
                    {!! $formulario !!}
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-3xl"></i>
                    <p class="mt-2">No hay datos de formulario.</p>
                </div>
            @endif
        </div>
        <!-- Documento tab -->
        <div x-show="activeTab==='doc'">
            @if(!empty($documento['ruta_archivo']))
                <div class="w-full h-[500px] rounded-md overflow-hidden border border-gray-300">
                    <iframe src="{{ Storage::url($documento['ruta_archivo']) }}" width="100%" height="100%" class="bg-white"></iframe>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-file-excel text-3xl"></i>
                    <p class="mt-2">No hay documento.</p>
                </div>
            @endif
        </div>
    </div>
</div> 