@extends('layouts.app')

@section('title', 'Eventos Turísticos')
@section('page-title', 'Eventos Turísticos')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Eventos</h6>
            <h1 class="mb-5">Próximos Eventos Turísticos</h1>
        </div>

        @if($eventos->count() > 0)
            <div class="row g-4">
                @foreach($eventos as $evento)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="card border-0 shadow-sm h-100 overflow-hidden">
                            <!-- Imagen del evento -->
                            @if($evento->lugar && $evento->lugar->imagenes->count() > 0)
                                <img src="{{ asset('storage/' . $evento->lugar->imagenes->first()->url) }}" 
                                     class="card-img-top" 
                                     style="height: 250px; object-fit: cover;" 
                                     alt="{{ $evento->nombre }}">
                            @else
                                <img src="{{ asset('tourism/img/package-1.jpg') }}" 
                                     class="card-img-top" 
                                     style="height: 250px; object-fit: cover;" 
                                     alt="{{ $evento->nombre }}">
                            @endif

                            <!-- Badge de estado -->
                            <div class="position-absolute top-0 start-0 m-3">
                                @if($evento->esGratuito())
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-gift me-1"></i>GRATIS
                                    </span>
                                @else
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="fas fa-dollar-sign me-1"></i>${{ number_format($evento->precio, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                            <!-- Badge de cupos -->
                            <div class="position-absolute top-0 end-0 m-3">
                                @if($evento->cuposDisponibles() <= 5 && $evento->cuposDisponibles() > 0)
                                    <span class="badge bg-warning text-dark px-3 py-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>{{ $evento->cuposDisponibles() }} cupos
                                    </span>
                                @elseif($evento->cuposDisponibles() == 0)
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="fas fa-times me-1"></i>Lleno
                                    </span>
                                @else
                                    <span class="badge bg-info px-3 py-2">
                                        <i class="fas fa-users me-1"></i>{{ $evento->cuposDisponibles() }} cupos
                                    </span>
                                @endif
                            </div>

                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="badge bg-light text-primary">
                                        <i class="fas fa-tag me-1"></i>{{ $evento->categoria->nombre }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $evento->fecha_inicio->format('d/m/Y') }}
                                    </small>
                                </div>

                                <h5 class="card-title mb-3">{{ $evento->nombre }}</h5>
                                
                                <p class="card-text text-muted">
                                    {{ Str::limit($evento->descripcion, 100) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="text-muted">
                                        <small>
                                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                            {{ $evento->ubicacion ?? ($evento->lugar ? $evento->lugar->nombre : 'Por definir') }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Botón de acción -->
                                <div class="mt-3">
                                    <a href="{{ route('eventos.show', $evento->id) }}" class="btn btn-primary w-100">
                                        <i class="fas fa-eye me-2"></i>Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-5">
                {{ $eventos->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                No hay eventos próximos disponibles en este momento.
            </div>
        @endif
    </div>
</div>
@endsection