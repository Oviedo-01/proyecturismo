@extends('layouts.app')

@section('title', 'Explorar Lugares Turísticos')
@section('page-title', 'Explorar Lugares Turísticos')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h2 class="mb-3 mb-md-0">
                Lugares Turísticos
                @if(isset($filtroActivo))
                    <span class="badge bg-primary ms-2">
                        @switch($filtroActivo)
                            @case('mejor-calificados')
                                🏆 Mejor Calificados
                                @break
                            @case('mas-economicos')
                                💰 Más Económicos
                                @break
                            @case('mas-recientes')
                                🆕 Más Recientes
                                @break
                        @endswitch
                    </span>
                @endif
            </h2>
            
            <!-- Dropdown de Filtros -->
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-filter me-2"></i>Filtrar por
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item {{ $filtroActivo == 'mejor-calificados' ? 'active' : '' }}" 
                           href="{{ route('lugares.explorar', ['filtro' => 'mejor-calificados']) }}">
                            <i class="fa fa-star text-warning me-2"></i>Mejor Calificados
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ $filtroActivo == 'mas-economicos' ? 'active' : '' }}" 
                           href="{{ route('lugares.explorar', ['filtro' => 'mas-economicos']) }}">
                            <i class="fa fa-dollar-sign text-success me-2"></i>Más Económicos (≤ $50,000)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ $filtroActivo == 'mas-recientes' ? 'active' : '' }}" 
                           href="{{ route('lugares.explorar', ['filtro' => 'mas-recientes']) }}">
                            <i class="fa fa-clock text-info me-2"></i>Más Recientes
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('lugares.explorar') }}">
                            <i class="fa fa-times text-muted me-2"></i>Quitar Filtros
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if ($lugares->isEmpty())
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fa fa-info-circle fa-3x mb-3"></i>
                <h4>No hay lugares turísticos disponibles</h4>
                <p>Vuelve pronto para descubrir nuevos destinos increíbles.</p>
            </div>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach ($lugares as $lugar)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="package-item">
                    <div class="overflow-hidden">
                        @if ($lugar->imagenes->first())
                            <img class="img-fluid" src="{{ asset('storage/' . $lugar->imagenes->first()->url) }}" alt="{{ $lugar->nombre }}" style="height: 250px; width: 100%; object-fit: cover;">
                        @else
                            <img class="img-fluid" src="{{ asset('tourism/img/package-1.jpg') }}" alt="Sin imagen" style="height: 250px; width: 100%; object-fit: cover;">
                        @endif
                    </div>
                    <div class="d-flex border-bottom">
                        <small class="flex-fill text-center border-end py-2">
                            <i class="fa fa-map-marker-alt text-primary me-2"></i>{{ $lugar->categoria->nombre ?? 'Sin categoría' }}
                        </small>
                        <small class="flex-fill text-center border-end py-2">
                            <i class="fa fa-star text-primary me-2"></i>{{ number_format($lugar->promedio_calificacion, 1) }}
                        </small>
                        <small class="flex-fill text-center py-2">
                            @if($lugar->precio && $lugar->precio > 0)
                                <i class="fa fa-dollar-sign text-primary me-2"></i>${{ number_format($lugar->precio, 0, ',', '.') }}
                            @else
                                <i class="fa fa-gift text-success me-2"></i>Gratis
                            @endif
                        </small>
                    </div>
                    <div class="text-center p-4">
                        <h3 class="mb-0">{{ $lugar->nombre }}</h3>
                        <div class="mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <small class="fa fa-star {{ $i <= round($lugar->promedio_calificacion) ? 'text-primary' : 'text-muted' }}"></small>
                            @endfor
                        </div>
                        <p>{{ Str::limit($lugar->descripcion, 100) }}</p>
                        <div class="d-flex justify-content-center mb-2">
                            <a href="{{ route('lugar.mostrar', $lugar->id) }}" class="btn btn-sm btn-primary px-3">
                                <i class="fa fa-eye me-1"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection