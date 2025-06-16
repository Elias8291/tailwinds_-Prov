<!-- Desktop Sidebar -->
<div class="hidden md:block">
    <div class="group fixed top-16 left-0 h-[calc(100vh-4rem)] transition-all duration-300 ease-in-out w-[65px] hover:w-72 bg-gradient-to-b from-white to-gray-50 border-r border-gray-200 shadow-lg overflow-hidden hover:overflow-y-auto z-30"
         x-data="{ hovered: false }"
         @mouseenter="hovered = true; $dispatch('sidebar-hover', true)"
         @mouseleave="hovered = false; $dispatch('sidebar-hover', false)">
        <div class="flex flex-col flex-grow">
            <div class="flex-grow flex flex-col pt-4">
                <nav class="flex-1 px-2 space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('dashboard') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-transform duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2V5z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Dashboard</span>
                    </a>

                   

                    <!-- Roles y Permisos -->
                    @can('dashboard.admin')
                    <a href="{{ route('roles.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('roles.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('roles.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Roles y Permisos</span>
                    </a>

                    <!-- Usuarios -->
                    <a href="{{ route('users.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('users.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Usuarios</span>
                    </a>
                    @endcan



                    <!-- Mis Trámites (Solicitante) -->
                    <a href="{{ route('tramites.solicitante.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('tramites.solicitante.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('tramites.solicitante.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Mis Trámites</span>
                    </a>

                    <!-- Revisión de Trámites -->
                    <a href="{{ route('revision.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('revision.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('revision.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Revisión de Trámites</span>
                    </a>

                    <!-- Documentos -->
                    <a href="{{ route('documentos.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('documentos.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('documentos.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Documentos</span>
                    </a>

                    <!-- Proveedores -->
                    <a href="{{ route('proveedores.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('proveedores.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('proveedores.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Proveedores</span>
                    </a>

                    <!-- Perfil -->
                    <a href="{{ route('profile.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('profile.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('profile.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Mi Perfil</span>
                    </a>



                    <!-- Citas -->
                    <a href="{{ route('citas.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('citas.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('citas.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Citas</span>
                    </a>

                    <!-- Configuración -->
                    <a href="#" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                        <svg class="text-gray-400 flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:text-primary group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Configuración</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Mobile sidebar -->
<div class="md:hidden" x-show="sidebarOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full">
    <div class="fixed inset-0 flex z-40">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-600/75 backdrop-blur-sm" 
             x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gradient-to-b from-white to-gray-50 transform transition-transform ease-in-out duration-300">
            <div class="absolute top-0 right-0 -mr-12 pt-4">
                <button class="flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebarOpen = false">
                    <span class="sr-only">Cerrar menú</span>
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Profile Section -->
            <div class="flex-shrink-0 px-4 py-4 flex items-center">
                <div class="flex-shrink-0 w-full group">
                    <div class="flex items-center">
                        <div>
                            <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 text-primary">
                                <span class="text-xl font-medium leading-none">JD</span>
                            </span>
                        </div>
                        <div class="ml-3">
                            <p class="text-base font-medium text-gray-700">John Doe</p>
                            <p class="text-sm font-medium text-gray-500">Administrador</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div class="flex-1 h-0 overflow-y-auto">
                <nav class="px-4 py-4 space-y-2">
                    <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('dashboard') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="#" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                        <svg class="text-gray-400 group-hover:text-primary mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Analytics
                    </a>

                    <a href="#" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                        <svg class="text-gray-400 group-hover:text-primary mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 011-1h1m-1 1v1m0 0h1m-1 0v3M9 7h1m0 0v3m0-3h1m0 0v3m0-3h1m-1 0v1" />
                        </svg>
                        Proyectos
                    </a>

                    <a href="#" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                        <svg class="text-gray-400 group-hover:text-primary mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        Equipo
                    </a>

                    <!-- Roles y Permisos en móvil -->
                    @can('dashboard.admin')
                    <a href="{{ route('roles.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('roles.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('roles.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                        </svg>
                        Roles y Permisos
                    </a>

                    <!-- Usuarios en móvil -->
                    <a href="{{ route('users.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('users.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Usuarios
                    </a>
                    @endcan

                    <!-- Trámites en móvil -->
                    <a href="{{ route('tramites.solicitante.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('tramites.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('tramites.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Trámites
                    </a>

                    <!-- Documentos en móvil -->
                    <a href="{{ route('documentos.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('documentos.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('documentos.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Documentos
                    </a>

                    <!-- Proveedores en móvil -->
                    <a href="{{ route('proveedores.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('proveedores.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('proveedores.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Proveedores
                    </a>

                    <!-- Perfil en móvil -->
                    <a href="{{ route('profile.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('profile.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('profile.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mi Perfil
                    </a>

                    <!-- Citas en móvil -->
                    <a href="{{ route('citas.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('citas.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('citas.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Citas
                    </a>
                </nav>
            </div>

            <!-- Mobile Footer Actions -->
            <div class="flex-shrink-0 px-4 py-4 border-t border-gray-200 space-y-2">
                <!-- Notifications Button -->
                <button x-data="{ open: false }" @click="open = !open" class="group flex items-center w-full px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                    <div class="relative">
                        <svg class="text-gray-400 group-hover:text-primary mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center bg-red-500 text-white text-xs rounded-full">3</span>
                    </div>
                    Notificaciones
                </button>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="group flex items-center w-full px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-red-700 hover:bg-red-50">
                        <svg class="text-red-400 group-hover:text-red-500 mr-4 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> 