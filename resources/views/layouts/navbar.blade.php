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
        <div class="navbar-nav ms-auto py-0">
            
            <!-- Inicio -->
            <a href="{{ url('/') }}" class="nav-item nav-link {{ Request::is('/') ? 'active' : '' }}">
                <i class="fas fa-home me-1"></i>Inicio
            </a>

            <!-- Lugares con Dropdown de Filtros -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-map me-1"></i>Lugares
                </a>
                <div class="dropdown-menu m-0">
                    <a href="{{ route('lugares.explorar') }}" class="dropdown-item">
                        <i class="fas fa-list me-2"></i>Ver Todos
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('lugares.explorar', ['filtro' => 'mejor-calificados']) }}" class="dropdown-item">
                        <i class="fa fa-star text-warning me-2"></i>Mejor Calificados
                    </a>
                    <a href="{{ route('lugares.explorar', ['filtro' => 'mas-economicos']) }}" class="dropdown-item">
                        <i class="fa fa-dollar-sign text-success me-2"></i>Más Económicos
                    </a>
                    <a href="{{ route('lugares.explorar', ['filtro' => 'mas-recientes']) }}" class="dropdown-item">
                        <i class="fa fa-clock text-info me-2"></i>Más Recientes
                    </a>
                </div>
            </div>

            <!-- Eventos -->
            <a href="{{ route('eventos.index') }}" class="nav-item nav-link">
                <i class="fas fa-calendar-alt me-1"></i>Eventos
            </a>

            @auth
                @role('admin')
                    <!-- Menú de administración -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i>Administración
                        </a>
                        <div class="dropdown-menu m-0">
                            <a href="{{ route('dashboard') }}" class="dropdown-item">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                            <a href="{{ route('lugares.index') }}" class="dropdown-item">
                                <i class="fas fa-map me-2"></i>Lugares Turísticos
                            </a>
                            <a href="{{ route('lugares.create') }}" class="dropdown-item">
                                <i class="fas fa-plus me-2"></i>Agregar Lugar
                            </a>
                            <a href="{{ route('categorias.index') }}" class="dropdown-item">
                                <i class="fas fa-tags me-2"></i>Categorías
                            </a>
                            <a href="{{ route('usuarios.index') }}" class="dropdown-item">
                                <i class="fas fa-users me-2"></i>Usuarios
                            </a>
                            <a href="{{ route('eventos.index') }}" class="dropdown-item">
                                <i class="fas fa-calendar-alt me-2"></i>Eventos
                            </a>
                            <a href="{{ route('eventos.create') }}" class="dropdown-item">
                                <i class="fas fa-plus-circle me-2"></i>Crear Evento
                            </a>                     
                        </div>
                    </div>
                @elserole('usuario')
                    <!-- Menú para usuario registrado -->
                    <a href="{{ route('reservas.index') }}" class="nav-item nav-link">
                        <i class="fas fa-calendar-check me-1"></i>Mis Reservas
                    </a>
                @endrole
            @endauth
        </div>

        <!-- Sección derecha: login / perfil -->
        @guest
            <a href="{{ route('login') }}" class="btn btn-primary rounded-pill py-2 px-4 me-2">
                <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill py-2 px-4">
                <i class="fas fa-user-plus me-1"></i>Registrarse
            </a>
        @else
            <div class="dropdown">
                <a href="#" class="btn btn-primary rounded-pill py-2 px-4 dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-end m-0">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-id-card me-2"></i>Mi Perfil
                    </a>

                    @role('admin')
                        <a href="{{ route('dashboard') }}" class="dropdown-item">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    @endrole

                    <hr class="dropdown-divider">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        @endguest
    </div>
</nav>