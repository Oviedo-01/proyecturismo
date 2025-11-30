@extends('layouts.app')

@section('title', 'Iniciar Sesión')
@section('page-title', 'Iniciar Sesión')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-primary text-white text-center py-4">
                <h3 class="mb-0"><i class="fa fa-sign-in-alt me-2"></i>Iniciar </h3>
            </div>
            <div class="card-body p-5">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">
                            <i class="fa fa-envelope me-2 text-primary"></i>Correo Electrónico
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" 
                               class="form-control form-control-lg @error('email') is-invalid @enderror" 
                               required autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">
                            <i class="fa fa-lock me-2 text-primary"></i>Contraseña
                        </label>
                        <input id="password" type="password" name="password" 
                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                               required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label for="remember_me" class="form-check-label">
                            Recordarme
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                    </div>

                    <!-- Links -->
                    <div class="text-center mt-4">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-muted">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                        <div class="mt-3">
                            <span class="text-muted">¿No tienes cuenta?</span>
                            <a href="{{ route('register') }}" class="fw-bold">Regístrate aquí</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection