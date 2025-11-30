@extends('layouts.app')

@section('title', 'Lugares Turísticos')
@section('page-title', 'Lugares Turísticos')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h2 class="mb-3 mb-md-0">
                Todos los Lugares Turísticos
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
            
            <div class="d-flex gap-2 flex-wrap">
                <!-- Dropdown de Filtros -->
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa fa-filter me-2"></i>Filtrar por
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ $filtroActivo == 'mejor-calificados' ? 'active' : '' }}" 
                               href="{{ route('lugares.index', ['filtro' => 'mejor-calificados']) }}">
                                <i class="fa fa-star text-warning me-2"></i>Mejor Calificados
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ $filtroActivo == 'mas-economicos' ? 'active' : '' }}" 
                               href="{{ route('lugares.index', ['filtro' => 'mas-economicos']) }}">
                                <i class="fa fa-dollar-sign text-success me-2"></i>Más Económicos (≤ $50,000)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ $filtroActivo == 'mas-recientes' ? 'active' : '' }}" 
                               href="{{ route('lugares.index', ['filtro' => 'mas-recientes']) }}">
                                <i class="fa fa-clock text-info me-2"></i>Más Recientes
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('lugares.index') }}">
                                <i class="fa fa-times text-muted me-2"></i>Quitar Filtros
                            </a>
                        </li>
                    </ul>
                </div>

                @role('admin')
                    <a href="{{ route('lugares.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i>Agregar Nuevo Lugar
                    </a>
                @endrole
            </div>
        </div>
    </div>
</div>

@if ($lugares->isEmpty())
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fa fa-info-circle fa-3x mb-3"></i>
                <h4>No hay lugares turísticos registrados</h4>
                <p>¡Sé el primero en agregar un destino increíble!</p>
                @role('admin')
                    <a href="{{ route('lugares.create') }}" class="btn btn-primary mt-2">Agregar Lugar</a>
                @endrole
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
                            <i class="fa fa-dollar-sign text-primary me-2"></i>{{ $lugar->precio ?? 'Gratis' }}
                        </small>
                    </div>
                    <div class="text-center p-4">
                        <h3 class="mb-0">{{ $lugar->nombre }}</h3>
                        <div class="mb-3">
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                        </div>
                        <p>{{ Str::limit($lugar->descripcion, 100) }}</p>
                        <div class="d-flex justify-content-center mb-2">
                            <a href="{{ route('lugares.show', $lugar->id) }}" class="btn btn-sm btn-primary px-3 me-2">
                                <i class="fa fa-eye me-1"></i>Ver Detalles
                            </a>
                            @role('admin')
                                <a href="{{ route('lugares.edit', $lugar->id) }}" class="btn btn-sm btn-warning px-3 me-2">
                                    <i class="fa fa-edit me-1"></i>Editar
                                </a>
                                <form action="{{ route('lugares.destroy', $lugar->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este lugar?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-3">
                                        <i class="fa fa-trash me-1"></i>Eliminar
                                    </button>
                                </form>
                            @endrole
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
