@extends('layouts.app')

@section('title', 'Registrar Lugar Turístico')
@section('page-title', 'Registrar Lugar Turístico')

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
            <div class="card-body p-5">
                <form action="{{ route('lugares.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h5>Por favor corrige los siguientes errores:</h5>
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
                        <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Dirección -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}">
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
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
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
                            Haz clic en el mapa para seleccionar la ubicación exacta del lugar turístico
                        </p>
                        <div id="map"></div>
                        
                        <!-- Coordenadas ocultas -->
                        <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud') }}">
                        <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud') }}">
                        
                        <!-- Mostrar coordenadas seleccionadas -->
                        <div id="coordenadas-display" class="mt-3 alert alert-info" style="display: none;">
                            <strong><i class="fa fa-location-dot me-2"></i>Ubicación seleccionada:</strong><br>
                            <small>
                                Latitud: <span id="lat-display">-</span> | 
                                Longitud: <span id="lng-display">-</span>
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Precio -->
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio" class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio') }}">
                            </div>
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Horarios -->
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Horarios</label>
                            <input type="text" name="horarios" class="form-control @error('horarios') is-invalid @enderror" value="{{ old('horarios') }}" placeholder="Ej: Lun-Vie 9am-5pm">
                            @error('horarios')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contacto -->
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Contacto</label>
                            <input type="text" name="contacto" class="form-control @error('contacto') is-invalid @enderror" value="{{ old('contacto') }}" placeholder="Teléfono o email">
                            @error('contacto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Imágenes con Preview -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Imágenes del Lugar 
                            <span class="badge bg-primary ms-2" id="image-counter">0 / 5</span>
                        </label>
                        <input 
                            type="file" 
                            name="imagenes[]" 
                            id="imagenes-input"
                            multiple 
                            class="form-control @error('imagenes.*') is-invalid @enderror" 
                            accept="image/jpeg,image/jpg,image/png"
                            onchange="previewImages(event)"
                        >
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Puedes subir hasta 5 imágenes (JPG, JPEG, PNG - Máximo 2MB cada una)
                        </small>
                        @error('imagenes.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <!-- Preview de imágenes -->
                        <div id="image-preview-container" class="row g-3 mt-3" style="display: none;"></div>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('lugares.index') }}" class="btn btn-secondary px-4">
                            <i class="fa fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa fa-save me-2"></i>Guardar Lugar
                        </button>
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

// Coordenadas por defecto (Medellín, Colombia)
const defaultLat = 6.2476;
const defaultLng = -75.5658;

// Crear el mapa centrado en Medellín
const map = L.map('map').setView([defaultLat, defaultLng], 13);

// Agregar capa de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

// Variable para el marcador
let marker = null;

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
        draggable: true // Permitir mover el marcador
    }).addTo(map);
    
    // Popup con las coordenadas
    marker.bindPopup(`<b>Ubicación seleccionada</b><br>Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`).openPopup();
    
    // Actualizar inputs ocultos
    updateCoordinates(lat, lng);
    
    // Permitir arrastrar el marcador
    marker.on('dragend', function(e) {
        const newLat = e.target.getLatLng().lat;
        const newLng = e.target.getLatLng().lng;
        updateCoordinates(newLat, newLng);
        marker.bindPopup(`<b>Ubicación seleccionada</b><br>Lat: ${newLat.toFixed(4)}, Lng: ${newLng.toFixed(4)}`).openPopup();
    });
});

// Si hay coordenadas antiguas (old), mostrarlas
@if(old('latitud') && old('longitud'))
    const oldLat = {{ old('latitud') }};
    const oldLng = {{ old('longitud') }};
    marker = L.marker([oldLat, oldLng], { draggable: true }).addTo(map);
    marker.bindPopup('<b>Ubicación guardada</b>').openPopup();
    map.setView([oldLat, oldLng], 15);
    updateCoordinates(oldLat, oldLng);
@endif

// ==========================================
// PREVIEW DE IMÁGENES
// ==========================================

function previewImages(event) {
    const files = event.target.files;
    const container = document.getElementById('image-preview-container');
    const counter = document.getElementById('image-counter');
    const input = document.getElementById('imagenes-input');
    
    if (files.length > 5) {
        alert('⚠️ Puedes subir un máximo de 5 imágenes');
        input.value = '';
        container.style.display = 'none';
        counter.textContent = '0 / 5';
        counter.classList.remove('bg-success', 'bg-warning', 'bg-danger');
        counter.classList.add('bg-primary');
        return;
    }
    
    counter.textContent = `${files.length} / 5`;
    counter.classList.remove('bg-primary', 'bg-success', 'bg-warning', 'bg-danger');
    
    if (files.length === 0) {
        counter.classList.add('bg-primary');
    } else if (files.length <= 3) {
        counter.classList.add('bg-success');
    } else if (files.length === 4) {
        counter.classList.add('bg-warning');
    } else if (files.length === 5) {
        counter.classList.add('bg-danger');
    }
    
    container.innerHTML = '';
    
    if (files.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'flex';
    
    Array.from(files).forEach((file, index) => {
        if (!file.type.startsWith('image/')) return;
        if (file.size > 2097152) {
            alert(`⚠️ La imagen "${file.name}" supera los 2MB`);
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-md-4 col-lg-3';
            col.innerHTML = `
                <div class="card shadow-sm">
                    <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Preview ${index + 1}">
                    <div class="card-body p-2 text-center">
                        <small class="text-muted">
                            <i class="fas fa-image me-1"></i>Imagen ${index + 1}
                        </small><br>
                        <small class="badge bg-secondary">${(file.size / 1024).toFixed(0)} KB</small>
                    </div>
                </div>
            `;
            container.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

// Validación del formulario
document.querySelector('form').addEventListener('submit', function(e) {
    const input = document.getElementById('imagenes-input');
    const files = input.files;
    
    if (files.length > 5) {
        e.preventDefault();
        alert('⚠️ No puedes subir más de 5 imágenes');
        return false;
    }
    
    let hasError = false;
    Array.from(files).forEach(file => {
        if (file.size > 2097152) {
            hasError = true;
            alert(`⚠️ La imagen "${file.name}" supera los 2MB`);
        }
    });
    
    if (hasError) {
        e.preventDefault();
        return false;
    }
});
</script>
@endpush
@endsection