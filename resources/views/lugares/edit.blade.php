@extends('layouts.app')

@section('title', 'Editar Lugar')
@section('page-title', 'Editar Lugar Turístico')

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
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fa fa-edit me-2"></i>Editar: {{ $lugare->nombre }}</h4>
            </div>
            <div class="card-body p-5">
                <form action="{{ route('lugares.update', $lugare->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h5><i class="fa fa-exclamation-triangle me-2"></i>Por favor corrige los siguientes errores:</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Nombre -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Lugar <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" value="{{ old('nombre', $lugare->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" rows="5" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $lugare->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Dirección -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $lugare->direccion) }}">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
                                <option value="">-- Selecciona una categoría --</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ (old('categoria_id', $lugare->id_categoria) == $categoria->id) ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- MAPA INTERACTIVO -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fa fa-map-marker-alt text-primary me-2"></i>Ubicación en el Mapa
                        </label>
                        <p class="text-muted small mb-2">
                            <i class="fa fa-info-circle me-1"></i>
                            Haz clic en el mapa para actualizar la ubicación del lugar
                        </p>
                        <div id="map"></div>
                        
                        <!-- Coordenadas ocultas -->
                        <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $lugare->latitud) }}">
                        <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $lugare->longitud) }}">
                        
                        <!-- Mostrar coordenadas seleccionadas -->
                        <div id="coordenadas-display" class="mt-3 alert alert-info" style="{{ $lugare->tieneUbicacion() ? '' : 'display: none;' }}">
                            <strong><i class="fa fa-location-dot me-2"></i>Ubicación seleccionada:</strong><br>
                            <small>
                                Latitud: <span id="lat-display">{{ $lugare->latitud ?? '-' }}</span> | 
                                Longitud: <span id="lng-display">{{ $lugare->longitud ?? '-' }}</span>
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Precio -->
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio" class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio', $lugare->precio) }}">
                            </div>
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Horarios -->
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Horarios</label>
                            <input type="text" name="horarios" class="form-control @error('horarios') is-invalid @enderror" value="{{ old('horarios', $lugare->horarios) }}" placeholder="Ej: Lun-Vie 9am-5pm">
                            @error('horarios')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contacto -->
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Contacto</label>
                            <input type="text" name="contacto" class="form-control @error('contacto') is-invalid @enderror" value="{{ old('contacto', $lugare->contacto) }}" placeholder="Teléfono o email">
                            @error('contacto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Imágenes Actuales -->
                    @if ($lugare->imagenes->count())
                        <div class="mb-4">
                            <label class="form-label fw-bold">Imágenes Actuales</label>
                            <div class="row g-3">
                                @foreach ($lugare->imagenes as $img)
                                    <div class="col-md-3 col-6">
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $img->url) }}" alt="Imagen" class="img-fluid rounded shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-dark">
                                                    <i class="fa fa-image"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fa fa-info-circle me-1"></i>Para eliminar imágenes, usa la opción de gestión multimedia
                            </small>
                        </div>
                    @endif

                    <!-- Nuevas Imágenes -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Agregar Nuevas Imágenes</label>
                        <input type="file" name="imagenes[]" multiple class="form-control @error('imagenes.*') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Puedes subir múltiples imágenes (máximo 2MB cada una)</small>
                        @error('imagenes.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('lugares.show', $lugare->id) }}" class="btn btn-outline-secondary px-4">
                            <i class="fa fa-eye me-2"></i>Ver Lugar
                        </a>
                        <div>
                            <a href="{{ route('lugares.index') }}" class="btn btn-secondary px-4 me-2">
                                <i class="fa fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ==========================================
// INICIALIZAR MAPA CON LEAFLET
// ==========================================

// Coordenadas guardadas o por defecto (Medellín)
@if($lugare->tieneUbicacion())
    const initialLat = {{ $lugare->latitud }};
    const initialLng = {{ $lugare->longitud }};
    const initialZoom = 15;
@else
    const initialLat = 6.2476;
    const initialLng = -75.5658;
    const initialZoom = 13;
@endif

// Crear el mapa
const map = L.map('map').setView([initialLat, initialLng], initialZoom);

// Agregar capa de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

// Variable para el marcador
let marker = null;

// Si ya tiene ubicación guardada, mostrar marcador
@if($lugare->tieneUbicacion())
    marker = L.marker([initialLat, initialLng], {
        draggable: true
    }).addTo(map);
    marker.bindPopup('<b>Ubicación actual</b><br>Arrastra para mover').openPopup();
@endif

// Función para actualizar coordenadas
function updateCoordinates(lat, lng) {
    document.getElementById('latitud').value = lat.toFixed(6);
    document.getElementById('longitud').value = lng.toFixed(6);
    document.getElementById('lat-display').textContent = lat.toFixed(6);
    document.getElementById('lng-display').textContent = lng.toFixed(6);
    document.getElementById('coordenadas-display').style.display = 'block';
}

// Evento click en el mapa
map.on('click', function(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;
    
    // Si ya existe un marcador, quitarlo
    if (marker) {
        map.removeLayer(marker);
    }
    
    // Agregar nuevo marcador
    marker = L.marker([lat, lng], {
        draggable: true
    }).addTo(map);
    
    marker.bindPopup(`<b>Nueva ubicación</b><br>Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`).openPopup();
    
    // Actualizar inputs ocultos
    updateCoordinates(lat, lng);
    
    // Permitir arrastrar el marcador
    marker.on('dragend', function(e) {
        const newLat = e.target.getLatLng().lat;
        const newLng = e.target.getLatLng().lng;
        updateCoordinates(newLat, newLng);
        marker.bindPopup(`<b>Ubicación actualizada</b><br>Lat: ${newLat.toFixed(4)}, Lng: ${newLng.toFixed(4)}`).openPopup();
    });
});

// Si ya hay marcador, permitir arrastrarlo
if (marker) {
    marker.on('dragend', function(e) {
        const newLat = e.target.getLatLng().lat;
        const newLng = e.target.getLatLng().lng;
        updateCoordinates(newLat, newLng);
        marker.bindPopup(`<b>Ubicación actualizada</b><br>Lat: ${newLat.toFixed(4)}, Lng: ${newLng.toFixed(4)}`).openPopup();
    });
}
</script>
@endpush
@endsection