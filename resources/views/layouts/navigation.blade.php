<nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
    <a href="{{ url('/') }}" class="navbar-brand p-0">
        <h1 class="text-primary m-0">
            <i class="fa fa-map-marker-alt me-3"></i>Turismo
        </h1>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <!-- Menú principal -->
        <div class="navbar-nav ms-auto py-0 d-flex gap-2">
            
            <!-- Inicio -->
            <a href="{{ url('/') }}" class="btn btn-primary rounded-pill py-2 px-4 {{ Request::is('/') ? '' : '' }}">
                <i class="fas fa-home me-1"></i>Inicio
            </a>

            <!-- Lugares con Dropdown -->
            <div class="btn-group">
                <a href="{{ route('lugares.explorar') }}" class="btn btn-primary rounded-pill py-2 px-4">
                    <i class="fas fa-map me-1"></i>Lugares
                </a>
                <button type="button" class="btn btn-primary rounded-pill dropdown-toggle dropdown-toggle-split py-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('lugares.explorar') }}">
                        <i class="fas fa-list me-2"></i>Ver Todos
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('lugares.explorar', ['filtro' => 'mejor-calificados']) }}">
                        <i class="fa fa-star text-warning me-2"></i>Mejor Calificados
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('lugares.explorar', ['filtro' => 'mas-economicos']) }}">
                        <i class="fa fa-dollar-sign text-success me-2"></i>Más Económicos
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('lugares.explorar', ['filtro' => 'mas-recientes']) }}">
                        <i class="fa fa-clock text-info me-2"></i>Más Recientes
                    </a></li>
                </ul>
            </div>

            <!-- Eventos -->
            <a href="{{ route('eventos.index') }}" class="btn btn-primary rounded-pill py-2 px-4">
                <i class="fas fa-calendar-alt me-1"></i>Eventos
            </a>

            @auth
                @role('admin')
                    <!-- Menú de administración -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary rounded-pill py-2 px-4 dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i>Administración
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('lugares.index') }}">
                                <i class="fas fa-map me-2"></i>Lugares Turísticos
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('lugares.create') }}">
                                <i class="fas fa-plus me-2"></i>Agregar Lugar
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('categorias.index') }}">
                                <i class="fas fa-tags me-2"></i>Categorías
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('usuarios.index') }}">
                                <i class="fas fa-users me-2"></i>Usuarios
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('eventos.index') }}">
                                <i class="fas fa-calendar-alt me-2"></i>Eventos
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('eventos.create') }}">
                                <i class="fas fa-plus-circle me-2"></i>Crear Evento
                            </a></li>
                        </ul>
                    </div>
                @elserole('usuario')
                    <!-- Mis Reservas para usuario registrado -->
                    <a href="{{ route('reservas.index') }}" class="btn btn-primary rounded-pill py-2 px-4">
                        <i class="fas fa-calendar-check me-1"></i>Mis Reservas
                    </a>
                @endrole
            @endauth
        </div>

        <!-- Sección derecha: login / perfil -->
        <div class="d-flex gap-2 ms-3">
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary rounded-pill py-2 px-4">
                    <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill py-2 px-4">
                    <i class="fas fa-user-plus me-1"></i>Registrarse
                </a>
            @else
                <div class="btn-group">
                    <button type="button" class="btn btn-primary rounded-pill py-2 px-4 dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-id-card me-2"></i>Mi Perfil
                        </a></li>
                        @role('admin')
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                        @endrole
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </div>
</nav>