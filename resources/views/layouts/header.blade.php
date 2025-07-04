<nav class="bg-white border-b border-gray-200">
    <div class="w-full">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <button type="button" @click="sidebarOpen = !sidebarOpen" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex-shrink-0 flex items-center ml-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center hover:opacity-80 transition-opacity duration-200">
                        <img class="h-11 w-auto" src="{{ asset('images/logoColor.png') }}" alt="Logo">
                    </a>
                </div>
            </div>

            <div class="hidden md:flex items-center space-x-4 pr-4">
                <div class="relative" x-data="notificaciones()" @click.away="open = false">
                    <button @click="open = !open" class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <span class="sr-only">Ver notificaciones</span>
                        <span x-show="contadorNoLeidas > 0" x-text="contadorNoLeidas" class="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center bg-red-500 text-white text-xs rounded-full"></span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-96 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50">
                        <div class="px-4 py-3 bg-gradient-to-r from-[#B4325E]/10 to-[#93264B]/10 rounded-t-lg border-b border-gray-100">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-r from-[#B4325E] to-[#93264B] rounded-lg p-1 mr-2">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">Notificaciones</h3>
                                </div>
                                <button @click="marcarTodasComoLeidas()" x-show="contadorNoLeidas > 0" 
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#7a1d37] rounded-md transition-all duration-200 shadow-sm">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Marcar todas
                                </button>
                            </div>
                        </div>
                        
                        <div x-show="cargando" class="px-4 py-8 text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                            <p class="mt-2 text-sm text-gray-500">Cargando notificaciones...</p>
                        </div>
                        
                        <div x-show="!cargando && notificaciones.length === 0" class="px-4 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No tienes notificaciones</p>
                        </div>

                        <div x-show="!cargando" class="max-h-[40vh] overflow-y-auto">
                            <template x-for="notificacion in notificaciones" :key="notificacion.id">
                                <div @click="marcarComoLeida(notificacion.id)" 
                                     :class="!notificacion.leida ? 'bg-blue-50' : 'bg-white'"
                                     class="px-4 py-3 hover:bg-gray-50 transition-colors duration-200 cursor-pointer border-l-4"
                                     :class="{
                                         'border-blue-400': notificacion.color === 'blue',
                                         'border-yellow-400': notificacion.color === 'yellow', 
                                         'border-red-400': notificacion.color === 'red',
                                         'border-gray-400': notificacion.color === 'gray'
                                     }">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="relative">
                                                <div class="inline-flex items-center justify-center h-10 w-10 rounded-xl shadow-md"
                                                     :class="{
                                                         'bg-gradient-to-br from-blue-400 to-blue-600': notificacion.tipo === 'Informativo',
                                                         'bg-gradient-to-br from-yellow-400 to-yellow-600': notificacion.tipo === 'Advertencia',
                                                         'bg-gradient-to-br from-red-400 to-red-600': notificacion.tipo === 'Error'
                                                     }">
                                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <!-- Informativo -->
                                                        <path x-show="notificacion.tipo === 'Informativo'" 
                                                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        <!-- Advertencia -->
                                                        <path x-show="notificacion.tipo === 'Advertencia'" 
                                                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                        <!-- Error -->
                                                        <path x-show="notificacion.tipo === 'Error'" 
                                                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                                
                                                <span x-show="!notificacion.leida" 
                                                      class="absolute -top-1 -right-1 h-3 w-3 bg-red-500 border-2 border-white rounded-full animate-pulse"></span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center mb-1">
                                                <p class="text-sm font-semibold text-gray-900 mr-2" x-text="notificacion.titulo"></p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium"
                                                      :class="{
                                                          'bg-blue-100 text-blue-700': notificacion.color === 'blue',
                                                          'bg-yellow-100 text-yellow-700': notificacion.color === 'yellow',
                                                          'bg-red-100 text-red-700': notificacion.color === 'red',
                                                          'bg-gray-100 text-gray-700': notificacion.color === 'gray'
                                                      }"
                                                      x-text="notificacion.tipo">
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 leading-relaxed line-clamp-2" x-text="notificacion.mensaje"></p>
                                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span x-text="notificacion.tiempo_transcurrido"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div x-show="!cargando && notificaciones.length > 0" class="px-4 py-3 bg-gray-50 rounded-b-lg border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <span x-text="notificaciones.length"></span> notificaciones
                                </span>
                                <a href="{{ route('notificaciones.index') }}" 
                                   class="text-xs text-[#B4325E] hover:text-[#93264B] font-medium flex items-center transition-colors duration-200">
                                    Ver todas
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative" x-data="{ open: false }">
                    <div>
                        <button @click="open = !open" class="group flex items-center max-w-xs text-sm rounded-full hover:ring-2 hover:ring-primary/20 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" id="user-menu-button">
                            <span class="sr-only">Abrir menú de usuario</span>
                            <div class="relative">
                                <span class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-gradient-to-br from-primary to-primary-dark text-white shadow-md group-hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                    <span class="text-sm font-semibold leading-none">
                                        @if(auth()->check())
                                            {{ strtoupper(substr(auth()->user()->nombre ?? auth()->user()->name ?? '', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->nombre ?? auth()->user()->name ?? '')[1] ?? '', 0, 1)) }}
                                        @else
                                            U
                                        @endif
                                    </span>
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
                                    <span class="text-lg font-semibold leading-none">
                                        @if(auth()->check())
                                            {{ strtoupper(substr(auth()->user()->nombre ?? auth()->user()->name ?? '', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->nombre ?? auth()->user()->name ?? '')[1] ?? '', 0, 1)) }}
                                        @else
                                            U
                                        @endif
                                    </span>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        @if(auth()->check())
                                            {{ auth()->user()->nombre ?? auth()->user()->name ?? 'Usuario' }}
                                        @else
                                            Usuario
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        @if(auth()->check())
                                            {{ auth()->user()->email ?? 'email@example.com' }}
                                        @else
                                            email@example.com
                                        @endif
                                    </p>
                                    <div class="flex items-center mt-1">
                                        <div class="h-2 w-2 bg-green-400 rounded-full mr-1"></div>
                                        <span class="text-xs text-green-600 font-medium">En línea</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opciones principales -->
                        <div class="py-2">
                            <a href="{{ route('profile.index') }}" class="group flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-primary/5 hover:text-primary transition-all duration-200">
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
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
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

<script>
function notificaciones() {
    return {
        open: false,
        cargando: false,
        notificaciones: [],
        contadorNoLeidas: 0,
        
        init() {
            // Solo cargar el contador inicial, no las notificaciones completas
            this.cargarContador();
            // Actualizar contador cada 30 segundos
            setInterval(() => {
                this.cargarContador();
            }, 30000);
        },
        
        async cargarNotificaciones() {
            this.cargando = true;
            try {
                const response = await fetch('{{ route("notificaciones.header") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.notificaciones = data.notificaciones;
                    this.contadorNoLeidas = data.contador_no_leidas;
                }
            } catch (error) {
                console.error('Error al cargar notificaciones:', error);
            } finally {
                this.cargando = false;
            }
        },
        
        async cargarContador() {
            try {
                const response = await fetch('{{ route("notificaciones.contador") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.contadorNoLeidas = data.contador;
                }
            } catch (error) {
                console.error('Error al cargar contador:', error);
            }
        },
        
        async toggleNotificaciones() {
            this.open = !this.open;
            if (this.open && this.notificaciones.length === 0) {
                // Solo cargar las notificaciones completas cuando se abre por primera vez
                await this.cargarNotificaciones();
            }
        },
        
        async marcarComoLeida(notificacionId) {
            try {
                const response = await fetch(`{{ url('notificaciones') }}/${notificacionId}/marcar-leida`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    // Actualizar la notificación como leída
                    const notificacion = this.notificaciones.find(n => n.id === notificacionId);
                    if (notificacion && !notificacion.leida) {
                        notificacion.leida = true;
                        this.contadorNoLeidas = Math.max(0, this.contadorNoLeidas - 1);
                    }
                }
            } catch (error) {
                console.error('Error al marcar como leída:', error);
            }
        },
        
        async marcarTodasComoLeidas() {
            try {
                const response = await fetch('{{ route("notificaciones.marcar-todas-leidas") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    // Marcar todas como leídas
                    this.notificaciones.forEach(notificacion => {
                        notificacion.leida = true;
                    });
                    this.contadorNoLeidas = 0;
                }
            } catch (error) {
                console.error('Error al marcar todas como leídas:', error);
            }
        }
    }
}
</script>