<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    /**
     * Mostrar la lista de usuarios
     */
    public function index()
    {
        // Trae todos los usuarios con su rol
        $usuarios = User::with('roles')->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    /**
     * Asignar o cambiar el rol de un usuario
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        // Quita roles actuales y asigna el nuevo
        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * (Opcional) Eliminar un usuario
     */
    public function destroy(User $user)
    {
        // ✅ Evitar que un admin se elimine a sí mismo
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }
}
