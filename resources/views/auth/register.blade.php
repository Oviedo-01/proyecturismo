@extends('layouts.app')

@section('title', 'Registrarse')
@section('page-title', 'Crear Cuenta')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-primary text-white text-center py-4">
                <h3 class="mb-0"><i class="fa fa-user-plus me-2"></i>Crear Cuenta Nueva</h3>
            </div>
            <div class="card-body p-5">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            <i class="fa fa-user me-2 text-primary"></i>Nombre Completo
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" 
                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                               required autofocus autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Email -->
                        <div class="col-md-6 mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="fa fa-envelope me-2 text-primary"></i>Email
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   required autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6 mb-4">
                            <label for="telefono" class="form-label fw-bold">
                                <i class="fa fa-phone me-2 text-primary"></i>Teléfono
                            </label>
                            <input id="telefono" type="text" name="telefono" value="{{ old('telefono') }}" 
                                   class="form-control @error('telefono') is-invalid @enderror">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Password -->
                        <div class="col-md-6 mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="fa fa-lock me-2 text-primary"></i>Contraseña
                            </label>
                            <input id="password" type="password" name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6 mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">
                                <i class="fa fa-lock me-2 text-primary"></i>Confirmar
                            </label>
                            <input id="password_confirmation" type="password" name="password_confirmation" 
                                   class="form-control" required autocomplete="new-password">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-user-plus me-2"></i>Registrarse
                        </button>
                    </div>

                    <!-- Link -->
                    <div class="text-center mt-4">
                        <span class="text-muted">¿Ya tienes cuenta?</span>
                        <a href="{{ route('login') }}" class="fw-bold">Inicia sesión aquí</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection