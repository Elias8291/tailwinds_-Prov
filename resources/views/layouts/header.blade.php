<nav class="bg-white border-b border-gray-200">
    <div class="w-full">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <button type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex-shrink-0 flex items-center ml-3">
                    <a href="#" class="flex items-center hover:opacity-80 transition-opacity duration-200">
                        <img class="h-11 w-auto" src="/images/logoColor.png" alt="Logo">
                    </a>
                </div>
            </div>

            <div class="hidden md:flex items-center space-x-4 pr-4">
                <div class="relative" x-data="{ open: false }">
                    <div>
                        <button @click="open = !open" class="group flex items-center max-w-xs text-sm rounded-full hover:ring-2 hover:ring-primary/20 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" id="user-menu-button">
                            <span class="sr-only">Abrir menú de usuario</span>
                            <div class="relative">
                                <span class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-gradient-to-br from-primary to-primary-dark text-white shadow-md group-hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                    <span class="text-sm font-semibold leading-none">U</span>
                                </span>
                                <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 bg-green-400 border-2 border-white rounded-full"></div>
                            </div>
                        </button>
                    </div>
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-3 w-64 rounded-xl shadow-xl bg-white ring-1 ring-gray-200 divide-y divide-gray-100 focus:outline-none z-50 overflow-hidden">
                        
                        <!-- Header del usuario -->
                        <div class="px-4 py-4 bg-gradient-to-r from-primary/10 to-primary-dark/10">
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-br from-primary to-primary-dark text-white shadow-md">
                                    <span class="text-lg font-semibold leading-none">U</span>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">Usuario</p>
                                    <p class="text-xs text-gray-500 truncate">email@example.com</p>
                                    <div class="flex items-center mt-1">
                                        <div class="h-2 w-2 bg-green-400 rounded-full mr-1"></div>
                                        <span class="text-xs text-green-600 font-medium">En línea</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opciones principales -->
                        <div class="py-2">
                            <a href="#" class="group flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-primary/5 hover:text-primary transition-all duration-200">
                                <div class="flex-shrink-0 w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center mr-3 group-hover:bg-primary/20 transition-colors duration-200">
                                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">Mi Perfil</div>
                                    <div class="text-xs text-gray-500">Configurar cuenta</div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-primary transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Cerrar sesión -->
                        <div class="py-2">
                            <form method="POST" action="#">
                                <button type="submit" class="group flex w-full items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200">
                                    <div class="flex-shrink-0 w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center mr-3 group-hover:bg-red-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 text-left">
                                        <div class="font-medium">Cerrar Sesión</div>
                                        <div class="text-xs text-red-400">Salir del sistema</div>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>