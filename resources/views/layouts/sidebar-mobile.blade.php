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
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('dashboard') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6a2 2 0 01-2 2H10a2 2 0 01-2-2V5z" />
                            </svg>
                            <span class="font-semibold tracking-wide">Dashboard</span>
                        </a>
                        
                        <!-- Roles y Permisos -->
                        @can('dashboard.admin')
                        <a href="{{ route('roles.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('roles.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('roles.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                            </svg>
                            <span class="font-medium tracking-wide">Roles y Permisos</span>
                        </a>

                        <!-- Usuarios -->
                        <a href="{{ route('users.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('users.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="font-medium tracking-wide">Usuarios</span>
                        </a>
                        @endcan

                        <!-- Trámites -->
                        <a href="{{ route('tramites.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('tramites.*') && !request()->routeIs('tramites.solicitante.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('tramites.*') && !request()->routeIs('tramites.solicitante.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium tracking-wide">Trámites</span>
                        </a>

                        <!-- Mis Trámites (Solicitante) -->
                        <a href="{{ route('tramites.solicitante.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('tramites.solicitante.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('tramites.solicitante.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="font-medium tracking-wide">Mis Trámites</span>
                        </a>

                        <!-- Revisión de Trámites -->
                        <a href="{{ route('revision.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('revision.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('revision.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium tracking-wide">Revisión de Trámites</span>
                        </a>

                        <!-- Documentos -->
                        <a href="{{ route('documentos.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('documentos.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('documentos.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium tracking-wide">Documentos</span>
                        </a>

                        <!-- Proveedores -->
                        <a href="{{ route('proveedores.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('proveedores.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('proveedores.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium tracking-wide">Proveedores</span>
                        </a>

                        <!-- Perfil -->
                        <a href="{{ route('profile.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('profile.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('profile.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium tracking-wide">Mi Perfil</span>
                        </a>

                        <!-- Settings -->
                        <a href="#" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-gray-700 hover:bg-white hover:shadow-md hover:text-primary">
                            <svg class="text-gray-400 flex-shrink-0 w-6 h-6 mr-3 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <span class="font-medium tracking-wide">Configuración</span>
                        </a>
                        
                        <!-- Citas -->
                        <a href="{{ route('citas.index') }}" @click="sidebarOpen = false" class="group flex items-center px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                            {{ request()->routeIs('citas.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                            <svg class="{{ request()->routeIs('citas.*') ? 'text-primary' : 'text-gray-400 group-hover:text-primary' }} flex-shrink-0 w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium tracking-wide">Citas</span>
                        </a>
                    </nav>
                </div>

            </div>
        </div>
    </div>
</div> 