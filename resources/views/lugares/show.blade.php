@extends('layouts.app')

@section('title', $lugare->nombre)
@section('page-title', $lugare->nombre)

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#map {
    height: 400px;
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
</style>
@endpush

@section('content')

<!-- Mensajes de éxito/error -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-5">
    <!-- Columna Principal - Información del Lugar -->
    <div class="col-lg-8">
        <!-- Galería de Imágenes con Carousel -->
        <div class="mb-4">
            @if($lugare->imagenes->count() > 0)
                <div id="lugarCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($lugare->imagenes as $key => $img)
                            <button type="button" data-bs-target="#lugarCarousel" data-bs-slide-to="{{ $key }}" class="{{ $key === 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner rounded">
                        @foreach($lugare->imagenes as $key => $img)
                            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $img->url) }}" class="d-block w-100" alt="{{ $lugare->nombre }}" style="height: 500px; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#lugarCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#lugarCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            @else
                <img src="{{ asset('tourism/img/package-1.jpg') }}" class="img-fluid rounded w-100" alt="Sin imagen" style="height: 500px; object-fit: cover;">
            @endif
        </div>

        <!-- Descripción Completa -->
        <div class="bg-light rounded p-4 mb-4">
            <h3 class="mb-4"><i class="fa fa-info-circle text-primary me-2"></i>Descripción</h3>
            <p class="text-justify" style="line-height: 1.8;">
                {{ $lugare->descripcion ?? 'Este lugar aún no tiene una descripción detallada.' }}
            </p>
        </div>

        <!-- MAPA DE UBICACIÓN -->
        @if($lugare->tieneUbicacion())
        <div class="bg-light rounded p-4 mb-4">
            <h3 class="mb-4"><i class="fa fa-map-marked-alt text-primary me-2"></i>Ubicación en el Mapa</h3>
            <div id="map"></div>
            <div class="mt-3 text-center">
                <a href="{{ $lugare->urlGoogleMaps() }}" target="_blank" class="btn btn-primary">
                    <i class="fa fa-external-link-alt me-2"></i>Abrir en Google Maps
                </a>
            </div>
        </div>
        @endif

        <!-- Información Adicional -->
        <div class="bg-light rounded p-4 mb-4">
            <h3 class="mb-4"><i class="fa fa-list text-primary me-2"></i>Información Adicional</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 btn-square bg-primary rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-map-marker-alt text-white"></i>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0 text-muted">Dirección</p>
                            <h6 class="mb-0">{{ $lugare->direccion ?? 'No especificada' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 btn-square bg-primary rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-clock text-white"></i>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0 text-muted">Horarios</p>
                            <h6 class="mb-0">{{ $lugare->horarios ?? 'Consultar disponibilidad' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 btn-square bg-primary rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-dollar-sign text-white"></i>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0 text-muted">Precio de Entrada</p>
                            <h6 class="mb-0">{{ $lugare->precio ? '$' . number_format($lugare->precio, 0, ',', '.') : 'Gratis' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 btn-square bg-primary rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-phone text-white"></i>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0 text-muted">Contacto</p>
                            <h6 class="mb-0">{{ $lugare->contacto ?? 'No disponible' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Miniaturas de Galería -->
        @if($lugare->imagenes->count() > 1)
        <div class="bg-light rounded p-4 mb-4">
            <h4 class="mb-3">Galería de Imágenes</h4>
            <div class="row g-2">
                @foreach($lugare->imagenes as $img)
                    <div class="col-md-3 col-6">
                        <img src="{{ asset('storage/' . $img->url) }}" class="img-fluid rounded" alt="Imagen" style="height: 120px; width: 100%; object-fit: cover; cursor: pointer;" data-bs-target="#lugarCarousel" data-bs-slide-to="{{ $loop->index }}">
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- SECCIÓN DE COMENTARIOS Y RESEÑAS -->
        <div class="bg-light rounded p-4 mb-4">
            <h3 class="mb-4">
                <i class="fa fa-comments text-primary me-2"></i>
                Reseñas y Comentarios 
                <span class="badge bg-primary">{{ $lugare->totalComentarios() }}</span>
            </h3>

            <!-- Estadísticas de calificación -->
            @if($lugare->totalComentarios() > 0)
            <div class="row mb-4 pb-4 border-bottom">
                <div class="col-md-4 text-center">
                    <h1 class="display-3 text-primary mb-0">{{ number_format($lugare->calificacionPromedio(), 1) }}</h1>
                    <div class="mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star {{ $i <= round($lugare->calificacionPromedio()) ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </div>
                    <p class="text-muted mb-0">{{ $lugare->totalComentarios() }} {{ $lugare->totalComentarios() == 1 ? 'reseña' : 'reseñas' }}</p>
                </div>
                <div class="col-md-8">
                    @php $distribucion = $lugare->distribucionCalificaciones(); @endphp
                    @for($i = 5; $i >= 1; $i--)
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2">{{ $i }} <i class="fa fa-star text-warning"></i></span>
                            <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                <div class="progress-bar bg-warning" style="width: {{ $lugare->totalComentarios() > 0 ? ($distribucion[$i] / $lugare->totalComentarios() * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-muted">{{ $distribucion[$i] }}</span>
                        </div>
                    @endfor
                </div>
            </div>
            @endif

            <!-- Formulario para agregar comentario (solo usuarios autenticados) -->
            @auth
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="mb-3"><i class="fa fa-edit text-primary me-2"></i>Deja tu reseña</h5>
                    <form action="{{ route('comentarios.store', $lugare->id) }}" method="POST" id="form-comentario">
                        @csrf
                        <!-- Calificación con estrellas -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Calificación *</label>
                            <div class="rating-stars d-flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="star-label" style="cursor: pointer;">
                                        <input type="radio" name="calificacion" value="{{ $i }}" class="d-none" required>
                                        <i class="fa fa-star fa-2x text-muted star-icon" data-value="{{ $i }}"></i>
                                    </label>
                                @endfor
                            </div>
                            @error('calificacion')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Comentario -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tu comentario *</label>
                            <textarea name="comentario" class="form-control" rows="4" 
                                placeholder="Comparte tu experiencia sobre este lugar..." 
                                required>{{ old('comentario') }}</textarea>
                            <small class="text-muted">Mínimo 10 caracteres</small>
                            @error('comentario')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-paper-plane me-2"></i>Publicar Reseña
                        </button>
                    </form>
                </div>
            @else
                <div class="alert alert-light border mb-4 text-center">
                    <i class="fa fa-sign-in-alt me-2"></i>
                    <a href="{{ route('login') }}" class="text-primary fw-bold">Inicia sesión</a> 
                    o 
                    <a href="{{ route('register') }}" class="text-primary fw-bold">regístrate</a> 
                    para dejar una reseña
                </div>
            @endauth

            <!-- Lista de comentarios -->
            <div class="comentarios-lista">
                @forelse($lugare->comentarios as $comentario)
                    <div class="comentario-item border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $comentario->usuario->name }}</h6>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star {{ $i <= $comentario->calificacion ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <small class="text-muted">{{ $comentario->created_at->diffForHumans() }}</small>
                        </div>
                        <p>{{ $comentario->comentario }}</p>
                    </div>
                @empty
                    <p class="text-muted">Aún no hay comentarios sobre este lugar.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sidebar Derecho -->
    <div class="col-lg-4">
        <div class="bg-light rounded p-4 mb-4 sticky-top" style="top: 100px;">
            <div class="text-center mb-4">
                <h4 class="text-primary">{{ $lugare->nombre }}</h4>
                <div class="d-flex justify-content-center mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <small class="fa fa-star {{ $i <= round($lugare->promedio_calificacion) ? 'text-primary' : 'text-muted' }}"></small>
                    @endfor
                </div>
                <p class="mb-0">
                    <span class="badge bg-primary">{{ $lugare->categoria->nombre ?? 'Sin categoría' }}</span>
                </p>
            </div>

            <div class="border-top border-bottom py-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="fa fa-star text-warning me-1"></i>Calificación:</span>
                    <strong>{{ number_format($lugare->promedio_calificacion, 1) }}/5.0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="fa fa-tag text-success me-1"></i>Precio:</span>
                    <strong>{{ $lugare->precio ? '$' . number_format($lugare->precio, 0) : 'Gratis' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span><i class="fa fa-eye text-info me-1"></i>Visible:</span>
                    <strong>{{ $lugare->visible ? 'Sí' : 'No' }}</strong>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="d-grid gap-2">
                @role('admin')
                    <a href="{{ route('lugares.edit', $lugare->id) }}" class="btn btn-warning btn-lg">
                        <i class="fa fa-edit me-2"></i>Editar Lugar
                    </a>
                    <form action="{{ route('lugares.destroy', $lugare->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este lugar?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-lg w-100">
                            <i class="fa fa-trash me-2"></i>Eliminar Lugar
                        </button>
                    </form>
                @endrole
                @role('admin')
                    <a href="{{ route('lugares.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fa fa-arrow-left me-2"></i>Volver a la Lista
                    </a>
                @else
                    <a href="{{ route('lugares.explorar') }}" class="btn btn-secondary btn-lg">
                        <i class="fa fa-arrow-left me-2"></i>Volver a Explorar
                    </a>
                @endrole

            <!-- Información del Creador -->
            @if($lugare->creador)
            <div class="mt-4 pt-3 border-top">
                <small class="text-muted d-block">Registrado por:</small>
                <strong>{{ $lugare->creador->name }}</strong>
                <small class="text-muted d-block mt-1">
                    {{ $lugare->created_at->format('d/m/Y') }}
                </small>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ==========================================
// MAPA DE SOLO LECTURA
// ==========================================
@if($lugare->tieneUbicacion())
const map = L.map('map').setView([{{ $lugare->latitud }}, {{ $lugare->longitud }}], 15);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

// Marcador fijo
const marker = L.marker([{{ $lugare->latitud }}, {{ $lugare->longitud }}]).addTo(map);
marker.bindPopup('<b>{{ $lugare->nombre }}</b><br>{{ $lugare->direccion ?? "Sin dirección" }}').openPopup();
@endif

// ==========================================
// SISTEMA DE CALIFICACIÓN CON ESTRELLAS
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-icon');
    const starLabels = document.querySelectorAll('.star-label');
    let selectedRating = 0;

    stars.forEach((star, index) => {
        star.addEventListener('mouseenter', function() {
            highlightStars(index + 1);
        });
    });

    starLabels.forEach((label, index) => {
        label.addEventListener('click', function() {
            selectedRating = index + 1;
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            highlightStars(selectedRating, true);
        });
    });

    document.querySelector('.rating-stars')?.addEventListener('mouseleave', function() {
        if (selectedRating > 0) {
            highlightStars(selectedRating, true);
        } else {
            resetStars();
        }
    });

    function highlightStars(count, selected = false) {
        stars.forEach((star, index) => {
            if (index < count) {
                star.classList.remove('text-muted');
                star.classList.add('text-warning');
                if (selected) {
                    star.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        star.style.transform = 'scale(1)';
                    }, 200);
                }
            } else {
                star.classList.remove('text-warning');
                star.classList.add('text-muted');
            }
        });
    }

    function resetStars() {
        stars.forEach(star => {
            star.classList.remove('text-warning');
            star.classList.add('text-muted');
        });
    }

    // Validación
    document.getElementById('form-comentario')?.addEventListener('submit', function(e) {
        const calificacion = document.querySelector('input[name="calificacion"]:checked');
        const comentario = document.querySelector('textarea[name="comentario"]').value.trim();

        if (!calificacion) {
            e.preventDefault();
            alert('⭐ Por favor selecciona una calificación');
            return false;
        }

        if (comentario.length < 10) {
            e.preventDefault();
            alert('📝 El comentario debe tener al menos 10 caracteres');
            return false;
        }
    });
});
</script>

<style>
.rating-stars {
    transition: all 0.3s ease;
}

.star-icon {
    transition: all 0.2s ease;
    cursor: pointer;
}

.star-icon:hover {
    transform: scale(1.1);
}

.star-label {
    display: inline-block;
}

.comentario-item {
    transition: background-color 0.3s ease;
}

.comentario-item:hover {
    background-color: #f8f9fa;
}
</style>
@endpush

@endsection