@extends('layouts.app')

@section('title', 'Mis Reservas')
@section('page-title', 'Mis Inscripciones a Eventos')

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

        <div class="text-center mb-5">
            <h6 class="section-title bg-white text-center text-primary px-3">Mis Reservas</h6>
            <h1 class="mb-3">Eventos en los que estás inscrito</h1>
        </div>

        @if($reservas->count() > 0)
            <div class="row g-4">
                @foreach($reservas as $reserva)
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="row g-0">
                                <!-- Imagen del evento -->
                                <div class="col-md-4">
                                    @if($reserva->evento->lugar && $reserva->evento->lugar->imagenes->count() > 0)
                                        <img src="{{ asset('storage/' . $reserva->evento->lugar->imagenes->first()->url) }}" 
                                             class="img-fluid rounded-start h-100" 
                                             style="object-fit: cover;" 
                                             alt="{{ $reserva->evento->nombre }}">
                                    @else
                                        <img src="{{ asset('tourism/img/package-1.jpg') }}" 
                                             class="img-fluid rounded-start h-100" 
                                             style="object-fit: cover;" 
                                             alt="{{ $reserva->evento->nombre }}">
                                    @endif
                                </div>

                                <div class="col-md-8">
                                    <div class="card-body">
                                        <!-- Badge de estado de reserva -->
                                        <div class="mb-2">
                                            @if($reserva->estado === 'confirmada')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Confirmada
                                                </span>
                                            @elseif($reserva->estado === 'cancelada')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Cancelada
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>Pendiente
                                                </span>
                                            @endif

                                            <!-- Badge de precio -->
                                            @if($reserva->evento->esGratuito())
                                                <span class="badge bg-info ms-1">Gratis</span>
                                            @else
                                                <span class="badge bg-primary ms-1">
                                                    ${{ number_format($reserva->evento->precio, 0) }}
                                                </span>
                                            @endif
                                        </div>

                                        <h5 class="card-title mb-2">{{ $reserva->evento->nombre }}</h5>
                                        
                                        <p class="card-text text-muted small mb-3">
                                            {{ Str::limit($reserva->evento->descripcion, 80) }}
                                        </p>

                                        <!-- Información del evento -->
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-calendar text-primary me-2"></i>
                                                <small>{{ $reserva->evento->fecha_inicio->format('d/m/Y H:i') }}</small>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                <small>{{ $reserva->evento->ubicacion ?? 'Por confirmar' }}</small>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-ticket-alt text-primary me-2"></i>
                                                <small>Reserva: {{ $reserva->fecha_reserva->format('d/m/Y') }}</small>
                                            </div>
                                        </div>

                                        @if($reserva->notas)
                                            <div class="alert alert-light mb-3 py-2">
                                                <small><strong>Notas:</strong> {{ $reserva->notas }}</small>
                                            </div>
                                        @endif

                                        <!-- Botones de acción -->
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('eventos.show', $reserva->evento->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Ver Evento
                                            </a>

                                            @if($reserva->estado === 'confirmada' && !$reserva->evento->estaEnCurso() && !$reserva->evento->haTerminado())
                                                <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" 
                                                      onsubmit="return confirm('¿Cancelar tu inscripción?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-times me-1"></i>Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-5">
                {{ $reservas->links() }}
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-calendar-times fa-5x text-muted"></i>
                        </div>
                        <h4 class="mb-3">No tienes reservas aún</h4>
                        <p class="text-muted mb-4">
                            Explora nuestros eventos turísticos y encuentra experiencias increíbles para inscribirte.
                        </p>
                        <a href="{{ route('eventos.index') }}" class="btn btn-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Ver Eventos Disponibles
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection