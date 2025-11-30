@extends('layouts.app')

@section('title', 'Crear Evento')
@section('page-title', 'Crear Nuevo Evento')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <form action="{{ route('eventos.store') }}" method="POST">
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

                            <!-- Nombre del Evento -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Nombre del Evento <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea name="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror" 
                                          placeholder="Describe el evento, actividades, requisitos, etc.">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Fecha de Inicio -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Fecha y Hora de Inicio <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                           value="{{ old('fecha_inicio') }}" required>
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Fecha de Fin -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Fecha y Hora de Fin (Opcional)</label>
                                    <input type="datetime-local" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                           value="{{ old('fecha_fin') }}">
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
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

                                <!-- Lugar Turístico -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Lugar Turístico (Opcional)</label>
                                    <select name="lugar_id" class="form-select @error('lugar_id') is-invalid @enderror">
                                        <option value="">-- Sin lugar específico --</option>
                                        @foreach ($lugares as $lugar)
                                            <option value="{{ $lugar->id }}" {{ old('lugar_id') == $lugar->id ? 'selected' : '' }}>
                                                {{ $lugar->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lugar_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Ubicación</label>
                                <input type="text" name="ubicacion" class="form-control @error('ubicacion') is-invalid @enderror" 
                                       value="{{ old('ubicacion') }}" placeholder="Ej: Plaza Principal, Centro Turístico">
                                <small class="text-muted">Si seleccionaste un lugar turístico, este campo es opcional</small>
                                @error('ubicacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Capacidad -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Capacidad (personas) <span class="text-danger">*</span></label>
                                    <input type="number" name="capacidad" class="form-control @error('capacidad') is-invalid @enderror" 
                                           value="{{ old('capacidad', 50) }}" min="1" required>
                                    @error('capacidad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Precio -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Precio <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="precio" class="form-control @error('precio') is-invalid @enderror" 
                                               value="{{ old('precio', 0) }}" min="0" required>
                                    </div>
                                    <small class="text-muted">Usa 0 para eventos gratuitos</small>
                                    @error('precio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('eventos.index') }}" class="btn btn-secondary px-4">
                                    <i class="fa fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-save me-2"></i>Crear Evento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection