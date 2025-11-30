@extends('layouts.app')

@section('title', 'Mi Perfil - Plataforma Turística')
@section('page-title', 'Mi Perfil')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4">
            
            {{-- 📊 Card de Información General --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        {{-- Avatar --}}
                        <div class="position-relative d-inline-block mb-3">
                            <img 
                                id="avatar-preview" 
                                src="{{ auth()->user()->avatar_url }}" 
                                alt="{{ auth()->user()->name }}"
                                class="rounded-circle border border-primary border-3"
                                style="width: 150px; height: 150px; object-fit: cover;"
                            >
                            <span class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2" style="width: 40px; height: 40px;">
                                <i class="fas fa-camera"></i>
                            </span>
                        </div>
                        
                        {{-- Información básica --}}
                        <h4 class="mb-1">{{ auth()->user()->name }}</h4>
                        <p class="text-muted mb-2">
                            <i class="fas fa-envelope me-1"></i>{{ auth()->user()->email }}
                        </p>
                        
                        @if(auth()->user()->telefono)
                            <p class="text-muted mb-3">
                                <i class="fas fa-phone me-1"></i>{{ auth()->user()->telefono }}
                            </p>
                        @endif
                        
                        {{-- Rol --}}
                        <span class="badge mb-3 px-3 py-2 {{ auth()->user()->hasRole('admin') ? 'bg-danger' : 'bg-primary' }}">
                            <i class="fas fa-user-shield me-1"></i>
                            {{ auth()->user()->hasRole('admin') ? 'Administrador' : 'Usuario' }}
                        </span>
                        
                        {{-- Fecha de registro --}}
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted">
                                <i class="far fa-calendar-alt me-1"></i>
                                Miembro desde {{ auth()->user()->created_at->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formularios de edición --}}
            <div class="col-lg-8">
                
                {{-- 🖼️ Actualizar Foto de Perfil --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-image me-2"></i>Foto de Perfil
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            
                            {{-- Campos ocultos para mantener los datos actuales --}}
                            <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                            <input type="hidden" name="telefono" value="{{ auth()->user()->telefono }}">
                            
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Nueva foto de perfil (opcional)</label>
                                <input 
                                    type="file" 
                                    class="form-control @error('avatar') is-invalid @enderror" 
                                    id="avatar" 
                                    name="avatar"
                                    accept="image/jpeg,image/jpg,image/png"
                                    onchange="previewAvatar(event)"
                                >
                                <small class="text-muted">JPG, JPEG o PNG. Máximo 2MB.</small>
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(auth()->user()->avatar)
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Guardar Foto
                                    </button>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-danger"
                                        onclick="if(confirm('¿Eliminar foto de perfil?')) { document.getElementById('delete-avatar-form').submit(); }"
                                    >
                                        <i class="fas fa-trash me-1"></i>Eliminar Foto
                                    </button>
                                </div>
                            @else
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-1"></i>Subir Foto
                                </button>
                            @endif
                        </form>

                        @if(auth()->user()->avatar)
                            <form id="delete-avatar-form" method="POST" action="{{ route('profile.avatar.delete') }}" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>

                {{-- ✏️ Actualizar Información Personal --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-edit me-2"></i>Información Personal
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre Completo *</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', auth()->user()->name) }}"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico *</label>
                                <input 
                                    type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', auth()->user()->email) }}"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('telefono') is-invalid @enderror" 
                                    id="telefono" 
                                    name="telefono" 
                                    value="{{ old('telefono', auth()->user()->telefono) }}"
                                    placeholder="+57 300 123 4567"
                                >
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Guardar Cambios
                            </button>
                        </form>
                    </div>
                </div>

                {{-- 🔒 Cambiar Contraseña --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Contraseña Actual *</label>
                                <input 
                                    type="password" 
                                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                    id="current_password" 
                                    name="current_password"
                                    required
                                >
                                @error('current_password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Nueva Contraseña *</label>
                                <input 
                                    type="password" 
                                    class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                    id="password" 
                                    name="password"
                                    required
                                >
                                @error('password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña *</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password_confirmation" 
                                    name="password_confirmation"
                                    required
                                >
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i>Actualizar Contraseña
                            </button>
                        </form>
                    </div>
                </div>

                {{-- 🗑️ Eliminar Cuenta --}}
                <div class="card border-0 shadow-sm border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Zona Peligrosa
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-3">
                            Una vez eliminada tu cuenta, todos tus datos serán borrados permanentemente. 
                            Esta acción no se puede deshacer.
                        </p>
                        <button 
                            type="button" 
                            class="btn btn-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteAccountModal"
                        >
                            <i class="fas fa-trash me-1"></i>Eliminar Cuenta
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación de eliminación --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                
                <div class="modal-body">
                    <p class="mb-3">¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.</p>
                    
                    <div class="mb-3">
                        <label for="password_delete" class="form-label">Confirma tu contraseña *</label>
                        <input 
                            type="password" 
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                            id="password_delete" 
                            name="password"
                            required
                        >
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Mi Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Preview de avatar antes de subir
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }

    // Mostrar alertas de éxito
    @if(session('status') === 'profile-updated')
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: '¡Perfil actualizado correctamente!'
        });
    @endif

    @if(session('status') === 'password-updated')
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: '¡Contraseña actualizada correctamente!'
        });
    @endif

    @if(session('status') === 'avatar-deleted')
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: '¡Foto eliminada correctamente!'
        });
    @endif
</script>
@endpush
@endsection