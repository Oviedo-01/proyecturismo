@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Gestión de Usuarios</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Rol actual</th>
                <th>Cambiar rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->telefono }}</td>
                    <td>
                        {{ $usuario->roles->pluck('name')->implode(', ') ?: 'Sin rol' }}
                    </td>
                    <td>
                        <form action="{{ route('usuarios.updateRole', $usuario->id) }}" method="POST">
                            @csrf
                            <select name="role" class="form-select form-select-sm d-inline w-auto">
                                <option value="usuario" {{ $usuario->hasRole('usuario') ? 'selected' : '' }}>Usuario</option>
                                <option value="admin" {{ $usuario->hasRole('admin') ? 'selected' : '' }}>Administrador</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary ms-2">Actualizar</button>
                        </form>
                    </td>
                    <td>
                        @if(auth()->id() !== $usuario->id)
                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        @else
                            <span class="text-muted">No disponible</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
