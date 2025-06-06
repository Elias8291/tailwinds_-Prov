<!-- Mobile sidebar -->
<div x-show="sidebarOpen" x-cloak class="fixed inset-0 flex z-40">
    <div class="fixed inset-0 bg-gray-600/75 backdrop-blur-sm" @click="sidebarOpen = false"></div>
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gradient-to-b from-white to-gray-50">
        <div class="fixed top-16 bottom-16 left-0 w-full max-w-xs overflow-hidden">
            <div class="h-full flex flex-col">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button class="flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebarOpen = false">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 h-full overflow-y-auto">
      
                    <nav class="px-3 space-y-2">
                        <a href="#" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 bg-primary-50 text-primary border-l-4 border-primary shadow-sm">
                            <svg class="text-primary flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2V5z" />
                            </svg>
                            <span class="font-semibold tracking-wide">Dashboard</span>
                        </a>
                        <a href="#" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                            <svg class="text-gray-400 flex-shrink-0 w-6 h-6 mr-3 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="font-medium tracking-wide">Analytics</span>
                        </a>
                        <a href="#" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                            <svg class="text-gray-400 flex-shrink-0 w-6 h-6 mr-3 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 011-1h1m-1 1v1m0 0h1m-1 0v3M9 7h1m0 0v3m0-3h1m0 0v3m0-3h1m-1 0v1" />
                            </svg>
                            <span class="font-medium tracking-wide">Proyectos</span>
                        </a>
                        <a href="#" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                            <svg class="text-gray-400 flex-shrink-0 w-6 h-6 mr-3 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            <span class="font-medium tracking-wide">Equipo</span>
                        </a>
                        <a href="#" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                            <svg class="text-gray-400 flex-shrink-0 w-6 h-6 mr-3 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-medium tracking-wide">Configuración</span>
                        </a>
                        <a href="{{ route('tramites.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                            <svg class="text-gray-400 flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:text-primary group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Trámites</span>
                        </a>
                    </nav>
                </div>

            </div>
        </div>
    </div>
</div> 