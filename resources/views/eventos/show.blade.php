@extends('layouts.app')

@section('title', $evento->nombre)
@section('page-title', 'Detalle del Evento')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        
        <!-- Mensajes -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-5">
            <!-- Columna principal -->
            <div class="col-lg-8">
                <!-- Imagen principal -->
                <div class="mb-4">
                    @if($evento->lugar && $evento->lugar->imagenes->count() > 0)
                        <img src="{{ asset('storage/' . $evento->lugar->imagenes->first()->url) }}" 
                             class="img-fluid rounded w-100" 
                             style="height: 500px; object-fit: cover;" 
                             alt="{{ $evento->nombre }}">
                    @else
                        <img src="{{ asset('tourism/img/package-1.jpg') }}" 
                             class="img-fluid rounded w-100" 
                             style="height: 500px; object-fit: cover;" 
                             alt="{{ $evento->nombre }}">
                    @endif
                </div>

                <!-- Descripción -->
                <div class="bg-light rounded p-4 mb-4">
                    <h3 class="mb-4">
                        <i class="fa fa-info-circle text-primary me-2"></i>Descripción del Evento
                    </h3>
                    <p style="line-height: 1.8;">
                        {{ $evento->descripcion ?? 'Este evento aún no tiene descripción detallada.' }}
                    </p>
                </div>

                <!-- Información del evento -->
                <div class="bg-light rounded p-4 mb-4">
                    <h3 class="mb-4">
                        <i class="fa fa-calendar-alt text-primary me-2"></i>Información del Evento
                    </h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 btn-square bg-primary rounded-circle" 
                                     style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-calendar text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted">Fecha y Hora</p>
                                    <h6 class="mb-0">{{ $evento->fechaFormato() }}</h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 btn-square bg-primary rounded-circle" 
                                     style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-map-marker-alt text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted">Ubicación</p>
                                    <h6 class="mb-0">{{ $evento->ubicacion ?? 'Por confirmar' }}</h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 btn-square bg-primary rounded-circle" 
                                     style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-users text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted">Capacidad</p>
                                    <h6 class="mb-0">
                                        {{ $evento->reservasConfirmadas()->count() }} / {{ $evento->capacidad }} personas
                                    </h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 btn-square bg-primary rounded-circle" 
                                     style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-dollar-sign text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted">Precio</p>
                                    <h6 class="mb-0">
                                        @if($evento->esGratuito())
                                            <span class="text-success">GRATIS</span>
                                        @else
                                            ${{ number_format($evento->precio, 0, ',', '.') }}
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barra de progreso de ocupación -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Ocupación del evento:</span>
                            <strong>{{ $evento->porcentajeOcupacion() }}%</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar 
                                {{ $evento->porcentajeOcupacion() >= 80 ? 'bg-danger' : ($evento->porcentajeOcupacion() >= 50 ? 'bg-warning' : 'bg-success') }}" 
                                style="width: {{ $evento->porcentajeOcupacion() }}%">
                                {{ $evento->porcentajeOcupacion() }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lugar turístico asociado -->
                @if($evento->lugar)
                <div class="bg-light rounded p-4 mb-4">
                    <h5 class="mb-3">
                        <i class="fa fa-map text-primary me-2"></i>Lugar Turístico
                    </h5>
                    <div class="d-flex align-items-center">
                        @if($evento->lugar->imagenes->count() > 0)
                            <img src="{{ asset('storage/' . $evento->lugar->imagenes->first()->url) }}" 
                                 class="rounded me-3" 
                                 style="width: 80px; height: 80px; object-fit: cover;" 
                                 alt="{{ $evento->lugar->nombre }}">
                        @endif
                        <div>
                            <h6 class="mb-1">{{ $evento->lugar->nombre }}</h6>
                            <p class="text-muted mb-0">{{ $evento->lugar->direccion }}</p>
                            <a href="{{ route('lugar.mostrar', $evento->lugar->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                                Ver lugar
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="bg-light rounded p-4 mb-4 sticky-top" style="top: 100px;">
                    <h4 class="text-primary mb-4">{{ $evento->nombre }}</h4>

                    <!-- Estado del evento -->
                    <div class="mb-3">
                        @if($evento->estado === 'activo')
                            <span class="badge bg-success px-3 py-2 w-100">
                                <i class="fas fa-check-circle me-1"></i>Evento Activo
                            </span>
                        @elseif($evento->estado === 'cancelado')
                            <span class="badge bg-danger px-3 py-2 w-100">
                                <i class="fas fa-times-circle me-1"></i>Evento Cancelado
                            </span>
                        @else
                            <span class="badge bg-secondary px-3 py-2 w-100">
                                <i class="fas fa-flag-checkered me-1"></i>Finalizado
                            </span>
                        @endif
                    </div>

                    <!-- Información rápida -->
                    <div class="border-top border-bottom py-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fa fa-users text-primary me-1"></i>Cupos:</span>
                            <strong>{{ $evento->cuposDisponibles() }} disponibles</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fa fa-tag text-primary me-1"></i>Categoría:</span>
                            <strong>{{ $evento->categoria->nombre }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="fa fa-dollar-sign text-primary me-1"></i>Precio:</span>
                            <strong>{{ $evento->esGratuito() ? 'Gratis' : '$'.number_format($evento->precio, 0) }}</strong>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    @auth
                        @if($evento->usuarioEstaInscrito(auth()->id()))
                            <div class="alert alert-success text-center mb-3">
                                <i class="fas fa-check-circle me-2"></i>Ya estás inscrito
                            </div>
                            <a href="{{ route('reservas.index') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-list me-2"></i>Ver Mis Reservas
                            </a>
                        @elseif($evento->tieneCupos() && $evento->estado === 'activo' && !$evento->haTerminado())
                            <form action="{{ route('reservas.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                                
                                <div class="mb-3">
                                    <label class="form-label">Notas (opcional)</label>
                                    <textarea name="notas" class="form-control" rows="3" 
                                              placeholder="¿Algún comentario o pregunta?"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-ticket-alt me-2"></i>Inscribirme Ahora
                                </button>
                            </form>
                        @elseif(!$evento->tieneCupos())
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>Sin cupos disponibles
                            </div>
                        @elseif($evento->haTerminado())
                            <div class="alert alert-secondary text-center">
                                <i class="fas fa-flag-checkered me-2"></i>Evento finalizado
                            </div>
                        @endif
                    @else
                        <div class="alert alert-light border text-center">
                            <a href="{{ route('login') }}" class="text-primary fw-bold">Inicia sesión</a> 
                            para inscribirte
                        </div>
                    @endauth

                    @role('admin')
                        <div class="border-top pt-3 mt-3">
                            <a href="{{ route('eventos.edit', $evento->id) }}" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-edit me-2"></i>Editar Evento
                            </a>
                            <form action="{{ route('eventos.destroy', $evento->id) }}" method="POST" 
                                  onsubmit="return confirm('¿Eliminar este evento?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </button>
                            </form>
                        </div>
                    @endrole

                    <a href="{{ route('eventos.index') }}" class="btn btn-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Eventos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection