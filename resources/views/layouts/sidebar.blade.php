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
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">Dashboard</span>
                    </a>

                    <!-- Usuarios -->
                    <a href="{{ route('users.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                    {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                    <div class="relative">
                        <svg class="{{ request()->routeIs('users.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">Usuarios</span>
                </a>

                    <!-- Tramites -->
                    <a href="{{ route('tramites.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('tramites.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('tramites.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 4h6a2 2 0 002-2v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">Tramites</span>
                    </a>

                    {{--
                    <!-- Roles y Permisos -->
                    @can('roles.ver')
                    <a href="{{ route('roles.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('roles.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('roles.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1017.25 2.75z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">Roles y Permisos</span>
                    </a>
                    @endcan
                    --}}

                    <!-- Notificaciones -->
                    <a href="{{ route('notificaciones.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('notificaciones.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <div class="relative">
                            <svg class="{{ request()->routeIs('notificaciones.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Eliminado contador visual -->
                        </div>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">Notificaciones</span>
                    </a>

                    <!-- Mi Perfil -->
                    @can('perfil.ver')
                    <a href="{{ route('profile.index') }}" class="group/item flex items-center min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 
                        {{ request()->routeIs('profile.*') ? 'bg-primary-50 text-primary border-l-4 border-primary shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-md hover:text-primary' }}">
                        <svg class="{{ request()->routeIs('profile.*') ? 'text-primary' : 'text-gray-400 group-hover/item:text-primary' }} flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">Mi Perfil</span>
                    </a>
                    @endcan

                    <!-- Separador final -->
                    <div class="px-3 py-2">
                        <div class="h-px bg-gray-200"></div>
                    </div>

                    <!-- Cerrar Sesión -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="group/item flex items-center w-full min-w-[250px] px-3 py-3 text-base font-medium rounded-xl transition-all duration-200 text-red-700 hover:bg-red-50 hover:shadow-md hover:text-red-800">
                            <svg class="text-red-400 group-hover/item:text-red-500 flex-shrink-0 w-6 h-6 transition-all duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="ml-3 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">Cerrar Sesión</span>
                        </button>
                    </form>
                </nav>
            </div>
        </div>
    </div>
</div> 